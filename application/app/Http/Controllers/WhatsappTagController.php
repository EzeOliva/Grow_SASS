<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WhatsappTag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * @fileoverview WhatsApp Tag Controller
 * @description Manages global tags for contacts and tickets with color coding and categorization
 */
class WhatsappTagController extends Controller
{
    /**
     * Display a listing of tags
     */
    public function index(Request $request)
    {
        $tenantId = app('currentTenant')->id ?? 1;
        
        // Log the tenant ID for debugging
        \Log::info('Retrieving tags for tenant_id: ' . $tenantId);
        
        $query = WhatsappTag::where('tenant_id', $tenantId);

        // Apply filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $tags = $query->with(['creator'])
            ->orderBy('type')
            ->orderBy('name')
            ->paginate(20);

        // Log the number of tags found
        \Log::info('Found ' . $tags->count() . ' tags for tenant_id: ' . $tenantId);

        $tagTypes = [
            'global' => 'Global Tags',
            'contact' => 'Contact Tags',
            'ticket' => 'Ticket Tags'
        ];

        return view('whatsapp.tags.index', compact('tags', 'tagTypes'));
    }

    /**
     * Show the form for creating a new tag
     */
    public function create()
    {
        $tagTypes = [
            'global' => 'Global Tags (apply to both contacts and tickets)',
            'contact' => 'Contact Tags (apply only to contacts)',
            'ticket' => 'Ticket Tags (apply only to tickets)'
        ];

        $colors = [
            '#6c757d' => 'Gray',
            '#007bff' => 'Blue',
            '#28a745' => 'Green',
            '#ffc107' => 'Yellow',
            '#dc3545' => 'Red',
            '#fd7e14' => 'Orange',
            '#6f42c1' => 'Purple',
            '#e83e8c' => 'Pink',
            '#20c997' => 'Teal',
            '#17a2b8' => 'Cyan'
        ];

        return view('whatsapp.tags.create', compact('tagTypes', 'colors'));
    }

    /**
     * Store a newly created tag
     */
    public function store(Request $request)
    {
        // Log that the request reached the controller
        \Log::info('WhatsappTagController@store called', [
            'request_data' => $request->all(),
            'user_agent' => $request->userAgent(),
            'ip' => $request->ip(),
            'method' => $request->method(),
            'url' => $request->url()
        ]);
        
        // Also log to a file for easier debugging
        file_put_contents(storage_path('logs/tag_debug.log'), 
            date('Y-m-d H:i:s') . " - Store method called\n" . 
            "Data: " . json_encode($request->all()) . "\n" . 
            "Method: " . $request->method() . "\n" . 
            "URL: " . $request->url() . "\n\n", 
            FILE_APPEND
        );
        
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:global,contact,ticket'
        ]);

        try {
            $tenantId = app('currentTenant')->id ?? 1;
            
            // Log the tenant ID for debugging
            \Log::info('Creating tag with tenant_id: ' . $tenantId);
            
            // Also log to file for easier debugging
            file_put_contents(storage_path('logs/tag_debug.log'), 
                date('Y-m-d H:i:s') . " - About to create tag with tenant_id: $tenantId\n", 
                FILE_APPEND
            );
            
            // Check for duplicate tag name within the same type
            $existingTag = WhatsappTag::where('tenant_id', $tenantId)
                ->where('name', $request->name)
                ->where('type', $request->type)
                ->first();

            if ($existingTag) {
                return back()->with('error', 'A tag with this name already exists for the selected type.')->withInput();
            }

            // Log before creating the tag
            file_put_contents(storage_path('logs/tag_debug.log'), 
                date('Y-m-d H:i:s') . " - About to create tag with data: " . json_encode([
                    'tenant_id' => $tenantId,
                    'name' => $request->name,
                    'color' => $request->color,
                    'description' => $request->description,
                    'type' => $request->type,
                    'is_active' => true,
                    'created_by' => Auth::id()
                ]) . "\n", 
                FILE_APPEND
            );
            
            $tag = WhatsappTag::create([
                'tenant_id' => $tenantId,
                'name' => $request->name,
                'color' => $request->color,
                'description' => $request->description,
                'type' => $request->type,
                'is_active' => true,
                'created_by' => Auth::id()
            ]);

            \Log::info('Tag created successfully with ID: ' . $tag->id);
            
            // Also log to file for easier debugging
            file_put_contents(storage_path('logs/tag_debug.log'), 
                date('Y-m-d H:i:s') . " - Tag created successfully with ID: " . $tag->id . "\n", 
                FILE_APPEND
            );

            // Log before redirect
            file_put_contents(storage_path('logs/tag_debug.log'), 
                date('Y-m-d H:i:s') . " - About to redirect to whatsapp.tags.index\n", 
                FILE_APPEND
            );
            
            return redirect()->route('whatsapp.tags.index')
                ->with('success', 'Tag created successfully!');
        } catch (\Exception $e) {
            \Log::error('Error creating tag: ' . $e->getMessage());
            
            // Also log to file for easier debugging
            file_put_contents(storage_path('logs/tag_debug.log'), 
                date('Y-m-d H:i:s') . " - ERROR creating tag: " . $e->getMessage() . "\n" . 
                "Trace: " . $e->getTraceAsString() . "\n", 
                FILE_APPEND
            );
            
            return back()->with('error', 'Error creating tag: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified tag
     */
    public function show(WhatsappTag $tag)
    {
        $tag->load(['creator']);
        
        // Get usage statistics
        $usageStats = $this->getTagUsageStats($tag);

        return view('whatsapp.tags.show', compact('tag', 'usageStats'));
    }

    /**
     * Show the form for editing the specified tag
     */
    public function edit(WhatsappTag $tag)
    {
        $tagTypes = [
            'global' => 'Global Tags (apply to both contacts and tickets)',
            'contact' => 'Contact Tags (apply only to contacts)',
            'ticket' => 'Ticket Tags (apply only to tickets)'
        ];

        $colors = [
            '#6c757d' => 'Gray',
            '#007bff' => 'Blue',
            '#28a745' => 'Green',
            '#ffc107' => 'Yellow',
            '#dc3545' => 'Red',
            '#fd7e14' => 'Orange',
            '#6f42c1' => 'Purple',
            '#e83e8c' => 'Pink',
            '#20c997' => 'Teal',
            '#17a2b8' => 'Cyan'
        ];

        return view('whatsapp.tags.edit', compact('tag', 'tagTypes', 'colors'));
    }

    /**
     * Update the specified tag
     */
    public function update(Request $request, WhatsappTag $tag)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:global,contact,ticket'
        ]);

        try {
            // Check for duplicate tag name within the same type (excluding current tag)
            $existingTag = WhatsappTag::where('tenant_id', app('currentTenant')->id ?? 1)
                ->where('name', $request->name)
                ->where('type', $request->type)
                ->where('id', '!=', $tag->id)
                ->first();

            if ($existingTag) {
                return back()->with('error', 'A tag with this name already exists for the selected type.')->withInput();
            }

            $tag->update([
                'name' => $request->name,
                'color' => $request->color,
                'description' => $request->description,
                'type' => $request->type
            ]);

            return redirect()->route('whatsapp.tags.show', $tag)
                ->with('success', 'Tag updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating tag: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified tag
     */
    public function destroy(WhatsappTag $tag)
    {
        try {
            // Check if tag is in use
            $usageCount = $this->getTagUsageCount($tag);
            
            if ($usageCount > 0) {
                return back()->with('error', "Cannot delete tag. It is currently used by {$usageCount} items. Please remove all assignments first.");
            }

            $tag->delete();

            return redirect()->route('whatsapp.tags.index')
                ->with('success', 'Tag deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting tag: ' . $e->getMessage());
        }
    }

    /**
     * Toggle tag active status
     */
    public function toggleStatus(WhatsappTag $tag)
    {
        try {
            $tag->update(['is_active' => !$tag->is_active]);

            $status = $tag->is_active ? 'activated' : 'deactivated';
            return back()->with('success', "Tag {$status} successfully!");
        } catch (\Exception $e) {
            return back()->with('error', 'Error toggling tag status: ' . $e->getMessage());
        }
    }

    /**
     * Get tags for AJAX requests (used in forms)
     */
    public function getTags(Request $request)
    {
        $type = $request->get('type', 'global');
        $search = $request->get('search', '');

        $query = WhatsappTag::where('tenant_id', app('currentTenant')->id ?? 1)
            ->where('is_active', true);

        // Filter by type
        if ($type === 'global') {
            $query->whereIn('type', ['global', $request->get('context', 'ticket')]);
        } else {
            $query->where('type', $type);
        }

        // Apply search
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $tags = $query->select('id', 'name', 'color', 'type')
            ->orderBy('name')
            ->limit(20)
            ->get();

        return response()->json($tags);
    }

    /**
     * Get tag usage statistics
     */
    private function getTagUsageStats(WhatsappTag $tag): array
    {
        $stats = [
            'total_usage' => 0,
            'ticket_usage' => 0,
            'contact_usage' => 0
        ];

        try {
            // Count ticket usage
            if ($tag->isForTickets()) {
                $stats['ticket_usage'] = DB::table('whatsapp_ticket_tags')
                    ->where('tag_id', $tag->id)
                    ->count();
            }

            // Count contact usage (if applicable)
            if ($tag->isForContacts()) {
                // This would depend on your contact system implementation
                $stats['contact_usage'] = 0; // Placeholder
            }

            $stats['total_usage'] = $stats['ticket_usage'] + $stats['contact_usage'];
        } catch (\Exception $e) {
            // Log error but don't fail
            \Log::warning("Error getting tag usage stats: " . $e->getMessage());
        }

        return $stats;
    }

    /**
     * Get total usage count for a tag
     */
    private function getTagUsageCount(WhatsappTag $tag): int
    {
        try {
            $count = 0;

            if ($tag->isForTickets()) {
                $count += DB::table('whatsapp_ticket_tags')
                    ->where('tag_id', $tag->id)
                    ->count();
            }

            if ($tag->isForContacts()) {
                // This would depend on your contact system implementation
                // $count += ContactTag::where('tag_id', $tag->id)->count();
            }

            return $count;
        } catch (\Exception $e) {
            \Log::warning("Error getting tag usage count: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Bulk operations on tags
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'tag_ids' => 'required|array',
            'tag_ids.*' => 'exists:whatsapp_tags,id'
        ]);

        try {
            $tags = WhatsappTag::whereIn('id', $request->tag_ids)
                ->where('tenant_id', app('currentTenant')->id ?? 1)
                ->get();

            $successCount = 0;
            $errorCount = 0;

            foreach ($tags as $tag) {
                try {
                    switch ($request->action) {
                        case 'activate':
                            $tag->update(['is_active' => true]);
                            $successCount++;
                            break;
                        case 'deactivate':
                            $tag->update(['is_active' => false]);
                            $successCount++;
                            break;
                        case 'delete':
                            if ($this->getTagUsageCount($tag) === 0) {
                                $tag->delete();
                                $successCount++;
                            } else {
                                $errorCount++;
                            }
                            break;
                    }
                } catch (\Exception $e) {
                    $errorCount++;
                }
            }

            $message = "Bulk action completed. {$successCount} tags processed successfully.";
            if ($errorCount > 0) {
                $message .= " {$errorCount} tags could not be processed.";
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Error performing bulk action: ' . $e->getMessage());
        }
    }
}

