<?php

class ErrorHandler{
    public static function handleError(int $errno, string $errstr, string $errfile, int $errline)
    {
        if(DEVELOPMENT_ENVIRONMENT){
            throw new ErrorException($errstr,0, $errno, $errfile, $errline);
        }else{
        error_reporting(0);
			http_response_code(500);
			echo json_encode([
				"status" => false,
				"message" => "Something went wrong under the hood"
			]); exit;
        }
    }
    public static function handleException(Throwable $e)
    {
         http_response_code(500);

        echo json_encode(array(
            'code'=> $e->getCode(),
            'message'=>$e->getMessage(),
            'file'=>$e->getFile(),
            'line'=>$e->getLine(),
        ));

    }
}