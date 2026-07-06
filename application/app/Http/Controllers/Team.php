<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for team
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\Common\CommonResponse;
use App\Http\Responses\Team\CreateResponse;
use App\Http\Responses\Team\EditResponse;
use App\Http\Responses\Team\IndexResponse;
use App\Http\Responses\Team\StoreResponse;
use App\Http\Responses\Team\UpdateResponse;
use App\Repositories\ProjectRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Repositories\TeamRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Validator;
use App\Models\Lead;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class Team extends Controller {

    /**
     * The roles repository instance.
     */
    protected $roles;

    /**
     * The users repository instance.
     */
    protected $userrepo;

    /**
     * The projectrepo repository instance.
     */
    protected $projectrepo;

    /**
     * The team repository instance.
     */
    protected $teamrepo;

    public function __construct(
        RoleRepository $roles,
        UserRepository $userrepo,
        ProjectRepository $projectrepo,
        TeamRepository $teamrepo) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        $this->middleware('teamMiddlewareIndex')->only([
            'index',
            'update',
            'store',
        ]);

        $this->middleware('teamMiddlewareCreate')->only([
            'create',
            'store',
        ]);

        $this->middleware('teamMiddlewareEdit')->only([
            'edit',
            'update',
            'destroy',
        ]);

        //dependencies
        $this->roles = $roles;
        $this->userrepo = $userrepo;
        $this->projectrepo = $projectrepo;
        $this->teamrepo = $teamrepo;
    }

    /**
     * Display a listing of team
     * @return \Illuminate\Http\Response
     */
    public function index() {

        //get team members
        request()->merge([
            'type' => 'team',
            'status' => 'active',
        ]);
        $users = $this->userrepo->search();

        //admin only - private productivity snapshot
        $adminTeamTaskStatsVisible = (bool) (config('visibility.action_super_user') ?? false);
        $adminTeamTaskStats = $adminTeamTaskStatsVisible ? $this->getAdminCompletedTasksByMemberStats() : collect();

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('team'),
            'users' => $users,
            'adminTeamTaskStatsVisible' => $adminTeamTaskStatsVisible,
            'adminTeamTaskStats' => $adminTeamTaskStats,
        ];

        //show views
        return new IndexResponse($payload);
    }

    /**
     * Admin-only metrics: completed tasks by team member for last 7, 30, 60 and 90 days.
     *
     * @return \Illuminate\Support\Collection
     */
    private function getAdminCompletedTasksByMemberStats()
    {
        $weekStart = now()->copy()->subDays(7);
        $monthStart = now()->copy()->subDays(30);
        $sixtyDaysStart = now()->copy()->subDays(60);
        $ninetyDaysStart = now()->copy()->subDays(90);
        $completedCondition = $this->getCompletedTaskConditionSql();

        return DB::table('users')
            ->leftJoin('roles', 'roles.role_id', '=', 'users.role_id')
            ->leftJoin('tasks_assigned', 'tasks_assigned.tasksassigned_userid', '=', 'users.id')
            ->leftJoin('tasks', 'tasks.task_id', '=', 'tasks_assigned.tasksassigned_taskid')
            ->leftJoin('tasks_status', 'tasks_status.taskstatus_id', '=', 'tasks.task_status')
            ->where('users.type', 'team')
            ->where('users.status', 'active')
            ->groupBy(
                'users.id',
                'users.first_name',
                'users.last_name',
                'users.position',
                'roles.role_name'
            )
            ->select(
                'users.id',
                'users.first_name',
                'users.last_name',
                'users.position',
                'roles.role_name'
            )
            ->selectRaw(
                "COUNT(DISTINCT CASE WHEN {$completedCondition} AND tasks.task_updated >= ? THEN tasks.task_id END) as completed_last_week",
                [$weekStart]
            )
            ->selectRaw(
                "COUNT(DISTINCT CASE WHEN {$completedCondition} AND tasks.task_updated >= ? THEN tasks.task_id END) as completed_last_month",
                [$monthStart]
            )
            ->selectRaw(
                "COUNT(DISTINCT CASE WHEN {$completedCondition} AND tasks.task_updated >= ? THEN tasks.task_id END) as completed_last_60_days",
                [$sixtyDaysStart]
            )
            ->selectRaw(
                "COUNT(DISTINCT CASE WHEN {$completedCondition} AND tasks.task_updated >= ? THEN tasks.task_id END) as completed_last_90_days",
                [$ninetyDaysStart]
            )
            ->orderByDesc('completed_last_90_days')
            ->orderByDesc('completed_last_60_days')
            ->orderByDesc('completed_last_month')
            ->orderByDesc('completed_last_week')
            ->orderBy('users.first_name')
            ->get();
    }

    /**
     * Admin-only modal content with completed tasks detail per member.
     *
     * @param int $id Team member id
     * @return \Illuminate\Http\JsonResponse
     */
    public function adminCompletedTasksModal($id)
    {
        if (!$this->canViewAdminTeamTaskStats()) {
            abort(403);
        }

        $member = DB::table('users')
            ->leftJoin('roles', 'roles.role_id', '=', 'users.role_id')
            ->where('users.id', $id)
            ->where('users.type', 'team')
            ->select('users.id', 'users.first_name', 'users.last_name', 'users.position', 'roles.role_name')
            ->first();

        if (!$member) {
            abort(404);
        }

        $weekStart = now()->copy()->subDays(7);
        $monthStart = now()->copy()->subDays(30);
        $sixtyDaysStart = now()->copy()->subDays(60);
        $ninetyDaysStart = now()->copy()->subDays(90);

        $completedLastWeekTasks = $this->getCompletedTasksByMemberSince($id, $weekStart);
        $completedLastMonthTasks = $this->getCompletedTasksByMemberSince($id, $monthStart);
        $completedLastSixtyDaysTasks = $this->getCompletedTasksByMemberSince($id, $sixtyDaysStart);
        $completedLastNinetyDaysTasks = $this->getCompletedTasksByMemberSince($id, $ninetyDaysStart);

        $html = view('pages.team.components.table.modals.admin-completed-tasks-list', [
            'member' => $member,
            'completedLastWeekTasks' => $completedLastWeekTasks,
            'completedLastMonthTasks' => $completedLastMonthTasks,
            'completedLastSixtyDaysTasks' => $completedLastSixtyDaysTasks,
            'completedLastNinetyDaysTasks' => $completedLastNinetyDaysTasks,
        ])->render();

        return response()->json([
            'dom_html' => [
                [
                    'selector' => '#commonModalBody',
                    'action' => 'replace',
                    'value' => $html,
                ],
            ],
            'dom_visibility' => [
                [
                    'selector' => '#commonModalFooter',
                    'action' => 'hide',
                ],
            ],
        ]);
    }

    /**
     * Determines if current user can view admin-only team task stats.
     *
     * @return bool
     */
    private function canViewAdminTeamTaskStats()
    {
        if (!auth()->check()) {
            return false;
        }

        $user = auth()->user();
        $roleTeamLevel = optional($user->role)->role_team;

        return (bool) ($user->is_admin || (int) $roleTeamLevel === 3);
    }

    /**
     * Returns SQL condition that determines if a task is considered completed.
     *
     * @return string
     */
    private function getCompletedTaskConditionSql()
    {
        $completedStatusValues = [
            '2',
            'completed',
            'complete',
            'done',
            'closed',
            'completado',
            'completada',
            'finalizado',
            'finalizada',
        ];

        $quotedStatusValues = "'" . implode("','", $completedStatusValues) . "'";

        return "(
            LOWER(CAST(tasks.task_status AS CHAR)) IN ({$quotedStatusValues})
            OR LOWER(COALESCE(tasks_status.taskstatus_title, '')) IN ({$quotedStatusValues})
            OR LOWER(COALESCE(tasks_status.taskstatus_title, '')) LIKE '%complet%'
            OR LOWER(COALESCE(tasks_status.taskstatus_title, '')) LIKE '%finaliz%'
        )";
    }

    /**
     * Get completed tasks for a team member from a given date.
     *
     * @param int $memberId
     * @param mixed $fromDate
     * @return \Illuminate\Support\Collection
     */
    private function getCompletedTasksByMemberSince($memberId, $fromDate)
    {
        $completedCondition = $this->getCompletedTaskConditionSql();

        return DB::table('tasks_assigned')
            ->join('tasks', 'tasks.task_id', '=', 'tasks_assigned.tasksassigned_taskid')
            ->leftJoin('tasks_status', 'tasks_status.taskstatus_id', '=', 'tasks.task_status')
            ->leftJoin('projects', 'projects.project_id', '=', 'tasks.task_projectid')
            ->where('tasks_assigned.tasksassigned_userid', $memberId)
            ->where('tasks.task_updated', '>=', $fromDate)
            ->whereRaw($completedCondition)
            ->select(
                'tasks.task_id',
                'tasks.task_title',
                'tasks.task_updated',
                'tasks.task_date_due',
                'tasks.task_status',
                'tasks_status.taskstatus_title',
                'projects.project_id',
                'projects.project_title'
            )
            ->distinct()
            ->orderByDesc('tasks.task_updated')
            ->limit(250)
            ->get();
    }

    /**
     * Show the form for creating a new team member
     * @return \Illuminate\Http\Response
     */
    public function create() {

        //get all team level roles
        $roles = $this->roles->allTeamRoles();

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('create'),
            'roles' => $roles,
        ];

        //show the form
        return new CreateResponse($payload);
    }

    /**
     * Store a newly created team member in storage.
     * @return \Illuminate\Http\Response
     */
    public function store() {

        //custom error messages
        $messages = [];

        //validate
        $validator = Validator::make(request()->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email',
            'role_id' => 'required|exists:roles,role_id',
        ], $messages);

        //errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }

            abort(409, $messages);
        }

        //if this is creating an admin user - check permissions
        if (!runtimeTeamCreateAdminPermissions(request('role_id'))) {
            abort(403);
        }

        //set other data
        request()->merge(['type' => 'team']);

        //save
        $password = str_random(9);
        if (!$userid = $this->userrepo->create(bcrypt($password))) {
            abort(409);
        }

        //get the user
        $users = $this->userrepo->search($userid);
        $user = $users->first();

        //update team user specific - default notification settings (defaults are set in config/settings.php)
        $user->notifications_projects_activity = 'yes_email';
        $user->notifications_billing_activity = 'yes_email';
        $user->notifications_new_assignement = 'yes_email';
        $user->notifications_leads_activity = 'yes_email';
        $user->notifications_tasks_activity = 'yes_email';
        $user->notifications_tickets_activity = 'yes_email';
        $user->notifications_system = 'yes_email';
        $user->force_password_change = config('settings.force_password_change');
        $user->pref_language = config('system.settings_system_language_default');
        $user->save();

        //create users space
        $space_uniqueid = $this->projectrepo->createUserSpace();
        $user->space_uniqueid = $space_uniqueid;
        $user->save();

        /** ----------------------------------------------
         * send email [comment
         * ----------------------------------------------*/
        //send to users
        $data = [
            'password' => $password,
        ];
        $mail = new \App\Mail\UserWelcome($user, $data);
        $mail->build();

        //reponse payload
        $payload = [
            'users' => $users,
        ];

        //process reponse
        return new StoreResponse($payload);

    }

    /**
     * Show the form for editing the specified team member
     * @param int $id team member id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        //get all team level roles
        $roles = $this->roles->allTeamRoles();

        //get the user
        $user = $this->userrepo->get($id);

        //check permissions
        if (!runtimeTeamPermissionEdit($user)) {
            abort(403);
        }

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('edit'),
            'roles' => $roles,
            'user' => $user,
        ];

        //process reponse
        return new EditResponse($payload);

    }

    /**
     * Update profile
     * @param int $id team member id
     * @return \Illuminate\Http\Response
     */
    public function update($id) {

        //get the user
        $user = $this->userrepo->get($id);

        //check permissions
        if (!runtimeTeamPermissionEdit($user)) {
            abort(403);
        }

        //custom error messages
        $messages = [
            'role_id.exists' => __('lang.user_role_not_found'),
        ];

        //validate the form
        $validator = Validator::make(request()->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => [
                'required',
                Rule::unique('users', 'email')->ignore($id, 'id'),
            ],
            'role_id' => 'nullable|exists:roles,role_id',
            'password' => 'nullable|confirmed|min:5',
        ], $messages);

        //validation errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }

            abort(409, $messages);
        }

        //update the user
        if (!$this->userrepo->update($id)) {
            abort(409);
        }

        //get user
        $users = $this->userrepo->search($id);

        //reponse payload
        $payload = [
            'users' => $users,
        ];

        //generate a response
        return new UpdateResponse($payload);
    }

    /**
     * Update preferences of logged in user
     * @return null silent
     */
    public function updatePreferences() {

        $this->userrepo->updatePreferences(auth()->id());

    }

    /**
     * Remove the specified team member from storage.
     * @param int $id team member id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        //get the user
        $user = $this->userrepo->get($id);

        //check permissions
        if (!runtimeTeamPermissionDelete($user)) {
            abort(403);
        }

        //delete project assignments
        \App\Models\ProjectAssigned::Where('projectsassigned_userid', $id)->delete();

        //delete task assignments
        \App\Models\TaskAssigned::Where('tasksassigned_userid', $id)->delete();

        //delete lead assignments
        \App\Models\LeadAssigned::Where('leadsassigned_userid', $id)->delete();

        //delete project manager
        \App\Models\ProjectManager::Where('projectsmanager_userid', $id)->delete();

        //delete calendar events
        \App\Models\CalendarEvent::Where('calendar_event_creatorid', $id)->delete();


        //make account as deleted
        $user->status = 'deleted';

        //remove user role
        $user->role_id = 0;

        //delete email
        $user->email = '';

        //delete password
        $user->password = '';

        //remove avater
        $user->avatar_filename = '';

        //update delete date
        $user->deleted = now();

        //save user
        $user->save();

        //reponse payload
        $payload = [
            'type' => 'remove-basic',
            'element' => "#team_$id",
        ];

        //generate a response
        return new CommonResponse($payload);
    }

    /**
     * AI Analysis - Team Weekly Report Tab
     * @return \Illuminate\Http\Response
     */
    public function analyzeAIWeeklyReport()
    {
        try {
            $teamId     = request('team_id');
            $oneWeekAgo = now()->copy()->subWeek();

            if ($teamId) {
                // Prompt base (tareas, alertas, etc.)
                $prompt = $this->teamrepo->generateMemberWeeklyReportPrompt($teamId);

                // 0) Detectar columnas de comments (para evitar 1054)
                $commentUserCol    = Schema::hasColumn('comments','comment_creatorid') ? 'comment_creatorid'
                                    : (Schema::hasColumn('comments','comment_userid') ? 'comment_userid'
                                    : (Schema::hasColumn('comments','user_id') ? 'user_id' : null));
                $commentCreatedCol = Schema::hasColumn('comments','comment_created') ? 'comment_created'
                                    : (Schema::hasColumn('comments','created_at') ? 'created_at'
                                    : (Schema::hasColumn('comments','comment_date') ? 'comment_date' : null));
                $commentTextCol    = Schema::hasColumn('comments','comment_text') ? 'comment_text'
                                    : (Schema::hasColumn('comments','text') ? 'text' : 'comment');

                // 1) SIEMPRE normalizamos la sección de leads: borramos bloque viejo
                $prompt = preg_replace(
                    '/^\s*---\s*\n\s*###\s*Participación en Leads[\s\S]*?(?=^\s*---|\z)/mi',
                    '',
                    $prompt
                );

                // 2) Traemos al miembro
                $member = \App\Models\User::where('type','team')->where('status','active')->find($teamId);

                // 3) ¿Hay actividad de leads esta semana?
                $hasLeadActivity =
                    Lead::where('lead_creatorid', $member->id)
                        ->where('lead_created', '>=', $oneWeekAgo)->exists()
                    ||
                    $member->assignedLeads()
                        ->where('leads.lead_updated', '>=', $oneWeekAgo)->exists()
                    ||
                    $member->assignedLeads()
                        ->whereNotNull('leads.lead_last_contacted')
                        ->where('leads.lead_last_contacted', '>=', $oneWeekAgo)->exists()
                    ||
                    (($commentUserCol && $commentCreatedCol) ? $member->assignedLeads()
                        ->whereHas('comments', function ($q) use ($member, $oneWeekAgo, $commentUserCol, $commentCreatedCol) {
                            $q->where($commentUserCol, $member->id)
                            ->where($commentCreatedCol, '>=', $oneWeekAgo);
                        })->exists() : false);

                // 4) Si no hay actividad, inyectamos sección vacía y seguimos
                if (!$hasLeadActivity) {
                    $prompt .= "\n---\n### Participación en Leads\n- **Creados:**\n  - —\n- **Asignados con actividad:**\n  - —\n- **Contactados:**\n  - —\n- **Resumen por estado:**\n  - —\n- **Comentarios del miembro:**\n  - —\n";
                } else {
                    // 5) Base: empresa/título y join a leads_status
                    $companyExpr = DB::raw("COALESCE(leads.lead_company_name, leads.lead_title) as lead_company");
                    $statusTitle = 'leads_status.leadstatus_title as lead_status_title';

                    $baseCols = [
                        'leads.lead_id',
                        'leads.lead_firstname',
                        'leads.lead_lastname',
                        'leads.lead_status',
                        'leads.lead_created',
                        'leads.lead_updated',
                        'leads.lead_last_contacted',
                        $companyExpr,
                        DB::raw($statusTitle),
                    ];

                    // 5.1 Creados por el miembro
                    $leadsCreadosSemana = \App\Models\Lead::from('leads')
                        ->leftJoin('leads_status', 'leads.lead_status', '=', 'leads_status.leadstatus_id')
                        ->where('lead_creatorid', $member->id)
                        ->where('lead_created', '>=', $oneWeekAgo)
                        ->orderBy('lead_created', 'desc')
                        ->get($baseCols);

                    // 5.2 Asignados con actividad
                    $leadsAsignadosConActividad = $member->assignedLeads()
                        ->leftJoin('leads_status', 'leads.lead_status', '=', 'leads_status.leadstatus_id')
                        ->where('leads.lead_updated', '>=', $oneWeekAgo)
                        ->orderBy('leads.lead_updated', 'desc')
                        ->get($baseCols);

                    // 5.3 Contactados esta semana
                    $leadsContactadosSemana = $member->assignedLeads()
                        ->leftJoin('leads_status', 'leads.lead_status', '=', 'leads_status.leadstatus_id')
                        ->whereNotNull('leads.lead_last_contacted')
                        ->where('leads.lead_last_contacted', '>=', $oneWeekAgo)
                        ->orderBy('leads.lead_last_contacted', 'desc')
                        ->get($baseCols);

                    // 5.4 Resumen por estado (por TÍTULO)
                    $leadsPorEstado = $member->assignedLeads()
                        ->leftJoin('leads_status', 'leads.lead_status', '=', 'leads_status.leadstatus_id')
                        ->where('leads.lead_updated', '>=', $oneWeekAgo)
                        ->groupBy('leads_status.leadstatus_title')
                        ->selectRaw('leads_status.leadstatus_title as title, COUNT(*) as total')
                        ->pluck('total','title');

                    // 5.5 Comentarios del miembro (incluye empresa/contacto)
                    $leadsConComentarios = collect();
                    $totalComentariosSemana = 0;
                    if ($commentUserCol && $commentCreatedCol) {
                        $leadsConComentarios = $member->assignedLeads()
                            ->leftJoin('leads_status', 'leads.lead_status', '=', 'leads_status.leadstatus_id')
                            ->withCount(['comments as comments_semana_count' => function ($q) use ($member, $oneWeekAgo, $commentUserCol, $commentCreatedCol) {
                                $q->where($commentUserCol, $member->id)
                                ->where($commentCreatedCol, '>=', $oneWeekAgo);
                            }])
                            ->with(['comments' => function ($q) use ($member, $oneWeekAgo, $commentUserCol, $commentCreatedCol) {
                                $q->where($commentUserCol, $member->id)
                                ->where($commentCreatedCol, '>=', $oneWeekAgo)
                                ->orderBy($commentCreatedCol, 'desc');
                            }])
                            ->orderBy('leads.lead_updated','desc')
                            ->get($baseCols)
                            ->filter(fn($l) => (int)($l->comments_semana_count ?? 0) > 0);

                        $totalComentariosSemana = $leadsConComentarios->sum('comments_semana_count');
                    }

                    // 6) Render ordenado y con títulos
                    $leadsBlock  = "\n---\n";
                    $leadsBlock .= "### Participación en Leads\n";

                    $fmtLead = function($l, $tipoFecha) {
                        $empresa  = $l->lead_company ?? '—';
                        $contacto = trim(($l->lead_firstname ?? '').' '.($l->lead_lastname ?? ''));
                        $estado   = $l->lead_status_title ?? ($l->lead_status !== null ? "Estado #{$l->lead_status}" : '—');
                        $fecha    = $tipoFecha === 'actualizado' ? ($l->lead_updated ?? '')
                                : ($tipoFecha === 'contacto'   ? ($l->lead_last_contacted ?? '')
                                                                : ($l->lead_created ?? ''));
                        $label    = $tipoFecha === 'actualizado' ? 'actualizado'
                                : ($tipoFecha === 'contacto'   ? 'último contacto' : 'creado');
                        return "  - #{$l->lead_id} • Empresa: {$empresa} • Contacto: {$contacto} • Estado: {$estado} • {$label}: {$fecha}\n";
                    };

                    // Creados
                    $leadsBlock .= "- **Creados:**\n";
                    if ($leadsCreadosSemana->isEmpty()) {
                        $leadsBlock .= "  - —\n";
                    } else {
                        foreach ($leadsCreadosSemana->take(5) as $l) { $leadsBlock .= $fmtLead($l, 'creado'); }
                        if ($leadsCreadosSemana->count() > 5) $leadsBlock .= "  - (+".($leadsCreadosSemana->count()-5)." más)\n";
                    }

                    // Asignados con actividad
                    $leadsBlock .= "- **Asignados con actividad:**\n";
                    if ($leadsAsignadosConActividad->isEmpty()) {
                        $leadsBlock .= "  - —\n";
                    } else {
                        foreach ($leadsAsignadosConActividad->take(5) as $l) { $leadsBlock .= $fmtLead($l, 'actualizado'); }
                        if ($leadsAsignadosConActividad->count() > 5) $leadsBlock .= "  - (+".($leadsAsignadosConActividad->count()-5)." más)\n";
                    }

                    // Contactados
                    $leadsBlock .= "- **Contactados:**\n";
                    if ($leadsContactadosSemana->isEmpty()) {
                        $leadsBlock .= "  - —\n";
                    } else {
                        foreach ($leadsContactadosSemana->take(5) as $l) { $leadsBlock .= $fmtLead($l, 'contacto'); }
                        if ($leadsContactadosSemana->count() > 5) $leadsBlock .= "  - (+".($leadsContactadosSemana->count()-5)." más)\n";
                    }

                    // Resumen por estado (por TÍTULO)
                    $leadsBlock .= "- **Resumen por estado:**\n";
                    if ($leadsPorEstado && $leadsPorEstado->count()) {
                        foreach ($leadsPorEstado as $titulo => $total) {
                            $leadsBlock .= "  - {$titulo}: {$total}\n";
                        }
                    } else {
                        $leadsBlock .= "  - —\n";
                    }

                    // Comentarios del miembro: Empresa — Contacto — "texto"
                    $leadsBlock .= "- **Comentarios del miembro:**\n";
                    if ($totalComentariosSemana <= 0) {
                        $leadsBlock .= "  - —\n";
                    } else {
                        $ultimosTres = $leadsConComentarios
                            ->flatMap(fn ($lead) => $lead->comments)
                            ->sortByDesc(fn ($c) => $c->$commentCreatedCol ?? $c->created_at)
                            ->take(3);

                        foreach ($ultimosTres as $c) {
                            $leadId  = $c->commentresource_id ?? null;
                            $leadRef = $leadId ? $leadsConComentarios->firstWhere('lead_id', $leadId) : null;
                            $empresa = $leadRef->lead_company ?? '—';
                            $contacto = $leadRef ? trim(($leadRef->lead_firstname ?? '').' '.($leadRef->lead_lastname ?? '')) : '—';
                            $texto = $c->$commentTextCol ?? '';
                            if (is_string($texto) && mb_strlen($texto) > 120) $texto = mb_substr($texto, 0, 117).'...';
                            $cuando = $c->$commentCreatedCol ?? ($c->created_at ?? '');
                            $leadsBlock .= "  - {$cuando} — {$empresa} — {$contacto} — \"{$texto}\"\n";
                        }
                    }

                    // Inyectamos el bloque
                    $prompt .= $leadsBlock;
                }

            } else {
                // Reporte general del equipo (sin foco de miembro)
                $prompt = $this->userrepo->generateTeamWeeklyReportPrompt();
            }

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


            /* ---- Mensajes para la llamada a la API ---- */
            $messages = [
                [ 'role' => 'system', 'content' => $systemPrompt ],
                [ 'role' => 'user',   'content' => $prompt ] // tareas + (potencial) leads
            ];

            $aiResponse = $this->callOpenAI($messages);

            $payload = [
                'aiPrompt'   => $prompt,
                'aiAnalysis' => $aiResponse,
            ];

            return new \App\Http\Responses\Team\AnalyzeAIWeeklyReportAIResponse($payload);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'AI analysis failed: ' . $e->getMessage()
            ]);
        }
    }


    /**
     * Show the Team AI Analysis modal (AJAX)
     */
    public function analyzeAIModal()
    {
        $teamId = request('team_id');
        $team = null;
        if ($teamId) {
            $team = \App\Models\User::where('type', 'team')->where('id', $teamId)->first();
        }
        $payload = [
            'team' => $team,
        ];
        return new \App\Http\Responses\Team\AnalyzeAIModalResponse($payload);
    }

    /**
     * AI Analysis - Team General Alerts Tab
     * @return \Illuminate\Http\Response
     */
    public function analyzeAIGeneralAlerts()
    {
        try {
            $teamId = request('team_id');
            if ($teamId) {
                // You can customize the prompt logic for alerts here
                $prompt = $this->teamrepo->generateMemberGeneralAlertsPrompt($teamId);
            } else {
                $prompt = $this->userrepo->generateTeamGeneralAlertsPrompt();
            }
            $messages = [
                [
                    'role' => 'system',
                    'content' => 'Eres una IA experta en análisis del desempeño de equipos. Analiza las siguientes alertas y cuellos de botella generales, y proporciona recomendaciones accionables en un formato claro y profesional.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ];
            $aiResponse = $this->callOpenAI($messages);
            $payload = [
                'aiPrompt' => $prompt,
                'aiAnalysis' => $aiResponse,
            ];
            return new \App\Http\Responses\Team\AnalyzeAIGeneralAlertsAIResponse($payload);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'AI analysis failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Base Data - Team Weekly Report Tab (non-AI)
     */
    public function baseWeeklyReport()
    {
        $teamId = request('team_id');
        $data = $this->teamrepo->getMemberWeeklyReportData($teamId);
        if (!$data) {
            $html = '<div class="alert alert-danger">Team member not found.</div>';
            return new \App\Http\Responses\Team\AnalyzeAIWeeklyReportBaseResponse(['html' => $html]);
        }
        return new \App\Http\Responses\Team\AnalyzeAIWeeklyReportBaseResponse($data);
    }

    /**
     * AI Analysis - Team Weekly Report Tab
     */
    public function aiWeeklyReport()
    {
        $teamId = request('team_id');
        $data = $this->teamrepo->getWeeklyReportAIAnalysis($teamId);
        $html = view('pages.team.views.modals.tabs.weekly_report_analysis_ai', $data)->render();
        return response()->json([
            'dom_html' => [[
                'selector' => '.ai-analysis-result',
                'action' => 'replace',
                'value' => $html
            ]],
            'postrun_functions' => ['convertTeamAIMarkdown']
        ]);
    }

    /**
     * Base Data - Team General Alerts Tab (non-AI)
     */
    public function baseGeneralAlerts()
    {
        $teamId = request('team_id');
        $data = $this->teamrepo->getMemberGeneralAlertsData($teamId);
        if (!$data) {
            return new \App\Http\Responses\Team\AnalyzeAIGeneralAlertsBaseResponse([ 'html' => '<div class="alert alert-danger">Team member not found.</div>' ]);
        }
        return new \App\Http\Responses\Team\AnalyzeAIGeneralAlertsBaseResponse($data);
    }

    /**
     * AI Analysis - Team General Alerts Tab
     */
    public function aiGeneralAlerts()
    {
        $teamId = request('team_id');
        $data = $this->teamrepo->getGeneralAlertsAIAnalysis($teamId);
        $html = view('pages.team.views.modals.tabs.general_alerts_analysis_ai', $data)->render();
        return response()->json([
            'dom_html' => [[
                'selector' => '.ai-analysis-result',
                'action' => 'replace',
                'value' => $html
            ]],
            'postrun_functions' => ['convertTeamAIMarkdown']
        ]);
    }

    /**
     * Base Data - Team Productivity Tab (non-AI)
     */
    public function baseProductivity()
    {
        $teamId = request('team_id');
        $data = $this->teamrepo->getMemberProductivityData($teamId);
        if (!$data) {
            $html = '<div class="alert alert-danger">Team member not found.</div>';
            return new \App\Http\Responses\Team\AnalyzeAIProductivityBaseResponse(['html' => $html]);
        }
        return new \App\Http\Responses\Team\AnalyzeAIProductivityBaseResponse($data);
    }

    /**
     * AI Analysis - Team Productivity Tab
     */
    public function aiProductivity()
    {
        $teamId = request('team_id');
        $data = $this->teamrepo->getProductivityAIAnalysis($teamId);
        return new \App\Http\Responses\Team\AnalyzeAIProductivityAIResponse($data);
    }

    /**
     * Call OpenAI API
     */
    private function callOpenAI($messages)
    {
        try {
            $response = \OpenAI\Laravel\Facades\OpenAI::chat()->create([
                'model' => config('openai.model', 'gpt-3.5-turbo'),
                'messages' => $messages,
                'max_tokens' => 1000,
                'temperature' => 0.7
            ]);

            return $response['choices'][0]['message']['content'];

        } catch (\OpenAI\Exceptions\RateLimitException $e) {
            throw new \Exception('Rate limit exceeded. Please try again later.');
        } catch (\OpenAI\Exceptions\AuthenticationException $e) {
            throw new \Exception('AI service authentication failed.');
        } catch (\OpenAI\Exceptions\ErrorException $e) {
            throw new \Exception('AI service error: ' . $e->getMessage());
        } catch (\OpenAI\Exceptions\TransporterException $e) {
            throw new \Exception('Connection error. Please check your internet connection.');
        } catch (\Exception $e) {
            throw new \Exception('AI analysis failed: ' . $e->getMessage());
        }
    }

    /**
     * basic page setting for this section of the app
     * @param string $section page section (optional)
     * @param array $data any other data (optional)
     * @return array
     */
    private function pageSettings($section = '', $data = []) {

        //common settings
        $page = [
            'crumbs' => [
                __('lang.team_members'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'page' => 'team',
            'no_results_message' => __('lang.no_results_found'),
            'mainmenu_settings' => 'active',
            'submenu_team' => 'active',
            'sidepanel_id' => 'sidepanel-filter-team',
            'dynamic_search_url' => 'team/search?source=' . request('source') . '&action=search',
            'add_button_classes' => '',
            'load_more_button_route' => 'team',
            'source' => 'list',
        ];

        //default modal settings (modify for sepecif sections)
        $page += [
            'add_modal_title' => __('lang.add_user'),
            'add_modal_create_url' => url('team/create'),
            'add_modal_action_url' => url('team'),
            'add_modal_action_ajax_class' => '',
            'add_modal_action_ajax_loading_target' => 'commonModalBody',
            'add_modal_action_method' => 'POST',
        ];

        //contracts list page
        if ($section == 'team') {
            $page += [
                'meta_title' => __('lang.team_members'),
                'heading' => __('lang.team_members'),
            ];
            if (request('source') == 'ext') {
                $page += [
                    'list_page_actions_size' => 'col-lg-12',
                ];
            }
            return $page;
        }

        //create new resource
        if ($section == 'create') {
            $page += [
                'section' => 'create',
                'create_type' => 'team',
            ];
            return $page;
        }

        //edit new resource
        if ($section == 'edit') {
            $page += [
                'section' => 'edit',
            ];
            return $page;
        }

        //ext page settings
        if ($section == 'ext') {

            $page += [
                'list_page_actions_size' => 'col-lg-12',

            ];

            return $page;
        }

        //return
        return $page;
    }
}