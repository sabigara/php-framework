<?php

class Request
{
    public function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function isPost()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return true;
        }

        return false;
    }

    public function getGet($name, $default = null)
    {
        if (isset($_GET[$name])) {
            return $_GET[$name];
        }

        return $default;
    }

    public function getPost($name, $default = null)
    {
        if (isset($_POST[$name])) {
            return $_POST[$name];
        }

        return $default;
    }

    public function getHost()
    {
        if (!empty($_SERVER['HTTP_HOST'])) {
            return $_SERVER['HTTP_HOST'];
        }

        return $_SERVER['SERVER_NAME'];
    }

    public function isSSL()
    {
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            return true;
        }

        return false;
    }

    public function getRequestURI()
    {
        return $_SERVER['REQUEST_URI'];
    }

    public function getBaseURL()
    {
        return '/index.php';
        // $script_name = $_SERVER['SCRIPT_NAME'];

        // $request_uri = $this->getRequestURI();

        // if (strpos($request_uri, $script_name) === 0) {
        //     return $script_name;
        // } else if (strpos($request_uri, dirname($script_name)) === 0) {
        //     return rtrim(dirname($script_name, '/'));
        // }

        // return '';
    }

    public function getPathInfo()
    {
        // $base_url = $this->getBaseURL();
        $request_uri = $this->getRequestURI();

        if (strpos($request_uri, '?') !== false) {
            $query_position = strpos($request_uri, '?');
            $request_uri = substr($request_uri, 0, $query_position);
        }

        return $request_uri;
    }
}