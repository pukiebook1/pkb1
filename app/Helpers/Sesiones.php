<?php
namespace Helpers;

use Helpers\Database;

class Sesiones
{
	public static $logged = false;
	public static $db = null;
	public static $id = 0;
	public static $time = 0;
	public static $personaId = 0;
	public static $csrf_token = null;
	public static $csrf_token_time = null;

	public static function init()
	{
		$new = true;

		if(isset($_COOKIE['sesion']) && !empty($_COOKIE['sesion']))
		{
			$id = $_COOKIE['sesion'];
			$time = $_COOKIE['time'];
			$new = false;
		}
		else
		{
			$id = COOKIE_PREFIX.(time()*3)."_".rand(0,99999);
			$time = time();
		}

		self::$db = Database::get();
		self::$id = $id;
		self::$time = $time;

		setcookie('sesion', self::$id, time() + COOKIE_LIFE, '/');
		setcookie('time', self::$time, time() + COOKIE_LIFE, '/');

		$existe = self::$db->select("SELECT * FROM sesiones WHERE sessionId = '".self::$id."'");
		
		if($new)
		{
			if($existe)
				self::$db->delete('sesiones', array('sessionId' => self::$id), 'a');

			self::$db->insert('sesiones', array('sessionId' => self::$id, 'fecha' => self::$time, 'lastAct' => self::$time, 'ip' => $_SERVER['REMOTE_ADDR']));
		}
		else
		{
			if(empty($existe))
				self::$db->insert('sesiones', array('sessionId' => self::$id, 'fecha' => self::$time, 'lastAct' => time(), 'ip' => $_SERVER['REMOTE_ADDR']));
			else
			{
				self::$db->update('sesiones', array('lastAct' => time(), 'ip' => $_SERVER['REMOTE_ADDR']), array('sessionId' => self::$id));
				self::$personaId = $existe[0]->personaId;
				self::$csrf_token = $existe[0]->csrf_token;
				self::$csrf_token_time = $existe[0]->csrf_token_time;
				self::$logged = true;
			}
		}
	}

	public static function guardarEstado()
	{
		self::$db->update('sesiones', array('lastAct' => time(), 'personaId' => self::$personaId), array('sessionId' => self::$id));
	}

	public static function destroy()
	{
		self::$db->delete('sesiones', array('sessionId' => self::$id), 'a');
		self::init();
	}

	public static function setCsrf($token, $time)
	{
		self::$csrf_token = $token;
		self::$csrf_token_time = $time;
		self::$db->update('sesiones', array('lastAct' => time(), 'csrf_token' => $token, 'csrf_token_time' => $time, 'ip' => $_SERVER['REMOTE_ADDR']), array('sessionId' => self::$id));
	}

	public static function getCsrfToken()
	{
		return self::$csrf_token;
	}

	public static function getCsrfTokenTime()
	{
		return self::$csrf_token_time;
	}
}

?>