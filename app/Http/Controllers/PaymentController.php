<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    // Display payment history
    public function index(Request $request)
    {
        $user = Auth::user();
        $status = $request->query('status');

        if ($user->isBillingStaff() || $user->isAdmin()) {
            $query = Payment::with(['user', 'bill']);
        } else {
            $query = $user->payments()->with(['bill']);
        }

        if (in_array($status, ['pending', 'completed', 'failed', 'refunded'], true)) {
            $query->where('status', $status);
        }

        $payments = $query->latest()
            ->paginate(20)
            ->withQueryString();

        return view('payments.index', compact('payments', 'status'));
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
            'amount' => 'required|numeric|min:0.01|max:'.($bill->amount - ($bill->paid_amount ?? 0)),
        ]);

        try {
            // Create HitPay payment request
            $hitpayResponse = $this->createHitPayRequest($bill, $validated['amount']);

            if (! isset($hitpayResponse['url'])) {
                throw new \Exception('Failed to generate payment URL from HitPay');
            }

            Payment::create([
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
                ->with('error', 'Payment processing failed: '.$e->getMessage());
        }
    }

    // HitPay callback webhook
    public function webhook(Request $request)
    {
        $payload = $request->all();
        Log::info('HitPay webhook received.', $payload);

        // Verify HitPay signature
        if (config('services.hitpay.verify_webhook', true) && ! $this->verifyHitPaySignature($payload)) {
            Log::warning('HitPay webhook signature verification failed.', $payload);

            return response('Unauthorized', 401);
        }

        $transactionId = $payload['id'] ?? null;
        $status = strtolower((string) ($payload['status'] ?? ''));

        $payment = Payment::where('hitpay_transaction_id', $transactionId)->first();

        if (! $payment) {
            return response('Payment not found', 404);
        }

        // Skip if already processed
        if ($payment->status === 'completed') {
            return response('OK', 200);
        }

        if (in_array($status, ['completed', 'paid', 'succeeded', 'successful'], true)) {
            $this->completePayment($payment, $transactionId);
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

        if (! $transactionId) {
            return redirect()->route('bills.index')
                ->with('error', 'Invalid payment reference');
        }

        $payment = Payment::where('hitpay_transaction_id', $transactionId)->first();

        if (! $payment) {
            return redirect()->route('bills.index')
                ->with('error', 'Payment record not found');
        }

        if ($payment->status === 'completed') {
            return redirect()->route('payments.show', $payment)
                ->with('success', 'Payment completed successfully!');
        }

        $status = strtolower((string) request('status'));

        if (in_array($status, ['completed', 'paid', 'succeeded', 'successful'], true)) {
            $this->completePayment($payment, $transactionId);

            return redirect()->route('payments.show', $payment->fresh())
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

        if ($user->isBillingStaff() || $user->isAdmin()) {
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
        if (blank(config('services.hitpay.api_key'))) {
            throw new \Exception('HitPay API key is not configured.');
        }

        $payload = [
            'amount' => number_format((float) $amount, 2, '.', ''),
            'currency' => config('services.hitpay.currency', 'PHP'),
            'email' => Auth::user()->email,
            'name' => Auth::user()->name,
            'reference_number' => 'BILL-'.$bill->id.'-'.uniqid(),
            'redirect_url' => $this->publicUrl('/payments/confirm'),
            'webhook' => $this->publicUrl('/webhook/hitpay'),
        ];

        $apiUrl = rtrim((string) config('services.hitpay.api_url'), '/');

        $request = Http::withHeaders([
            'X-BUSINESS-API-KEY' => (string) config('services.hitpay.api_key'),
        ])
            ->acceptJson()
            ->withOptions([
                'proxy' => '',
                'curl' => [
                    CURLOPT_PROXY => '',
                    CURLOPT_NOPROXY => '*',
                ],
            ])
            ->asForm();

        if (! config('services.hitpay.verify_ssl', true)) {
            $request = $request->withoutVerifying();
        }

        $response = $request->post($apiUrl.'/payment-requests', $payload);

        if ($response->failed()) {
            Log::error('HitPay payment request failed.', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \Exception($response->json('message') ?? 'HitPay request failed. Please check the payment gateway settings.');
        }

        return $response->json();
    }

    private function publicUrl(string $path): string
    {
        return rtrim((string) config('app.url'), '/').'/'.ltrim($path, '/');
    }

    private function completePayment(Payment $payment, ?string $transactionId = null): void
    {
        $payment->update([
            'status' => 'completed',
            'paid_at' => $payment->paid_at ?? now(),
        ]);

        $bill = $payment->bill;

        if (! $bill) {
            return;
        }

        $paidAmount = $bill->payments()->completed()->sum('amount');

        $bill->update([
            'status' => $paidAmount >= $bill->amount ? 'Paid' : 'Unpaid',
            'paid_amount' => $paidAmount,
            'paid_at' => $paidAmount >= $bill->amount ? ($bill->paid_at ?? now()) : null,
            'reference_number' => $paidAmount >= $bill->amount ? $transactionId : $bill->reference_number,
        ]);
    }

    // Verify HitPay webhook signature

    private function verifyHitPaySignature($payload)
    {
        $signature = $payload['hmac'] ?? null;
        unset($payload['hmac']);

        $computed = hash_hmac(
            'sha256',
            json_encode($payload, JSON_UNESCAPED_SLASHES),
            (string) config('services.hitpay.api_salt')
        );

        return hash_equals($signature, $computed);
    }
}
