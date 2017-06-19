<?php
namespace Helpers;

/*
 * Date Helper - collection of methods for working with dates
 *
 * @author David Carr - dave@simplemvcframework.com
 * @version 1.0
 * @date May 18 2015
 */
class Date
{
	/**
	 * get the difference between 2 dates
	 * @param  date $from start date
	 * @param  date $to   end date
	 * @param  string $type the type of difference to return
	 * @return string or array, if type is set then a string is returned otherwise an array is returned
	 */
	public static function difference($from, $to, $type = null)
	{
		$d1 = new \DateTime($from);
		$d2 = new \DateTime($to);
		$diff = $d2->diff($d1);
		if ($type == null) {
			//return array
			return $diff;
		} else {
			return $diff->$type;
		}
	}

	public static function timeToFullString($time)
	{														
		$mili = 0;
		$seg = 0;
		$min = 0;
		$hor = 0;
		$tiempo = $time;

		$mili = $tiempo % 1000;
		$tiempo = ($tiempo - $mili) / 1000;
		$seg = $tiempo % 60;
		$tiempo = ($tiempo - $seg) / 60;
		$min = $tiempo % 60;
		$hor = ($tiempo - $min) / 60;

		return sprintf("%'02d", $hor).":".sprintf("%'02d", $min).":".sprintf("%'02d", $seg);
	}
}
