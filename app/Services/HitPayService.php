<?php

class HitPayService
{
    private string $apiKey;
    private string $salt;
    private ?string $lastPaymentRequestId = null;

    public function __construct()
    {
        $this->apiKey = config('services.hitpay.api_key');
        $this->apiUrl = config('services.hitpay.sandbox', true) ? 'https://sandbox.hitpayapp.com/api/v1' : 'https://api.hitpayapp.com/api/v1';
    }

    public function createPaymentRequest(Order $order): ?string
    {
        if(empty($this->apiKey)) {
            Log::error('HitPay API key is not configured.');
            return null;
        }
        try {
            $http = Http::withHeaders([
                'X-BUSINESS-API-KEY' => $this->apiKey,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ]);

            if(config('services.hitpay.sandbox', true)) {
                $http = $http->withoutVerifying();
            }

            $payload = [
                'amount' => $order->amount,
                'currency' => 'SGD',
                'description' => "Payment for Order #{$order->id}",
                'reference_id' => (string)$order->id,
                'callback_url' => route('hitpay.webhook'),
            ];

            $webhookUrl = route('hitpay.webhook');
            if(!str_contains($webhookUrl, 'localhost') && !str_contains($webhookUrl, '127.0.0.1')) 
                {
                    $payload['webhook'] = $webhookUrl;
                }

                Log::info('Creating HitPay payment request with payload: ' . json_encode($payload));
                $response = $http->asForm()->post($this->apiUrl . '/payment-requests', $payload);
                Log::info('HitPay API response status: ' . $response->status());
                Log::info('HitPay API response body: ' . $response->body());

                if ($response->successful()) {
                    $data = $response->json();
                    $this->lastPaymentRequestId = $data['id'] ?? null;
                    $url = $data['url'] ?? null;
                    Log::info('HitPay payment request created with ID: ' . $this->lastPaymentRequestId);
                    Log::info('HitPay payment URL: ' . $url);
                    return $url;
                }

                Log::error('Failed to create HitPay payment request. Status: ' . $response->status() . ', Body: ' . $response->body());
                return null;

        } catch (\Exception $e) {
            Log::error('Error creating HitPay payment request: ' . $e->getMessage());
            return null;
        }
    }
}