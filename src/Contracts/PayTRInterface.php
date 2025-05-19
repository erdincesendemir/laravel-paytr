<?php

namespace ErdincEsendemir\PayTR\Services;

use ErdincEsendemir\PayTR\Contracts\PayTRInterface;
use GuzzleHttp\Client;
use ErdincEsendemir\PayTR\Helpers\HashGenerator;

class PayTRManager implements PayTRInterface
{
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function generateToken(array $data): string
    {
        return HashGenerator::make($data, $this->config);
    }

    public function initPayment(array $payload): array
    {
        $payload['user_basket'] = base64_encode(json_encode(
            json_decode(base64_decode($payload['user_basket']), true),
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        ));

        $token = $this->generateToken($payload);

        $requestData = array_merge($payload, [
            'paytr_token' => $token,
            'merchant_id' => $this->config['merchant_id'],
            'test_mode' => $this->config['test_mode'] ? 1 : 0,
        ]);

        $client = new Client();

        $response = $client->post('https://www.paytr.com/odeme/api/get-token', [
            'form_params' => $requestData,
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ],
            'http_errors' => false
        ]);

        return json_decode($response->getBody()->getContents(), true);
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

        $client = new Client();

        $response = $client->post('https://www.paytr.com/odeme/api/bin-detail', [
            'form_params' => $data,
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ],
            'http_errors' => false
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
