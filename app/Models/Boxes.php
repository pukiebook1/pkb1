<?php
namespace Models;

use Core\Model;

class Boxes extends Model
{
	function __construct()
	{
		parent::__construct();
	}

	public function prepararBoxes($boxes = array())
	{
		foreach ($boxes as $key => $value)
		{
			self::prepararBox($value);
		}
	}

	public function prepararBox($box)
	{
		if(!isset($box) || empty($box))
			return;

		if ($box->logoBox && file_exists("./".BOXPICTUREPATH.$box->logoBox))
			$box->fotoPath = DIR.BOXPICTUREPATH.$box->logoBox;
		else
			$box->fotoPath = DIR.BOXPICTUREPATH."default.jpg";

		if($box->id)
			$box->internalURL = $box->id;
	}

	public function getBox($boxId)
	{
		$res = $this->db->select("SELECT `bo`.`id` AS `id`, `bo`.`nombre` AS `nombre`, `bo`.`logoBox` AS `logoBox`, `bo`.`urlBox` AS `urlBox`, `bo`.`coach` AS `coach`, `bo`.`pais` AS `pais`, `bo`.`creadorId` AS `creadorId`, `bo`.`aprobado` AS `aprobado`, `bo`.`fechaRegistro` AS `fechaRegistro`, `pa`.`country_name` AS `paisStr` FROM (`boxes` `bo` LEFT JOIN `paises` `pa` ON ((`bo`.`pais` = `pa`.`id`))) WHERE `bo`.`id` = ".$boxId);

		if(empty($res))
			return false;
		else
			return $res[0];
	}

	public function getAtletasBox($boxId)
	{
		$atletas = array();

		$res = $this->db->select("SELECT * FROM personas WHERE boxId = ".$boxId." ORDER BY nombre ASC");

		foreach ($res as $key => $value)
		{
			$atletas[$value->id] = $value;
		}

		return $atletas;
	}
}

?>