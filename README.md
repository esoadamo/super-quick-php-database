# Super Quick PHP database

Just put one file on your web server and you are ready to go!

## What is this?

This script provides you with a simple database that you can use anywhere
by using its API. It's so simple that you do not even have to configure 
anything!

## When should I use this?

Anytime you want to setup a database with easy remote access
in literary no time and for free. (You can use any form of
free web hosting) 

## What type storage does it use?

Your database is stored in a external PHP file as base64 JSON.
The PHP file is protected from direct access, so nobody can just download
your whole database at once.

## Is this secure?

I would say so. Some level of basic security IS provided. Nobody can access
your database without knowing the name of the database and existing item 
in that database (if they use anything else, they are given a random generated
data that look like a ba64 file)

Furthermore, you can setup a key that is required to manipulate with 
your database.

## Can I use it as my production database?

NO! I specifically said that it provides just **some level of basic security**.

Plus, the type of storage this script uses is not event remotely effective,
so it is not recommended for storing big loads of data.

## How do I use it?

First, deploy the `db.php` script to your web server (let's say `http://example.com/db.php`).
And that's it. You have successfully deployed your database!

Now you can access your data by calling `GET http://example.com/db.php/databaseName/itemName`

Or set the using the `val` parameter: `GET http://example.com/db.php/databaseName/itemName?val=helloWorld`

Sweet, right?

### More configuration

If you want to configure your database, open the `db.php` file and edit the `CONFIG` array.

```php
$CONFIG = [
  "databaseFile" => "db.db.php", // data will be stored in this file
  "apiKey" => false, // set to false to diable API
  "accessKey" => false, // set to false to allow any key
  // when wrong key is provided or nonexisting item is requested, script will pretend to send existing data to prevent data stealing 
  "maskNonexistingKeys" => true  
];
```

### Remote API

If you have set up a API key in `CONFIG["apiKey"]` you can use following existing API:

- `GET http://example.com/db.php/api/backup` will return whole database as a base64 file
- `GET http://example.com/db.php/api/clear` will **DELETE** all data

### Can you give use a working example?

Sure. This [Python implementation](https://github.com/esoadamo/super-quick-php-database/blob/master/php_db.py) behaves just like normal dictionary, but it uses this PHP database as a backend.
