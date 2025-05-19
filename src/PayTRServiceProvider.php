<?php

namespace ErdincEsendemir\PayTR;

use Illuminate\Support\ServiceProvider;
use ErdincEsendemir\PayTR\Services\PayTRManager;

class PayTRServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Config dosyasını birleştir
        $this->mergeConfigFrom(__DIR__.'/../config/paytr.php', 'paytr');

        // Servisi uygulamaya tanıt
        $this->app->singleton('paytr', function ($app) {
            return new PayTRManager(config('paytr'));
        });
        $this->app->bind(\ErdincEsendemir\PayTR\Contracts\PayTRInterface::class, function ($app) {
            return new PayTRManager(config('paytr'));
        });

    }

    public function boot()
    {
        // Config publish
        $this->publishes([
            __DIR__.'/../config/paytr.php' => config_path('paytr.php'),
        ], 'config');
    }
}
