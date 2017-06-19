<?php
namespace Controllers;

use Core\View;
use Core\Controller;
use Helpers\Url;
use Helpers\Gump;
use Helpers\CsrfNew as Csrf;
use Helpers\Password;
use Models;

class Admin extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}
		
	public function resetearClaves($info = array())
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

		$data['title'] = "Reseteo de claves";
		$data['csrf_token'] = Csrf::makeToken();
		$data['logeado'] = $logeado;

		if(empty($info))
		{
			$info['mensajes'] = array();
			$info['data'] = array();
		}

		View::renderTemplate('header', $data);
		View::render('tracker/admin/reseteoClaves', $data, $info);
		View::renderTemplate('footer', $data);
	}

	public function resetearClavesPOST($info = array())
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

		if (!Csrf::isTokenValid())
		{
			Cuenta::sectionCheckInvalid();
			return;
		}

		if(isset($_POST['submit']))
		{
			$tmpCorreos = $_POST['correos'];

			$cuentas = new Models\Cuentas();
			$toFailed = array();
			$toReset = array();
			$info = array();

			foreach ($tmpCorreos as $key => $value)
			{
				$correo = trim($value);

				if(!empty($correo))
				{
					if($cuentas->correoRegistrado($correo))
						$toReset[] = $value;
					else
						$toFailed[] = $value;
				}
			}

			foreach ($toReset as $key => $value)
			{			
				$sinCifrado = rand(1000, 9999);
				$pass = Password::make($sinCifrado);
				$valueUp['contrasena'] = $pass;

				$idUsuario = $cuentas->actualizarContrasena($value, $pass);

				if($idUsuario > 0)
				{
					$gestor = fopen("app/templates/mail/mail.html", "r");
					$txt = "";

					while (!feof($gestor))
					{
						$line_of_text = fgets($gestor);
						$txt .= $line_of_text;
					}
					fclose($gestor);

					$bdy = "<h1>Pukiebook</h1><p>Bienvenido a Pukiebook. </p><p>Tu usuario es: ".$value."</p><p>Tu contraseña es: ".$sinCifrado."</p>";
					$strMail = str_replace("[MENSAJE]", $bdy, $txt);
					$strMail = str_replace("[LOGO1]", DIR."app/templates/".TEMPLATE."/img/logoCuadrado.png", $strMail);

					$mail = new \Helpers\PhpMailer\Mail();
					$mail->CharSet = "UTF-8";
					$mail->IsHTML(true);
					$mail->setFrom(SITEEMAIL);
					$mail->addAddress($value);
					$mail->subject('Pukiebook - Contraseña');
					$mail->body($strMail);
					$mail->send();
				}
			}

			if(!empty($toFailed))
			{
				$info['data'] = $toFailed;
				$info['mensajes'][0] = "No se encontraron cuentas para los siguientes correos.";

				self::resetearClaves($info);
			}
			else
			{
				$data['title'] = "Reseteo completado";
				$data['titulo'] = "Reseteo de claves completado";
				$data['mensaje'] = "El reseteo de claves se ha completado correctamente. Se ha enviado un correo a cada cuenta reseteada con su nueva clave.";
				$data['boton'] = "Ir a mi cuenta";
				$data['href'] = DIR."cuenta";

				View::renderTemplate('header', $data);
				View::render('tracker/mensaje', $data);
				View::renderTemplate('footer', $data);
			}
		}
	}

	public function registrarUsuarios($info = array())
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

		$data['title'] = "Registro de Usuarios";
		$data['csrf_token'] = Csrf::makeToken();
		$data['logeado'] = $logeado;

		if(empty($info))
		{
			$info['mensajes'] = array();
			$info['data'] = array();
		}

		View::renderTemplate('header', $data);
		View::render('tracker/admin/registroUsuarios', $data, $info);
		View::renderTemplate('footer', $data);
	}


	public function registrarUsuariosPOST()
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

		if (!Csrf::isTokenValid())
		{
			Cuenta::sectionCheckInvalid();
			return;
		}

		if(isset($_POST['submit']))
		{
			$tmpNombres = $_POST['nombres'];
			$tmpApellidos = $_POST['apellidos'];
			$tmpCorreos = $_POST['correos'];

			$toInsert = array();
			$info = array();

			foreach ($tmpNombres as $key => $value)
			{
				$nombre = trim($tmpNombres[$key]);
				$apellido = trim($tmpApellidos[$key]);
				$correo = trim($tmpCorreos[$key]);

				if(!empty($nombre) || !empty($apellido) || !empty($correo))
				{
					$item = array();
					$item['nombre'] = $nombre;
					$item['apellido'] = $apellido;
					$item['correo'] = $correo;

					if(!empty($nombre) && !empty($correo))
						$toInsert[] = $item;
					else
						$info['mensajes'][0] = "Verifique los campos requeridos (nombre/correo).";

					$info['data'][] = $item;
				}
				else
				{
					unset($tmpNombres[$key]);
					unset($tmpApellidos[$key]);
					unset($tmpCorreos[$key]);
				}
			}

			if(!empty($info) && isset($info['mensajes']))
			{
				self::registrarUsuarios($info);
			}
			else
			{
				$modelCuenta = new Models\Cuentas();
				$gumpValidator = new Gump();
				$repetidos = array();

				foreach ($toInsert as $key => $value)
				{
					$is_valid = $gumpValidator->validate($value, array('correo' => 'valid_email'));

					if($is_valid !== true)
					{
						$info2['mensajes'][1] = "Verifique los correos.";
						$repetidos[] = $value;
					}
					else
					{
						if($modelCuenta->correoRegistrado($value['correo']))
						{
							$info2['mensajes'][2] = "Se encontraron correos registrados actualmente...";
							$repetidos[] = $value;
						}
						else
						{
							$sinCifrado = rand(1000, 9999);
							$pass = Password::make($sinCifrado);
							$value['contrasena'] = $pass;
							$value['fechaRegistro'] = time();
							$idUsuario = $modelCuenta->registrarUsuario($value);

							if($idUsuario > 0)
							{
								$gestor = fopen("app/templates/mail/mail.html", "r");
								$txt = "";

								while (!feof($gestor))
								{
									$line_of_text = fgets($gestor);
									$txt .= $line_of_text;
								}
								fclose($gestor);

								$bdy = "<h1>Pukiebook</h1><p>Bienvenido a Pukiebook <b>".$value['nombre']." ".$value['apellido']."</b>. Tu registro ha sido completado correctamente.</p><p>Tu número de registro para eventos es #".$idUsuario."</p><p>Tu usuario es: ".$value['correo']."</p><p>Tu contraseña es: ".$sinCifrado."</p>";
								$strMail = str_replace("[MENSAJE]", $bdy, $txt);
								$strMail = str_replace("[LOGO1]", DIR."app/templates/".TEMPLATE."/img/logoCuadrado.png", $strMail);

								$mail = new \Helpers\PhpMailer\Mail();
								$mail->CharSet = "UTF-8";
								$mail->IsHTML(true);
								$mail->setFrom(SITEEMAIL);
								$mail->addAddress($value['correo']);
								$mail->subject('Pukiebook - Registro Completado');
								$mail->body($strMail);
								$mail->send();
							}
						}						
					}
				}

				if(!empty($repetidos))
				{
					$info2['data'] = $repetidos;
					self::registrarUsuarios($info2);
				}
				else
				{
					$data['title'] = "Registro completado";
					$data['titulo'] = "Registro de cuentas completado";
					$data['mensaje'] = "El registro de las cuentas se ha completado correctamente. Se ha enviado un correo a cada cuenta registrada con su clave.";
					$data['boton'] = "Ir a mi cuenta";
					$data['href'] = DIR."cuenta";

					View::renderTemplate('header', $data);
					View::render('tracker/mensaje', $data);
					View::renderTemplate('footer', $data);
				}
			}
		}
	}
}
?>