# Simple configuration library

Configuration library enables you to load configurations into application from different source also it give you way to
validate configuration on developer environment.
 
We are providing 5 base type of configurations.

* `Assertis\Configuration\Providers\SourceProvider` : the simple one provider, you can set configuration array into constructor.

* `Assertis\Configuration\Providers\DatabaseProvider` : provider is using PDO connection to gain access to configuration from database.
This provider is returning `LazyConfiguration`.

* `Assertis\Configuration\Providers\File\IniProvider` : configuration provided from `.ini` file
 
* `Assertis\Configuration\Providers\File\JsonProvider` : configuration provided from `.json` file

* `Assertis\Configuration\Providers\File\PhpProvider` : configuration provided from `.php` file. File should return array
with configuration. 

We are returning configuration in 2 type of objects:

* `Assertis\Configuration\ConfigurationArray` is array type object with some extra methods. More detail below.  

* `Assertis\Configuration\LazyConfiguration` is simple array type object but with many limitations. Basically you can
only use `get` method, others will throw `Exception`. 

Library is also providing `Assertis\Configuration\ConfigurationFactory` to create configuration easily. 