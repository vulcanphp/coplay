# PHP Simple File System
PHP Simple File Management System Helper Class that can manage files and folders easily

## Installation

It's recommended that you use [Composer](https://getcomposer.org/) to install PHP Simple File System

```bash
$ composer require vulcanphp/filesystem
```

## Basic Usage with File

```php
<?php
// index.php

use VulcanPhp\FileSystem\File;

require_once __DIR__ . '/vendor/autoload.php';

// Create a file instance
$file = File::choose(__DIR__ .'/test.txt');

// or, use a helper function
$file = file_handler(__DIR__ .'/test.txt');

// get human format file size
var_dump($file->getBytes());

// get file extension
var_dump($file->getExt());

// get file url
var_dump($file->getUrl());

// move file to another directory
var_dump($file->move(__DIR__ .'/etc/test.txt'));

// remove a file
var_dump($file->remove());

// quick access to file methods
var_dump(File::exists(__DIR__ .'/test.txt'));
var_dump(File::bytes(__DIR__ .'/test.txt'));
var_dump(File::ext(__DIR__ .'/test.txt'));
var_dump(File::move(__DIR__ .'/test.txt', __DIR__ .'/etc/test.txt'));

// ...
```
### File Available Methods
all methods can be also access statically, and without get word EX: getBytes() is equal to bytes()
- getPath(): string
- is(): bool
- exists(): bool
- getMtime(): int|false
- getSize(): int|false
- getName(): string
- getDirName(): string
- getExt(): string
- getMimeType(): string
- getBytes(): string
- remove(): bool
- getContent(): string
- putContent(): bool
- copy(): bool
- rename(): bool
- move(): bool
- open()
- readFile()
- read()
- close()
- end()

## Basic Usage with Folder

```php
<?php
// index.php

use VulcanPhp\FileSystem\Folder;

require_once __DIR__ . '/vendor/autoload.php';

// Create a Folder instance
$folder = Folder::select(__DIR__ .'/test');

// or, use a helper function
$folder = folder_handler(__DIR__ .'/test');

// enter to sub folder
$folder->enter('etc');

// get all the files and folder
var_dump($folder->scan());

// remove files within folder
var_dump($folder->delete(['test.txt', 'test2.txt']));

// remove current folder
var_dump($folder->remove());

// quick access to Folder methods
Folder::check(__DIR__ .'/test');
Folder::chmod(__DIR__ .'/test', 0777);
Folder::delete(__DIR__ .'/test', ['text.txt']);
Folder::remove(__DIR__ .'/test');
var_dump(Folder::writable(__DIR__ .'/test'));
var_dump(Folder::scan(__DIR__ .'/test'));

// ...
```
### Folder Available Methods
- getPath(): string
- enter(): self
- back(): self
- is(): bool
- writable(): bool
- readable(): bool
- getFile()
- scan()
- create()
- chmod()
- check()
- remove()
- delete()

## Basic Usage with Storage Folder

```php
<?php
// index.php

require_once __DIR__ . '/vendor/autoload.php';

// Initialize Storage Folder
storage_init([
    'upload_extensions' => ['png', 'jpg', 'jpeg', 'gif', 'pdf'],
    'max_upload_size'   => 1048576 * 2, // 1048576 = 1 MB
    'upload_dir'        => 'uploads', // make it empty to upload parent folder
]);

// get storage directory uri
var_dump(storage_dir('test.txt'));

// get url from storage directory
var_dump(storage_url('test.txt'));

// download a file from storage 
storage()->download('test.txt');

// download a file with download speed metered
storage()->downloadRate('text.txt', 600); // 600 KB per Second

// download multiple files as a zip
storage()->downloadZip(['test.txt', 'test2.txt'], 'test.zip');

// upload files to storage
storage()->upload(
    // EX: $_FILES['input']
    'input',
    
    // strict: throw error when already exists
    // keep: will keep both files
    // override: will replace previous file
    'strict',
);

// upload file from url
storage()->uploadFromUrl('http://file-location.com');

// ALSO AVAILABLE ALL THE FOLDER METHODS
// enter to a sub folder
storage()->enter('test');

// get all the available files
var_dump(storage()->scan());

// delete files in this sub folder
storage()->delete(['test.txt']);

// back to prevues folder
storage()->back();

// ...
```

## Usage of Simple Image Handler
```php
<?php
// index.php

use VulcanPhp\FileSystem\Image;

require_once __DIR__ . '/vendor/autoload.php';

// create a image hanlder instance
$image = Image::choose(__DIR__ . '/test.jpg');

// or, use a helper function
$image = image_handler(__DIR__ . '/test.jpg');

// compress this image
$image->compress(60); // it will compress 60%

// compress and save to another files
$image->compress(60, __DIR__ . '/test-com.jpg');

// resize this image
// width = 520, height = 240
$image->resize(520, 240);

// resize this image and save another file
$image->resize(520, 240, __DIR__ . '/test-thumb.jpg');

// create multiple resizes images 
$image->bulkResize([520 => 240, 360 => 360, 64 => 64]);
// it will create 3 images
// test-520x240.jpg
// test-360x360.jpg
// test-64x64.jpg

// Rotate a Image
$image->rotate(180); // 180 degree

// ...
```