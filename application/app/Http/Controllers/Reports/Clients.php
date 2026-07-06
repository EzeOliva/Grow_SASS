<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Http\Responses\Reports\Clients\OverviewResponse;
use App\Http\Responses\Reports\Clients\StageHealthResponse;
use App\Repositories\ClientAIRepository;
use App\Repositories\ClientRepository;
use App\Repositories\Reports\ClientReportRepository;
use DB;
use Illuminate\Support\Facades\Schema;

class Clients extends Controller {

    /**
     * The reportrepo repository instance.
     */
    protected $reportrepo;

    /**
     * Client AI repository instance.
     */
    protected $clientairepo;

    /**
     * Client repository instance.
     */
    protected $clientrepo;

    public function __construct(ClientReportRepository $reportrepo, ClientAIRepository $clientairepo, ClientRepository $clientrepo) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        //route middleware
        $this->middleware('reportsMiddlewareShow')->only([
            'overview',
            'stageHealth',
        ]);

        $this->reportrepo = $reportrepo;
        $this->clientairepo = $clientairepo;
        $this->clientrepo = $clientrepo;
    }

    /**
     * grouped by client
     * @return \Illuminate\Http\Response
     */
    public function overview() {

        //default search values
        if (!request()->isMethod('post')) {
            request()->merge([
                'page_limit' => 25,
            ]);
        }

        //search
        $clients = $this->reportrepo->getOverview();

        //get totals
        $totals = [
            'count_projects' => $this->reportrepo->getOverview(null, ['totals' => 'count_projects']),
            'count_projects_pending' => $this->reportrepo->getOverview(null, ['totals' => 'count_projects_pending']),
            'count_projects_completed' => $this->reportrepo->getOverview(null, ['totals' => 'count_projects_completed']),
            'sum_invoices_due' => $this->reportrepo->getOverview(null, ['totals' => 'sum_invoices_due']),
            'sum_invoices_paid' => $this->reportrepo->getOverview(null, ['totals' => 'sum_invoices_paid']),
            'sum_invoices_overdue' => $this->reportrepo->getOverview(null, ['totals' => 'sum_invoices_overdue']),
            'sum_estimates_accepted' => $this->reportrepo->getOverview(null, ['totals' => 'sum_estimates_accepted']),
            'sum_estimates_declined' => $this->reportrepo->getOverview(null, ['totals' => 'sum_estimates_declined']),
            'sum_expenses' => $this->reportrepo->getOverview(null, ['totals' => 'sum_expenses']),
            'sum_expenses_invoiced' => $this->reportrepo->getOverview(null, ['totals' => 'sum_expenses_invoiced']),
            'sum_expenses_not_invoiced' => $this->reportrepo->getOverview(null, ['totals' => 'sum_expenses_not_invoiced']),
            'sum_expenses_not_billable' => $this->reportrepo->getOverview(null, ['totals' => 'sum_expenses_not_billable']),
        ];

        //reponse payload
        $payload = [
            'clients' => $clients,
            'page' => $this->pageSettings('overview'),
            'totals' => $totals,
        ];

        //process reponse
        return new OverviewResponse($payload);

    }

    /**
     * Clients health report grouped by stages.
     *
     * @return \Illuminate\Http\Response
     */
    public function stageHealth() {

        if (!request()->isMethod('post')) {
            request()->merge([
                'health_period' => 'quarter',
                'filter_client_stage_id' => 'all',
            ]);
        }

        $period = request('health_period', 'quarter');
        if (!in_array($period, ['month', 'quarter'])) {
            $period = 'quarter';
        }

        $stageOptions = collect();
        if (Schema::hasTable('client_stages')) {
            $stageOptions = DB::table('client_stages')
                ->select('client_stage_id', 'client_stage_title', 'client_stage_description', 'client_stage_position')
                ->orderBy('client_stage_position', 'asc')
                ->orderBy('client_stage_title', 'asc')
                ->get();
        }

        $stageOrderLookup = [];
        foreach ($stageOptions as $index => $stageOption) {
            $stageOrderLookup[(int) $stageOption->client_stage_id] = $index + 1;
        }

        $clientsQuery = DB::table('clients')
            ->select('clients.client_id', 'clients.client_company_name', 'clients.client_status');

        if (Schema::hasTable('client_stages') && Schema::hasColumn('clients', 'client_stage_id')) {
            $clientsQuery->leftJoin('client_stages', 'client_stages.client_stage_id', '=', 'clients.client_stage_id')
                ->addSelect('client_stages.client_stage_id', 'client_stages.client_stage_title', 'client_stages.client_stage_description', 'client_stages.client_stage_position');
        } else {
            $clientsQuery->addSelect(DB::raw('NULL as client_stage_id'))
                ->addSelect(DB::raw("'Sin etapa' as client_stage_title"))
                ->addSelect(DB::raw("'' as client_stage_description"))
                ->addSelect(DB::raw('999999 as client_stage_position'));
        }

        if (request()->filled('filter_client_stage_id') && request('filter_client_stage_id') !== 'all') {
            $clientsQuery->where('clients.client_stage_id', (int) request('filter_client_stage_id'));
        }

        $clients = $clientsQuery
            ->where('clients.client_id', '>', 0)
            ->orderBy('client_stage_title', 'asc')
            ->orderBy('clients.client_company_name', 'asc')
            ->get();

        $stageGroups = [];

        foreach ($clients as $client) {
            $healthData = $this->clientairepo->getClientHealthReportData((int) $client->client_id, $period);
            $stats = $this->clientrepo->getCustomerSuccessStats((int) $client->client_id);

            $hitos = [];
            if ((int) ($healthData['tasks_completed'] ?? 0) > 0) {
                $hitos[] = 'Tareas completadas: ' . (int) ($healthData['tasks_completed'] ?? 0);
            }
            if ((int) ($healthData['expectations_fulfilled'] ?? 0) > 0) {
                $hitos[] = 'Expectativas cumplidas: ' . (int) ($healthData['expectations_fulfilled'] ?? 0);
            }
            if ((int) ($healthData['minutas_count'] ?? 0) > 0) {
                $hitos[] = 'Minutas registradas: ' . (int) ($healthData['minutas_count'] ?? 0);
            }
            if ((int) ($healthData['capacitaciones_count'] ?? 0) > 0) {
                $hitos[] = 'Capacitaciones realizadas: ' . (int) ($healthData['capacitaciones_count'] ?? 0);
            }
            if ((int) ($healthData['feedback_count'] ?? 0) > 0) {
                $hitos[] = 'Feedbacks recibidos: ' . (int) ($healthData['feedback_count'] ?? 0);
            }

            if (empty($hitos)) {
                $hitos[] = 'Sin hitos relevantes en el período.';
            }

            $hitos = array_slice($hitos, 0, 3);

            $analysis = $this->buildStageAwareBrief(
                $healthData,
                (string) ($client->client_stage_description ?? '')
            );

            $stageTitle = trim((string) ($client->client_stage_title ?? 'Sin etapa'));
            if ($stageTitle === '') {
                $stageTitle = 'Sin etapa';
            }

            $stageKey = strtolower($stageTitle);

            if (!isset($stageGroups[$stageKey])) {
                $stageId = (int) ($client->client_stage_id ?? 0);
                $stageOrder = $stageOrderLookup[$stageId] ?? 999999;

                $stageGroups[$stageKey] = [
                    'stage_title' => $stageTitle,
                    'stage_description' => (string) ($client->client_stage_description ?? ''),
                    'stage_order' => $stageOrder,
                    'clients' => [],
                ];
            }

            $stageGroups[$stageKey]['clients'][] = [
                'client_id' => (int) $client->client_id,
                'client_name' => (string) $client->client_company_name,
                'health_status' => (string) ($stats['health_status'] ?? 'red'),
                'tasks_pending' => (int) ($healthData['tasks_pending'] ?? 0),
                'hitos' => $hitos,
                'brief' => $analysis,
                'feedback_average' => $stats['average_feedback'] ?? 0,
                'expectation_percent' => $stats['expectation_percent'] ?? 0,
            ];
        }

        uasort($stageGroups, function ($a, $b) {
            $orderA = (int) ($a['stage_order'] ?? 999999);
            $orderB = (int) ($b['stage_order'] ?? 999999);

            if ($orderA === $orderB) {
                return strcasecmp((string) ($a['stage_title'] ?? ''), (string) ($b['stage_title'] ?? ''));
            }

            return $orderA <=> $orderB;
        });

        $payload = [
            'stage_groups' => $stageGroups,
            'stage_options' => $stageOptions,
            'selected_period' => $period,
            'selected_stage' => request('filter_client_stage_id', 'all'),
            'page' => $this->pageSettings('stage_health'),
        ];

        return new StageHealthResponse($payload);
    }

    /**
     * grouped by client
     * @return \Illuminate\Http\Response
     */
    public function category() {

        //default search values
        if (!request()->isMethod('post')) {
            request()->merge([
                'page_limit' => 25,
            ]);
        }

        //search
        $projects = $this->reportrepo->getCategory('projects');

        //get totals
        $totals =   $this->reportrepo->getCategory('totals');


        //reponse payload
        $payload = [
            'projects' => $projects,
            'page' => $this->pageSettings('category'),
            'totals' => $totals,
        ];

        //process reponse
        return new CategoryResponse($payload);

    }

    /**
     * basic page setting for this section of the app
     * @param string $section page section (optional)
     * @param array $data any other data (optional)
     * @return array
     */
    private function pageSettings($section = '', $data = []) {

        $page = [];

        //overview
        if ($section == 'overview') {
            $page += [
                'breadcrumbs-heading' => __('lang.clients'),
                'breadcrumbs-sub-heading' => __('lang.overview'),
            ];
        }

        if ($section == 'stage_health') {
            $page += [
                'breadcrumbs-heading' => __('lang.clients'),
                'breadcrumbs-sub-heading' => 'Salud por etapas',
            ];
        }

        //return
        return $page;
    }

    /**
     * Build a short stage-aware summary for a client.
     *
     * @param array $healthData
     * @param string $stageDescription
     * @return string
     */
    private function buildStageAwareBrief($healthData = [], $stageDescription = '') {

        $pending = (int) ($healthData['tasks_pending'] ?? 0);
        $completed = (int) ($healthData['tasks_completed'] ?? 0);
        $notes = (int) count($healthData['notes'] ?? []);
        $minutas = (int) ($healthData['minutas_count'] ?? 0);
        $feedbacks = (int) ($healthData['feedback_count'] ?? 0);

        $parts = [];

        if ($completed > 0) {
            $parts[] = 'Mostró avance operativo en el período.';
        }

        if ($pending > $completed) {
            $parts[] = 'Tiene carga pendiente alta y requiere seguimiento cercano.';
        }

        if ($notes === 0 && $minutas === 0) {
            $parts[] = 'Falta evidencia documental (notas/minutas) para validar acompañamiento.';
        }

        if ($feedbacks === 0) {
            $parts[] = 'No hay feedback reciente para confirmar nivel de adopción/satisfacción.';
        }

        if (empty($parts)) {
            $parts[] = 'Estado estable, sin bloqueos críticos visibles en el período.';
        }

        $brief = implode(' ', array_slice($parts, 0, 2));

        if (!empty(trim($stageDescription))) {
            $brief .= ' Foco de etapa: ' . mb_strimwidth(trim($stageDescription), 0, 140, '...');
        }

        return $brief;
    }
}