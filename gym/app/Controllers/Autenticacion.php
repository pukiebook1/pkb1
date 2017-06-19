<?php
namespace Controllers;

use Core\View;
use Core\Controller;
use Helpers\CsrfNew as Csrf;
use Helpers\Codes;
use Helpers\Gump;
use Helpers\Password;
use Helpers\Sesiones;
use Helpers\Url;
use Models;

class Autenticacion extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function activacionSolicitada($persona)
	{
		$data['title'] = "Activaci&oacute;n Enviada";
		$data['titulo'] = "Revise su correo";
		$data['mensaje'] = "Ha sido enviado un correo a \"".$persona->correo."\", para proceder con la activaci&oacute;n por favor revise su bandeja de entrada.";

		$boton1 = array();
		$boton1['href'] = DIR."";
		$boton1['boton'] = "Ir a Inicio";

		$data['botones'][] = $boton1;

		View::renderTemplate('header', $data);
		View::render('tracker/mensaje', $data);
		View::renderTemplate('footer', $data);
	}

	public function verificacionSolicitada($persona)
	{
		$data['title'] = "Verificaci&oacute;n Enviada";
		$data['titulo'] = "Revise su correo";
		$data['mensaje'] = "Ha sido enviado un correo a \"".$persona->correo."\", para proceder con la verificaci&oacute;n por favor revise su bandeja de entrada.";

		$boton1 = array();
		$boton1['href'] = DIR."";
		$boton1['boton'] = "Ir a Inicio";

		$data['botones'][] = $boton1;

		View::renderTemplate('header', $data);
		View::render('tracker/mensaje', $data);
		View::renderTemplate('footer', $data);
	}

	public function debeActivarCuenta($persona)
	{
		$data['title'] = "Activaci&oacute;n Necesaria";
		$data['titulo'] = "Active su cuenta";
		$data['mensaje'] = "Para activar su cuenta debe ingresar al link que fue enviado a su correo electr&oacute;nico (".$persona->correo."). Si no lo ha recibido presione \"Reenviar Correo\".";

		$boton1 = array();
		$boton1['href'] = DIR."";
		$boton1['boton'] = "Ir a Inicio";

		$boton2 = array();
		$boton2['href'] = DIR."enviarActivacion/cuenta/".$persona->idPersona;
		$boton2['boton'] = "Reenviar Correo";

		$data['botones'][] = $boton2;
		$data['botones'][] = $boton1;

		View::renderTemplate('header', $data);
		View::render('tracker/mensaje', $data);
		View::renderTemplate('footer', $data);
	}

	public function verifiqueCorreo($persona)
	{
		$data['title'] = "Verifique su Correo";
		$data['titulo'] = "Verifique Su Correo";
		$data['mensaje'] = "Debe verificar su direcci&oacute;n de correo para poder recibir notificacions de Pukiebook. Para verificar ahora presione \"Verificar Correo\".";

		$boton1 = array();
		$boton1['href'] = DIR."";
		$boton1['boton'] = "Ir a Inicio";

		$boton2 = array();
		$boton2['href'] = DIR."enviarActivacion/correo/".$persona->idPersona;
		$boton2['boton'] = "Verificar Correo";

		$data['botones'][] = $boton2;
		$data['botones'][] = $boton1;

		View::renderTemplate('header', $data);
		View::render('tracker/mensaje', $data);
		View::renderTemplate('footer', $data);
	}

	public function cuentaNoExiste()
	{
		$data['title'] = "No Existe";
		$data['titulo'] = "La cuenta no existe";
		$data['mensaje'] = "No se encontr&oacute; la cuenta solicitada.";

		$boton1 = array();
		$boton1['href'] = DIR."";
		$boton1['boton'] = "Ir a Inicio";

		$data['botones'][] = $boton1;

		View::renderTemplate('header', $data);
		View::render('tracker/mensaje', $data);
		View::renderTemplate('footer', $data);
	}

	public function cuentaActivada()
	{
		$data['title'] = "Cuenta Activada";
		$data['titulo'] = "Cuenta Activada";
		$data['mensaje'] = "Su cuenta ha sido activada.";

		$boton1 = array();
		$boton1['href'] = DIR."acceder";
		$boton1['boton'] = "Ingresar";

		$data['botones'][] = $boton1;

		View::renderTemplate('header', $data);
		View::render('tracker/mensaje', $data);
		View::renderTemplate('footer', $data);
	}

	public function correoVerificado()
	{
		$data['title'] = "Correo Verificado";
		$data['titulo'] = "Correo Verificado";
		$data['mensaje'] = "Su correo ha sido verificado.";

		$boton1 = array();
		$boton1['href'] = DIR."acceder";
		$boton1['boton'] = "Ingresar";

		$data['botones'][] = $boton1;

		View::renderTemplate('header', $data);
		View::render('tracker/mensaje', $data);
		View::renderTemplate('footer', $data);
	}

	public function enviarActivacion($idPersona)
	{
		$modelCuenta = new Models\Cuentas();
		$persona = $modelCuenta->getPersona($idPersona);
		
		if($persona)
		{
			$codigo = Codes::genCode();
			$modelCuenta->setActivationCode($idPersona, $codigo);

			$gestor = fopen("app/templates/mail/mail.html", "r");
			$txt = "";

			while (!feof($gestor))
			{
				$line_of_text = fgets($gestor);
				$txt .= $line_of_text;
			}
			fclose($gestor);

			$bdy = "Ingrese al siguiente enlace para activar su cuenta: <a href=\"".DIR."activar/cuenta/".$idPersona."/".$codigo."\">Activar Cuenta</a> ";
			$strMail = str_replace("[MENSAJE]", $bdy, $txt);
			$strMail = str_replace("[LOGO1]", DIR."app/templates/".TEMPLATE."/img/logoCuadrado.png", $strMail);

			$mail = new \Helpers\PhpMailer\Mail();
			$mail->CharSet = "UTF-8";
			$mail->IsHTML(true);
			$mail->setFrom(SITEEMAIL);
			$mail->addAddress($persona->correo);
			$mail->subject('Pukiebook - Activación de Cuenta');
			$mail->body($strMail);
			$mail->send();

			self::activacionSolicitada($persona);
			return;
		}
		else
		{
			self::cuentaNoExiste();
			return;
		}
	}

	public function activarCuenta($personaId, $codigo)
	{
		$cuentas = new Models\Cuentas();
		$persona = $cuentas->getPersona($personaId);
		$ok = false;

		if($persona)
		{
			if(strcmp($persona->activationCode,$codigo) == 0) 
				$ok = true;
		}

		$data['title'] = "Activaci&oacute;n de Cuenta";

		if(!$ok)
		{
			$data['titulo'] = "Solicitud No V&aacute;lida";
			$data['mensaje'] = "Esta solicitud de activaci&oacute;n ha expirado o no es v&aacute;lida. Intente de nuevo";
			$data['href'] = DIR."enviarActivacion/cuenta/".$personaId;
			$data['boton'] = "Solicitar Activaci&oacute;n";
			
			View::renderTemplate('header', $data);
			View::render('tracker/mensaje', $data);
			View::renderTemplate('footer', $data);
		}
		else
		{
			$cuentas->activarCuenta($personaId);

			$gestor = fopen("app/templates/mail/mail.html", "r");
			$txt = "";

			while (!feof($gestor))
			{
				$line_of_text = fgets($gestor);
				$txt .= $line_of_text;
			}
			fclose($gestor);

			$bdy = "Su cuenta ha sido activada con éxito";
			$strMail = str_replace("[MENSAJE]", $bdy, $txt);
			$strMail = str_replace("[LOGO1]", DIR."app/templates/".TEMPLATE."/img/logoCuadrado.png", $strMail);

			$mail = new \Helpers\PhpMailer\Mail();
			$mail->CharSet = "UTF-8";
			$mail->IsHTML(true);
			$mail->setFrom(SITEEMAIL);
			$mail->addAddress($persona->correo);
			$mail->subject('Pukiebook - Cuenta Activada');
			$mail->body($strMail);
			$mail->send();

			self::cuentaActivada();
			return;
		}
	}

	public function verificarCorreo($personaId, $codigo)
	{
		$cuentas = new Models\Cuentas();
		$persona = $cuentas->getPersona($personaId);
		$ok = false;

		if($persona)
		{
			if(strcmp($persona->activationCode,$codigo) == 0) 
				$ok = true;
		}

		$data['title'] = "Verificaci&oacute;n de Correo";

		if(!$ok)
		{
			$data['titulo'] = "Solicitud No V&aacute;lida";
			$data['mensaje'] = "Esta solicitud de verificaci&oacute;n ha expirado o no es v&aacute;lida. Intente de nuevo";
			$data['href'] = DIR."enviarVerificacion/correo/".$personaId;
			$data['boton'] = "Solicitar Verificaci&oacute;n";
			
			View::renderTemplate('header', $data);
			View::render('tracker/mensaje', $data);
			View::renderTemplate('footer', $data);
		}
		else
		{
			$cuentas->correoVerificado($personaId);

			$gestor = fopen("app/templates/mail/mail.html", "r");
			$txt = "";

			while (!feof($gestor))
			{
				$line_of_text = fgets($gestor);
				$txt .= $line_of_text;
			}
			fclose($gestor);

			$bdy = "Su correo ha sido verificado con éxito";
			$strMail = str_replace("[MENSAJE]", $bdy, $txt);
			$strMail = str_replace("[LOGO1]", DIR."app/templates/".TEMPLATE."/img/logoCuadrado.png", $strMail);

			$mail = new \Helpers\PhpMailer\Mail();
			$mail->CharSet = "UTF-8";
			$mail->IsHTML(true);
			$mail->setFrom(SITEEMAIL);
			$mail->addAddress($persona->correo);
			$mail->subject('Pukiebook - Correo Verificado');
			$mail->body($strMail);
			$mail->send();

			self::correoVerificado();
			return;
		}
	}

	public function enviarVerificacion($idPersona)
	{
		$modelCuenta = new Models\Cuentas();
		$persona = $modelCuenta->getPersona($idPersona);
		
		if($persona)
		{
			$codigo = Codes::genCode();
			$modelCuenta->setActivationCode($idPersona, $codigo);

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

			self::verificacionSolicitada($persona);
			return;
		}
		else
		{
			self::cuentaNoExiste();
			return;
		}
	}
	public function salir()
	{
		Sesiones::destroy();
		Url::redirect();
	}

	public function accederSinRedirect()
	{
		self::acceder(array(), true);
	}

	public function acceder($info = array(), $anulaPrevio = false)
	{
		if(Models\Cuentas::getSession())
			Url::redirect('cuenta');
		
		$data['title'] = "Acceder";
		$data['csrf_token'] = Csrf::makeToken();
		$data['previ'] = Url::getPrevious();

		if($anulaPrevio)
			unset($data['previ']);

		if(empty($info))
		{
			$info['mensajes'] = array();
			$info['campos'] = array();
			$info['data'] = array();
		}

		View::renderTemplate('header', $data);
		View::render('tracker/acceder', $data, $info);
		View::renderTemplate('footer', $data);
	}

	public function accederPOST()
	{
		if(isset($_POST['submit']))
		{
			$info = array();
			$info['mensajes'] = array();
			$info['campos'] = array();
			$info['data'] = array();

			if (!Csrf::isTokenValid())
			{
				Cuenta::sectionCheckInvalid();
				return;
			}

			$email = filter_input(INPUT_POST, 'correo');
			$password = filter_input(INPUT_POST, 'contrasena');
			$previo = filter_input(INPUT_POST, 'previous');

			$valores = array('correo' => $email);
			$info['data'] = $valores;

			$gumpValidator = new Gump();

			$is_valid = $gumpValidator->validate($_POST, array(
				'contrasena' => 'required',
				'correo' => 'required'
			));

			if($is_valid !== true)
			{
				$info['campos'] = $gumpValidator->errors();
			}

			$modelCuenta = new Models\Cuentas();
			$persona = $modelCuenta->comprobarAcceso($email, $password);

			if($persona == false)
			{
				$info['mensajes'][] = "Combinaci&oacute;n correo/contrase&ntilde;a incorrecta o no existe la cuenta.";
			}
			else
			{
				if(!$persona->cuentaActivada)
				{
					self::debeActivarCuenta($persona);
					return;
				}
				else
				{
					Sesiones::$logged = true;
					Sesiones::$personaId = $persona->idPersona;
					Sesiones::$time = $time;
					Sesiones::guardarEstado();

					if(!$persona->correo_verificado)
					{
						self::verifiqueCorreo($persona);
					}
					else
					{
						if(!empty($previo))
							Url::redirect($previo, true);
						else
							Url::redirect();
					}

					return;
				}
			}

			if(!empty($info['campos']) || !empty($info['mensajes']))
			{
				self::acceder($info);
				return;
			}
		}
	}

	public function registrar($info = array(), $anulaPrevio = false)
	{
		$data['title'] = "Registrar";

		$web = new Models\Web();
		$paises = $web->getPaises();
		
		$data['paises'] = $paises;
		$data['csrf_token'] = Csrf::makeToken();
		$data['previ'] = Url::getPrevious();

		if($anulaPrevio)
			unset($data['previ']);

		if(empty($info))
		{
			$info['mensajes'] = array();
			$info['campos'] = array();
			$info['data'] = array();
		}

		View::renderTemplate('header', $data);
		View::render('tracker/registrar', $data, $info);
		View::renderTemplate('footer', $data);
	}

	public function registrarPOST()
	{
		if (!Csrf::isTokenValid())
		{
			Cuenta::sectionCheckInvalid();
			return;
		}

		if(isset($_POST['submit']))
		{
			$info = array();
			$info['mensajes'] = array();
			$info['campos'] = array();
			$info['data'] = array();

			$nombre = filter_input(INPUT_POST, 'nombre');
			$apellido = filter_input(INPUT_POST, 'apellido');
			$ident = filter_input(INPUT_POST, 'ident');
			$password = filter_input(INPUT_POST, 'contrasena');
			$email = filter_input(INPUT_POST, 'correo');
			$sexo = filter_input(INPUT_POST, 'sexo');
			$previo = filter_input(INPUT_POST, 'previous');

			$ident = str_replace(".", "", $ident);
			$ident = str_replace(",", "", $ident);
			$ident = str_replace(" ", "", $ident);
			$ident = str_replace("-", "", $ident);

			$valores = array(
				'nombre' => ucwords(strtolower($nombre)),
				'apellido' => ucwords(strtolower($apellido)),
				'ident' => strtolower($ident),
				'contrasena' => $password,
				'sexo' => $sexo,
				'correo' => strtolower($email),
				);

			$info['data'] = $valores;

			$gumpValidator = new Gump();

			$is_valid = $gumpValidator->validate($_POST, array(
				'nombre' => 'required|valid_name',
				'apellido' => 'required|valid_name',
				'ident' => 'required|alpha_numeric',
				'contrasena' => 'required|alpha_numeric',
				'sexo' => 'required',
				'correo' => 'required|valid_email',
			));

			
			if($is_valid !== true)
			{
				$info['campos'] = $gumpValidator->errors();
			}

			if(!empty($info['campos']) || !empty($info['mensajes']))
			{
				self::registrar($info);
				return;
			}

			$modelCuenta = new Models\Cuentas();

			if($modelCuenta->correoRegistrado($email))
			{
				$info['mensajes'][] = "Ya existe una cuenta con el correo electr&oacute;nico ingresado.";
				self::registrar($info);
				return;
			}

			$valores['contrasena'] = Password::make($password);
			$valores['fechaRegistro'] = time();
			
			$idUsuario = $modelCuenta->registrarUsuario($valores);
			
			if($idUsuario > 0)
			{
				Sesiones::$logged = true;
				Sesiones::$personaId = $idUsuario;
				Sesiones::$time = $time;
				Sesiones::guardarEstado();

				$gestor = fopen("app/templates/mail/mail.html", "r");
				$txt = "";

				while (!feof($gestor))
				{
					$line_of_text = fgets($gestor);
					$txt .= $line_of_text;
				}
				fclose($gestor);

				$bdy = "<h1>Pukiebook</h1><p>Bienvenido a Pukiebook <b>".$nombre." ".$apellido."</b>. Tu registro ha sido completado correctamente.</p><p>Tu número de registro para eventos es #".$idUsuario."</p>";
				$strMail = str_replace("[MENSAJE]", $bdy, $txt);
				$strMail = str_replace("[LOGO1]", DIR."app/templates/".TEMPLATE."/img/logoCuadrado.png", $strMail);

				$mail = new \Helpers\PhpMailer\Mail();
				$mail->CharSet = "UTF-8";
				$mail->IsHTML(true);
				$mail->setFrom(SITEEMAIL);
				$mail->addAddress($email);
				$mail->subject('Pukiebook - Registro Completado');
				$mail->body($strMail);
				$mail->send();

				if(!empty($previo))
					Url::redirect($previo, true);
				else
					Url::redirect('cuenta');
			}

			//Url::redirect();
		}
	}

	public function test($get)
	{
		print_r(Password::make($get));
	}
}

?>