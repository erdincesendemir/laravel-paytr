<?php

namespace ErdincEsendemir\PayTR\Contracts;

interface PayTRInterface
{
    public function initPayment(array $payload): array;

    public function generateToken(array $data): string;

    public function binCheck(string $binNumber): array;
}
