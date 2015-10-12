# Simple configuration library

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