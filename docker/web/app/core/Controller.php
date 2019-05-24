<?php

abstract class Controller
{
    protected $controller_name;
    protected $action_name;
    protected $app;
    protected $request;
    protected $response;
    protected $session;
    protected $auth_actions = array();

    public function __construct($app)
    {
        // Subtract 10 == char count of 'Controller'
        $this->controller_name = strtolower(substr(get_class($this), 0, -10));

        $this->app = $app;
        $this->request = $app->getRequest();
        $this->response = $app->getResponse();
        $this->session = $app->getSession();
    }

    public function run($action, $params = array())
    {
        $this->action_name = $action;

        $action_method = $action . 'Action';
        if (!method_exists($this, $action_method)) {
            $this->forward404();
        }

        if ($this->needsAuthentication($action) && !$this->session->isAuthenticated()) {
            throw new UnauthorizedActionException();
        }

        $content = $this->$action_method($params);

        return $content;
    }

    protected function needsAuthentication($action)
    {
        if ($this->auth_actions === true
            || (is_array($this->auth_actions)
            && in_array($action, $this->auth_actions))
        ) {
            return true;    
        }
        
        return false;
    }

    protected function render($vars = array(), $template = null, $layout = 'layout')
    {
        $defaults = array(
            'request' => $this->request,
            'base_url' => $this->request->getBaseUrl(),
            'session' => $this->session,
        );

        $view = new View($this->app->getViewDir(), $defaults);

        if (is_null($template)) {
            $template = $this->action_name;
        }
 
        $path = $this->controller_name . '/' . $template;

        return $view->render($path, $vars, $layout);
    }

    protected function forward404()
    {
        throw new HttpNotFoundException('Forwarded 404 page from '
            . $this->controller_name . '/' . $this->action_name);
    }

    protected function redirect($url)
    {
        if (!preg_match('#https?://#', $url)) {
            $protocol = $this->request->isSSL() ? 'https://' : 'http://';
            $host = $this->request->getHost();
            $port = '';
            if (APP::getConfig('DEBUG') === true) {
                $port = ':' . App::getConfig('HOST_PORT');
            }
            $url = $protocol . $host . $port . $url;
        }

        $this->response->setStatusCode(302, 'Found');
        $this->response->setHttpHeader('Location', $url);
    }

    protected function generateCsrfToken($form_name)
    {
        $key = 'csrf_tokens/' . $form_name;
        $tokens = $this->session->get($key, array());
        if (count($tokens) >= 10) {
            array_shift($tokens);
        }

        $token = sha1($form_name . session_id() . microtime());
        $tokens[] = $token;

        $this->session->set($key, $tokens);

        return $token;
    }

    protected function checkCsrfToken($form_name, $token)
    {
        if (substr($form_name, 0, 1) === '/') {
            $form_name = ltrim($form_name, '/');
        }

        $key = 'csrf_tokens/' . $form_name;
        $tokens = $this->session->get($key, array());

        if (array_search($token, $tokens, true) !== false)
        {
            $position = array_search($token, $tokens, true);
            unset($tokens[$position]);
            $this->session->set($key, $tokens);

            return true;
        }

        return false;
    }
}