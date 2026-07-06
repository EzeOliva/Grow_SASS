<?php

namespace App\Services\SupportAgents;

use App\Models\SupportAgent;

class AgentChatService
{
    protected AgentContextBuilder $contextBuilder;

    public function __construct(AgentContextBuilder $contextBuilder)
    {
        $this->contextBuilder = $contextBuilder;
    }

    /**
     * Ask the configured support agent a question using KB context.
     */
    public function ask(SupportAgent $agent, string $question, string $audience = 'team'): array
    {
        $model = config('openai.model', 'gpt-4o-mini');
        $contextPayload = $this->contextBuilder->build($agent, $audience, 6, $question);
        $context = $contextPayload['context'];
        $sources = $contextPayload['sources'];

        $systemPrompt = trim((string) $agent->agent_identity_prompt) . "\n\n"
            . "Instrucciones operativas:\n"
            . "- Usa solamente el contexto de base de conocimiento provisto.\n"
            . "- Si no hay contexto suficiente, dilo claramente y recomienda abrir/escalar ticket.\n"
            . "- No inventes enlaces, datos ni acciones ejecutadas.\n"
            . "- Cuando uses informacion del contexto, cita codigo de fuente como [S1], [S2], etc.\n"
            . "- Estilo de respuesta: conversacional, claro y humano.\n"
            . "- Evita markdown (no uses **, #, ni listas 1. 2. 3.).\n"
            . "- Si hay pasos, usa lineas con prefijo -> Paso ...\n"
            . "- Puedes usar 1 emoji maximo cuando aporte calidez, sin exagerar.";

        $userPrompt = "Pregunta del usuario:\n{$question}\n\n"
            . "Contexto de base de conocimiento:\n"
            . ($context !== '' ? $context : '(sin articulos disponibles para esta configuracion)');

        $response = \OpenAI\Laravel\Facades\OpenAI::chat()->create([
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt],
            ],
            'max_tokens' => 700,
            'temperature' => 0.2,
        ]);

        $rawAnswer = trim((string) ($response['choices'][0]['message']['content'] ?? ''));

        return [
            'answer' => $this->normalizeAnswerForUi($rawAnswer),
            'sources' => $sources,
            'usage' => $response['usage'] ?? null,
            'model' => $model,
        ];
    }

    /**
     * Keep answer readable in UI even if the model returns markdown.
     */
    protected function normalizeAnswerForUi(string $answer): string
    {
        if ($answer === '') {
            return $answer;
        }

        // Remove bold markdown markers.
        $answer = str_replace('**', '', $answer);

        // Convert numbered and bullet lists to arrow steps.
        $answer = preg_replace('/^\s*\d+[\.)]\s*/m', '-> ', $answer) ?? $answer;
        $answer = preg_replace('/^\s*[-*]\s+/m', '-> ', $answer) ?? $answer;

        // Avoid excessive vertical whitespace.
        $answer = preg_replace("/\n{3,}/", "\n\n", $answer) ?? $answer;

        return trim($answer);
    }
}
