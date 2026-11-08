<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AwbController
{
    public function show(Request $request, Shipment $shipment): View
    {
        abort_unless(
            $request->user()?->can('view shipments') ?? false,
            403
        );

        $shipment->load([
            'originBranch',
            'destinationBranch',
        ]);

        return view('shipments.awb', compact('shipment'));
    }
}
