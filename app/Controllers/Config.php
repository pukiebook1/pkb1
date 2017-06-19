<?php
namespace Core;

class Config
{
	public function __construct()
	{
		//turn on output buffering
		ob_start();

		//site address
		define('DIR', 'http://pukiebook.com/');
		//define('DIR', 'http://pkb.localhost/');

		//set default controller and method for legacy calls
		define('DEFAULT_CONTROLLER', 'home');
		define('DEFAULT_METHOD', 'index');

		//set the default template
		define('TEMPLATE', 'default');

		//set a default language
		define('LANGUAGE_CODE', 'es');

		//database details ONLY NEEDED IF USING A DATABASE
		define('DB_TYPE', 'mysql');
		define('DB_HOST', 'localhost');
		define('DB_NAME', 'u361724199_pkb');
		define('DB_USER', 'u361724199_web');
		define('DB_PASS', 'pukiepukie');
		define('PREFIX', 'twod_');

		//set prefix for sessions
		define('SESSION_PREFIX', 'twod_');
		define('COOKIE_PREFIX', 'twod_');
		define('COOKIE_LIFE', 60*60*24*15);
		define('CSRF_LIFE', 60 * 60 * 24);

		//optionall create a constant for the name of the site
		define('SITETITLE', 'Pukiebook');
		define('MAINTENANCE', false);

		$version = @file_get_contents("VERSION");

		if($version)
			define('VERSION', trim($version));
		else
			define('VERSION', 'DEV');

		//optionall set a site email address
		define('SITEEMAIL', 'info@pukiebook.com');
		define('ADMINNOTIF', 'pukiebook@gmail.com');

		define('PROFILEPICTUREPATH', "imagenes/perfil/");
		define('TEAMPICTUREPATH', "imagenes/equipo/");
		define('EVENTPICTUREPATH', "imagenes/evento/");
		define('SPONSORPICTUREPATH', "imagenes/patrocinadores/");
		define('BOXPICTUREPATH', "imagenes/box/");

		define('EVENT_NORMAL_LIFE', 4*24*60*60);
		define('EVENT_OPEN_LIFE', 5*24*60*60);
		define('EVENT_VENTANA_REGISTRO', 60*60);
		
		//turn on custom error handling
		set_exception_handler('Core\Logger::ExceptionHandler');
		set_error_handler('Core\Logger::ErrorHandler');
		register_shutdown_function( "Core\Logger::FatalHandler" );

		//set timezone
		date_default_timezone_set('UTC');
	}
}