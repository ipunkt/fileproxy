<?php

namespace App\Validation;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Validation\Validator;

class RouteUrlValidator
{
    /**
     * Validates a given relative or absolute url against configured routes.
     *
     * Definition:
     * a)
     * route_url:YOUR_PATH_OR_URL,serve -> use route("serve") to build absolute url with given param
     * - route "serve" does not fail when matching will be checked
     *
     * b)
     * route_url:YOUR_PATH_OR_URL -> use url() to build absolute url with given param
     * - route "serve" does fail when matching will be checked (see difference to a) )
     *
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @param Validator $validator
     * @return bool
     */
    public function validate(string $attribute, string $value, array $parameters, Validator $validator)
    {
        $routeName = array_get($parameters, '1', null);
        $urlToVerify = $routeName === null
            ? url($value)
            : route($routeName, [$value]);

        /** @var Router $router */
        $router = app(Router::class);

        //  only check GET routes
        $getRoutes = $router->getRoutes()->get('GET');

        $request = Request::create($urlToVerify);

        /**
         * @var string $url
         * @var Route $route
         */
        foreach ($getRoutes as $url => $route) {
            //  do not match the selected route name with which the url gets created
            if ($route->getName() === $routeName) {
                continue;
            }

            if ($route->matches($request)) {
                $validator->errors()->add($attribute, 'The specified url is already assigned');
                return false;
            }
        }

        return true;
    }
}