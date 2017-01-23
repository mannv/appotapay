# Integrate appotapay to lumen or laravel

## Installation

1. Run 
    ```
    composer require kayac/appotapay
    ```
    
2. Add service provider into **config/app.php** file.

For laravel: config/app.php
```
Kayac\AppotaPay\AppotapayServiceProvider::class
```
For lumen: bootstrap/app.php
```
$app->register(\Kayac\AppotaPay\AppotapayServiceProvider::class);
$app->configure('appotapay');
```

- add config to .env
```
#appota pay config
APPOTA_VERSION=v1
APPOTA_LANG=vi
APPOTA_SECRET_KEY=kBMSHd9gCOF6o8qi
APPOTA_API_KEY=A180326-1CLONJ-F8630E75A39ACE88
APPOTA_APP_URL=https://api.appotapay.com/
APPOTA_SANDBOX=true
```    

3 . publish appotapay.php to config folder
```
php artisan vendor:publish --provider="Kayac\AppotaPay\AppotapayServiceProvider" --tag=config
```

or copy file /vendor/kayac/appotapay/config/appotapay.php to config folder


##Author
Hà Anh Mận

##Document
Document appotapay

https://appotapay.com/Docs