<?php
namespace Helpers;

class Codes
{
	public static function genCode()
	{
		$chars = "pkb13579dorh"; 
		srand((double)microtime()*1000000);
		$i = 0; 
		$pass = '';

		while ($i <= 7)
		{
			$num = rand() % 12;
			$tmp = substr($chars, $num, 1); 
			$pass = $pass . $tmp; 
			$i++; 
		}

		return $pass; 
	}

	public static function getEventoNombre($numTipo)
	{
		$ev = "";

		switch ($numTipo) {
			case 1:
				$ev = "Crossfit";
				break;
			case 2:
				$ev = "MTB";
				break;
			case 3:
				$ev = "BMX";
				break;
			case 4:
				$ev = "Functional Training";
				break;
			case 5:
				$ev = "Running";
				break;
			case 6:
				$ev = "Mud Race";
				break;
			case 7:
				$ev = "Biatlon";
				break;
			case 8:
				$ev = "Triatlon";
				break;
			case 9:
				$ev = "Weightlifting";
				break;
			case 10:
				$ev = "Open";
				break;
			case 11:
				$ev = "Crossfit (Standard Crossfit Games)";
				break;
			case 12:
				$ev = "Crossfit (Standard Crossfit Regional)";
				break;
			
			default:
				$ev = "Desconocido";
				break;
		}

		return $ev;
	}
}

?>