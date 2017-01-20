<?php
/**
 * Created by PhpStorm.
 * User: mannv
 * Date: 1/20/2017
 * Time: 9:20 AM
 */
return [
    'version' => env('APPOTA_VERSION', 'v1'),
    'lang' => env('APPOTA_LANG', 'vi'),
    'secret_key' => env('APPOTA_SECRET_KEY', ''),
    'api_key' => env('APPOTA_API_KEY', ''),
    'app_url' => env('APPOTA_APP_URL', ''),
    'sandbox' => env('APPOTA_SANDBOX', false),
];