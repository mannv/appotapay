# Integrate appotapay to lumen or laravel

## Installation

1. Run 
    ```
    composer require kayac/appotapay
    ```
    
2. Add service provider into **config/app.php** file.
    ```php
    Kayac\AppotaPay\AppotapayServiceProvider::class
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

3. Run **composer update**

4. publish appotapay.php to config folder
```
php artisan vendor:publish --provider="Kayac\AppotaPay\AppotapayServiceProvider" --tag=config
```

##Author
Hà Anh Mận

##Document
Document appotapay
https://appotapay.com/Docs