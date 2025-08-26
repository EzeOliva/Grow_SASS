<?php
namespace App\Repositories;

use App\Models\User;
use App\Models\Lead;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use League\CommonMark\CommonMarkConverter;

class TeamRepository
{
    /**
     * Generate AI prompt for a team member's weekly report and general alerts
     * @param int $teamId
     * @return string
     */
public function generateMemberWeeklyReportPrompt($teamId)
{
    $member = User::where('type', 'team')->where('status', 'active')->where('id', $teamId)->first();
    if (!$member) {
        return "No se encontró ningún miembro de equipo con ese ID.";
    }

    $now       = now();
    $oneWeekAgo = $now->copy()->subWeek();

    $prompt  = "## <u>Informe semanal de {$member->full_name}</u>\n\n";
    $prompt .= "===============================\n\n";

    /* =========================
     * 1) TAREAS
     * =========================*/
    // Completadas
    $completedTasks = $member->assignedTasks()
        ->with(['project:project_id,project_title,project_clientid','project.client:client_id,client_company_name'])
        ->whereHas('status', fn($q) =>
            $q->whereRaw("LOWER(taskstatus_title) REGEXP '(complet|finaliz|terminad|done|closed)'")
        )
        ->where('task_updated', '>=', $oneWeekAgo)
        ->get();

    // En progreso
    $inProgressTasks = $member->assignedTasks()
        ->with(['project:project_id,project_title,project_clientid','project.client:client_id,client_company_name'])
        ->whereHas('status', fn($q) =>
            $q->whereRaw("LOWER(taskstatus_title) REGEXP '(progreso|proceso|camino|progress|test|esperando|wait)'")
        )
        ->where('task_updated', '>=', $oneWeekAgo)
        ->get();

    // Vencidas (no completadas)
    $overdueTasks = $member->assignedTasks()
        ->with(['project:project_id,project_title,project_clientid','project.client:client_id,client_company_name'])
        ->where('task_date_due', '<', $now)
        ->whereHas('status', fn($q) =>
            $q->whereRaw("LOWER(taskstatus_title) NOT REGEXP '(complet|finaliz|terminad|done|closed)'")
        )
        ->get();

    $prompt .= "- **Tareas completadas (última semana):**\n";
    if ($completedTasks->count()) {
        foreach ($completedTasks as $task) {
            $prompt .= "  - {$task->task_title} ({$task->task_updated})\n";
        }
    } else {
        $prompt .= "  - Ninguna\n";
    }

    $prompt .= "- **Tareas en progreso (última semana):**\n";
    if ($inProgressTasks->count()) {
        foreach ($inProgressTasks as $task) {
            $prompt .= "  - {$task->task_title} (Fecha límite: {$task->task_date_due})\n";
        }
    } else {
        $prompt .= "  - Ninguna\n";
    }

    $prompt .= "- **Tareas vencidas:**\n";
    if ($overdueTasks->count()) {
        foreach ($overdueTasks as $task) {
            $prompt .= "  - {$task->task_title} (Fecha de vencimiento: {$task->task_date_due})\n";
        }
    } else {
        $prompt .= "  - Ninguna\n";
    }

    $prompt .= "\n## Alertas Generales\n";
    $prompt .= "- **Tareas en progreso:** " . ($inProgressTasks->count() == 0 ? $member->full_name : 'Ninguna') . "\n";
    $bottleneckCount = $overdueTasks->count() + $inProgressTasks->count();
    $prompt .= "- **Cuellos de botella:** " . ($bottleneckCount > 5 ? $member->full_name . " ({$bottleneckCount} tareas)" : 'Ninguna') . "\n";

    /* =========================
     * 2) LEADS (última semana)
     * =========================*/
    $prompt .= "\n---\n";
    $prompt .= "### Participación en Leads (última semana)\n";

    // Base: empresa (COALESCE) y título de estado por join
    $companyExpr = DB::raw("COALESCE(leads.lead_company_name, leads.lead_title) as lead_company");
    $baseCols = [
        'leads.lead_id',
        'leads.lead_firstname',
        'leads.lead_lastname',
        'leads.lead_status',
        'leads.lead_created',
        'leads.lead_updated',
        'leads.lead_last_contacted',
        $companyExpr,
        DB::raw('leads_status.leadstatus_title as lead_status_title'),
    ];

    // 2.1 Creados por el miembro
    $leadsCreadosSemana = Lead::from('leads')
        ->leftJoin('leads_status', 'leads.lead_status', '=', 'leads_status.leadstatus_id')
        ->where('lead_creatorid', $member->id)
        ->where('lead_created', '>=', $oneWeekAgo)
        ->orderBy('lead_created', 'desc')
        ->get($baseCols);

    // 2.2 Asignados con actividad
    $leadsAsignadosConActividad = $member->assignedLeads()
        ->leftJoin('leads_status', 'leads.lead_status', '=', 'leads_status.leadstatus_id')
        ->where('leads.lead_updated', '>=', $oneWeekAgo)
        ->orderBy('leads.lead_updated', 'desc')
        ->get($baseCols);

    // 2.3 Contactados esta semana
    $leadsContactadosSemana = $member->assignedLeads()
        ->leftJoin('leads_status', 'leads.lead_status', '=', 'leads_status.leadstatus_id')
        ->whereNotNull('leads.lead_last_contacted')
        ->where('leads.lead_last_contacted', '>=', $oneWeekAgo)
        ->orderBy('leads.lead_last_contacted', 'desc')
        ->get($baseCols);

    // 2.4 Resumen por estado (por TÍTULO)
    $leadsPorEstado = $member->assignedLeads()
        ->leftJoin('leads_status', 'leads.lead_status', '=', 'leads_status.leadstatus_id')
        ->where('leads.lead_updated', '>=', $oneWeekAgo)
        ->groupBy('leads_status.leadstatus_title')
        ->selectRaw('leads_status.leadstatus_title as title, COUNT(*) as total')
        ->pluck('total', 'title');

    // Utilidad de formato
    $fmtLead = function ($l, $tipoFecha) {
        $empresa  = $l->lead_company ?? '—';
        $contacto = trim(($l->lead_firstname ?? '') . ' ' . ($l->lead_lastname ?? ''));
        $estado   = $l->lead_status_title ?? ($l->lead_status !== null ? "Estado #{$l->lead_status}" : '—');
        $fecha    = $tipoFecha === 'actualizado' ? ($l->lead_updated ?? '')
                  : ($tipoFecha === 'contacto'   ? ($l->lead_last_contacted ?? '')
                                                 : ($l->lead_created ?? ''));
        $label    = $tipoFecha === 'actualizado' ? 'actualizado'
                  : ($tipoFecha === 'contacto'   ? 'último contacto' : 'creado');
        return "  - #{$l->lead_id} • Empresa: {$empresa} • Contacto: {$contacto} • Estado: {$estado} • {$label}: {$fecha}\n";
    };

    // Render de secciones
    $prompt .= "- **Nuevos leads creados:** " . ($leadsCreadosSemana->count() ?: 0) . "\n";
    if ($leadsCreadosSemana->count()) {
        foreach ($leadsCreadosSemana->take(5) as $l) { $prompt .= $fmtLead($l, 'creado'); }
        if ($leadsCreadosSemana->count() > 5) { $prompt .= "  - (+".($leadsCreadosSemana->count()-5)." más)\n"; }
    } else {
        $prompt .= "  - Ninguno\n";
    }

    $prompt .= "- **Leads asignados con actividad:** " . ($leadsAsignadosConActividad->count() ?: 0) . "\n";
    if ($leadsAsignadosConActividad->count()) {
        foreach ($leadsAsignadosConActividad->take(5) as $l) { $prompt .= $fmtLead($l, 'actualizado'); }
        if ($leadsAsignadosConActividad->count() > 5) { $prompt .= "  - (+".($leadsAsignadosConActividad->count()-5)." más)\n"; }
    } else {
        $prompt .= "  - Ninguno\n";
    }

    $prompt .= "- **Leads contactados:** " . ($leadsContactadosSemana->count() ?: 0) . "\n";
    if ($leadsContactadosSemana->count()) {
        foreach ($leadsContactadosSemana->take(5) as $l) { $prompt .= $fmtLead($l, 'contacto'); }
        if ($leadsContactadosSemana->count() > 5) { $prompt .= "  - (+".($leadsContactadosSemana->count()-5)." más)\n"; }
    } else {
        $prompt .= "  - Ninguno\n";
    }

    $prompt .= "- **Resumen por estado (asignados con actividad):**\n";
    if ($leadsPorEstado && $leadsPorEstado->count()) {
        foreach ($leadsPorEstado as $titulo => $total) {
            $prompt .= "  - {$titulo}: {$total}\n";
        }
    } else {
        $prompt .= "  - Sin movimientos por estado\n";
    }

    /* ==============================
     * 3) COMENTARIOS EN LEADS (última semana)
     * ==============================*/
    $prompt .= "\n---\n";
    $prompt .= "### Comentarios del miembro en Leads (última semana)\n";

    // Detección de columnas en comments
    $commentUserCol    = Schema::hasColumn('comments', 'comment_creatorid') ? 'comment_creatorid'
                         : (Schema::hasColumn('comments', 'comment_userid') ? 'comment_userid'
                         : (Schema::hasColumn('comments', 'user_id') ? 'user_id' : null));
    $commentCreatedCol = Schema::hasColumn('comments', 'comment_created') ? 'comment_created'
                         : (Schema::hasColumn('comments', 'created_at') ? 'created_at'
                         : (Schema::hasColumn('comments', 'comment_date') ? 'comment_date' : null));
    $commentTextCol    = Schema::hasColumn('comments', 'comment_text') ? 'comment_text'
                         : (Schema::hasColumn('comments', 'text') ? 'text' : 'comment');

    $leadsConComentarios = collect();
    $totalComentariosSemana = 0;

    if ($commentUserCol && $commentCreatedCol) {
        $leadsConComentarios = $member->assignedLeads()
            ->leftJoin('leads_status', 'leads.lead_status', '=', 'leads_status.leadstatus_id')
            ->withCount(['comments as comments_semana_count' => function ($q) use ($member, $oneWeekAgo, $commentUserCol, $commentCreatedCol) {
                $q->where($commentUserCol, $member->id)->where($commentCreatedCol, '>=', $oneWeekAgo);
            }])
            ->with(['comments' => function ($q) use ($member, $oneWeekAgo, $commentUserCol, $commentCreatedCol) {
                $q->where($commentUserCol, $member->id)->where($commentCreatedCol, '>=', $oneWeekAgo)->orderBy($commentCreatedCol, 'desc');
            }])
            ->orderBy('leads.lead_updated', 'desc')
            ->get($baseCols)
            ->filter(fn ($lead) => (int) ($lead->comments_semana_count ?? 0) > 0);

        $totalComentariosSemana = $leadsConComentarios->sum('comments_semana_count');
    }

    $prompt .= "- **Total de comentarios agregados a leads:** {$totalComentariosSemana}\n";

    // Top 5 leads con más comentarios del miembro
    $prompt .= "- **Top leads por comentarios del miembro:**\n";
    if ($leadsConComentarios->isEmpty()) {
        $prompt .= "  - Ninguno\n";
    } else {
        foreach ($leadsConComentarios->sortByDesc('comments_semana_count')->take(5) as $l) {
            $empresa  = $l->lead_company ?? '—';
            $contacto = trim(($l->lead_firstname ?? '') . ' ' . ($l->lead_lastname ?? ''));
            $ultimo   = $l->comments->first();
            $texto    = $ultimo? ($ultimo->$commentTextCol ?? '') : '';
            if (is_string($texto) && mb_strlen($texto) > 120) { $texto = mb_substr($texto, 0, 117) . '...'; }
            $ultimoCuando = $ultimo ? ($ultimo->$commentCreatedCol ?? ($ultimo->created_at ?? null)) : null;

            $prompt .= "  - #{$l->lead_id} — {$empresa} — {$contacto} — {$l->comments_semana_count} comentarios — último: {$ultimoCuando}";
            if (!empty($texto)) { $prompt .= " — \"{$texto}\""; }
            $prompt .= "\n";
        }
    }

    // Últimos 5 comentarios (desc)
    $prompt .= "- **Últimos 5 comentarios del miembro en sus leads:**\n";
    $ultimosCinco = $leadsConComentarios
        ->flatMap(fn ($lead) => $lead->comments)
        ->sortByDesc(fn ($c) => $c->$commentCreatedCol ?? $c->created_at)
        ->take(5);

    if ($ultimosCinco->isEmpty()) {
        $prompt .= "  - Ninguno\n";
    } else {
        foreach ($ultimosCinco as $c) {
            $leadId   = $c->commentresource_id ?? null;
            $leadRef  = $leadId ? $leadsConComentarios->firstWhere('lead_id', $leadId) : null;
            $empresa  = $leadRef->lead_company ?? '—';
            $contacto = $leadRef ? trim(($leadRef->lead_firstname ?? '') . ' ' . ($leadRef->lead_lastname ?? '')) : '—';
            $texto    = $c->$commentTextCol ?? '';
            if (is_string($texto) && mb_strlen($texto) > 140) { $texto = mb_substr($texto, 0, 137) . '...'; }
            $cuando   = $c->$commentCreatedCol ?? ($c->created_at ?? '');

            $prompt .= "  - {$cuando} — {$empresa} — {$contacto} — \"{$texto}\"\n";
        }
    }

    return $prompt;
}


    /**
     * Generate AI prompt for a team member's general alerts
     * @param int $teamId
     * @return string
     */
    public function generateMemberGeneralAlertsPrompt($teamId)
    {
        $member = \App\Models\User::where('type', 'team')->where('status', 'active')->where('id', $teamId)->first();
        if (!$member) {
            return "No se encontró ningún miembro de equipo con ese ID.";
        }
        // Example: You can customize this logic to gather bottlenecks, overdue tasks, etc.
        $now = now();
        $prompt = "# Alertas Generales para {$member->full_name}\n\n";

        // 1️⃣ Atrasadas (vencidas y no completadas)
        $overdueTasks = $member->assignedTasks()
            ->where('task_date_due', '<', $now)
            ->whereHas('status', fn($q) =>
                $q->whereRaw("LOWER(taskstatus_title) NOT REGEXP '(complet|finaliz|terminad|done|closed)'")
            )
            ->get();

        // 2️⃣ ¿Sin tareas en progreso?
        $noTasksInProgress = $member->assignedTasks()
            ->whereHas('status', fn($q) =>
                $q->whereRaw("LOWER(taskstatus_title) REGEXP '(progreso|proceso|camino|progress|test|esperando|wait)'")
            )
            ->count() == 0;


        $prompt .= "## Tareas Vencidas\n";
        if ($overdueTasks->count()) {
            foreach ($overdueTasks as $task) {
                $prompt .= "- {$task->title} (Fecha de vencimiento: {$task->due_date})\n";
            }
        } else {
            $prompt .= "Ninguna.\n";
        }
        $prompt .= "\n## No hay Tareas en Progreso\n";
        $prompt .= $noTasksInProgress ? "Este miembro no tiene tareas en progreso.\n" : "Este miembro tiene tareas en progreso.\n";
        return $prompt;
    }

    /**
     * Get base data for a team member's weekly report (non-AI)
     * @param int $teamId
     * @return array|null
     */
    public function getMemberWeeklyReportData($teamId)
    {
        $member = User::where('type', 'team')->where('status', 'active')->where('id', $teamId)->first();
        if (!$member) {
            return null;
        }
        $now        = now();
        $oneWeekAgo = $now->copy()->subWeek();

        // 1️⃣ Completadas
        $completedTasks = $member->assignedTasks()
            ->whereHas('status', fn($q) =>
                $q->whereRaw("LOWER(taskstatus_title) REGEXP '(complet|finaliz|terminad|done|closed)'")
            )
            ->where('task_updated', '>=', $oneWeekAgo)
            ->get();

        // 2️⃣ En progreso
        $inProgressTasks = $member->assignedTasks()
            ->whereHas('status', fn($q) =>
                $q->whereRaw("LOWER(taskstatus_title) REGEXP '(progreso|proceso|camino|progress)'")
            )
            ->where('task_updated', '>=', $oneWeekAgo)
            ->get();

        // 3️⃣ Atrasadas (fecha vencida y no completadas)
        $overdueTasks = $member->assignedTasks()
            ->where('task_date_due', '<', $now)
            ->whereHas('status', fn($q) =>
                $q->whereRaw("LOWER(taskstatus_title) NOT REGEXP '(complet|finaliz|terminad|done|closed)'")
            )
            ->get();


        return [
            'member' => $member,
            'completedTasks' => $completedTasks,
            'inProgressTasks' => $inProgressTasks,
            'overdueTasks' => $overdueTasks,
        ];
    }

    /**
     * Get base data for a team member's general alerts (non-AI)
     * @param int $teamId
     * @return array|null
     */
    public function getMemberGeneralAlertsData($teamId)
    {
        $member = User::where('type', 'team')->where('status', 'active')->where('id', $teamId)->first();
        if (!$member) {
            return null;
        }
        $now = now();

        /* 1️⃣  Tareas vencidas (fecha pasada y no completadas) */
        $overdueTasks = $member->assignedTasks()
            ->where('task_date_due', '<', $now)
            ->whereHas('status', fn($q) =>
                $q->whereRaw("LOWER(taskstatus_title) NOT REGEXP '(complet|finaliz|terminad|done|closed)'")
            )
            ->get();

        /* 2️⃣  ¿Sin tareas en progreso?  (true si NO hay ninguna) */
        $noTasksInProgress = $member->assignedTasks()
            ->whereHas('status', fn($q) =>
                $q->whereRaw("LOWER(taskstatus_title) REGEXP '(progreso|proceso|camino|progress)'")
            )
            ->count() == 0;


        return [
            'member' => $member,
            'overdueTasks' => $overdueTasks,
            'noTasksInProgress' => $noTasksInProgress,
        ];
    }

    public function getWeeklyReportAIAnalysis($teamId)
    {
        $prompt = $this->generateMemberWeeklyReportPrompt($teamId);

        /* ---- Prompt “system” mejorado ---- */
        $systemPrompt = <<<SYS
                **Rol:** Eres un Scrum Master senior asistido por IA.  
                **Objetivo:** Analizar la actividad de los últimos **7 días** y generar un **reporte semanal ágil**.

                **Formato de salida (Markdown), máx. 250 palabras.**  
                Encabezado con nombre del empleado y rango de fechas.

                **Secciones obligatorias (usa exactamente estos títulos y emojis):**
                1. **📝 Resumen ejecutivo** (≤ 3 líneas).
                2. **🔄 Detalle de progreso**
                - ✅ Completadas
                - 🔄 En progreso
                - ⛔ Bloqueadas (tareas vencidas hace mucho).
                3. **📈 Participación en Leads** (mostrar SIEMPRE esta sección; si no hay datos, “—”)
                - **Creados:** lista hasta **3** en una sola línea cada uno → `ID #, Nombre, Estado, creado: AAAA-MM-DD`.
                - **Asignados con actividad:** lista hasta **3** → `ID #, Nombre, Estado, actualizado: AAAA-MM-DD hh:mm, último contacto: AAAA-MM-DD` (si existe).
                - **Comentarios del miembro:** lista hasta **2** → `fecha — Lead — "extracto ≤120 caracteres"`.
                - **Si hay que recortar por el límite de palabras, prioriza esta sección sobre explicaciones.**
                4. **🏁 Conclusión & Próximos pasos**
                - Acciones (bullets concretos).
                - Prioridades.
                - Riesgos.

                **Estilo:** claro, conciso y profesional; usa verbos en infinitivo (“Revisar”, “Desbloquear”).  
                **Regla:** No inventar datos. Solo usar lo provisto en el prompt. Si falta un campo, omitirlo sin rellenar.
            SYS;

        $messages = [
            [
                'role' => 'system',
                'content' => $systemPrompt
            ],
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ];
        $aiAnalysisMarkdown = null;
        $aiAnalysisError = null;
        try {
            $response = \OpenAI\Laravel\Facades\OpenAI::chat()->create([
                'model' => config('openai.model', 'gpt-3.5-turbo'),
                'messages' => $messages,
                'max_tokens' => 800,
                'temperature' => 0.7,
            ]);
            $aiAnalysisMarkdown = $response['choices'][0]['message']['content'] ?? '';
        } catch (\Exception $e) {
            $aiAnalysisError = $e->getMessage();
        }
        return [
            'aiAnalysisMarkdown' => $aiAnalysisMarkdown,
            'aiAnalysisError' => $aiAnalysisError,
        ];
    }

    public function getGeneralAlertsAIAnalysis($teamId)
    {
        $prompt = $this->generateMemberGeneralAlertsPrompt($teamId);
        $messages = [
            [
                'role' => 'system',
                'content' => 'Eres una IA experta en análisis de desempeño de equipos. Analiza las siguientes alertas generales y proporciona ideas y recomendaciones accionables en un formato breve, claro y profesional.'
            ],
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ];
        $aiAnalysisMarkdown = null;
        $aiAnalysisError = null;
        try {
            $response = \OpenAI\Laravel\Facades\OpenAI::chat()->create([
                'model' => config('openai.model', 'gpt-3.5-turbo'),
                'messages' => $messages,
                'max_tokens' => 800,
                'temperature' => 0.7,
            ]);
            $aiAnalysisMarkdown = $response['choices'][0]['message']['content'] ?? '';
        } catch (\Exception $e) {
            $aiAnalysisError = $e->getMessage();
        }
        return [
            'aiAnalysisMarkdown' => $aiAnalysisMarkdown,
            'aiAnalysisError' => $aiAnalysisError,
        ];
    }

    /**
     * Get base data for a team member's productivity (non-AI)
     * @param int $teamId
     * @return array|null
     */
    public function getMemberProductivityData($teamId)
    {
        $member = User::where('type', 'team')->where('status', 'active')->where('id', $teamId)->first();
        if (!$member) {
            return null;
        }
        $now        = now();
        $oneWeekAgo = $now->copy()->subWeek();

        /*
        |------------------------------------------------------
        | 1) Carga todas las tareas ACTUALIZADAS en la última semana
        |    + eager-load del estado para no disparar consultas N+1
        |------------------------------------------------------
        */
        $tasks = $member->assignedTasks()
            ->with('status')                 // Task → status()
            ->where('task_updated', '>=', $oneWeekAgo)
            ->get();

        /*
        |------------------------------------------------------
        | Helpers de coincidencia por REGEXP (minúsculas)
        |------------------------------------------------------
        */
        $isDone  = fn($name) => preg_match('/(complet|finaliz|terminad|done|closed)/i', $name);
        $isProg  = fn($name) => preg_match('/(progreso|proceso|camino|progress)/i',  $name);

        /*
        |------------------------------------------------------
        | 2) Filtrado en la colección (ya en memoria)
        |------------------------------------------------------
        */
        $completed   = $tasks->filter(fn($t) => $isDone(mb_strtolower($t->status->taskstatus_title ?? '')));

        $inProgress  = $tasks->filter(fn($t) => $isProg(mb_strtolower($t->status->taskstatus_title ?? '')));

        $overdue     = $tasks->filter(fn($t) =>
            $t->task_date_due && $t->task_date_due < $now &&
            !$isDone(mb_strtolower($t->status->taskstatus_title ?? ''))
        );


        $hoursWorked = $completed->sum('task_actual_hours');
        $avgCompletionTime = $completed->count() ? $completed->avg(function($task) {
            if ($task->task_started && $task->task_completed) {
                return strtotime($task->task_completed) - strtotime($task->task_started);
            }
            return null;
        }) : null;
        $metrics = [
            ['label' => 'Tareas completadas', 'value' => $completed->count()],
            ['label' => 'Tareas en progreso', 'value' => $inProgress->count()],
            ['label' => 'Tareas vencidas', 'value' => $overdue->count()],
            ['label' => 'Horas trabajadas', 'value' => round($hoursWorked, 1)],
        ];
        if ($avgCompletionTime) {
            $metrics[] = [
                'label' => 'Tiempo promedio de finalización',
                'value' => gmdate('H:i:s', (int) $avgCompletionTime)
            ];
        }
        return [
            'member' => $member,
            'productivityMetrics' => $metrics,
        ];
    }

    /**
     * Get AI analysis for a team member's productivity
     * @param int $teamId
     * @return array
     */
    public function getProductivityAIAnalysis($teamId)
    {
        $data = $this->getMemberProductivityData($teamId);
        if (!$data) {
            return [
                'aiAnalysisMarkdown' => null,
                'aiAnalysisError' => 'No se encontró ningún miembro de equipo con ese ID.'
            ];
        }
        $member = $data['member'];
        $metrics = $data['productivityMetrics'];
        $prompt = "# Informe de Productividad para {$member->full_name}\n\n";
        foreach ($metrics as $metric) {
            $prompt .= "- {$metric['label']}: {$metric['value']}\n";
        }
        $prompt .= "\nPor favor, analiza las métricas de productividad anteriores y proporciona ideas y sugerencias accionables para mejorar en formato markdown.";
        $messages = [
            [
                'role' => 'system',
                'content' => 'Eres una inteligencia artificial experta en análisis de productividad. Analiza las siguientes métricas de productividad y proporciona recomendaciones accionables en un formato breve, claro y profesional.'
            ],
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ];
        $aiAnalysisMarkdown = null;
        $aiAnalysisError = null;
        try {
            $response = \OpenAI\Laravel\Facades\OpenAI::chat()->create([
                'model' => config('openai.model', 'gpt-3.5-turbo'),
                'messages' => $messages,
                'max_tokens' => 800,
                'temperature' => 0.7,
            ]);
            $aiAnalysisMarkdown = $response['choices'][0]['message']['content'] ?? '';
        } catch (\Exception $e) {
            $aiAnalysisError = $e->getMessage();
        }
        return [
            'aiAnalysisMarkdown' => $aiAnalysisMarkdown,
            'aiAnalysisError' => $aiAnalysisError,
        ];
    }
} 