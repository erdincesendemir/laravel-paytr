<?php

namespace ErdincEsendemir\PayTR\Services;

use ErdincEsendemir\PayTR\Contracts\PayTRInterface;
use Illuminate\Support\Facades\Http;
use ErdincEsendemir\PayTR\Helpers\HashGenerator;

class PayTRManager implements PayTRInterface
{
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Generate token for payment request.
     */
    public function generateToken(array $data): string
    {
        return HashGenerator::make($data, $this->config);
    }

    /**
     * Send payment initialization request to PayTR.
     */
    public function initPayment(array $payload): array
    {
        $token = $this->generateToken($payload);

        $requestData = array_merge($payload, [
            'paytr_token' => $token,
            'merchant_id' => $this->config['merchant_id'],
            'test_mode' => $this->config['test_mode'] ? 1 : 0,
        ]);

        $response = Http::asForm()->post('https://www.paytr.com/odeme/api/get-token', $requestData);

        if ($response->failed()) {
            throw new \Exception('PayTR API error: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Verify callback response from PayTR.
     */
    public function verifyCallback(array $postData): bool
    {
        $expectedHash = base64_encode(
            hash_hmac(
                'sha256',
                $postData['merchant_oid'] .
                $this->config['merchant_salt'] .
                $postData['status'] .
                $postData['total_amount'],
                $this->config['merchant_key'],
                true
            )
        );

        return isset($postData['hash']) && $postData['hash'] === $expectedHash;
    }
    public function binCheck(string $binNumber): array
    {
        $tokenStr = $this->config['merchant_id'] . $binNumber . $this->config['merchant_salt'];

        $paytrToken = base64_encode(
            hash_hmac('sha256', $tokenStr, $this->config['merchant_key'], true)
        );

        $data = [
            'merchant_id' => $this->config['merchant_id'],
            'bin_number' => $binNumber,
            'paytr_token' => $paytrToken,
        ];

        $response = Http::asForm()->post('https://www.paytr.com/odeme/api/bin-detail', $data);

        if ($response->failed()) {
            throw new \Exception('PayTR binCheck failed: ' . $response->body());
        }

        return $response->json();
    }

}
