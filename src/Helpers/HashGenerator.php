<?php

namespace ErdincEsendemir\PayTR\Helpers;

class HashGenerator
{
    public static function make(array $data, array $config): string
    {
        $hashStr =
            $config['merchant_id'] .
            $data['user_ip'] .
            $data['merchant_oid'] .
            $data['email'] .
            $data['payment_amount'] .
            $data['payment_type'] .
            $data['installment'] .
            $data['currency'] .
            $data['no_installment'] .
            $data['max_installment'] .
            $data['user_name'] .
            $data['user_address'] .
            $data['user_phone'] .
            $data['merchant_ok_url'] .
            $data['merchant_fail_url'] .
            $data['timeout_limit'] .
            $data['debug_on'] .
            $data['language'] .
            $data['user_basket'] .
            $config['merchant_salt'];

        return base64_encode(hash_hmac('sha256', $hashStr, $config['merchant_key'], true));
    }

    public static function verifyCallback(array $postData, array $config): bool
    {
        $expected = base64_encode(
            hash_hmac(
                'sha256',
                $postData['merchant_oid'] . $config['merchant_salt'] . $postData['status'] . $postData['total_amount'],
                $config['merchant_key'],
                true
            )
        );

        return $expected === $postData['hash'];
    }
}