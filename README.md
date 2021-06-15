## Cost Explorer

Laravel Sail is used for setting up development environment. These are the steps to get it up and running for development.

 1. Copy over the .env file
`cp .env.example .env`
 2. Install the composer packages
 `composer install`
 3. Bring up the docker container. The Laravel app will be served at localhost:8000 and PostgreSQL will be available at localhost:54320. The APP_PORT and FORWARD_DB_PORT can be updated if you want to run the services on different ports.
 `php artisan sail:install`
 `./vendor/bin/sail up`
 4. Genearate application key
 `./vendor/bin/sail artisan key:generate`
 5. Laravel Sail will create a `cost_explorer` database. Restore the database dump to the `cost_explorer` database. Refer `.env` file for DB credentials
 6. The app will be available at http://localhost:8000/explorer
 7. Running the tests
 `./vendor/bin/sail artisan test`
 Note: you will have to create a database named `cost_explorer_test` to run the tests.




