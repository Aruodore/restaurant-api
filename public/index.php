<?php

declare(strict_types= 1);

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(dirname(__FILE__)));

$url = explode("/", $_SERVER["REQUEST_URI"]);

require_once (ROOT . DS . 'lib' . DS . 'bootstrap.php');

