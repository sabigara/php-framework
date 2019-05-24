<?php

abstract class App
{
    protected $request;
    protected $response;
    protected $session;
    protected $router;
    protected static $_config = array();

    public function __construct($debug = false)
    {
        $this->setDebugMode($debug);
        $this->initialize();
        $this->configure();
    }

    protected function setDebugMode($is_debug)
    {
        if ($is_debug) {
            ini_set('display_errors', 1);
            error_reporting(-1);
        } else {
            ini_set('display_erros', 0);
        }

        App::setConfig('DEBUG', $is_debug);
    }

    protected function initialize()
    {
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->router = new Router($this->registerRoutes());
    }

    protected function configure()
    {
    }

    public static function getConfig($name)
    {
        return self::$_config[$name];
    }

    protected static function setConfig($name, $value = null)
    {
        if (is_null($value)) {
            self::$_config[$name] = getenv($name);
        } else {
            self::$_config[$name] = $value;
        }
    }

    abstract public function getRootDir();

    abstract protected function registerRoutes();

    public function isDebugMode()
    {
        return $this->debug;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function getSession()
    {
        return $this->session;
    }

    public function getControllerDir()
    {
        return $this->getRootDir() . '/controllers';
    }

    public function getViewDir()
    {
        return $this->getRootDir() . '/views';
    }

   public function getModelDir()
    {
        return $this->getRootDir() . '/models';
    }

    public function getWebDir()
    {
        return $this->getRootDir() . '/web';
    }

    public function run()
    {
        try {
            $path = $this->request->getPathInfo();
            $method = $this->request->method();

            $route_params = $this->router->resolve($path, $method);

            $controller = $route_params['controller'];
            $action = $route_params['action'];

            $this->runAction($controller, $action);

        } catch (HttpNotFoundException $e) {
            $this->render404Page($e);
        } catch (UnauthorizedActionException $e) {
            list($controller, $action) = $this->login_action;
            $this->runAction($controller, $action);
        } catch (HttpMethodNotAllowdException $e) {
            $this->response->setContent($e->getMessage());
        }

        $this->response->send();
    }

    public function runAction($controller_name, $action, $params = array())
    {
        $controller_class = ucfirst($controller_name) . 'Controller';

        $controller = $this->findController($controller_class);
        if ($controller === false) {
            throw new HttpNotFoundException($controller_class . ' controller not found');
        }

        $content = $controller->run($action, $params);

        $this->response->setContent($content);
    }

    protected function findController($controller_class)
    {
        if (!class_exists($controller_class)) {
            $controller_file = $this->getControllerDir() . '/' . $controller_class . '.php';

            if (!is_readable($controller_file)) {
                return false;
            } else {
                require_once $controller_file;

                if (!class_exists($controller_class)) {
                    return false;
                }
            }
        }

        return new $controller_class($this);
    }

    protected function render404Page($e)
    {
        $this->response->setStatusCode(404, 'Not Found');
        $message = $this->isDebugMode() ? $e->getMessage() : 'Page not found';
        $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
        $html = <<<EOF
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; chaset=utf-8" />
    <title>404</title>
</head>
<body>
    {$message}
</body>
</html>
EOF;
        $this->response->setContent($html);
    }
}