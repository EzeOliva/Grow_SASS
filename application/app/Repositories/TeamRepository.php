<?php
namespace App\Repositories;

use App\Models\User;
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
        $now = now();
        $oneWeekAgo = $now->copy()->subWeek();
        $prompt = "## <u>Informe semanal de {$member->full_name}</u>\n\n";
        $prompt .= "===============================\n\n";
        // 1️⃣ Completadas
        $completedTasks = $member->assignedTasks()
            ->with([
                'project:project_id,project_title,project_clientid',
                'project.client:client_id,client_company_name'
            ])
            ->whereHas('status', fn($q) =>
                $q->whereRaw("LOWER(taskstatus_title) REGEXP '(complet|finaliz|terminad|done|closed)'")
            )
            ->where('task_updated', '>=', $oneWeekAgo)
            ->get();

        // 2️⃣ En progreso
        $inProgressTasks = $member->assignedTasks()
            ->with([
                'project:project_id,project_title,project_clientid',
                'project.client:client_id,client_company_name'
            ])
            ->whereHas('status', fn($q) =>
                $q->whereRaw("LOWER(taskstatus_title) REGEXP '(progreso|proceso|camino|progress|test|esperando|wait)'")
            )
            ->where('task_updated', '>=', $oneWeekAgo)
            ->get();

        // 3️⃣ Atrasadas (fecha vencida y no completadas)
        $overdueTasks = $member->assignedTasks()
            ->with([
                'project:project_id,project_title,project_clientid',
                'project.client:client_id,client_company_name'
            ])
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
            **Objetivo:** Analizar la actividad de los últimos **7 días** y generar un **reporte semanal ágil** para el empleado indicado.

            **Instrucciones de salida**  
            - Formato **Markdown**, máx. 250 palabras.  
            - Encabezado con nombre del empleado y rango de fechas.  
            - Secciones obligatorias:  
            1. **Resumen ejecutivo** (≤ 3 líneas).  
            2. **Detalle de progreso**  
                - ✅ Completadas  
                - 🔄 En progreso  
                - ⛔ Bloqueadas (indicar causa).  
            3. **Conclusión & Próximos pasos** (acciones, prioridades, riesgos).  
            - Usa tono claro, conciso y profesional; verbos en infinitivo (“Revisar”, “Desbloquear”).  
            - Si una categoría no tiene datos, muestra “—” para mantener la estructura.
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