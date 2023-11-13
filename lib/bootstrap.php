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


if(($url[2] !== "restaurants") AND ($url[2] !== 'locations') AND ($url[2] !== 'categories')) {
    http_response_code(404);
    exit;
}

$id = $url[3] ?? null;
$sub  = $url[4] ?? null;


if($url[2] === 'categories'){
    $model = new Category;
    $controller = new CategoryController($model);

    $controller->processRequest($_SERVER['REQUEST_METHOD'], $id, $sub);
}

if($url[2] === 'locations'){
    $model = new Location;
    $controller = new LocationController($model);

    $controller->processRequest($_SERVER['REQUEST_METHOD'], $id, $sub);
}


if ($url[2] === 'restaurants') {
    $model = new Restaurant;
    $controller = new RestaurantController($model);
    
    $controller->processRequest($_SERVER['REQUEST_METHOD'], $id, $sub);
}




