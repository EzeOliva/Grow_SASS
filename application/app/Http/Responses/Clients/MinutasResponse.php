<?php

namespace App\Http\Responses\Clients;

use Illuminate\Contracts\Support\Responsable;

class MinutasResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    public function toResponse($request) {

        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        $html = view('pages/client/components/tabs/minutas', compact('page', 'client', 'tags', 'minutas'))->render();
        $jsondata['dom_html'][] = [
            'selector' => '#embed-content-container',
            'action' => 'replace',
            'value' => $html,
        ];

        $jsondata['dom_classes'][] = [
            'selector' => '.tabs-menu-item',
            'action' => 'remove',
            'value' => 'active',
        ];

        $jsondata['dom_classes'][] = [
            'selector' => '#tabs-menu-minutas',
            'action' => 'add',
            'value' => 'active',
        ];

        $html = view('pages/client/components/misc/actions', compact('page', 'client'))->render();
        $jsondata['dom_html'][] = [
            'selector' => '#list-page-actions-container',
            'action' => 'replace-with',
            'value' => $html,
        ];

        if (config('visibility.edit_client_button')) {
            $jsondata['postrun_functions'][] = [
                'value' => 'NXClientDetails',
            ];
        }

        return response()->json($jsondata);
    }
}
