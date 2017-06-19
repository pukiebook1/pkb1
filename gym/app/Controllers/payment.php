<?php
namespace Controllers;

use Core\Controller;
use Core\View;
use Helpers\CsrfNew as Csrf;
use Helpers\Gump;
use Helpers\Url;
use Helpers\ResizeImage;
use Helpers\Codes;
use Models;

class Eventos extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	private static function showInTemplate($data, $info = null)
	{
		View::renderTemplate('header', $data);
		View::render('tracker/mensaje', $data);
		View::renderTemplate('footer', $data);
	}

	public static function atletaEquipoAlredyRegistered()
	{
		$data['title'] = "Atleta registrado";
		$data['titulo'] = "Equipo no permitido";
		$data['mensaje'] = "Uno o m&aacute;s atletas del equipo ya se encuentran registrados en el evento con otro equipo.";
		$data['href'] = DIR."eventos/";
		$data['boton'] = "Ir a lista de eventos";

		Eventos::showInTemplate($data);
	}

	public static function errorPatrocinadores($eventoId = 0, $insert = array(), $update = array())
	{
		$data['title'] = "Error cargando im&aacute;genes";
		$data['titulo'] = "Error cargando im&aacute;genes";
		$data['mensaje'] = "Ocurrieron los siguientes errores cargando im&aacute;genes:<br/>";
		foreach ($insert as $key => $value)
		{
			$data['mensaje'] .= "(".$value['name'].") - ".$value['error'];
		}
		foreach ($update as $key => $value)
		{
			$data['mensaje'] .= "(".$value['name'].") - ".$value['error'];
		}
		$data['href'] = DIR."cuenta/modevento/".$eventoId."/patrocinadores";
		$data['boton'] = "Modificar Patrocinadores";

		Eventos::showInTemplate($data);
	}

	public static function eventoNotPermission()
	{
		$data['title'] = "Evento No Permitido";
		$data['titulo'] = "Evento No Permitido";
		$data['mensaje'] = "No tienes permiso para ver este evento.";
		$data['href'] = DIR."eventos/";
		$data['boton'] = "Ir a lista de eventos";

		Eventos::showInTemplate($data);
	}

	public static function eventoNotFound()
	{
		$data['title'] = "Evento No Encontrado";
		$data['titulo'] = "Evento No Encontrado";
		$data['mensaje'] = "No se encontr&oacute; el evento al cual intentas acceder.";
		$data['href'] = DIR."eventos/";
		$data['boton'] = "Ir a lista de eventos";

		Eventos::showInTemplate($data);
	}

	public static function eventoFinished()
	{
		$data['title'] = "Evento Terminado";
		$data['titulo'] = "Evento Terminado";
		$data['mensaje'] = "Este evento ya ha terminado.";
		$data['href'] = DIR."eventos/";
		$data['boton'] = "Ir a lista de eventos";

		Eventos::showInTemplate($data);
	}

	public static function eventoNoNewAthletes()
	{
		$data['title'] = "Evento Cerrado";
		$data['titulo'] = "Evento Cerrado";
		$data['mensaje'] = "Este evento ya no permite el registro de nuevos participantes.";
		$data['href'] = DIR."eventos/";
		$data['boton'] = "Ir a lista de eventos";

		Eventos::showInTemplate($data);
	}

	public static function eventoAlredyRegistered()
	{
		$data['title'] = "Registrarse en evento";
		$data['titulo'] = "Registrarse en evento";
		$data['mensaje'] = "Ya se encuentra registrado en este evento.";
		$data['boton'] = "Volver";
		$data['href'] = DIR."cuenta";

		Eventos::showInTemplate($data);
	}

	public static function eventoRegistrationPending()
	{
		$data['title'] = "Registrarse en evento";
		$data['titulo'] = "Registrarse en evento";
		$data['mensaje'] = "Su registro se encuentra pendiente por aprobaci&oacute;n del organizador del evento.";
		$data['boton'] = "Volver";
		$data['href'] = DIR."cuenta";

		Eventos::showInTemplate($data);
	}

	public static function eventoRegistroCompleto()
	{
		$data['title'] = "Registro completado";
		$data['titulo'] = "Registro completado";
		$data['mensaje'] = "Tu registro en el evento ha sido completado y debe ser aprobado por el organizador del evento.";
		$data['boton'] = "Ir a mi cuenta";
		$data['href'] = DIR."cuenta";

		Eventos::showInTemplate($data);
	}

	public static function eventoRegistroFallido()
	{
		$data['title'] = "Registro fallido";
		$data['titulo'] = "Registro fallido";
		$data['mensaje'] = "Ya te encuentras registrado en este evento.";			
		$data['boton'] = "Ir a mi cuenta";
		$data['href'] = DIR."cuenta";

		Eventos::showInTemplate($data);
	}

	public static function categoriaNotFound($evento)
	{
		$data['title'] = "Categor&iacute;a no encontrada";
		$data['titulo'] = "Categor&iacute;a no encontrada";
		$data['mensaje'] = "No se encontr&oacute; la categor&iacute;a deseada para el evento \"".$evento->nombre."\"";
		$data['href'] = DIR."evento/".$evento->customurl;
		$data['boton'] = "Ir al evento";

		Eventos::showInTemplate($data);
	}

	public static function wodNotFound($evento, $categoria)
	{
		$data['title'] = "WOD no encontrado";
		$data['titulo'] = "WOD no encontrado";
		$data['mensaje'] = "No se encontr&oacute; el WOD en la categor&iacute;a \"".$categoria->nombre."\" del evento \"".$evento->nombre."\"";
		$data['href'] = DIR."evento/".$evento->customurl;
		$data['boton'] = "Ir al evento";

		Eventos::showInTemplate($data);
	}

	private function usortByDate($a, $b)
	{
		if($a->fecha == $b->fecha)
			return 0;

		return ($a->fecha > $b->fecha) ? -1 : 1;
	}

	public function index()
	{
		$eventos = new Models\Eventos();
		$eventosAp = $eventos->getEventosAprobados();
		$eventos->prepararEventos($eventosAp);

		usort($eventosAp, 'self::usortByDate');
		$byYear = array();

		if(isset($_GET['buscar']))
		{
			$string = trim($_GET['buscar']);

			if(!empty($string))
			{
				foreach ($eventosAp as $key => $value)
				{
					$strNombre = $value->nombre;

					if(stripos($strNombre, $string) === FALSE)
						unset($eventosAp[$key]);
				}
			}
		}

		$cuenta = new Cuenta();
		$logeado = Models\Cuentas::getSession();

		foreach ($eventosAp as $keyE => $valueE)
		{
			if(($valueE->visible) || ($logeado && $logeado['persona']->esAdmin && !$valueE->visible))
				$byYear[$valueE->anno][] = $valueE;
		}
		
		$data['title'] = "Eventos";
		$data['logeado'] = $logeado;
		$data['byYear'] = $byYear;
		$data['fechaActual'] = time();

		View::renderTemplate('header', $data);
		View::render('tracker/eventos', $data);
		View::renderTemplate('footer', $data);
	}

	public function verEquipo($eventoId, $equipoId)
	{
		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			Eventos::eventoNotFound();
			return;
		}

		$equipos = new Models\Equipos();
		$equipo = $eventos->getEquipo($evento->id, $equipoId);
		$equipos->prepararEquipo($equipo);

		if(!$equipo)
		{
			Equipos::equipoNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();
		$persona = null;
		$esOrganizador = false;
		$esAdmin = false;
		$estoy = false;

		if($logeado)
		{
			$persona = $logeado['persona'];
			$esAdmin = $persona->esAdmin;
			$esOrganizador = ($equipo->capitanId == $persona->idPersona);
			$registro = $eventos->getRegistroEquipoEvento($evento->id, $equipo->id, $persona->idPersona);

			if($registro)
				$estoy = $registro;
		}

		$atletas = $eventos->getAtletasRegistradosEvento($evento->id, $equipo->id);
		$atlApro = array();
		$atlPend = array();
		$perfiles = new Models\Perfiles();
		$perfiles->prepararCuentas($atletas);

		foreach ($atletas as $key => $value)
		{
			if($value->aprobado)
				$atlApro[$key] = $value;
			else
				$atlPend[$key] = $value;
		}

		$data['title'] = $equipo->nombre;
		$data["equipo"] = $equipo;
		$data["equipoHome"] = true;
		$data["estoy"] = $estoy;
		$data["logeado"] = $logeado;
		$data["esOrganizador"] = $esOrganizador;
		$data["esAdmin"] = $esAdmin;
		$data["atletasApro"] = $atlApro;
		$data["atletasPend"] = $atlPend;
		$data["fbableTeam"] = true;
		$data["eventoTeam"] = true;

		View::renderTemplate('header', $data);
		View::render('tracker/equipo/equipo', $data);
		View::renderTemplate('footerTabla', $data);
	}

	public function preCrearEvento()
	{
		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}
		
		$data['title'] = "Crear Evento";
		$data['logeado'] = $logeado;
		$data['persona'] = $logeado['persona'];
		
		View::renderTemplate('header', $data);
		View::render('tracker/evento/preCrear', $data);
		View::renderTemplate('footer', $data);
	}

	public function crearEvento($info = array())
	{
		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$data['title'] = "Crear Evento";
		$data['csrf_token'] = Csrf::makeToken();
		$data['logeado'] = $logeado;
		$web = new Models\Web();
		$data['zonas'] = $web->getZonasHorarias();
		
		if(empty($info))
		{
			$info['mensajes'] = array();
			$info['campos'] = array();
			$info['data'] = array();
			$info['data']['urlEvento'] = "http://";
		}

		View::renderTemplate('header', $data);
		View::render('tracker/evento/crear', $data, $info);
		View::renderTemplate('footer', $data);
	}

	public function crearEventoPOST()
	{
		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$persona = $logeado['persona'];

		if (!Csrf::isTokenValid())
		{
			Cuenta::sectionCheckInvalid();
			return;
		}

		if(isset($_POST['submit']))
		{
			$nombre 		= filter_input(INPUT_POST, 'nombre');
			$fechaD 		= filter_input(INPUT_POST, 'fechaD');
			$fechaM 		= filter_input(INPUT_POST, 'fechaM');
			$fechaA 		= filter_input(INPUT_POST, 'fechaA');
			$zonaHoraria	= filter_input(INPUT_POST, 'zonaHoraria');
			$horaH 			= filter_input(INPUT_POST, 'horaH');
			$horaM 			= filter_input(INPUT_POST, 'horaM');
			$urlEvento 		= filter_input(INPUT_POST, 'urlEvento');
			$disciplina 	= filter_input(INPUT_POST, 'disciplina');

			if($urlEvento == "http://")
			{
				$_POST['urlEvento'] = "";
				$urlEvento = "";
			}

			$valores 				= array();
			$valores['nombre'] 		= $nombre;
			$valores['urlEvento'] 	= $urlEvento;
			$valores['fechaD'] 		= $fechaD;
			$valores['fechaM'] 		= $fechaM;
			$valores['fechaA'] 		= $fechaA;
			$valores['zonaHoraria'] = $zonaHoraria;
			$valores['horaH'] 		= $horaH;
			$valores['horaM'] 		= $horaM;
			$valores['disciplina'] 	= $disciplina;

			$info['data'] = $valores;

			$validator 				  = array();
			$validator['nombre'] 	  = 'required';
			$validator['urlEvento']   = 'valid_url';
			$validator['fechaD'] 	  = 'required|numeric|min_numeric,1';
			$validator['fechaM'] 	  = 'required|numeric|min_numeric,1';
			$validator['fechaA'] 	  = 'required|numeric|min_numeric,1';
			$validator['zonaHoraria'] = 'required|numeric';
			$validator['horaH'] 	  = 'required|min_numeric,0|max_numeric,23';
			$validator['horaM'] 	  = 'required|min_numeric,0|max_numeric,59';
			$validator['disciplina']  = 'required';

			$gumpValidator = new Gump();
			$is_valid = $gumpValidator->validate($_POST, $validator);

			if($is_valid !== true)
				$info['campos'] = $gumpValidator->errors();
			else
			{
				$fechaValida = checkdate($fechaM, $fechaD, $fechaA);

				if(!$fechaValida)
					$info['campos']['fechaEvento'] = true;
			}

			if(!empty($info['campos']) || !empty($info['mensajes']))
			{
				self::crearEvento($info);
				return;
			}
			else
			{
				$evv = new Models\Eventos();
				$plan = $persona->subscripcionPlan;
				
				$fecha = strtotime($fechaA."-".$fechaM."-".$fechaD." ".$horaH.":".$horaM);
				if($zonaHoraria < 0)
					$fecha = $fecha - $zonaHoraria;
				else
					$fecha = $fecha + $zonaHoraria;

				$new['creadorId'] = $persona->idPersona;
				$new['tipoSubscripcion'] = $plan;
				$new['nombre'] = $nombre;
				$new['urlEvento'] = $urlEvento;
				$new['fecha'] = $fecha;
				$new['zonaHoraria'] = $zonaHoraria;
				$new['disciplina'] = $disciplina;
				$new['fechaRegistro'] = time();
				$evID = $evv->crearEvento($new);

				if($evID > 0)
					Url::redirect('cuenta/crearevento/crearCategorias/'.$evID);
			}
		}
	}

	private function checkPermisoCreacion($evento, $sessionData)
	{
		$persona = $sessionData['persona'];

		$esOrganizador = ($evento->creadorId == $persona->idPersona);
		$esAdmin = $persona->esAdmin;

		if( ($esOrganizador && !$evento->eventoFinalizado) || $esAdmin)
			return true;

		return false;
	}

	public function checkPasoCreacion($evento)
	{
		if($evento->creacionFinalizada)
			return 0;
		else
			return $evento->paso;
	}

	public function continuarCreacion($eventoId, $paso)
	{
		switch ($paso)
		{
			case 1:
				Url::redirect('cuenta/crearevento/crearCategorias/'.$eventoId);
				break;
			case 2:
				Url::redirect('cuenta/crearevento/crearWods/'.$eventoId);
				break;
			case 3:
				Url::redirect('cuenta/crearevento/crearRelaciones/'.$eventoId);
				break;
			default:
				Url::redirect('evento/'.$eventoId);
				break;
		}
	}

	public function comprobarPasoCreacion($evento, $paso = 0)
	{
		$pasoEvento = self::checkPasoCreacion($evento);

		if($pasoEvento == 0)
			Url::redirect('evento/'.$evento->internalURL);

		if($pasoEvento != $paso)
			self::continuarCreacion($evento->id, $pasoEvento);
	}

	public function continuarCreacionPendiente($eventoId)
	{
		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			self::eventoNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return false;
		}

		$permisoC = self::checkPermisoCreacion($evento, $logeado);

		if(!$permisoC)
		{
			Cuenta::sectionNotPermission();
			return false;
		}

		self::comprobarPasoCreacion($evento, 0);
	}

	public function crearEventoCategorias($eventoId, $info = array())
	{
		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			self::eventoNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$permisoC = self::checkPermisoCreacion($evento, $logeado);

		if(!$permisoC)
		{
			Cuenta::sectionNotPermission();
			return;
		}

		self::comprobarPasoCreacion($evento, 1);

		$cuentas = new Models\Cuentas();

		$data['title'] = "Agregar Categor&iacute;as";
		$data['csrf_token'] = Csrf::makeToken();
		$data['evento'] = $evento;
		$data['logeado'] = $logeado;

		if(empty($info))
		{
			$info['data'] = array();
			$info['mensajes'] = array();
		}

		View::renderTemplate('header', $data);
		View::render('tracker/evento/crearCategorias', $data, $info);
		View::renderTemplate('footer', $data);
	}

	public function crearEventoCategoriasPOST($eventoId)
	{
		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			self::eventoNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$permisoC = self::checkPermisoCreacion($evento, $logeado);

		if(!$permisoC)
		{
			Cuenta::sectionNotPermission();
			return;
		}

		self::comprobarPasoCreacion($evento, 1);
		
		if (!Csrf::isTokenValid())
		{
			Cuenta::sectionCheckInvalid();
			return;
		}

		if(isset($_POST['submit']))
		{			
			$validos = array();
			$info = array();
			$info['data'] = array();
			$info['mensajes'] = array();

			foreach ($_POST['categorias'] as $key => $value)
			{
				$valueT = trim($value);

				if(!empty($valueT))
				{
					$dataDB = array();
					$dataDB['nombre'] = $valueT;
					$dataDB['eventoId'] = $eventoId;
					$validos[] = $dataDB;
					$info['data'][] = $_POST['categorias'][$key];
				}
			}

			if(empty($validos))
			{
				$info['mensajes'][] = "Debe especificar al menos una categor&iacute;a";
			}
			else
			{
				$cuentas = new Models\Cuentas();

				if(count($validos) > $evento->categoriasPlan)
				{
					$info['mensajes'][] = "Ha superado la cantidad de categor&iacute;as permitidas. (Max: ".$evento->categoriasPlan.")";
				}
				else
				{
					foreach ($validos as $key => $value)
					{
						$add = $eventos->agregarCategorias($value);
					}
				}
			}

			if(!empty($info['mensajes']))
			{
				self::crearEventoCategorias($evento->id, $info);
			}
			else
			{
				if($evento->disciplina != 9)
				{
					$eventos->setPasoCreacion($evento->id, 2);
					Url::redirect('cuenta/crearevento/crearWods/'.$evento->internalURL);
				}
				else
				{
					$dataDB = array();
					$dataDB['nombre'] = "SNATCH";
					$dataDB['descripcion'] = "3 Intentos para sacar 1 RM SNATCH";
					$dataDB['tipo'] = 4;
					$dataDB['eventoId'] = $eventoId;
					$addSnatch = $eventos->agregarWods($dataDB);

					$dataDB = array();
					$dataDB['nombre'] = "CLEAN & JERK";
					$dataDB['descripcion'] = "3 Intentos para sacar 1 RM CLEAN & JERK";
					$dataDB['tipo'] = 4;
					$dataDB['eventoId'] = $eventoId;
					$addClean = $eventos->agregarWods($dataDB);

					$cate = $eventos->getCategorias($evento->id);

					foreach ($cate as $keyC => $value)
					{
						$dataDB = array();
						$dataDB['categoriaId'] = $keyC;
						$dataDB['wodId'] = $addSnatch;
						$dataDB['eventoId'] = $evento->id;
						$dataDB['orden'] = 1;
						$eventos->agregarRelaciones($dataDB);

						$dataDB = array();
						$dataDB['categoriaId'] = $keyC;
						$dataDB['wodId'] = $addClean;
						$dataDB['eventoId'] = $evento->id;
						$dataDB['orden'] = 2;
						$eventos->agregarRelaciones($dataDB);
					}

					$cuentas = new Models\Cuentas();
					$organizador = $cuentas->getPersona($evento->creadorId);

					if($organizador)
					{
						$gestor = fopen("app/templates/mail/mail.html", "r");
						$txt = "";

						while (!feof($gestor))
						{
							$line_of_text = fgets($gestor);
							$txt .= $line_of_text;
						}
						fclose($gestor);

						$bdy = "Se registro un evento nuevo en la web: \"".$evento->nombre."\" en la disciplina: ".Codes::getEventoNombre($evento->disciplina).". Creado por: \"".$organizador->nombre." ".$organizador->apellido."\" (".$organizador->correo.")<br/><br/>Informacion del evento ".$evento->nombre."<br/><br/><b>Fecha: </b>".$evento->fechaStr."<br/><b>Web del evento: </b> <a href='".$evento->urlEvento."'>".$evento->urlEvento."</a><br/><br/>Para mayor informacion: <br/><b>Organizador:</b> ".$organizador->nombre." ".$organizador->apellido."<br/><b>Correo:</b> <a href='mailto:".$organizador->correo."'>".$organizador->correo."</a><br/><b>Telefono:</b>".$organizador->telefono_1."<br/><br/><a href=\"".DIR."evento/".$evento->id."\">Ver evento</a>";
						$strMail = str_replace("[MENSAJE]", $bdy, $txt);
						$strMail = str_replace("[LOGO1]", DIR."app/templates/<?php echo TEMPLATE;?>/img/logoCuadrado.png", $strMail);

						$mail = new \Helpers\PhpMailer\Mail();
						$mail->setFrom(SITEEMAIL);
						$mail->addAddress(ADMINNOTIF);
						$mail->subject('Evento nuevo en Pukiebook: '.$evento->nombre);
						$mail->body($strMail);
						$mail->send();
					}

					$eventos->setPasoCreacion($evento->id, 3);
					$eventos->finalizaCreacion($evento->id);
					
					Url::redirect('cuenta/modevento/'.$evento->internalURL."/info");
				}
			}
		}
	}

	public function crearEventoWods($eventoId, $info = array())
	{
		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			self::eventoNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$permisoC = self::checkPermisoCreacion($evento, $logeado);

		if(!$permisoC)
		{
			Cuenta::sectionNotPermission();
			return;
		}

		self::comprobarPasoCreacion($evento, 2);
		
		$cuentas = new Models\Cuentas();

		$data['title'] = "Agregar WoDs";
		$data['csrf_token'] = Csrf::makeToken();
		$data['evento'] = $evento;
		$data['logeado'] = $logeado;

		if(empty($info))
		{
			$info['data'] = array();
			$info['mensajes'] = array();
		}

		View::renderTemplate('header', $data);
		View::render('tracker/evento/crearWods', $data, $info);
		View::renderTemplate('footer', $data);
	}

	public function crearEventoWodsPOST($eventoId)
	{
		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			self::eventoNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$permisoC = self::checkPermisoCreacion($evento, $logeado);

		if(!$permisoC)
		{
			Cuenta::sectionNotPermission();
			return;
		}

		self::comprobarPasoCreacion($evento, 2);

		if (!Csrf::isTokenValid())
		{
			Cuenta::sectionCheckInvalid();
			return;
		}

		if(isset($_POST['submit']))
		{
			$data = array();
			$validos = array();
			$info = array();

			$info['data'] = array();
			$info['mensajes'] = array();

			foreach ($_POST['nombres'] as $key => $value)
			{
				$data[$key]['nombre'] = $value;
			}

			foreach ($_POST['tipos'] as $key => $value)
			{
				if(empty($value))
					$value = 0;

				$data[$key]['tipo'] = $value;
			}

			foreach ($_POST['descripciones'] as $key => $value)
			{
				$data[$key]['descripcion'] = $value;
			}

			foreach ($_POST['repsRounds'] as $key => $value)
			{
				if(empty($value))
					$value = 0;
				
				$data[$key]['repsRound'] = $value;
			}

			if(isset($_POST['permPenalizaciones']))
			{
				foreach ($_POST['permPenalizaciones'] as $key => $value)
				{
					if(empty($value))
						$value = 0;

					$data[$key]['permPenalizacion'] = $value;
				}
			}

			if(isset($_POST['permTieBreak']))
			{
				foreach ($_POST['permTieBreak'] as $key => $value)
				{
					if(empty($value))
						$value = 0;

					$data[$key]['permTieBreak'] = $value;
				}
			}

			foreach ($_POST['horas'] as $key => $value)
			{
				if(empty($value))
					$value = 0;
				
				$data[$key]['horas'] = $value;
			}

			foreach ($_POST['minutos'] as $key => $value)
			{
				if(empty($value))
					$value = 0;
				
				$data[$key]['minutos'] = $value;
			}

			foreach ($_POST['segundos'] as $key => $value)
			{
				if(empty($value))
					$value = 0;
				
				$data[$key]['segundos'] = $value;
			}

			if(isset($_POST['pesoCorporal']))
			{
				foreach ($_POST['pesoCorporal'] as $key => $value)
				{
					if(empty($value))
						$value = 0;
					
					$data[$key]['pesoCorporal'] = $value;
				}
			}

			if(isset($_POST['diaA']))
			{
				foreach ($_POST['diaA'] as $key => $value)
				{
					if(empty($value))
						$value = 0;
					
					$data[$key]['diaA'] = $value;
				}
			}

			if(isset($_POST['mesA']))
			{
				foreach ($_POST['mesA'] as $key => $value)
				{
					if(empty($value))
						$value = 0;
					
					$data[$key]['mesA'] = $value;
				}
			}

			if(isset($_POST['annoA']))
			{
				foreach ($_POST['annoA'] as $key => $value)
				{
					if(empty($value))
						$value = 0;
					
					$data[$key]['annoA'] = $value;
				}
			}

			if(isset($_POST['horaA']))
			{
				foreach ($_POST['horaA'] as $key => $value)
				{
					if(empty($value))
						$value = 0;
					
					$data[$key]['horaA'] = $value;
				}
			}

			if(isset($_POST['minutoA']))
			{
				foreach ($_POST['minutoA'] as $key => $value)
				{
					if(empty($value))
						$value = 0;
					
					$data[$key]['minutoA'] = $value;
				}
			}

			if(isset($_POST['diaC']))
			{
				foreach ($_POST['diaC'] as $key => $value)
				{
					if(empty($value))
						$value = 0;
					
					$data[$key]['diaC'] = $value;
				}
			}

			if(isset($_POST['mesC']))
			{
				foreach ($_POST['mesC'] as $key => $value)
				{
					if(empty($value))
						$value = 0;
					
					$data[$key]['mesC'] = $value;
				}
			}

			if(isset($_POST['annoC']))
			{
				foreach ($_POST['annoC'] as $key => $value)
				{
					if(empty($value))
						$value = 0;
					
					$data[$key]['annoC'] = $value;
				}
			}

			if(isset($_POST['horaC']))
			{
				foreach ($_POST['horaC'] as $key => $value)
				{
					if(empty($value))
						$value = 0;
					
					$data[$key]['horaC'] = $value;
				}
			}

			if(isset($_POST['minutoC']))
			{
				foreach ($_POST['minutoC'] as $key => $value)
				{
					if(empty($value))
						$value = 0;
					
					$data[$key]['minutoC'] = $value;
				}
			}

			foreach ($data as $key => $value)
			{
				$valueT = trim($value['nombre']);

				if(!empty($valueT) || !empty($value['tipo']) )
				{
					$dataDB 						= array();
					$dataDB['eventoId'] 			= $eventoId;
					$dataDB['nombre'] 				= $valueT;
					$dataDB['tipo'] 				= $value['tipo'];
					$dataDB['repsRound'] 			= $value['repsRound'];
					$dataDB['permPenalizacion'] 	= $value['permPenalizacion'];
					$dataDB['permTieBreak'] 		= $value['permTieBreak'];
					$dataDB['apertura'] 			= 0;
					$dataDB['cierre'] 				= 0;
					$dataDB['timeCap'] 				= 0;

					$lines = array_filter(explode(PHP_EOL, trim($value['descripcion'])));

					foreach ($lines as $keyA => &$valueA)
					{
						$valueA = trim($valueA);
					}
					unset($valueA);

					$dataDB['descripcion'] = implode("<br/>", $lines);

					$validator 				= array();
					$validator['nombre'] 	= 'required';
					$validator['tipo'] 		= 'required';

					if($value['tipo'] == 3)
					{
						$validator['repsRound'] 	= 'numeric|min_numeric,0';

						if($value['permPenalizacion'])
						{
							$validator['repsRound'] 	= 'required|numeric|min_numeric,1';
							$validator['horas']			= 'numeric|min_numeric,0';
							$validator['minutos']	  	= 'numeric|min_numeric,0|max_numeric,59';
							$validator['segundos']	 	= 'numeric|min_numeric,0|max_numeric,59';

							$timeCap = ($value['horas'] * 3600) + ($value['minutos'] * 60) + $value['segundos'];
							$dataDB['timeCap'] = $timeCap;
						}
					}

					if($value['tipo'] == 4)
					{
						$dataDB['permTieBreak'] 		= $value['tieBreak'];
						$validator['repsRound'] 		= 'numeric|min_numeric,0';
						$validator['horas'] 			= 'numeric|min_numeric,0';
						$validator['minutos'] 			= 'numeric|min_numeric,0|max_numeric,59';
						$validator['segundos'] 			= 'numeric|min_numeric,0|max_numeric,59';

						$time = ($value['horas'] * 3600) + ($value['minutos'] * 60) + $value['segundos'];
						$dataDB['time'] = $time;
					}

					if($value['tipo'] == 9)
					{
						$dataDB['permTieBreak'] 	= $value['pesoCorporal'];
					}

					if($evento->disciplina == 10)
					{
						$validator['diaA'] 		= 'required|numeric|min_numeric,1';
						$validator['mesA'] 		= 'required|numeric|min_numeric,1|max_numeric,12';
						$validator['annoA'] 	= 'required|numeric|min_numeric,2016';
						$validator['horaA'] 	= 'required|numeric|min_numeric,0';
						$validator['minutoA'] 	= 'required|numeric|min_numeric,0|max_numeric,59';

						$validator['diaC'] 		= 'required|numeric|min_numeric,1';
						$validator['mesC'] 		= 'required|numeric|min_numeric,1|max_numeric,12';
						$validator['annoC'] 	= 'required|numeric|min_numeric,2016';
						$validator['horaC'] 	= 'required|numeric|min_numeric,0';
						$validator['minutoC'] 	= 'required|numeric|min_numeric,0|max_numeric,59';
					}

					$gumpValidator = new Gump();
					$is_valid = $gumpValidator->validate($value, $validator);
					
					if( ($value['tipo'] == 3) && $value['permPenalizacion'] && ($dataDB['timeCap'] <= 0) )
					{
						$info['mensajes'][$key][] = "Debe ingresar un tiempo para el Time Cap.";
						$is_valid = false;
					}

					if($evento->disciplina == 10)
					{
						$fechaAtmp = 0;

						if(checkdate($value['mesA'], $value['diaA'], $value['annoA']))
						{
							$fecha = strtotime($value['annoA']."-".$value['mesA']."-".$value['diaA']." ".date("H:i", $evento->fecha));
							$fechaAtmp = $fecha;

							if($fecha < $evento->fecha)
							{
								$info['mensajes'][$key][] = "Apertura de WOD no v&aacute;lida (Menor a fecha del evento).";
								$is_valid = false;
							}
							else
								$dataDB['apertura'] = $fecha;
						}
						else
						{
							$info['mensajes'][$key][] = "Apertura de WOD no v&aacute;lida (Fecha incorrecta).";
							$is_valid = false;
						}					

						if(checkdate($value['mesC'], $value['diaC'], $value['annoC']))
						{
							$fecha = strtotime($value['annoC']."-".$value['mesC']."-".$value['diaC']." ".date("H:i", $evento->fecha));

							if($fecha < $evento->fecha)
							{
								$info['mensajes'][$key][] = "Cierre de WOD no v&aacute;lido.";
								$is_valid = false;
							}
							else
							{
								if($fecha <= $fechaAtmp)
								{
									$info['mensajes'][$key][] = "Cierre de WOD no v&aacute;lido.";
									$is_valid = false;
								}
								else
									$dataDB['cierre'] = $fecha;
							}
								
						}
						else
						{
							$info['mensajes'][$key][] = "Cierre de WOD no v&aacute;lido.";
							$is_valid = false;
						}
					}


					if( ($is_valid !== true) )
					{						
						$campos = $gumpValidator->errors();
						$info['campos'][$key] = $campos;

						if(isset($campos['repsRound']))
							$info['mensajes'][$key][] = "Compruebe el valor ingresado en Repeticiones, debe ser mayor a 0 si usa Time Cap.";

						if(isset($campos['nombre']))
							$info['mensajes'][$key][] = "Debe especificar un nombre.";

						if(isset($campos['tipo']))
							$info['mensajes'][$key][] = "Debe escoger el tipo de WOD.";

						if(isset($campos['horas']))
							$info['mensajes'][$key][] = "Compruebe el valor ingresado en Horas.";

						if(isset($campos['minutos']))
							$info['mensajes'][$key][] = "Compruebe el valor ingresado en Minutos.";

						if(isset($campos['segundos']))
							$info['mensajes'][$key][] = "Compruebe el valor ingresado en Segundos.";

						if(isset($campos['annoA']) || isset($campos['mesA']) || isset($campos['diaA']) || isset($campos['horaA']) || isset($campos['minutoA']) )
							$info['mensajes'][$key][] = "Compruebe la fecha de Apertura.";

						if(isset($campos['annoC']) || isset($campos['mesC']) || isset($campos['diaC']) || isset($campos['horaC']) || isset($campos['minutoC']) )
							$info['mensajes'][$key][] = "Compruebe la fecha de Cierre.";

						$info['data'][$key] = $data[$key];
					}
					else
					{
						$validos[] = $dataDB;
					}
				}
			}

			if(empty($validos))
			{
				$info['mensajes']['general'][] = "Debe especificar al menos un WOD correctamente y definir su tipo.";
			}
			else
			{
				foreach ($validos as $key => $value)
				{
					$add = $eventos->agregarWods($value);
				}
			}

			if(!empty($info['mensajes']))
			{
				self::crearEventoWods($evento->id, $info);
			}
			else
			{
				$eventos->setPasoCreacion($evento->id, 3);
				Url::redirect('cuenta/crearevento/crearRelaciones/'.$evento->internalURL);
			}
		}
	}

	public function crearEventoRelaciones($eventoId, $info = array())
	{
		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			self::eventoNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$permisoC = self::checkPermisoCreacion($evento, $logeado);

		if(!$permisoC)
		{
			Cuenta::sectionNotPermission();
			return;
		}

		self::comprobarPasoCreacion($evento, 3);

		$categorias = $eventos->getCategorias($evento->id);
		$wods = $eventos->getWODs($evento->id);

		$data['logeado'] = $logeado;
		$data['title'] = "Terminar Categorias";
		$data['csrf_token'] = Csrf::makeToken();
		$data['evento'] = $evento;
		$data['categorias'] = $categorias;
		$data['wods'] = $wods;

		View::renderTemplate('header', $data);
		View::render('tracker/evento/crearRelaciones', $data, $info);
		View::renderTemplate('footer', $data);
	}

	public function crearEventoRelacionesPOST($eventoId)
	{
		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			self::eventoNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$permisoC = self::checkPermisoCreacion($evento, $logeado);

		if(!$permisoC)
		{
			Cuenta::sectionNotPermission();
			return;
		}

		self::comprobarPasoCreacion($evento, 3);

		if (!Csrf::isTokenValid())
		{
			Cuenta::sectionCheckInvalid();
			return;
		}

		if(isset($_POST['submit']))
		{
			$validos = array();
			$info = array();
			$info['mensajes'] = array();

			if(isset($_POST['wodR']))
			{
				foreach ($_POST['wodR'] as $keyC => $value)
				{
					$pos = 1;
					foreach ($value as $key => $keyW)
					{
						$dataDB = array();
						$dataDB['categoriaId'] = $keyC;
						$dataDB['wodId'] = $keyW;
						$dataDB['eventoId'] = $evento->id;
						$dataDB['orden'] = $pos++;
						$validos[] = $dataDB;
					}
				}
			}

/*
			if(empty($validos))
			{
				$info['mensajes'][] = "Debe crear una relaci&oacute;n al menos.";
			}
			else
			{
				*/
				foreach ($validos as $key => $value)
				{
					$add = $eventos->agregarRelaciones($value);
				}
			//}

			if(!empty($info['mensajes']))
			{
				self::crearEventoRelaciones($evento->id, $info);
			}
			else
			{
				$cuentas = new Models\Cuentas();
				$organizador = $cuentas->getPersona($evento->creadorId);

				if($organizador)
				{
					$gestor = fopen("app/templates/mail/mail.html", "r");
					$txt = "";

					while (!feof($gestor))
					{
						$line_of_text = fgets($gestor);
						$txt .= $line_of_text;
					}
					fclose($gestor);

					$bdy = "Se registro un evento nuevo en la web: \"".$evento->nombre."\" en la disciplina: ".Codes::getEventoNombre($evento->disciplina).". Creado por: \"".$organizador->nombre." ".$organizador->apellido."\" (".$organizador->correo.")<br/><br/>Informacion del evento ".$evento->nombre."<br/><br/><b>Fecha: </b>".$evento->fechaStr."<br/><b>Web del evento: </b> <a href='".$evento->urlEvento."'>".$evento->urlEvento."</a><br/><br/>Para mayor informacion: <br/><b>Organizador:</b> ".$organizador->nombre." ".$organizador->apellido."<br/><b>Correo:</b> <a href='mailto:".$organizador->correo."'>".$organizador->correo."</a><br/><b>Telefono:</b>".$organizador->telefono_1."<br/><br/><a href=\"".DIR."evento/".$evento->id."\">Ver evento</a>";
					$strMail = str_replace("[MENSAJE]", $bdy, $txt);
					$strMail = str_replace("[LOGO1]", DIR."app/templates/<?php echo TEMPLATE;?>/img/logoCuadrado.png", $strMail);

					$mail = new \Helpers\PhpMailer\Mail();
					$mail->setFrom(SITEEMAIL);
					$mail->addAddress(ADMINNOTIF);
					$mail->subject('Evento nuevo en Pukiebook: '.$evento->nombre);
					$mail->body($strMail);
					$mail->send();
				}

				$eventos->setPasoCreacion($evento->id, 3);
				$eventos->finalizaCreacion($evento->id);
				
				Url::redirect('cuenta/modevento/'.$evento->internalURL."/info");
			}
		}
	}

	private function checkModificable($evento)
	{
		$pasoEvento = self::checkPasoCreacion($evento);

		if($pasoEvento > 0)
			self::continuarCreacion($evento->id, $pasoEvento);
	}

	public function modEventoInfo($eventoId, $info = array())
	{
		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			self::eventoNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$permisoC = self::checkPermisoCreacion($evento, $logeado);

		if(!$permisoC)
		{
			Cuenta::sectionNotPermission();
			return;
		}

		self::checkModificable($evento);

		$cuentas = new Models\Cuentas();

		$data['title'] = "Modificaci&oacute;n de Evento";
		$data['csrf_token'] = Csrf::makeToken();
		$data['evento'] = $evento;
		$data['logeado'] = $logeado;
		$web = new Models\Web();
		$data['zonas'] = $web->getZonasHorarias();

		if(empty($info))
		{
			$info['mensajes'] = array();
			$info['campos'] = array();

			$fechaUnix = $evento->fecha;
			if($evento->zonaHoraria < 0)
				$fechaUnix = $fechaUnix + $evento->zonaHoraria;
			else
				$fechaUnix = $fechaUnix - $evento->zonaHoraria;

			$info['data'] = array();
			$info['data']['nombre'] = $evento->nombre;
			$info['data']['urlEvento'] = $evento->urlEvento;
			$info['data']['fechaD'] = date('d', $fechaUnix);
			$info['data']['fechaM'] = date('m', $fechaUnix);
			$info['data']['fechaA'] = date('Y', $fechaUnix);
			$info['data']['horaH'] = date('G', $fechaUnix);
			$info['data']['horaM'] = date('i', $fechaUnix);
			$info['data']['customurl'] = $evento->customurl;
			$info['data']['zonaHoraria'] = $evento->zonaHoraria;
			$info['data']['disciplina'] = $evento->disciplina;
		}

		View::renderTemplate('header', $data);
		View::render('tracker/cuenta/modevento/info', $data, $info);
		View::renderTemplate('footer', $data);
	}

	public function modEventoInfoPOST($eventoId)
	{
		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			self::eventoNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$permisoC = self::checkPermisoCreacion($evento, $logeado);

		if(!$permisoC)
		{
			Cuenta::sectionNotPermission();
			return;
		}

		self::checkModificable($evento);

		if (!Csrf::isTokenValid())
		{
			Cuenta::sectionCheckInvalid();
			return;
		}

		if(isset($_POST['submit']))
		{
			$nombre 			= filter_input(INPUT_POST, 'nombre');
			$fechaD 			= filter_input(INPUT_POST, 'fechaD');
			$fechaM 			= filter_input(INPUT_POST, 'fechaM');
			$fechaA 			= filter_input(INPUT_POST, 'fechaA');
			$zonaHoraria 		= filter_input(INPUT_POST, 'zonaHoraria');
			$horaH 				= filter_input(INPUT_POST, 'horaH');
			$horaM 				= filter_input(INPUT_POST, 'horaM');
			$urlEvento 			= filter_input(INPUT_POST, 'urlEvento');
			$customurl 			= filter_input(INPUT_POST, 'customurl');
			$disciplina 		= filter_input(INPUT_POST, 'disciplina');
			$permiteRegistros 	= 0;
			$visible 			= 0;
			$wodsvisible 		= 0;
			$showVideo	 		= 0;
			$reqPago 			= 0;
			$removerLogo 		= 0;
			$tipoRegistro 		= 'I';
	
			if(isset($_POST['removerLogo']))
				$removerLogo = 1;
			
			if(isset($_POST['permiteRegistros']))
				$permiteRegistros = 1;

			if(isset($_POST['visible']))
				$visible = 1;
			
			if(isset($_POST['wodsvisible']))
				$wodsvisible = 1;

			if(isset($_POST['showVideo']))
				$showVideo = 1;

			if(isset($_POST['reqPago']))
				$reqPago = 1;

			if(isset($_POST['teamOpen']))
				$tipoRegistro = 'E';
		
			if($urlEvento == "http://")
			{
				$_POST['urlEvento'] = "";
				$urlEvento = "";
			}

			$valores 						= array();
			$valores['nombre']				= $nombre;
			$valores['urlEvento'] 			= $urlEvento;
			$valores['fechaD'] 				= $fechaD;
			$valores['fechaM'] 				= $fechaM;
			$valores['fechaA'] 				= $fechaA;
			$valores['zonaHoraria'] 		= $zonaHoraria;
			$valores['horaH'] 				= $horaH;
			$valores['horaM'] 				= $horaM;
			$valores['permiteRegistros'] 	= $permiteRegistros;
			$valores['visible'] 			= $visible;
			$valores['wodsvisible'] 		= $wodsvisible;
			$valores['reqPago'] 			= $reqPago;
			$valores['customurl'] 			= $customurl;
			$valores['disciplina'] 			= $disciplina;
			$valores['tipoRegistro'] 		= $tipoRegistro;

			$info['data'] = $valores;

			$validator = array();
			$validator['nombre'] = 'required';
			$validator['urlEvento'] = 'valid_url';
			$validator['fechaD'] = 'required|numeric|min_numeric,1';
			$validator['fechaM'] = 'required|numeric|min_numeric,1';
			$validator['fechaA'] = 'required|numeric|min_numeric,1';
			$validator['zonaHoraria'] = 'required|numeric';
			$validator['horaH'] = 'required|min_numeric,0|max_numeric,23';
			$validator['horaM'] = 'required|min_numeric,0|max_numeric,59';
			$validator['customurl'] = 'alpha';

			$gumpValidator = new Gump();
			$is_valid = $gumpValidator->validate($_POST, $validator);
			
			if($is_valid !== true)
				$info['campos'] = $gumpValidator->errors();
			else
			{
				$fechaValida = checkdate($fechaM, $fechaD, $fechaA);

				if(!$fechaValida)
					$info['campos']['fechaEvento'] = true;
			}
			
			$nombreArchivo = $evento->id;
			$ext = null;
			$cuentas = new Models\Cuentas();

			if($removerLogo)
			{
				if($evento->archivoLogo)
				{
					if(file_exists("./".EVENTPICTUREPATH.$evento->archivoLogo))
						unlink("./".EVENTPICTUREPATH.$evento->archivoLogo);

					$data['archivoLogo'] = null;
					$res = $eventos->actualizarEventoInfo($data, array('id' => $evento->id));
				}
			}
			else
			{
				$archivo = $_FILES['archivoLogo'];

				if($archivo['error'] == UPLOAD_ERR_OK)
				{
					if($archivo['type'] == "image/jpeg")
						$ext = "jpg";
					else if($archivo['type'] == "image/png")
						$ext = "png";

					if($ext)
					{
						if($evento->archivoLogo)
						{
							if(file_exists("./".EVENTPICTUREPATH.$evento->archivoLogo))
								unlink("./".EVENTPICTUREPATH.$evento->archivoLogo);
							
							$data['archivoLogo'] = null;
							$res = $eventos->actualizarEventoInfo($data, array('id' => $evento->id));
						}

						$proc = move_uploaded_file($_FILES['archivoLogo']['tmp_name'], "./".EVENTPICTUREPATH.$nombreArchivo.".".$ext);

						if($proc)
						{
							$eventos->actualizarEventoInfo(array('archivoLogo' => $nombreArchivo.".".$ext), array('id' => $evento->id));

	    					$resizer = new ResizeImage("./".EVENTPICTUREPATH.$nombreArchivo.".".$ext);
	    					$resizer->resizeTo(300, 300, 'maxHeight');
	    					$resizer->saveImage("./".EVENTPICTUREPATH.$nombreArchivo.".".$ext);
						}
						else
							$errorImagen = "Hubo un error cargando su im&aacute;gen de evento";
					}
					else
						$errorImagen = "Tipo de archivo de im&aacute;gen de evento no permitido. (Permitido: .jpg y .png)";
				}
				else
				{
					if($archivo['error'] != UPLOAD_ERR_NO_FILE)
						$errorImagen = "Tama&ntilde;o m&aacute;ximo de im&aacute;gen de evento: 2MB.";
				}
			}

			if(!empty($info['campos']) || !empty($info['mensajes']))
			{
				self::modEventoInfo($evento->id, $info);
				return;
			}
			else
			{
				$eventos = new Models\Eventos();

				if(!empty($customurl))
				{
					$registrada = $eventos->urlRegistrada($customurl);

					if(!empty($registrada) && ($registrada->id != $evento->id))
						$errorURL = "La url escogida ya se encuentra en uso. Intente con otro nombre.";
					else
						$new['customurl'] = strtolower($customurl);
				}
				else
				{
					$eventos->deleteUrl($evento->id);
				}

				$fecha = strtotime($fechaA."-".$fechaM."-".$fechaD." ".$horaH.":".$horaM);
				if($zonaHoraria < 0)
					$fecha = $fecha - $zonaHoraria;
				else
					$fecha = $fecha + $zonaHoraria;

				$new['nombre'] = $nombre;
				$new['urlEvento'] = $urlEvento;
				$new['fecha'] = $fecha;
				$new['zonaHoraria'] = $zonaHoraria;
				$new['permiteRegistros'] = $permiteRegistros;
				$new['visible'] = $visible;
				$new['wodsvisible'] = $wodsvisible;
				$new['showVideo'] = $showVideo;
				$new['reqPago'] = $reqPago;
				$new['tipoRegistro'] = $tipoRegistro;

				if($logeado['persona']->esAdmin)
					$new['disciplina'] = $disciplina;

				if(empty($customurl))
					$evento->internalURL = $evento->id;
				else
					$evento->internalURL = strtolower($customurl);
				
				$res = $eventos->actualizarEventoInfo($new, array('id' => $evento->id));

				if(isset($errorImagen) && !empty($errorImagen))
				{
					$data['title'] = "Error cargando im&aacute;gen";
					$data['titulo'] = "Error cargando im&aacute;gen";
					$data['mensaje'] = $errorImagen;
					$data['boton'] = "Volver";
					$data['href'] = DIR."cuenta/modevento/".$evento->internalURL."/info";	

					View::renderTemplate('header', $data);
					View::render('tracker/mensaje', $data);
					View::renderTemplate('footer', $data);

					return;
				}

				if(isset($errorURL) && !empty($errorURL))
				{
					$data['title'] = "Error escogiendo URL";
					$data['titulo'] = "Error escogiendo URL";
					$data['mensaje'] = $errorURL;
					$data['boton'] = "Volver";
					$data['href'] = DIR."cuenta/modevento/".$evento->internalURL."/info";	

					View::renderTemplate('header', $data);
					View::render('tracker/mensaje', $data);
					View::renderTemplate('footer', $data);

					return;
				}

				Url::redirect('evento/'.$evento->internalURL);
			}
		}
	}

	public function modEventoCategorias($eventoId, $info = array())
	{
		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			self::eventoNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$permisoC = self::checkPermisoCreacion($evento, $logeado);

		if(!$permisoC)
		{
			Cuenta::sectionNotPermission();
			return;
		}

		self::checkModificable($evento);

		$categorias = $eventos->getCategorias($evento->id);
		$cuentas = new Models\Cuentas();

		$data['title'] = "Modificar Categorias";
		$data['csrf_token'] = Csrf::makeToken();
		$data['evento'] = $evento;
		$data['categorias'] = $categorias;
		$data['logeado'] = $logeado;

		if(empty($info))
		{
			$info['mensajes'] = array();
			$info['data'] = array();
		}

		View::renderTemplate('header', $data);
		View::render('tracker/cuenta/modevento/categorias', $data, $info);
		View::renderTemplate('footer', $data);
	}

	public function modEventoCategoriasPOST($eventoId)
	{
		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			self::eventoNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$permisoC = self::checkPermisoCreacion($evento, $logeado);

		if(!$permisoC)
		{
			Cuenta::sectionNotPermission();
			return;
		}

		self::checkModificable($evento);

		if (!Csrf::isTokenValid())
		{
			Cuenta::sectionCheckInvalid();
			return;
		}

		if(isset($_POST['submit']))
		{
			$categorias = $eventos->getCategorias($evento->id);

			$toUpdate = array();
			$toInsert = array();
			$toDelete = array();

			$info = array();

			foreach ($_POST['categorias'] as $keyC => $valueC)
			{
				if(isset($categorias[$keyC]))
				{
					if(isset($_POST['borrar'][$keyC]))
						$toDelete[$keyC] = $valueC;
					else
					{
						if(!empty($valueC))
							$toUpdate[$keyC] = $valueC;
					}
				}
				else
				{
					if(!empty($valueC))
						$toInsert[] = $valueC;
				}
			}

			$toUpdateCant = count($toUpdate);
			$toInsertCant = count($toInsert);
			$toDeleteCant = count($toDelete);
			$finalCant = $toUpdateCant + $toInsertCant - $toDeleteCant;

			$cuentas = new Models\Cuentas();

			if($finalCant > $evento->categoriasPlan)
			{
				$info['mensajes'][] = "Ha superado la cantidad de categor&iacute;as permitidas. (Max: ".$evento->categoriasPlan.")";
			}

			if(empty($info['mensajes']))
			{
				foreach ($toUpdate as $key => $value)
				{
					$valueT = trim($value);

					if(!empty($valueT))
					{
						$valueD = array('nombre' => $valueT);
						$eventos->actualizarCategoria($key, $valueD);
					}
				}

				foreach ($toInsert as $key => $value)
				{
					$valueT = trim($value);

					if(!empty($valueT))
					{
						$dataDB = array();
						$dataDB['nombre'] = $valueT;
						$dataDB['eventoId'] = $evento->id;
						$add = $eventos->agregarCategorias($dataDB);
					}
				}

				$eventos->borrarCategorias($toDelete);

				Url::redirect('evento/'.$evento->internalURL);
			}
			else
			{
				self::modEventoCategorias($evento->id, $info);
			}
		}
	}


	public function modEventoWods($eventoId, $info = array())
	{
		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			self::eventoNotFound();
			return;
		}

		if($evento->disciplina == 9)
		{
			Cuenta::sectionNotPermission();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$permisoC = self::checkPermisoCreacion($evento, $logeado);

		if(!$permisoC)
		{
			Cuenta::sectionNotPermission();
			return;
		}

		self::checkModificable($evento);

		$wods = $eventos->getWODs($evento->id);
		$cuentas = new Models\Cuentas();

		foreach ($wods as $key => &$value)
		{
			$lines = array_filter(explode("<br/>", $value->descripcion));
			
			foreach ($lines as $keyA => &$valueA)
			{
				$valueA = trim($valueA);
			}
			unset($valueA);

			$value->descripcion = implode(PHP_EOL, $lines);

			if($value->tipo == 3)
			{
				$seg = 0;
				$min = 0;
				$hor = 0;
				$tiempo = $value->timeCap;

				$seg = $tiempo % 60;
				$tiempo = ($tiempo - $seg) / 60;
				$min = $tiempo % 60;
				$hor = ($tiempo - $min) / 60;

				$value->segundos = $seg;
				$value->minutos = $min;
				$value->horas = $hor;
			}

			if($value->tipo == 4)
			{
				$seg = 0;
				$min = 0;
				$hor = 0;
				$tiempo = $value->time;

				$seg = $tiempo % 60;
				$tiempo = ($tiempo - $seg) / 60;
				$min = $tiempo % 60;
				$hor = ($tiempo - $min) / 60;

				$value->segundos = $seg;
				$value->minutos = $min;
				$value->horas = $hor;
			}

			if($evento->disciplina == 10)
			{
				$fecha = $value->apertura;
				$value->diaA = date("d", $fecha);
				$value->mesA = date("m", $fecha);
				$value->annoA = date("Y", $fecha);
				$value->horaA = date("H", $fecha);
				$value->minutoA = date("i", $fecha);

				$fechaC = $value->cierre;
				$value->diaC = date("d", $fechaC);
				$value->mesC = date("m", $fechaC);
				$value->annoC = date("Y", $fechaC);
				$value->horaC = date("H", $fechaC);
				$value->minutoC = date("i", $fechaC);
			}
		}
		unset($value);

		if(empty($info))
		{
			$info['mensajes'] = array();
			$info['campos'] = array();
		}

		$data['title'] = "Modificar WoDs";
		$data['csrf_token'] = Csrf::makeToken();
		$data['evento'] = $evento;
		$data['wods'] = $wods;
		$data['logeado'] = $logeado;

		View::renderTemplate('header', $data);
		View::render('tracker/cuenta/modevento/wods', $data, $info);
		View::renderTemplate('footer', $data);
	}

	public function modEventoWodsPOST($eventoId)
	{
		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			self::eventoNotFound();
			return;
		}

		if($evento->disciplina == 9)
		{
			Cuenta::sectionNotPermission();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$permisoC = self::checkPermisoCreacion($evento, $logeado);

		if(!$permisoC)
		{
			Cuenta::sectionNotPermission();
			return;
		}

		self::checkModificable($evento);

		if (!Csrf::isTokenValid())
		{
			Cuenta::sectionCheckInvalid();
			return;
		}

		if(isset($_POST['submit']))
		{
			$wods = $eventos->getWODs($evento->id);

			foreach ($_POST['wod'] as $key => $value)
			{
				if(empty($_POST['tipos'][$key]))
					$_POST['tipos'][$key] = 0;

				if(empty($_POST['repsRounds'][$key]))
					$_POST['repsRounds'][$key] = 0;

				if(empty($_POST['horas'][$key]))
					$_POST['horas'][$key] = 0;

				if(empty($_POST['minutos'][$key]))
					$_POST['minutos'][$key] = 0;

				if(empty($_POST['segundos'][$key]))
					$_POST['segundos'][$key] = 0;

				if(empty($_POST['diaA'][$key]))
					$_POST['diaA'][$key] = 0;

				if(empty($_POST['mesA'][$key]))
					$_POST['mesA'][$key] = 0;

				if(empty($_POST['annoA'][$key]))
					$_POST['annoA'][$key] = 0;

				if(empty($_POST['horaA'][$key]))
					$_POST['horaA'][$key] = 0;

				if(empty($_POST['minutoA'][$key]))
					$_POST['minutosA'][$key] = 0;

				if(empty($_POST['diaC'][$key]))
					$_POST['diaC'][$key] = 0;

				if(empty($_POST['mesC'][$key]))
					$_POST['mesC'][$key] = 0;

				if(empty($_POST['annoC'][$key]))
					$_POST['annoC'][$key] = 0;

				if(empty($_POST['horaC'][$key]))
					$_POST['horaC'][$key] = 0;

				if(empty($_POST['minutoC'][$key]))
					$_POST['minutosC'][$key] = 0;

				if(empty($_POST['pesoCorporal'][$key]))
					$_POST['pesoCorporal'][$key] = 0;

				if(empty($_POST['permPenalizaciones'][$key]))
					$_POST['permPenalizaciones'][$key] = 0;

				if(empty($_POST['permTieBreak'][$key]))
					$_POST['permTieBreak'][$key] = 0;
			}

			$pWods 			= $_POST['nombres'];
			$pDescripciones = $_POST['descripciones'];
			$pTipos 		= $_POST['tipos'];
			$repsRound 		= $_POST['repsRounds'];
			$penal 			= $_POST['permPenalizaciones'];
			$tie 			= $_POST['permTieBreak'];
			$peso 			= $_POST['pesoCorporal'];
			$diaA 			= $_POST['diaA'];
			$mesA 			= $_POST['mesA'];
			$annoA 			= $_POST['annoA'];
			$horaA 			= $_POST['horaA'];
			$minutoA 		= $_POST['minutoA'];
			$diaC 			= $_POST['diaC'];
			$mesC 			= $_POST['mesC'];
			$annoC 			= $_POST['annoC'];
			$horaC 			= $_POST['horaC'];
			$minutoC 		= $_POST['minutoC'];
			$horas 			= $_POST['horas'];
			$minutos 		= $_POST['minutos'];
			$segundos 		= $_POST['segundos'];
			$pBorrar 		= $_POST['borrar'];

			$toUpdate = array();
			$toInsert = array();
			$toDelete = array();

			foreach ($pDescripciones as $key => &$value) 
			{
				$lines = array_filter(explode(PHP_EOL, $value));
				foreach ($lines as $keyA => &$valueA)
				{
					$valueA = trim($valueA);
				}
				unset($valueA);
				$value = implode("<br/>", $lines);
			}
			unset($value);

			foreach ($_POST['nombres'] as $keyC => $valueC)
			{
				if(isset($wods[$keyC]))
				{
					if(isset($_POST['borrar'][$keyC]))
						$toDelete[$keyC] = $valueC;
					else
					{
						if(!empty($valueC))
						{
							$toUpdate[$keyC] = $valueC;
						}
					}
				}
				else
				{
					if(!empty($valueC) || !empty($pTipos[$keyC]) )
					{
						$toInsert[$keyC] = $valueC;
					}
				}
			}

			foreach ($toUpdate as $key => $value)
			{
				$valueD = array(
					'nombre' 			=> $pWods[$key],
					'descripcion' 		=> $pDescripciones[$key],
					'tipo' 				=> $pTipos[$key],
					'repsRound' 		=> $repsRound[$key],
					'permPenalizacion'  => $penal[$key],
					'permTieBreak' 		=> $tie[$key],
					'horas' 			=> $horas[$key],
					'minutos' 			=> $minutos[$key],
					'segundos' 			=> $segundos[$key],
					'diaA' 				=> $diaA[$key],
					'mesA' 				=> $mesA[$key],
					'annoA'				=> $annoA[$key],
					'horaA' 			=> $horaA[$key],
					'minutoA'			=> $minutoA[$key],
					'diaC' 				=> $diaC[$key],
					'mesC' 				=> $mesC[$key],
					'annoC'				=> $annoC[$key],
					'horaC' 			=> $horaC[$key],
					'minutoC'			=> $minutoC[$key],
					'apertura'			=> 0,
					'cierre'			=> 0,
					'eventoId' 			=> $evento->id);

				$validator 				= array();
				$validator['nombre'] 	= 'required';
				$validator['tipo'] 	  	= 'required';

				if($pTipos[$key] == 3)
				{
					$validator['repsRound'] 	  = 'numeric|min_numeric,0';
					$timeCap = 0;

					if($penal[$key])
					{
						$validator['repsRound'] = 'required|numeric|min_numeric,1';
						$validator['horas'] 	= 'numeric|min_numeric,0';
						$validator['minutos'] 	= 'numeric|min_numeric,0|max_numeric,59';
						$validator['segundos'] 	= 'numeric|min_numeric,0|max_numeric,59';

						$timeCap = ($horas[$key] * 3600) + ($minutos[$key] * 60) + $segundos[$key];
					}

					$valueD['timeCap'] = $timeCap;
				}

				if($pTipos[$key] == 4)
				{
					$valueD['permTieBreak'] 	= $tie[$key];

					$validator['repsRound'] 	= 'numeric|min_numeric,0';
					$validator['horas'] 		= 'numeric|min_numeric,0';
					$validator['minutos'] 		= 'numeric|min_numeric,0|max_numeric,59';
					$validator['segundos'] 		= 'numeric|min_numeric,0|max_numeric,59';

					$time = ($horas[$key] * 3600) + ($minutos[$key] * 60) + $segundos[$key];
					$valueD['time'] = $time;
				}

				if($pTipos[$key] == 9)
				{
					$valueD['permTieBreak'] = $peso[$key];
				}

				if($evento->disciplina == 10)
				{
					$validator['diaA'] 		= 'required|numeric|min_numeric,1';
					$validator['mesA'] 		= 'required|numeric|min_numeric,1|max_numeric,12';
					$validator['annoA'] 	= 'required|numeric|min_numeric,2016';
					$validator['horaA'] 	= 'required|numeric|min_numeric,0|max_numeric,23';
					$validator['minutoA'] 	= 'required|numeric|min_numeric,0|max_numeric,59';

					$validator['diaC'] 		= 'required|numeric|min_numeric,1';
					$validator['mesC'] 		= 'required|numeric|min_numeric,1|max_numeric,12';
					$validator['annoC'] 	= 'required|numeric|min_numeric,2016';
					$validator['horaC'] 	= 'required|numeric|min_numeric,0|max_numeric,23';
					$validator['minutoC'] 	= 'required|numeric|min_numeric,0|max_numeric,59';
				}

				$gumpValidator = new Gump();
				$is_valid = $gumpValidator->validate($valueD, $validator);

				if( ($pTipos[$key] == 3) && $penal[$key] && ($valueD['timeCap'] <= 0) )
				{
					$info['mensajes'][$key][] = "Debe ingresar un tiempo para el Time Cap.";
					$is_valid = false;
				}

				if($evento->disciplina == 10)
				{
					$fechaAtmp = 0;

					if(checkdate($mesA[$key], $diaA[$key], $annoA[$key]))
					{
						$fecha = strtotime($annoA[$key]."-".$mesA[$key]."-".$diaA[$key]." ".date("H:i", $evento->fecha));
						$fechaAtmp = $fecha;

						if($fecha < $evento->fecha)
						{
							$info['mensajes'][$key][] = "Apertura de WOD no v&aacute;lida (Menor a fecha del evento).";
							$is_valid = false;
						}
						else
							$valueD['apertura'] = $fecha;
					}
					else
					{
						$info['mensajes'][$key][] = "Apertura de WOD no v&aacute;lida (Fecha incorrecta).";
						$is_valid = false;
					}					

					if(checkdate($mesC[$key], $diaC[$key], $annoC[$key]))
					{
						$fecha = strtotime($annoC[$key]."-".$mesC[$key]."-".$diaC[$key]." ".date("H:i", $evento->fecha));

						if($fecha < $evento->fecha)
						{
							$info['mensajes'][$key][] = "Cierre de WOD no v&aacute;lido.";
							$is_valid = false;
						}
						else
						{
							if($fecha <= $fechaAtmp)
							{
								$info['mensajes'][$key][] = "Cierre de WOD no v&aacute;lido.";
								$is_valid = false;
							}
							else
								$valueD['cierre'] = $fecha;
						}
							
					}
					else
					{
						$info['mensajes'][$key][] = "Cierre de WOD no v&aacute;lido.";
						$is_valid = false;
					}
				}

				if($is_valid !== true)
				{
					$campos = $gumpValidator->errors();
					$info['campos'][$key] = $campos;

					if(isset($campos['repsRound']))
						$info['mensajes'][$key][] = "Compruebe el valor ingresado en Repeticiones, debe ser mayor a 0 si usa Time Cap.";

					if(isset($campos['nombre']))
						$info['mensajes'][$key][] = "Debe especificar un nombre.";

					if(isset($campos['tipo']))
						$info['mensajes'][$key][] = "Debe escoger el tipo de WOD.";

					if(isset($campos['horas']))
						$info['mensajes'][$key][] = "Compruebe el valor ingresado en Horas.";

					if(isset($campos['minutos']))
						$info['mensajes'][$key][] = "Compruebe el valor ingresado en Minutos.";

					if(isset($campos['segundos']))
						$info['mensajes'][$key][] = "Compruebe el valor ingresado en Segundos.";

					if(isset($campos['annoA']) || isset($campos['mesA']) || isset($campos['diaA']) || isset($campos['horaA']) || isset($campos['minutoA']) )
						$info['mensajes'][$key][] = "Compruebe la fecha de Apertura.";

					if(isset($campos['annoC']) || isset($campos['mesC']) || isset($campos['diaC']) || isset($campos['horaC']) || isset($campos['minutoC']) )
						$info['mensajes'][$key][] = "Compruebe la fecha de Cierre.";
				}
				else
				{
					unset($valueD['horas']);
					unset($valueD['minutos']);
					unset($valueD['segundos']);
					unset($valueD['diaA']);
					unset($valueD['mesA']);
					unset($valueD['annoA']);
					unset($valueD['horaA']);
					unset($valueD['minutoA']);
					unset($valueD['diaC']);
					unset($valueD['mesC']);
					unset($valueD['annoC']);
					unset($valueD['horaC']);
					unset($valueD['minutoC']);

					$eventos->actualizarWod($key, $valueD);
				}
			}

			foreach ($toInsert as $key => $value)
			{
				$dataDB = array(
					'nombre' 			=> $pWods[$key],
					'descripcion' 		=> $pDescripciones[$key],
					'tipo' 				=> $pTipos[$key],
					'repsRound' 		=> $repsRound[$key],
					'permPenalizacion' 	=> $penal[$key],
					'permTieBreak' 		=> $tie[$key],
					'horas' 			=> $horas[$key],
					'minutos' 			=> $minutos[$key],
					'segundos' 			=> $segundos[$key],
					'diaA' 				=> $diaA[$key],
					'mesA' 				=> $mesA[$key],
					'annoA'				=> $annoA[$key],
					'horaA' 			=> $horaA[$key],
					'minutoA'			=> $minutoA[$key],
					'diaC' 				=> $diaC[$key],
					'mesC' 				=> $mesC[$key],
					'annoC'				=> $annoC[$key],
					'horaC' 			=> $horaC[$key],
					'minutoC'			=> $minutoC[$key],
					'apertura'			=> 0,
					'cierre' 			=> 0,
					'eventoId' 			=> $evento->id);

				$validator 				= array();
				$validator['nombre'] 	= 'required';
				$validator['tipo'] 	  	= 'required';

				if($pTipos[$key] == 3)
				{
					$validator['repsRound'] 	  = 'numeric|min_numeric,0';
					$timeCap = 0;

					if($penal[$key])
					{
						$validator['repsRound'] 	= 'required|numeric|min_numeric,1';
						$validator['horas'] 		= 'numeric|min_numeric,0';
						$validator['minutos'] 	  	= 'numeric|min_numeric,0|max_numeric,59';
						$validator['segundos'] 	  	= 'numeric|min_numeric,0|max_numeric,59';

						$timeCap = ($horas[$key] * 3600) + ($minutos[$key] * 60) + $segundos[$key];
					}

					$dataDB['timeCap'] = $timeCap;
				}

				if($pTipos[$key] == 4)
				{
					$dataDB['permTieBreak'] 	= $tie[$key];

					$validator['repsRound'] 	= 'numeric|min_numeric,0';
					$validator['horas'] 		= 'numeric|min_numeric,0';
					$validator['minutos'] 		= 'numeric|min_numeric,0|max_numeric,59';
					$validator['segundos'] 		= 'numeric|min_numeric,0|max_numeric,59';

					$time = ($horas[$key] * 3600) + ($minutos[$key] * 60) + $segundos[$key];
					$dataDB['time'] = $time;
				}

				if($pTipos[$key] == 9)
				{
					$dataDB['permTieBreak'] = $peso[$key];
				}

				if($evento->disciplina == 10)
				{
					$validator['diaA'] 		= 'required|numeric|min_numeric,1';
					$validator['mesA'] 		= 'required|numeric|min_numeric,1|max_numeric,12';
					$validator['annoA'] 	= 'required|numeric|min_numeric,2016';
					$validator['horaA'] 	= 'required|numeric|min_numeric,0|max_numeric,23';
					$validator['minutoA'] 	= 'required|numeric|min_numeric,0|max_numeric,59';

					$validator['diaC'] 		= 'required|numeric|min_numeric,1';
					$validator['mesC'] 		= 'required|numeric|min_numeric,1|max_numeric,12';
					$validator['annoC'] 	= 'required|numeric|min_numeric,2016';
					$validator['horaC'] 	= 'required|numeric|min_numeric,0|max_numeric,23';
					$validator['minutoC'] 	= 'required|numeric|min_numeric,0|max_numeric,59';
				}

				$gumpValidator = new Gump();
				$is_valid = $gumpValidator->validate($dataDB, $validator);

				if( ($pTipos[$key] == 3) && $penal[$key] && ($dataDB['timeCap'] <= 0) )
				{
					$info['mensajes'][$key][] = "Debe ingresar un tiempo para el Time Cap.";
					$is_valid = false;
				}

				if($evento->disciplina == 10)
				{
					$fechaAtmp = 0;

					if(checkdate($mesA[$key], $diaA[$key], $annoA[$key]))
					{
						$fecha = strtotime($annoA[$key]."-".$mesA[$key]."-".$diaA[$key]." ".date("H:i", $evento->fecha));
						$fechaAtmp = $fecha;

						if($fecha < $evento->fecha)
						{
							$info['mensajes'][$key][] = "Apertura de WOD no v&aacute;lida (Menor a fecha del evento).";
							$is_valid = false;
						}
						else
							$dataDB['apertura'] = $fecha;
					}
					else
					{
						$info['mensajes'][$key][] = "Apertura de WOD no v&aacute;lida (Fecha incorrecta).";
						$is_valid = false;
					}					

					if(checkdate($mesC[$key], $diaC[$key], $annoC[$key]))
					{
						$fecha = strtotime($annoC[$key]."-".$mesC[$key]."-".$diaC[$key]." ".date("H:i", $evento->fecha));

						if($fecha < $evento->fecha)
						{
							$info['mensajes'][$key][] = "Cierre de WOD no v&aacute;lido.";
							$is_valid = false;
						}
						else
						{
							if($fecha <= $fechaAtmp)
							{
								$info['mensajes'][$key][] = "Cierre de WOD no v&aacute;lido.";
								$is_valid = false;
							}
							else
								$dataDB['cierre'] = $fecha;
						}
							
					}
					else
					{
						$info['mensajes'][$key][] = "Cierre de WOD no v&aacute;lido.";
						$is_valid = false;
					}
				}

				if($is_valid !== true)
				{
					$campos = $gumpValidator->errors();
					$info['campos'][$key] = $campos;

					if(isset($campos['repsRound']))
						$info['mensajes'][$key][] = "Compruebe el valor ingresado en Repeticiones Por Ronda (S&oacute;lo n&uacute;meros).";

					if(isset($campos['nombre']))
						$info['mensajes'][$key][] = "Debe especificar un nombre.";

					if(isset($campos['tipo']))
						$info['mensajes'][$key][] = "Debe escoger el tipo de WOD.";

					if(isset($campos['horas']))
						$info['mensajes'][$key][] = "Compruebe el valor ingresado en Horas.";

					if(isset($campos['minutos']))
						$info['mensajes'][$key][] = "Compruebe el valor ingresado en Minutos.";

					if(isset($campos['segundos']))
						$info['mensajes'][$key][] = "Compruebe el valor ingresado en Segundos.";

					if(isset($campos['annoA']) || isset($campos['mesA']) || isset($campos['diaA']) || isset($campos['horaA']) || isset($campos['minutoA']) )
						$info['mensajes'][$key][] = "Compruebe la fecha de Apertura.";

					if(isset($campos['annoC']) || isset($campos['mesC']) || isset($campos['diaC']) || isset($campos['horaC']) || isset($campos['minutoC']) )
						$info['mensajes'][$key][] = "Compruebe la fecha de Cierre.";
				}
				else
				{
					unset($dataDB['horas']);
					unset($dataDB['minutos']);
					unset($dataDB['segundos']);
					unset($dataDB['diaA']);
					unset($dataDB['mesA']);
					unset($dataDB['annoA']);
					unset($dataDB['horaA']);
					unset($dataDB['minutoA']);
					unset($dataDB['diaC']);
					unset($dataDB['mesC']);
					unset($dataDB['annoC']);
					unset($dataDB['horaC']);
					unset($dataDB['minutoC']);

					$eventos->agregarWods($dataDB);
				}
			}

			$eventos->borrarWods($toDelete);

			if(!empty($info['campos']) || !empty($info['mensajes']))
			{
				self::modEventoWods($eventoId, $info);
				return;
			}

			Url::redirect('cuenta/modevento/'.$evento->internalURL.'/wods');
		}
	}


	public function modEventoRelaciones($eventoId)
	{
		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			self::eventoNotFound();
			return;
		}


		if($evento->disciplina == 9)
		{
			Cuenta::sectionNotPermission();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$permisoC = self::checkPermisoCreacion($evento, $logeado);

		if(!$permisoC)
		{
			Cuenta::sectionNotPermission();
			return;
		}

		self::checkModificable($evento);

		$relaciones = $eventos->getRelacionWODCategoria($evento->id);
		$categorias = $eventos->getCategorias($evento->id);
		$wods = $eventos->getWODs($evento->id);

		$relacion = array();
		foreach ($relaciones as $key => $value)
		{
			$relacion[$value->categoriaId][$value->wodId] = $value;
		}

		$relacionesN = array();
		foreach ($categorias as $keyC => $valueC)
		{
			foreach ($wods as $keyW => $valueW)
			{
				$relacionesN[$keyC] = $valueC;
				$relacionesN[$keyC]->wods[$valueW->id] = new \stdClass();
				$relacionesN[$keyC]->wods[$valueW->id]->check = false;
				$relacionesN[$keyC]->wods[$valueW->id]->wod = $valueW;
				$relacionesN[$keyC]->wods[$valueW->id]->orden = count($relacion[$keyC])+1;

				if(isset($relacion[$keyC][$keyW]))
				{
					$relacionesN[$keyC]->wods[$valueW->id]->check = true;
					$relacionesN[$keyC]->wods[$valueW->id]->orden = $relacion[$keyC][$keyW]->orden;
				}
			}
		}

		foreach ($relacionesN as $keyR => $valueR)
		{
			usort($valueR->wods, "self::ordenarRelacionesPorOrden");
		}

		$cuentas = new Models\Cuentas();

		$data['title'] = "Modificar Categoria/WoD";
		$data['csrf_token'] = Csrf::makeToken();
		$data['evento'] = $evento;
		$data['categorias'] = $categorias;
		$data['wods'] = $wods;
		$data['logeado'] = $logeado;
		$data['relaciones'] = $relacionesN;

		View::renderTemplate('header', $data);
		View::render('tracker/cuenta/modevento/relaciones', $data, $info);
		View::renderTemplate('footer', $data);
	}

	private function ordenarRelacionesPorOrden($a, $b)
	{
		if($a->orden == $b->orden)
			return 0;

		if($a->orden < $b->orden)
			return -1;
		else
			return 1;
	}
	
	public function modEventoRelacionesPOST($eventoId)
	{
		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			self::eventoNotFound();
			return;
		}

		if($evento->disciplina == 9)
		{
			Cuenta::sectionNotPermission();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$permisoC = self::checkPermisoCreacion($evento, $logeado);

		if(!$permisoC)
		{
			Cuenta::sectionNotPermission();
			return;
		}

		self::checkModificable($evento);

		if (!Csrf::isTokenValid())
		{
			Cuenta::sectionCheckInvalid();
			return;
		}


		if(isset($_POST['submit']))
		{
			$categorias = $eventos->getCategorias($evento->id);
			$wods = $eventos->getWODs($evento->id);
			$relaciones = $eventos->getRelacionWODCategoria($evento->id);

			if(isset($_POST['wodR']))
				$wodR = $_POST['wodR'];
			else
				$wodR = array();

			$byCategoria = array();
			foreach ($relaciones as $key => $value)
			{
				$byCategoria[$value->categoriaId][$value->wodId] = $value;
			}

			$toDelete = array();
			$toInsert = array();
			$toUpdate = array();

			foreach ($byCategoria as $keyC => $valueR)
			{
				foreach ($valueR as $keyW => $valueRR)
				{
					if(empty($wodR[$keyC][$keyW]))
						$toDelete[$keyC] = $keyW;
				}
			}

			foreach ($wodR as $keyC => $value)
			{
				$pos = 1;
				foreach ($value as $keyW => $valueR)
				{
					$relacion = new \stdClass();
					$relacion->wod = $keyW;
					$relacion->categoria = $keyC;
					$relacion->orden = $pos++;

					if(empty($byCategoria[$keyC][$keyW]))
						$toInsert[] = $relacion;
					else
						$toUpdate[] = $relacion;
				}
			}

			foreach ($toInsert as $value)
			{
				$data = array();
				$data['wodId'] = $value->wod;
				$data['categoriaId'] = $value->categoria;
				$data['eventoId'] = $evento->id;
				$data['orden'] = $value->orden;

				$eventos->agregarRelaciones($data);
			}

			foreach ($toUpdate as $value)
			{
				$data = array();
				$data['orden'] = $value->orden;

				$where = array();
				$where['wodId'] = $value->wod;
				$where['categoriaId'] = $value->categoria;
				$where['eventoId'] = $evento->id;

				$eventos->actualizarRelaciones($data, $where);
			}

			foreach ($toDelete as $key => $value)
			{
				$eventos->borrarRelacion($evento->id, $key, $value);
			}

			Url::redirect('evento/'.$evento->internalURL);
		}
	}

	public function modEventoPatrocinadores($eventoId)
	{
		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			self::eventoNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$permisoC = self::checkPermisoCreacion($evento, $logeado);

		if(!$permisoC)
		{
			Cuenta::sectionNotPermission();
			return;
		}

		self::checkModificable($evento);

		$patrocinadores = $eventos->getPatrocinadores($evento->id);
		usort($patrocinadores, "self::ordenarRelacionesPorOrden");

		$cuentas = new Models\Cuentas();

		$data['title'] = "Modificar Patrocinadores";
		$data['csrf_token'] = Csrf::makeToken();
		$data['evento'] = $evento;
		$data['logeado'] = $logeado;
		$data['patrocinadores'] = $patrocinadores;

		View::renderTemplate('header', $data);
		View::render('tracker/cuenta/modevento/patrocinadores', $data, $info);
		View::renderTemplate('footer', $data);
	}


	public function modEventoPatrocinadoresPOST($eventoId)
	{
		$eventos = new Models\Eventos();
		$cuentas = new Models\Cuentas();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			self::eventoNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$permisoC = self::checkPermisoCreacion($evento, $logeado);

		if(!$permisoC)
		{
			Cuenta::sectionNotPermission();
			return;
		}

		self::checkModificable($evento);

		if (!Csrf::isTokenValid())
		{
			Cuenta::sectionCheckInvalid();
			return;
		}


		if(isset($_POST['submit']))
		{
			$patrocinadores = $eventos->getPatrocinadores($evento->id);

			$toDelete = array();
			$toInsert = array();
			$toUpdate = array();
			$orden = array();
			$ordenN = array();

			foreach ($_POST['orden'] as $key => $value)
			{
				if(empty($value))
					$ordenN[] = $key + 1;
				else
					$orden[$value] = $key + 1;
			}

			foreach ($patrocinadores as $key => $value)
			{
				if(!isset($_POST['nombre'][$value->id]))
					$toDelete[] = $value->id;
				else
				{
					$sponsor = array();
					$sponsor['nombre'] = trim($_POST['nombre'][$value->id]);
					$sponsor['orden'] = $orden[$value->id];
					$toUpdate[$value->id] = $sponsor;
				}
			}

			foreach ($_POST['nombreN'] as $key => $value)
			{
				$sponsor = array();
				$sponsor['nombre'] = trim($_POST['nombreN'][$key]);
				$sponsor['orden'] = $ordenN[$key];
				$toInsert[$key] = $sponsor;
			}

			$actuales = count($patrocinadores) - count($toDelete);
			$permitidos = $evento->patrocinadoresPlan - $actuales;

			foreach ($toDelete as $key => $value)
			{
				$eventos->borrarPatrocinador($value);
			}

			$erroresUpdate = array();
			$erroresInsert = array();

			foreach ($toUpdate as $key => $value)
			{
				if($_FILES['logo']['error'][$key] == UPLOAD_ERR_OK)
				{
					if($_FILES['logo']['type'][$key] == "image/jpeg")
						$ext = "jpg";
					else if($_FILES['logo']['type'][$key] == "image/png")
						$ext = "png";

					if($ext)
					{
						$nombreArchivo = $key."_".rand(10000,99999);

						if($patrocinadores[$key]->archivoLogo)
						{
							if(file_exists("./".SPONSORPICTUREPATH.$patrocinadores[$key]->archivoLogo))
								unlink("./".SPONSORPICTUREPATH.$patrocinadores[$key]->archivoLogo);
							
							$eventos->actualizarPatrocinador($key, array('archivoLogo' => null));
						}

						$proc = move_uploaded_file($_FILES['logo']['tmp_name'][$key], "./".SPONSORPICTUREPATH.$nombreArchivo.".".$ext);

						if($proc)
						{
							$eventos->actualizarPatrocinador($key, array('archivoLogo' => $nombreArchivo.".".$ext));

	    					$resizer = new ResizeImage("./".SPONSORPICTUREPATH.$nombreArchivo.".".$ext);
	    					$resizer->resizeTo(200, 200, 'maxHeight');
	    					$resizer->saveImage("./".SPONSORPICTUREPATH.$nombreArchivo.".".$ext);
						}
					}
					else
					{
						$error = array();
						$error['name'] = $value['nombre'];
						$error['error'] = "Tipo de im&aacute;gen no permitida (JPG, PNG)";
						$erroresUpdate[]= $error;
					}
				}
				else
				{
					if($_FILES['logo']['error'][$key] != UPLOAD_ERR_NO_FILE)
					{
						$error = array();
						$error['name'] = $value['nombre'];
						$error['error'] = "Tama&ntilde;o m&aacute;ximo de im&aacute;gen excedido. (2 MB)";
						$erroresUpdate[]= $error;
					}
				}

				$dataAct = array();
				$dataAct['nombre'] = $value['nombre'];
				$dataAct['orden'] = $value['orden'];
				$eventos->actualizarPatrocinador($key, $dataAct);
			}

			foreach ($toInsert as $key => $value)
			{
				if($permitidos <= 0)
					break;

				if($_FILES['logoN']['error'][$key] == UPLOAD_ERR_OK)
				{
					$dataAct = array();
					$dataAct['nombre'] = $value['nombre'];
					$dataAct['orden'] = $value['orden'];
					$patId = $eventos->agregarPatrocinador($evento->id, $dataAct);
					$permitidos--;

					if($_FILES['logoN']['type'][$key] == "image/jpeg")
						$ext = "jpg";
					else if($_FILES['logoN']['type'][$key] == "image/png")
						$ext = "png";

					if($ext)
					{
						$nombreArchivo = $patId."_".rand(10000,99999);
						$proc = move_uploaded_file($_FILES['logoN']['tmp_name'][$key], "./".SPONSORPICTUREPATH.$nombreArchivo.".".$ext);

						if($proc)
						{
							$eventos->actualizarPatrocinador($patId, array('archivoLogo' => $nombreArchivo.".".$ext));

	    					$resizer = new ResizeImage("./".SPONSORPICTUREPATH.$nombreArchivo.".".$ext);
	    					$resizer->resizeTo(200, 200, 'maxHeight');
	    					$resizer->saveImage("./".SPONSORPICTUREPATH.$nombreArchivo.".".$ext);
						}
					}
					else
					{
						$error = array();
						$error['name'] = $value['nombre'];
						$error['error'] = "Tipo de im&aacute;gen no permitida (JPG, PNG)";
						$erroresInsert[]= $error;
					}
				}
				else
				{
					if($_FILES['logoN']['error'][$key] != UPLOAD_ERR_NO_FILE)
					{
						$error = array();
						$error['name'] = $value['nombre'];
						$error['error'] = "Tama&ntilde;o m&aacute;ximo de im&aacute;gen excedido. (2 MB)";
						$erroresInsert[]= $error;
					}
				}
			}

			if(empty($erroresUpdate) && empty($erroresInsert))
				Url::redirect('evento/'.$evento->internalURL);
			else
				self::errorPatrocinadores($evento->id, $erroresInsert, $erroresUpdate);
		}
	}

	public function modEventoAtletasAprobar($eventoId, $info = array())
	{
		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			self::eventoNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$permisoC = self::checkPermisoCreacion($evento, $logeado);

		if(!$permisoC)
		{
			Cuenta::sectionNotPermission();
			return;
		}

		self::checkModificable($evento);

		$cuentas = new Models\Cuentas();
		$categorias = $eventos->getCategorias($evento->id);
		$pendientes = array();
		$pagos = array();

		if(!$evento->registroTeam)
		{
			$pendientes = $eventos->getAtletasPendientes($evento->id);
			$pagos = $eventos->getPagos($pendientes);
		}
		else
		{
			$pendientes = $eventos->getEquiposPendientes($evento->id);
			$pagos = $eventos->getPagosEquipos($pendientes);
		}

		$data['title'] = "Aprobar Participantes";
		$data['csrf_token'] = Csrf::makeToken();
		$data['evento'] = $evento;
		$data['categorias'] = $categorias;
		$data['atletas'] = $pendientes;
		$data['pagos'] = $pagos;
		$data['logeado'] = $logeado;

		View::renderTemplate('headerNORESPONSIVE', $data);

		if(!$evento->registroTeam)
			View::render('tracker/cuenta/modevento/atletasaprobar', $data, $info);
		else
			View::render('tracker/cuenta/modevento/equiposaprobar', $data, $info);

		View::renderTemplate('footer', $data);
	}

	public function modEventoAtletasAprobarPOST($eventoId)
	{
		$eventos = new Models\Eventos();
		$personas = new Models\Cuentas();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			self::eventoNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$permisoC = self::checkPermisoCreacion($evento, $logeado);

		if(!$permisoC)
		{
			Cuenta::sectionNotPermission();
			return;
		}

		self::checkModificable($evento);

		if (!Csrf::isTokenValid())
		{
			Cuenta::sectionCheckInvalid();
			return;
		}

		if(isset($_POST['submit']))
		{
			$pendientes = array();
			$aceptar = array();
			$rechazar = array();

			if(isset($_POST['pendiente']))
			{
				foreach ($_POST['pendiente'] as $key => $value)
				{
					$pendientes[$key] = $value;
				}
			}

			if(isset($_POST['opcion']))
			{
				foreach ($_POST['opcion'] as $key => $value)
				{
					if($value == "ok")
					{
						if(isset($pendientes[$key]) && !empty($pendientes[$key]))
							$aceptar[$key] = $pendientes[$key];
					}

					if($value == "rechazar")
					{
						if(isset($pendientes[$key]))
							$rechazar[$key] = $pendientes[$key];
					}
				}
			}

			$cuentas = new Models\Cuentas();
			$atletas = $eventos->getAtletasPendientes($evento->id);
			$atletas2 = $eventos->getAtletasRegistrados($evento->id);
			$cantRegistrados = count($atletas2);
			$cantNuevos = count($aceptar);
			$total = $cantRegistrados + $cantNuevos;


			//if( (($evento->disciplina == 11) /*&& ($total > 40)*/) || (($evento->disciplina == 12) /*&& ($total > 50)*/) )
			//{
				//$info['mensajes'][] = "Ha superado la cantidad de participantes que puede registrar.";
			//}
			/*else*/ if($total > $evento->atletasPlan)
			{
				$info['mensajes'][] = "Ha superado la cantidad de participantes que puede registrar. (Max: ".$evento->atletasPlan.")";
			}
			else
			{


				foreach ($aceptar as $key => $value)
				{
					$resultado = false;

					if(!$evento->registroTeam)
						$resultado = $eventos->crearRegistroAtleta($key, $value);
					else
						$resultado = $eventos->crearRegistroEquipo($key, $value);

					$categoria = $eventos->getCategoria($evento->id, $value);

					if($resultado > 0)
					{
						$persona = $atletas[$key];
						$organizador = $cuentas->getPersona($evento->creadorId);

						if(!empty($persona->correo))
						{
							$gestor = fopen("app/templates/mail/mail.html", "r");
							$txt = "";

							while (!feof($gestor))
							{
								$line_of_text = fgets($gestor);
								$txt .= $line_of_text;
							}
							fclose($gestor);

							$bdy = "Saludos ".$persona->nombre." ".$persona->apellido.", le informamos que su solicitud de registro en el evento \"".$evento->nombre."\" ha sido aprobado.<br/><br/>Informacion del evento: ".$evento->nombre."<br/><b>Fecha: </b>".$evento->fechaStr."<br/><b>Categoria: </b>".$categoria->nombre."<br/><b>Web del evento: </b> <a href='".$evento->urlEvento."'>".$evento->urlEvento."</a><br/><br/>Para mayor informacion: <br/><b>Organizador:</b> ".$organizador->nombre." ".$organizador->apellido."<br/><b>Correo:</b> <a href='mailto:".$organizador->correo."'>".$organizador->correo."</a><br/><b>Telefono:</b>".$organizador->telefono_1;
							$strMail = str_replace("[MENSAJE]", $bdy, $txt);
							$strMail = str_replace("[LOGO1]", DIR."app/templates/<?php echo TEMPLATE;?>/img/logoCuadrado.png", $strMail);

							if($evento->id == 41)
							{
								$bdy = "Bienvenido al clasificatorio del campeonato nacional FitGames Venezuela. &Eacute;xito! #ACTIVOenFitGamesLAPLAYA <br/><br/>Informacion del evento: ".$evento->nombre."<br/><b>Fecha: </b>".$evento->fechaStr."<br/><b>Categoria: </b>".$categoria->nombre."<br/><b>Web del evento: </b> <a href='".$evento->urlEvento."'>".$evento->urlEvento."</a><br/><br/>Para mayor informacion: <br/><b>Organizador:</b> ".$organizador->nombre." ".$organizador->apellido."<br/><b>Correo:</b> <a href='mailto:".$organizador->correo."'>".$organizador->correo."</a><br/><b>Telefono:</b>".$organizador->telefono_1;
								$strMail = str_replace("[MENSAJE]", $bdy, $txt);
								$strMail = str_replace("[LOGO1]", DIR."imagenes/evento/41_mail.jpg", $strMail);
							}

							$mail = new \Helpers\PhpMailer\Mail();
							$mail->setFrom(SITEEMAIL);
							$mail->addAddress($persona->correo);
							$mail->subject('Registro en evento aprobado: '.$evento->nombre);
							$mail->body($strMail);
							$mail->send();
						}
					}
				}
			}

			foreach ($rechazar as $key => $value)
			{
				$persona = $atletas[$key];

				if(!$evento->registroTeam)
					$resultado = $eventos->borrarRegistro($key);
				else
					$resultado = $eventos->borrarRegistroEquipo($key);

				if($resultado > 0)
				{
					$organizador = $cuentas->getPersona($evento->creadorId);

					if(!empty($persona->correo))
					{
						$gestor = fopen("app/templates/mail/mail.html", "r");
						$txt = "";

						while (!feof($gestor))
						{
							$line_of_text = fgets($gestor);
							$txt .= $line_of_text;
						}
						fclose($gestor);

						$bdy = "Saludos ".$persona->nombre." ".$persona->apellido.", <br/><br/>Le informamos que su solicitud de registro en el evento \"".$evento->nombre."\" ha sido rechazado.<br/><br/>Informacion del evento: ".$evento->nombre."<br/><b>Fecha: </b>".$evento->fechaStr."<br/><b>Categoria: </b>".$categoria->nombre."<br/><b>Web del evento: </b> <a href='".$evento->urlEvento."'>".$evento->urlEvento."</a><br/><br/>Para mayor informacion: <br/><b>Organizador:</b> ".$organizador->nombre." ".$organizador->apellido."<br/><b>Correo:</b> <a href='mailto:".$organizador->correo."'>".$organizador->correo."</a><br/><b>Telefono:</b>".$organizador->telefono_1;
						$strMail = str_replace("[MENSAJE]", $bdy, $txt);
						$strMail = str_replace("[LOGO1]", DIR."app/templates/<?php echo TEMPLATE;?>/img/logoCuadrado.png", $strMail);

						$mail = new \Helpers\PhpMailer\Mail();
						$mail->setFrom(SITEEMAIL);
						$mail->addAddress($persona->correo);
						$mail->subject('Registro en evento rechazado: '.$evento->nombre);
						$mail->body($strMail);
						$mail->send();
					}
				}
			}

			Url::redirect('cuenta/modevento/'.$evento->internalURL.'/participantesaprobar');
		}
	}


	public function modEventoAtletasAprobados($eventoId)
	{
		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			self::eventoNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$permisoC = self::checkPermisoCreacion($evento, $logeado);

		if(!$permisoC)
		{
			Cuenta::sectionNotPermission();
			return;
		}

		self::checkModificable($evento);

		$categorias = $eventos->getCategorias($evento->id);
		$atletas = array();
		$pagos = array();

		if(!$evento->registroTeam)
		{
			$atletas = $eventos->getAtletasRegistrados($evento->id);
			$pagos = $eventos->getPagos($atletas);
		}
		else
		{
			$atletas = $eventos->getEquiposRegistrados($evento->id);
			$pagos = $eventos->getPagosEquipos($atletas);
		}

		$cuentas = new Models\Cuentas();

		$data['title'] = "Modificar Participantes";
		$data['csrf_token'] = Csrf::makeToken();
		$data['evento'] = $evento;
		$data['pagos'] = $pagos;
		$data['categorias'] = $categorias;
		$data['atletas'] = $atletas;
		$data['logeado'] = $logeado;

		View::renderTemplate('headerNORESPONSIVE', $data);

		if(!$evento->registroTeam)
			View::render('tracker/cuenta/modevento/atletasaprobados', $data, $info);
		else
			View::render('tracker/cuenta/modevento/equiposaprobados', $data, $info);

		View::renderTemplate('footer', $data);
	}


	public function modEventoAtletasAprobadosPOST($eventoId)
	{
		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			self::eventoNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$permisoC = self::checkPermisoCreacion($evento, $logeado);

		if(!$permisoC)
		{
			Cuenta::sectionNotPermission();
			return;
		}

		self::checkModificable($evento);

		if (!Csrf::isTokenValid())
		{
			Cuenta::sectionCheckInvalid();
			return;
		}

		if(isset($_POST['submit']))
		{
			$registros = array();
			$borrar = array();

			if(isset($_POST['registro']))
			{
				foreach ($_POST['registro'] as $key => $value)
				{
					$registros[$key] = $value;
				}
			}

			if(isset($_POST['borrar']))
			{
				foreach ($_POST['borrar'] as $key => $value)
				{
					if(isset($registros[$key]))
					{
						$borrar[$key] = $value;
						unset($registros[$key]);
					}
				}
			}

			$eventos = new Models\Eventos();
			foreach ($registros as $key => $value)
			{
				if(empty($value))
					$value = 0;

				if(!$evento->registroTeam)
					$eventos->actualizarRegistro($key, array('categoriaId' => $value));
				else
					$eventos->actualizarRegistroEquipo($key, array('categoriaId' => $value));
			}

			foreach ($borrar as $key => $value)
			{
				if(!$evento->registroTeam)
					$eventos->borrarRegistro($key);
				else
					$eventos->borrarRegistroEquipo($key);
			}

			Url::redirect('cuenta/modevento/'.$evento->internalURL.'/participantesaprobados');
		}
	}

	public function modEventoAtletasPesos($eventoId)
	{
		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			self::eventoNotFound();
			return;
		}

		if($evento->disciplina != 9)
		{
			Cuenta::sectionNotPermission();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$permisoC = self::checkPermisoCreacion($evento, $logeado);

		if(!$permisoC)
		{
			Cuenta::sectionNotPermission();
			return;
		}

		self::checkModificable($evento);

		$categorias = $eventos->getCategorias($evento->id);
		$atletas = $eventos->getAtletasRegistrados($evento->id);
		$cuentas = new Models\Cuentas();

		$data['title'] = "Modificar Pesos";
		$data['csrf_token'] = Csrf::makeToken();
		$data['evento'] = $evento;
		$data['categorias'] = $categorias;
		$data['atletas'] = $atletas;
		$data['logeado'] = $logeado;

		View::renderTemplate('headerNORESPONSIVE', $data);
		View::render('tracker/cuenta/modevento/atletaspesos', $data, $info);
		View::renderTemplate('footer', $data);
	}


	public function modEventoAtletasPesosPOST($eventoId)
	{
		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			self::eventoNotFound();
			return;
		}

		if($evento->disciplina != 9)
		{
			Cuenta::sectionNotPermission();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$permisoC = self::checkPermisoCreacion($evento, $logeado);

		if(!$permisoC)
		{
			Cuenta::sectionNotPermission();
			return;
		}

		self::checkModificable($evento);

		if (!Csrf::isTokenValid())
		{
			Cuenta::sectionCheckInvalid();
			return;
		}

		if(isset($_POST['submit']))
		{
			
			$pesos = array();
			$enKg = array();

			if(isset($_POST['peso']))
			{
				foreach ($_POST['peso'] as $key => $value)
				{
					$pesos[$key] = $value;
				}
			}

			if(isset($_POST['kg']))
			{
				foreach ($_POST['kg'] as $key => $value)
				{
					$enKg[$key] = $value;
				}
			}


			$eventos = new Models\Eventos();

			foreach ($pesos as $key => $value)
			{
				if($enKg[$key] == "kg")
					$eventos->actualizarPeso($key, array('bodyweight' => $value));
				else
					$eventos->actualizarPeso($key, array('bodyweight' => $value / 2.20462));
			}

			Url::redirect('cuenta/modevento/'.$evento->internalURL.'/participantespesos');
		}
	}

	private function convert_to_csv($input_array, $header_array, $output_file_name, $delimiter)
	{
		$f = fopen('php://memory', 'w');

 		fputcsv($f, array_values($header_array[0]), $delimiter);

		foreach ($input_array as $line)
		{
			fputcsv($f, $line, $delimiter);
		}

		fseek($f, 0);

		header('Content-Type: application/csv; charset=ISO-8859-1');
		header('Content-Disposition: attachement; filename="' . $output_file_name . '";');

		fpassthru($f);
	}

	public function ordenarPorCat($a, $b)
	{
		if($a['categoria'] == $b['categoria'])
			return 0;

		if($a['categoria'] < $b['categoria'])
			return -1;
		else
			return 1;
	}

	public function modEventoAtletasExportable($eventoId, $info = array())
	{
		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			self::eventoNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$permisoC = self::checkPermisoCreacion($evento, $logeado);

		if(!$permisoC)
		{
			Cuenta::sectionNotPermission();
			return;
		}

		self::checkModificable($evento);

		$perfiles = new Models\Perfiles();
		$categorias = $eventos->getCategorias($evento->id);
		$atletas = $eventos->getAtletasRegistrados($evento->id);
		$perfiles->prepararCuentas($atletas);

		$data['evento'] = $evento;
		$data['categorias'] = $categorias;
		$data['atletas'] = $atletas;

		$cabeceras = array();
		$cabeceras[0] = array();
		$cabeceras[0][] = 'Num. Registro';
		$cabeceras[0][] = 'Categoria';
		$cabeceras[0][] = 'Ced/Pasaporte';
		$cabeceras[0][] = 'Nombre';
		$cabeceras[0][] = 'Apellido';
		$cabeceras[0][] = 'Box';
		$cabeceras[0][] = 'Correo';
		$cabeceras[0][] = 'Telefono';
		$cabeceras[0][] = 'Fecha de Nacimiento';
		$cabeceras[0][] = 'Pais';
		$cabeceras[0][] = 'Estado';

		$tabla = array();
		foreach ($atletas as $keyA => $valueA)
		{
			$tabla[$keyA] = array();
			$tabla[$keyA]['id'] = $valueA->id;
			$tabla[$keyA]['categoria'] = utf8_decode($categorias[$valueA->categoriaId]->nombre);
			$tabla[$keyA]['ident'] = ucwords(strtolower(utf8_decode($valueA->ident)));
			$tabla[$keyA]['nombre'] = ucwords(strtolower(utf8_decode($valueA->nombre)));
			$tabla[$keyA]['apellido'] = ucwords(strtolower(utf8_decode($valueA->apellido)));
			$tabla[$keyA]['box'] = $valueA->boxStr;
			$tabla[$keyA]['correo'] = strtolower($valueA->correo);
			$tabla[$keyA]['telefono'] = $valueA->telefono_1;
			$tabla[$keyA]['fechaN'] = $valueA->nacimientoStr;
			$tabla[$keyA]['pais'] = ucwords(strtolower(utf8_decode($valueA->paisStr)));
			$tabla[$keyA]['estado'] = ucwords(strtolower(utf8_decode($valueA->estado)));
		}
		
		usort($tabla, 'self::ordenarPorCat');
		self::convert_to_csv($tabla, $cabeceras, 'reporte'.$evento->internalURL.'.csv', ',');
	}


	public function modEventoJuez($eventoId, $info = array())
	{
		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			self::eventoNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$permisoC = self::checkPermisoCreacion($evento, $logeado);

		if(!$permisoC)
		{
			Cuenta::sectionNotPermission();
			return;
		}

		self::checkModificable($evento);

		$categorias = $eventos->getCategorias($evento->id);
		$atletas = $eventos->getAtletasRegistrados($evento->id);
		$cuentas = new Models\Cuentas();
		$perfiles = new Models\Perfiles();
		$jueces = $eventos->getJueces($evento->id);
		$perfiles->prepararCuentas($jueces);

		$data['title'] = "Modificar Juez";
		$data['csrf_token'] = Csrf::makeToken();
		$data['evento'] = $evento;
		$data['juezP'] = $jueces[$evento->juezPrincipal];
		$data['juezS'] = $jueces[$evento->juezSuplente];
		$data['logeado'] = $logeado;

		View::renderTemplate('header', $data);
		View::render('tracker/cuenta/modevento/juez', $data, $info);
		View::renderTemplate('footer', $data);
	}
	
	public function modEventoJuezPOST($eventoId, $info = array())
	{
		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			self::eventoNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$permisoC = self::checkPermisoCreacion($evento, $logeado);

		if(!$permisoC)
		{
			Cuenta::sectionNotPermission();
			return;
		}

		self::checkModificable($evento);

		if (!Csrf::isTokenValid())
		{
			Cuenta::sectionCheckInvalid();
			return;
		}

		if(isset($_POST['submit']))
		{
			if(isset($_POST['juez']))
			{
				if(isset($_POST['juez'][0]))
				{
					$id = trim($_POST['juez'][0]);
					if(!empty($id))
						$eventos->agregarJuezPrincipal($evento->id, $id);
				}

				if(isset($_POST['juez'][1]))
				{
					$id = trim($_POST['juez'][1]);
					if(!empty($id))
						$eventos->agregarJuezSuplente($evento->id, $id);
				}
			}

			if(isset($_POST['borrar']))
			{
				if(isset($_POST['borrar'][0]))
					$eventos->borrarJuezPrincipal($evento->id);
				if(isset($_POST['borrar'][1]))
					$eventos->borrarJuezSuplente($evento->id);
			}

			self::modEventoJuez($evento->id);
		}
	}
	
	public function modEventoAtletas($eventoId, $info = array())
	{
		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			self::eventoNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$permisoC = self::checkPermisoCreacion($evento, $logeado);

		if(!$permisoC)
		{
			Cuenta::sectionNotPermission();
			return;
		}

		self::checkModificable($evento);

		$cuentas = new Models\Cuentas();

		$data['title'] = "Registrar Participantes";
		$data['csrf_token'] = Csrf::makeToken();
		$data['evento'] = $evento;
		$data['logeado'] = $logeado;

		if(empty($info))
		{
			$info['mensajes'] = array();
			$info['errores'] = array();
			$info['repetidos'] = array();
		}

		View::renderTemplate('header', $data);
		View::render('tracker/cuenta/modevento/atletas', $data, $info);
		View::renderTemplate('footer', $data);
	}

	public function modEventoAtletasPOST($eventoId)
	{
		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			self::eventoNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$permisoC = self::checkPermisoCreacion($evento, $logeado);

		if(!$permisoC)
		{
			Cuenta::sectionNotPermission();
			return;
		}

		self::checkModificable($evento);

		if (!Csrf::isTokenValid())
		{
			Cuenta::sectionCheckInvalid();
			return;
		}

		if(isset($_POST['submit']))
		{
			$ids = $_POST['ids'];
			
			$getdata = str_replace("\r\n", "\n", $ids);
			$arr = explode("\n", $getdata);

			$lines = array_filter($arr);

			foreach ($lines as $key => $value)
			{
				if(empty(trim($value)) || !ctype_digit(strval($value)) )
					unset($lines[$key]);
			}

			$personas = new Models\Cuentas();
			$atletas = array();
			$repetidos = array();
			$errores = array();

			foreach ($lines as $key => $value)
			{
				$pp = $personas->getPersona($value);

				if($pp)
					$atletas[$pp->id] = $pp;
				else
				{
					$errores[] = $value;
					unset($lines[$key]);
				}
			}

			$atletas2 = $eventos->getAtletasRegistrados($evento->id);
			$registradosByID = array();

			foreach ($atletas2 as $key => $value)
			{
				$registradosByID[$value->personaId] = $value;
			}

			foreach ($lines as $key => $keyAtl)
			{
				if(isset($registradosByID[$keyAtl]))
				{
					$repetidos[$keyAtl] = $registradosByID[$keyAtl];
					unset($lines[$key]);
				}
			}

			if(empty($repetidos) && empty($errores))
			{
				$cantRegistrados = count($atletas2);
				$cantNuevos = count($lines);
				$total = $cantRegistrados + $cantNuevos;

				//if( (($evento->disciplina == 11) /*&& ($total > 40)*/) || (($evento->disciplina == 12) /*&& ($total > 50)*/) )
				//{
					//$info['mensajes'][] = "Ha superado la cantidad de participantes que puede registrar.";
				//}
				/*else */if($total > $evento->atletasPlan)
				{
					$info['mensajes'][] = "Ha superado la cantidad de participantes que puede registrar. (Max: ".$evento->atletasPlan.")";
				}
			}

			if(empty($repetidos) && empty($errores) && empty($info['mensajes']))
			{
				foreach ($lines as $key => $value)
				{
					$res = $eventos->crearAtletaPendiente($evento->id, 0, $value);
				}

				Url::redirect('cuenta/modevento/'.$evento->internalURL.'/participantesaprobar');
			}
			else
			{
				$info['data'] = implode(PHP_EOL, $lines);
				$info['errores'] = $errores;
				$info['repetidos'] = $repetidos;

				self::modEventoAtletas($evento->id, $info);
			}
		}
	}

	public function modEventoEliminar($eventoId, $info = array())
	{
		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			self::eventoNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$permisoC = self::checkPermisoCreacion($evento, $logeado);

		if(!$permisoC)
		{
			Cuenta::sectionNotPermission();
			return;
		}

		$cuentas = new Models\Cuentas(); 

		$data['title'] = "Eliminar evento";
		$data['csrf_token'] = Csrf::makeToken();
		$data['logeado'] = $logeado;
		$data['evento'] = $evento;

		View::renderTemplate('header', $data);
		View::render('tracker/cuenta/modevento/eliminar', $data, $info);
		View::renderTemplate('footer', $data);
	}

	public function modEventoEliminarPOST($eventoId)
	{
		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			self::eventoNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$permisoC = self::checkPermisoCreacion($evento, $logeado);

		if(!$permisoC)
		{
			Cuenta::sectionNotPermission();
			return;
		}

		if (!Csrf::isTokenValid())
		{
			Cuenta::sectionCheckInvalid();
			return;
		}

		if(isset($_POST['submit']))
		{
			if(isset($_POST['eliminar']))
			{
				$eventos = new Models\Eventos();
				$evento = $eventos->borrarEvento($evento->id);
			}
		}

		Url::redirect('cuenta/eventosorg');
	}

	private function checkPermisoModificacionResultados($evento, $sessionData)
	{
		if($evento->eventoFinalizado)
			return false;

		$eventos = new Models\Eventos();

		$persona = $sessionData['persona'];
		$esAdmin = $persona->esAdmin;
		$esOrganizador = ($evento->creadorId == $persona->idPersona);

		if($evento->disciplina == 10)
			$tienePermiso = $eventos->getRegistro($evento->id, $persona->idPersona);
		
		$tienePermiso = ( ($evento->juezPrincipal == $persona->idPersona) || ($evento->juezSuplente == $persona->idPersona) || $tienePermiso);

		if($tienePermiso || $esAdmin || $esOrganizador)
			return true;

		return false;
	}


	public function modEventoResultados($eventoId, $info = array())
	{
		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			self::eventoNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$permisoMR = self::checkPermisoModificacionResultados($evento, $logeado);

		if(!$permisoMR)
		{
			Cuenta::sectionOnlyJudges($evento);
			return;
		}

		$categorias = $eventos->getCategorias($evento->id);

		$data['title'] = "Registro de Resultados";
		$data['evento'] = $evento;
		$data['categorias'] = $categorias;
		$data['logeado'] = $logeado;

		View::renderTemplate('header', $data);
		View::render('tracker/cuenta/modevento/resultados', $data, $info);
		View::renderTemplate('footer', $data);
	}

	public function modEventoResultados2($eventoId, $categoriaId = 0, $info = array())
	{
		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			self::eventoNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$permisoMR = self::checkPermisoModificacionResultados($evento, $logeado);

		if(!$permisoMR)
		{
			Cuenta::sectionOnlyJudges($evento);
			return;
		}

		$cuentas = new Models\Cuentas();
		$categoria = $eventos->getCategoria($evento->id, $categoriaId);
		$jueces = $eventos->getJueces($evento->id);
		$esJ = false;
		$esOrg = false;

		foreach ($jueces as $key => $value)
		{
			if($logeado['persona']->idPersona == $value->id)
				$esJ = true;
		}

		if($evento->creadorId == $logeado['persona']->idPersona)
			$esOrg = true;

		if(!$categoria)
		{
			Eventos::categoriaNotFound($evento);
			return;
		}

		$wods = $eventos->getWods($evento->id);
		$relaciones = $eventos->getRelacionWODCategoria($evento->id);

		$byCategoria = array();
		foreach ($relaciones as $key => $value)
		{
			if($value->categoriaId == $categoriaId)
				$byCategoria[$value->wodId] = $wods[$value->wodId];
		}

		$data['title'] = "Registro de Resultados";
		$data['logeado'] = $logeado;
		$data['wods'] = $byCategoria;
		$data['evento'] = $evento;
		$data['esJuez'] = $esJ;
		$data['esOrg'] = $esOrg;
		$data['categoria'] = $categoria;

		View::renderTemplate('header', $data);
		View::render('tracker/cuenta/modevento/resultados2nuevo', $data, $info);
		View::renderTemplate('footer', $data);
	}


	public function modEventoResultados3($eventoId, $categoriaId = 0, $wodId = 0, $info = array())
	{
		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			self::eventoNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$permisoMR = self::checkPermisoModificacionResultados($evento, $logeado);

		if(!$permisoMR)
		{
			Cuenta::sectionOnlyJudges($evento);
			return;
		}
/*
		if($evento->disciplina == 10)
		{
			Cuenta::sectionNotPermission();
			return;
		}
*/
		$categoria = $eventos->getCategoria($evento->id, $categoriaId);

		if(!$categoria)
		{
			self::categoriaNotFound($evento);
			return;
		}

		$wod = $eventos->getWOD($evento->id, $wodId);

		if(!$wod)
		{
			self::wodNotFound($evento, $categoria);
			return;
		}

		if(!$evento->registroTeam)
			$atletasCategoria = $eventos->getAtletasCategoria($evento->id, $categoriaId);		
		else
			$atletasCategoria = $eventos->getEquiposCategoria($evento->id, $categoriaId);		

		$resultados = $eventos->getResultadosWOD($evento->id, $wodId);

		foreach ($atletasCategoria as $key => $value)
		{
			if(isset($resultados[$key]))
			{
				$value->resCargado = true;
			}
		}

		$data['title'] = "Registro de Resultados";
		$data['csrf_token'] = Csrf::makeToken();
		$data['logeado'] = $logeado;
		$data['atletas'] = $atletasCategoria;
		$data['evento'] = $evento;
		$data['categoria'] = $categoria;
		$data['wod'] = $wod;

		View::renderTemplate('header', $data);
		View::render('tracker/cuenta/modevento/resultados2', $data, $info);
		View::renderTemplate('footer', $data);
	}

	public function modEventoResultados3POST($eventoId, $categoriaId = 0, $wodId = 0, $info = array())
	{		
		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			self::eventoNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$permisoMR = self::checkPermisoModificacionResultados($evento, $logeado);

		if(!$permisoMR)
		{
			Cuenta::sectionOnlyJudges($evento);
			return;
		}
/*
		if($evento->disciplina == 10)
		{
			Cuenta::sectionNotPermission();
			return;
		}
*/
		$categoria = $eventos->getCategoria($evento->id, $categoriaId);

		if(!$categoria)
		{
			self::categoriaNotFound($evento);
			return;
		}

		$wod = $eventos->getWOD($evento->id, $wodId);

		if(!$wod)
		{
			self::wodNotFound($evento, $categoria);
			return;
		}


		if (!Csrf::isTokenValid())
		{
			Cuenta::sectionCheckInvalid();
			return;
		}

		$resultadosViejos = $eventos->getResultadosWOD($evento->id, $wodId);

		$resultados = array();
		$operacion = array();
		$operacion['errores'] = array();
		$operacion['operaciones'] = array();

		if(isset($_POST['participante']))
		{
			$participantes = $_POST['participante'];

			foreach ($participantes as $keyP => $valueP)
			{
				if(empty($valueP))
					continue;

				$valores = array();
				$valores['regID'] = $valueP;
				$valores['evento'] = $evento;

				$validator = array();

				if($evento->disciplina == 10)
				{
					foreach ($resultadosViejos as $key => $value)
					{
						if($key != $valueP)
							continue;

						foreach ($value as $keyW => $valueW)
						{
							if($keyW == $wodId)
							{
								$valores['videoLink'] = $valueW->videoLink;
								break;
							}
							
						}
					}
				}

				if($evento->disciplina == 9)
				{
					$valores['unidadRes'] 	= $_POST['unidadRes'][$keyP];
					$valores['intento1'] 	= $_POST['intento1'][$keyP];
					$valores['intento2'] 	= $_POST['intento2'][$keyP];
					$valores['intento3'] 	= $_POST['intento3'][$keyP];

					$validator 				= array();
					$validator['unidadRes'] = 'required';
					$validator['intento1'] 	= 'numeric|min_numeric,0';
					$validator['intento2'] 	= 'numeric|min_numeric,0';
					$validator['intento3'] 	= 'numeric|min_numeric,0';

					$gumpValidator = new Gump();
					$is_valid = $gumpValidator->validate($valores, $validator);

					if($is_valid !== true)
						$operacion['errores'][$valueP] = $gumpValidator->errors();
					else
					{
						$esLb = false;

						if($valores['unidadRes'] == "lb")
							$esLb = true;

						if(!empty($valores['intento1']) || $valores['intento1'] == "0")
						{
							$r1 = $valores['intento1'];
							$resultados[$valueP.'r1'] = self::entidadResultado($valueP, $evento->id, $wodId);
							$resultados[$valueP.'r1']['attempt'] = 1;

							if($esLb)
								$resultados[$valueP.'r1']['resultado'] = sprintf("%'0.4f", $r1 / 2.20462);
							else
								$resultados[$valueP.'r1']['resultado'] = $r1;
						}

						if(!empty($valores['intento2']) || $valores['intento2'] == "0")
						{
							$r2 = $valores['intento2'];

							$resultados[$valueP.'r2'] = self::entidadResultado($valueP, $evento->id, $wodId);
							$resultados[$valueP.'r2']['attempt'] = 2;

							if($esLb)
								$resultados[$valueP.'r2']['resultado'] = sprintf("%'0.4f", $r2 / 2.20462);
							else
								$resultados[$valueP.'r2']['resultado'] = $r2;
						}

						if(!empty($valores['intento3']) || $valores['intento3'] == "0")
						{
							$r3 = $valores['intento3'];

							$resultados[$valueP.'r3'] = self::entidadResultado($valueP, $evento->id, $wodId);
							$resultados[$valueP.'r3']['attempt'] = 3;

							if($esLb)
								$resultados[$valueP.'r3']['resultado'] = sprintf("%'0.4f", $r3 / 2.20462);
							else
								$resultados[$valueP.'r3']['resultado'] = $r3;
						}
					}
				}
				else
				{
					if($wod->tipo == 3)
					{
						$valores['horas'] 			= $_POST['horasRes'][$keyP];
						$valores['minutos'] 		= $_POST['minutosRes'][$keyP];
						$valores['segundos'] 		= $_POST['segundosRes'][$keyP];
						$valores['tiempoT'] 		= $valores['horas'] + $valores['minutos'] + $valores['segundos'];

						$validator['horas'] 		= 'numeric|min_numeric,0';
						$validator['minutos'] 		= 'numeric|min_numeric,0|max_numeric,59';
						$validator['segundos'] 		= 'numeric|min_numeric,0|max_numeric,59';
						$validator['tiempoT'] 		= 'required|numeric|min_numeric,1';

						if($wod->permPenalizacion)
						{
							$valores['reps'] 	= $_POST['reps'][$keyP];
							$validator['reps'] 	= 'required|numeric|min_numeric,0';
						}

						if($wod->permTieBreak)
						{
							$valores['horasTB'] 		= $_POST['horasTB'][$keyP];
							$valores['minutosTB'] 		= $_POST['minutosTB'][$keyP];
							$valores['segundosTB'] 		= $_POST['segundosTB'][$keyP];
							$validator['horasTB'] 		= 'numeric|min_numeric,0';
							$validator['minutosTB'] 	= 'numeric|min_numeric,0|max_numeric,59';
							$validator['segundosTB'] 	= 'numeric|min_numeric,0|max_numeric,59';
						}

						$gumpValidator = new Gump();
						$is_valid = $gumpValidator->validate($valores, $validator);

						if($wod->permPenalizacion && ($valores['tiempoT'] > $wod->timeCap) ) 
							$is_valid = false;

						if($is_valid !== true)
							$operacion['errores'][$valueP] = $gumpValidator->errors();
						else
							$resultados[$valueP] = self::procesaResultado($valores, $wod);
					}
					else
					{						
						if( ($wod->tipo == 4) )
						{
							$valores['resultado'] 		= $_POST['resultado'][$keyP];
							$validator['resultado'] 	= 'required|numeric|min_numeric,0';

							if($wod->permTieBreak)
							{
								$valores['horasTB'] 		= $_POST['horasTB'][$keyP];
								$valores['minutosTB'] 		= $_POST['minutosTB'][$keyP];
								$valores['segundosTB'] 		= $_POST['segundosTB'][$keyP];

								$validator['horasTB'] 		= 'numeric|min_numeric,0';
								$validator['minutosTB'] 	= 'numeric|min_numeric,0|max_numeric,59';
								$validator['segundosTB'] 	= 'numeric|min_numeric,0|max_numeric,59';
							}

							$gumpValidator = new Gump();
							$is_valid = $gumpValidator->validate($valores, $validator);

							if($is_valid !== true)
								$operacion['errores'][$valueP] = $gumpValidator->errors();
							else
								$resultados[$valueP] = self::procesaResultado($valores, $wod);

						}
						else
						{						
							if( ($wod->tipo == 9) )
							{
								$valores['resultado'] 	= $_POST['resultado'][$keyP];
								$valores['unidadRes'] 	= $_POST['unidadRes'][$keyP];

								if($wod->permTieBreak)
								{
									$valores['tiebreak'] 	= $_POST['bodyw'][$keyP];
									$valores['unidadAtl'] 	= $_POST['unidadAtl'][$keyP];

									$validator['tiebreak'] 	= 'required|numeric|min_numeric,1';
								}

								$validator['resultado'] = 'required|numeric|min_numeric,0';

								$gumpValidator = new Gump();
								$is_valid = $gumpValidator->validate($valores, $validator);

								if($is_valid !== true)
									$operacion['errores'][$valueP] = $gumpValidator->errors();
								else
									$resultados[$valueP] = self::procesaResultado($valores, $wod);
							}
							else
							{
								$valores['resultado'] 		= $_POST['resultado'][$keyP];

								$validator['resultado'] 	= 'required|numeric|min_numeric,0';

								$gumpValidator = new Gump();
								$is_valid = $gumpValidator->validate($valores, $validator);

								if($is_valid !== true)
									$operacion['errores'][$valueP] = $gumpValidator->errors();
								else
									$resultados[$valueP] = self::procesaResultado($valores, $wod);
							}
						}
					}
				}
			}
		}

		$registrarResultados = $eventos->registrarResultados($resultados, $resultadosViejos);
		self::postResultados($eventoId, $categoriaId, $wodId, $operacion);
	}

	public function modEventoMiResultado($eventoId, $categoriaId = 0, $wodId = 0, $info = array())
	{
		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			self::eventoNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$permisoMR = self::checkPermisoModificacionResultados($evento, $logeado);

		if(!$permisoMR)
		{
			Cuenta::sectionOnlyJudges($evento);
			return;
		}

		if($evento->disciplina != 10)
		{
			Cuenta::sectionNotPermission();
			return;
		}

		$categoria = $eventos->getCategoria($evento->id, $categoriaId);

		if(!$categoria)
		{
			self::categoriaNotFound($evento);
			return;
		}

		$wod = $eventos->getWOD($evento->id, $wodId);

		if(!$wod)
		{
			self::wodNotFound($evento, $categoria);
			return;
		}

		$persona = $logeado['persona'];
		$registrado = $eventos->getRegistro($evento->id, $persona->idPersona);
		$estoy = false;

		if($registrado)
		{
			$estoy = $registrado->aprobado;

			if($registrado->categoriaId != $categoriaId)
			{
				Cuenta::sectionNotPermission();
				return;
			}
		}

		$data['title'] = "Registro de Resultados";
		$data['csrf_token'] = Csrf::makeToken();
		$data['logeado'] = $logeado;
		$data['evento'] = $evento;
		$data['categoria'] = $categoria;
		$data['wod'] = $wod;

		View::renderTemplate('header', $data);
		View::render('tracker/cuenta/modevento/miresultado', $data, $info);
		View::renderTemplate('footer', $data);
	}

	public function modEventoMiResultadoPOST($eventoId, $categoriaId = 0, $wodId = 0, $info = array())
	{		
		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			self::eventoNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$permisoMR = self::checkPermisoModificacionResultados($evento, $logeado);

		if(!$permisoMR)
		{
			Cuenta::sectionOnlyJudges($evento);
			return;
		}

		if($evento->disciplina != 10)
		{
			Cuenta::sectionNotPermission();
			return;
		}

		$categoria = $eventos->getCategoria($evento->id, $categoriaId);

		if(!$categoria)
		{
			self::categoriaNotFound($evento);
			return;
		}

		$wod = $eventos->getWOD($evento->id, $wodId);

		if(!$wod)
		{
			self::wodNotFound($evento, $categoria);
			return;
		}

		$persona = $logeado['persona'];
		$registrado = $eventos->getRegistro($evento->id, $persona->idPersona);
		$estoy = false;

		if($registrado)
		{
			$estoy = $registrado->aprobado;

			if($registrado->categoriaId != $categoriaId)
			{
				Cuenta::sectionNotPermission();
				return;
			}
		}
		if (!Csrf::isTokenValid())
		{
			Cuenta::sectionCheckInvalid();
			return;
		}

		$valueP = $registrado->id;

		$operacion = array();
		$operacion['errores'] = array();
		$operacion['operaciones'] = array();

		$valores 				= array();
		$valores['regID'] 		= $valueP;
		$valores['evento'] 		= $evento;
		$valores['videoLink'] 	= $_POST['video'];
		$valores['scaled'] 		= $_POST['scaled'];
		$valores['judgedBy'] 	= $_POST['juez'];

		$validator = array();
		$validator['videoLink'] = 'required|valid_url';

		$resultadosViejos = $eventos->getResultadosWOD($evento->id, $wodId);
		$resultados = array();

		if($wod->tipo == 3)
		{
			$valores['horas'] 			= $_POST['horasRes'];
			$valores['minutos'] 		= $_POST['minutosRes'];
			$valores['segundos'] 		= $_POST['segundosRes'];
			$valores['tiempoRes'] 		= $valores['horas'] + $valores['minutos'] + $valores['segundos'];

			$validator['horas'] 		= 'numeric|min_numeric,0';
			$validator['minutos'] 		= 'numeric|min_numeric,0|max_numeric,59';
			$validator['segundos'] 		= 'numeric|min_numeric,0|max_numeric,59';
			$validator['tiempoRes'] 	= 'required|numeric|min_numeric,1';

			if($wod->permPenalizacion)
			{
				$valores['reps'] 	= $_POST['reps'];
				$validator['reps'] 	= 'required|numeric|min_numeric,0';
			}

			if($wod->permTieBreak)
			{
				$valores['horasTB'] 		= $_POST['horasTB'];
				$valores['minutosTB'] 		= $_POST['minutosTB'];
				$valores['segundosTB'] 		= $_POST['segundosTB'];
				$validator['horasTB'] 		= 'numeric|min_numeric,0';
				$validator['minutosTB'] 	= 'numeric|min_numeric,0|max_numeric,59';
				$validator['segundosTB'] 	= 'numeric|min_numeric,0|max_numeric,59';
			}

			$gumpValidator = new Gump();
			$is_valid = $gumpValidator->validate($valores, $validator);

			if($wod->permPenalizacion && ($valores['tiempoRes'] > $wod->timeCap) ) 
			{
				$is_valid = false;
			}

			if($is_valid !== true)
				$operacion['errores'] = $gumpValidator->errors();
			else
				$resultados[$valueP] = self::procesaResultado($valores, $wod);
		}
		else
		{						
			if( ($wod->tipo == 4) )
			{
				$valores['resultado'] 		= $_POST['resultado'];

				$validator['resultado'] 	= 'required|numeric|min_numeric,0';

				if($wod->permTieBreak)
				{
					$valores['horasTB'] 		= $_POST['horasTB'];
					$valores['minutosTB'] 		= $_POST['minutosTB'];
					$valores['segundosTB'] 		= $_POST['segundosTB'];

					$validator['horasTB'] 		= 'numeric|min_numeric,0';
					$validator['minutosTB'] 	= 'numeric|min_numeric,0|max_numeric,59';
					$validator['segundosTB'] 	= 'numeric|min_numeric,0|max_numeric,59';
				}

				$gumpValidator = new Gump();
				$is_valid = $gumpValidator->validate($valores, $validator);

				if($is_valid !== true)
					$operacion['errores'] = $gumpValidator->errors();
				else
					$resultados[$valueP] = self::procesaResultado($valores, $wod);
			}
			else
			{						
				if( ($wod->tipo == 9) )
				{
					$valores['resultado'] 	= $_POST['resultado'];
					$valores['unidadRes'] 	= $_POST['unidadRes'];

					if($wod->permTieBreak)
					{
						$valores['tiebreak'] 	= $_POST['bodyw'];
						$valores['unidadAtl'] 	= $_POST['unidadAtl'];

						$validator['tiebreak'] 	= 'required|numeric|min_numeric,1';
					}

					$validator['resultado'] = 'required|numeric|min_numeric,0';

					$gumpValidator = new Gump();
					$is_valid = $gumpValidator->validate($valores, $validator);

					if($is_valid !== true)
						$operacion['errores'] = $gumpValidator->errors();
					else
						$resultados[$valueP] = self::procesaResultado($valores, $wod);
				}
				else
				{
					$valores['resultado'] 		= $_POST['resultado'];

					$validator['resultado'] 	= 'required|numeric|min_numeric,0';

					$gumpValidator = new Gump();
					$is_valid = $gumpValidator->validate($valores, $validator);

					if($is_valid !== true)
						$operacion['errores'] = $gumpValidator->errors();
					else
						$resultados[$valueP] = self::procesaResultado($valores, $wod);
				}
			}
		}

		if(!empty($resultados))
			$registrarResultado = $eventos->registrarResultados($resultados, $resultadosViejos);

		self::postMiResultado($eventoId, $categoriaId, $wodId, $operacion);
	}

	private function procesaResultado($valores, $wod)
	{
		$resultado = self::entidadResultado($valores['regID'], $valores['evento']->id, $wod->id);
		$evento = $valores['evento'];

		if($evento->disciplina == 10)
		{
			$resultado['videoLink'] = $valores['videoLink'];
			$resultado['scaled'] 	= $valores['scaled'];
		}

		if($wod->tipo == 3)
		{
			$tiempoRes = 0;
			$tiempoRes = $valores['horas'] * 60 * 60;
			$tiempoRes = $tiempoRes + $valores['minutos'] * 60;
			$tiempoRes = $tiempoRes + $valores['segundos'];
			$tiempoRes = $tiempoRes * 1000;

			$resultado['resultado'] = $tiempoRes;

			if($wod->permTieBreak)
			{
				$tiempoTB = 0;
				$tiempoTB = $valores['horasTB'] * 60 * 60;
				$tiempoTB = $tiempoTB + $valores['minutosTB'] * 60;
				$tiempoTB = $tiempoTB + $valores['segundosTB'];
				$tiempoTB = $tiempoTB * 1000;

				$resultado['tiebreak'] = $tiempoTB;
			}

			if($wod->permPenalizacion)
				$resultado['reps'] = $valores['reps'];

			$resultado['judgedBy'] = $valores['judgedBy'];
		}
		else
		{
			if($wod->tipo == 4)
			{
				$resultado['resultado'] = $valores['resultado'];

				if($wod->permTieBreak)
				{
					$tiempoTB = 0;
					$tiempoTB = $valores['horasTB'] * 60 * 60;
					$tiempoTB = $tiempoTB + $valores['minutosTB'] * 60;
					$tiempoTB = $tiempoTB + $valores['segundosTB'];
					$tiempoTB = $tiempoTB * 1000;

					$resultado['tiebreak'] = $tiempoTB;
				}

				$resultado['judgedBy'] = $valores['judgedBy'];
			}
			else
			{				
				if($wod->tipo == 9)
				{
					$resultado['resultado'] = $valores['resultado'];

					if($valores['unidadRes'] == "lb")
						$resultado['resultado'] = ($valores['resultado'] / 2.20462);
					
					$resultado['tiebreak'] = $valores['tiebreak'];

					if($valores['unidadAtl'] == "lb")
						$resultado['tiebreak'] = ($valores['tiebreak'] / 2.20462);

					$resultado['judgedBy'] = $valores['judgedBy'];
				}
				else
				{
					$resultado['resultado'] = $valores['resultado'];
					$resultado['judgedBy'] = $valores['judgedBy'];
				}
			}
		}

		return $resultado;
	}

	private function postResultados($eventoId, $categoriaId = 0, $wodId = 0, $info = array())
	{
		$cuentas = new Models\Cuentas();
		$errores = array();
		$errStr = array();

		if(empty($info['errores']))
		{
			self::modEventoResultados3($eventoId, $categoriaId, $wodId, array());
			return;
		}

		foreach ($info['errores'] as $key => $value)
		{
			$persona = $cuentas->getPersonaPorRegistro($key);
			$errores[$key]['persona'] = $persona;
			$errores[$key]['errores'] = $value;
			$errStr[] = $persona->nombre." ".$persona->apellido;
		}

		$data['mensaje'] = "Verifique los resultados ingresados para los participantes:<br/><br/>";
		$data['mensaje'] .= implode(", ", $errStr);

		$data['title'] = "Verifique los resultados";
		$data['titulo'] = "Verifique los resultados";
		$data['href'] = DIR."cuenta/modevento/".$eventoId."/miresultado/".$categoriaId."/".$wodId;
		$data['boton'] = "Continuar";

		View::renderTemplate('header', $data);
		View::render('tracker/mensaje', $data);
		View::renderTemplate('footer', $data);
	}

	private function postMiResultado($eventoId, $categoriaId = 0, $wodId = 0, $info = array())
	{
		$cuentas = new Models\Cuentas();
		$errores = array();
		$errStr = array();

		if(empty($info['errores']))
		{
			self::modEventoMiResultado($eventoId, $categoriaId, $wodId, array());
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$persona = $logeado['persona'];
		$errores['persona'] = $persona;
		$errores['errores'] = $value;

		$data['mensaje'] = "Verifique los resultados ingresados.<br/><br/>";

		$data['title'] = "Verifique los resultados";
		$data['titulo'] = "Verifique los resultados";
		$data['href'] = DIR."cuenta/modevento/".$eventoId."/miresultado/".$categoriaId."/".$wodId;
		$data['boton'] = "Continuar";

		View::renderTemplate('header', $data);
		View::render('tracker/mensaje', $data);
		View::renderTemplate('footer', $data);
	}

	private function entidadResultado($valueP, $eventoId, $wodId)
	{
		$resultados = array();
		$resultados['eventoId'] = $eventoId;
		$resultados['wodId'] = $wodId;
		$resultados['fecha'] = time();
		$resultados['registroAtletaId'] = $valueP;

		return $resultados;
	}

	public function aprobarEvento($eventoId)
	{
		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		if(!$logeado['persona']->esAdmin)
		{
			Cuenta::sectionNotPermission();
			return;
		}

		$eventos = new Models\Eventos();
		$cuentas = new Models\Cuentas();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			self::eventoNotFound();
			return;
		}

		$aprobacion = $eventos->aprobarEvento($evento->id);

		if($aprobacion > 0)
		{
			$organizador = $cuentas->getPersona($evento->creadorId);

			$gestor = fopen("app/templates/mail/mail.html", "r");
			$txt = "";

			while (!feof($gestor))
			{
				$line_of_text = fgets($gestor);
				$txt .= $line_of_text;
			}
			fclose($gestor);

			$bdy = "Saludos ".$organizador->nombre." ".$organizador->apellido.", <br/><br/>Le informamos que su evento de nombre \"".$evento->nombre."\" ha sido aprobado correctamente.<br/><br/>A partir de ahora su evento podra ser mostrado en la lista de eventos del sitio.";
			$strMail = str_replace("[MENSAJE]", $bdy, $txt);
			$strMail = str_replace("[LOGO1]", DIR."app/templates/<?php echo TEMPLATE;?>/img/logoCuadrado.png", $strMail);

			$mail = new \Helpers\PhpMailer\Mail();

			$mail->setFrom(SITEEMAIL);
			$mail->addAddress($organizador->correo);
			$mail->subject('Evento Aprobado ('.$evento->nombre.") - ".SITETITLE);
			$mail->body($strMail);
			$mail->send();
		}

		$data['title'] = "Evento Aprobado";
		$data['titulo'] = "El evento ha sido aprobado correctamente";
		$data['href'] = DIR."evento/".$evento->internalURL;
		$data['boton'] = "Volver";

		View::renderTemplate('header', $data);
		View::render('tracker/mensaje', $data);
		View::renderTemplate('footer', $data);
	}

	public function desaprobarEvento($eventoId)
	{
		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		if(!$logeado['persona']->esAdmin)
		{
			Cuenta::sectionNotPermission();
			return;
		}

		$eventos = new Models\Eventos();
		$cuentas = new Models\Cuentas();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			self::eventoNotFound();
			return;
		}

		$desaprobacion = $eventos->desaprobarEvento($evento->id);

		$data['title'] = "Evento Desaprobado";
		$data['titulo'] = "El evento ha sido desaprobado correctamente";
		$data['href'] = DIR."evento/".$evento->internalURL;
		$data['boton'] = "Volver";

		View::renderTemplate('header', $data);
		View::render('tracker/mensaje', $data);
		View::renderTemplate('footer', $data);
	}

	public function verPago($pagoId)
	{		
		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$eventos = new Models\Eventos();
		$cuentas = new Models\Cuentas();

		$pago = $eventos->getPago($pagoId);

		if($pago)
		{
			$solicitud = $eventos->getRegistroPorIdPago($pago->id);

			$persona = $cuentas->getPersona($solicitud->personaId);
			$evento = $eventos->getEvento($solicitud->eventoId);

			if( ($evento->creadorId != $logeado['persona']->idPersona) && (!$logeado['persona']->esAdmin))
			{
				Cuenta::sectionNotPermission();
				return;
			}
			
			$categoria = $eventos->getCategoria($solicitud->eventoId, $solicitud->categoriaId);
		}

		$data['pago'] = $pago;
		$data['persona'] = $persona;
		$data['categoria'] = $categoria;
		$data['evento'] = $evento;
		$data['title'] = "Detalle de pago";

		View::renderTemplate('header', $data);
		View::render('tracker/evento/pago', $data);
		View::renderTemplate('footer', $data);
	}

	public function verPagoEquipo($pagoId)
	{		
		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$eventos = new Models\Eventos();
		$cuentas = new Models\Cuentas();
		$equipos = new Models\Equipos();

		$pago = $eventos->getPago($pagoId);
		$data = array();

		if($pago)
		{
			$solicitud = $eventos->getRegistroPorIdPagoEquipo($pago->id);
			$equipo = $equipos->getEquipo($solicitud->equipoId);
			$orgEquipo = $cuentas->getPersona($equipo->capitanId);
			$evento = $eventos->getEvento($solicitud->eventoId);


			if( ($evento->creadorId != $logeado['persona']->idPersona) && (!$logeado['persona']->esAdmin))
			{
				Cuenta::sectionNotPermission();
				return;
			}
			
			$data['equipo'] = $equipo;
			$data['persona'] = $orgEquipo;
			$categoria = $eventos->getCategoria($solicitud->eventoId, $solicitud->categoriaId);
		}

		$data['pago'] = $pago;
		$data['categoria'] = $categoria;
		$data['evento'] = $evento;
		$data['title'] = "Detalle de pago";

		View::renderTemplate('header', $data);
		View::render('tracker/evento/pagoEquipo', $data);
		View::renderTemplate('footer', $data);
	}

	public function modEventoPlan($eventoId, $info = array())
	{
		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			self::eventoNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		if(!$logeado['persona']->esAdmin)
		{
			Cuenta::sectionNotPermission();
			return;
		}

		$cuentas = new Models\Cuentas();
		$web = new Models\Web();
		$planes = $web->getPlanes();

		$data['title'] = "Modificar Plan";
		$data['csrf_token'] = Csrf::makeToken();
		$data['evento'] = $evento;
		$data['planes'] = $planes;

		if(empty($info))
		{
			$info['mensajes'] = array();
			$info['data'] = array();
		}

		View::renderTemplate('header', $data);
		View::render('tracker/cuenta/modevento/plan', $data, $info);
		View::renderTemplate('footer', $data);
	}

	public function modEventoPlanPOST($eventoId, $info = array())
	{
		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			self::eventoNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		if(!$logeado['persona']->esAdmin)
		{
			Cuenta::sectionNotPermission();
			return;
		}

		if (!Csrf::isTokenValid())
		{
			Cuenta::sectionCheckInvalid();
			return;
		}

		if(isset($_POST['submit']))
		{
			$tipoSubscripcion = filter_input(INPUT_POST, 'tipoSubscripcion');

			if($tipoSubscripcion)
			{
				$web = new Models\Web();
				$planes = $web->getPlanes();

				if(isset($planes[$tipoSubscripcion]))
				{
					$eventos->actualizarEventoInfo(array('tipoSubscripcion' => $tipoSubscripcion), array('id' => $evento->id));
				}
			}
		}

		Url::redirect('evento/'.$evento->internalURL);
	}

	public function modEventoEquipoCrear($eventoId, $info = array())
	{
		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			self::sectionNeedLogin();
			return;
		}

		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			Eventos::eventoNotFound();
			return;
		}

		$persona = $logeado['persona'];
		$registrado = $eventos->getRegistro($evento->id, $persona->idPersona);
		$estoy = false;

		if(!$registrado)
		{
			Cuenta::sectionNotRegisteredInCategory();
			return;
		}

		if($registrado)
		{
			$estoy = $registrado->aprobado;
			
			if(!$estoy)
			{
				Cuenta::sectionNotRegisteredInCategory();
				return;
			}
		}

		$data['eventoTeam'] = true;
		//$data['categorias'] = $eventos->getCategorias($evento->id);
		$data['categorias'] = array();
		$data['title'] = "Crear Equipo";
		$data['csrf_token'] = Csrf::makeToken();

		if(empty($info))
		{
			$info['mensajes'] = array();
			$info['campos'] = array();
			$info['data'] = array();
			//$info['data']['urlEvento'] = "http://";
		}

		View::renderTemplate('header', $data);
		View::render('tracker/equipo/crear', $data, $info);
		View::renderTemplate('footer', $data);
	}

	public function modEventoEquipoCrearPOST($eventoId)
	{
		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			self::sectionNeedLogin();
			return;
		}

		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			Eventos::eventoNotFound();
			return;
		}

		$persona = $logeado['persona'];

		if (!Csrf::isTokenValid())
		{
			self::sectionCheckInvalid();
			return;
		}

		if(isset($_POST['submit']))
		{
			$nombre 		= filter_input(INPUT_POST, 'nombre');

			$persona = $logeado['persona'];
			$registrado = $eventos->getRegistro($evento->id, $persona->idPersona);
			$estoy = false;
			$categoriaId = null;

			if($registrado)
			{
				$categoriaId = $registrado->categoriaId;
			}

			$categoria = $eventos->getCategoria($evento->id, $categoriaId);

			if(!$categoria)
			{
				Eventos::categoriaNotFound($evento);
				return;
			}

			$valores 				= array();
			$valores['nombre'] 		= $nombre;
			$valores['eventoId'] 	= $evento->id;

			$info['data'] = $valores;

			$validator 				  = array();
			$validator['nombre'] 	  = 'required';

			$gumpValidator = new Gump();
			$is_valid = $gumpValidator->validate($_POST, $validator);

			if($is_valid !== true)
				$info['campos'] = $gumpValidator->errors();

			if(!empty($info['campos']) || !empty($info['mensajes']))
			{
				self::modEventoEquipoCrear($evento->id, $info);
				return;
			}
			else
			{
				$equipos = new Models\Equipos();

				//$new['capitanId'] = $persona->idPersona;
				$new['nombre'] = $nombre;
				$new['fechaRegistro'] = time();
				$new['eventoId'] = $evento->id;
				$new['categoriaId'] = $categoria->id;
				$equipoId = $eventos->crearEquipo($new);
				//$registroId = $equipos->agregarAtleta($persona->idPersona, $equipoId, true);


				Url::redirect('cuenta/modevento/'.$evento->id.'/crearEquipo/'.$equipoId.'/participantes');
			}
		}
	}

	public function modEventoEquipoCrearParticipantes($eventoId, $equipoId, $info = array())
	{
		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			self::sectionNeedLogin();
			return;
		}

		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			Eventos::eventoNotFound();
			return;
		}

		$equipo = $eventos->getEquipo($evento->id, $equipoId);

		if(!$equipo)
		{
			Equipos::equipoNotFound();
			return;
		}

		$categoria = $eventos->getCategoria($evento->id, $equipo->categoriaId);

		if(!$categoria)
		{
			Eventos::categoriaNotFound($evento);
			return;
		}


		$atlCategoria = $eventos->getAtletasCategoria($evento->id, $categoria->id);
		$atlEquipados = $eventos->getAtletasEquipados($evento->id, $categoria->id);

		foreach ($atlCategoria as $key => $value)
		{
			if(isset($atlEquipados[$key]))
				$value->ocupado = true;
			else
				$value->ocupado = false;
		}

		$data['atletas'] = $atlCategoria;
		$data['evento'] = $evento;
		$data['categoria'] = $categoria;
		$data['equipo'] = $equipo;
		$data['title'] = "Modificacion Equipo";
		$data['eventoTeam'] = true;
		$data['csrf_token'] = Csrf::makeToken();

		//print_r($atlCategoria);
		View::renderTemplate('header', $data);
		View::render('tracker/cuenta/modevento/atletasequipo', $data, $info);
		View::renderTemplate('footer', $data);
	}

	public function modEventoEquipoCrearParticipantesPOST($eventoId, $equipoId, $info = array())
	{

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			self::sectionNeedLogin();
			return;
		}

		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			Eventos::eventoNotFound();
			return;
		}

		$equipo = $eventos->getEquipo($evento->id, $equipoId);

		if(!$equipo)
		{
			Equipos::equipoNotFound();
			return;
		}

		$categoria = $eventos->getCategoria($evento->id, $equipo->categoriaId);

		if(!$categoria)
		{
			Eventos::categoriaNotFound($evento);
			return;
		}

		$persona = $logeado['persona'];

		if (!Csrf::isTokenValid())
		{
			self::sectionCheckInvalid();
			return;
		}

		if(isset($_POST['submit']))
		{
			//print_r($_POST);

			$tot = count($_POST['wodR']);

			if($evento->id == 71 || $evento->id == 74)
			{
				if($tot != 3)
				{
					Equipos::noCumpleCantIntegrantes2();
					return;
				}
			}


			foreach ($_POST['wodR'] as $key => $value)
			{
				$eventos->agregarAtletaEquipo($equipoId, $key);
			}

			Url::redirect("evento/".$evento->id."/team".$categoria->id);
		}
	}

	public function ajax()
	{
	 	$data['title'] = "Eventos";
		$data['byYear'] = array();
		$data['fechaActual'] = time();

		View::renderTemplate('header', $data);
		View::render('tracker/ajax', $data);
		View::renderTemplate('footer', $data);
	}

	public function ajaxEventos()
	{
		$eventos = new Models\Eventos();
		$eventosAp = $eventos->getEventosAprobados();
		$eventos->prepararEventos($eventosAp);

		usort($eventosAp, 'self::usortByDate');
		$byYear = array();

		$cuenta = new Cuenta();
		$logeado = Models\Cuentas::getSession();

		foreach ($eventosAp as $keyE => $valueE)
		{
			if(($valueE->visible) || ($logeado && $logeado['persona']->esAdmin && !$valueE->visible))
				$byYear[$valueE->anno][] = $valueE;
		}

		$ret = array();
		$ret['success'] = true;
		$ret['data']['annos'] = count($byYear);
		$ret['data']['eventos'] = $byYear;

		print_r(json_encode($ret));
	}
}

?>