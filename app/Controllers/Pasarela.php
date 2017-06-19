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




echo "hola";
// get the variables from PayGol system 
$persona		= $_GET['x_extra3'];
$eventoId		= $_GET['x_extra2'];
$categoriaId	= $_GET['x_extra1'];
$x_cod_response	= $_GET['x_cod_response'];
$telefono = $persona[0];
$edad = $persona[1];
$centro_entrenamiento = $persona[2];
$observacion	= $persona[3];
$personaId		= $persona[4];

//Replace these parameters by your database details 
$dbhost = "localhost"; // TU HOST 
$dbuser = "root"; // USUARIO MYSQL u361724199_web
$dbpassword = ""; //TU DATABASE PASSWORD pukiepukie
$db = "pkb"; // TU DATABASE (DONDE ESTAN LAS CUENTAS)u361724199_pkb

if ($x_cod_response == 1) {
	//Connect to Database 
	
	

	$conn = mysql_connect($dbhost, $dbuser, $dbpassword); 
	mysql_select_db($db); 
	// verificamos si ya no hay solici
	$sql = "INSERT INTO `$db`.`pagosevento` (`id`, `numreferencia`, `monto`, `formapago`, `observacion`, `telefono`, `edad`, `centro_entrenamiento`, `pagoId`) VALUES ('NULL', '99999', '2', '4', '$observacion', '$telefono', '$edad', '$centro_entrenamiento', 'NULL')";
	mysql_query($sql);
	// Ingresamos al alteta al evento
	$sql = "INSERT INTO `$db`.`registroatletas` (`personaId`, `eventoId`, `categoriaId`, `aprobado`) VALUES ('$personaId', '$eventoId', '$categoriaId', '1')";
	mysql_query($sql); 		
	// Agregar y asignar pago
	
	
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
	
	
	echo "Aceptada, ";

} 
 if ($x_cod_response == 2) {
 	echo "Rechazada";
 } 
 if ($x_cod_response == 3) {
 	//Connect to Database 
	$conn = mysql_connect($dbhost, $dbuser, $dbpassword); 
	mysql_select_db($db); 
	// Creamos una solicitud pendiente
	$sql = "INSERT INTO `$db`.`registroatletas` (`personaId`, `eventoId`, `categoriaId`, `aprobado`) VALUES ('$personaId', '$eventoId', '$categoriaId', '0')";
	mysql_query($sql); 
	mysql_close($conn); 
	echo "Aceptada,$x_cod_response ";
 	echo "Pendiente";
 } 
 if ($x_cod_response == 4) {
 	echo "Fallida";
 } 

/*
//Connect to Database 
$conn = mysql_connect($dbhost, $dbuser, $dbpassword); 
mysql_select_db($db); 

$sql = "UPDATE `pkb`.`personas` SET `apellido`='$price' WHERE `id`='1'";
mysql_query($sql); 
mysql_close($conn); */


?> 