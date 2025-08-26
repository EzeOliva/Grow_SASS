<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenAI\Laravel\Facades\OpenAI;
use App\Repositories\ClientAIRepository;

class FeedbackAIController extends Controller
{
    public function __construct(private ClientAIRepository $aiRepo) {}

    public function suggest(Request $request, int $clientId)
    {
        $data = $request->validate([
            'details' => 'required|array|min:1',
            'details.*.feedback_query_id' => 'required|integer',
            'details.*.value' => 'required|integer|min:0|max:10',
        ]);

        $details = $data['details'];

        // Traigo los textos de las preguntas (en tu esquema el campo se llama "content")
        $ids   = collect($details)->pluck('feedback_query_id')->all();
        $meta  = DB::table('feedback_queries')
            ->whereIn('feedback_query_id', $ids)
            ->select('feedback_query_id','content','range','type')
            ->get()
            ->keyBy('feedback_query_id');

        // Armo aspectos + normalizo cada valor a escala 0–10
        $aspects = collect($details)->map(function ($d) use ($meta) {
            $m = $meta[$d['feedback_query_id']] ?? null;
            $range = max(1, (int)($m->range ?? 10)); // fallback a 10
            $value = (int)$d['value'];
            $norm  = round($value * 10 / $range, 1); // <-- normalización
            return [
                'id'    => (int)$d['feedback_query_id'],
                'title' => (string)($m->content ?? 'Pregunta'),
                'range' => $range,
                'type'  => (int)($m->type ?? 1),
                'value' => $value,
                'norm'  => $norm, // valor en 0–10
            ];
        })->values()->all();

        // Usamos el NORMALIZADO para promedio y tono
        $avg  = round(collect($aspects)->avg('norm'), 1);
        $tone = $avg >= 8 ? 'positiva y agradecida'
            : ($avg >= 6 ? 'neutral con una sugerencia' : 'crítica constructiva');

        // Para que el modelo entienda ambas escalas, pasamos el valor crudo y su equivalente 0–10
        $aspectLines = collect($aspects)
        ->map(fn($a) => "- {$a['title']}: {$a['value']}/{$a['range']} (≈ {$a['norm']}/10)")
        ->implode("\n");

        // Cacheo el contexto por 10 min
        try {
            $context = cache()->remember("feedback_ai_context_lite_{$clientId}", now()->addMinutes(10), function () use ($clientId) {
                $start = now()->subDays(90)->toDateString();
                $end   = now()->toDateString();
                $topics = ['projects','tasks','feedback','communication']; // lo clave para reseñas
                $data = $this->aiRepo->fetchClientAnalysisData($clientId, $topics, $start, $end);
                return $this->aiRepo->formatDataForAI($clientId, $data);
            });
        } catch (\Throwable $e) {
            $context = ''; // si falla, seguimos sin contexto
        }

        $aspectLines = collect($aspects)
        ->map(fn($a) => "- {$a['title']}: {$a['value']}/{$a['range']} (≈ {$a['norm']}/10)")
        ->implode("\n");

        $prompt = <<<PROMPT
            Escribe una reseña breve en español rioplatense (máx. 400 caracteres) hablando en primera persona como cliente.
            Debe sonar natural y coherente con estos puntajes (0–10):
            $aspectLines

            Promedio: $avg/10. Tono: $tone.
            Incluye el nombre del producto "Legajos Online".
            Si hay un puntaje ≤ 6, menciona brevemente qué mejorar sin agresividad.
            Entrega solo el texto de la reseña, sin comillas ni rótulos.

            Contexto del cliente (para personalizar, no lo cites literal):
            $context
            PROMPT;

        try {
            $response = OpenAI::chat()->create([
                'model' => config('openai.model', 'gpt-4o-mini'),
                'messages' => [
                    ['role' => 'system', 'content' => 'Sos un redactor experto en reseñas breves y naturales.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.6,
                'max_tokens' => 200, // opcional
            ]);

            $text = trim($response->choices[0]->message->content ?? '');
            if ($text === '') {
                $text = $this->fallback($aspects, $avg);
            }
        } catch (\Throwable $e) {
            $text = $this->fallback($aspects, $avg);
        }

        return response()->json(['suggestion' => $text]);
    }

    private function fallback(array $aspects, float $avg): string
    {
        // Filtramos por la nota normalizada
        $positives = collect($aspects)->filter(fn($a) => $a['norm'] >= 8)->values();
        $lows      = collect($aspects)->filter(fn($a) => $a['norm'] <= 6)->values();

        $p = $positives->first();
        $l = $lows->first();

        $posTxt = $p ? "Nos resultó excelente {$p['title']}" : "La experiencia con Legajos Online fue muy buena";
        $lowTxt = $l ? "Podrían mejorar {$l['title']}." : "";

        return trim("Muy conforme con Legajos Online. Promedio {$avg}/10. $posTxt. $lowTxt");
    }
}
