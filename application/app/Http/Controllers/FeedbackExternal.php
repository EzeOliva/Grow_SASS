<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Feedback;
use App\Models\FeedbackDetail;
use App\Models\FeedbackQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FeedbackExternal extends Controller
{
    public function __construct()
    {
        parent::__construct();
        // Only generateToken requires authentication
        $this->middleware('auth')->only('generateToken');
    }

    /**
     * Show the public feedback form (no auth required)
     */
    public function show($token)
    {
        $client = Client::where('client_feedback_token', $token)->first();

        if (!$client) {
            abort(404);
        }

        $feedbackQueries = FeedbackQuery::orderBy('feedback_query_id')->get();

        // Get impact summary for last 90 days
        $feedbackImpact = [
            'tasks_completed' => 0,
            'capacitaciones_count' => 0,
            'expectations_fulfilled' => 0,
            'minutas_count' => 0,
        ];
        try {
            $aiRepo = app(\App\Repositories\ClientAIRepository::class);
            $healthData = $aiRepo->getClientHealthReportData($client->client_id, 'quarter');
            $feedbackImpact = [
                'tasks_completed' => (int) ($healthData['tasks_completed'] ?? 0),
                'capacitaciones_count' => (int) ($healthData['capacitaciones_count'] ?? 0),
                'expectations_fulfilled' => (int) ($healthData['expectations_fulfilled'] ?? 0),
                'minutas_count' => (int) ($healthData['minutas_count'] ?? 0),
            ];
        } catch (\Throwable $e) {
            // Keep defaults
        }

        return view('pages.feedback.external', [
            'client' => $client,
            'token' => $token,
            'feedbackQueries' => $feedbackQueries,
            'feedbackImpact' => $feedbackImpact,
        ]);
    }

    /**
     * Store feedback from public form (no auth required)
     */
    public function store(Request $request, $token)
    {
        $client = Client::where('client_feedback_token', $token)->first();

        if (!$client) {
            abort(404);
        }

        // Create the feedback record
        $feedback = Feedback::create([
            'client_id' => $client->client_id,
            'feedback_date' => now()->format('Y-m-d H:i:s'),
            'comment' => $request->get('comment', ''),
            'feedback_created' => now()->format('Y-m-d H:i:s'),
        ]);

        if ($feedback) {
            // Save each query answer
            $feedbackQueries = FeedbackQuery::all();
            foreach ($feedbackQueries as $query) {
                FeedbackDetail::create([
                    'feedback_id' => $feedback->feedback_id,
                    'feedback_query_id' => $query->feedback_query_id,
                    'value' => (int) $request->get('q_' . $query->feedback_query_id, 0),
                    'feedback_detail_created' => now()->format('Y-m-d H:i:s'),
                ]);
            }

            // Invalidate cache
            \Illuminate\Support\Facades\Cache::forget('feedback_needed_' . $client->client_id);
        }

        return view('pages.feedback.external-thanks', [
            'client' => $client,
        ]);
    }

    /**
     * Generate or retrieve feedback token for a client (admin endpoint)
     */
    public function generateToken(Request $request, $clientId)
    {
        $client = Client::findOrFail($clientId);

        if (empty($client->client_feedback_token)) {
            $client->client_feedback_token = Str::random(48);
            $client->save();
        }

        $url = url('/feedback/external/' . $client->client_feedback_token);

        return response()->json([
            'success' => true,
            'url' => $url,
            'token' => $client->client_feedback_token,
        ]);
    }

    /**
     * AI suggestion for external feedback (no auth, uses token)
     */
    public function suggest(Request $request, $token)
    {
        $client = Client::where('client_feedback_token', $token)->first();
        if (!$client) {
            abort(404);
        }

        $data = $request->validate([
            'details' => 'required|array|min:1',
            'details.*.feedback_query_id' => 'required|integer',
            'details.*.value' => 'required|integer|min:0|max:10',
        ]);

        // Delegate to the existing FeedbackAIController logic
        $aiController = app(\App\Http\Controllers\FeedbackAIController::class);
        return $aiController->suggest($request, $client->client_id);
    }
}
