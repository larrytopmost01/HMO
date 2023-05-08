# Wellness Patient Backend

[![codecov](https://codecov.io/gh/HazonTechnologies/wellness_patient_backend/branch/main/graph/badge.svg?token=9WB2OVL4E3)](https://codecov.io/gh/HazonTechnologies/wellness_patient_backend)
![Github Actions](https://github.com/HazonTechnologies/wellness_patient_backend/actions/workflows/test.yml/badge.svg)

## Brief Description

Backend repo for the Wellness Super App.

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development purposes.

### Prerequisites

System requirements for this project to work includes:

-   Git
-   PHP(v7.0 or higher)
-   XDebug (for code coverage)
-   Composer
-   Postgres (optional)
-   Docker (optional)
-   Any IDE of your choice (VS Code is recommended)

### Running the project

To run the project on your local machine, follow the steps below:

#### Manual Setup

-   Clone this repo and navigate to the project folder
-   Create a **.env** file in the root directory of the project according to the variables contained in the **.env.example** file and edit as appropriate with your defined credentials (database, redis, Sendgrid etc etc)
-   Run the following command to generate JWT secret key

```bash
php artisan jwt:secret
```

-   Install all dependencies by running the following command:

```bash
composer install
```

-   Create your database in your local Postgres server and run all migrations with the following command:

```bash
php artisan migrate
```

-   To start the server, run the following command

```bash
composer start
```

You should see the server up and running on the link: `http://localhost:8000`

#### Automatic Setup (Docker)

If you want your setup to be fully automated by Docker, follow the steps below:

-   Clone this repo and navigate to the project folder
-   Run the following command:

```bash
docker-compose up
```

## Unit Testing

For automated testing, run the following command:

```bash
composer test:coverage
```

This will run the tests and generate a corresponding code coverage report located in the `coverage_html` folder which can be viewed on the browser.

## Helpful Commands

The following commands will be helpful:

### Migrations & Seeding

-   **Migrate without seeding:** `php artisan migrate`
-   **Migrate with seeding:** `php artisan migrate --seed`
-   **Refresh migration without seeding:** `php artisan migrate:refresh`
-   **Refresh migration with seeding:** `php artisan migrate:refresh --seed`
-   **Seed a single seed file:** `php artisan db:seed seed_file_name`

### File Creation

-   **Controller:** `php artisan make:controller file_name` // the file_name should end with 'Controller'
-   **Model:** `php artisan make:model file_name` // the file_name should end with 'Model'
-   **Migration file:** `php artisan make:migration file_name`
-   **Seeder file:** `php artisan make:migration file_name` // the file_name should end with 'Seeder'
-   **Feature Test:** `php artisan make:test file_name` // the file_name should end with 'Test'
-   **Unit Test:** `php artisan make:test file_name --unit` // the file_name should end with 'Test'

## Helpful links

The following links will be helpful:

-   [Laravel 8.x Documentation](https://laravel.com/docs/8.x)
