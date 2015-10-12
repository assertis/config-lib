# Simple configuration library

## Design

Configuration library enables you to load configurations into application from different source also it give you way to
validate configuration on developer environment.
 
We are providing 5 base type of configurations.

* `Assertis\Configuration\Drivers\SourceProvider` : simple provider, you can set array with configuration into constructor.

* `Assertis\Configuration\Drivers\DatabaseProvider` : provider is using PDO connection to gain access to configuration from database.
This provider is returning `LazyConfiguration`.

* `Assertis\Configuration\Drivers\File\IniProvider` : configuration provided from `.ini` file
 
* `Assertis\Configuration\Drivers\File\JsonProvider` : configuration provided from `.json` file

* `Assertis\Configuration\Drivers\File\PhpProvider` : configuration provided from `.php` file. File should return array
with configuration. Ex:
```php
<?php
  return [
    'key' => 'value'
  ];
 ?>

````
We are returning configuration in 2 type of objects:

* `Assertis\Configuration\ConfigurationArray` is array type object with some extra methods. More detail below.  

* `Assertis\Configuration\LazyConfiguration` is simple array type object but with many limitations. Basically you can
only use `get` method, others will throw `Exception`. 

Library is also providing `Assertis\Configuration\ConfigurationFactory` to create configuration easily. 

## Extras

* Configuration will load "common" file if exists. File extension depends of driver type. If we are using
 `IniDriver` for test env then `common.ini` will be loaded if exists. 
 
* Environment is read from  `$_SERVER['ENV']` or `getenv('ENV')`

* You can get value using path, for example: `rdis.db.user`

* When you are asking for key when value is array, this value will be changed to `Assertis\Configuration\ConfigurationArray`

* You can add easily extra drivers

## Installation

To install this library add the following to your `composer.json` file and run `composer update`:

```
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/assertis/config-lib"
    }
  ],
  "require": {
    "assertis/config-lib": "dev-master"
  },
```

## Usage

To use this library add the following to your Silex application:

```php
$app->register(new ConfigurationProvider(), [
    'config.driver' => new IniDriver($app['dir.root'] . '/configs')
]);

```
