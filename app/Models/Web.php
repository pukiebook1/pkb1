<?php
namespace Models;

use Core\Model;

class Web extends Model
{
	function __construct()
	{
		parent::__construct();
	}

	public function getPaises()
	{
		$res = $this->db->select("SELECT * FROM paises");
		$paises = array();

		foreach ($res as $key => $value)
		{
			$paises[$value->id] = $value;
		}
		
		return $paises;
	}

	public function getPlanes()
	{
		$res = $this->db->select("SELECT * FROM planes");
		$planes = array();

		foreach ($res as $key => $value)
		{
			$planes[$value->id] = $value;
		}
		
		return $planes;
	}

	public function getZonasHorarias()
	{
		$res = $this->db->select("SELECT * FROM zonashorarias");
		$zonas = array();

		foreach ($res as $key => $value)
		{
			$zonas[$value->id] = $value;
		}
		
		return $zonas;
	}

	public function getBoxes()
	{
		$res = $this->db->select("SELECT `bo`.`id` AS `id`, `bo`.`nombre` AS `nombre`, `bo`.`logoBox` AS `logoBox`, `bo`.`urlBox` AS `urlBox`, `bo`.`coach` AS `coach`, `bo`.`pais` AS `pais`, `bo`.`creadorId` AS `creadorId`, `bo`.`aprobado` AS `aprobado`, `bo`.`fechaRegistro` AS `fechaRegistro`, `pa`.`country_name` AS `paisStr` FROM (`boxes` `bo` LEFT JOIN `paises` `pa` ON ((`bo`.`pais` = `pa`.`id`))) WHERE bo.aprobado = 1 ORDER BY bo.nombre");
		$boxes = array();

		foreach ($res as $key => $value)
		{
			$boxes[$value->id] = $value;
		}
		
		return $boxes;
	}
}

?>