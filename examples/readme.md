# Example Section

The file proxy app is a microservice with an optional web ui. We do not want to provide any user authentication or authorization. A microservice should be secured by its environment. So we provide example configurations for various environment systems.

If you prefer using as laravel application container you can add authorization and authentication by providing middleware and doing the laravel `make:auth` command. But we recommend using this service inside your infrastructure.

## nginx configuration

Check out `./nginx/nginx.conf`

## apache2 configuration

Check out `./apache2/apache.conf`