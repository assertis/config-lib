# Simple configuration library

## Design

Configuration library enables you to load configurations into application from different source, it give you way to
validate configuration on developer environment.
 
We are providing 5 base type of configurations.

* `Assertis\Configuration\Drivers\SourceDriver` : simple driver, you can set array with configuration into constructor.

* `Assertis\Configuration\Drivers\DatabaseDriver` : driver is using PDO connection to gain access to configuration from database.
This driver is returning `LazyConfiguration`.

* `Assertis\Configuration\Drivers\File\IniDriver` : configuration provided from `.ini` file
 
* `Assertis\Configuration\Drivers\File\JsonDriver` : configuration provided from `.json` file

* `Assertis\Configuration\Drivers\File\PhpDriver` : configuration provided from `.php` file. File should return array

* `Assertis\Configuration\Drivers\File\YmlDriver` : configuration provided from `.yml` file. File should return array

* `Assertis\Configuration\Drivers\File\XmlDriver` : configuration provided from `.xml` file. File should return array
with configuration. Ex:
```php
<?php
  return [
    'key' => 'value'
  ];
 ?>
````
We are returning configuration in 2 type of objects:

* `Assertis\Configuration\Collection\ConfigurationArray` is array type object with some extra methods. More detail below.  

* `Assertis\Configuration\Collection\LazyConfiguration` is simple array type object but with many limitations. Basically you can
only use `get` method, others will throw `Exception`. 

Library is also providing `Assertis\Configuration\ConfigurationFactory` to create configuration easily. 

## Extras

* Configuration will load "common" file if exists. File extension depends of driver type. If we are using
 `IniDriver` for test env then `common.ini` will be loaded if exists. 
 
* Environment is read from  `$_SERVER['ENV']` or `getenv('ENV')`

* When environment is default (dev) or test, configuration structure will be tested

* You can get value using path, for example: `rdis.db.user`

* When you are asking for key when value is array, this value will be changed to `Assertis\Configuration\Collection\ConfigurationArray`

* You can add easily extra drivers

## Installation

To install this library add the following to your `composer.json` file and run `composer update`:

```json
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/assertis/config-lib"
    }
  ],
  "require": {
    "assertis/config-lib": "dev-master"
  }
```

## Usage

To use this library add the following to your Silex application:

```php
$app->register(new ConfigurationProvider(), [
    'config.driver' => new IniDriver($app['dir.root'] . '/configs'),
    'config.validator' => $app['validator'],
    'config.validator.constraints' => null
]);
```

## Add new tenant to configuration

1) First of all your service should have something called tenant schema.
2) Put your regular tenant body into `config/schema/tenant.json`
e.g.
```json
{
  "database": {
    "order": {
      "dsn": "mysql:host=127.0.0.1;dbname=",
      "username": "root",
      "password": ""
    }
  },
  "nlc": "",
  "barcode_key": "",
  "issuer_id": ""
}
``` 

3) Now in your service you should be able to add tenant to specific config file
```bash
php vendor/assertis/config-lib/src/Scripts/AddTenant.php {tenantName} {configFileName}  [tenantSchemaFile]
```

If tenantSchemaFile is in different localization than `config/schema/tenant.json` please provide it as a last parameter.
E.g. of usage

```bash
php vendor/assertis/config-lib/src/Scripts/AddTenant.php hex-qa-b2b stage.json
```
