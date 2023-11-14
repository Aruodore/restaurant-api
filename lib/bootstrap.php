<?php

require ROOT.DS."config".DS."config.php";

spl_autoload_register(function ($class) {
    if(file_exists(ROOT.DS."lib".DS."$class.php")) {
        require_once ROOT.DS."lib".DS."$class.php";
    }
   else if(file_exists(ROOT.DS."app".DS.'controllers'.DS."$class.php")) {
        require_once ROOT.DS."app".DS.'controllers'.DS."$class.php";
    }
   else if(file_exists(ROOT.DS."app".DS.'models'.DS."$class.php")) {
        require_once ROOT.DS."app".DS.'models'.DS."$class.php";
    }
    else if(file_exists(__DIR__.DS."$class.php")){
         require_once __DIR__.DS."$class.php";
     }
});


set_error_handler("ErrorHandler::handleError");
set_exception_handler("ErrorHandler::handleException");

header("Content-type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true ");
header("Access-Control-Allow-Methods: OPTIONS, GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Depth, User-Agent, X-File-Size, X-Requested-With, If-Modified-Since, X-File-Name, Cache-Control");


if(($url[2] !== "restaurants") AND ($url[2] !== 'locations') AND ($url[2] !== 'categories')) {
    http_response_code(404);
    echo json_encode([
        'error'=> 'EndPoint does not exist',
    ]);
    exit;
}

function callHook()
{
    global $url;
    $enpt = $url[2];
    $id = $url[3] ?? null;
    $sub  = $url[4] ?? null;

    if($sub ){
        if(($sub !== "restaurants") AND ($sub!== 'locations') AND ($sub!== 'categories')){
            http_response_code(404);
            echo json_encode([
            'error'=> 'EndPoint does not exist',
            ]);
            return;
        }
    }

    $model = $enpt === 'categories'? 'category' : rtrim($enpt, 's');
    $model = ucfirst($model);
    $controller = $model . 'Controller';

    $model = new $model;

    $controller = new $controller( $model );

    $controller->processRequest($_SERVER['REQUEST_METHOD'], $id, $sub);
}

callHook();




