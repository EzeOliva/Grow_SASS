<?php

/** --------------------------------------------------------------------------------
 * Response for clients stage health report
 *
 * @package    Grow CRM
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Reports\Clients;

use Illuminate\Contracts\Support\Responsable;

class StageHealthResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    public function toResponse($request) {

        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        if (request('action') == 'load') {
            $html = view('pages/reports/clients/health/table', compact('stage_groups', 'selected_period'))->render();
            $jsondata['dom_html'][] = [
                'selector' => '#report-results-container',
                'action' => 'replace',
                'value' => $html,
            ];

            $jsondata['skip_dom_reset'] = true;
        } else {
            $html = view('pages/reports/clients/health/wrapper', compact('stage_groups', 'stage_options', 'selected_period', 'selected_stage'))->render();
            $jsondata['dom_html'][] = [
                'selector' => '#embed-content-container',
                'action' => 'replace',
                'value' => $html,
            ];
        }

        $jsondata['dom_classes'][] = [
            'selector' => '.reports-breadcrumbs',
            'action' => 'remove',
            'value' => 'active',
        ];
        $jsondata['dom_classes'][] = [
            'selector' => '.reports-breadcrumbs',
            'action' => 'remove',
            'value' => 'hidden',
        ];
        $jsondata['dom_html'][] = [
            'selector' => '#reports-breadcrumbs-heading',
            'action' => 'replace',
            'value' => $page['breadcrumbs-heading'],
        ];
        $jsondata['dom_html'][] = [
            'selector' => '#reports-breadcrumbs-sub-heading',
            'action' => 'replace',
            'value' => $page['breadcrumbs-sub-heading'],
        ];
        $jsondata['dom_classes'][] = [
            'selector' => '#reports-breadcrumbs-sub-heading',
            'action' => 'add',
            'value' => 'active',
        ];

        $html = view('pages/reports/clients/health/actions')->render();
        $jsondata['dom_html'][] = [
            'selector' => '#list-page-actions',
            'action' => 'replace',
            'value' => $html,
        ];

        $jsondata['dom_classes'][] = [
            'selector' => '#reports_tabs_clients',
            'action' => 'add',
            'value' => 'active',
        ];

        return response()->json($jsondata);
    }
}
