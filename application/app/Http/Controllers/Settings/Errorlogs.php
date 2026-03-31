<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class Errorlogs extends Controller
{
    /**
     * Display error logs
     */
    public function index()
    {
        try {
            $logFile = storage_path('logs/laravel.log');
            $logs = [];
            
            if (File::exists($logFile)) {
                $content = File::get($logFile);
                $lines = explode("\n", $content);
                
                // Get last 100 lines
                $logs = array_slice($lines, -100);
                $logs = array_reverse($logs);
            }
            
            $data = [
                'logs' => $logs,
                'logFile' => $logFile
            ];
            
            return view('pages.settings.errorlogs', $data);
            
        } catch (\Exception $e) {
            Log::error('Error loading error logs: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load error logs');
        }
    }
    
    /**
     * Delete error logs
     */
    public function delete(Request $request)
    {
        try {
            $logFile = storage_path('logs/laravel.log');
            
            if (File::exists($logFile)) {
                File::put($logFile, '');
            }
            
            return redirect()->back()->with('success', 'Error logs cleared successfully');
            
        } catch (\Exception $e) {
            Log::error('Error deleting error logs: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to clear error logs');
        }
    }
    
    /**
     * Download error logs
     */
    public function download()
    {
        try {
            $logFile = storage_path('logs/laravel.log');
            
            if (!File::exists($logFile)) {
                return redirect()->back()->with('error', 'Log file not found');
            }
            
            return response()->download($logFile, 'error-logs-' . date('Y-m-d-H-i-s') . '.log');
            
        } catch (\Exception $e) {
            Log::error('Error downloading error logs: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to download error logs');
        }
    }
}
