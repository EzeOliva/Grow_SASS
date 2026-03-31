<?php

/**
 * @fileoverview Account Pay Mollie Controller
 * @description Placeholder controller for Mollie payment processing
 */

namespace App\Http\Controllers\Account\Pay;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Mollie extends Controller
{
    /**
     * Display the thank you page after Mollie payment
     *
     * @return \Illuminate\Http\Response
     */
    public function thankYouPage()
    {
        return response()->json([
            'message' => 'Mollie payment thank you page - not implemented yet'
        ]);
    }

    /**
     * Process Mollie payment
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function payNowButton(Request $request)
    {
        return response()->json([
            'message' => 'Mollie payment processing - not implemented yet'
        ]);
    }
}
