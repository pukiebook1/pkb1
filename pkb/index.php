<?php

if (file_exists('vendor/autoload.php')) {
	require 'vendor/autoload.php';
} else {
	echo "<h1>Please install via composer.json</h1>";
	echo "<p>Install Composer instructions: <a href='https://getcomposer.org/doc/00-intro.md#globally'>https://getcomposer.org/doc/00-intro.md#globally</a></p>";
	echo "<p>Once composer is installed navigate to the working directory in your terminal/command promt and enter 'composer install'</p>";
	exit;
}

if (!is_readable('app/Core/Config.php')) {
	die('No Config.php found, configure and rename Config.example.php to Config.php in app/Core.');
}

/*
 *---------------------------------------------------------------
 * APPLICATION ENVIRONMENT
 *---------------------------------------------------------------
 *
 * You can load different configurations depending on your
 * current environment. Setting the environment also influences
 * things like logging and error reporting.
 *
 * This can be set to anything, but default usage is:
 *
 *     development
 *     production
 *
 * NOTE: If you change these, also change the error_reporting() code below
 *
 */
	define('ENVIRONMENT', 'production');
/*
 *---------------------------------------------------------------
 * ERROR REPORTING
 *---------------------------------------------------------------
 *
 * Different environments will require different levels of error reporting.
 * By default development will show errors but production will hide them.
 */

if (defined('ENVIRONMENT')) {
	switch (ENVIRONMENT) {
		case 'development':
			error_reporting(E_ALL);
			break;
		case 'production':
			error_reporting(0);
			break;
		default:
			exit('The application environment is not set correctly.');
	}

}

//initiate config
new Core\Config();

//create alias for Router
use Core\Router;
use Helpers\Hooks;

//define routes
if(defined('MAINTENANCE') && (MAINTENANCE == true))
{
	Router::any('(:all)', 'Controllers\Home@maintenance');
}
else
{
	Router::any('', 'Controllers\Home@index');

	/* VISTA EVENTO Y TABLA */
	
	Router::get('/eventos', 'Controllers\Eventos@index');
	Router::get('/evento/(:any)', 'Controllers\Ranking@index');
	Router::get('/evento/(:any)/(:num)', 'Controllers\Ranking@tablaResultados');
	Router::get('/evento/(:any)/(:num)/(:num)', 'Controllers\Ranking@tablaResultados');
	Router::get('/evento/(:any)/team(:num)', 'Controllers\Ranking@tablaResultadosTeam');
	Router::get('/evento/(:any)/team(:num)/(:num)', 'Controllers\Ranking@tablaResultadosTeam');

	/*VISTA BOX*/
	Router::get('/box/(:any)', 'Controllers\Box@index');

	/* VISTA TEAM EN EVENTO */
	Router::get('/evento/(:any)/team/(:num)', 'Controllers\Eventos@verEquipo');

	/* CREACION EVENTO */
	Router::get('/cuenta/crearevento', 'Controllers\Eventos@preCrearEvento');
	Router::get('/cuenta/crearevento/crear', 'Controllers\Eventos@crearEvento');
	Router::post('/cuenta/crearevento/crear', 'Controllers\Eventos@crearEventoPOST');
	Router::get('/cuenta/crearevento/continuar/(:any)', 'Controllers\Eventos@continuarCreacionPendiente');
	Router::get('/cuenta/crearevento/crearCategorias/(:any)', 'Controllers\Eventos@crearEventoCategorias');
	Router::post('/cuenta/crearevento/crearCategorias/(:any)', 'Controllers\Eventos@crearEventoCategoriasPOST');
	Router::get('/cuenta/crearevento/crearWods/(:any)', 'Controllers\Eventos@crearEventoWods');
	Router::post('/cuenta/crearevento/crearWods/(:any)', 'Controllers\Eventos@crearEventoWodsPOST');
	Router::get('/cuenta/crearevento/crearRelaciones/(:any)', 'Controllers\Eventos@crearEventoRelaciones');
	Router::post('/cuenta/crearevento/crearRelaciones/(:any)', 'Controllers\Eventos@crearEventoRelacionesPOST');

	/* CREACION DE BOX */
	Router::get('/cuenta/crearbox', 'Controllers\Cuenta@preCrearBox');
	Router::get('/cuenta/crearbox/crear', 'Controllers\Cuenta@crearBox');
	Router::post('/cuenta/crearbox/crear', 'Controllers\Cuenta@crearBoxPOST');

	/* CREACION DE EQUIPO */
	Router::get('/cuenta/equipo/preCrear', 'Controllers\Cuenta@preCrearEquipo');
	Router::get('/cuenta/equipo/crear', 'Controllers\Cuenta@crearEquipo');
	Router::post('/cuenta/equipo/crear', 'Controllers\Cuenta@crearEquipoPOST');

	/* MODIFICACION DE EQUIPO */
	Router::get('/cuenta/equipo/mod/(:any)', 'Controllers\Cuenta@modEquipo');
	Router::post('/cuenta/equipo/mod/(:any)', 'Controllers\Cuenta@modEquipoPOST');
	Router::get('/cuenta/equipo/mod/(:any)/integrantes', 'Controllers\Cuenta@modEquipoIntegrantes');
	Router::post('/cuenta/equipo/mod/(:any)/integrantes', 'Controllers\Cuenta@modEquipoIntegrantesPOST');
	Router::get('/cuenta/equipo/mod/(:any)/agregarintegrantes', 'Controllers\Cuenta@modEquipoAgregarIntegrantes');
	Router::post('/cuenta/equipo/mod/(:any)/agregarintegrantes', 'Controllers\Cuenta@modEquipoAgregarIntegrantesPOST');
	Router::get('/cuenta/equipo/mod/(:num)/formalizar', 'Controllers\Equipos@formalizar');
	Router::post('/cuenta/equipo/mod/(:num)/formalizar', 'Controllers\Equipos@formalizarPOST');
	Router::get('/cuenta/equipo/eliminar/(:any)', 'Controllers\Cuenta@modEquipoEliminar');
	Router::post('/cuenta/equipo/eliminar/(:any)', 'Controllers\Cuenta@modEquipoEliminarPOST');

	/* VISTA DE EQUIPO */
	Router::get('/equipo/(:num)', 'Controllers\Equipos@ver');
	Router::get('/equipo/(:num)/aceptar', 'Controllers\Equipos@aceptarSolicitud');
	Router::get('/equipo/(:num)/rechazar', 'Controllers\Equipos@rechazarSolicitud');

	/* MODIFICACION EVENTO */
	Router::get('/cuenta/modevento/(:any)/info', 'Controllers\Eventos@modEventoInfo');
	Router::post('/cuenta/modevento/(:any)/info', 'Controllers\Eventos@modEventoInfoPOST');
	Router::get('/pagosEvento/(:num)', 'Controllers\Eventos@verPago');
	Router::get('/pagosEventoEquipo/(:num)', 'Controllers\Eventos@verPagoEquipo');
	Router::get('/cuenta/modevento/(:any)/categorias', 'Controllers\Eventos@modEventoCategorias');
	Router::post('/cuenta/modevento/(:any)/categorias', 'Controllers\Eventos@modEventoCategoriasPOST');
	Router::get('/cuenta/modevento/(:any)/wods', 'Controllers\Eventos@modEventoWods');
	Router::post('/cuenta/modevento/(:any)/wods', 'Controllers\Eventos@modEventoWodsPOST');
	Router::get('/cuenta/modevento/(:any)/relaciones', 'Controllers\Eventos@modEventoRelaciones');
	Router::post('/cuenta/modevento/(:any)/relaciones', 'Controllers\Eventos@modEventoRelacionesPOST');
	Router::get('/cuenta/modevento/(:any)/patrocinadores', 'Controllers\Eventos@modEventoPatrocinadores');
	Router::post('/cuenta/modevento/(:any)/patrocinadores', 'Controllers\Eventos@modEventoPatrocinadoresPOST');
	Router::get('/cuenta/modevento/(:any)/participantesaprobar', 'Controllers\Eventos@modEventoAtletasAprobar');
	Router::post('/cuenta/modevento/(:any)/participantesaprobar', 'Controllers\Eventos@modEventoAtletasAprobarPOST');
	Router::get('/cuenta/modevento/(:any)/participantesaprobados', 'Controllers\Eventos@modEventoAtletasAprobados');
	Router::post('/cuenta/modevento/(:any)/participantesaprobados', 'Controllers\Eventos@modEventoAtletasAprobadosPOST');
	Router::get('/cuenta/modevento/(:any)/participantespesos', 'Controllers\Eventos@modEventoAtletasPesos');
	Router::post('/cuenta/modevento/(:any)/participantespesos', 'Controllers\Eventos@modEventoAtletasPesosPOST');
	Router::get('/cuenta/modevento/(:any)/participantesExportable', 'Controllers\Eventos@modEventoAtletasExportable');
	Router::get('/cuenta/modevento/(:any)/juez', 'Controllers\Eventos@modEventoJuez');
	Router::post('/cuenta/modevento/(:any)/juez', 'Controllers\Eventos@modEventoJuezPOST');
	Router::get('/cuenta/modevento/(:any)/participantes', 'Controllers\Eventos@modEventoAtletas');
	Router::post('/cuenta/modevento/(:any)/participantes', 'Controllers\Eventos@modEventoAtletasPOST');
	Router::get('/cuenta/modevento/(:any)/eliminar', 'Controllers\Eventos@modEventoEliminar');
	Router::post('/cuenta/modevento/(:any)/eliminar', 'Controllers\Eventos@modEventoEliminarPOST');
	Router::get('/cuenta/modevento/(:any)/plan', 'Controllers\Eventos@modEventoPlan');
	Router::post('/cuenta/modevento/(:any)/plan', 'Controllers\Eventos@modEventoPlanPOST');
	Router::get('/cuenta/modevento/(:any)/crearEquipo', 'Controllers\Eventos@modEventoEquipoCrear');
	Router::post('/cuenta/modevento/(:any)/crearEquipo', 'Controllers\Eventos@modEventoEquipoCrearPOST');

	//Router::get('/cuenta/modevento/(:any)/crearEquipo/(:num)/participantes', 'Controllers\Eventos@modEventoEquipoCrearParticipantes');
	//Router::post('/cuenta/modevento/(:any)/crearEquipo/(:num)/participantes', 'Controllers\Eventos@modEventoEquipoCrearParticipantesPOST');

	/* MODIFICACION BOX */
	Router::get('/cuenta/modbox/(:any)', 'Controllers\Cuenta@modBox');
	Router::post('/cuenta/modbox/(:any)', 'Controllers\Cuenta@modBoxPOST');
	Router::get('/cuenta/modbox/(:any)/eliminar', 'Controllers\Cuenta@modBoxEliminar');
	Router::post('/cuenta/modbox/(:any)/eliminar', 'Controllers\Cuenta@modBoxEliminarPOST');

	/* ADMINISTRACION DE BOX */
	Router::get('/cuenta/modbox/(:any)/aprobar', 'Controllers\Cuenta@aprobarBox');
	Router::get('/cuenta/modbox/(:any)/desaprobar', 'Controllers\Cuenta@desaprobarBox');

	/* INGRESO DE RESULTADOS */
	Router::get('/cuenta/modevento/(:any)/resultados', 'Controllers\Eventos@modEventoResultados');
	Router::get('/cuenta/modevento/(:any)/resultados/(:num)', 'Controllers\Eventos@modEventoResultados2');
	Router::get('/cuenta/modevento/(:any)/resultados/(:num)/(:num)', 'Controllers\Eventos@modEventoResultados3');
	Router::post('/cuenta/modevento/(:any)/resultados/(:num)/(:num)', 'Controllers\Eventos@modEventoResultados3POST');
	
	/* INGRESO DE RESULTADOS OPEN */
	Router::get('/cuenta/modevento/(:any)/miresultado/(:num)/(:num)', 'Controllers\Eventos@modEventoMiResultado');
	Router::post('/cuenta/modevento/(:any)/miresultado/(:num)/(:num)', 'Controllers\Eventos@modEventoMiResultadoPOST');

	/* ADMINISTRACION DE EVENTOS */
	Router::get('/cuenta/modevento/(:any)/aprobar', 'Controllers\Eventos@aprobarEvento');
	Router::get('/cuenta/modevento/(:any)/desaprobar', 'Controllers\Eventos@desaprobarEvento');
	Router::get('/cuenta/modevento/(:any)/finalizar', 'Controllers\Ranking@fijarResultados');
	Router::get('/cuenta/modevento/(:any)/abrir', 'Controllers\Ranking@liberarResultados');
	Router::get('/auto/eventonormalclose', 'Controllers\Ranking@fijarResultadosVencidos'); 	
	Router::get('/auto/eventoopenclose', 'Controllers\Ranking@fijarResultadosVencidosOpen');

	/* ADMINISTRACION DE CUENTAS */
	Router::get('/admin/registrousuarios', 'Controllers\Admin@registrarUsuarios');
	Router::post('/admin/registrousuarios', 'Controllers\Admin@registrarUsuariosPOST');
	Router::get('/admin/reseteoclaves', 'Controllers\Admin@resetearClaves');
	Router::post('/admin/reseteoclaves', 'Controllers\Admin@resetearClavesPOST');

	/* REGISTRO EN EVENTO */
	Router::get('/cuenta/registrarseevento', 'Controllers\Cuenta@registrarEnEvento');
	Router::get('/cuenta/registrarseevento/(:any)', 'Controllers\Cuenta@registrarEnEventoID');
	Router::get('/cuenta/registrarseevento/(:any)/(:num)', 'Controllers\Cuenta@registrarEnEventoIDCAT');
	Router::get('/cuenta/registrarseevento/(:any)/(:num)/registrarPago', 'Controllers\Cuenta@registrarEnEventoIDCATPAGOTIPO');
	Router::get('/cuenta/registrarseevento/(:any)/(:num)/registrarPago/(:num)', 'Controllers\Cuenta@registrarEnEventoIDCATPAGO');
	Router::post('/cuenta/registrarseevento/(:any)/(:num)/registrarPago/(:num)', 'Controllers\Cuenta@registrarEnEventoIDCATPAGOPOST');

	Router::get('/cuenta/registrarseevento/(:any)/(:num)/e', 'Controllers\Cuenta@registrarEnEventoEquipo2');
	Router::get('/cuenta/registrarseevento/(:any)/(:num)/e/(:num)', 'Controllers\Cuenta@registrarEnEventoEquipo3');

	Router::get('/cuenta/registrarseevento/(:any)/(:num)/e/(:num)/registrarPago', 'Controllers\Cuenta@registrarEnEventoEquipoIDCATPAGOTIPO');
	Router::get('/cuenta/registrarseevento/(:any)/(:num)/e/(:num)/registrarPago/(:num)', 'Controllers\Cuenta@registrarEnEventoEquipoIDCATPAGO');
	Router::post('/cuenta/registrarseevento/(:any)/(:num)/e/(:num)/registrarPago/(:num)', 'Controllers\Cuenta@registrarEnEventoEquipoIDCATPAGOPOST');

	/* PASARELAS DE PAGO */
	Router::get('/epayco', 'Controllers\Pasarela@registrarepayco');

	/* CUENTA Y PERFIL */
	Router::get('/cuenta', 'Controllers\Cuenta@index');
	Router::get('/perfil/(:num)', 'Controllers\Cuenta@perfil');
	Router::get('/perfil/(:num)/edit', 'Controllers\Cuenta@perfilEdit');
	Router::post('/perfil/(:num)/edit', 'Controllers\Cuenta@perfilEditPOST');
	Router::get('/cuenta/eventosorg', 'Controllers\Cuenta@eventosOrganizados');
	Router::get('/cuenta/eventosjuez', 'Controllers\Cuenta@eventosJuez');
	Router::get('/cuenta/misboxes', 'Controllers\Cuenta@boxesCuenta');
	Router::get('/cuenta/misequipos', 'Controllers\Cuenta@equiposCuenta');

	Router::get('/cuenta/contrasena', 'Controllers\Cuenta@cambioContrasena');
	Router::post('/cuenta/contrasena', 'Controllers\Cuenta@cambioContrasenaPOST');
	Router::get('/lost', 'Controllers\Cuenta@lost');
	Router::post('/lost', 'Controllers\Cuenta@lostPOST');
	Router::get('/lost/(:num)/(:any)', 'Controllers\Cuenta@lostCODE');
	Router::post('/lost/(:num)/(:any)', 'Controllers\Cuenta@lostCODEPOST');

	/* AUTENTICACION Y REGISTRO */
	Router::get('/verificar/correo/(:num)/(:any)', 'Controllers\Autenticacion@verificarCorreo');
	Router::get('/enviarActivacion/correo/(:num)', 'Controllers\Autenticacion@enviarVerificacion');
	Router::get('/activar/cuenta/(:num)/(:any)', 'Controllers\Autenticacion@activarCuenta');
	Router::get('/enviarActivacion/cuenta/(:num)', 'Controllers\Autenticacion@enviarActivacion');
	Router::get('/registrar', 'Controllers\Autenticacion@registrar');
	Router::post('/registrar', 'Controllers\Autenticacion@registrarPOST');
	Router::get('/accederred', 'Controllers\Autenticacion@accederSinRedirect');
	Router::get('/acceder', 'Controllers\Autenticacion@acceder');
	Router::post('/acceder', 'Controllers\Autenticacion@accederPOST');
	Router::get('/salir', 'Controllers\Autenticacion@salir');
	// Router::get('/test/(:any)', 'Controllers\Autenticacion@test');
}

//module routes
Hooks::setHook('menu');
Hooks::setHook('menuJuez');
Hooks::setHook('menuCuenta');
$hooks = Hooks::get();
$hooks->run('routes');

//start sessions
use Helpers\Sesiones;
Sesiones::init();

//if no route found
Router::error('Core\Error@index');

//turn on old style routing
Router::$fallback = false;

//execute matched routes
Router::dispatch();

