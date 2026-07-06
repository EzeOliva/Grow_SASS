<?php

namespace App\Http\Controllers;

use App\Models\KbCategories;
use App\Models\SupportAgent;
use App\Models\SupportAgentTestRun;
use App\Models\SupportAgentUnansweredQuery;
use App\Services\SupportAgents\AgentChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class SupportAgents extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Auth::check() || !Auth::user()->is_team) {
                abort(403);
            }

            return $next($request);
        });
    }

    public function index()
    {
        $agents = SupportAgent::withCount('kbCategories')->orderBy('id', 'desc')->paginate(20);

        $payload = [
            'page' => $this->pageSettings('index'),
            'agents' => $agents,
        ];

        return view('pages.support-agents.wrapper', $payload);
    }

    public function create()
    {
        $payload = [
            'page' => $this->pageSettings('create'),
            'categories' => KbCategories::orderBy('kbcategory_title', 'asc')->get(),
            'agent' => new SupportAgent([
                'agent_visibility' => 'team',
                'is_active' => true,
                'allow_client_chat' => false,
                'allow_ticket_suggestions' => false,
                'allow_document_sources' => true,
            ]),
            'selectedCategories' => [],
        ];

        return view('pages.support-agents.form', $payload);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        $agent = new SupportAgent();
        $agent->tenant_id = app()->bound('currentTenant') ? (app('currentTenant')->id ?? null) : null;
        $agent->agent_creatorid = Auth::id();
        $agent->agent_name = $data['agent_name'];
        $agent->agent_identity_prompt = $data['agent_identity_prompt'];
        $agent->agent_visibility = $data['agent_visibility'];
        $agent->is_active = $request->boolean('is_active');
        $agent->allow_client_chat = $request->boolean('allow_client_chat');
        $agent->allow_ticket_suggestions = $request->boolean('allow_ticket_suggestions');
        $agent->allow_document_sources = $request->boolean('allow_document_sources');
        $agent->save();

        $agent->kbCategories()->sync($request->input('kbcategory_ids', []));

        return redirect('/support-agents')->with('success', 'Agente creado correctamente');
    }

    public function edit($id)
    {
        $agent = SupportAgent::with('kbCategories')->findOrFail($id);

        $payload = [
            'page' => $this->pageSettings('edit', $agent),
            'categories' => KbCategories::orderBy('kbcategory_title', 'asc')->get(),
            'agent' => $agent,
            'selectedCategories' => $agent->kbCategories->pluck('kbcategory_id')->all(),
        ];

        return view('pages.support-agents.form', $payload);
    }

    public function update(Request $request, $id)
    {
        $data = $this->validatedData($request);

        $agent = SupportAgent::findOrFail($id);
        $agent->agent_name = $data['agent_name'];
        $agent->agent_identity_prompt = $data['agent_identity_prompt'];
        $agent->agent_visibility = $data['agent_visibility'];
        $agent->is_active = $request->boolean('is_active');
        $agent->allow_client_chat = $request->boolean('allow_client_chat');
        $agent->allow_ticket_suggestions = $request->boolean('allow_ticket_suggestions');
        $agent->allow_document_sources = $request->boolean('allow_document_sources');
        $agent->save();

        $agent->kbCategories()->sync($request->input('kbcategory_ids', []));

        return redirect('/support-agents')->with('success', 'Agente actualizado correctamente');
    }

    public function show($id)
    {
        return redirect('/support-agents/' . $id . '/edit');
    }

    public function destroy($id)
    {
        $agent = SupportAgent::findOrFail($id);
        $agent->delete();

        return redirect('/support-agents')->with('success', 'Agente eliminado correctamente');
    }

    public function test($id)
    {
        $agent = SupportAgent::with('kbCategories')->findOrFail($id);

        $payload = [
            'page' => $this->pageSettings('test', $agent),
            'agent' => $agent,
            'answer' => null,
            'sources' => [],
            'audience' => old('audience', 'team'),
            'question' => old('question', ''),
            'lastRunStatus' => session('lastRunStatus'),
            'unansweredQueries' => $agent->unansweredQueries()
                ->where('unanswered_status', 'open')
                ->latest()
                ->take(10)
                ->get(),
        ];

        return view('pages.support-agents.test', $payload);
    }

    public function testAsk(Request $request, $id, AgentChatService $chatService)
    {
        $agent = SupportAgent::with('kbCategories')->findOrFail($id);

        $validated = $request->validate([
            'question' => ['required', 'string', 'max:5000'],
            'audience' => ['required', Rule::in(['team', 'client'])],
        ]);

        $audience = $validated['audience'];
        if (!$this->isAudienceAllowed($agent, $audience)) {
            return redirect()->back()->withInput()->with('error', 'Ese agente no esta habilitado para la audiencia seleccionada.');
        }

        try {
            $result = $chatService->ask($agent, $validated['question'], $audience);
            $unansweredCheck = $this->evaluateUnanswered($result['answer'] ?? '', $result['sources'] ?? []);

            $testRun = $this->storeTestRun($agent, [
                'test_creatorid' => Auth::id(),
                'test_audience' => $audience,
                'test_question' => $validated['question'],
                'test_answer' => $result['answer'] ?? null,
                'test_sources' => $result['sources'] ?? [],
                'response_status' => $unansweredCheck['is_unanswered'] ? 'unanswered' : 'answered',
                'unanswered_reasons' => $unansweredCheck['reasons'],
                'model_name' => $result['model'] ?? null,
                'model_tokens_prompt' => $result['usage']['prompt_tokens'] ?? null,
                'model_tokens_completion' => $result['usage']['completion_tokens'] ?? null,
                'model_tokens_total' => $result['usage']['total_tokens'] ?? null,
                'error_message' => null,
            ]);

            if ($unansweredCheck['is_unanswered']) {
                $this->storeUnansweredQuery($agent, $testRun, [
                    'unanswered_creatorid' => Auth::id(),
                    'unanswered_audience' => $audience,
                    'unanswered_question' => $validated['question'],
                    'unanswered_reason' => $unansweredCheck['reasons'][0] ?? 'insufficient_context',
                    'unanswered_reason_details' => !empty($unansweredCheck['reasons'])
                        ? implode(', ', $unansweredCheck['reasons'])
                        : null,
                    'unanswered_status' => 'open',
                ]);
            }

            $payload = [
                'page' => $this->pageSettings('test', $agent),
                'agent' => $agent,
                'answer' => $result['answer'],
                'sources' => $result['sources'],
                'audience' => $audience,
                'question' => $validated['question'],
                'lastRunStatus' => $unansweredCheck['is_unanswered']
                    ? 'Se guardo como consulta no respondida para mejora del agente.'
                    : 'Consulta respondida y guardada en historial de pruebas.',
                'unansweredQueries' => $agent->unansweredQueries()
                    ->where('unanswered_status', 'open')
                    ->latest()
                    ->take(10)
                    ->get(),
            ];

            return view('pages.support-agents.test', $payload);
        } catch (\Throwable $e) {
            Log::error('support agent test failed', [
                'agent_id' => $agent->id,
                'error' => $e->getMessage(),
            ]);

            $testRun = $this->storeTestRun($agent, [
                'test_creatorid' => Auth::id(),
                'test_audience' => $audience,
                'test_question' => $validated['question'],
                'test_answer' => null,
                'test_sources' => [],
                'response_status' => 'error',
                'unanswered_reasons' => ['model_error'],
                'model_name' => config('openai.model', 'gpt-4o-mini'),
                'model_tokens_prompt' => null,
                'model_tokens_completion' => null,
                'model_tokens_total' => null,
                'error_message' => $e->getMessage(),
            ]);

            $this->storeUnansweredQuery($agent, $testRun, [
                'unanswered_creatorid' => Auth::id(),
                'unanswered_audience' => $audience,
                'unanswered_question' => $validated['question'],
                'unanswered_reason' => 'model_error',
                'unanswered_reason_details' => $e->getMessage(),
                'unanswered_status' => 'open',
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'No se pudo generar la respuesta IA. Revisa la configuracion de OpenAI e intenta nuevamente.')
                ->with('lastRunStatus', 'Consulta guardada como no respondida (error del modelo) para mejora posterior.');
        }
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'agent_name' => ['required', 'string', 'max:190'],
            'agent_identity_prompt' => ['required', 'string'],
            'agent_visibility' => ['required', Rule::in(['team', 'client', 'everyone'])],
            'kbcategory_ids' => ['nullable', 'array'],
            'kbcategory_ids.*' => ['integer', Rule::exists('kb_categories', 'kbcategory_id')],
        ]);
    }

    private function pageSettings($section = '', $data = [])
    {
        $page = [
            'crumbs' => [
                __('lang.support'),
                'Agentes IA',
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'page' => 'support-agents',
            'mainmenu_support' => 'active',
            'submenu_support_agents' => 'active',
            'meta_title' => 'Agentes IA',
            'heading' => 'Agentes IA',
        ];

        if ($section == 'create') {
            $page['crumbs'][] = 'Crear';
            $page['heading'] = 'Nuevo agente IA';
        }

        if ($section == 'edit') {
            $page['crumbs'][] = 'Editar';
            $page['heading'] = 'Editar agente: ' . ($data->agent_name ?? '');
        }

        if ($section == 'test') {
            $page['crumbs'][] = 'Probar';
            $page['heading'] = 'Probar agente: ' . ($data->agent_name ?? '');
        }

        return $page;
    }

    private function isAudienceAllowed(SupportAgent $agent, string $audience): bool
    {
        if ($audience === 'team') {
            return in_array($agent->agent_visibility, ['team', 'everyone']);
        }

        if (!$agent->allow_client_chat) {
            return false;
        }

        return in_array($agent->agent_visibility, ['client', 'everyone']);
    }

    private function evaluateUnanswered(string $answer, array $sources): array
    {
        $reasons = [];
        $normalizedAnswer = mb_strtolower(trim($answer));

        if ($normalizedAnswer === '') {
            $reasons[] = 'empty_answer';
        }

        if (empty($sources)) {
            $reasons[] = 'no_sources';
        }

        $patterns = [
            'no tengo suficiente informacion',
            'no hay contexto suficiente',
            'no cuento con informacion',
            'no dispongo de informacion',
            'no puedo responder',
            'sin articulos disponibles',
            'base de conocimiento',
        ];

        foreach ($patterns as $pattern) {
            if ($normalizedAnswer !== '' && mb_stripos($normalizedAnswer, $pattern) !== false) {
                $reasons[] = 'insufficient_context';
                break;
            }
        }

        $reasons = array_values(array_unique($reasons));

        return [
            'is_unanswered' => !empty($reasons),
            'reasons' => $reasons,
        ];
    }

    private function storeTestRun(SupportAgent $agent, array $attributes): ?SupportAgentTestRun
    {
        try {
            $attributes['agent_id'] = $agent->id;

            return SupportAgentTestRun::create($attributes);
        } catch (\Throwable $e) {
            Log::warning('support agent test run could not be stored', [
                'agent_id' => $agent->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function storeUnansweredQuery(SupportAgent $agent, ?SupportAgentTestRun $testRun, array $attributes): void
    {
        try {
            $attributes['agent_id'] = $agent->id;
            $attributes['test_run_id'] = $testRun?->id;

            SupportAgentUnansweredQuery::create($attributes);
        } catch (\Throwable $e) {
            Log::warning('support agent unanswered query could not be stored', [
                'agent_id' => $agent->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
