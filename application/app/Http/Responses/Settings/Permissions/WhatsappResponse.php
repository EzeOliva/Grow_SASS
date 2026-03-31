<?php

/**
 * @fileoverview Settings & Permissions WhatsApp Response
 * @description Handles the response for the WhatsApp Permissions page
 */

namespace App\Http\Responses\Settings\Permissions;

use Illuminate\Contracts\Support\Responsable;

class WhatsappResponse implements Responsable
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

        $html = view('pages.settings.sections.permissions.whatsapp', compact(
            'page',
            'roles',
            'whatsappPermissions'
        ))->render();

        $jsondata['dom_html'][] = array(
            'selector' => "#settings-wrapper",
            'action' => 'replace',
            'value' => $html);

        //left menu activate
        if (request('url_type') == 'dynamic') {
            $jsondata['dom_classes'][] = [
                'selector' => '#settings-menu-permissions-whatsapp',
                'action' => 'add',
                'value' => 'active',
            ];
        }

        //ajax response
        return response()->json($jsondata);
    }
}
