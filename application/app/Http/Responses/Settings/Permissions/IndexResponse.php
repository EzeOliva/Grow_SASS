<?php

/**
 * @fileoverview Settings & Permissions Index Response
 * @description Handles the response for the main Settings & Permissions page
 */

namespace App\Http\Responses\Settings\Permissions;

use Illuminate\Contracts\Support\Responsable;

class IndexResponse implements Responsable
{
    protected $payload;

    public function __construct($payload = [])
    {
        $this->payload = $payload;
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request)
    {
        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        $html = view('pages.settings.sections.permissions.index', compact(
            'page',
            'roles',
            'modules'
        ))->render();

        $jsondata['dom_html'][] = array(
            'selector' => "#settings-wrapper",
            'action' => 'replace',
            'value' => $html);

        //left menu activate
        if (request('url_type') == 'dynamic') {
            $jsondata['dom_attributes'][] = [
                'selector' => '#settings-menu-permissions',
                'attr' => 'aria-expanded',
                'value' => false,
            ];
            $jsondata['dom_action'][] = [
                'selector' => '#settings-menu-permissions',
                'action' => 'trigger',
                'value' => 'click',
            ];
            $jsondata['dom_classes'][] = [
                'selector' => '#settings-menu-permissions',
                'action' => 'add',
                'value' => 'active',
            ];
        }

        //ajax response
        return response()->json($jsondata);
    }
}
