<?php
namespace Controllers;

use Core\View;
use Core\Controller;
use Helpers\Sesiones;
use Helpers\Url;
use Helpers\Gump;
use Helpers\CsrfNew as Csrf;
use Helpers\Codes;
use Helpers\Password;
use Helpers\ResizeImage;
use Models;

class Cuenta extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function boxNotFound()
	{
		$data['title'] = "Box no encontrado";
		$data['titulo'] = "Box no encontrado";
		$data['mensaje'] = "El Box solicitado no fue encontrado.";
		$data['href'] = DIR."";
		$data['boton'] = "Ir a Inicio";

		View::renderTemplate('header', $data);
		View::render('tracker/mensaje', $data);
		View::renderTemplate('footer', $data);
	}

	public function profileNotFound()
	{
		$data['title'] = "Perfil no encontrado";
		$data['titulo'] = "Perfil no encontrado";
		$data['mensaje'] = "El perfil solicitado no fue encontrado.";
		$data['href'] = DIR."";
		$data['boton'] = "Ir a Inicio";

		View::renderTemplate('header', $data);
		View::render('tracker/mensaje', $data);
		View::renderTemplate('footer', $data);
	}

	public function reqIdent($persona)
	{
		$data['title'] = "Requiere identificaci&oacute;n";
		$data['titulo'] = "Requiere identificaci&oacute;n";
		$data['mensaje'] = "Debe especificar su n&uacute;mero de identificaci&oacute;n (CI/Pasaporte/etc.) en su perfil.";
		$data['href'] = DIR."perfil/".$persona->idPersona."/edit";
		$data['boton'] = "Editar Perfil";

		View::renderTemplate('header', $data);
		View::render('tracker/mensaje', $data);
		View::renderTemplate('footer', $data);
	}

	public function sectionOnlyJudges($evento)
	{
		$data['title'] = "Secci&oacute;n exclusiva para jueces";
		$data['titulo'] = "Secci&oacute;n exclusiva para jueces";
		$data['mensaje'] = "Esta secci&oacute;n est&aacute; permitida exclusivamente para jueces del evento.";
		$data['href'] = DIR."evento/".$evento->internalURL;
		$data['boton'] = "Ir al evento";

		View::renderTemplate('header', $data);
		View::render('tracker/mensaje', $data);
		View::renderTemplate('footer', $data);
	}

	public function sectionNeedLogin()
	{
		$data['title'] = "Debe iniciar sesi&oacute;n";
		$data['titulo'] = "Debe iniciar sesi&oacute;n";
		$data['mensaje'] = "Debe acceder para poder ingresar a esta secci&oacute;n.";

		$data['botones'] = array();
		$data['botones'][0]['href'] = DIR."acceder";
		$data['botones'][0]['boton'] = "Acceder";
		$data['botones'][1]['href'] = DIR."registrar";
		$data['botones'][1]['boton'] = "Registrarse";

		View::renderTemplate('header', $data);
		View::render('tracker/mensaje', $data);
		View::renderTemplate('footer', $data);
	}

	public function sectionNotPermission()
	{
		$data['title'] = "Secci&oacute;n no Permitida";
		$data['titulo'] = "Secci&oacute;n no Permitida";
		$data['mensaje'] = "No tiene los permisos necesarios para poder ingresar a esta secci&oacute;n.";
		$data['href'] = DIR."";
		$data['boton'] = "Ir a Inicio";

		View::renderTemplate('header', $data);
		View::render('tracker/mensaje', $data);
		View::renderTemplate('footer', $data);
	}

	public function sectionNotRegisteredInCategory()
	{
		$data['title'] = "No est&aacute; registrado";
		$data['titulo'] = "No est&aacute; registrado";
		$data['mensaje'] = "No se encuentra registrado en este evento.";
		$data['href'] = DIR."";
		$data['boton'] = "Ir a Inicio";

		View::renderTemplate('header', $data);
		View::render('tracker/mensaje', $data);
		View::renderTemplate('footer', $data);
	}

	public function poseeEquipoEnEvento()
	{
		$data['title'] = "Ya posee equipo";
		$data['titulo'] = "Ya posee equipo en este evento";
		$data['mensaje'] = "Ya se encuentra registrado en este evento.";
		$data['href'] = DIR."";
		$data['boton'] = "Ir a Inicio";

		View::renderTemplate('header', $data);
		View::render('tracker/mensaje', $data);
		View::renderTemplate('footer', $data);
	}

	public function sectionCheckInvalid()
	{
		$data['title'] = "Error";
		$data['titulo'] = "Error";
		$data['mensaje'] = "Se detect&oacute; un error en la comprobaci&oacute;n de integridad de los datos ingresados. Intente de nuevo.";
		$data['href'] = DIR."";
		$data['boton'] = "Ir al inicio";

		View::renderTemplate('header', $data);
		View::render('tracker/mensaje', $data);
		View::renderTemplate('footer', $data);
	}
	
	public function index()
	{
		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			self::sectionNeedLogin();
			return;
		}
		
		$persona = $logeado['persona'];

		self::perfil($persona->idPersona);
	}

	public function perfil($idPersona)
	{		
		$logeado = Models\Cuentas::getSession();
		$mio = false;
		$esAdmin = false;

		if($logeado)
		{	
			if($logeado['persona']->idPersona == $idPersona)
				$mio = true;

			if($logeado['persona']->esAdmin)
				$esAdmin = true;
		}

		$perfiles = new Models\Perfiles();
		$cuentas = new Models\Cuentas();
		$eventos = new Models\Eventos();
		$persona = $cuentas->getPersona($idPersona);
		$perfiles->prepararCuenta($persona);

		if(!$persona)
		{
			self::profileNotFound();
			return;
		}

		if($mio)
		{
			$eventosPersona 			= $perfiles->getRegistrosEvento($persona->idPersona);
			$eventosSolicitudPersona 	= $perfiles->getRegistrosPendientesEvento($persona->idPersona);
			$eventosOrganizados 		= $perfiles->getEventosOrganizados($persona->idPersona);
			$eventosJuez 				= $perfiles->getEventosJuez($persona->idPersona);

			$eventosJuezP = array();
			$eventosJuezS = array();
			$eventosJuezA = array();
			$eventosJuezF = array();

			foreach ($eventosJuez as $key => $value)
			{
				if($value->juezPrincipal == $persona->idPersona)
					$eventosJuezP[] = $value;

				if($value->juezSuplente == $persona->idPersona)
					$eventosJuezS[] = $value;

				if($value->eventoFinalizado)
					$eventosJuezF[] = $value;
				else
					$eventosJuezA[] = $value;
			}

			$eventosPersonaF 			= array();
			$eventosPersonaS 			= array();
			$eventosPersonaFinalizados 	= array();
			$eventosPersonaActivos 		= array();

			foreach ($eventosPersona as $key => $value)
			{
				if($value->eventoFinalizado)
					$eventosPersonaFinalizados[] = $value;
				else
					$eventosPersonaActivos[] = $value;

				$eventosPersonaF[] = $value;
			}

			foreach ($eventosSolicitudPersona as $key => $value)
			{
				$eventosPersonaS[] = $value;
			}

			$info = new \stdClass();
			$info->eventos = count($eventosPersonaF);
			$info->eventosAct = count($eventosPersonaActivos);
			$info->eventosFin = count($eventosPersonaFinalizados);
			$info->eventosSolicitud = count($eventosPersonaS);
			$info->eventosOrganizados = count($eventosOrganizados);
			$info->eventosJuez = count($eventosJuezP) + count($eventosJuezS);
			$info->eventosJuezActivo = count($eventosJuezA);
			$info->eventosJuezFinalizado = count($eventosJuezF);
			
			$data['info'] = $info;
		}

		$participaciones = $perfiles->getParticipaciones($persona->idPersona);
		
		foreach ($participaciones as $key => $valueE)
		{
			if(!$valueE->visible && !$esAdmin && !$mio)
				unset($participaciones[$key]);
		}

		$eventos->prepararEventos($participaciones);
		$equipos = new Models\Equipos();

		$equiposInt = $perfiles->getEquiposIntegrante($persona->idPersona);
		$equipos->prepararEquipos($equiposInt);
		
		$data['equiposIntegrante'] = $equiposInt;
		$data['participaciones'] = $participaciones;
		$data['title'] = $persona->nombre." ".$persona->apellido;
		$data['logeado'] = $logeado;
		$data['persona'] = $persona;
		$data['fbablePerfil'] = true;
		$data['mio'] = $mio;
		$data['esAdmin'] = $esAdmin;

		View::renderTemplate('headerNORESPONSIVE', $data);
		View::render('tracker/perfil', $data);
		View::renderTemplate('footer', $data);
	}

	public function perfilEdit($idPersona, $info = array())
	{
		$logeado = Models\Cuentas::getSession();

		if(!$logeado )
		{
			self::profileNotFound();
			return;
		}

		if( ($logeado['persona']->idPersona != $idPersona) && (!$logeado['persona']->esAdmin) )
		{
			self::sectionNotPermission();
			return;
		}

		$perfiles = new Models\Perfiles();
		$cuentas = new Models\Cuentas();
		$persona = $cuentas->getPersona($idPersona);
		$perfiles->prepararCuenta($persona);

		$web = new Models\Web();

		//$persona = $logeado['persona'];
		
		$paises = $web->getPaises();
		$boxes = $web->getBoxes();

		if(empty($info))
		{
			$info['mensajes'] = array();
			$info['campos'] = array();
			$info['data'] = array();
		}

		$data['title'] = $persona->nombre." ".$persona->apellido;
		$data['csrf_token'] = Csrf::makeToken();
		$data['logeado'] = $logeado;
		$data['persona'] = $persona;
		$data['paises'] = $paises;
		$data['boxes'] = $boxes;

		View::renderTemplate('headerNORESPONSIVE', $data);
		View::render('tracker/perfilEdit', $data, $info);
		View::renderTemplate('footer', $data);
	}

	public function perfilEditPOST($idPersona)
	{
		$logeado = Models\Cuentas::getSession();

		if(!$logeado )
		{
			self::sectionNeedLogin();
			return;
		}

		if( ($logeado['persona']->idPersona != $idPersona) && !$logeado['persona']->esAdmin )
		{
			self::sectionNotPermission();
			return;
		}

		if (!Csrf::isTokenValid())
		{
			self::sectionCheckInvalid();
			return;
		}

		if(isset($_POST['submit']))
		{
			$cuentas = new Models\Cuentas();
			$persona = $cuentas->getPersona($idPersona);

			if(!$persona)
			{
				self::cuentaNoEncontrada();
				return;
			}

			$removerImagen = false;
	
			if(isset($_POST['removerImagen']))
				$removerImagen = true;

			$nombre 	= trim($_POST['nombre']);
			$apellido 	= trim($_POST['apellido']);
			$alias 		= trim($_POST['alias']);
			$ident 		= trim($_POST['ident']);
			$correo 	= trim($_POST['correo']);
            $telf 		= trim($_POST['i']);
			$estado 	= trim($_POST['estado']);
			$pic 		= trim($_POST['pic']);

			$ident = str_replace(".", "", $ident);
			$ident = str_replace(",", "", $ident);
			$ident = str_replace(" ", "", $ident);
			$ident = str_replace("-", "", $ident);

			$pais 		= $_POST['pais'];
			$boxId 		= $_POST['box'];
			$sexo 		= $_POST['sexo'];

			$dia 		= $_POST['dd'];
			$mes 		= $_POST['mm'];
			$anno 		= $_POST['yyyy'];

			$cleanV 	= $_POST['cleanV'];
			$cleanT 	= $_POST['cleanT'];
			$snatchV 	= $_POST['snatchV'];
			$snatchT 	= $_POST['snatchT'];
			$deadV 		= $_POST['deadV'];
			$deadT 		= $_POST['deadT'];
			$backV 		= $_POST['backV'];
			$backT 		= $_POST['backT'];

			$franH 		= $_POST['franH'];
			$franM 		= $_POST['franM'];
			$franS 		= $_POST['franS'];
			$franV 		= 0;

			$isabelH 	= $_POST['isabelH'];
			$isabelM 	= $_POST['isabelM'];
			$isabelS 	= $_POST['isabelS'];
			$isabelV 	= 0;

			$graceH 	= $_POST['graceH'];
			$graceM 	= $_POST['graceM'];
			$graceS 	= $_POST['graceS'];
			$graceV 	= 0;

			$run5kH 	= $_POST['run5kH'];
			$run5kM 	= $_POST['run5kM'];
			$run5kS 	= $_POST['run5kS'];
			$run5kV 	= 0;
			
			$cindyH 	= $_POST['cindyH'];
			$cindyM 	= $_POST['cindyM'];
			$cindyS 	= $_POST['cindyS'];
			$cindyV 	= 0;

			$fechaValida = checkdate($mes, $dia, $anno);
			$nombreArchivo = $persona->idPersona;
			$ext = null;
			$cuentas = new Models\Cuentas();

			$valores = array(
				'nombre' => ucwords(strtolower($nombre)),
				'apellido' => ucwords(strtolower($apellido)),
				'correo' => strtolower($correo),
				'ident' => $ident,
				'sexo' => $sexo
				);

			$info['data'] = $valores;

			$gumpValidator = new Gump();

			$is_valid = $gumpValidator->validate($valores, array(
				'nombre' => 'required|valid_name',
				'apellido' => 'required|valid_name',
				'correo' => 'required|valid_email',
				'ident' => 'required',
				'sexo' => 'required'
			));

			
			if($is_valid !== true)
			{
				$info['campos'] = $gumpValidator->errors();
			}

			if( (strcmp($persona->correo, $correo) != 0) && $cuentas->correoRegistrado($correo))
			{
				$info['campos']['correo']['registrado'] = true;
			}

			if(strcmp($persona->correo, $correo) != 0)
			{
				$cuentas->verificarCorreo($persona->idPersona);

				$codigo = Codes::genCode();
				$cuentas->setActivationCode($persona->idPersona, $codigo);

				$gestor = fopen("app/templates/mail/mail.html", "r");
				$txt = "";

				while (!feof($gestor))
				{
					$line_of_text = fgets($gestor);
					$txt .= $line_of_text;
				}
				fclose($gestor);

				$bdy = "Ingrese al siguiente enlace para verificar su correo: <a href=\"".DIR."verificar/correo/".$idPersona."/".$codigo."\">Verificar Correo</a> ";
				$strMail = str_replace("[MENSAJE]", $bdy, $txt);
				$strMail = str_replace("[LOGO1]", DIR."app/templates/".TEMPLATE."/img/logoCuadrado.png", $strMail);

				$mail = new \Helpers\PhpMailer\Mail();
				$mail->CharSet = "UTF-8";
				$mail->IsHTML(true);
				$mail->setFrom(SITEEMAIL);
				$mail->addAddress($persona->correo);
				$mail->subject('Pukiebook - Verificación de Correo');
				$mail->body($strMail);
				$mail->send();
			}

			if(!empty($info['campos']) || !empty($info['mensajes']))
			{
				self::perfilEdit($persona->idPersona, $info);
				return;
			}

			if($removerImagen)
			{
				if($persona->archivoFoto)
				{
					if(file_exists("./".PROFILEPICTUREPATH.$persona->archivoFoto))
						unlink("./".PROFILEPICTUREPATH.$persona->archivoFoto);

					$data['archivoFoto'] = null;
					$res = $cuentas->actualizarPersona($persona->idPersona, $data);
				}
			}
			else
			{
				$archivo = $_FILES['archivoPerfil'];

				if($archivo['error'] == UPLOAD_ERR_OK)
				{
					if($archivo['type'] == "image/jpeg")
						$ext = "jpg";
					else if($archivo['type'] == "image/png")
						$ext = "png";

					if($ext)
					{
						if($persona->archivoFoto)
						{
							if(file_exists("./".PROFILEPICTUREPATH.$persona->archivoFoto))
								unlink("./".PROFILEPICTUREPATH.$persona->archivoFoto);
							
							$data['archivoFoto'] = null;
							$res = $cuentas->actualizarPersona($persona->idPersona, $data);
						}

						$proc = move_uploaded_file($_FILES['archivoPerfil']['tmp_name'], "./".PROFILEPICTUREPATH.$nombreArchivo.".".$ext);

						if($proc)
						{
							$cuentas->actualizarPersona($persona->idPersona, array('archivoFoto' => $nombreArchivo.".".$ext));

	    					$resizer = new ResizeImage("./".PROFILEPICTUREPATH.$nombreArchivo.".".$ext);
	    					$resizer->resizeTo(400, 400, 'maxHeight');
	    					$resizer->cropTo(400,400);
	    					$resizer->saveImage("./".PROFILEPICTUREPATH.$nombreArchivo.".".$ext);
						}
						else
							$errorImagen = "Hubo un error cargando su im&aacute;gen de perfil";
					}
					else
						$errorImagen = "Tipo de archivo de im&aacute;gen de perfil no permitido. (Permitido: .jpg y .png)";
				}
				else
				{
					if($archivo['error'] != UPLOAD_ERR_NO_FILE)
						$errorImagen = "Tama&ntilde;o m&aacute;ximo de im&aacute;gen de perfil: 2MB.";
				}
			}

			if(empty($nombre) || empty($apellido))
			{
				$data['title'] = "Datos Incompletos";
				$data['titulo'] = "Datos Incompletos";
				$data['mensaje'] = "Debe especificar al menos su nombre y su apellido.";
				$data['boton'] = "Volver";
				$data['href'] = DIR."perfil/".$idPersona."/edit";	

				View::renderTemplate('header', $data);
				View::render('tracker/mensaje', $data);
				View::renderTemplate('footer', $data);
			}
			else
			{
				$valores = array();
				$valores['nombre'] = ucwords(strtolower($nombre));
				$valores['apellido'] = ucwords(strtolower($apellido));
				$valores['alias'] = ucwords(strtolower($alias));
				$valores['ident'] = strtolower($ident);
				$valores['correo'] = strtolower($correo);
				$valores['pais'] = $pais;
				$valores['sexo'] = $sexo;
				$valores['boxId'] = $boxId;
				$valores['estado'] = ucwords(strtolower($estado));
				$valores['pic'] = ucwords(strtolower($pic));

				if( $fechaValida )
					$fecha = $anno."-".$mes."-".$dia;
				else
					$fecha = null;

				$valores['fechaNacimiento'] = $fecha;

				$valores['wkclean'] = Models\Perfiles::pesoNormal($cleanV, $cleanT);
				$valores['wksnatch'] = Models\Perfiles::pesoNormal($snatchV, $snatchT);
				$valores['wkdead'] = Models\Perfiles::pesoNormal($deadV, $deadT);
				$valores['wkbacksquat'] = Models\Perfiles::pesoNormal($backV, $backT);

				$valores['wkfran'] = Models\Perfiles::tiempoValor($franH, $franM, $franS);
				$valores['wkisabel'] = Models\Perfiles::tiempoValor($isabelH, $isabelM, $isabelS);
				$valores['wkgrace'] = Models\Perfiles::tiempoValor($graceH, $graceM, $graceS);
				$valores['wkrun5k'] = Models\Perfiles::tiempoValor($run5kH, $run5kM, $run5kS);
				$valores['wkcindy'] = Models\Perfiles::tiempoValor($cindyH, $cindyM, $cindyS);

				$cuentas = new Models\Cuentas();
				$act = $cuentas->actualizarPersona($persona->idPersona, $valores);

				if(isset($errorImagen) && !empty($errorImagen))
				{
					$data['title'] = "Error cargando im&aacute;gen";
					$data['titulo'] = "Error cargando im&aacute;gen";
					$data['mensaje'] = $errorImagen;
					$data['boton'] = "Volver";
					$data['href'] = DIR."perfil/".$persona->idPersona."/edit";	

					View::renderTemplate('header', $data);
					View::render('tracker/mensaje', $data);
					View::renderTemplate('footer', $data);

					return;
				}

				Url::redirect("cuenta");
			}
		}
	}

	public function lost()
	{
		$data['title'] = "Recuperaci&oacute;n de Contrase&ntilde;a";
		$data['csrf_token'] = Csrf::makeToken();

		View::renderTemplate('header', $data);
		View::render('tracker/lost', $data);
		View::renderTemplate('footer', $data);
	}

	public function lostPOST()
	{
		if (!Csrf::isTokenValid())
		{
			self::sectionCheckInvalid();
			return;
		}

		if(isset($_POST['submit']))
		{
			$correo = trim($_POST['correo']);
			$ok = false;

			if(!empty($correo))
			{
				$cuentas = new Models\Cuentas();
				$registrado = $cuentas->correoRegistrado($correo);

				if($registrado)
				{
					$codigo = Codes::genCode();
					$cuentas->setResetCode($registrado->idPersona, $codigo);

					$gestor = fopen("app/templates/mail/mail.html", "r");
					$txt = "";

					while (!feof($gestor))
					{
						$line_of_text = fgets($gestor);
						$txt .= $line_of_text;
					}
					fclose($gestor);

					$bdy = "Ingrese al siguiente enlace para recuperar su cuenta: <a href=\"".DIR."lost/".$registrado->idPersona."/".$codigo."\">Cambiar Contraseña</a> ";
					$strMail = str_replace("[MENSAJE]", $bdy, $txt);
					$strMail = str_replace("[LOGO1]", DIR."app/templates/".TEMPLATE."/img/logoCuadrado.png", $strMail);

					$mail = new \Helpers\PhpMailer\Mail();
					$mail->CharSet = "UTF-8";
					$mail->IsHTML(true);
					$mail->setFrom(SITEEMAIL);
					$mail->addAddress($correo);
					$mail->subject('Pukiebook - Recuperación de Contraseña');
					$mail->body($strMail);
					$mail->send();

					$ok = true;
				}
			}

			$data['title'] = "Recuperaci&oacute;n de Contrase&ntilde;a";

			if($ok)
			{
				$data['titulo'] = "Solicitud Completada";
				$data['mensaje'] = "Espere en su bandeja de entrada un correo con el enlace para recuperar su contrase&ntilde;a.";
				$data['href'] = DIR;
				$data['boton'] = "Continuar";
			}
			else
			{
				$data['titulo'] = "Solicitud Fallida";
				$data['mensaje'] = "El correo indicado no se encuentra registrado en Pukiebook";
				$data['href'] = DIR."lost";
				$data['boton'] = "Intentar de nuevo";
			}

			View::renderTemplate('header', $data);
			View::render('tracker/mensaje', $data);
			View::renderTemplate('footer', $data);
		}
		else
		{
			Url::redirect("lost");
		}

	}

	public function lostCODE($personaId, $codigo)
	{
		$cuentas = new Models\Cuentas();
		$persona = $cuentas->getPersona($personaId);
		$ok = false;

		if($persona)
		{
			if($persona->resetCode == $codigo)
				$ok = true;
		}

		$data['title'] = "Recuperaci&oacute;n de Contrase&ntilde;a";
		$data['csrf_token'] = Csrf::makeToken();

		if($ok)
		{
			$data['personaId'] = $persona->idPersona;
			$data['resetCode'] = $codigo;

			View::renderTemplate('header', $data);
			View::render('tracker/cambioclave', $data);
			View::renderTemplate('footer', $data);
		}
		else
		{
			$data['titulo'] = "Solicitud No V&aacute;lida";
			$data['mensaje'] = "Esta solicitud de recuperaci&oacute;n ha expirado o no es v&aacute;lida. Intente de nuevo";
			$data['href'] = DIR."lost";
			$data['boton'] = "Recuperar Contrase&ntilde;a";
			
			View::renderTemplate('header', $data);
			View::render('tracker/mensaje', $data);
			View::renderTemplate('footer', $data);
		}

	}

	public function lostCODEPOST($personaId, $codigo)
	{
		if (!Csrf::isTokenValid())
		{
			self::sectionCheckInvalid();
			return;
		}

		if(isset($_POST['submit']))
		{
			$cuentas = new Models\Cuentas();
			$persona = $cuentas->getPersona($personaId);
			$ok = false;

			if($persona)
			{
				if($persona->resetCode == $codigo)
					$ok = true;
			}

			$data['title'] = "Recuperaci&oacute;n de Contrase&ntilde;a";
			if(!$ok)
			{
				$data['titulo'] = "Solicitud No V&aacute;lida";
				$data['mensaje'] = "Esta solicitud de recuperaci&oacute;n ha expirado o no es v&aacute;lida. Intente de nuevo";
				$data['href'] = DIR."lost";
				$data['boton'] = "Recuperar Contrase&ntilde;a";
				
				View::renderTemplate('header', $data);
				View::render('tracker/mensaje', $data);
				View::renderTemplate('footer', $data);
			}
			else
			{
				$contra = trim($_POST['contrasena']);
				$contraC = trim($_POST['contrasenaC']);

				if(!empty($contra) && ($contra == $contraC))
				{
					$dd = array('contrasena' => Password::make($contra), 'resetCode' => null);
					$cc = $cuentas->actualizarPersona($persona->idPersona, $dd);

					$data['titulo'] = "Su contrase&ntilde;a ha sido cambiada correctamente";
					$data['mensaje'] = "";
					$data['href'] = DIR."accederred";
					$data['boton'] = "Acceder";
				}
				else
				{
					$data['titulo'] = "Contrase&ntilde;as no v&aacute;lidas";
					$data['mensaje'] = "Las contrase&ntilde;as no coinciden o no ingres&oacute; ninguna.";
					$data['href'] = DIR."lost/".$persona->idPersona."/".$codigo;
					$data['boton'] = "Intentar de nuevo";
				}

				View::renderTemplate('header', $data);
				View::render('tracker/mensaje', $data);
				View::renderTemplate('footer', $data);
			}
		}
	}

	public function cambioContrasena()
	{
		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			self::sectionNeedLogin();
			return;
		}

		$data['title'] = "Cambio de Contrase&ntilde;a";
		$data['csrf_token'] = Csrf::makeToken();
		$data['logeado'] = $logeado;

		View::renderTemplate('header', $data);
		View::render('tracker/cambioclave', $data);
		View::renderTemplate('footer', $data);
	}

	public function cambioContrasenaPOST()
	{
		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			self::sectionNeedLogin();
			return;
		}

		if (!Csrf::isTokenValid())
		{
			self::sectionCheckInvalid();
			return;
		}

		if(isset($_POST['submit']))
		{
			$data['logeado'] = $logeado;
			$persona = $logeado['persona'];

			$contra = trim($_POST['contrasena']);
			$contraC = trim($_POST['contrasenaC']);

			$data['title'] = "Cambio de Contrase&ntilde;a";

			if(!empty($contra) && ($contra == $contraC))
			{
				$dd = array('contrasena' => Password::make($contra), 'resetCode' => null);
				$cuentas = new Models\Cuentas();
				$cc = $cuentas->actualizarPersona($persona->idPersona, $dd);

				$data['titulo'] = "Su contrase&ntilde;a ha sido cambiada correctamente";
				$data['mensaje'] = "";
				$data['href'] = DIR."cuenta";
				$data['boton'] = "Acceder";
			}
			else
			{
				$data['titulo'] = "Contrase&ntilde;as no v&aacute;lidas";
				$data['mensaje'] = "Las contrase&ntilde;as no coinciden o no ingres&oacute; ninguna.";
				$data['href'] = DIR."lost/".$persona->idPersona."/".$codigo;
				$data['boton'] = "Intentar de nuevo";
			}

			View::renderTemplate('header', $data);
			View::render('tracker/mensaje', $data);
			View::renderTemplate('footer', $data);
		}
	}

	public function eventosOrganizados()
	{
		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			self::sectionNeedLogin();
			return;
		}

		$perfiles = new Models\Perfiles();
		$eventos = new Models\Eventos();

		$persona = $logeado['persona'];
		$eventosOrganizados = $perfiles->getEventosOrganizados($persona->idPersona);
		$eventos->prepararEventos($eventosOrganizados);

		$data['title'] = "Eventos Organizados";
		$data['logeado'] = $logeado;
		$data['eventosOrganizados'] = $eventosOrganizados;

		View::renderTemplate('header', $data);
		View::render('tracker/cuenta/eventosOrg', $data);
		View::renderTemplate('footer', $data);
	}

	public function eventosJuez()
	{
		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			self::sectionNeedLogin();
			return;
		}

		$cuentas = new Models\Cuentas();
		$perfiles = new Models\Perfiles();
		$eventos = new Models\Eventos();

		$persona = $logeado['persona'];
		$eventosJuez = $perfiles->getEventosJuez($persona->idPersona);
		$eventos->prepararEventos($eventosJuez);

		$eventosJuezP = array();
		$eventosJuezS = array();

		foreach ($eventosJuez as $key => $value)
		{
			if($value->juezPrincipal == $persona->idPersona)
				$eventosJuezP[] = $value;

			if($value->juezSuplente == $persona->idPersona)
				$eventosJuezS[] = $value;
		}

		$data['title'] = "Eventos como Juez";
		$data['logeado'] = $logeado;
		$data['eventosJuezPrincipal'] = $eventosJuezP;
		$data['eventosJuezSuplente'] = $eventosJuezS;

		View::renderTemplate('header', $data);
		View::render('tracker/cuenta/eventosJuez', $data);
		View::renderTemplate('footer', $data);
	}

	public function registrarEnEvento()
	{
		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			self::sectionNeedLogin();
			return;
		}

		$data['title'] = "Registrarse en evento";
		$data['logeado'] = $logeado;
		
		$eventos = new Models\Eventos();
		$eventosActivos = $eventos->getEventosActivosRegistro();
		$eventos->prepararEventos($eventosActivos);

		$data['info'] = new \stdClass();
		$data['info']->eventosActivos = $eventosActivos;

		View::renderTemplate('header', $data);
		View::render('tracker/cuenta/registrarenevento', $data, $info);
		View::renderTemplate('footer', $data);
	}

	public function registrarEnEventoEquipo($evento, $logeado)
	{
		$equipos = new Models\Equipos();
		$teams = $equipos->getOrganizados($logeado['persona']->idPersona);

		foreach ($teams as $key => $value)
		{
			if($value->dedicado)
				unset($teams[$key]);
		}

		$equipos->prepararEquipos($teams);

		$data['title'] = "Registrarse en evento";
		$data['evento'] = $evento;
		$data['equipos'] = $teams;
		$data['logeado'] = $logeado;

		View::renderTemplate('header', $data);
		View::render('tracker/cuenta/registrareneventoequipo', $data, $info);
		View::renderTemplate('footer', $data);
	}

	public function registrarEnEventoEquipo2($eventoId, $equipoId)
	{
		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			self::sectionNeedLogin();
			return;
		}

		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);

		if(!$evento)
		{
			Eventos::eventoNotFound();
			return;
		}

		$eventos->prepararEvento($evento);

		if(!$evento->registroTeam)
		{
			Eventos::eventoNotFound();
			return;
		}

		if($evento->eventoFinalizado)
		{
			Eventos::eventoFinished();
			return;
		}
		
		if(!$evento->permiteRegistros)
		{
			Eventos::eventoNoNewAthletes();
			return;
		}

		$equipos = new Models\Equipos();
		$team = $equipos->get($equipoId);

		if(!$team)
		{
			Equipos::equipoNotFound();
			return;
		}
		
		$equipos->prepararEquipo($team);

		$registro = $eventos->getRegistroEquipo($evento->id, $team->id);
		$registrado = false;
		$pendiente = false;

		if($registro)
		{
			if($registro->aprobado)
				$registrado = $registro;
			else
				$pendiente = true;
		}

		if($registrado)
		{
			Eventos::eventoAlredyRegistered();
			return;
		}

		if($pendiente)
		{
			Eventos::eventoRegistrationPending();
			return;
		}

		$atletas = $equipos->getAtletas($team->id);
		$atlDuplicado = $eventos->getAtletaRegistradoEquipo($evento->id, $atletas);

		if($atlDuplicado)
		{
			Eventos::atletaEquipoAlredyRegistered();
			return;
		}

		$totAprobados = 0;
		$sameSex = 1;
		$sexTmp = 'N';

		foreach ($atletas as $key => $value)
		{
			if(!$value->aprobado)
				continue;

			$totAprobados++;

			if(strcmp($sexTmp, 'N') == 0)
				$sexTmp = $value->sexo;

			if(strcmp($value->sexo, $sexTmp) != 0)
				$sameSex = 0;

			$sexTmp = $value->sexo;
		}

		if($evento->eqCantIntegrantes != $totAprobados)
		{
			Equipos::noCumpleCantIntegrantes($evento->eqCantIntegrantes);
			return;
		}

		if($evento->eqMismoGenero )
		{
			if(strcmp($sexTmp, 'X') == 0)
			{
				Equipos::noEspecificaGeneroIntegrantes();
				return;
			}
			else
			{
				if(!$sameSex)
				{
					Equipos::noCumpleGeneroIntegrantes();
					return;
				}
			}
		}

		$categorias = $eventos->getCategorias($evento->id);

		$data['title'] = "Registrarse en evento";
		$data['categorias'] = $categorias;
		$data['evento'] = $evento;
		$data['equipo'] = $team;
		$data['logeado'] = $logeado;

		View::renderTemplate('header', $data);
		View::render('tracker/cuenta/registrareneventoequipo2', $data, $info);
		View::renderTemplate('footer', $data);
	}

	public function registrarEnEventoEquipo3($eventoId, $equipoId, $categoriaId)
	{
		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			self::sectionNeedLogin();
			return;
		}

		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);

		if(!$evento)
		{
			Eventos::eventoNotFound();
			return;
		}

		$eventos->prepararEvento($evento);

		if(!$evento->registroTeam)
		{
			Eventos::eventoNotFound();
			return;
		}

		if($evento->eventoFinalizado)
		{
			Eventos::eventoFinished();
			return;
		}
		
		if(!$evento->permiteRegistros)
		{
			Eventos::eventoNoNewAthletes();
			return;
		}

		$equipos = new Models\Equipos();
		$team = $equipos->get($equipoId);

		if(!$team)
		{
			Equipos::equipoNotFound();
			return;
		}
		$equipos->prepararEquipo($team);

		$registro = $eventos->getRegistroEquipo($evento->id, $team->id);
		$registrado = false;
		$pendiente = false;

		if($registro)
		{
			if($registro->aprobado)
				$registrado = $registro;
			else
				$pendiente = true;
		}

		if($registrado)
		{
			Eventos::eventoAlredyRegistered();
			return;
		}

		if($pendiente)
		{
			Eventos::eventoRegistrationPending();
			return;
		}

		$atletas = $equipos->getAtletas($team->id);
		$atlDuplicado = $eventos->getAtletaRegistradoEquipo($evento->id, $atletas);

		if($atlDuplicado)
		{
			Eventos::atletaEquipoAlredyRegistered();
			return;
		}

		$totAprobados = 0;
		$sameSex = 1;
		$sexTmp = 'N';

		foreach ($atletas as $key => $value)
		{
			if(!$value->aprobado)
				continue;

			$totAprobados++;

			if(strcmp($sexTmp, 'N') == 0)
				$sexTmp = $value->sexo;

			if(strcmp($value->sexo, $sexTmp) != 0)
				$sameSex = 0;

			$sexTmp = $value->sexo;
		}

		if($evento->eqCantIntegrantes != $totAprobados)
		{
			Equipos::noCumpleCantIntegrantes($evento->eqCantIntegrantes);
			return;
		}

		if($evento->eqMismoGenero )
		{
			if(strcmp($sexTmp, 'X') == 0)
			{
				Equipos::noEspecificaGeneroIntegrantes();
				return;
			}
			else
			{
				if(!$sameSex)
				{
					Equipos::noCumpleGeneroIntegrantes();
					return;
				}
			}
		}

		$categoria = $eventos->getCategoria($evento->id, $categoriaId);

		if(!$categoria)
		{
			Eventos::categoriaNotFound($evento);
			return;
		}

		if($evento->reqPago)
		{
			Url::redirect("cuenta/registrarseevento/".$evento->internalURL."/".$team->id."/e/".$categoriaId."/registrarPago");
		}
		else
		{
			Url::redirect("cuenta/registrarseevento/".$evento->internalURL."/".$team->id."/e/".$categoriaId."/registrarPago/3");
		}
	}

	public function registrarEnEventoID($eventoId)
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

		if($evento->eventoFinalizado)
		{
			Eventos::eventoFinished();
			return;
		}
		
		if(!$evento->permiteRegistros)
		{
			Eventos::eventoNoNewAthletes();
			return;
		}

		$data['title'] = "Registrarse en evento";
		$data['logeado'] = $logeado;
		$persona = $logeado['persona'];

		if($evento->registroTeam)
		{
			self::registrarEnEventoEquipo($evento, $logeado);
			return;
		}

		$registro = $eventos->getRegistro($evento->id, $persona->idPersona);
		$registrado = false;
		$pendiente = false;

		if($registro)
		{
			if($registro->aprobado)
				$registrado = $registro;
			else
				$pendiente = true;
		}

		if($registrado)
		{
			Eventos::eventoAlredyRegistered();
			return;
		}

		if($pendiente)
		{
			Eventos::eventoRegistrationPending();
			return;
		}

		if(!$registrado && !$pendiente)
		{
			$categorias = $eventos->getCategorias($evento->id);

			$data['evento'] = $evento;
			$data['categorias'] = $categorias;
			$data['logeado'] = $logeado;

			View::renderTemplate('header', $data);
			View::render('tracker/cuenta/registrarenevento2', $data, $info);
			View::renderTemplate('footer', $data);
		}
	}

	public function registrarEnEventoIDCAT($eventoId, $categoriaId = 0) 
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

		if($evento->eventoFinalizado)
		{
			Eventos::eventoFinished();
			return;
		}
		if(!$evento->permiteRegistros)
		{
			Eventos::eventoNoNewAthletes();
			return;
		}

		$categoria = $eventos->getCategoria($evento->id, $categoriaId);

		if(!$categoria)
		{
			Eventos::categoriaNotFound($evento);
			return;
		}

		$data['logeado'] = $logeado;
		$persona = $logeado['persona'];
		$cuentas = new Models\Cuentas();

		$organizador = $cuentas->getPersona($evento->creadorId);
		$registro = $eventos->getRegistro($evento->id, $persona->idPersona);
		$registrado = false;
		$pendiente = false;

		if($registro)
		{
			if($registro->aprobado)
				$registrado = $registro;
			else
				$pendiente = true;
		}
		
		if($registrado)
		{
			Eventos::eventoAlredyRegistered();
			return;
		}

		if($pendiente)
		{
			Eventos::eventoRegistrationPending();
			return;
		}

		if(!$registrado && !$pendiente)
		{
			if($evento->reqPago)
			{
				Url::redirect("cuenta/registrarseevento/".$evento->internalURL."/".$categoriaId."/registrarPago");
			}
			else
			{
				Url::redirect("cuenta/registrarseevento/".$evento->internalURL."/".$categoriaId."/registrarPago/3");
			}
		}
	}


	public function registrarEnEventoEquipoIDCATPAGOTIPO($eventoId, $equipoId, $categoriaId = 0)
	{
		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			self::sectionNeedLogin();
			return;
		}

		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);

		if(!$evento)
		{
			Eventos::eventoNotFound();
			return;
		}

		$eventos->prepararEvento($evento);

		if(!$evento->registroTeam)
		{
			Eventos::eventoNotFound();
			return;
		}

		if($evento->eventoFinalizado)
		{
			Eventos::eventoFinished();
			return;
		}
		
		if(!$evento->permiteRegistros)
		{
			Eventos::eventoNoNewAthletes();
			return;
		}

		$equipos = new Models\Equipos();
		$team = $equipos->get($equipoId);

		if(!$team)
		{
			Equipos::equipoNotFound();
			return;
		}
		$equipos->prepararEquipo($team);

		$registro = $eventos->getRegistroEquipo($evento->id, $team->id);
		$registrado = false;
		$pendiente = false;

		if($registro)
		{
			if($registro->aprobado)
				$registrado = $registro;
			else
				$pendiente = true;
		}

		if($registrado)
		{
			Eventos::eventoAlredyRegistered();
			return;
		}

		if($pendiente)
		{
			Eventos::eventoRegistrationPending();
			return;
		}

		$atletas = $equipos->getAtletas($team->id);
		$atlDuplicado = $eventos->getAtletaRegistradoEquipo($evento->id, $atletas);

		if($atlDuplicado)
		{
			Eventos::atletaEquipoAlredyRegistered();
			return;
		}

		$hom = 0;
		$muj = 0;
		$tot = 0;

		foreach ($atletas as $key => $value)
		{

			if(!$value->aprobado)
				continue;

			if($value->sexo == 'M')
				$hom++;
			if($value->sexo == 'F')
				$muj++;

			$tot++;
		}

		if($evento->id == 68)
		{
			if($tot != 4 /*|| $hom != 2 || $muj != 2*/)
			{
				//Equipos::noCumpleCantIntegrantes();
				//return;
			}
		}

		$categoria = $eventos->getCategoria($evento->id, $categoriaId);

		if(!$categoria)
		{
			Eventos::categoriaNotFound($evento);
			return;
		}

		$data['title'] = "Registro de Pago";
		$data['logeado'] = $logeado;
		$data['evento'] = $evento;
		$data['equipo'] = $team;
		$data['categoria'] = $categoria;

		View::renderTemplate('header', $data);
		View::render('tracker/cuenta/registrareneventoequipopagotipo', $data, $info);
		View::renderTemplate('footer', $data);
	}

	public function registrarEnEventoEquipoIDCATPAGO($eventoId, $equipoId, $categoriaId = 0, $tipoPago = 0, $info = array())
	{
		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			self::sectionNeedLogin();
			return;
		}

		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);

		if(!$evento)
		{
			Eventos::eventoNotFound();
			return;
		}

		$eventos->prepararEvento($evento);

		if(!$evento->registroTeam)
		{
			Eventos::eventoNotFound();
			return;
		}

		if($evento->eventoFinalizado)
		{
			Eventos::eventoFinished();
			return;
		}
		
		if(!$evento->permiteRegistros)
		{
			Eventos::eventoNoNewAthletes();
			return;
		}

		$equipos = new Models\Equipos();
		$team = $equipos->get($equipoId);

		if(!$team)
		{
			Equipos::equipoNotFound();
			return;
		}
		$equipos->prepararEquipo($team);

		$registro = $eventos->getRegistroEquipo($evento->id, $team->id);
		$registrado = false;
		$pendiente = false;

		if($registro)
		{
			if($registro->aprobado)
				$registrado = $registro;
			else
				$pendiente = true;
		}

		if($registrado)
		{
			Eventos::eventoAlredyRegistered();
			return;
		}

		if($pendiente)
		{
			Eventos::eventoRegistrationPending();
			return;
		}

		$atletas = $equipos->getAtletas($team->id);
		$atlDuplicado = $eventos->getAtletaRegistradoEquipo($evento->id, $atletas);

		if($atlDuplicado)
		{
			Eventos::atletaEquipoAlredyRegistered();
			return;
		}

		$hom = 0;
		$muj = 0;
		$tot = 0;

		foreach ($atletas as $key => $value)
		{

			if(!$value->aprobado)
				continue;

			if($value->sexo == 'M')
				$hom++;
			if($value->sexo == 'F')
				$muj++;

			$tot++;
		}

		if($evento->id == 68)
		{
			if($tot != 4 /*|| $hom != 2 || $muj != 2*/)
			{
				//Equipos::noCumpleCantIntegrantes();
				//return;
			}
		}

		$categoria = $eventos->getCategoria($evento->id, $categoriaId);

		if(!$categoria)
		{
			Eventos::categoriaNotFound($evento);
			return;
		}

		$data['title'] = "Registro de Pago";
		$data['logeado'] = $logeado;
		$data['evento'] = $evento;
		$data['equipo'] = $team;
		$data['csrf_token'] = Csrf::makeToken();

		if(empty($info))
		{
			$info['mensajes'] = array();
			$info['campos'] = array();
		}

		View::renderTemplate('header', $data);
		if($tipoPago == 1)
			View::render('tracker/cuenta/registrareneventoequipopagotipo1', $data, $info);
		if($tipoPago == 2)
			View::render('tracker/cuenta/registrareneventoequipopagotipo2', $data, $info);
		if($tipoPago == 3)
			View::render('tracker/cuenta/registrareneventoequipopagotipo3', $data, $info);
		View::renderTemplate('footer', $data);
	}

	public function registrarEnEventoEquipoIDCATPAGOPOST($eventoId, $equipoId, $categoriaId = 0, $tipoPago = 0)
	{
		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			self::sectionNeedLogin();
			return;
		}

		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);

		if(!$evento)
		{
			Eventos::eventoNotFound();
			return;
		}

		$eventos->prepararEvento($evento);

		if(!$evento->registroTeam)
		{
			Eventos::eventoNotFound();
			return;
		}

		if($evento->eventoFinalizado)
		{
			Eventos::eventoFinished();
			return;
		}
		
		if(!$evento->permiteRegistros)
		{
			Eventos::eventoNoNewAthletes();
			return;
		}

		$equipos = new Models\Equipos();
		$team = $equipos->get($equipoId);

		if(!$team)
		{
			Equipos::equipoNotFound();
			return;
		}
		$equipos->prepararEquipo($team);

		$registro = $eventos->getRegistroEquipo($evento->id, $team->id);
		$registrado = false;
		$pendiente = false;

		if($registro)
		{
			if($registro->aprobado)
				$registrado = $registro;
			else
				$pendiente = true;
		}

		if($registrado)
		{
			Eventos::eventoAlredyRegistered();
			return;
		}

		if($pendiente)
		{
			Eventos::eventoRegistrationPending();
			return;
		}

		$atletas = $equipos->getAtletas($team->id);
		$atlDuplicado = $eventos->getAtletaRegistradoEquipo($evento->id, $atletas);

		if($atlDuplicado)
		{
			Eventos::atletaEquipoAlredyRegistered();
			return;
		}

		$hom = 0;
		$muj = 0;
		$tot = 0;

		foreach ($atletas as $key => $value)
		{

			if(!$value->aprobado)
				continue;

			if($value->sexo == 'M')
				$hom++;
			if($value->sexo == 'F')
				$muj++;

			$tot++;
		}

		if($evento->id == 68)
		{
			if($tot != 4 /*|| $hom != 2 || $muj != 2*/)
			{
				//Equipos::noCumpleCantIntegrantes();
				//return;
			}
		}

		$categoria = $eventos->getCategoria($evento->id, $categoriaId);

		if(!$categoria)
		{
			Eventos::categoriaNotFound($evento);
			return;
		}

		$data['title'] = "Registro de Pago";
		$data['logeado'] = $logeado;
		$data['evento'] = $evento;
		$persona = $logeado['persona'];

		if(isset($_POST['submit']))
		{
			$valores 	= array();
			$validator 	= array();

			$observacion 			= filter_input(INPUT_POST, 'observacion');
			$valores['observacion'] = $observacion;

			if($tipoPago != 3)
			{
				$numreferencia 	= filter_input(INPUT_POST, 'numreferencia');
				$monto 			= filter_input(INPUT_POST, 'monto');
				$fechaD 		= filter_input(INPUT_POST, 'fechaD');
				$fechaM 		= filter_input(INPUT_POST, 'fechaM');
				$fechaA 		= filter_input(INPUT_POST, 'fechaA');

				$valores['numreferencia'] 	= $numreferencia;
				$valores['monto'] 			= $monto;
				$valores['fechaD'] 			= $fechaD;
				$valores['fechaM'] 			= $fechaM;
				$valores['fechaA'] 			= $fechaA;

				$validator['monto'] 	= 'required';
				$validator['fechaD'] 	= 'required|numeric|min_numeric,1';
				$validator['fechaM'] 	= 'required|numeric|min_numeric,1';
				$validator['fechaA'] 	= 'required|numeric|min_numeric,1';
			}
			else
			{
				$fechaAc 	= time();
				$fechaD 	= date('d', $fechaAc);
				$fechaM 	= date('m', $fechaAc);
				$fechaA 	= date('Y', $fechaAc);
			}

			if($tipoPago == 1)
			{
				$banco = filter_input(INPUT_POST, 'banco');
				
				$valores['banco'] = $banco;
				$valores['numcuenta'] = $numcuenta;
				$info['data'] = $valores;

				$gumpValidator = new Gump();
				$validator['banco'] = 'required';
				$validator['numreferencia'] = 'required';
				

				$is_valid = $gumpValidator->validate($_POST, $validator);

				if($is_valid !== true)
				{
					$info['campos'] = $gumpValidator->errors();
				}
				else
				{
					$fechaValida = checkdate($fechaM, $fechaD, $fechaA);

					if(!$fechaValida)
						$info['campos']['fecha'] = true;
				}
			}
			else if($tipoPago == 2)
			{
				$info['data'] = $valores;
				$gumpValidator = new Gump();
				$is_valid = $gumpValidator->validate($_POST, $validator);

				if($is_valid !== true)
				{
					$info['campos'] = $gumpValidator->errors();
				}
				else
				{
					$fechaValida = checkdate($fechaM, $fechaD, $fechaA);

					if(!$fechaValida)
						$info['campos']['fecha'] = true;
				}
			}

			if(!empty($info['campos']) || !empty($info['mensajes']))
			{
				self::registrarEnEventoEquipoIDCATPAGO($evento->id, $team->id, $categoriaId, $tipoPago, $info);
				return;
			}
			else
			{
				$eventos = new Models\Eventos();
				$cuentas = new Models\Cuentas();

				$pendienteId = $eventos->crearEquipoPendiente($evento->id, $categoriaId, $equipoId, $atletas);

				if($pendienteId > 0)
				{
					$send 					= array();
					$send['formapago'] 		= $tipoPago;
					$send['numreferencia'] 	= $numreferencia;
					$send['monto'] 			= $monto;
					$send['fecha'] 			= $fechaA."-".$fechaM."-".$fechaD;
					$send['banco'] 			= $banco;						
					$send['observacion'] 	= $observacion;

					$registroPago = $eventos->registrarPago($send);
					$eventos->asignarPagoEquipo($pendienteId, $registroPago);

					if($registroPago > 0)
					{
						$data['title'] = "Registro completado";
						$data['titulo'] = "Registro completado";
						$data['mensaje'] = "Tu registro en el evento ha sido completado y debe ser aprobado por el organizador del evento.";
						$data['boton'] = "Ir a mi cuenta";
						$data['href'] = DIR."cuenta";
						
						$organizador = $cuentas->getPersona($evento->creadorId);
						$organizadorEq = $cuentas->getPersona($team->capitanId);
						$categoria = $eventos->getCategoria($evento->id, $categoriaId);

						$gestor = fopen("app/templates/mail/mail.html", "r");
						$txt = "";

						while (!feof($gestor))
						{
							$line_of_text = fgets($gestor);
							$txt .= $line_of_text;
						}
						fclose($gestor);

						//banco
						$cadena = "Saludos ".$organizador->nombre." ".$organizador->apellido.", <br/><br/>Le informamos que tiene una solicitud de registro en el evento \"".$evento->nombre."\".<br/><br/>Informacion del equipo: <br/><b>Categoria: </b>".$categoria->nombre."<br/><b>Equipo:</b> ".$team->nombre."<br/><b>Correo:</b> <a href='mailto:".$organizadorEq->correo."'>".$organizadorEq->correo."</a><br/><b>Telefono:</b>".$organizadorEq->telefono_1."<br/><br/>";

						if($tipoPago == 1)
							$cadena .= "Datos de la transaccion:<br/><b>Banco: </b>".$banco."<br/><b>Num. Referencia: </b>".$numreferencia."<br/><b>Monto:</b> ".$monto."<br/><b>Fecha:</b> ".$send['fecha']."<br/>";
						else if($tipoPago == 2)
							$cadena .= "Datos del pago:<br/><b>Num. Recibo: </b>".$numreferencia."<br/><b>Monto:</b> ".$monto."<br/><b>Fecha:</b> ".$send['fecha']."<br/>";

						$cadena .= "<b>Observaciones</b>:" .$observacion."<br/>";
						$cadena .= "<br/><br/>Para gestionar esta solicitud vaya al menu de Administracion en el evento y seleccione 'Aprobar Participantes' o <a href='".DIR."cuenta/modevento/".$evento->internalURL."/participantesaprobar'>Haga click aqui</a>";

						$bdy = $cadena;
						$strMail = str_replace("[MENSAJE]", $bdy, $txt);
						$strMail = str_replace("[LOGO1]", DIR."app/templates/".TEMPLATE."/img/logoCuadrado.png", $strMail);

						$mail = new \Helpers\PhpMailer\Mail();
						$mail->setFrom(SITEEMAIL);
						$mail->addAddress($organizador->correo);
						$mail->subject('Registro en evento: '.$evento->nombre);
						$mail->body($strMail);
						$mail->send();
						
						View::renderTemplate('header', $data);
						View::render('tracker/mensaje', $data, $info);
						View::renderTemplate('footer', $data);
					}
				}
			}
		}
		
	}

	public function registrarEnEventoIDCATPAGOTIPO($eventoId, $categoriaId = 0)
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

		if($evento->eventoFinalizado)
		{
			Eventos::eventoFinished();
			return;
		}

		if(!$evento->permiteRegistros)
		{
			Eventos::eventoNoNewAthletes();
			return;
		}

		$categoria = $eventos->getCategoria($evento->id, $categoriaId);

		if(!$categoria)
		{
			Eventos::categoriaNotFound($evento);
			return;
		}

		$data['title'] = "Registro de PagASDSADo";
		$data['logeado'] = $logeado;
		$data['evento'] = $evento;
		$data['categoria'] = $categoria;
		$persona = $logeado['persona'];

		$registro = $eventos->getRegistro($evento->id, $persona->idPersona);
		$registrado = false;
		$pendiente = false;

		if($registro)
		{
			if($registro->aprobado)
				$registrado = $registro;
			else
				$pendiente = true;
		}
		
		if($registrado)
		{
			Eventos::eventoAlredyRegistered();
			return;
		}

		if($pendiente)
		{
			Eventos::eventoRegistrationPending();
			return;
		}

		if(!$registrado && !$pendiente)
		{
			View::renderTemplate('header', $data);
			View::render('tracker/cuenta/registrareneventopagotipo', $data, $info);
			View::renderTemplate('footer', $data);
		}
	}

	public function registrarEnEventoIDCATPAGO($eventoId, $categoriaId = 0, $tipoPago = 0, $info = array())
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

		if($evento->eventoFinalizado)
		{
			Eventos::eventoFinished();
			return;
		}

		if(!$evento->permiteRegistros)
		{
			Eventos::eventoNoNewAthletes();
			return;
		}

		$categoria = $eventos->getCategoria($evento->id, $categoriaId);

		if(!$categoria)
		{
			Eventos::categoriaNotFound($evento);
			return;
		}

		$data['title'] = "Registro de Pago";
		$data['logeado'] = $logeado;
		$data['evento'] = $evento;
		$data['csrf_token'] = Csrf::makeToken();
		$persona = $logeado['persona'];

		$registro = $eventos->getRegistro($evento->id, $persona->idPersona);
		$registrado = false;
		$pendiente = false;

		if($registro)
		{
			if($registro->aprobado)
				$registrado = $registro;
			else
				$pendiente = true;
		}

		if($registrado)
		{
			Eventos::eventoAlredyRegistered();
			return;
		}

		if($pendiente)
		{
			Eventos::eventoRegistrationPending();
			return;
		}

		if(!$registrado && !$pendiente)
		{
			if($persona->ident == null)
			{
				self::reqIdent($persona);
				return;
			}

			if(empty($info))
			{
				$info['mensajes'] = array();
				$info['campos'] = array();
			}

			View::renderTemplate('header', $data);
			if($tipoPago == 1)
				View::render('tracker/cuenta/registrareneventopagotipo1', $data, $info);
			if($tipoPago == 2)
				View::render('tracker/cuenta/registrareneventopagotipo2', $data, $info);
			if($tipoPago == 3)
				View::render('tracker/cuenta/registrareneventopagotipo3', $data, $info);
			if($tipoPago == 4)
				View::render('tracker/cuenta/registrareneventopagotipo4', $data, $info);
			View::renderTemplate('footer', $data);
		}
	}

	public function registrarEnEventoIDCATPAGOPOST($eventoId, $categoriaId = 0, $tipoPago = 0)
	{
		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			self::sectionNeedLogin();
			return;
		}

		if (!Csrf::isTokenValid())
		{
			self::sectionCheckInvalid();
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

		if($evento->eventoFinalizado)
		{
			Eventos::eventoFinished();
			return;
		}

		if(!$evento->permiteRegistros)
		{
			Eventos::eventoNoNewAthletes();
			return;
		}

		$categoria = $eventos->getCategoria($evento->id, $categoriaId);

		if(!$categoria)
		{
			Eventos::categoriaNotFound($evento);
			return;
		}


		$data['title'] = "Registro de Pago";
		$data['logeado'] = $logeado;
		$data['evento'] = $evento;
		$persona = $logeado['persona'];

		$eventos = new Models\Eventos();
		$registro = $eventos->getRegistro($evento->id, $persona->idPersona);
		$registrado = false;
		$pendiente = false;

		if($registro)
		{
			if($registro->aprobado)
				$registrado = $registro;
			else
				$pendiente = true;
		}

		if($registrado)
		{
			Eventos::eventoAlredyRegistered();
			return;
		}

		if($pendiente)
		{
			Eventos::eventoRegistrationPending();
			return;
		}

		if(!$registrado && !$pendiente)
		{
			if(isset($_POST['submit']))
			{
				$valores 	= array();
				$validator 	= array();

				$observacion 		= filter_input(INPUT_POST, 'observacion');			
				$edad 				= filter_input(INPUT_POST, 'edad');
				$centro_entrenamiento = filter_input(INPUT_POST, 'centro_entrenamiento');
				$telefono 			= filter_input(INPUT_POST, 'telefono');
				$address 			= filter_input(INPUT_POST, 'address');				
					
				
				
				$valores['edad'] = $edad;
				$valores['observacion'] = $observacion;
				$valores['centro_entrenamiento'] = $centro_entrenamiento;
				$valores['address'] = $address;
				$valores['telefono'] = $telefono;				

				$info['data'] = $valores;

				$validator['edad'] = 'required';
				$validator['centro_entrenamiento'] = 'required';				
				$validator['observacion'] = 'required';				

				$gumpValidator = new Gump();
					$is_valid = $gumpValidator->validate($_POST, $validator);

					if($is_valid !== true)
					{
						$info['campos'] = $gumpValidator->errors();
					}
					
				

				if($tipoPago != 3)
				{
					$numreferencia 	= filter_input(INPUT_POST, 'numreferencia');
					$monto 			= filter_input(INPUT_POST, 'monto');
					$fechaD 		= filter_input(INPUT_POST, 'fechaD');
					$fechaM 		= filter_input(INPUT_POST, 'fechaM');
					$fechaA 		= filter_input(INPUT_POST, 'fechaA');					
					

					$valores['numreferencia'] 	= $numreferencia;
					$valores['monto'] 			= $monto;
					$valores['fechaD'] 			= $fechaD;
					$valores['fechaM'] 			= $fechaM;
					$valores['fechaA'] 			= $fechaA;

					$validator['monto'] 	= 'required';
					$validator['fechaD'] 	= 'required|numeric|min_numeric,1';
					$validator['fechaM'] 	= 'required|numeric|min_numeric,1';
					$validator['fechaA'] 	= 'required|numeric|min_numeric,1';
					
				}
				if($tipoPago == 3)
				{
					$fechaAc 	= time();
					$fechaD 	= date('d', $fechaAc);
					$fechaM 	= date('m', $fechaAc);
					$fechaA 	= date('Y', $fechaAc);
					$telefono 			= filter_input(INPUT_POST, 'telefono');
					$address 			= filter_input(INPUT_POST, 'address');
					$valores['address'] = $address;
					$valores['telefono'] = $telefono;
					$info['data'] = $valores;
					
					$gumpValidator = new Gump();			
					$validator['address'] = 'required';
					$validator['telefono'] = 'required';


				}

				if($tipoPago == 1)
				{
					
					$banco = filter_input(INPUT_POST, 'banco');	
					$telefono 			= filter_input(INPUT_POST, 'telefono');
					$address 			= filter_input(INPUT_POST, 'address');				
					
					$valores['banco'] = $banco;
					$valores['numcuenta'] = $numcuenta;
					$info['data'] = $valores;

					$gumpValidator = new Gump();
					$validator['banco'] = 'required';
					$validator['numreferencia'] = 'required';
					$validator['address'] = 'required';
					$validator['telefono'] = 'required';
					


					$is_valid = $gumpValidator->validate($_POST, $validator);

					if($is_valid !== true)
					{
						$info['campos'] = $gumpValidator->errors();
					}
					else
					{
						$fechaValida = checkdate($fechaM, $fechaD, $fechaA);

						if(!$fechaValida)
							$info['campos']['fecha'] = true;
					}
				}
				else if($tipoPago == 2)
				{
				$observacion 		= filter_input(INPUT_POST, 'observacion');			
				$edad 				= filter_input(INPUT_POST, 'edad');
				$centro_entrenamiento = filter_input(INPUT_POST, 'centro_entrenamiento');			
				$numreferencia			= filter_input(INPUT_POST, 'numreferencia');
				$monto			= filter_input(INPUT_POST, 'monto');
				
				
				$valores['edad'] = $edad;
				$valores['observacion'] = $observacion;
				$valores['centro_entrenamiento'] = $centro_entrenamiento;				
				$valores['numreferencia'] = $numreferencia;	
				$valores['monto'] = $monto;					

				$validator['monto'] 	= 'required';
				$validator['edad'] = 'required';
				$validator['centro_entrenamiento'] = 'required';				
				$validator['numreferencia'] = 'required';
				

					$info['data'] = $valores;
					$gumpValidator = new Gump();
					$is_valid = $gumpValidator->validate($_POST, $validator);

					if($is_valid !== true)
					{
						$info['campos'] = $gumpValidator->errors();
					}
					else
					{
						$fechaValida = checkdate($fechaM, $fechaD, $fechaA);

						if(!$fechaValida)
							$info['campos']['fecha'] = true;
					}
				}

				if(!empty($info['campos']) || !empty($info['mensajes']))
				{
					self::registrarEnEventoIDCATPAGO($evento->id, $categoriaId, $tipoPago, $info);
					return;
				}
				else
				{
					$eventos = new Models\Eventos();
					$cuentas = new Models\Cuentas();

					$pendienteId = $eventos->crearAtletaPendiente($evento->id, $categoriaId, $persona->idPersona);

				

				if($pendienteId > 0)
				{
					$send 					= array();
					$send['formapago'] 		= $tipoPago;
					$send['numreferencia'] 	= $numreferencia;
					$send['monto'] 			= $monto;
					$send['fecha'] 			= $fechaA."-".$fechaM."-".$fechaD;
					$send['banco'] 			= $banco;						
					$send['observacion'] 	= $observacion;
					$send['address'] 		= $address;
					$send['telefono'] 		= $telefono;
					$send['edad'] 			= $edad;	
					$send['centro_entrenamiento'] 			= $centro_entrenamiento;										
						

						$registroPago = $eventos->registrarPago($send);
						$eventos->asignarPago($pendienteId, $registroPago);

						if($registroPago > 0)
						{
							$data['title'] = "Solicitud creada";
							$data['titulo'] = "Solicitud creada";
							$data['mensaje'] = "Tu registro en el evento ha sido completado y debe ser aprobado por el organizador del evento.";
							$data['boton'] = "Ir a mi cuenta";
							$data['href'] = DIR."cuenta";
							
							$organizador = $cuentas->getPersona($evento->creadorId);
							$categoria = $eventos->getCategoria($evento->id, $categoriaId);

							$gestor = fopen("app/templates/mail/mail.html", "r");
							$txt = "";

							while (!feof($gestor))
							{
								$line_of_text = fgets($gestor);
								$txt .= $line_of_text;
							}
							fclose($gestor);

							//banco
							$cadena = "Saludos ".$organizador->nombre." ".$organizador->apellido.", <br/><br/>Le informamos que tiene una solicitud de registro en el evento \"".$evento->nombre."\".<br/><br/>Informacion del atleta: <br/><b>Categoria: </b>".$categoria->nombre."<br/><b>Atleta:</b> ".$persona->nombre." ".$persona->apellido."<br/><b>Correo:</b> <a href='mailto:".$persona->correo."'>".$persona->correo."</a><br/><b>Telefono:</b>".$persona->telefono_1."<br/><br/>";

							if($tipoPago == 1)
							$cadena .= "Datos de la transaccion:<br/><b>Banco: </b>".$banco."<br/><b>Num. Referencia: </b>".$numreferencia."<br/><b>Monto:</b> ".$monto."<br/><b>Fecha:</b> ".$send['fecha']."<br/>";
							else if($tipoPago == 2)
							$cadena .= "Datos del pago:<br/><b>Num. Recibo: </b>".$numreferencia."<br/><b>Monto:</b> ".$monto."<br/><b>Fecha:</b> ".$send['fecha']."<br/>";

							$cadena .= "<b>Observaciones</b>:" .$observacion."<br/>";
							$cadena .= "<br/><br/>Para gestionar esta solicitud vaya al menu de Administracion en el evento y seleccione 'Aprobar Participantes' o <a href='".DIR."cuenta/modevento/".$evento->internalURL."/participantesaprobar'>Haga click aqui</a>";

							$bdy = $cadena;
							$strMail = str_replace("[MENSAJE]", $bdy, $txt);
							$strMail = str_replace("[LOGO1]", DIR."app/templates/".TEMPLATE."/img/logoCuadrado.png", $strMail);

							$mail = new \Helpers\PhpMailer\Mail();
							$mail->setFrom(SITEEMAIL);
							$mail->addAddress($organizador->correo);
							$mail->subject('Registro en evento: '.$evento->nombre);
							$mail->body($strMail);
							$mail->send();

							View::renderTemplate('header', $data);
							View::render('tracker/mensaje', $data, $info);
							View::renderTemplate('footer', $data);
						}
					}
				}
			}
		}
	}

	public function preCrearBox()
	{
		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			self::sectionNeedLogin();
			return;
		}

		$data['title'] = "Registro de Box";

		View::renderTemplate('header', $data);
		View::render('tracker/box/preCrear', $data, $info);
		View::renderTemplate('footer', $data);
	}

	
	public function crearBox($info = array())
	{
		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			self::sectionNeedLogin();
			return;
		}

		$web = new Models\Web();
		$paises = $web->getPaises();

		$data['title'] = "Crear Box";
		$data['csrf_token'] = Csrf::makeToken();
		$data['paises'] = $paises;

		if(empty($info))
		{
			$info['mensajes'] = array();
			$info['campos'] = array();
			$info['data'] = array();
			$info['data']['urlEvento'] = "http://";
		}

		View::renderTemplate('header', $data);
		View::render('tracker/box/crear', $data, $info);
		View::renderTemplate('footer', $data);
	}


	public function crearBoxPOST()
	{
		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			self::sectionNeedLogin();
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
			$urlBox 		= filter_input(INPUT_POST, 'urlBox');
			$coach 			= filter_input(INPUT_POST, 'coach');
			$pais 			= filter_input(INPUT_POST, 'pais');

			if($urlBox == "http://")
			{
				$_POST['urlBox'] = "";
				$urlBox = "";
			}

			$valores 				= array();
			$valores['nombre'] 		= $nombre;
			$valores['urlBox'] 		= $urlBox;
			$valores['coach'] 		= $coach;

			$info['data'] = $valores;

			$validator 				= array();
			$validator['nombre'] 	= 'required';
			$validator['urlBox'] 	= 'valid_url';

			$gumpValidator = new Gump();
			$is_valid = $gumpValidator->validate($_POST, $validator);

			if($is_valid !== true)
				$info['campos'] = $gumpValidator->errors();

			if(!empty($info['campos']) || !empty($info['mensajes']))
			{
				self::crearBox($info);
				return;
			}
			else
			{
				$evv = new Models\Eventos();
				$plan = $persona->subscripcionPlan;

				$new['creadorId'] = $persona->idPersona;
				$new['nombre'] = $nombre;
				$new['urlBox'] = $urlBox;
				$new['coach'] = $coach;
				$new['pais'] = $pais;
				$new['fechaRegistro'] = time();
				$evID = $evv->agregarBox($new);
	
				$cuentas = new Models\Cuentas();
				$organizador = $cuentas->getPersona($persona->idPersona);

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

					$bdy = "Se registró un box nuevo en la web: \"".$nombre."\". Creado por: \"".$organizador->nombre." ".$organizador->apellido."\" (".$organizador->correo.")<br/><br/>Información del Box: ".$nombre."<br/><b>Web del Box: </b> <a href='".$urlBox."'>".$urlBox."</a><br/><br/>Para mayor informacion: <br/><b>Organizador:</b> ".$organizador->nombre." ".$organizador->apellido."<br/><b>Correo:</b> <a href='mailto:".$organizador->correo."'>".$organizador->correo."</a><br/><b>Teléfono:</b>".$organizador->telefono_1."<br/><br/><a href=\"".DIR."cuenta/modbox/".$evID."\">Ver box</a>";
					$strMail = str_replace("[MENSAJE]", $bdy, $txt);
					$strMail = str_replace("[LOGO1]", DIR."app/templates/".TEMPLATE."/img/logoCuadrado.png", $strMail);

					$mail = new \Helpers\PhpMailer\Mail();
					$mail->CharSet = "UTF-8";
					$mail->IsHTML(true);
					$mail->setFrom(SITEEMAIL);
					$mail->addAddress(ADMINNOTIF);
					$mail->subject('Box nuevo en Pukiebook: '.$nombre);
					$mail->body($strMail);
					$mail->send();
				}


				Url::redirect('cuenta/modbox/'.$evID);
			}
		}
	}

	public function preCrearEquipo()
	{
		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			self::sectionNeedLogin();
			return;
		}

		$data['title'] = "Registro de Equipo";

		View::renderTemplate('header', $data);
		View::render('tracker/equipo/preCrear', $data, $info);
		View::renderTemplate('footer', $data);
	}

	
	public function crearEquipo($info = array())
	{
		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			self::sectionNeedLogin();
			return;
		}

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


	public function crearEquipoPOST()
	{
		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			self::sectionNeedLogin();
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

			$valores 				= array();
			$valores['nombre'] 		= $nombre;

			$info['data'] = $valores;

			$validator 				  = array();
			$validator['nombre'] 	  = 'required';

			$gumpValidator = new Gump();
			$is_valid = $gumpValidator->validate($_POST, $validator);

			if($is_valid !== true)
				$info['campos'] = $gumpValidator->errors();

			if(!empty($info['campos']) || !empty($info['mensajes']))
			{
				self::crearEquipo($info);
				return;
			}
			else
			{
				$equipos = new Models\Equipos();

				$new['capitanId'] = $persona->idPersona;
				$new['nombre'] = $nombre;
				$new['fechaRegistro'] = time();
				$equipoId = $equipos->crearEquipo($new);
				$registroId = $equipos->agregarAtleta($equipoId, $persona->idPersona, true);

				Url::redirect('cuenta/equipo/mod/'.$equipoId);
			}
		}
	}

	public function boxesCuenta()
	{
		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			self::sectionNeedLogin();
			return;
		}

		$perfiles = new Models\Perfiles();
		$boxesM = new Models\Boxes();
		$boxes = $perfiles->getBoxesOrganizados($logeado['persona']->idPersona);
		$boxesM->prepararBoxes($boxes);

		$data['title'] = "Mis Boxes";
		$data['boxes'] = $boxes;

		View::renderTemplate('header', $data);
		View::render('tracker/cuenta/boxescuenta', $data, $info);
		View::renderTemplate('footer', $data);
	}

	public function equiposCuenta()
	{
		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			self::sectionNeedLogin();
			return;
		}

		$equipos = new Models\Equipos();
		$misteams = $equipos->getOrganizados($logeado['persona']->idPersona);
		$equipos->prepararEquipos($misteams);
		$teamsintegrante = $equipos->getEquiposCuenta($logeado['persona']->idPersona);
		$equipos->prepararEquipos($teamsintegrante);

		$data['title'] = "Mis Equipos";
		$data['equipos'] = $misteams;
		$data['equiposParticipo'] = $teamsintegrante;

		View::renderTemplate('header', $data);
		View::render('tracker/cuenta/equiposcuenta', $data, $info);
		View::renderTemplate('footer', $data);
	}


	private function checkPermisoEquipo($equipo, $sessionData)
	{
		$persona = $sessionData['persona'];

		$esOrganizador = ($equipo->capitanId == $persona->idPersona);
		$esAdmin = $persona->esAdmin;

		if( $esOrganizador || $esAdmin)
			return true;

		return false;
	}

	public function modEquipo($equipoId, $info = array())
	{
		$equipos = new Models\Equipos();
		$equipo = $equipos->get($equipoId);
		//$perfiles = new Models\Perfiles();
		//$perfiles->prepararBox($box);

		if(!$equipo)
		{
			self::equipoNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			self::sectionNeedLogin();
			return;
		}

		$permiso = self::checkPermisoEquipo($equipo, $logeado);

		if(!$permiso)
		{
			self::sectionNotPermission();
			return;
		}

		//$web = new Models\Web();		
		//$paises = $web->getPaises();

		$data['title'] = "Modificaci&oacute;n de Equipo";
		$data['csrf_token'] = Csrf::makeToken();
		$data['equipo'] = $equipo;
		//$data['paises'] = $paises;

		if(empty($info))
		{
			$info['mensajes'] = array();
			$info['campos'] = array();

			$info['data'] = array();
			$info['data']['nombre'] = $equipo->nombre;
			//$info['data']['urlBox'] = $box->urlBox;
			//$info['data']['pais'] = $box->pais;
		}

		View::renderTemplate('header', $data);
		View::render('tracker/equipo/modinfo', $data, $info);
		View::renderTemplate('footer', $data);
	}


	public function modEquipoPOST($equipoId)
	{
		$equipos = new Models\Equipos();
		$equipo = $equipos->get($equipoId);
		//$perfiles = new Models\Perfiles();
		//$perfiles->prepararBox($box);

		if(!$equipo)
		{
			self::equipoNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$permiso = self::checkPermisoEquipo($equipo, $logeado);

		if(!$permiso)
		{
			self::sectionNotPermission();
			return;
		}

		if (!Csrf::isTokenValid())
		{
			self::sectionCheckInvalid();
			return;
		}

		if(isset($_POST['submit']))
		{
			$nombre 			= filter_input(INPUT_POST, 'nombre');

			$valores 						= array();
			$valores['nombre']				= $nombre;

			$info['data'] = $valores;

			$validator = array();
			$validator['nombre'] = 'required';

			$gumpValidator = new Gump();
			$is_valid = $gumpValidator->validate($_POST, $validator);
			
			if($is_valid !== true)
				$info['campos'] = $gumpValidator->errors();
			
			if(!empty($info['campos']) || !empty($info['mensajes']))
			{
				self::modEquipo($equipo->id, $info);
				return;
			}
			else
			{
				$eventos = new Models\Eventos();

				$new['nombre'] = $nombre;

				$res = $eventos->actualizarEquipo($new, $equipo->id);
				
				Url::redirect('cuenta/misequipos/');
			}
		}
	}

	public function modEquipoIntegrantes($equipoId, $info = array())
	{
		$equipos = new Models\Equipos();
		$equipo = $equipos->get($equipoId);

		if(!$equipo)
		{
			self::equipoNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			self::sectionNeedLogin();
			return;
		}

		$permiso = self::checkPermisoEquipo($equipo, $logeado);

		if(!$permiso)
		{
			self::sectionNotPermission();
			return;
		}

		$atletas = $equipos->getAtletas($equipo->id);
		$perfiles = new Models\Perfiles();
		$perfiles->prepararCuentas($atletas);

		$data['title'] = "Modificaci&oacute;n de Integrantes";
		$data['csrf_token'] = Csrf::makeToken();
		$data['equipo'] = $equipo;
		$data['atletas'] = $atletas;

		if(empty($info))
		{
			$info['mensajes'] = array();
			$info['campos'] = array();
			$info['data'] = array();
		}

		View::renderTemplate('header', $data);
		View::render('tracker/equipo/modintegrantes', $data, $info);
		View::renderTemplate('footer', $data);
	}


	public function modEquipoIntegrantesPOST($equipoId)
	{
		$equipos = new Models\Equipos();
		$equipo = $equipos->get($equipoId);

		if(!$equipo)
		{
			self::equipoNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$permiso = self::checkPermisoEquipo($equipo, $logeado);

		if(!$permiso)
		{
			self::sectionNotPermission();
			return;
		}

		if (!Csrf::isTokenValid())
		{
			self::sectionCheckInvalid();
			return;
		}

		if(isset($_POST['submit']))
		{
			$borrar = array();

			if(isset($_POST['borrar']))
			{
				foreach ($_POST['borrar'] as $key => $value)
				{
					$borrar[$key] = $value;
				}
			}

			foreach ($borrar as $key => $value)
			{
				$equipos->borrarAtleta($equipo->id, $key);
			}

			Url::redirect('cuenta/equipo/mod/'.$equipo->id.'/integrantes');
		}
	}

	public function modEquipoAgregarIntegrantes($equipoId, $info = array())
	{
		$equipos = new Models\Equipos();
		$equipo = $equipos->get($equipoId);

		if(!$equipo)
		{
			self::equipoNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			self::sectionNeedLogin();
			return;
		}

		$permiso = self::checkPermisoEquipo($equipo, $logeado);

		if(!$permiso)
		{
			self::sectionNotPermission();
			return;
		}

		if($equipo->dedicado)
		{
			$eventos = new Models\Eventos();
			$eventoId = $equipo->eventoId;
			$categoriaId = $equipo->categoriaId;

			$atlCategoria = $eventos->getAtletasCategoria($eventoId, $categoriaId);
			$atlEquipados = $eventos->getAtletasEquipados($eventoId, $categoriaId);

			foreach ($atlCategoria as $key => $value)
			{
				if(isset($atlEquipados[$key]) && ($atlEquipados[$key]->aprobado || $atlEquipados[$key]->equipoId == $equipo->id) )
				{
					$value->ocupado = true;
					unset($atlCategoria[$key]);
				}
				else
					$value->ocupado = false;
			}
			
			$data['atletasCategoria'] = $atlCategoria;
		}

		$data['title'] = "Agregar Integrantes";
		$data['csrf_token'] = Csrf::makeToken();
		$data['equipo'] = $equipo;

		if(empty($info))
		{
			$info['mensajes'] = array();
			$info['errores'] = array();
			$info['repetidos'] = array();
		}

		View::renderTemplate('header', $data);
		View::render('tracker/equipo/addintegrantes', $data, $info);
		View::renderTemplate('footer', $data);
	}


	public function modEquipoAgregarIntegrantesPOST($equipoId)
	{
		$equipos = new Models\Equipos();
		$equipo = $equipos->get($equipoId);

		if(!$equipo)
		{
			self::equipoNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$permiso = self::checkPermisoEquipo($equipo, $logeado);

		if(!$permiso)
		{
			self::sectionNotPermission();
			return;
		}

		if (!Csrf::isTokenValid())
		{
			self::sectionCheckInvalid();
			return;
		}

		if(isset($_POST['submit']))
		{
			$ids = $_POST['ids'];
			$idarray = array();
			$mails = array();

			if($equipo->dedicado)
			{
				if(!empty($ids))
				{
					foreach ($ids as $key => $value)
					{
						$idarray[$key] = $value;
					}
				}
			}
			else
			{
				$getdata = str_replace("\r\n", "\n", $ids);
				$arr = explode("\n", $getdata);

				$lines = array_filter($arr);

				foreach ($lines as $key => $value)
				{
					$val = trim($value);

					if(empty($val))
						continue;
					else
					{
						if(ctype_digit(strval($val)))
							$idarray[$key] = $val;
						else
							$mails[$key] = $val;
					}
				}
			}

			$personas = new Models\Cuentas();
			$atletas = array();
			$repetidos = array();
			$errores = array();

			foreach ($idarray as $key => $value)
			{
				$pp = $personas->getPersona($value);

				if($pp)
					$atletas[$pp->idPersona] = $pp;
				else
				{
					$errores[] = $value;
					unset($idarray[$key]);
				}
			}

			foreach ($mails as $key => &$value)
			{
				$pp = $personas->getPersonaPorCorreo($value);

				if($pp)
				{
					$atletas[$pp->correo] = $pp;
					$value = $pp->correo;
				}
				else
				{
					$errores[] = $value;
					unset($mails[$key]);
				}
			}
			unset($value);

			$atletas2 = $equipos->getAtletas($equipo->id);
			$registradosByID = array();
			$registradosByMAIL = array();

			foreach ($atletas2 as $key => $value)
			{
				$registradosByID[$value->personaId] = $value;
				$registradosByMAIL[$value->correo] = $value;
			}


			foreach ($idarray as $key => $keyAtl)
			{
				if(isset($registradosByID[$keyAtl]))
				{
					$repetidos[$keyAtl] = $registradosByID[$keyAtl];
					unset($idarray[$key]);
				}
			}

			foreach ($mails as $key => $mail)
			{
				if(isset($registradosByMAIL[$mail]))
				{
					$repetidos[$mail] = $registradosByMAIL[$mail];
					unset($mails[$key]);
				}
				else
				{
					if(isset($lines[$registradosByMAIL[$mail]->id]))
					{
						$repetidos[$mail] = $registradosByMAIL[$keyAtl];
						unset($mails[$key]);
					}
				}
			}

			if(empty($repetidos) && empty($errores) && empty($info['mensajes']))
			{
				$cuentas = new Models\Cuentas();
				$personas = array();

				foreach ($idarray as $key => $value)
				{
					$self = false;

					if($value == $logeado['id'])
						$self = true;

					$res = $equipos->agregarAtleta($equipo->id, $value, $self);
					$personas[$value] = $cuentas->getPersona($value);
				}

				foreach ($mails as $key => $value)
				{
					$self = false;

					if(!isset($atletas[$value]))
						continue;

					if($atletas[$value]->idPersona == $logeado['id'])
						$self = true;

					$res = $equipos->agregarAtleta($equipo->id, $atletas[$value]->idPersona, $self);
					$personas[$atletas[$value]->idPersona] = $atletas[$value];
				}

				foreach ($personas as $id => $cuenta)
				{
					if($cuenta->correo && $cuenta->correo_verificado)
					{
						$gestor = fopen("app/templates/mail/mail.html", "r");
						$txt = "";

						while (!feof($gestor))
						{
							$line_of_text = fgets($gestor);
							$txt .= $line_of_text;
						}
						fclose($gestor);

						$bdy = "<h1>Pukiebook</h1><p>Saludos, <b>".$cuenta->nombre." ".$cuenta->apellido."</b>. Usted tiene una invitación nueva al equipo \"".$equipo->nombre."\". <a href=\"".DIR."equipo/".$equipo->id."\">Ver Equipo</a></p>";
						$strMail = str_replace("[MENSAJE]", $bdy, $txt);
						$strMail = str_replace("[LOGO1]", DIR."app/templates/".TEMPLATE."/img/logoCuadrado.png", $strMail);

						$mail = new \Helpers\PhpMailer\Mail();
						$mail->CharSet = "UTF-8";
						$mail->IsHTML(true);
						$mail->setFrom(SITEEMAIL);
						$mail->addAddress($cuenta->correo);
						$mail->subject('Pukiebook - Invitación a Equipo');
						$mail->body($strMail);
						$mail->send();
					}
				}

				Url::redirect('cuenta/equipo/mod/'.$equipo->id.'/integrantes');
			}
			else
			{
				$dataStr = array();

				foreach ($idarray as $key => $value)
				{
					$dataStr[$key] = $value;
				}

				foreach ($mails as $key => $value)
				{
					$dataStr[$key] = $value;
				}

				$info['data'] = implode(PHP_EOL, $dataStr);
				$info['errores'] = $errores;
				$info['repetidos'] = $repetidos;

				self::modEquipoAgregarIntegrantes($equipo->id, $info);
			}
		}
	}

	private function checkPermisoBox($box, $sessionData)
	{
		$persona = $sessionData['persona'];

		$esOrganizador = ($box->creadorId == $persona->idPersona);
		$esAdmin = $persona->esAdmin;

		if( $esOrganizador || $esAdmin)
			return true;

		return false;
	}

	public function modBox($boxId, $info = array())
	{
		$eventos = new Models\Eventos();
		$box = $eventos->getBox($boxId);
		$boxes = new Models\Boxes();
		$boxes->prepararBox($box);

		if(!$box)
		{
			self::boxNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			self::sectionNeedLogin();
			return;
		}

		$permiso = self::checkPermisoBox($box, $logeado);

		if(!$permiso)
		{
			self::sectionNotPermission();
			return;
		}

		$web = new Models\Web();		
		$paises = $web->getPaises();

		$data['title'] = "Modificaci&oacute;n de Box";
		$data['csrf_token'] = Csrf::makeToken();
		$data['box'] = $box;
		$data['paises'] = $paises;

		if(empty($info))
		{
			$info['mensajes'] = array();
			$info['campos'] = array();

			$info['data'] = array();
			$info['data']['nombre'] = $box->nombre;
			$info['data']['urlBox'] = $box->urlBox;
			$info['data']['coach'] = $box->coach;
			$info['data']['pais'] = $box->pais;
		}

		View::renderTemplate('header', $data);
		View::render('tracker/box/modinfo', $data, $info);
		View::renderTemplate('footer', $data);
	}


	public function modBoxPOST($boxId)
	{
		$eventos = new Models\Eventos();
		$box = $eventos->getBox($boxId);
		$boxes = new Models\Boxes();
		$boxes->prepararBox($box);

		if(!$box)
		{
			self::boxNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$permiso = self::checkPermisoBox($box, $logeado);

		if(!$permiso)
		{
			self::sectionNotPermission();
			return;
		}

		if (!Csrf::isTokenValid())
		{
			self::sectionCheckInvalid();
			return;
		}

		if(isset($_POST['submit']))
		{
			$nombre 			= filter_input(INPUT_POST, 'nombre');
			$urlBox 			= filter_input(INPUT_POST, 'urlBox');
			$coach 				= filter_input(INPUT_POST, 'coach');
			$pais 				= filter_input(INPUT_POST, 'pais');
			$removerLogo 		= false;
	
			if(isset($_POST['removerLogo']))
				$removerLogo = true;
			
			if($urlBox == "http://")
			{
				$_POST['urlBox'] = "";
				$urlBox = "";
			}

			$valores 						= array();
			$valores['nombre']				= $nombre;
			$valores['urlBox'] 				= $urlBox;
			$valores['coach'] 				= $coach;
			$valores['pais'] 				= $pais;

			$info['data'] = $valores;

			$validator = array();
			$validator['nombre'] = 'required';
			$validator['urlBox'] = 'valid_url';

			$gumpValidator = new Gump();
			$is_valid = $gumpValidator->validate($_POST, $validator);
			
			if($is_valid !== true)
				$info['campos'] = $gumpValidator->errors();
			
			$nombreArchivo = $box->id;
			$ext = null;
			$cuentas = new Models\Cuentas();

			if($removerLogo)
			{
				if($box->logoBox)
				{
					if(file_exists("./".BOXPICTUREPATH.$box->logoBox))
						unlink("./".BOXPICTUREPATH.$box->logoBox);

					$data['logoBox'] = null;
					$res = $eventos->actualizarBox($data, $box->id);
				}
			}
			else
			{
				$archivo = $_FILES['logoBox'];

				if($archivo['error'] == UPLOAD_ERR_OK)
				{
					if($archivo['type'] == "image/jpeg")
						$ext = "jpg";
					else if($archivo['type'] == "image/png")
						$ext = "png";

					if($ext)
					{
						if($box->logoBox)
						{
							if(file_exists("./".BOXPICTUREPATH.$box->logoBox))
								unlink("./".BOXPICTUREPATH.$box->logoBox);
							
							$data['logoBox'] = null;
							$res = $eventos->actualizarBox($data, $box->id);
						}

						$proc = move_uploaded_file($_FILES['logoBox']['tmp_name'], "./".BOXPICTUREPATH.$nombreArchivo.".".$ext);

						if($proc)
						{
							$res = $eventos->actualizarBox(array('logoBox' => $nombreArchivo.".".$ext), $box->id);

	    					$resizer = new ResizeImage("./".BOXPICTUREPATH.$nombreArchivo.".".$ext);
	    					$resizer->resizeTo(300, 300, 'maxHeight');
	    					$resizer->saveImage("./".BOXPICTUREPATH.$nombreArchivo.".".$ext);
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
				self::modBox($box->id, $info);
				return;
			}
			else
			{
				$eventos = new Models\Eventos();

				$new['nombre'] = $nombre;
				$new['urlBox'] = $urlBox;
				$new['coach'] = $coach;
				$new['pais'] = $pais;

				$res = $eventos->actualizarBox($new, $box->id);

				if(isset($errorImagen) && !empty($errorImagen))
				{
					$data['title'] = "Error cargando im&aacute;gen";
					$data['titulo'] = "Error cargando im&aacute;gen";
					$data['mensaje'] = $errorImagen;
					$data['boton'] = "Volver";
					$data['href'] = DIR."cuenta/modbox/".$box->id;	

					View::renderTemplate('header', $data);
					View::render('tracker/mensaje', $data);
					View::renderTemplate('footer', $data);

					return;
				}

				Url::redirect('cuenta/misboxes/');
			}
		}
	}

	public function aprobarBox($boxId)
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
		$perfiles = new Models\Perfiles();
		$boxes = new Models\Boxes();
		$box = $eventos->getBox($boxId);
		$boxes->prepararBox($box);

		if(!$box)
		{
			self::boxNotFound();
			return;
		}

		$aprobacion = $eventos->aprobarBox($box->id);

		if($aprobacion > 0)
		{
			$organizador = $cuentas->getPersona($box->creadorId);

			if($organizador->correo_verificado)
			{
				$gestor = fopen("app/templates/mail/mail.html", "r");
				$txt = "";

				while (!feof($gestor))
				{
					$line_of_text = fgets($gestor);
					$txt .= $line_of_text;
				}
				fclose($gestor);

				$bdy = "Saludos ".$organizador->nombre." ".$organizador->apellido.", <br/><br/>Le informamos que su box \"".$box->nombre."\" ha sido aprobado correctamente.<br/>";
				$strMail = str_replace("[MENSAJE]", $bdy, $txt);
				$strMail = str_replace("[LOGO1]", DIR."app/templates/".TEMPLATE."/img/logoCuadrado.png", $strMail);

				$mail = new \Helpers\PhpMailer\Mail();
				$mail->CharSet = "UTF-8";
				$mail->IsHTML(true);
				$mail->setFrom(SITEEMAIL);
				$mail->addAddress($organizador->correo);
				$mail->subject(SITETITLE." - Box Aprobado (".$box->nombre.")");
				$mail->body($strMail);
				$mail->send();
			}
		}

		$data['title'] = "Box Aprobado";
		$data['titulo'] = "El Box ha sido aprobado correctamente";
		$data['href'] = DIR;
		$data['boton'] = "Volver";

		View::renderTemplate('header', $data);
		View::render('tracker/mensaje', $data);
		View::renderTemplate('footer', $data);
	}

	public function desaprobarBox($boxId)
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
		$perfiles = new Models\Perfiles();
		$boxes = new Models\Boxes();
		$box = $eventos->getBox($boxId);
		$boxes->prepararBox($box);

		if(!$box)
		{
			self::boxNotFound();
			return;
		}

		$aprobacion = $eventos->desaprobarBox($box->id);

		$data['title'] = "Box Desaprobado";
		$data['titulo'] = "El Box ha sido desaprobado correctamente";
		$data['href'] = DIR;
		$data['boton'] = "Volver";

		View::renderTemplate('header', $data);
		View::render('tracker/mensaje', $data);
		View::renderTemplate('footer', $data);
	}

	public function modBoxEliminar($boxId, $info = array())
	{
		$eventos = new Models\Eventos();
		$perfiles = new Models\Perfiles();
		$boxes = new Models\Boxes();
		$box = $eventos->getBox($boxId);
		$boxes->prepararBox($box);

		if(!$box)
		{
			self::boxNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$permiso = self::checkPermisoBox($box, $logeado);

		if(!$permiso)
		{
			self::sectionNotPermission();
			return;
		}

		$data['title'] = "Eliminar box";
		$data['csrf_token'] = Csrf::makeToken();
		$data['box'] = $box;

		View::renderTemplate('header', $data);
		View::render('tracker/box/eliminar', $data, $info);
		View::renderTemplate('footer', $data);
	}

	public function modBoxEliminarPOST($boxId)
	{
		$eventos = new Models\Eventos();
		$perfiles = new Models\Perfiles();
		$boxes = new Models\Boxes();
		$box = $eventos->getBox($boxId);
		$boxes->prepararBox($box);

		if(!$box)
		{
			self::boxNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();

		if(!$logeado)
		{
			Cuenta::sectionNeedLogin();
			return;
		}

		$permiso = self::checkPermisoBox($box, $logeado);

		if(!$permiso)
		{
			self::sectionNotPermission();
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
				$evento = $eventos->borrarBox($box->id);
			}
		}

		Url::redirect('cuenta/misboxes');
	}

}

?>	