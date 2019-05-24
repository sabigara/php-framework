<?php

class Router
{
    protected $routes;

    public function __construct($definitions)
    {
        $this->routes = $this->compileRoutes($definitions);
    }

    public function compileRoutes($definitions)
    {
        $routes = array();

        foreach ($definitions as $url => $params) {
            $tokens = explode('/', ltrim($url, '/'));
            foreach ($tokens as $index => $token) {
                if (strpos($token, ':') === 0) {
                    $name = substr($token, 1);
                    $token = '(?P<' . $name . '>[^/]+)';
                }
                $tokens[$index] = $token;
            }

            $pattern = '/' . implode('/', $tokens);
            $routes[$pattern] = $params;
        }

        return $routes;
    }

    public function resolve($path, $method)
    {
        if ('/' !== substr($path, 0, 1)) {
            $path = '/' . $path;
        }

        foreach ($this->routes as $url => $route_params) {
            if (preg_match('#^' . $url . '$#', $path, $matches)) {
                if (isset($route_params['methods']) && !in_array($method, $route_params['methods'])) {
                    throw new HttpMethodNotAllowdException("{$method} method is not allowd for {$path}");
                }

                if (!isset($route_params['action'])) {
                    $route_params['action'] = 'index';
                }

                return $route_params;
            }
        }

        throw new HttpNotFoundException("No route found for {$path}");
    }
}