# SIO-BE
Tugas Interview BackEnd Developer: Mini Sosial Media
A simple Social Media built with Laravel 10.0 framework, Laravel Passport 11.0 for authentication, and MySQL for database.

## Instruction
- Clone project.
- Install all the dependencies.
```
composer install
```
- Create a new database named "sosmed" on MySql.
- Run migration and seeder.
```
php artisan migrate --seed
```
- Install laravel passport.
```
php artisan passport:install
```
- Start the app.
```
php artisan serve
```
- Import postman collection or check api route by run the command below.
```
php artisan route:list
```
- Run features on postman.


NB: Admin account stored by default
```
email: superadmin@gmail.com
password: superadmin123
```