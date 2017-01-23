<?php
/**
 * Created by PhpStorm.
 * User: mannv
 * Date: 1/20/2017
 * Time: 9:19 AM
 */

namespace Kayac\AppotaPay;


use Illuminate\Support\ServiceProvider;

class AppotapayServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     * php artisan vendor:publish --provider="Kayac\AppotaPay\AppotapayServiceProvider" --tag=config
     * @return void
     */
    public function boot()
    {
        $configPath = __DIR__ . '/../config/appotapay.php';
        if (function_exists('config_path')) {
            $publishPath = config_path('appotapay.php');
        } else {
            $publishPath = base_path('config/appotapay.php');
        }
        $this->publishes([$configPath => $publishPath], 'config');
    }
}