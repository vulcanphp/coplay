# Simple DB
Simple DB is a lightweight and easy SQL Query Builder with many common features in PHP Database Management

## Installation

It's recommended that you use [Composer](https://getcomposer.org/) to install Simple DB

```bash
$ composer require vulcanphp/simpledb
```

## Create or Initialize Database

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

// initialize database
db_init([
    'driver' => 'mysql',
    'charset' => 'utf8mb4',
    'collate' => 'utf8mb4_unicode_ci',
    'name' => 'test',
    'host' => 'localhost',
    'port' => '3306',
    'user' => 'root',
    'password' => '',
]);

// or, sqlite connection
db_init([
    'driver' => 'sqlite',
    'file' => __DIR__ . '/test.db',
]);

// create non-global database
// only for external usage
$db1 = db_create([
    // .. db1 config
]);

$db2 = db_create([
    // .. db2 config
]);

var_dump($db1, $db2);

// ...
```
Available Methods of Database
- getPdo()
- prepare()
- last_id()
- ping()
- resetPdo()
- config()
- beginTransaction()
- commit()
- rollBack()
- getHookHandler()
- .. (and all PDO methods)

## Fetch Data Using Query Builder
```php
<?php

$query = qb_table('users');

// fetch all record from users
var_dump($query->result());

// fetch first or last record from users
var_dump($query->first(), $query->last());

// get latest records from users
var_dump($query->latest());

// fetch record with condition
var_dump(
    $query->where(['id' => [1, 2, 3, 4]])
        ->result();
);

// select some specific fields from users in
var_dump(
    $query->select(['name', 'email', 'role'])
        ->fetch(\PDO::FETCH_ASSOC)
        ->result();
);

// select records with joining tables
var_dump(
    $query->select('p.name, p.email, um.token')
        ->join('usermeta as um', 'um.user_id = p.id')
        ->result();
);

// ...
```
## Available Methods to Fetch Record
- select()
- where()
- whereIn()
- orWhere()
- andWhere()
- join()
- leftJoin()
- rightJoin()
- crossJoin()
- order()
- orderAsc()
- orderDesc()
- group()
- having()
- limit()
- fetch()
- first()
- last()
- latest()
- result()
- paginate()

## Insert Record Using Query Builder
```php
<?php

$query = qb_table('users');

// insert single row into users table
$last_id = $query->insert([
    'name' => 'John Doe',
    'email' => 'john@mail.com',
    'role' => 'admin'
]);

// insert multiple rows into users table
$query->insert([
    [
        'name' => 'John Doe 1',
        'email' => 'john1@mail.com',
        'role' => 'admin'
    ],
    [
        'name' => 'John Doe 2',
        'email' => 'john2@mail.com',
        'role' => 'editor'
    ],
    [...]
]);

// ...
```

## Update Record Using Query Builder
```php
<?php

$query = qb_table('users');

// update users record
$status = $query->update([
    'name' => 'Mr. John',
    'email' => 'mr.john@mail.com',
], ['id' => 1]);

// or 
$status = $query
    ->where(['id' => 1])
    ->update([
        'name' => 'Mr. John',
        'email' => 'mr.john@mail.com',
    ]);

// ...
```

## Delete Record Using Query Builder
```php
<?php

$query = qb_table('users');

$status = $query->delete(['id' => [1, 2]]);

// or

$status = $query
    ->whereIn('id', [1, 2])
    // or
    ->where(['id' => [1, 2]])
    // or
    ->where('id IN(1, 2)')

    // delete records
    ->delete();

// ...
```


## Basic Usage of Model

Example User Model:
```php
<?php

use VulcanPhp\SimpleDb\Model;

class User extends Model
{
    public static function tableName(): string
    {
        return 'users';
    }

    public static function primaryKey(): string
    {
        return 'id';
    }

    public static function fillable(): array
    {
        return ['name', 'email', 'role'];
    }
}
```

Create a New User Using Model
```php
<?php

// create a new user using model
$user = new User; 

// input all model fillable data from http request automatically
$user->input(); 

// or, set model data manually
$user->load(['name' => 'Jhon', 'email' => 'jhon@mail.com', 'role' => 'admin']);

// save user
$user->save();

// alt, method to create user
User::create([
    // single or multiple rows
]); 

// ..

```

Edit or Update User Using Model
```php
<?php

// get user record
$user = User::find(['id' => 1]); 

// print user record
var_dump($user);

// edit user data and update

$user->name = 'John Doe';
$user->role = 'editor';

// then, save user
$user->save();

// alt, method to update user
User::put(['name' => 'Jhon Doe'], ['id' => 1]);

// ..

```

Remove User Using Model
```php
<?php

// get user record
$user = User::find(['id' => 1]); 

// print user record
var_dump($user);

// then, save user
$user->remove();

// alt, method to remove user
User::erase(['id' => 1]);

// ..

```

Fetch User Using Model
```php
<?php

// fetch user with admin role
var_dump(
    User::where(['role' => 'admin'])
        ->result()
);

// get total records of user
var_dump(User::total([
    // condition
    'role' => 'admin'
    // or empty, without any condition
    // and it will return total number of users from database
]));

// find a single user with id 1
var_dump(User::find(['id' => 1]));

// fetch users with joining usermeta model
var_dump(
    User::select('p.name, p.email, t1.token')
        ->join(Usermeta::class)
        ->result();
);

// use query builder from Model
var_dump(
    User::query()
        ->select('*')
        ->where(['role' => ['admin', 'editor']])
        ->limit(15)
        ->result()
);

// fetch all usermeta with simple prm
$user = User::find(1);
var_dump(
    $user->hasMany(Usermeta::class);
);

// ..

```
Model Available Methods:
- ::query()
- ::put()
- ::create()
- ::select()
- ::where()
- ::all()
- ::find()
- ::findOrFail()
- ::total()
- ::paginate()
- ::erase()
- ::clearData()
- ->save()
- ->remove()
- ->hasOne()
- ->hasMany()
- ->belongsTo()
- ->belongsToMany()

Usage of Paginator
```php
<?php

$paginator = qb_table('users')
    ->paginate(10);

// or 
$paginator = User::paginate(10);

// get data from paginator
var_dump($paginator->getData());

// get links from paginate
echo $paginator->getLinks();

// ..

```
Paginator Available Methods:
- hasData()
- hasLinks()
- getData()
- getLinks()
- getPages()
- getOffset()
- getTotal()
- getLimit()
- getKeyword()
- setTotal()
- setLimit()
- setKeyword()
- reset()
- setEntity()
- setStyle()

set an external database to query builder

```php
<?php

$db1 = db_create([..]);
$query = qb_table('users')
        ->setDatabase($db1);

```

Usage of Database Hooks
```php
<?php

db_hooks()
    // add a new method to query builder
    ->fallback('query', 'getAll', fn($query) => $query->result())

    // filter resoles data
    ->filter('result', fn($data) => collect($data))

    // example usage of when made any changed in database
    ->action('changed', fn($table, $key, $action) => cache($table)->clear());

```
Available Hooks in Database, Query Builder, and Model:
- fallback(): database, query, model, model_static
- filter: pdo()
- filter: prepare()
- filter: dsn()
- filter: pdo_options()
- filter: insert_data()
- filter: insert()
- filter: delete()
- filter: update_data()
- filter: update()
- filter: select()
- filter: result()
- filter: paginator()
- filter: fillable_data()
- action: transaction()
- action: commit()
- action: rollback()
- action: prepare()
- action: last_id()
- action: ping()
- action: before_pdo()
- action: after_pdo()
- action: changed()
- action: insert()
- action: insert()
- action: deleted()
- action: inserted()
- action: delete()
- action: update()
- action: updated()
- action: select()
- action: selected()

## All Helper Functions of Simple DB
- database_init() / db_init()
- database_create() / db_create()
- database_hooks() / db_hooks()
- database() / db()
- qb_table() / qb()
- pdo()
- reset_pdo()
- prepare()
- is_pdo_driver()
- is_sqlite()
- is_mysql()
- paginator()

## Direct Usage of Simple DB
```php
<?php

use VulcanPhp\SimpleDb\Query;
use VulcanPhp\SimpleDb\Database;

// initialize database or set some hooks
Database::init([
    'driver' => 'sqlite',
    'file' => __DIR__ . '/test.db'
])
    ->getHookHandler()
    // add a custom method to query builder
    ->fallback('query', 'getAll', fn(Query $query) => $query->result())
    // filter records after getting
    ->filter('result', fn(array $data) => collect($data));

// use of Query Builder
$query = Query::table('users');

// fetch all record
var_dump(
    $query
        ->where(['role' => ['admin', 'editor']])
        ->getAll()
);

// create database instances
$db1 = Database::create([
    // config for db1
]);

$query->setDatabase($db1);

// $query .. whatever

```