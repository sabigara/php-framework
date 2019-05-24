<?php

require 'core/ClassLoader.php';
require 'vendor/autoload.php';

$loader = new ClassLoader();
$loader->registerDir(dirname(__FILE__).'/core');
$loader->registerDir(dirname(__FILE__).'/core/exceptions');
$loader->registerDir(dirname(__FILE__).'/main/models');
$loader->register();