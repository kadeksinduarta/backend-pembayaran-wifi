<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\UserPayment;
use App\Models\WifiBill;
use App\Models\PaymentSetting;

class UserController extends Controller
{
    public function getDashboard(Request $request)
    {
        $user = $request->user();
        
        // Latest bill for this user
        $latestPayment = UserPayment::where('user_id', $user->id)
            ->with('bill')
            ->latest()
            ->first();

        $settings = PaymentSetting::first();

        return response()->json([
            'latest_payment' => $latestPayment,
            'payment_settings' => $settings,
            'user' => $user
        ]);
    }

    public function getBills(Request $request)
    {
        $payments = UserPayment::where('user_id', $request->user()->id)
            ->with('bill')
            ->latest()
            ->get();

        return response()->json($payments);
    }

    public function uploadProof(Request $request, $id)
    {
        $payment = UserPayment::where('user_id', $request->user()->id)->findOrFail($id);

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('proofs', 'public');
            $payment->update([
                'payment_proof' => $path,
                'status' => 'pending'
            ]);
        }

        return response()->json($payment);
    }
}
