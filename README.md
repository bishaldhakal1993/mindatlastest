### Please follow the steps to setup this application in your local.

### Requirements.

- PHP 8
- Composer 2.3.5
- Mysql 5.7

#### This is a simple solution for mindAtlas test so .env files or config are not included. You can add your database credentials in app/Service/Database.php

### Steps to setup
 - After having all those requirements above up and running, clone this application somewhere you are easy.
 - Once cloned, Run `composer install`
 - After successful installation, Run `php -S localhost:8888` to up your server. You can use other port if you like.
 - One more step before opening the page, make sure you have created a db and use the db name in the app/Service/Database.php.
 - Hurray, now you can run the application.

 - You may see a button `Migrate and Seed` in the begining. Click it to migrate the tables and seeders. It is just one time thing.