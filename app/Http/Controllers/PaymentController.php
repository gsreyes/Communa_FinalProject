<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;

class PaymentController extends Controller
{
    // Display payment history
    public function index()
    {
        $user = Auth::user();

        if ($user->isBillingStaff()) {
            $payments = Payment::with(['user', 'bill'])
                ->latest()
                ->paginate(20);
        } else {
            $payments = $user->payments()
                ->with(['bill'])
                ->latest()
                ->paginate(20);
        }

        return view('payments.index', compact('payments'));
    }

    // Show payment form for a specific bill
    public function create(Bill $bill)
    {
        $this->authorize('view', $bill);

        if ($bill->status === 'Paid') {
            return redirect()->route('bills.show', $bill)
                ->with('info', 'This bill has already been paid.');
        }

        return view('payments.create', compact('bill'));
    }

    // Process payment through HitPay
    public function store(Request $request, Bill $bill)
    {
        $this->authorize('view', $bill);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $bill->amount,
        ]);

        try {
            // Create HitPay payment request
            $hitpayResponse = $this->createHitPayRequest($bill, $validated['amount']);

            if (!isset($hitpayResponse['url'])) {
                throw new \Exception('Failed to generate payment URL from HitPay');
            }

            // Store payment record with pending status
            $payment = Payment::create([
                'user_id' => Auth::id(),
                'bill_id' => $bill->id,
                'hitpay_transaction_id' => $hitpayResponse['id'] ?? '',
                'amount' => $validated['amount'],
                'status' => 'pending',
                'hitpay_response' => $hitpayResponse,
            ]);

            return redirect($hitpayResponse['url']);

        } catch (\Exception $e) {
            return redirect()->route('bills.show', $bill)
                ->with('error', 'Payment processing failed: ' . $e->getMessage());
        }
    }

    // HitPay callback webhook
    public function webhook(Request $request)
    {
        $payload = $request->all();

        // Verify HitPay signature
        if (!$this->verifyHitPaySignature($payload)) {
            return response('Unauthorized', 401);
        }

        $transactionId = $payload['id'] ?? null;
        $status = $payload['status'] ?? null;

        $payment = Payment::where('hitpay_transaction_id', $transactionId)->first();

        if (!$payment) {
            return response('Payment not found', 404);
        }

        if ($status === 'completed') {
            $payment->update([
                'status' => 'completed',
                'paid_at' => now(),
            ]);

            // Update bill status if full payment
            if ($payment->amount >= $payment->bill->amount) {
                $payment->bill->update([
                    'status' => 'Paid',
                    'paid_amount' => $payment->amount,
                    'paid_at' => now(),
                    'reference_number' => $transactionId,
                ]);
            }
        } elseif ($status === 'failed') {
            $payment->update([
                'status' => 'failed',
                'failure_reason' => $payload['reason'] ?? 'Unknown error',
            ]);
        }

        return response('OK', 200);
    }

    // Confirm payment after HitPay redirect
    public function confirm()
    {
        $transactionId = request('id');

        if (!$transactionId) {
            return redirect()->route('bills.index')
                ->with('error', 'Invalid payment reference');
        }

        $payment = Payment::where('hitpay_transaction_id', $transactionId)->first();

        if (!$payment) {
            return redirect()->route('bills.index')
                ->with('error', 'Payment record not found');
        }

        if ($payment->status === 'completed') {
            return redirect()->route('payments.show', $payment)
                ->with('success', 'Payment completed successfully!');
        }

        return redirect()->route('payments.show', $payment)
            ->with('info', 'Payment is being processed. Please check back soon.');
    }

    // Display a specific payment
    public function show(Payment $payment)
    {
        $this->authorize('view', $payment);

        $payment->load(['user', 'bill']);

        return view('payments.show', compact('payment'));
    }

    // Get payment statistics
    public function getStats()
    {
        $user = Auth::user();

        if ($user->isBillingStaff()) {
            return [
                'total_payments' => Payment::completed()->sum('amount'),
                'total_transactions' => Payment::completed()->count(),
                'pending' => Payment::pending()->count(),
                'failed' => Payment::failed()->count(),
            ];
        }

        return [
            'total_paid' => $user->payments()->completed()->sum('amount'),
            'pending' => $user->payments()->pending()->count(),
        ];
    }

    
    // Create HitPay payment request
    private function createHitPayRequest(Bill $bill, $amount)
    {
        $client = new Client();

        $payload = [
            'amount' => $amount * 100, // HitPay expects amount in cents
            'currency' => 'SGD',
            'email' => Auth::user()->email,
            'name' => Auth::user()->name,
            'phone' => Auth::user()->phone ?? '',
            'reference_number' => 'BILL-' . $bill->id . '-' . uniqid(),
            'redirect_url' => route('payments.confirm'),
            'webhook' => route('payments.webhook'),
            'method' => 'direct', // Allow all payment methods
        ];

        $response = $client->post('https://api.sandbox.hit-pay.com/v1/payment_request', [
            'headers' => [
                'Authorization' => 'Bearer ' . env('HITPAY_API_KEY'),
                'Content-Type' => 'application/json',
            ],
            'json' => $payload,
        ]);

        return json_decode($response->getBody(), true);
    }

    //Verify HitPay webhook signature
     
    private function verifyHitPaySignature($payload)
    {
        $signature = $payload['hmac'] ?? null;
        unset($payload['hmac']);

        $computed = hash_hmac(
            'sha256',
            json_encode($payload, JSON_UNESCAPED_SLASHES),
            env('HITPAY_API_SALT')
        );

        return hash_equals($signature, $computed);
    }
}
