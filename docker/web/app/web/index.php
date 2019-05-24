<?php

require '../bootstrap.php';
require '../main/MyApp.php';

$is_debug = (bool)getenv('DEBUG');
$app = new MyApp($is_debug);
$app->run();