#Althingi

You will need PHP7 with x-debug.
You will need MySQL (>=5.7.15)
...and you will need composer. You can install composer how ever you want. I have installed it as a global bin, so every time you see `composer` in teh description below you will have to call it `php /path/to/composer.phar` if you do not have it in you $PATH


then get ZF2 skeleton app
composer create-project --stability="dev" zendframework/skeleton-application Althingi

now `cd` into the project's root directory
cd ./Althingi

then get the module
git clone https://github.com/fizk/Loggjafarthing.git ./module/Althingi


then get the depenencies
composer install -d ./module/Althingi 


remove unwanted modules
rm -rf ./module/Application

now, register the new module

./config/application.config.php

'modules' => array(
        'Althingi',
    ),

now point to the dependencies
./init_autoload.php

```php
if (file_exists('module/Althingi/vendor/autoload.php')) {
    $loader = include 'module/Althingi/vendor/autoload.php';
}

...

```

Now to configure the Database... go to ./config/autoload/global.php and set it like this:
```php
return [
    'db' => [
        'dns' => 'mysql:host=127.0.0.1;dbname=althingi',
        'user' => 'root',
        'password' => '',
    ]
];
```

...or what ever the name of your host and database it...

then shell into it and create the database
```sql
create database althingi;
```
you can get the latest snapshot of the db here http://107.170.87.126/dump.sql
and then you just have to pipe it in
```sh
$ mysql -u root althingi < ~/Desktop/dump.sql
```

if you are having problems with the GROUP BY clauses, add this to /etc/mysql/my.cnf
```
[mysqld]
sql_mode = STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION
```
and restart

now you can run the server to make sure everythinbg is working
php -S 0.0.0.0:3030 -t public

if it says something like this:

```
Parse error: parse error in ~/workspace/Althingi/module/Althingi/view/althingi/index/index.phtml on line 1
```

you have to go into that file and remove the XML decleration

```
<?xml version="1.0" encoding="UTF-8"?>
```


ZF2 application. Place in your module directory under then name **Althingi**. Remember to register in `config/application.config.php`

I usually override `init_autoloader.php` so I can use the `vendor` directory inside the module and not the one in the skeleton application.
Replace content in  `init_autoloader.php` with

```php

// Composer autoloading
if (file_exists('module/Althingi/vendor/autoload.php')) {
    $loader = include 'module/Althingi/vendor/autoload.php';
}

if (class_exists('Zend\Loader\AutoloaderFactory')) {
    return;
}

$zf2Path = false;

if (is_dir('vendor/ZF2/library')) {
    $zf2Path = 'vendor/ZF2/library';
} elseif (getenv('ZF2_PATH')) {      // Support for ZF2_PATH environment variable or git submodule
    $zf2Path = getenv('ZF2_PATH');
} elseif (get_cfg_var('zf2_path')) { // Support for zf2_path directive value
    $zf2Path = get_cfg_var('zf2_path');
}

if ($zf2Path) {
    if (isset($loader)) {
        $loader->add('Zend', $zf2Path);
        $loader->add('ZendXml', $zf2Path);
    } else {
        include $zf2Path . '/Zend/Loader/AutoloaderFactory.php';
        Zend\Loader\AutoloaderFactory::factory(array(
            'Zend\Loader\StandardAutoloader' => array(
                'autoregister_zf' => true
            )
        ));
    }
}

if (!class_exists('Zend\Loader\AutoloaderFactory')) {
    throw new RuntimeException('Unable to load ZF2. Run `php composer.phar install` or define a ZF2_PATH environment variable.');
}

```

This makes PHPUnits tests easier to manage.
