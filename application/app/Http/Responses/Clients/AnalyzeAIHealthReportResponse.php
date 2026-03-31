<?php

namespace App\Http\Responses\Clients;

use Illuminate\Contracts\Support\Responsable;

class AnalyzeAIHealthReportResponse implements Responsable
{
    private $payload;

    public function __construct($payload = array())
    {
        $this->payload = $payload;
    }

    public function toResponse($request)
    {
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        $html = view('pages.clients.components.table.modals.tabs.health_report_analysis', compact('client', 'healthData'))->render();

        return response()->json([
            'dom_html' => [
                [
                    'selector' => '#analysis-content',
                    'action' => 'replace',
                    'value' => $html,
                ],
            ],
        ]);
    }
}
