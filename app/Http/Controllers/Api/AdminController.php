<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\WifiBill;
use App\Models\UserPayment;
use App\Models\PaymentSetting;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    // User management
    public function getUsers()
    {
        return response()->json(User::where('role', 'user')->get());
    }

    public function createUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'username' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $validated['role'] = 'user';
        $validated['is_active'] = true;

        $user = User::create($validated);
        return response()->json($user, 201);
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $validated = $request->validate([
            'name' => 'string',
            'username' => 'string|unique:users,username,' . $id,
            'email' => 'email|unique:users,email,' . $id,
            'is_active' => 'boolean',
        ]);

        if ($request->password) {
            $validated['password'] = bcrypt($request->password);
        }

        $user->update($validated);
        return response()->json($user);
    }

    public function deleteUser($id)
    {
        User::findOrFail($id)->delete();
        return response()->json(['message' => 'User deleted']);
    }

    // Bill management
    public function getBills()
    {
        return response()->json(WifiBill::withCount('payments')->get());
    }

    public function createBill(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|string',
            'total_amount' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $bill = WifiBill::create([
                'month' => $validated['month'],
                'total_amount' => $validated['total_amount'],
                'created_by' => $request->user()->id,
            ]);

            $activeUsers = User::where('role', 'user')->where('is_active', true)->get();
            $count = $activeUsers->count();

            if ($count > 0) {
                $amountPerUser = $validated['total_amount'] / $count;

                foreach ($activeUsers as $user) {
                    UserPayment::create([
                        'user_id' => $user->id,
                        'bill_id' => $bill->id,
                        'amount_due' => $amountPerUser,
                        'amount_paid' => 0,
                        'status' => 'unpaid',
                    ]);
                }
            }

            return response()->json($bill->load('payments'), 201);
        });
    }

    public function deleteBill($id)
    {
        WifiBill::findOrFail($id)->delete();
        return response()->json(['message' => 'Bill deleted']);
    }

    // Payment management
    public function getPayments()
    {
        return response()->json(UserPayment::with(['user', 'bill'])->latest()->get());
    }

    public function verifyPayment(Request $request, $id)
    {
        $payment = UserPayment::findOrFail($id);
        $payment->update([
            'status' => 'paid',
            'amount_paid' => $payment->amount_due
        ]);

        return response()->json($payment);
    }

    // Settings
    public function getSettings()
    {
        return response()->json(PaymentSetting::first());
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'bank_name' => 'required|string',
            'account_number' => 'required|string',
            'account_owner' => 'required|string',
        ]);

        $settings = PaymentSetting::first();
        if ($settings) {
            $settings->update($validated);
        } else {
            $settings = PaymentSetting::create($validated);
        }

        return response()->json($settings);
    }
}
