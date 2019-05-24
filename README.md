# php-framework

## What's this?

PHP web app framework accompanied by reverse proxy.  
They are already set up so that able to work on docker using `docker-compose up`  

## Usage

### Creating your app

1. Extend `App` class.
2. Create instance of your app class in `index.php`
3. `$app->run()`

### Adding routing

Your app class must have `registerRoutes()` method like this:

```php
    protected function registerRoutes()
    {
        return array(
            '/'
                => array('controller' => 'root'),
            '/account'
                => array('controller' => 'account'),
            '/account/signup'
                => array('controller' => 'account', 'action' => 'signup'),
            '/account/register'
                => array('controller' => 'account', 'action' => 'register',
                         'methods' => array('POST'))
        );
    }
```

It returns a dictionary to determine routing.  
Keys are obviously urls.  
values are dictionary to let the framework know which controller, action and http method to be used for the url.  

### Adding controller

1. Extend `Controller` class.
2. Give the class name end with "Controller"

```php
class AccountController extends Controller
```

### Adding action

Just add method for the controler class, but its name must end with "Action".

## Reference

https://www.amazon.co.jp/%E3%83%91%E3%83%BC%E3%83%95%E3%82%A7%E3%82%AF%E3%83%88PHP-PERFECT-3-%E5%B0%8F%E5%B7%9D-%E9%9B%84%E5%A4%A7/dp/4774144371/ref=sr_1_1?__mk_ja_JP=%E3%82%AB%E3%82%BF%E3%82%AB%E3%83%8A&keywords=%E3%83%91%E3%83%BC%E3%83%95%E3%82%A7%E3%82%AF%E3%83%88php&qid=1558679939&s=gateway&sr=8-1
