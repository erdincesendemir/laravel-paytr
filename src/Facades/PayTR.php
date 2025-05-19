<?php

namespace ErdincEsendemir\PayTR\Facades;

use Illuminate\Support\Facades\Facade;

class PayTR extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'paytr';
    }
}
