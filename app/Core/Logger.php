<?php
namespace Core;

use Core\Controller;
use Core\View;
use Core\Config;
use Helpers\PhpMailer\Mail;

/*
 * logger class - Custom errors
 *
 * @author David Carr - dave@simplemvcframework.com
 * @version 2.2
 * @date June 27, 2014
 * @date updated May 18 2015
 */
class Logger extends Controller
{

	/**
	* determins if error should be displayed
	* @var boolean
	*/
	private static $printError = false;

	/**
	* determins if error should be emailed to SITEEMAIL defined in app/Core/Config.php
	* @var boolean
	*/
	private static $emailError = false;

	/**
	* clear the errorlog
	* @var boolean
	*/
	private static $clear = false;

	/**
	* path to error file
	* @var boolean
	*/
	private static $errorFile = 'errorlog.html';

	/**
	* in the event of an error show this message
	*/
	public static function customErrorMsg()
	{
		//echo "<p>An error occured, The error has been reported.</p>";

		header("HTTP/1.0 500 Internal Server Error");

		$data['title'] = '500';
		//$data['error'] = $this->error;

		View::renderTemplate('header', $data);
		View::render('error/500', $data);
		View::renderTemplate('footer', $data);

		exit;
	}

	/**
	* saved the exception and calls customer error function
	* @param  exeption $e
	*/
	public static function exceptionHandler($e)
	{
		self::newMessage($e);
		self::customErrorMsg();
	}

	public static function FatalHandler()
	{
		$error = error_get_last();
		
		if($error !== NULL && $error['type'] === E_ERROR)
		{
			self::errorHandler($error['type'], $error['message'], $error['file'], $error['line'], true);
			echo "<p>Ha ocurrido un error fatal. Este error ha sido reportado.</p>";
			exit;
		}
	}

	/**
	* saves error message from exception
	* @param  numeric $number  error number
	* @param  string $message the error
	* @param  string $file    file originated from
	* @param  numeric $line   line number
	*/
	public static function errorHandler($number, $message, $file, $line, $fatal = false)
	{
		$msg = "$message en $file en linea $line";

		if (($number !== E_NOTICE) && ($number < 2048))
		{
			self::errorMessage($msg);
			
			if(!$fatal)
				self::customErrorMsg();
		}

		return 0;
	}

	/**
	* new exception
	* @param  Exception $exception
	* @param  boolean   $printError show error or not
	* @param  boolean   $clear       clear the errorlog
	* @param  string    $errorFile  file to save to
	*/
	public static function newMessage(\Exception $exception)
	{
		$message = $exception->getMessage();
		$code = $exception->getCode();
		$file = $exception->getFile();
		$line = $exception->getLine();
		$trace = $exception->getTraceAsString();
		$date = date('d M Y G:iA');

		$logMessage = "<h3>Informaci&oacute;n del Error:</h3>\n
		   <p><strong>Fecha:</strong> {$date}</p>\n
		   <p><strong>Versi&oacute;n de Pukiebook:</strong> ".VERSION."</p>\n
		   <p><strong>Mensaje:</strong> {$message}</p>\n
		   <p><strong>C&oacute;digo:</strong> {$code}</p>\n
		   <p><strong>Archivo:</strong> {$file}</p>\n
		   <p><strong>L&iacute;nea:</strong> {$line}</p>\n
		   <h3>Stack trace:</h3>\n
		   <pre>{$trace}</pre>\n
		   <hr />\n";

		if (is_file(self::$errorFile) === false) {
			file_put_contents(self::$errorFile, '');
		}

		$content = "";

		if (self::$clear)
		{
			$f = fopen(self::$errorFile, "r+");
			
			if ($f !== false)
			{
				ftruncate($f, 0);
				fclose($f);
			}

			$content = null;
		}
		else
		{
			$content = file_get_contents(self::$errorFile);
		}

		file_put_contents(self::$errorFile, $logMessage . $content);

		//send email
		self::sendEmail($logMessage);

		if (self::$printError == true) {
			echo $logMessage;
			exit;
		}
	}

	/**
	* custom error
	* @param  string  $error       the error
	* @param  boolean $printError display error
	* @param  string  $errorFile  file to save to
	*/
	public static function errorMessage($error)
	{
		$date = date('d M Y G:iA');
		$logMessage = "<p>Version: ".VERSION."<br/><br/>Error en $date:<br/>$error</p>";

		if (is_file(self::$errorFile) === false) {
			file_put_contents(self::$errorFile, '');
		}

		$content = "";

		if (self::$clear)
		{
			$f = fopen(self::$errorFile, "r+");
			
			if ($f !== false)
			{
				ftruncate($f, 0);
				fclose($f);
			}

			$content = null;
		}
		else
		{
			$content = file_get_contents(self::$errorFile);
		}

		file_put_contents(self::$errorFile, $logMessage . $content);

		//send email
		self::sendEmail($logMessage);

		if (self::$printError == true) {
			echo $logMessage;
			exit;
		}
	}

	public static function sendEmail($message)
	{
		if (self::$emailError == true)
		{
			$mail = new Mail();
			$mail->setFrom(SITEEMAIL);
			$mail->addAddress("pukiebook@gmail.com");
			$mail->subject('Error en '.SITETITLE);
			$mail->body($message);
			$mail->send();
		}
	}
}
