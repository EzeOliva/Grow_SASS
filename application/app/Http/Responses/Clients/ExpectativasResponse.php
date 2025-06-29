<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [details] process for the clients
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Clients;
use Illuminate\Contracts\Support\Responsable;

class ExpectativasResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for team members
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        //render the form
        $html = view('pages/client/components/tabs/expectativas', compact('page', 'client', 'tags'))->render();
        $jsondata['dom_html'][] = [
            'selector' => '#embed-content-container',
            'action' => 'replace',
            'value' => $html,
        ];

        //for embed -change active tabs menu
        $jsondata['dom_classes'][] = [
            'selector' => '.tabs-menu-item',
            'action' => 'remove',
            'value' => 'active',
        ];

        $jsondata['dom_classes'][] = [
            'selector' => '#tabs-menu-expectativas',
            'action' => 'add',
            'value' => 'active',
        ];

        //change actions right panel
        $html = view('pages/client/components/misc/actions', compact('page', 'client'))->render();
        $jsondata['dom_html'][] = [
            'selector' => '#list-page-actions-container',
            'action' => 'replace-with',
            'value' => $html,
        ];

        // POSTRUN FUNCTIONS------
        $jsondata['postrun_functions'][] = [
            'value' => 'NXClientDetails',
        ];
        $jsondata['postrun_functions'][] = [
            'value' => 'NXExpectationList',
        ];
        $jsondata['postrun_functions'][] = [
            'value' => 'NXAddEditExpections',
        ];

        //ajax response
        return response()->json($jsondata);
    }

}
