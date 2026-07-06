<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for timeline
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\Timeline\IndexResponse;
use App\Models\Comment;
use App\Models\Event;
use App\Models\Note;
use App\Permissions\ProjectPermissions;
use App\Repositories\ClientRepository;
use App\Repositories\EventRepository;
use App\Repositories\ProjectRepository;

class Timeline extends Controller {

    /**
     * The events repository instance.
     */
    protected $eventrepo;

    /**
     * The project permission instance.
     */
    protected $projectpermissions;

    /**
     * The client permission instance.
     */
    protected $clientrepo;

    /**
     * The project repository instance.
     */
    protected $projectrepo;

    public function __construct(
        EventRepository $eventrepo,
        ProjectPermissions $projectpermissions,
        ClientRepository $clientrepo,
        ProjectRepository $projectrepo
    ) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        $this->eventrepo = $eventrepo;
        $this->projectpermissions = $projectpermissions;
        $this->clientrepo = $clientrepo;
        $this->projectrepo = $projectrepo;

    }

    /**
     * show a list of timeline events
     * @return \Illuminate\Http\Response
     */
    public function projectTimeline() {

        //basic settings
        $page = [];

        //filter
        request()->merge([
            'eventresource_type' => request('timelineresource_type'),
            'eventresource_id' => request('timelineresource_id'),
        ]);

        //get events
        $events = $this->eventrepo->search([
            'pagination' => config('settings.limits.pagination_project_timeline'),
            'filter' => 'timeline_visible',
        ]);

        //process events
        $events = $this->processEvents($events);

        //reponse payload
        $payload = [
            'page' => $this->pageSettings(),
            'events' => $events,
            'count' => $events->total(),
            'replace_actions_nav' => '',
        ];

        //response
        return new IndexResponse($payload);
    }

    /**
     * show a list of timeline events
     * @return \Illuminate\Http\Response
     */
    public function clientTimeline() {

        //basic settings
        $page = [];

        //keep timeline complete for legacy data that may not have created events
        if (request()->filled('timelineclient_id') && (int) request('page', 1) === 1) {
            $this->backfillClientTimelineEvents((int) request('timelineclient_id'));
        }

        //filter
        request()->merge([
            'eventresource_type' => request('timelineresource_type'),
            'eventresource_id' => request('timelineresource_id'),
        ]);

        //get events
        $events = $this->eventrepo->search([
            'pagination' => config('settings.limits.pagination_project_timeline'),
            'filter' => 'timeline_visible',
        ]);

        //process events
        $events = $this->processEvents($events);

        //reponse payload
        $payload = [
            'page' => $this->pageSettings(),
            'events' => $events,
            'count' => $events->total(),
            'replace_actions_nav' => '',
        ];

        //get clent resource
        if (request('request_source') == 'client' && request()->filled('timelineclient_id')) {

            //get the client (full format)
            $clients = $this->clientrepo->search(request('timelineclient_id'));
            if ($client = $clients->first()) {
                $payload['replace_actions_nav'] = 'client';
                $payload['client'] = $client;
            }
        }

        //response
        return new IndexResponse($payload);
    }

    /**
     * Backfill missing timeline events for client-related data.
     *
     * This keeps client chronology consistent with legacy records where
     * events may not have been generated at creation time.
     *
     * @param int $clientId
     * @return void
     */
    private function backfillClientTimelineEvents($clientId = 0) {

        if (!is_numeric($clientId) || (int) $clientId <= 0) {
            return;
        }

        $clients = $this->clientrepo->search((int) $clientId);
        if (!$client = $clients->first()) {
            return;
        }

        //missing project-level comment events
        $projectComments = Comment::query()
            ->leftJoin('projects', 'projects.project_id', '=', 'comments.commentresource_id')
            ->leftJoin('events as existing_events', function ($join) use ($clientId) {
                $join->on('existing_events.event_item_id', '=', 'comments.comment_id')
                    ->where('existing_events.event_item', '=', 'comment')
                    ->where('existing_events.event_clientid', '=', (int) $clientId);
            })
            ->where('comments.commentresource_type', 'project')
            ->where('projects.project_clientid', (int) $clientId)
            ->whereNull('existing_events.event_id')
            ->select([
                'comments.comment_id',
                'comments.comment_text',
                'comments.comment_creatorid',
                'comments.comment_created',
                'projects.project_id',
                'projects.project_title',
                'projects.project_clientid',
            ])
            ->get();

        foreach ($projectComments as $comment) {
            $eventId = $this->eventrepo->create([
                'event_creatorid' => (int) ($comment->comment_creatorid ?? 0),
                'event_clientid' => (int) ($comment->project_clientid ?? $clientId),
                'event_item' => 'comment',
                'event_item_id' => (int) $comment->comment_id,
                'event_item_lang' => 'event_posted_a_comment',
                'event_item_content' => $comment->comment_text ?? '',
                'event_parent_type' => 'project',
                'event_parent_id' => (int) ($comment->project_id ?? 0),
                'event_parent_title' => $comment->project_title ?? '',
                'event_show_item' => 'yes',
                'event_show_in_timeline' => 'yes',
                'eventresource_type' => 'project',
                'eventresource_id' => (int) ($comment->project_id ?? 0),
                'event_notification_category' => 'notifications_projects_activity',
            ]);

            if ($eventId && !empty($comment->comment_created)) {
                Event::where('event_id', $eventId)->update([
                    'event_created' => $comment->comment_created,
                    'event_updated' => $comment->comment_created,
                ]);
            }
        }

        //missing client note events
        $clientNotes = Note::query()
            ->leftJoin('events as existing_events', function ($join) use ($clientId) {
                $join->on('existing_events.event_item_id', '=', 'notes.note_id')
                    ->where('existing_events.event_item', '=', 'note')
                    ->where('existing_events.event_clientid', '=', (int) $clientId);
            })
            ->where('notes.noteresource_type', 'client')
            ->where('notes.noteresource_id', (int) $clientId)
            ->whereNull('existing_events.event_id')
            ->select([
                'notes.note_id',
                'notes.note_title',
                'notes.note_description',
                'notes.note_creatorid',
                'notes.note_created',
            ])
            ->get();

        foreach ($clientNotes as $note) {
            $eventId = $this->eventrepo->create([
                'event_creatorid' => (int) ($note->note_creatorid ?? 0),
                'event_clientid' => (int) $clientId,
                'event_item' => 'note',
                'event_item_id' => (int) $note->note_id,
                'event_item_lang' => 'Se registró una nota del cliente',
                'event_item_content' => $note->note_title ?? '',
                'event_item_content2' => $note->note_description ?? '',
                'event_parent_type' => 'client',
                'event_parent_id' => (int) $clientId,
                'event_parent_title' => $client->client_company_name ?? '',
                'event_show_item' => 'yes',
                'event_show_in_timeline' => 'yes',
                'eventresource_type' => 'client',
                'eventresource_id' => (int) $clientId,
                'event_notification_category' => '',
            ]);

            if ($eventId && !empty($note->note_created)) {
                Event::where('event_id', $eventId)->update([
                    'event_created' => $note->note_created,
                    'event_updated' => $note->note_created,
                ]);
            }
        }
    }

    /**
     * [sanity]
     *  Additional processing of the time line as follows:
     *       - Do not show clients [task] related events, if the project settings do now allos
     *
     * @return object
     */
    private function processEvents($events = '') {

        if ($events instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            foreach ($events as $key => $event) {

                //hide [task events] for clients as per project settings
                if ($event->event_parent_type == 'task' && auth()->user()->is_client) {
                    if (!$this->projectpermissions->check('tasks-view', $event->eventresource_id)) {
                        $events->forget($key);
                    }
                }

                //hide event as per user role [tickets]
                if ($event->event_item == 'ticket' && auth()->user()->role->role_tickets == 0) {
                    $events->forget($key);
                }

                //hide event as per user role [invoices]
                if ($event->event_item == 'invoice' && auth()->user()->role->role_invoices == 0) {
                    $events->forget($key);
                }

                //hide event as per user role [payments]
                if ($event->event_item == 'payment' && auth()->user()->role->role_invoices == 0) {
                    $events->forget($key);
                }

            }
            return $events;
        }
    }

    /**
     * basic page setting for this section of the app
     * @param string $section page section (optional)
     * @param array $data any other data (optional)
     * @return array
     */
    private function pageSettings($section = '', $data = []) {

        //common settings
        $page = [
        ];

        //return
        return $page;
    }
}
