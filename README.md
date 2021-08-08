# Avast Interview Task

### Task
- From attached XML file (resources/config.xml), please export data to Redis,
- key "subdomains" will contain JSON with all subdomains (e.g. ["http://secureline.tools.avast.com", "http://gf.tools.avast.com"]),
- keys "cookie:%NAME%:%HOST%" will contain values of cookie elements (e.g. key "cookie:dlp-avast:amazon" will contain string "mmm_amz_dlp_777_ppc_m"),
- use docker-compose for setting up cloud environment (PHP and Redis needs to have their own containers),
- please use PHPUnit for tests.
- to run the app please use this command: export.sh /path/to/xml
- if "-v" argument is present in command it should print all keys saved to Redis (export.sh -v /path/to/xml)

## Solution

### Command
As you may have noticed - this was pretty robust one. 
Not that it had to be robust, but I wanted to show you various design patterns, code expandability predictions, etc.
I will explain how the program works in the following lines...

#### Entry point
The entry point for our application is (based on the task) the `export.sh` file.
This file is added to our `php` service image and mounted to `/use/local/bin` by docker-compose,
which allows us to execute it without prepending `./` (looks like it was a desired behaviour in task specification).

The `export.sh` file acts like the bridge between the CLI and Symfony's command ecosystem as it
executes only the following line `cd /var/www/symfony && php bin/console config:load <args>`.

To run export.sh use:
```bash
# Access php container: docker-compose exec php /bin/sh
export.sh resources/config.xml
```

#### Configuration load command
The `config:load` command accepts one argument, which is a path to the `.xml` file with
configuration. When the path is not provided, error is presented to the user.

Command also accepts verbosity level options like `-q`, `-v`, `-vv` and `-vvv`.
All options are presented by default via Symfony's command line (I haven't created them myself)
and for the purpose of this program, the following applies:

- no verbosity option or `-q`: results is no output
- `-v`, `-vv` and `-vvv`: outputs all the keys saved to cache

The purpose of this command is to extract the passed argument (filepath)
and pass it to `ConfigService::loadFromFile` method.

#### ConfigService
Currently, contains only the `loadFromFile` method, but as the application
might grow we can add other methods like `loadFromString`, etc.

Method `loadFromFile` returns `iterable` with saved keys.

In our case, `ConfigService` runs the `ConfigParserInterface` to parse the
passed configuration and then saves the parsed configuration to the cache via
`CacheInterface`.

### Parsing

#### ConfigParserInterface
Not that we're creating a reusable library, but using interfaces is always a good practice.
This interface exposes two methods: `parseFile` and `parseString`.

#### ConfigParser (impl. ConfigParserInterface)
The `parseFile` method calls the `FileService::read` to get the contents of the file.
When file contents are obtained, the `parseString` method is called, which iterates over
available format parsers and uses the first supported one. This enables us to easily add new format parsers
in the future (e.g. `JsonParser`). Format parsers are added to the array in `services.yaml`.

#### FileService
Simple service used for obtaining the file contents from existing files.

#### FormatParserInterface
Interface used for format parsers. Contains only two methods: `supports` and `parse`.

#### XMLFormatParser (impl. FormatParserInterface)
Method `supports` tries to parse the contents of the XML to determine if the XML is correct and also
validates the config via `ConfigValidatorInterface`.

Method `parse` traverses the parsed DOM from the entry point (`config` element).
Whenever an element is found, similar design pattern as with format parsers is repeated.
Function iterates over available node parsers until the supported one is found and then parse method is called.
Each node parser returns and array of parsed results, which are then added to the `ConfigDto`.

#### XMLNodeParserInterface and specific XMLNodeParsers
No need to explain this, as the previous point went through the core of it.

#### ConfigValidatorInterface
Exposes method `validate` which validates the provided content.

#### AbstractConfigValidator (impl. ConfigValidatorInterface)
Loads configuration schema (or mapping, as some would say) and contains helper methods to validate it.
Configuration schema is present in `config/validation/config_schema.yaml`. It enables us to quickly add supported
properties and, hopefully, still have the correct validation for them.

#### XMLConfigValidator (ext. AbstractConfigValidator)
Traverses the configuration schema and runs the callback for each schema element to check against the DOM.

### Cache
As mention in the task, Redis is used as the main (and only) caching mechanism.
For best results when invalidating the cache, tags are enabled.

#### CacheableCollection

For easier cache manipulation and no "hard-coding" of what should be stored, the `CacheableCollection` was created.
The cacheable collection groups multiple `CacheableCollectionItem` object and sets properties
as expiration and tags globally for each item inside.

Object `CacheableCollectionItem` contains value and the key under it's going to be stored.

Of course, both collection and item operates with interfaces, which can be used for your own implementation
(as in the Dtos below).

#### Dtos
`ConfigDto` contains parsed configuration items and implements `CacheableCollectionIterface`. This enables us
to save the whole config at once. Both normal and `CacheableCollectionItemIterface` items can be added. Whenever
the normal key-value is added to the configuration it gets converted to `CacheableCollectionItem` automatically.

While parsing the configuration, `SubdomainsDto` and `CookieDto` instances are created
respectively. Both Dtos implement `CacheableCollectionItemIterface`.
There should only be one `CacheableCollectionItemIterface` item per "key in cache".

#### App\Cache\CacheInterface

In order to work with `CacheableCollection` we can use `App\Cache\CacheInterface`, which is automatically
configured to auto-wire custom `RedisCacheAdapter`. The interface currently exposes only `saveCollection` method.

### Other

#### Enum
Native enums are not yet in the PHP v8.0 and will be introduced in v8.1. Fortunately,
there's an alternative way to store enum types, which is setting up the public constants
in a public classes.

#### Exception
Custom Exception objects to correctly handle errors in the application.


## Testing
For now, only unit tests were created for the application.
Unit test are grouped in their own test suite and can be executed via:
```bash
# Access php container: docker-compose exec php /bin/sh
php vendor/bin/phpunit --testsuite Unit
```
