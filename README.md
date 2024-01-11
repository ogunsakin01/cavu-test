# CAVU API Challenge

## Installation

### Prerequisites
Make sure you have the following installed on your system before proceeding with the installation:

- PHP ^8.0
- Composer
- Laravel 10
- Laravel Valet (optional, for local development with Valet)

### General Installation Steps

#### 1. Clone the repository to your local machine
```shell
git clone https://github.com/ogunsakin01/cavu-test.git
```

#### 2. Change into the project directory
```shell
cd cavu-test
```

#### 3. Install project dependencies using Composer:
```shell
composer install
```

#### 4. Copy the .env.example file to create a new .env file
```shell
cp .env.example .env
```

#### 5. Generate the application key
```shell
php artisan key:generate
```

#### 6. Update the database connection settings in the .env file according to your database setup
```dotenv
DB_CONNECTION=****
DB_HOST=****
DB_PORT=****
DB_DATABASE=****
DB_USERNAME=****
DB_PASSWORD=****
```

#### 7. Run database migrations and seed the database
```shell
php artisan migrate --seed
```

#### 8. Clear the application cache
```shell
php artisan optimize:clear
```

## Starting the application
To start the application, you can use the built-in Laravel development server

```shell
php artisan serve
```
or you can directly use PHP built-in server
```shell
php -S locahost:8000 -t public/
```

or if you choose to use Laravel Valet, run the command below to have it available at [http://cavu.test](http://cavu.test)
```shell
valet link
```
or if you wish to test locally  with SSL, run the command below to have it available  at [https://cavu.test](https://cavu.test)

## Running Tests
To run the tests for the application, you can use either of the following commands
```shell
php artisan test
```
or use 
```shell
vendon/bin/phpunit
```

Make sure to run these commands from the root folder of the application. The tests will help ensure that the application functions as expected and that any changes made in the future do not introduce regressions.

## API Documentation
The API documentation can be found at []()
