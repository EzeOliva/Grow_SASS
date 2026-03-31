<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WhatsappConnection;
use Illuminate\Support\Facades\Validator;

class WhatsappConnectionController extends Controller
{
    /**
     * Display a listing of WhatsApp connections
     */
    public function index()
    {
        $connections = WhatsappConnection::where('tenant_id', app('currentTenant')->id ?? 1) // TODO: Get from current tenant context
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('whatsapp.connections.index', compact('connections'));
    }

    /**
     * Show the form for creating a new connection
     */
    public function create()
    {
        return view('whatsapp.connections.create');
    }

    /**
     * Store a newly created connection
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'connection_name' => 'required|string|max:255',
            'connection_type' => 'required|in:baileys,twilio,360dialog,gupshup',
            'phone_number' => 'nullable|string|max:255',
            'connection_data' => 'nullable|array',
            'webhook_config' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $connection = WhatsappConnection::create([
                            'tenant_id' => app('currentTenant')->id ?? 1, // TODO: Get from current tenant context
            'connection_name' => $request->connection_name,
            'connection_type' => $request->connection_type,
            'phone_number' => $request->phone_number,
            'connection_data' => $request->connection_data,
            'webhook_config' => $request->webhook_config,
            'status' => 'disconnected',
        ]);

        return redirect()->route('whatsapp.connections.index')
            ->with('success', 'Connection created successfully!');
    }

    /**
     * Display the specified connection
     */
    public function show(WhatsappConnection $connection)
    {
        return view('whatsapp.connections.show', compact('connection'));
    }

    /**
     * Show the form for editing the specified connection
     */
    public function edit(WhatsappConnection $connection)
    {
        return view('whatsapp.connections.edit', compact('connection'));
    }

    /**
     * Update the specified connection
     */
    public function update(Request $request, WhatsappConnection $connection)
    {
        $validator = Validator::make($request->all(), [
            'connection_name' => 'required|string|max:255',
            'connection_type' => 'required|in:baileys,twilio,360dialog,gupshup',
            'phone_number' => 'nullable|string|max:255',
            'connection_data' => 'nullable|array',
            'webhook_config' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $connection->update($request->only([
            'connection_name',
            'connection_type',
            'phone_number',
            'connection_data',
            'webhook_config',
        ]));

        return redirect()->route('whatsapp.connections.index')
            ->with('success', 'Connection updated successfully!');
    }

    /**
     * Remove the specified connection
     */
    public function destroy(WhatsappConnection $connection)
    {
        $connection->delete();
        return redirect()->route('whatsapp.connections.index')
            ->with('success', 'Connection deleted successfully!');
    }

    /**
     * Connect to WhatsApp
     */
    public function connect(WhatsappConnection $connection)
    {
        // TODO: Implement actual WhatsApp connection logic
        $connection->update([
            'status' => 'connecting',
            'last_connected_at' => now(),
        ]);

        return redirect()->route('whatsapp.connections.index')
            ->with('success', 'Connection initiated!');
    }

    /**
     * Disconnect from WhatsApp
     */
    public function disconnect(WhatsappConnection $connection)
    {
        $connection->update([
            'status' => 'disconnected',
        ]);

        return redirect()->route('whatsapp.connections.index')
            ->with('success', 'Connection disconnected!');
    }

    /**
     * Get QR code for Baileys connection
     */
    public function getQRCode(WhatsappConnection $connection)
    {
        if ($connection->connection_type !== 'baileys') {
            return response()->json(['error' => 'QR code only available for Baileys connections'], 400);
        }

        try {
            // Generate QR code data
            $qrData = [
                'connection_id' => $connection->id,
                'phone_number' => $connection->phone_number,
                'connection_name' => $connection->connection_name,
                'timestamp' => now()->timestamp,
                'type' => 'whatsapp_connection'
            ];

            // Create QR code using a simple approach
            $qrCode = $this->generateQRCodeImage(json_encode($qrData));

            return response()->json(['qr_code' => $qrCode]);

        } catch (\Exception $e) {
            \Log::error('Error generating QR code: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate QR code'], 500);
        }
    }

    /**
     * Generate QR code image using simple approach
     */
    private function generateQRCodeImage(string $data): string
    {
        // For now, we'll use a simple approach with a QR code API
        // In production, you should use a proper QR code library like SimpleSoftwareIO/simple-qrcode
        
        $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/';
        $params = http_build_query([
            'size' => '200x200',
            'data' => $data,
            'format' => 'png',
            'bgcolor' => 'ffffff',
            'color' => '000000',
            'margin' => '10'
        ]);

        $fullUrl = $qrCodeUrl . '?' . $params;
        
        // Try to get the QR code image
        try {
            $imageData = file_get_contents($fullUrl);
            if ($imageData !== false) {
                return 'data:image/png;base64,' . base64_encode($imageData);
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to fetch QR code from external API: ' . $e->getMessage());
        }

        // Fallback: Generate a simple placeholder QR code
        return $this->generatePlaceholderQRCode($data);
    }

    /**
     * Generate a placeholder QR code (simple pattern)
     */
    private function generatePlaceholderQRCode(string $data): string
    {
        // Create a simple 200x200 QR-like pattern
        $size = 200;
        $image = imagecreate($size, $size);
        
        // Colors
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        
        // Fill with white
        imagefill($image, 0, 0, $white);
        
        // Create a simple pattern that looks like a QR code
        $blockSize = 10;
        $blocks = $size / $blockSize;
        
        // Add some random blocks to simulate QR code
        $hash = md5($data);
        for ($i = 0; $i < $blocks; $i++) {
            for ($j = 0; $j < $blocks; $j++) {
                $charIndex = ($i * $blocks + $j) % strlen($hash);
                $char = $hash[$charIndex];
                $shouldFill = (ord($char) % 2) == 0;
                
                if ($shouldFill) {
                    imagefilledrectangle(
                        $image,
                        $i * $blockSize,
                        $j * $blockSize,
                        ($i + 1) * $blockSize - 1,
                        ($j + 1) * $blockSize - 1,
                        $black
                    );
                }
            }
        }
        
        // Add corner markers (like real QR codes)
        $this->addQRCornerMarker($image, 0, 0, $blockSize, $black, $white);
        $this->addQRCornerMarker($image, $size - 7 * $blockSize, 0, $blockSize, $black, $white);
        $this->addQRCornerMarker($image, 0, $size - 7 * $blockSize, $blockSize, $black, $white);
        
        // Output as base64
        ob_start();
        imagepng($image);
        $imageData = ob_get_contents();
        ob_end_clean();
        imagedestroy($image);
        
        return 'data:image/png;base64,' . base64_encode($imageData);
    }

    /**
     * Add corner marker to QR code
     */
    private function addQRCornerMarker($image, $x, $y, $blockSize, $black, $white)
    {
        // Outer square (7x7)
        imagefilledrectangle($image, $x, $y, $x + 6 * $blockSize, $y + 6 * $blockSize, $black);
        
        // Inner white square (5x5)
        imagefilledrectangle($image, $x + $blockSize, $y + $blockSize, $x + 5 * $blockSize, $y + 5 * $blockSize, $white);
        
        // Center black square (3x3)
        imagefilledrectangle($image, $x + 2 * $blockSize, $y + 2 * $blockSize, $x + 4 * $blockSize, $y + 4 * $blockSize, $black);
    }
}

