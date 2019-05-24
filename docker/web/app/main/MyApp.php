<?php

class MyApp extends App
{
    protected static $_settings = array();
    
    public function getRootDir()
    {
        return dirname(__FILE__);
    }

    protected function registerRoutes()
    {
        return array(
            '/'
                => array('controller' => 'root'),
        );
    }

    protected function configure()
    {
        self::setConfig('HOST_PORT');
    }
}