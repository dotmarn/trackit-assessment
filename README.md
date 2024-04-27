# TrackTik Assessment
This task is developed with the latest version of the Laravel framework (Laravel v11). PHP v8.2 and above is required to run this task.

### Task Overview
I developed an API endpoints that accepts employee data from two (2) different identity providers. Each provider has a different schema and the data is transformed before posting to the TrackTik Rest API to handle.

## Installation
Here is how you can run the project locally:
1. Clone this repo
    ```sh
    git clone https://github.com/dotmarn/trackit-assessment.git
    ```
1. Go into the project root directory
    ```sh
    cd trackit-assessment
    ```
1. Copy .env.example file to .env file
    ```sh
    cp .env.example .env
    ```
1. Run installation
    ```sh
    composer install
    ```
1. Generate app key 
    ```sh
    php artisan key:generate
    ```
1. As of Laravel v11, it ships with SQLite as the default database. Proceed to run the migration to create the database tables.
    ```
    php artisan migrate
    ```
1. Start the application 
    ```sh
    php artisan serve
    ``` 
### API Endpoint (Create)

    ```sh
    POST /api/v1/employees
    ```

### API Endpoint (Update)

    ```sh
    PATCH /api/v1/employees?employee_id=1
    ```

#### Provider One Schema
    ```json
    {
        "provider": "one",
        "first_name": "John",
        "last_name": "Doe",
        "email_address": "johndoe@gmail.com"
    }
    ```

#### Provider Two Schema
    ```json
    {
        "provider": "two",
        "FirstName": "John",
        "LastName": "Doe",
        "EmailAddress": "johndoe@gmail.com"
    }
    ```



1. Create an employee
