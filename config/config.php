<?php

/** Configuration Variables **/

	# |===============================================
	# | DEVELOPMENT ENVIRONMENT
	# | Auto move to production environment when not running on local network
	# |===============================================
	$devEnvironments = ['localhost', '127.0.0.1', '::1'];
	if( in_array($_SERVER['REMOTE_ADDR'], $devEnvironments) === TRUE && TRUE ){

		# MYSQL Database Configuration
        define('DB_USER', 'root');
        define('DB_HOST', 'localhost');
		define('DB_PASSWORD', '');
        define('DB_NAME', 'restaurant_api');
		define('DB_ERROR', true);
        define ('DEVELOPMENT_ENVIRONMENT',true);

	# |===============================================
	# | PRODUCTION CONFIGURATION
	# |===============================================
	}else{

		# MYSQL Database Configuration
		define('DB_USER', '');
        define('DB_HOST', '');
		define('DB_PASSWORD', '');
        define('DB_NAME', '');
		define('DB_ERROR', false);
        define ('DEVELOPMENT_ENVIRONMENT',false);

	}