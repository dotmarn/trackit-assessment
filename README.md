# TrackTik Assessment
This task is developed with the latest version of the Laravel framework (Laravel v11). PHP v8.2 and above is required to run this task.

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
1. Install PHP dependencies (PHP v8.2 is required)
    ```sh
    composer install
    ```
1. Generate app key 
    ```sh
    php artisan key:generate
    ```
1. Run migration
    ```
    php artisan migrate
    ```
1. Start the application 
    ```sh
    php artisan serve
    ``` 