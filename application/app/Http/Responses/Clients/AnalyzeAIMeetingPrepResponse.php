<?php

namespace App\Http\Responses\Clients;

use Illuminate\Contracts\Support\Responsable;

class AnalyzeAIMeetingPrepResponse implements Responsable
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

        $html = view('pages.clients.components.table.modals.tabs.meeting_prep_analysis', compact('client', 'meetingData'))->render();

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
