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