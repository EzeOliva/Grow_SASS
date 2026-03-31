<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for clients settings
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Settings;
use App\Http\Controllers\Controller;
use App\Models\ClientStage;
use App\Http\Responses\Settings\Clients\IndexResponse;
use App\Http\Responses\Settings\Clients\UpdateResponse;
use App\Repositories\SettingsRepository;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;

class Clients extends Controller {

        /**
     * The settings repository instance.
     */
    protected $settingsrepo;

    public function __construct(SettingsRepository $settingsrepo) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        //settings general
        $this->middleware('settingsMiddlewareIndex');

        $this->settingsrepo = $settingsrepo;

    }

    /**
     * Display general settings
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        //crumbs, page data & stats
        $page = $this->pageSettings();

        $settings = \App\Models\Settings::find(1);
        $settings2 = \App\Models\Settings2::find(1);
        $stages = collect([]);

        if (Schema::hasTable('client_stages')) {
            $stages = ClientStage::orderBy('client_stage_position', 'asc')
                ->orderBy('client_stage_id', 'asc')
                ->get();
        }

        //reponse payload
        $payload = [
            'page' => $page,
            'settings' => $settings,
            'settings2' => $settings2,
            'stages' => $stages,
        ];

        //show the view
        return new IndexResponse($payload);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update() {

        $settings = \App\Models\Settings::find(1);
        $settings2 = \App\Models\Settings2::find(1);

        //custom error messages
        $messages = [];

        //update
        if (!$this->settingsrepo->updateClients()) {
            abort(409);
        }

        //update other settings
        $settings2->settings2_importing_clients_duplicates_email = request('settings2_importing_clients_duplicates_email') == 'on' ? 'yes' : 'no';
        $settings2->settings2_importing_clients_duplicates_telephone = request('settings2_importing_clients_duplicates_telephone') == 'on' ? 'yes' : 'no';
        $settings2->settings2_importing_clients_duplicates_company = request('settings2_importing_clients_duplicates_company') == 'on' ? 'yes' : 'no';
        $settings2->save();


        //reponse payload
        $payload = [];

        //generate a response
        return new UpdateResponse($payload);
    }

    /**
     * Store a pipeline stage for clients
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeStage() {

        if (!Schema::hasTable('client_stages')) {
            abort(409, 'No existe la tabla client_stages. Ejecuta el script SQL de etapas.');
        }
        if (!Schema::hasColumn('client_stages', 'client_stage_description')) {
            abort(409, 'Falta la columna de descripción de etapas. Ejecuta el script SQL de actualización.');
        }

        $validator = Validator::make(request()->all(), [
            'client_stage_title' => 'required|string|max:190',
            'client_stage_description' => 'required|string|max:5000',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }
            abort(409, $messages);
        }

        $exists = ClientStage::whereRaw('LOWER(client_stage_title) = ?', [strtolower(trim((string) request('client_stage_title')))])
            ->exists();

        if ($exists) {
            abort(409, 'La etapa ya existe');
        }

        $last = ClientStage::orderBy('client_stage_position', 'desc')->first();
        $position = $last ? ((int) $last->client_stage_position + 1) : 1;

        $stage = new ClientStage();
        $stage->client_stage_title = trim((string) request('client_stage_title'));
        $stage->client_stage_description = trim((string) request('client_stage_description'));
        $stage->client_stage_position = (int) request('client_stage_position', $position);
        $stage->client_stage_active = request('client_stage_active') == 'on' ? 'yes' : 'no';
        $stage->save();

        return response()->json([
            'success' => true,
            'message' => __('lang.request_has_been_completed'),
        ]);
    }

    /**
     * Update a pipeline stage for clients
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStage($id) {

        if (!Schema::hasTable('client_stages')) {
            abort(409, 'No existe la tabla client_stages. Ejecuta el script SQL de etapas.');
        }
        if (!Schema::hasColumn('client_stages', 'client_stage_description')) {
            abort(409, 'Falta la columna de descripción de etapas. Ejecuta el script SQL de actualización.');
        }

        $validator = Validator::make(request()->all(), [
            'client_stage_title' => 'required|string|max:190',
            'client_stage_description' => 'required|string|max:5000',
            'client_stage_position' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }
            abort(409, $messages);
        }

        $stage = ClientStage::find($id);
        if (!$stage) {
            abort(404);
        }

        $exists = ClientStage::whereRaw('LOWER(client_stage_title) = ?', [strtolower(trim((string) request('client_stage_title')))])
            ->where('client_stage_id', '!=', $id)
            ->exists();

        if ($exists) {
            abort(409, 'La etapa ya existe');
        }

        $stage->client_stage_title = trim((string) request('client_stage_title'));
        $stage->client_stage_description = trim((string) request('client_stage_description'));
        $stage->client_stage_position = (int) request('client_stage_position');
        $stage->client_stage_active = request('client_stage_active') == 'on' ? 'yes' : 'no';
        $stage->save();

        return response()->json([
            'success' => true,
            'message' => __('lang.request_has_been_completed'),
        ]);
    }

    /**
     * Delete a pipeline stage for clients
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyStage($id) {

        if (!Schema::hasTable('client_stages')) {
            abort(409, 'No existe la tabla client_stages. Ejecuta el script SQL de etapas.');
        }

        $stage = ClientStage::find($id);
        if (!$stage) {
            abort(404);
        }

        if (Schema::hasColumn('clients', 'client_stage_id')) {
            $inUse = \App\Models\Client::where('client_stage_id', $id)->count();
            if ($inUse > 0) {
                abort(409, 'No se puede eliminar: la etapa está asignada a clientes.');
            }
        }

        $stage->delete();

        return response()->json([
            'success' => true,
            'message' => __('lang.request_has_been_completed'),
        ]);
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
                __('lang.settings'),
                __('lang.clients'),
            ],
            'crumbs_special_class' => 'main-pages-crumbs',
            'page' => 'settings',
            'meta_title' =>  __('lang.settings'),
            'heading' =>  __('lang.clients'),
            'settingsmenu_feedback' => 'active',
        ];
        return $page;
    }

}
