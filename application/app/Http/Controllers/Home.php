<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for home page
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers;
use App\Http\Responses\Home\HomeResponse;
use App\Repositories\EventRepository;
use App\Repositories\EventTrackingRepository;
use App\Repositories\LeadRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\StatsRepository;
use App\Repositories\TaskRepository;
use App\Repositories\ClientRepository;
use App\Repositories\FeedbackRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class Home extends Controller {

    private $page = array();

    protected $statsrepo;
    protected $eventsrepo;
    protected $trackingrepo;
    protected $projectrepo;
    protected $taskrepo;
    protected $leadrepo;
    protected $clientrepo;
    protected $feedbackrepo;

    public function __construct(
        StatsRepository $statsrepo,
        EventRepository $eventsrepo,
        EventTrackingRepository $trackingrepo,
        ProjectRepository $projectrepo,
        TaskRepository $taskrepo,
        LeadRepository $leadrepo,
        ClientRepository $clientrepo,
        FeedbackRepository $feedbackrepo
    ) {

        //parent
        parent::__construct();

        $this->statsrepo = $statsrepo;
        $this->eventsrepo = $eventsrepo;
        $this->trackingrepo = $trackingrepo;
        $this->projectrepo = $projectrepo;
        $this->taskrepo = $taskrepo; 
        $this->leadrepo = $leadrepo;  
        $this->clientrepo = $clientrepo;  
        $this->feedbackrepo = $feedbackrepo;  

        //authenticated
        $this->middleware('auth');

        $this->middleware('homeMiddlewareIndex')->only([
            'index',
        ]);
    }

    /**
     * Display the home page
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $page = $this->pageSettings();

        $payload = [];

        //Team Dashboards
        if (auth()->user()->type == 'team') {
            //admin user
            if (auth()->user()->is_admin) {
                //get payload
                $payload = $this->adminDashboard();
            }
            //team uder
            if (!auth()->user()->is_admin) {
                //get payload
                $payload = $this->teamDashboard();
            }
        }

        //Client Dashboards
        if (auth()->user()->type == 'client') {
            //get payload
            $payload = $this->clientDashboard();

        }

        //[AFFILIATE]
        if (config('settings.custom_modules.cs_affiliate')) {
            if (auth()->user()->type == 'cs_affiliate') {
                //get payload
                $payload = $this->csAffiliateDashboard();
                return view('pages/cs_affiliates/home/home', compact('page', 'payload'));
            }
        }

        //page
        $payload['page'] = $page;

        //process reponse
        return new HomeResponse($payload);

    }

    /**
     * [AFFILIATE]
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function csAffiliateDashboard() {

        //get events
        $events = \App\Models\Custom\CSEvent::Where('cs_event_affliateid', auth()->id())->orderBy('cs_event_id', 'DESC')
            ->take(100)
            ->get();

        //get projects
        $projects = \App\Models\Custom\CSAffiliateProject::leftJoin('projects', 'projects.project_id', '=', 'cs_affiliate_projects.cs_affiliate_project_projectid')
            ->selectRaw('*')
            ->Where('cs_affiliate_project_affiliateid', auth()->id())
            ->where('cs_affiliate_project_status', 'active')
            ->orderBy('cs_affiliate_project_id', 'DESC')
            ->take(100)
            ->get();

        //Profits - today
        $today = \Carbon\Carbon::now()->format('Y-m-d');
        $profits['today'] = \App\Models\Custom\CSAffiliateEarning::where('cs_affiliate_earning_commission_approval_date', $today)
            ->where('cs_affiliate_earning_affiliateid', auth()->id())
            ->where('cs_affiliate_earning_status', 'paid')
            ->sum('cs_affiliate_earning_amount');

        //Profits - today
        $start = \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
        $end = \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d');
        $profits['this_month'] = \App\Models\Custom\CSAffiliateEarning::where('cs_affiliate_earning_commission_approval_date', '>=', $start)
            ->where('cs_affiliate_earning_commission_approval_date', '<=', $end)
            ->where('cs_affiliate_earning_status', 'paid')
            ->where('cs_affiliate_earning_affiliateid', auth()->id())
            ->sum('cs_affiliate_earning_amount');

        //Profits - all time
        $profits['all_time'] = \App\Models\Custom\CSAffiliateEarning::where('cs_affiliate_earning_affiliateid', auth()->id())
            ->where('cs_affiliate_earning_status', 'paid')
            ->sum('cs_affiliate_earning_amount');

        //Profits - pending
        $profits['pending'] = \App\Models\Custom\CSAffiliateEarning::where('cs_affiliate_earning_affiliateid', auth()->id())
            ->where('cs_affiliate_earning_status', 'unpaid')
            ->sum('cs_affiliate_earning_amount');

        $payload = [
            'events' => $events,
            'projects' => $projects,
            'profits' => $profits,
        ];

        return $payload;

    }

    /**
     * display team dashboard
     * @return \Illuminate\Http\Response
     */
    public function teamDashboard() {

        //payload
        $payload = [];

        //[projects][all]
        $payload['projects'] = [
            'pending' => $this->statsrepo->countProjects([
                'status' => 'pending',
                'assigned' => auth()->id(),
            ]),
        ];

        //tasks]
        $payload['tasks'] = [
            'new' => $this->statsrepo->countTasks([
                'status' => 'new',
                'assigned' => auth()->id(),
            ]),
            'pending' => $this->statsrepo->countTasks([
                'status' => 'pending',
                'assigned' => auth()->id(),
            ]),
            'completed' => $this->statsrepo->countTasks([
                'status' => 'completed',
                'assigned' => auth()->id(),
            ]),
        ];

        //filter
        request()->merge([
            'eventtracking_userid' => auth()->id(),
        ]);
        $payload['all_events'] = $this->trackingrepo->search(20);

        //filter
        request()->merge([
            'filter_assigned' => [auth()->id()],
        ]);
        $payload['my_projects'] = $this->projectrepo->search('', ['limit' => 30]);

        //return payload
        return $payload;

    }

    /**
     * display client dashboard
     * @return \Illuminate\Http\Response
     */
    public function clientDashboard() {

        //payload
        $payload = [];

        //[projects][all]
        $payload['projects'] = [
            'pending' => $this->statsrepo->countProjects([
                'status' => 'pending',
                'client_id' => auth()->user()->clientid,
            ]),
            'completed' => $this->statsrepo->countProjects([
                'status' => 'completed',
                'client_id' => auth()->user()->clientid,
            ]),
        ];

        //filter
        request()->merge([
            'eventtracking_userid' => auth()->id(),
        ]);
        $payload['all_events'] = $this->trackingrepo->search(20);

        //filter
        request()->merge([
            'filter_project_clientid' => auth()->user()->clientid,
        ]);
        $payload['my_projects'] = $this->projectrepo->search('', ['limit' => 30]);

        $payload['stats'] = $this->clientrepo->getCustomerSuccessStats((int) auth()->user()->clientid);
        $payload['client_stage_title'] = 'Sin etapa';

        if (Schema::hasTable('client_stages') && Schema::hasColumn('clients', 'client_stage_id')) {
            $stageTitle = DB::table('clients')
                ->leftJoin('client_stages', 'client_stages.client_stage_id', '=', 'clients.client_stage_id')
                ->where('clients.client_id', (int) auth()->user()->clientid)
                ->value('client_stages.client_stage_title');

            if (!empty($stageTitle)) {
                $payload['client_stage_title'] = $stageTitle;
            }
        }

        //return payload
        return $payload;

    }

    /**
     * display admin User
     * @return \Illuminate\Http\Response
     */
    public function adminDashboard() {

        //payload
        $payload = [];

        //[projects][all]
        $payload['all_projects'] = [
            'not_started' => $this->statsrepo->countProjects([
                'status' => 'not_started',
            ]),
            'in_progress' => $this->statsrepo->countProjects([
                'status' =>
                'in_progress',
            ]),
            'on_hold' => $this->statsrepo->countProjects([
                'status' => 'on_hold',
            ]),
            'completed' => $this->statsrepo->countProjects([
                'status' => 'completed',
            ]),
        ];

        //[projects][ny]
        $payload['my_projects'] = [
            'not_started' => $this->statsrepo->countProjects([
                'status' => 'not_started',
                'assigned' => auth()->id(),
            ]),
            'in_progress' => $this->statsrepo->countProjects([
                'status' => 'in_progress',
                'assigned' => auth()->id(),
            ]),
            'on_hold' => $this->statsrepo->countProjects([
                'status' => 'on_hold',
                'assigned' => auth()->id(),
            ]),
            'completed' => $this->statsrepo->countProjects([
                'status' => 'completed',
                'assigned' => auth()->id(),
            ]),
        ];

        //filter
        $payload['all_events'] = $this->eventsrepo->search([
            'pagination' => 20,
            'filter' => 'timeline_visible',
        ]);

        $health = $this->buildHealthOverviewPayload();
        $payload['health_overview'] = $health['overview'];
        $payload['stage_health_snapshot'] = $health['stage_snapshot'];
        $payload['stats'] = $this->clientrepo->getCustomerSuccessStats();
        $payload['feedbacks'] = $this->feedbackrepo->getFeedbackSummariesForClient(0, null, 20);

        //return payload
        return $payload;

    }

    /**
     * Build health-focused home dashboard aggregates for admin users.
     *
     * @return array
     */
    private function buildHealthOverviewPayload() {

        $healthStatusCaseSql = $this->clientHealthStatusCaseSql();

        $healthRows = DB::table('clients')
            ->selectRaw("{$healthStatusCaseSql} as health_status, COUNT(*) as total")
            ->where('clients.client_id', '>', 0)
            ->groupBy('health_status')
            ->pluck('total', 'health_status');

        $totalClients = (int) DB::table('clients')
            ->where('clients.client_id', '>', 0)
            ->count();

        $withoutRecentFeedback = (int) DB::table('clients')
            ->leftJoin('feedbacks', function ($join) {
                $join->on('feedbacks.client_id', '=', 'clients.client_id')
                    ->where('feedbacks.feedback_created', '>=', now()->subMonths(3)->format('Y-m-d H:i:s'));
            })
            ->where('clients.client_id', '>', 0)
            ->whereNull('feedbacks.feedback_id')
            ->count('clients.client_id');

        $overview = [
            'green' => (int) ($healthRows['green'] ?? 0),
            'yellow' => (int) ($healthRows['yellow'] ?? 0),
            'red' => (int) ($healthRows['red'] ?? 0),
            'total_clients' => $totalClients,
            'without_recent_feedback' => $withoutRecentFeedback,
        ];

        $overview['at_risk'] = (int) ($overview['yellow'] + $overview['red']);
        $overview['at_risk_percent'] = $overview['total_clients'] > 0
            ? (int) round(($overview['at_risk'] * 100) / $overview['total_clients'])
            : 0;

        if (Schema::hasTable('client_stages') && Schema::hasColumn('clients', 'client_stage_id')) {
            $stageRows = DB::table('clients')
                ->leftJoin('client_stages', 'client_stages.client_stage_id', '=', 'clients.client_stage_id')
                ->where('clients.client_id', '>', 0)
                ->selectRaw('COALESCE(client_stages.client_stage_title, "Sin etapa") as stage_title')
                ->selectRaw('COALESCE(client_stages.client_stage_position, 999999) as stage_position')
                ->selectRaw('COUNT(*) as total_clients')
                ->selectRaw("SUM(CASE WHEN {$healthStatusCaseSql} = 'red' THEN 1 ELSE 0 END) as red_clients")
                ->selectRaw("SUM(CASE WHEN {$healthStatusCaseSql} = 'yellow' THEN 1 ELSE 0 END) as yellow_clients")
                ->groupBy('stage_title', 'stage_position')
                ->orderBy('stage_position', 'asc')
                ->orderBy('stage_title', 'asc')
                ->get();
        } else {
            $stageRows = DB::table('clients')
                ->where('clients.client_id', '>', 0)
                ->selectRaw('"Sin etapa" as stage_title')
                ->selectRaw('999999 as stage_position')
                ->selectRaw('COUNT(*) as total_clients')
                ->selectRaw("SUM(CASE WHEN {$healthStatusCaseSql} = 'red' THEN 1 ELSE 0 END) as red_clients")
                ->selectRaw("SUM(CASE WHEN {$healthStatusCaseSql} = 'yellow' THEN 1 ELSE 0 END) as yellow_clients")
                ->get();
        }

        $stageSnapshot = $stageRows->map(function ($row) {
            $total = (int) ($row->total_clients ?? 0);
            $red = (int) ($row->red_clients ?? 0);
            $yellow = (int) ($row->yellow_clients ?? 0);
            $atRisk = $red + $yellow;

            return [
                'stage_title' => (string) ($row->stage_title ?? 'Sin etapa'),
                'total_clients' => $total,
                'red_clients' => $red,
                'yellow_clients' => $yellow,
                'at_risk_clients' => $atRisk,
                'at_risk_percent' => $total > 0 ? (int) round(($atRisk * 100) / $total) : 0,
            ];
        })->values();

        return [
            'overview' => $overview,
            'stage_snapshot' => $stageSnapshot,
        ];
    }

    /**
     * Reusable SQL snippet that computes the health status for a client.
     *
     * @return string
     */
    private function clientHealthStatusCaseSql() {
        return '(
            CASE
                WHEN
                    (
                        SELECT
                            CASE WHEN SUM(weight) > 0
                                THEN ROUND(SUM(CASE WHEN status = "fulfilled" THEN weight ELSE 0 END) * 100 / SUM(weight), 0)
                                ELSE 0
                            END
                        FROM client_expectations
                        WHERE client_id = clients.client_id
                    ) >= 70
                    AND
                    (
                        SELECT ROUND(SUM(q.weight * d.value) * 10 / NULLIF(SUM(q.weight * q.range), 0), 2)
                        FROM feedback_details d
                        JOIN feedbacks f ON f.feedback_id = d.feedback_id
                        JOIN feedback_queries q ON q.feedback_query_id = d.feedback_query_id
                        WHERE f.client_id = clients.client_id
                    ) >= 7
                THEN "green"
                WHEN
                    (
                        (
                            SELECT
                                CASE WHEN SUM(weight) > 0
                                    THEN ROUND(SUM(CASE WHEN status = "fulfilled" THEN weight ELSE 0 END) * 100 / SUM(weight), 0)
                                    ELSE 0
                                END
                            FROM client_expectations
                            WHERE client_id = clients.client_id
                        ) BETWEEN 40 AND 69
                    )
                    OR
                    (
                        (
                            SELECT ROUND(SUM(q.weight * d.value) * 10 / NULLIF(SUM(q.weight * q.range), 0), 2)
                            FROM feedback_details d
                            JOIN feedbacks f ON f.feedback_id = d.feedback_id
                            JOIN feedback_queries q ON q.feedback_query_id = d.feedback_query_id
                            WHERE f.client_id = clients.client_id
                        ) BETWEEN 5 AND 6
                    )
                THEN "yellow"
                ELSE "red"
            END
        )';
    }

    /**
     * create a leads widget
     * [UPCOMING] call this via ajax for dynamically changing dashboad filters
     * @param string $filter [alltime|...]  //add as we go
     * @return \Illuminate\Http\Response
     */
    public function widgetLeads($filter) {

        $payload['stats'] = [];
        $payload['leads_key_colors'] = [];
        $payload['leads_chart_center_title'] = __('lang.leads');

        $counter = 0;

        //do this for each lead category
        foreach (config('home.lead_statuses') as $status) {

            //count all leads
            if ($filter = 'alltime') {
                $count = $this->statsrepo->countLeads(
                    [
                        'status' => $status['id'],
                    ]);
            }

            //add to array
            $payload['stats'][] = [
                $status['title'], $count,
            ];

            //add to counter
            $counter += $count;

            $payload['leads_key_colors'][] = $status['colorcode'];

        }

        // no lead in system - display something (No Leads - 100%) in chart
        if ($counter == 0) {
            $payload['stats'][] = [
                'No Leads', 1,
            ];
            $payload['leads_key_colors'][] = "#eff4f5";
            $payload['leads_chart_center_title'] = __('lang.no_leads');
        }

        return $payload;
    }

/**
 * generate a chart to show the following ticket stats
 * @param string $filter [alltime|thisyear]  //add as we go
 * @return array
 */
    public function widgetTickets($filter = 'thisyear') {

        $payload['stats'] = [];
        $payload['tickets_key_colors'] = [];
        $payload['tickets_chart_center_title'] = __('lang.tickets');
        $payload['ticket_statuses'] = [];

        $counter = 0;

        // Get all ticket statuses from database
        $statuses = \App\Models\TicketStatus::orderBy('ticketstatus_position', 'asc')->get();

        // Count tickets for each status
        $year_start = \Carbon\Carbon::now()->startOfYear()->format('Y-m-d');
        $year_end = \Carbon\Carbon::now()->endOfYear()->format('Y-m-d');

        $ticket_statuses = [];

        // Loop through each status
        foreach ($statuses as $status) {

            $count = \App\Models\Ticket::where('ticket_status', $status->ticketstatus_id)
                ->where('ticket_created', '>=', $year_start)
                ->where('ticket_created', '<=', $year_end)
                ->count();

            // Store the original title and the title with count
            $payload['ticket_statuses'][] = [
                'color' => $status->ticketstatus_color,
                'title' => $status->ticketstatus_title . ': ' . $count,
            ];

            // Add to stats array - use JS-safe title with count included (escape any special characters)
            $safe_title = str_replace("'", "\\'", $status->ticketstatus_title . ': ' . $count);

            $payload['stats'][] = [
                $safe_title, $count,
            ];

            // Add to counter
            $counter += $count;

            $payload['tickets_key_colors'][] = runtimeColorCode($status->ticketstatus_color);
        }

        //sum all tickets
        $payload['count_all_tickets'] = \App\Models\Ticket::where('ticket_created', '>=', $year_start)->where('ticket_created', '<=', $year_end)->count();

        // No tickets in system - display something (No Tickets - 100%) in chart
        if ($counter == 0) {
            $payload['stats'][] = [
                'No Tickets: 0', 1,
            ];
            $payload['tickets_key_colors'][] = "#eff4f5";
            $payload['tickets_chart_center_title'] = __('lang.no_tickets');
        }

        return $payload;
    }

    /**
     * basic page setting for this section of the app
     * @param string $section page section (optional)
     * @param array $data any other data (optional)
     * @return array
     */
    private function pageSettings($section = '', $data = []) {

        $page = [
            'crumbs' => [
                __('lang.home'),
            ],
            'crumbs_special_class' => 'main-pages-crumbs',
            'page' => 'home',
            'meta_title' => __('lang.home'),
            'heading' => __('lang.home'),
            'mainmenu_home' => 'active',
            'add_button_classes' => '',
        ];

        return $page;
    }

}
