# Simply PHP dotenv

Simply Dotenv parser .env files to make environment variables stored in them accessible 
via getenv(), $_ENV and Env::get().

[![License](https://img.shields.io/github/license/laxity7/dotenv.svg)](https://github.com/laxity7/dotenv/blob/master/LICENSE)
[![Latest Stable Version](https://img.shields.io/packagist/v/laxity7/dotenv.svg)](https://packagist.org/packages/laxity7/dotenv)
[![Total Downloads](https://img.shields.io/packagist/dt/laxity7/dotenv.svg)](https://packagist.org/packages/laxity7/dotenv)

The class allows working with local variables from .env, as with global environment variables

## Install

Install via composer 

```shell
composer require laxity7/dotenv
```

Or you may add dependency manually in composer.json: `"laxity7/dotenv": "*"`

## How to use

The .env file is generally kept out of version control since it can contain 
sensitive API keys and passwords. A separate .env.example file is created with 
all the required environment variables defined except for the sensitive ones, 
which are either user-supplied for their own development environments or are 
communicated elsewhere to project collaborators. The project collaborators then 
independently copy the .env.example file to a local .env and ensure all the settings 
are correct for their local environment, filling in the secret keys or providing their 
own values when necessary. In this usage, the .env file should be added to the project's 
.gitignore file so that it will never be committed by collaborators. This usage ensures 
that no sensitive passwords or API keys will ever be in the version control history so 
there is less risk of a security breach, and production values will never have to be 
shared with all project collaborators.

Add your application configuration to a .env file in the root of your project or other place.

**Make sure the .env file is added to your .gitignore so it is not checked-in the code**.

See all possible use cases in the file "tests/test.env". 

Ok, after creating the .env file you can then load .env in your application with:

```php
// By default, existing variables will not be overwritten. Use the second parameter in load ()

$dotenv = new Laxity7\Dotenv();
$dotenv->load($_SERVER['DOCUMENT ROOT'] . '/.env', false);
// equivalent
$dotenv->load();

// To overwrite
$dotenv->load($_SERVER['DOCUMENT ROOT'] . '/.env', true);
```

> There will be no error if the file .env does not exist, only global variables will be taken

Now all of the defined variables are available in the $_ENV and $_SERVER super-globals.

> If your $_ENV array is mysteriously empty, but you still see the variables when calling 
> getenv() or in your phpinfo(), check your 
> http://us.php.net/manual/en/ini.core.php#ini.variables-order 
> ini setting to ensure it includes "E" in the string.

To get values through the getenv() function use the usePutEnv parameter (but not recommended).

You can also use the get method, which can return a default value (default `null`) if a key is missing.

```php
// in .env FOO=456
$dotenv = new Laxity7\Dotenv();
$dotenv->load($_SERVER['DOCUMENT_ROOT'] . '/.env');

$bar = $dotenv->get('BAR', 999); // $bar = 999
$foo = $dotenv->get('FOO', 123); // $foo = 456
$foo = $_ENV['FOO']; // also 456
```

For simplicity, you can create an env helper function

```php
function env(string $name, $default = null) {
    static $env = null;

    if ($env === null) {
        $env = new Laxity7\Dotenv();
        $env->load($_SERVER['DOCUMENT ROOT'] . '/.env');
    }

    return $env->get($name, $default);
}
```

or use helper class Env

```php
$dotenv = new Laxity7\Dotenv();
$dotenv->load($_SERVER['DOCUMENT ROOT'] . '/.env');
Laxity7\Env::load($dotenv);
// now you can use the get method
$foo = Env::get('FOO', 'default');
```

For even more simplicity, see this [gist](https://gist.github.com/laxity7/b253a62e02a6b7237fd4d907fce3fe0a).