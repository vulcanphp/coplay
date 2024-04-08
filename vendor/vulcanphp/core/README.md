# VulcanPhp Core
VulcanPhp Micro MVC Framework Core functionalities

## Installation

You need to use [Composer](https://getcomposer.org/) to install VulcanPhp Core

```bash
$ composer require vulcanphp/core
```

## Core Available Libraries
- Auth (Basic Authentication)
- Console (vulcan cli)
- Crypto (Hashing)
- Database Helper (Schema, Migration, Seeder)
- Foundation (Application, Controller, Kernel)

## Core Available Helpers
- Arr
- Str
- Time
- Bucket
- Collection
- Session
- Cookie
- Inflect
- Mail
- Mixer
- PrettyDateTime
- Vite

## Available Functions
- app()
- root_dir()
- bucket()
- abort()
- config()
- session()
- cookie()
- home_url()
- resource_url()
- resource_dir()
- collect()
- router()
- response()
- request()
- url()
- input()
- redirect()
- csrf_token()
- csrf()
- method()
- password()
- dump()
- dd()
- is_dev()
- is_url()
- like_match()
- encode_string()
- decode_string()
- mb_unserialize()
- __is_int()
- mixer()
- __lang()
- vite()
- vite_view()

### Required VulcanPhp Packages
- vulcanphp/inputmaster
- vulcanphp/easycurl
- vulcanphp/fastcache
- vulcanphp/filesystem
- vulcanphp/phprouter
- vulcanphp/prettyerror
- vulcanphp/simpledb
- vulcanphp/sweetview
- vulcanphp/translator