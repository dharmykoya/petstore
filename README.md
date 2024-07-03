# PetStore API
Petstore api for buckhill assessment

## Introduction

The project is an ecommerce website for purchasing different kinds of products online.

## Features

- User can sign up
- User can log in
- User can log out
- User can view all their orders
- User can view profile
- User can reset password
- User can edit their profile
- User can delete the account
- Admin can create another admin
- Admin can log in
- Admin can log out
- Admin can view all user
- Admin can edit use account
- Admin can delete user account
- Admin can manage the category resource
- Admin can manage the order statuses resource

## Prerequisites

Before you begin, ensure you have met the following requirements:

- PHP >= 8.0
- Composer
- Laravel >= 8.0
- MySQL

## Installation

Follow these steps to install the project:

1. Clone the repository:
    ```bash
    git clone git@github.com:dharmykoya/petstore.git
    ```

2. Navigate to the project directory:
    ```bash
    cd petstore
    ```

3. Install the PHP dependencies:
    ```bash
    composer install
    ```

## Environment Setup

1. Copy the `.env.example` file to `.env`:
    ```bash
    cp .env.example .env
    ```

2. Generate an application key:
    ```bash
    php artisan key:generate
    ```

3. Configure the `.env` file with your database credentials and other settings.

4. Database Migration:
    ```bash
    php artisan migrate
    ```

5. Database Migration:
    ```bash
    php artisan migrate --seed
    ```

6. Start Server:
    ```bash
    php artisan serve
    ```
## Testing
- Run Test:
    ```bash
    php artisan test
    ```

## Documentation
- Swagger documentation
    ```bash
    {{baseUrl}}/api/v1/documentation
    ```

## Technologies
- Laravel
- Mysql
- JWT (RFC 7519 standard)
- GIT

## Improvements
- Add docker
- Caching


## License
&copy; Damilola Adekoya
