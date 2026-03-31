<?php
namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use App\Models\Client;

class ClientAIRepository
{
    public function fetchClientAnalysisData($clientId, $topics, $startDate, $endDate)
    {
        $data = [];

        if (in_array('billing', $topics)) {
            $data['billing'] = DB::table('invoices')
                ->select('bill_invoiceid', 'bill_final_amount', 'bill_date', 'bill_due_date', 'bill_status')
                ->where('bill_clientid', $clientId)
                ->whereBetween('bill_date', [$startDate, $endDate])
                ->get();
        }

        if (in_array('projects', $topics)) {
            $data['projects'] = DB::table('projects')
                ->select('project_title', 'project_status', 'project_date_due')
                ->where('project_clientid', $clientId)
                ->whereBetween('project_created', [$startDate, $endDate])
                ->get();
        }

        if (in_array('feedback', $topics)) {
            $data['feedback'] = DB::table('feedbacks')
                ->select('feedback_id', 'feedback_date', 'comment', 'feedback_created')
                ->where('client_id', $clientId)
                ->whereBetween('feedback_created', [$startDate, $endDate])
                ->get();
        }

        return $data;
    }

    public function formatDataForAI($clientId, array $data): string
    {
        $clientName = DB::table('clients')->where('client_id', $clientId)->value('client_company_name');
        $summary = "Analyze this client: {$clientName} (ID: {$clientId})\n\n";

        if (!empty($data['billing'])) {
            $summary .= "Billing:\n";
            foreach ($data['billing'] as $item) {
                $summary .= "- {$item->bill_status} invoice of {$item->bill_final_amount} due on {$item->bill_due_date}\n";
            }
        }

        if (!empty($data['projects'])) {
            $summary .= "\nProjects:\n";
            foreach ($data['projects'] as $p) {
                $summary .= "- {$p->project_title} ({$p->project_status}), deadline: {$p->project_date_due}\n";
            }
        }

        if (!empty($data['tasks'])) {
            $summary .= "\nTasks:\n";
            foreach ($data['tasks'] as $t) {
                $summary .= "- {$t->task_title}, status: {$t->task_status}, due: {$t->task_date_due}\n";
            }
        }

        if (!empty($data['comments'])) {
            $summary .= "\nClient Comments:\n";
            foreach ($data['comments'] as $c) {
                $summary .= "- \"{$c}\"\n";
            }
        }

        if (!empty($data['surveys'])) {
            $summary .= "\nSurvey Responses:\n";
            foreach ($data['surveys'] as $s) {
                $summary .= "- {$s->title}: {$s->value}/{$s->range} (weight {$s->weight})\n";
            }
        }

        if (!empty($data['expectations_summary'])) {
            $s = $data['expectations_summary'];
            $summary .= "\nExpectation Summary:\n";
            $summary .= "- Fulfilled: {$s['fulfilled_percent']}%\n";
            $summary .= "- Overdue expectations: {$s['overdue_count']}\n";
            $summary .= "- Total expectations: {$s['total_count']}\n";
        }

        return $summary;
    }

    /**
     * Get a client with all related data for AI analysis.
     *
     * @param int $clientId
     * @return Client|null
     */
    public function getClientWithRelations($clientId)
    {
        return Client::with([
            'creator',
            'projects.tasks',
            'projects.invoices',
            'projects.estimates',
            'projects.contracts',
            'projects.expenses',
            'projects.payments',
            'projects.milestones',
            'projects.tickets',
            'projects.files',
            'projects.tags',
            'projects.comments',
            'users.role',
            'invoices',
            'estimates',
            'notes',
            'proposals',
            'contracts',
            'expenses',
            'files',
            'payments',
            'tags',
            'tickets',
            'category',
            'feedbacks.feedbackDetails',
            'clientExpectations',
        ])->find($clientId);
    }

    /**
     * Generate a comprehensive prompt for OpenAI based on a client's full profile and activity, with detailed date-based analysis.
     * This enhanced version includes all important relationships and detailed feedback analysis.
     *
     * @param int $clientId
     * @return string
     */
    public function generateComprehensiveClientPrompt($clientId)
    {
        $now = Carbon::now();

        // --- Basic Client Info with Category ---
        $client = DB::table('clients')
            ->leftJoin('categories', 'categories.category_id', '=', 'clients.client_categoryid')
            ->select(
                'clients.*',
                'categories.category_name as category'
            )
            ->where('client_id', $clientId)
            ->first();

        // Always initialize $prompt
        $prompt = [];

        // Guard: If client not found, return a helpful message
        if (!$client) {
            $prompt[] = "No client found with ID: {$clientId}";
            return implode("\n", $prompt);
        }

        // --- Contacts/Users with Role Information ---
        $users = DB::table('users')
            ->leftJoin('roles', 'roles.role_id', '=', 'users.role_id')
            ->select(
                'users.*',
                'roles.role_name'
            )
            ->where('clientid', $clientId)
            ->get();

        // --- Projects with Tasks and Milestones ---
        $projects = DB::table('projects')
            ->leftJoin('categories', 'categories.category_id', '=', 'projects.project_categoryid')
            ->select(
                'projects.*',
                'categories.category_name as project_category'
            )
            ->where('project_clientid', $clientId)
            ->orderByDesc('project_created')
            ->get();
        $lastProject = $projects->first();
        $daysSinceLastProject = $lastProject ? Carbon::parse($lastProject->project_created)->diffInDays($now) : null;

        // --- Project Tasks ---
        $projectTasks = DB::table('tasks')
            ->leftJoin('projects', 'projects.project_id', '=', 'tasks.task_projectid')
            ->leftJoin('tasks_assigned', 'tasks_assigned.tasksassigned_taskid', '=', 'tasks.task_id')
            ->leftJoin('users', 'users.id', '=', 'tasks_assigned.tasksassigned_userid')
            ->select(
                'tasks.*',
                'projects.project_title',
                DB::raw("GROUP_CONCAT(CONCAT(users.first_name, ' ', users.last_name)) as assigned_users")
            )
            ->where('projects.project_clientid', $clientId)
            ->groupBy('tasks.task_id')
            ->orderByDesc('tasks.task_created')
            ->get();

        // --- Invoices with Line Items ---
        $invoices = DB::table('invoices')
            ->leftJoin('categories', 'categories.category_id', '=', 'invoices.bill_categoryid')
            ->select(
                'invoices.*',
                'categories.category_name as invoice_category'
            )
            ->where('bill_clientid', $clientId)
            ->orderByDesc('bill_date')
            ->get();
        $lastInvoice = $invoices->first();
        $daysSinceLastInvoice = $lastInvoice ? Carbon::parse($lastInvoice->bill_date)->diffInDays($now) : null;

        // --- Invoice Line Items ---
        $invoiceItems = DB::table('lineitems')
            ->where('lineitemresource_type', 'invoice')
            ->whereIn('lineitemresource_id', $invoices->pluck('bill_invoiceid'))
            ->get();

        // --- Payments with Gateway Information ---
        $payments = DB::table('payments')
            ->leftJoin('invoices', 'invoices.bill_invoiceid', '=', 'payments.payment_invoiceid')
            ->select(
                'payments.*',
                'invoices.bill_invoiceid',
                'invoices.bill_final_amount'
            )
            ->where('payment_clientid', $clientId)
            ->orderByDesc('payment_date')
            ->get();
        $lastPayment = $payments->first();
        $daysSinceLastPayment = $lastPayment ? Carbon::parse($lastPayment->payment_date)->diffInDays($now) : null;

        // --- Enhanced Feedbacks with Detailed Analysis ---
        $feedbacks = DB::table('feedbacks')
            ->leftJoin('users', 'users.clientid', '=', 'feedbacks.client_id')
            ->select(
                'feedbacks.*',
                'users.first_name',
                'users.last_name',
                'users.email'
            )
            ->where('feedbacks.client_id', $clientId)
            ->orderByDesc('feedback_created')
            ->get();
        $lastFeedback = $feedbacks->first();
        $daysSinceLastFeedback = $lastFeedback ? Carbon::parse($lastFeedback->feedback_created)->diffInDays($now) : null;

        // --- Detailed Feedback Analysis with Query Information ---
        $feedbackDetails = DB::table('feedback_details')
            ->join('feedbacks', 'feedbacks.feedback_id', '=', 'feedback_details.feedback_id')
            ->join('feedback_queries', 'feedback_queries.feedback_query_id', '=', 'feedback_details.feedback_query_id')
            ->leftJoin('users', 'users.clientid', '=', 'feedbacks.client_id')
            ->select(
                'feedback_details.*',
                'feedback_queries.title as query_title',
                'feedback_queries.content as query_content',
                'feedback_queries.type as query_type',
                'feedback_queries.range as query_range',
                'feedback_queries.weight as query_weight',
                'feedbacks.comment as feedback_comment',
                'feedbacks.feedback_date',
                'users.first_name',
                'users.last_name'
            )
            ->where('feedbacks.client_id', $clientId)
            ->orderByDesc('feedbacks.feedback_created')
            ->get();

        // --- Client Expectations ---
        $expectations = DB::table('client_expectations')
            ->where('client_id', $clientId)
            ->orderByDesc('expectation_created')
            ->get();
        $lastExpectation = $expectations->first();
        $daysSinceLastExpectation = $lastExpectation ? Carbon::parse($lastExpectation->expectation_created)->diffInDays($now) : null;

        // --- Support Tickets with Replies ---
        $tickets = DB::table('tickets')
            ->leftJoin('categories', 'categories.category_id', '=', 'tickets.ticket_categoryid')
            ->select(
                'tickets.*',
                'categories.category_name as ticket_category'
            )
            ->where('ticket_clientid', $clientId)
            ->orderByDesc('ticket_created')
            ->get();
        $lastTicket = $tickets->first();
        $daysSinceLastTicket = $lastTicket ? Carbon::parse($lastTicket->ticket_created)->diffInDays($now) : null;

        // --- Ticket Replies ---
        $ticketReplies = DB::table('ticket_replies')
            ->leftJoin('tickets', 'tickets.ticket_id', '=', 'ticket_replies.ticketreply_ticketid')
            ->leftJoin('users', 'users.id', '=', 'ticket_replies.ticketreply_creatorid')
            ->select(
                'ticket_replies.*',
                'tickets.ticket_subject',
                'users.first_name',
                'users.last_name',
                'users.type as user_type'
            )
            ->where('tickets.ticket_clientid', $clientId)
            ->orderByDesc('ticket_replies.ticketreply_created')
            ->get();

        // --- Notes with Creator Information ---
        $notes = DB::table('notes')
            ->leftJoin('users', 'users.id', '=', 'notes.note_creatorid')
            ->select(
                'notes.*',
                'users.first_name as creator_first_name',
                'users.last_name as creator_last_name'
            )
            ->where('noteresource_id', $clientId)
            ->where('noteresource_type', 'client')
            ->orderByDesc('note_created')
            ->get();
        $lastNote = $notes->first();
        $daysSinceLastNote = $lastNote ? Carbon::parse($lastNote->note_created)->diffInDays($now) : null;

        // --- Files ---
        $files = DB::table('files')
            ->leftJoin('users', 'users.id', '=', 'files.file_creatorid')
            ->select(
                'files.*',
                'users.first_name as creator_first_name',
                'users.last_name as creator_last_name'
            )
            ->where('fileresource_id', $clientId)
            ->where('fileresource_type', 'client')
            ->orderByDesc('file_created')
            ->get();

        // --- Tags ---
        $tags = DB::table('tags')
            ->where('tagresource_id', $clientId)
            ->where('tagresource_type', 'client')
            ->get();

        // --- Estimates ---
        $estimates = DB::table('estimates')
            ->leftJoin('categories', 'categories.category_id', '=', 'estimates.bill_categoryid')
            ->select(
                'estimates.*',
                'categories.category_name as estimate_category'
            )
            ->where('bill_clientid', $clientId)
            ->orderByDesc('bill_created')
            ->get();

        // --- Contracts ---
        $contracts = DB::table('contracts')
            ->where('doc_lead_id', $clientId)
            ->orderByDesc('doc_created')
            ->get();

        // --- Proposals ---
        $proposals = DB::table('proposals')
            ->where('doc_lead_id', $clientId)
            ->orderByDesc('doc_created')
            ->get();

        // --- Expenses ---
        $expenses = DB::table('expenses')
            ->leftJoin('categories', 'categories.category_id', '=', 'expenses.expense_categoryid')
            ->select(
                'expenses.*',
                'categories.category_name as expense_category'
            )
            ->where('expense_clientid', $clientId)
            ->orderByDesc('expense_created')
            ->get();

        // --- Days since client joined ---
        $daysSinceJoined = $client && $client->client_created ? Carbon::parse($client->client_created)->diffInDays($now) : null;

        // --- Calculate Financial Summary ---
        $totalInvoiced = $invoices->sum('bill_final_amount');
        $totalPaid = $payments->sum('payment_amount');
        $outstandingBalance = $totalInvoiced - $totalPaid;

        // --- Calculate Feedback Summary ---
        $feedbackSummary = $this->calculateFeedbackSummary($feedbackDetails);

        // --- Calculate Expectations Summary ---
        $expectationsSummary = $this->calculateExpectationsSummary($expectations);

        // --- Summarize Data ---
        $prompt[] = "You are an expert business analyst AI. Here is a comprehensive profile of a client from our CRM system.";
        $prompt[] = "\nClient Profile:";
        $prompt[] = "- Company Name: {$client->client_company_name}";
        $prompt[] = "- Industry: " . ($client->industry ?? 'N/A');
        $prompt[] = "- Category: " . ($client->category ?? 'N/A');
        $prompt[] = "- Status: " . ($client->client_status ?? 'N/A');
        $prompt[] = "- Joined: {$client->client_created} (" . ($daysSinceJoined !== null ? "$daysSinceJoined days ago" : 'N/A') . ")";
        $prompt[] = "- Description: " . ($client->client_description ?? 'N/A');
        $prompt[] = "- Website: " . ($client->client_website ?? 'N/A');
        $prompt[] = "- Phone: " . ($client->client_phone ?? 'N/A');
        $prompt[] = "- VAT: " . ($client->client_vat ?? 'N/A') . "\n";

        $prompt[] = "Contacts (Total: {$users->count()}):";
        foreach ($users as $user) {
            $roleName = $user->role_name ?? 'No Role';
            $prompt[] = "- {$user->first_name} {$user->last_name} ({$user->email}), Role: {$roleName}, Type: {$user->type}";
        }

        $prompt[] = "\nProjects (Total: {$projects->count()}, Last: " . ($lastProject ? $lastProject->project_created . ", $daysSinceLastProject days ago" : 'N/A') . "):";
        foreach ($projects->take(5) as $project) {
            $prompt[] = "- {$project->project_title} (Status: {$project->project_status}, Category: {$project->project_category}, Created: {$project->project_created}, Deadline: {$project->project_date_due})";
        }

        $prompt[] = "\nProject Tasks (Total: {$projectTasks->count()}):";
        foreach ($projectTasks->take(5) as $task) {
            $assignedUser = $task->assigned_users ?? 'Unassigned';
            $prompt[] = "- {$task->task_title} (Project: {$task->project_title}, Status: {$task->task_status}, Assigned: {$assignedUser}, Due: {$task->task_date_due})";
        }

        $prompt[] = "\nFinancial Summary:";
        $prompt[] = "- Total Invoiced: {$totalInvoiced}";
        $prompt[] = "- Total Paid: {$totalPaid}";
        $prompt[] = "- Outstanding Balance: {$outstandingBalance}";

        $prompt[] = "\nInvoices (Total: {$invoices->count()}, Last: " . ($lastInvoice ? $lastInvoice->bill_date . ", $daysSinceLastInvoice days ago" : 'N/A') . "):";
        foreach ($invoices->take(5) as $invoice) {
            $prompt[] = "- Invoice #{$invoice->bill_invoiceid}, Amount: {$invoice->bill_final_amount}, Status: {$invoice->bill_status}, Category: {$invoice->invoice_category}, Date: {$invoice->bill_date}";
        }

        $prompt[] = "\nPayments (Total: {$payments->count()}, Last: " . ($lastPayment ? $lastPayment->payment_date . ", $daysSinceLastPayment days ago" : 'N/A') . "):";
        foreach ($payments->take(5) as $payment) {
            $prompt[] = "- Payment #{$payment->payment_id}, Amount: {$payment->payment_amount}, Gateway: {$payment->payment_gateway}, Date: {$payment->payment_date}";
        }

        $prompt[] = "\nEnhanced Feedback Analysis:";
        $prompt[] = "- Total Feedback Entries: {$feedbacks->count()}";
        $prompt[] = "- Last Feedback: " . ($lastFeedback ? $lastFeedback->feedback_created . ", $daysSinceLastFeedback days ago" : 'N/A');
        $prompt[] = "- Average Overall Score: {$feedbackSummary['average_score']}";
        $prompt[] = "- Feedback Trend: {$feedbackSummary['trend']}";
        $prompt[] = "- Most Recent Feedback: " . ($lastFeedback ? "\"{$lastFeedback->comment}\"" : 'N/A');

        $prompt[] = "\nDetailed Feedback Breakdown (last 10 entries):";
        foreach ($feedbackDetails->take(10) as $fd) {
            $userName = $fd->first_name ? "{$fd->first_name} {$fd->last_name}" : 'Anonymous';
            $prompt[] = "- Query: \"{$fd->query_title}\" - Score: {$fd->value}/{$fd->query_range} (Weight: {$fd->query_weight}) - User: {$userName} - Date: {$fd->feedback_date}";
        }

        // --- New: Explicit AI instructions for perfect feedback analysis ---
        $prompt[] = "\nPlease perform a detailed feedback analysis with the following points:";
        $prompt[] = "1. Summarize the recency and frequency of feedback (e.g., how often feedback is received, any long gaps, last feedback date).";
        $prompt[] = "2. Calculate and comment on the average feedback score and its trend (improving, declining, or stable).";
        $prompt[] = "3. Identify the most common positive and negative themes or keywords from the feedback comments.";
        $prompt[] = "4. Highlight any specific concerns or praise that appear multiple times.";
        $prompt[] = "5. Provide actionable recommendations for the client relationship based on the feedback data.";
        $prompt[] = "6. If there are any red flags or urgent issues, mention them clearly.";
        $prompt[] = "7. Write your analysis in a clear, professional, and actionable style, suitable for a business manager.";

        $prompt[] = "\nClient Expectations (Total: {$expectations->count()}, Last: " . ($lastExpectation ? $lastExpectation->expectation_created . ", $daysSinceLastExpectation days ago" : 'N/A') . "):";
        $prompt[] = "- Fulfilled: {$expectationsSummary['fulfilled_count']} ({$expectationsSummary['fulfilled_percent']}%)";
        $prompt[] = "- Pending: {$expectationsSummary['pending_count']}";
        $prompt[] = "- Overdue: {$expectationsSummary['overdue_count']}";
        foreach ($expectations->take(5) as $exp) {
            $prompt[] = "- \"{$exp->title}\" (Status: {$exp->status}, Due: {$exp->due_date}, Weight: {$exp->weight})";
        }

        $prompt[] = "\nSupport Tickets (Total: {$tickets->count()}, Last: " . ($lastTicket ? $lastTicket->ticket_created . ", $daysSinceLastTicket days ago" : 'N/A') . "):";
        foreach ($tickets->take(5) as $ticket) {
            $prompt[] = "- Ticket #{$ticket->ticket_id}, Subject: {$ticket->ticket_subject}, Status: {$ticket->ticket_status}, Created: {$ticket->ticket_created}";
        }

        $prompt[] = "\nNotes (Total: {$notes->count()}, Last: " . ($lastNote ? $lastNote->note_created . ", $daysSinceLastNote days ago" : 'N/A') . "):";
        foreach ($notes->take(5) as $note) {
            $creator = $note->creator_first_name ? "{$note->creator_first_name} {$note->creator_last_name}" : 'Unknown';
            $prompt[] = "- \"{$note->note_title}\": {$note->note_description} (Created by: {$creator}, Date: {$note->note_created})";
        }

        $prompt[] = "\nFiles (Total: {$files->count()}):";
        foreach ($files->take(5) as $file) {
            $creator = $file->creator_first_name ? "{$file->creator_first_name} {$file->creator_last_name}" : 'Unknown';
            $prompt[] = "- {$file->file_filename} (Type: {$file->file_type}, Size: {$file->file_size}, Created by: {$creator})";
        }

        $prompt[] = "\nTags:";
        if ($tags->count() > 0) {
            foreach ($tags as $tag) {
                $prompt[] = "- {$tag->tag_title}";
            }
        } else {
            $prompt[] = "- No tags assigned";
        }

        $prompt[] = "\nEstimates (Total: {$estimates->count()}):";
        foreach ($estimates->take(3) as $estimate) {
            $prompt[] = "- Estimate #{$estimate->bill_estimateid}, Amount: {$estimate->bill_final_amount}, Status: {$estimate->bill_status}, Category: {$estimate->estimate_category}";
        }

        $prompt[] = "\nContracts (Total: {$contracts->count()}):";
        foreach ($contracts->take(3) as $contract) {
            $prompt[] = "- Contract #{$contract->doc_id}, Title: {$contract->doc_title}, Status: {$contract->doc_status}";
        }

        $prompt[] = "\nProposals (Total: {$proposals->count()}):";
        foreach ($proposals->take(3) as $proposal) {
            $prompt[] = "- Proposal #{$proposal->doc_id}, Title: {$proposal->doc_title}, Status: {$proposal->doc_status}";
        }

        $prompt[] = "\nExpenses (Total: {$expenses->count()}):";
        foreach ($expenses->take(3) as $expense) {
            $prompt[] = "- Expense #{$expense->expense_id}, Amount: {$expense->expense_amount}, Category: {$expense->expense_category}, Description: {$expense->expense_description}";
        }

        $prompt[] = "\nPlease analyze this client and provide:";
        $prompt[] = "1. A comprehensive summary of the client's current status and relationship with us.";
        $prompt[] = "2. Key insights from feedback analysis, including satisfaction trends and specific concerns.";
        $prompt[] = "3. Financial health assessment based on payment history and outstanding balances.";
        $prompt[] = "4. Risk assessment based on expectations, project status, and communication patterns.";
        $prompt[] = "5. Specific recommendations for improving client satisfaction and retention.";
        $prompt[] = "6. Opportunities for upselling or expanding services based on their needs.";
        $prompt[] = "7. Any red flags or areas requiring immediate attention.";

        return implode("\n", $prompt);
    }

    /**
     * Generate a detailed AI prompt for feedback analysis only
     */
    public function generateFeedbackAnalysisPrompt($clientId)
    {
        $now = Carbon::now();
        $feedbacks = DB::table('feedbacks')
            ->where('client_id', $clientId)
            ->orderByDesc('feedback_created')
            ->get();
        $lastFeedback = $feedbacks->first();
        $daysSinceLastFeedback = $lastFeedback ? Carbon::parse($lastFeedback->feedback_created)->diffInDays($now) : null;
        $feedbackCount = $feedbacks->count();
        $comments = $feedbacks->pluck('comment')->toArray();

        $prompt = [];
        $prompt[] = "You are an expert business analyst AI. Here is the feedback history for this client:";
        foreach ($feedbacks as $fb) {
            $prompt[] = "- Date: {$fb->feedback_created}, Comment: \"{$fb->comment}\"";
        }
        $prompt[] = "\nPlease analyze the following in a structured table format:";
        $prompt[] = "| Goal                  | Description                                           |";
        $prompt[] = "| --------------------- | ----------------------------------------------------- |";
        $prompt[] = "| **Sentiment**         | Is it positive, neutral, or negative?                 |";
        $prompt[] = "| **Topics/Keywords**   | What did they mention? (speed, quality, design, etc.) |";
        $prompt[] = "| **Emotion tone**      | Are they enthusiastic, disappointed, neutral?         |";
        $prompt[] = "| **Actionable points** | What should be improved or emphasized more?           |";
        $prompt[] = "| **Client type**       | Friendly? Demanding? Corporate tone?                  |";
        $prompt[] = "\nFor each goal, provide a detailed, actionable description based on the feedback above. Write your analysis in a clear, professional, and actionable style.";
        return implode("\n", $prompt);
    }

    /**
     * Generate a detailed AI prompt for expectations analysis only
     */
    public function generateExpectationsAnalysisPrompt($clientId)
    {
        $expectations = DB::table('client_expectations')
            ->where('client_id', $clientId)
            ->orderByDesc('expectation_created')
            ->get();
        $prompt = [];
        $prompt[] = "You are an expert business analyst AI. Here is the expectations history for this client:";
        foreach ($expectations->take(10) as $exp) {
            $prompt[] = "- Title: {$exp->title}, Status: {$exp->status}, Due: {$exp->due_date}, Created: {$exp->expectation_created}";
        }
        $prompt[] = "\nPlease analyze the following:";
        $prompt[] = "1. Progress on expectations (fulfilled, pending, overdue).";
        $prompt[] = "2. Any patterns or delays in meeting expectations.";
        $prompt[] = "3. Actionable recommendations for improving expectation management.";
        $prompt[] = "4. Any red flags or urgent issues.";
        $prompt[] = "Write your analysis in a clear, professional, and actionable style.";
        return implode("\n", $prompt);
    }

    /**
     * Generate a detailed AI prompt for projects analysis only
     */
    public function generateProjectsAnalysisPrompt($clientId)
    {
        $projects = DB::table('projects')
            ->where('project_clientid', $clientId)
            ->orderByDesc('project_created')
            ->get();
        $prompt = [];
        $prompt[] = "You are an expert business analyst AI. Here is the project history for this client:";
        foreach ($projects->take(10) as $p) {
            $prompt[] = "- Title: {$p->project_title}, Status: {$p->project_status}, Created: {$p->project_created}, Due: {$p->project_date_due}";
        }
        $prompt[] = "\nPlease analyze the following:";
        $prompt[] = "1. Overdue items or upcoming deadlines.";
        $prompt[] = "2. Patterns in project completion or delays.";
        $prompt[] = "3. Actionable recommendations for project management.";
        $prompt[] = "4. Any red flags or urgent issues.";
        $prompt[] = "Write your analysis in a clear, professional, and actionable style.";
        return implode("\n", $prompt);
    }

    /**
     * Generate a detailed AI prompt for comments analysis only
     */
    public function generateCommentsAnalysisPrompt($clientId)
    {
        $feedbacks = DB::table('feedbacks')
            ->where('client_id', $clientId)
            ->whereNotNull('comment')
            ->orderByDesc('feedback_created')
            ->get();
        $unanswered = $feedbacks->filter(function($fb) {
            $hasReply = DB::table('feedback_details')
                ->where('feedback_id', $fb->feedback_id)
                ->whereNotNull('value')
                ->exists();
            return !$hasReply;
        });
        $prompt = [];
        $prompt[] = "You are an expert business analyst AI. Here are the client comments that may need attention:";
        foreach ($unanswered->take(10) as $fb) {
            $prompt[] = "- Date: {$fb->feedback_created}, Comment: \"{$fb->comment}\"";
        }
        $prompt[] = "\nPlease analyze the following:";
        $prompt[] = "1. Identify any comments that have not been answered.";
        $prompt[] = "2. Suggest how to address these comments and improve communication.";
        $prompt[] = "3. Any red flags or urgent issues.";
        $prompt[] = "Write your analysis in a clear, professional, and actionable style.";
        return implode("\n", $prompt);
    }

    /**
     * Get aggregated customer health data by period.
     */
    public function getClientHealthReportData($clientId, $period = 'week')
    {
        [$startDate, $endDate, $label] = $this->resolvePeriodWindow($period);

        $clientStageTitle = 'Sin etapa';
        $clientStageDescription = '';
        if (Schema::hasTable('client_stages') && Schema::hasColumn('clients', 'client_stage_id')) {
            $stageData = DB::table('clients')
                ->leftJoin('client_stages', 'client_stages.client_stage_id', '=', 'clients.client_stage_id')
                ->select('client_stages.client_stage_title', 'client_stages.client_stage_description')
                ->where('clients.client_id', $clientId)
                ->first();

            if ($stageData && !empty($stageData->client_stage_title)) {
                $clientStageTitle = (string) $stageData->client_stage_title;
                $clientStageDescription = (string) ($stageData->client_stage_description ?? '');
            }
        }

        $notes = DB::table('notes')
            ->where('noteresource_type', 'client')
            ->where('noteresource_id', $clientId)
            ->whereBetween('note_created', [$startDate, $endDate])
            ->orderByDesc('note_created')
            ->get();

        $minutas = DB::table('client_minutas')
            ->select('client_minuta_id', 'minuta_date', 'minuta_detail', 'minuta_creatorid')
            ->where('client_id', $clientId)
            ->whereBetween('minuta_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderByDesc('minuta_date')
            ->get();

        $capacitaciones = DB::table('client_capacitaciones')
            ->select(
                'client_capacitacion_id',
                'capacitacion_date',
                'capacitacion_mode',
                'capacitacion_participants',
                'capacitacion_topics',
                'capacitacion_observations'
            )
            ->where('client_id', $clientId)
            ->whereBetween('capacitacion_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderByDesc('capacitacion_date')
            ->get();

        $tasks = DB::table('tasks')
            ->join('projects', 'projects.project_id', '=', 'tasks.task_projectid')
            ->select('tasks.task_id', 'tasks.task_title', 'tasks.task_status', 'tasks.task_created', 'tasks.task_updated', 'tasks.task_date_due')
            ->where('projects.project_clientid', $clientId)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('tasks.task_created', [$startDate, $endDate])
                    ->orWhereBetween('tasks.task_updated', [$startDate, $endDate]);
            })
            ->get();

        $completedTasks = $tasks->filter(function ($task) {
            $status = strtolower((string) ($task->task_status ?? ''));
            return in_array($status, ['2', 'completed', 'complete', 'done', 'closed'], true);
        });

        $completedTasksCount = $completedTasks->count();
        $pendingTasks = $tasks->count() - $completedTasksCount;

        $comments = DB::table('feedbacks')
            ->select('feedback_id', 'comment', 'feedback_created')
            ->where('client_id', $clientId)
            ->whereNotNull('comment')
            ->where('comment', '!=', '')
            ->whereBetween('feedback_created', [$startDate, $endDate])
            ->orderByDesc('feedback_created')
            ->get();

        $expectations = DB::table('client_expectations')
            ->select('title', 'status', 'due_date', 'expectation_created', 'expectation_updated')
            ->where('client_id', $clientId)
            ->whereBetween('expectation_created', [$startDate, $endDate])
            ->orderByDesc('expectation_created')
            ->get();

        $expectationsFulfilled = $expectations->filter(function ($item) {
            return strtolower((string) ($item->status ?? '')) === 'fulfilled';
        });
        $expectationsFulfilledCount = $expectationsFulfilled->count();

        $tenantBaseUrl = rtrim((string) url('/'), '/');
        $completedTasksWithLinks = $completedTasks->map(function ($task) use ($tenantBaseUrl) {
            $task->task_link = $tenantBaseUrl . '/tasks/v/' . $task->task_id;
            return $task;
        })->values();

        $expectationsFulfilledWithLinks = $expectationsFulfilled->map(function ($expectation) use ($tenantBaseUrl, $clientId) {
            $expectation->expectation_link = $tenantBaseUrl . '/clients/' . $clientId . '/expectativas';
            return $expectation;
        })->values();

        $feedbacks = DB::table('feedbacks')
            ->select('feedback_id', 'feedback_created', 'comment')
            ->where('client_id', $clientId)
            ->whereBetween('feedback_created', [$startDate, $endDate])
            ->orderByDesc('feedback_created')
            ->get();

        $feedbackLast3MonthsCount = DB::table('feedbacks')
            ->where('client_id', $clientId)
            ->where('feedback_created', '>=', Carbon::now()->subMonths(3))
            ->count();

        $expectationsLast3MonthsCount = DB::table('client_expectations')
            ->where('client_id', $clientId)
            ->where('expectation_created', '>=', Carbon::now()->subMonths(3))
            ->count();

        $minutasLast3MonthsCount = DB::table('client_minutas')
            ->where('client_id', $clientId)
            ->where('minuta_date', '>=', Carbon::now()->subMonths(3)->toDateString())
            ->count();

        $feedbackDetails = DB::table('feedback_details')
            ->join('feedbacks', 'feedbacks.feedback_id', '=', 'feedback_details.feedback_id')
            ->join('feedback_queries', 'feedback_queries.feedback_query_id', '=', 'feedback_details.feedback_query_id')
            ->select(
                'feedback_details.feedback_id',
                'feedback_details.value',
                'feedback_queries.weight as query_weight',
                'feedback_queries.range as query_range'
            )
            ->where('feedbacks.client_id', $clientId)
            ->whereBetween('feedbacks.feedback_created', [$startDate, $endDate])
            ->get();

        $feedbackSummary = $this->calculateFeedbackSummary($feedbackDetails);

        return [
            'period' => $period,
            'period_label' => $label,
            'date_start' => $startDate->toDateString(),
            'date_end' => $endDate->toDateString(),
            'client_stage_title' => $clientStageTitle,
            'client_stage_description' => $clientStageDescription,
            'notes' => $notes,
            'minutas' => $minutas,
            'minutas_count' => $minutas->count(),
            'capacitaciones' => $capacitaciones,
            'capacitaciones_count' => $capacitaciones->count(),
            'tasks_total' => $tasks->count(),
            'tasks_completed' => $completedTasksCount,
            'tasks_pending' => $pendingTasks,
            'tasks_completed_items' => $completedTasksWithLinks,
            'comments' => $comments,
            'expectations' => $expectations,
            'expectations_total' => $expectations->count(),
            'expectations_fulfilled' => $expectationsFulfilledCount,
            'expectations_fulfilled_items' => $expectationsFulfilledWithLinks,
            'feedbacks' => $feedbacks,
            'feedback_count' => $feedbacks->count(),
            'feedback_average' => $feedbackSummary['average_score'],
            'feedback_trend' => $feedbackSummary['trend'],
            'expectations_last_3_months_count' => $expectationsLast3MonthsCount,
            'has_expectation_last_3_months' => $expectationsLast3MonthsCount > 0,
            'feedback_last_3_months_count' => $feedbackLast3MonthsCount,
            'has_feedback_last_3_months' => $feedbackLast3MonthsCount > 0,
            'minutas_last_3_months_count' => $minutasLast3MonthsCount,
            'has_minuta_last_3_months' => $minutasLast3MonthsCount > 0,
        ];
    }

    /**
     * Build AI prompt for customer success health report.
     */
    public function generateClientHealthAnalysisPrompt($clientId, $period = 'week')
    {
        $client = DB::table('clients')
            ->select('client_id', 'client_company_name', 'client_status', 'client_description')
            ->where('client_id', $clientId)
            ->first();

        $healthData = $this->getClientHealthReportData($clientId, $period);

        $prompt = [];
        $prompt[] = "Eres especialista en Éxito del Cliente y análisis de salud de cuentas.";
        $prompt[] = "Genera un informe ejecutivo de salud del cliente para el período {$healthData['period_label']} ({$healthData['date_start']} a {$healthData['date_end']}).";
        $prompt[] = "";
        $prompt[] = "Cliente:";
        $prompt[] = "- Nombre: " . ($client->client_company_name ?? 'N/A');
        $prompt[] = "- Estado: " . ($client->client_status ?? 'N/A');
        $prompt[] = "- Descripción: " . ($client->client_description ?? 'N/A');
        $prompt[] = "- Etapa actual: " . ($healthData['client_stage_title'] ?? 'Sin etapa');
        $prompt[] = "- Descripción de etapa: " . (!empty($healthData['client_stage_description']) ? $healthData['client_stage_description'] : 'No definida');
        $prompt[] = "";
        $prompt[] = "Instrucción clave de análisis por etapa:";
        $prompt[] = "- Debes ajustar diagnóstico, riesgos, recomendaciones y próximos pasos según la etapa actual del cliente.";
        $prompt[] = "- No uses el mismo criterio para todas las etapas (ej: Implementación requiere foco distinto a Adopción).";
        $prompt[] = "";
        $prompt[] = "Datos base del período:";
        $prompt[] = "- Notas registradas: " . count($healthData['notes']);
        $prompt[] = "- Minutas registradas: " . ($healthData['minutas_count'] ?? 0);
        $prompt[] = "- Capacitaciones registradas: " . ($healthData['capacitaciones_count'] ?? 0);
        $prompt[] = "- Tareas totales: {$healthData['tasks_total']}";
        $prompt[] = "- Tareas completadas: {$healthData['tasks_completed']}";
        $prompt[] = "- Tareas pendientes: {$healthData['tasks_pending']}";
        $prompt[] = "- Comentarios del cliente: " . count($healthData['comments']);
        $prompt[] = "- Expectativas cargadas: {$healthData['expectations_total']}";
        $prompt[] = "- Expectativas cumplidas: {$healthData['expectations_fulfilled']}";
        $prompt[] = "- Feedbacks: {$healthData['feedback_count']}";
        $prompt[] = "- Score promedio de feedback: {$healthData['feedback_average']}";
        $prompt[] = "- Tendencia de feedback: {$healthData['feedback_trend']}";
        $prompt[] = "";
        $prompt[] = "Detalle breve (máximo 5 ítems por sección):";
        $prompt[] = "Notas:";
        foreach (collect($healthData['notes'])->take(5) as $note) {
            $prompt[] = "- {$note->note_created}: " . trim(($note->note_title ?? '') . ' ' . ($note->note_description ?? ''));
        }
        $prompt[] = "Minutas:";
        foreach (collect($healthData['minutas'] ?? [])->take(5) as $minuta) {
            $detalle = trim((string)($minuta->minuta_detail ?? ''));
            $prompt[] = "- {$minuta->minuta_date}: " . mb_strimwidth($detalle, 0, 220, '...');
        }
        $prompt[] = "Capacitaciones:";
        foreach (collect($healthData['capacitaciones'] ?? [])->take(5) as $capacitacion) {
            $modo = strtoupper((string)($capacitacion->capacitacion_mode ?? 'N/A'));
            $participantes = trim((string)($capacitacion->capacitacion_participants ?? ''));
            $temas = trim((string)($capacitacion->capacitacion_topics ?? ''));
            $prompt[] = "- {$capacitacion->capacitacion_date} [{$modo}] Participantes: {$participantes}. Temas: " . mb_strimwidth($temas, 0, 180, '...');
        }
        $prompt[] = "Comentarios:";
        foreach (collect($healthData['comments'])->take(5) as $comment) {
            $prompt[] = "- {$comment->feedback_created}: \"{$comment->comment}\"";
        }
        $prompt[] = "Expectativas:";
        foreach (collect($healthData['expectations'])->take(5) as $expectation) {
            $prompt[] = "- {$expectation->title} (estado: {$expectation->status}, vencimiento: {$expectation->due_date})";
        }
        $prompt[] = "Tareas completadas en el período (con link):";
        foreach (collect($healthData['tasks_completed_items'] ?? [])->take(10) as $task) {
            $prompt[] = "- [" . trim((string)($task->task_title ?? 'Tarea sin título')) . "](" . ($task->task_link ?? '') . ")";
        }
        $prompt[] = "Expectativas cumplidas en el período (con link):";
        foreach (collect($healthData['expectations_fulfilled_items'] ?? [])->take(10) as $expectation) {
            $prompt[] = "- [" . trim((string)($expectation->title ?? 'Expectativa')) . "](" . ($expectation->expectation_link ?? '') . ")";
        }
        $prompt[] = "";
        $prompt[] = "Devuelve un INFORME BREVE (aplica igual para Última semana, Último mes y Último trimestre).";
        $prompt[] = "Máximo 12-15 líneas en total. Sin introducciones largas.";
        $prompt[] = "";
        $prompt[] = "Usa exactamente esta estructura:";
        $prompt[] = "1) Qué se hizo en el período (3 bullets máximo).";
        $prompt[] = "2) Qué se habló en reuniones (solo si hay minutas en el período; si no hay, escribir 'Sin minutas en este período').";
        $prompt[] = "3) Qué avanzamos esta semana/mes/trimestre (3 bullets concretos).";
        $prompt[] = "4) Qué nos faltó esta semana/mes/trimestre (3 bullets concretos y accionables).";
        $prompt[] = "5) Próximo foco inmediato (1-2 acciones con responsable sugerido y fecha objetivo).";
        $prompt[] = "";
        $prompt[] = "Reglas:";
        $prompt[] = "- Prioriza claridad operativa para el equipo.";
        $prompt[] = "- Ajusta el análisis a la etapa actual del cliente.";
        $prompt[] = "- No inventes datos si faltan evidencias en notas/minutas/tareas/feedback.";
        $prompt[] = "- En 'Qué avanzamos' y/o 'Qué se hizo', menciona explícitamente tareas y expectativas completadas con links en formato markdown [Título](URL) cuando existan.";

        return implode("\n", $prompt);
    }

    /**
     * Build meeting preparation dataset based on events since last meeting (minuta).
     */
    public function getMeetingPreparationData($clientId, $fallbackDays = 60)
    {
        $fallbackDays = (int) $fallbackDays > 0 ? (int) $fallbackDays : 60;
        $now = Carbon::now();

        $clientQuery = DB::table('clients')
            ->select('clients.client_id', 'clients.client_company_name', 'clients.client_status', 'clients.client_description', 'clients.client_created')
            ->where('clients.client_id', $clientId);

        if (Schema::hasTable('client_stages') && Schema::hasColumn('clients', 'client_stage_id')) {
            $clientQuery->leftJoin('client_stages', 'client_stages.client_stage_id', '=', 'clients.client_stage_id')
                ->addSelect('client_stages.client_stage_title', 'client_stages.client_stage_description');
        } else {
            $clientQuery->addSelect(DB::raw("'Sin etapa' as client_stage_title"), DB::raw("'' as client_stage_description"));
        }

        $client = $clientQuery->first();

        $lastMinuta = DB::table('client_minutas')
            ->select('client_minuta_id', 'minuta_date', 'minuta_detail')
            ->where('client_id', $clientId)
            ->orderByDesc('minuta_date')
            ->orderByDesc('client_minuta_id')
            ->first();

        $referenceDate = $lastMinuta
            ? Carbon::parse($lastMinuta->minuta_date)->startOfDay()
            : $now->copy()->subDays($fallbackDays)->startOfDay();

        $stageChanges = collect([]);
        if (Schema::hasTable('client_stage_histories') && Schema::hasTable('client_stages')) {
            $stageChanges = DB::table('client_stage_histories as h')
                ->leftJoin('client_stages as from_stage', 'from_stage.client_stage_id', '=', 'h.from_stage_id')
                ->leftJoin('client_stages as to_stage', 'to_stage.client_stage_id', '=', 'h.to_stage_id')
                ->select(
                    'h.changed_at',
                    'from_stage.client_stage_title as from_stage_title',
                    'to_stage.client_stage_title as to_stage_title'
                )
                ->where('h.client_id', $clientId)
                ->where('h.changed_at', '>=', $referenceDate)
                ->orderByDesc('h.changed_at')
                ->get();
        }

        $minutasSinceReference = DB::table('client_minutas')
            ->select('client_minuta_id', 'minuta_date', 'minuta_detail')
            ->where('client_id', $clientId)
            ->where('minuta_date', '>', $referenceDate->toDateString())
            ->orderByDesc('minuta_date')
            ->get();

        $capacitacionesSinceReference = DB::table('client_capacitaciones')
            ->select('client_capacitacion_id', 'capacitacion_date', 'capacitacion_mode', 'capacitacion_topics', 'capacitacion_participants')
            ->where('client_id', $clientId)
            ->where('capacitacion_date', '>=', $referenceDate->toDateString())
            ->orderByDesc('capacitacion_date')
            ->get();

        $tasksSinceReference = DB::table('tasks')
            ->join('projects', 'projects.project_id', '=', 'tasks.task_projectid')
            ->select('tasks.task_id', 'tasks.task_title', 'tasks.task_status', 'tasks.task_created', 'tasks.task_date_due')
            ->where('projects.project_clientid', $clientId)
            ->where('tasks.task_created', '>=', $referenceDate)
            ->orderByDesc('tasks.task_created')
            ->get();

        $tasksCompleted = $tasksSinceReference->filter(function ($task) {
            $status = strtolower((string) ($task->task_status ?? ''));
            return in_array($status, ['2', 'completed', 'complete', 'done', 'closed'], true);
        });
        $tasksPending = $tasksSinceReference->filter(function ($task) {
            $status = strtolower((string) ($task->task_status ?? ''));
            return !in_array($status, ['2', 'completed', 'complete', 'done', 'closed'], true);
        });

        $tenantBaseUrl = rtrim((string) url('/'), '/');
        $tasksCompletedWithLinks = $tasksCompleted->map(function ($task) use ($tenantBaseUrl) {
            $task->task_link = $tenantBaseUrl . '/tasks/v/' . $task->task_id;
            return $task;
        })->values();
        $tasksPendingWithLinks = $tasksPending->map(function ($task) use ($tenantBaseUrl) {
            $task->task_link = $tenantBaseUrl . '/tasks/v/' . $task->task_id;
            return $task;
        })->values();

        $expectationsSinceReference = DB::table('client_expectations')
            ->select('title', 'status', 'due_date', 'expectation_created', 'expectation_updated')
            ->where('client_id', $clientId)
            ->where(function ($query) use ($referenceDate) {
                $query->where('expectation_created', '>=', $referenceDate)
                    ->orWhere('expectation_updated', '>=', $referenceDate);
            })
            ->orderByDesc('expectation_updated')
            ->orderByDesc('expectation_created')
            ->get();

        $expectationsOverdue = DB::table('client_expectations')
            ->select('title', 'status', 'due_date')
            ->where('client_id', $clientId)
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', $now->toDateString())
            ->where('status', '!=', 'fulfilled')
            ->orderBy('due_date', 'asc')
            ->get();

        $feedbackSinceReference = DB::table('feedbacks')
            ->select('feedback_id', 'feedback_created', 'comment')
            ->where('client_id', $clientId)
            ->where('feedback_created', '>=', $referenceDate)
            ->orderByDesc('feedback_created')
            ->get();

        $commentsSinceReference = $feedbackSinceReference->filter(function ($item) {
            return !empty(trim((string) ($item->comment ?? '')));
        })->values();

        $implementationReferenceDate = null;
        $implementationReferenceLabel = 'Sin hito de Implementación';

        if (Schema::hasTable('client_stage_histories') && Schema::hasTable('client_stages')) {
            $implementationEntry = DB::table('client_stage_histories as h')
                ->join('client_stages as to_stage', 'to_stage.client_stage_id', '=', 'h.to_stage_id')
                ->select('h.changed_at', 'to_stage.client_stage_title')
                ->where('h.client_id', $clientId)
                ->whereRaw('LOWER(to_stage.client_stage_title) LIKE ?', ['%implement%'])
                ->orderByDesc('h.changed_at')
                ->first();

            if ($implementationEntry && !empty($implementationEntry->changed_at)) {
                $implementationReferenceDate = Carbon::parse($implementationEntry->changed_at)->startOfDay();
                $implementationReferenceLabel = 'Desde entrada a Implementación';
            }
        }

        if (!$implementationReferenceDate && !empty($client->client_stage_title) && stripos((string) $client->client_stage_title, 'implement') !== false) {
            if (!empty($client->client_created)) {
                $implementationReferenceDate = Carbon::parse($client->client_created)->startOfDay();
                $implementationReferenceLabel = 'Cliente en Implementación (sin histórico, desde alta)';
            }
        }

        $tasksSinceImplementation = collect([]);
        $tasksCompletedSinceImplementation = collect([]);
        $tasksInProgressSinceImplementation = collect([]);
        $minutasSinceImplementation = collect([]);
        $feedbackSinceImplementation = collect([]);
        $expectationsSinceImplementation = collect([]);

        if ($implementationReferenceDate) {
            $tasksSinceImplementation = DB::table('tasks')
                ->join('projects', 'projects.project_id', '=', 'tasks.task_projectid')
                ->select('tasks.task_id', 'tasks.task_title', 'tasks.task_status', 'tasks.task_created')
                ->where('projects.project_clientid', $clientId)
                ->where('tasks.task_created', '>=', $implementationReferenceDate)
                ->orderByDesc('tasks.task_created')
                ->get();

            $tasksCompletedSinceImplementation = $tasksSinceImplementation->filter(function ($task) {
                $status = strtolower((string) ($task->task_status ?? ''));
                return in_array($status, ['2', 'completed', 'complete', 'done', 'closed'], true);
            })->values();

            $tasksInProgressSinceImplementation = $tasksSinceImplementation->filter(function ($task) {
                $status = strtolower((string) ($task->task_status ?? ''));
                return !in_array($status, ['2', 'completed', 'complete', 'done', 'closed'], true);
            })->values();

            $minutasSinceImplementation = DB::table('client_minutas')
                ->select('client_minuta_id')
                ->where('client_id', $clientId)
                ->where('minuta_date', '>=', $implementationReferenceDate->toDateString())
                ->get();

            $feedbackSinceImplementation = DB::table('feedbacks')
                ->select('feedback_id')
                ->where('client_id', $clientId)
                ->where('feedback_created', '>=', $implementationReferenceDate)
                ->get();

            $expectationsSinceImplementation = DB::table('client_expectations')
                ->select('title')
                ->where('client_id', $clientId)
                ->where(function ($query) use ($implementationReferenceDate) {
                    $query->where('expectation_created', '>=', $implementationReferenceDate)
                        ->orWhere('expectation_updated', '>=', $implementationReferenceDate);
                })
                ->get();
        }

        return [
            'client' => $client,
            'reference_date' => $referenceDate->toDateString(),
            'reference_label' => $lastMinuta ? 'Desde la última reunión (minuta)' : "Sin minuta previa (últimos {$fallbackDays} días)",
            'implementation_reference_date' => $implementationReferenceDate ? $implementationReferenceDate->toDateString() : null,
            'implementation_reference_label' => $implementationReferenceLabel,
            'last_minuta' => $lastMinuta,
            'stage_changes' => $stageChanges,
            'minutas_since_reference' => $minutasSinceReference,
            'capacitaciones_since_reference' => $capacitacionesSinceReference,
            'tasks_since_reference_total' => $tasksSinceReference->count(),
            'tasks_completed_since_reference' => $tasksCompleted->count(),
            'tasks_pending_since_reference' => $tasksPending->count(),
            'tasks_completed_items' => $tasksCompletedWithLinks,
            'tasks_in_progress_items' => $tasksPendingWithLinks,
            'expectations_since_reference' => $expectationsSinceReference,
            'expectations_overdue' => $expectationsOverdue,
            'feedback_since_reference' => $feedbackSinceReference,
            'comments_since_reference' => $commentsSinceReference,
            'tasks_since_implementation_total' => count($tasksSinceImplementation),
            'tasks_completed_since_implementation' => count($tasksCompletedSinceImplementation),
            'tasks_pending_since_implementation' => count($tasksInProgressSinceImplementation),
            'minutas_since_implementation_count' => count($minutasSinceImplementation),
            'feedback_since_implementation_count' => count($feedbackSinceImplementation),
            'expectations_since_implementation_count' => count($expectationsSinceImplementation),
        ];
    }

    /**
     * Build AI prompt for meeting preparation brief.
     */
    public function generateMeetingPreparationPrompt($clientId, $fallbackDays = 60)
    {
        $data = $this->getMeetingPreparationData($clientId, $fallbackDays);
        $client = $data['client'];

        $prompt = [];
        $prompt[] = 'Eres especialista en Customer Success y preparación de reuniones con clientes.';
        $prompt[] = 'Genera un informe breve y accionable para preparar la próxima reunión.';
        $prompt[] = '';
        $prompt[] = 'Contexto del cliente:';
        $prompt[] = '- Nombre: ' . ($client->client_company_name ?? 'N/A');
        $prompt[] = '- Estado: ' . ($client->client_status ?? 'N/A');
        $prompt[] = '- Etapa actual: ' . ($client->client_stage_title ?? 'Sin etapa');
        $prompt[] = '- Descripción de etapa: ' . (!empty($client->client_stage_description ?? '') ? $client->client_stage_description : 'No definida');
        $prompt[] = '- Marco temporal: ' . ($data['reference_label'] ?? 'Desde última referencia') . ' (' . ($data['reference_date'] ?? 'N/A') . ' a hoy)';
        if (!empty($data['implementation_reference_date'])) {
            $prompt[] = '- Segundo marco temporal: ' . ($data['implementation_reference_label'] ?? 'Desde Implementación') . ' (' . $data['implementation_reference_date'] . ' a hoy)';
        }
        $prompt[] = '';

        if (!empty($data['last_minuta'])) {
            $prompt[] = 'Última minuta registrada:';
            $prompt[] = '- Fecha: ' . $data['last_minuta']->minuta_date;
            $prompt[] = '- Resumen: ' . mb_strimwidth(trim((string)($data['last_minuta']->minuta_detail ?? '')), 0, 260, '...');
            $prompt[] = '';
        }

        $prompt[] = 'Evolución desde la referencia:';
        $prompt[] = '- Cambios de etapa: ' . count($data['stage_changes'] ?? []);
        $prompt[] = '- Minutas nuevas: ' . count($data['minutas_since_reference'] ?? []);
        $prompt[] = '- Capacitaciones: ' . count($data['capacitaciones_since_reference'] ?? []);
        $prompt[] = '- Tareas completadas: ' . ($data['tasks_completed_since_reference'] ?? 0);
        $prompt[] = '- Tareas pendientes: ' . ($data['tasks_pending_since_reference'] ?? 0);
        $prompt[] = '- Expectativas nuevas/actualizadas: ' . count($data['expectations_since_reference'] ?? []);
        $prompt[] = '- Expectativas vencidas: ' . count($data['expectations_overdue'] ?? []);
        $prompt[] = '- Feedbacks: ' . count($data['feedback_since_reference'] ?? []);
        $prompt[] = '- Comentarios con texto: ' . count($data['comments_since_reference'] ?? []);
        $prompt[] = '';

        if (!empty($data['implementation_reference_date'])) {
            $prompt[] = 'Evolución desde Implementación:';
            $prompt[] = '- Tareas totales: ' . ($data['tasks_since_implementation_total'] ?? 0);
            $prompt[] = '- Tareas completadas: ' . ($data['tasks_completed_since_implementation'] ?? 0);
            $prompt[] = '- Tareas en proceso: ' . ($data['tasks_pending_since_implementation'] ?? 0);
            $prompt[] = '- Minutas: ' . ($data['minutas_since_implementation_count'] ?? 0);
            $prompt[] = '- Feedbacks: ' . ($data['feedback_since_implementation_count'] ?? 0);
            $prompt[] = '- Expectativas nuevas/actualizadas: ' . ($data['expectations_since_implementation_count'] ?? 0);
            $prompt[] = '';
        }

        $prompt[] = 'Detalle breve de eventos (máximo 5 por sección):';
        $prompt[] = 'Cambios de etapa:';
        foreach (collect($data['stage_changes'] ?? [])->take(5) as $change) {
            $prompt[] = '- ' . ($change->changed_at ?? 'N/A') . ': ' . (($change->from_stage_title ?? 'Sin etapa') . ' -> ' . ($change->to_stage_title ?? 'Sin etapa'));
        }

        $prompt[] = 'Capacitaciones:';
        foreach (collect($data['capacitaciones_since_reference'] ?? [])->take(5) as $cap) {
            $prompt[] = '- ' . ($cap->capacitacion_date ?? 'N/A') . ' [' . strtoupper((string)($cap->capacitacion_mode ?? 'N/A')) . '] ' . mb_strimwidth(trim((string)($cap->capacitacion_topics ?? '')), 0, 160, '...');
        }

        $prompt[] = 'Expectativas vencidas:';
        foreach (collect($data['expectations_overdue'] ?? [])->take(5) as $expectation) {
            $prompt[] = '- ' . ($expectation->title ?? 'N/A') . ' (vencimiento: ' . ($expectation->due_date ?? 'N/A') . ', estado: ' . ($expectation->status ?? 'N/A') . ')';
        }

        $prompt[] = 'Comentarios del cliente:';
        foreach (collect($data['comments_since_reference'] ?? [])->take(5) as $comment) {
            $prompt[] = '- ' . ($comment->feedback_created ?? 'N/A') . ': "' . trim((string)($comment->comment ?? '')) . '"';
        }

        $prompt[] = 'Tareas completadas desde la última reunión (título + link):';
        foreach (collect($data['tasks_completed_items'] ?? [])->take(12) as $task) {
            $prompt[] = '- [' . trim((string)($task->task_title ?? 'Tarea sin título')) . '](' . ($task->task_link ?? '') . ')';
        }

        $prompt[] = 'Tareas en proceso desde la última reunión (título + link):';
        foreach (collect($data['tasks_in_progress_items'] ?? [])->take(12) as $task) {
            $prompt[] = '- [' . trim((string)($task->task_title ?? 'Tarea sin título')) . '](' . ($task->task_link ?? '') . ')';
        }

        $prompt[] = '';
        $prompt[] = 'Devuelve SOLO este formato breve:';
        $prompt[] = '1) Pantallazo general (5-7 líneas): cómo llegamos a esta reunión.';
        $prompt[] = '2) Qué cambió desde la última reunión: 3 a 5 bullets.';
        $prompt[] = '3) Qué cambió desde que entró a Implementación: 3 bullets (si hay dato, sino aclarar que no hay hito).';
        $prompt[] = '4) Tareas completadas desde la última reunión: lista breve con título y link.';
        $prompt[] = '5) Tareas en proceso: lista breve con título y link.';
        $prompt[] = '6) Riesgos y alertas: 3 bullets máximo.';
        $prompt[] = '7) Agenda sugerida para esta reunión: 5 puntos concretos.';
        $prompt[] = '8) Preguntas recomendadas para el cliente: 3 preguntas.';
        $prompt[] = 'Ajusta el criterio según la etapa actual del cliente y su descripción. Sé directo, accionable y no inventes datos faltantes.';
        $prompt[] = 'Importante: en las secciones de tareas conserva el formato markdown de link [Título](URL) para que el equipo pueda abrirlas desde el brief.';

        return implode("\n", $prompt);
    }

    private function resolvePeriodWindow($period)
    {
        $now = Carbon::now();
        $period = strtolower((string) $period);

        switch ($period) {
            case 'month':
                return [$now->copy()->subMonth(), $now, 'Último mes'];
            case 'quarter':
                return [$now->copy()->subMonths(3), $now, 'Último trimestre'];
            case 'week':
            default:
                return [$now->copy()->subWeek(), $now, 'Última semana'];
        }
    }

    /**
     * Calculate feedback summary statistics
     */
    private function calculateFeedbackSummary($feedbackDetails)
    {
        if ($feedbackDetails->isEmpty()) {
            return [
                'average_score' => 'N/A',
                'trend' => 'No feedback available'
            ];
        }

        // Group by feedback_id to calculate overall scores
        $feedbackScores = [];
        foreach ($feedbackDetails->groupBy('feedback_id') as $feedbackId => $details) {
            $totalWeightedScore = 0;
            $totalWeight = 0;
            
            foreach ($details as $detail) {
                $weightedScore = $detail->value * $detail->query_weight;
                $totalWeightedScore += $weightedScore;
                $totalWeight += $detail->query_weight;
            }
            
            if ($totalWeight > 0) {
                $feedbackScores[] = $totalWeightedScore / $totalWeight;
            }
        }

        $averageScore = count($feedbackScores) > 0 ? round(array_sum($feedbackScores) / count($feedbackScores), 2) : 0;

        // Determine trend (simplified - you could make this more sophisticated)
        $trend = 'Stable';
        if (count($feedbackScores) >= 2) {
            $recentScores = array_slice($feedbackScores, 0, 3);
            $olderScores = array_slice($feedbackScores, -3);
            
            $recentAvg = array_sum($recentScores) / count($recentScores);
            $olderAvg = array_sum($olderScores) / count($olderScores);
            
            if ($recentAvg > $olderAvg + 0.5) {
                $trend = 'Improving';
            } elseif ($recentAvg < $olderAvg - 0.5) {
                $trend = 'Declining';
            }
        }

        return [
            'average_score' => $averageScore,
            'trend' => $trend
        ];
    }

    /**
     * Calculate expectations summary statistics
     */
    private function calculateExpectationsSummary($expectations)
    {
        if ($expectations->isEmpty()) {
            return [
                'fulfilled_count' => 0,
                'fulfilled_percent' => 0,
                'pending_count' => 0,
                'overdue_count' => 0
            ];
        }

        $fulfilled = $expectations->where('status', 'fulfilled')->count();
        $pending = $expectations->where('status', 'pending')->count();
        $overdue = $expectations->where('status', 'overdue')->count();
        $total = $expectations->count();

        $fulfilledPercent = $total > 0 ? round(($fulfilled / $total) * 100, 1) : 0;

        return [
            'fulfilled_count' => $fulfilled,
            'fulfilled_percent' => $fulfilledPercent,
            'pending_count' => $pending,
            'overdue_count' => $overdue
        ];
    }

    /**
     * Check if client has received feedback in the last $months months.
     * Returns ['has_recent_feedback' => bool, 'last_feedback_date' => date|null, 'details' => array]
     */
    public function getRecentFeedbackStatus($clientId, $months = 3)
    {
        $since = Carbon::now()->subMonths($months);
        $feedbacks = DB::table('feedbacks')
            ->where('client_id', $clientId)
            ->where('feedback_created', '>=', $since)
            ->orderByDesc('feedback_created')
            ->get();
        return [
            'has_recent_feedback' => $feedbacks->count() > 0,
            'last_feedback_date' => $feedbacks->first()->feedback_created ?? null,
            'details' => $feedbacks
        ];
    }

    /**
     * Check if client has made progress on any expectations in the last $months months.
     * Returns ['has_recent_progress' => bool, 'recent_expectations' => array, 'details' => array]
     */
    public function getRecentExpectationProgress($clientId, $months = 3)
    {
        $since = Carbon::now()->subMonths($months);
        $expectations = DB::table('client_expectations')
            ->where('client_id', $clientId)
            ->where('expectation_updated', '>=', $since)
            ->orderByDesc('expectation_updated')
            ->get();
        return [
            'has_recent_progress' => $expectations->count() > 0,
            'recent_expectations' => $expectations,
            'details' => $expectations
        ];
    }

    /**
     * Get projects with overdue items or deadlines within $daysUpcoming days.
     * Returns ['overdue' => array, 'upcoming' => array]
     */
    public function getProjectOverdueOrUpcoming($clientId, $daysUpcoming = 14)
    {
        $now = Carbon::now();
        $upcoming = $now->copy()->addDays($daysUpcoming);
        $projects = DB::table('projects')
            ->where('project_clientid', $clientId)
            ->select('project_id', 'project_title', 'project_status', 'project_date_due')
            ->get();
        $overdue = $projects->filter(function($p) use ($now) {
            return $p->project_date_due && Carbon::parse($p->project_date_due)->lt($now) && $p->project_status != 'completed';
        })->values();
        $upcomingList = $projects->filter(function($p) use ($now, $upcoming) {
            return $p->project_date_due && Carbon::parse($p->project_date_due)->gte($now) && Carbon::parse($p->project_date_due)->lte($upcoming) && $p->project_status != 'completed';
        })->values();
        return [
            'overdue' => $overdue,
            'upcoming' => $upcomingList
        ];
    }

    /**
     * Get client feedback comments that have not received a reply (unanswered).
     * Returns array of feedbacks with comments and no reply.
     */
    public function getUnansweredClientComments($clientId)
    {
        // Feedbacks with a comment
        $feedbacks = DB::table('feedbacks')
            ->where('client_id', $clientId)
            ->whereNotNull('comment')
            ->orderByDesc('feedback_created')
            ->get();
        // For each feedback, check if there is a reply in feedback_details or another table (customize as needed)
        $unanswered = $feedbacks->filter(function($fb) {
            // If there is no feedback_detail with a non-null value or reply, consider it unanswered
            $hasReply = DB::table('feedback_details')
                ->where('feedback_id', $fb->feedback_id)
                ->whereNotNull('value')
                ->exists();
            return !$hasReply;
        })->values();
        return $unanswered;
    }

    /**
     * Get latest feedbacks with marks for a client
     */
    public function getLatestFeedbackWithMarks($clientId, $limit = 3)
    {
        $query = DB::table('feedbacks as f')
            ->join('feedback_details as d', 'f.feedback_id', '=', 'd.feedback_id')
            ->join('feedback_queries as q', 'd.feedback_query_id', '=', 'q.feedback_query_id')
            ->select(
                'f.feedback_id',
                'f.feedback_created',
                'f.comment',
                DB::raw('ROUND(SUM(q.weight * d.value) * 10 / SUM(q.weight * q.range), 2) as total_marks')
            )
            ->where('f.client_id', $clientId)
            ->groupBy('f.feedback_id', 'f.feedback_created', 'f.comment')
            ->orderBy('f.feedback_created', 'desc')
            ->limit($limit);
        return $query->get();
    }
}
