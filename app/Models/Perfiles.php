<?php
namespace Models;

use Core\Model;

class Perfiles extends Model
{
	function __construct()
	{
		parent::__construct();
	}

	public function getRegistrosEvento($idPersona)
	{
		$res = $this->db->select("SELECT C.nombre as categoria, E.*, R.id as regId FROM registroatletas R, eventos E, categorias C WHERE E.id = R.eventoId AND R.categoriaId = C.id AND R.personaId =".$idPersona);
		
		if(!empty($res))
			return $res;

		return array();
	}

	public function getRegistrosPendientesEvento($idPersona)
	{
		$res = $this->db->select("SELECT C.nombre as categoria, E.*, R.id as regId FROM registroatletas R, eventos E, categorias C WHERE R.aprobado = 0 AND E.id = R.eventoId AND R.categoriaId = C.id AND R.personaId =".$idPersona);
		
		if(!empty($res))
			return $res;

		return array();
	}

	public function getEventosOrganizados($idPersona)
	{
		$res = $this->db->select("SELECT * FROM eventos WHERE creadorId = ".$idPersona);
		
		if(!empty($res))
			return $res;

		return array();
	}

	public function getBoxesOrganizados($idPersona)
	{
		$res = $this->db->select("SELECT `bo`.`id` AS `id`, `bo`.`nombre` AS `nombre`, `bo`.`logoBox` AS `logoBox`, `bo`.`urlBox` AS `urlBox`, `bo`.`coach` AS `coach`, `bo`.`pais` AS `pais`, `bo`.`creadorId` AS `creadorId`, `bo`.`aprobado` AS `aprobado`, `bo`.`fechaRegistro` AS `fechaRegistro`, `pa`.`country_name` AS `paisStr` FROM (`boxes` `bo` LEFT JOIN `paises` `pa` ON ((`bo`.`pais` = `pa`.`id`))) WHERE bo.creadorId = " . $idPersona);
		
		if(!empty($res))
			return $res;

		return array();
	}

	public function getEventosJuez($idPersona)
	{
		$res = $this->db->select("SELECT * FROM eventos WHERE juezSuplente = " . $idPersona." OR juezPrincipal = ". $idPersona);
		
		if(!empty($res))
			return $res;

		return array();
	}

	public function getParticipaciones($idPersona)
	{
		$res = $this->db->select("SELECT EV.* FROM eventos EV INNER JOIN registroatletas RA ON (RA.eventoId = EV.id) AND RA.aprobado = 1 AND EV.aprobado = 1 AND RA.personaId = ". $idPersona);
		
		if(!empty($res))
			return $res;

		return array();
	}

	public function getEquiposIntegrante($idPersona)
	{
		$res = $this->db->select("SELECT EQ.* FROM equipos EQ INNER JOIN relpersonaequipo RPE ON (RPE.equipoId = EQ.id) WHERE RPE.aprobado = 1 AND RPE.personaId = ". $idPersona);
		
		if(!empty($res))
			return $res;

		return array();
	}
	/*
	public function getEventosJuezPrincipal($idPersona)
	{
		$res = $this->db->select("SELECT * FROM eventos WHERE juezPrincipal = " . $idPersona. " AND eventoFinalizado = 0");
		
		if(!empty($res))
			return $res;

		return array();
	}

	public function getEventosJuezSuplente($idPersona)
	{
		$res = $this->db->select("SELECT * FROM eventos WHERE juezSuplente = " . $idPersona. " AND eventoFinalizado = 0");
		
		if(!empty($res))
			return $res;

		return array();
	}


	public function getEventosJuezActivos($idPersona)
	{
		$res = $this->db->select("SELECT * FROM eventos WHERE juezSuplente = " . $idPersona." OR juezPrincipal = ". $idPersona . " AND eventoFinalizado = 0");
		
		if(!empty($res))
			return $res;

		return array();
	}

	public function getEventosJuezFinalizados($idPersona)
	{
		$res = $this->db->select("SELECT * FROM eventos WHERE juezSuplente = " . $idPersona." OR juezPrincipal = ". $idPersona . " AND eventoFinalizado = 1");
		
		if(!empty($res))
			return $res;

		return array();
	}
	*/
	public function setPuntajesFinales(array $data)
	{
		/*
		ini_set('max_execution_time', 0); 
		$start = time();

		$keysReg = array();
		$keysRes = array();

		foreach ($data as $keyA => $valueA)
		{
			$keysReg[] = $valueA->registroAtletaId;
			
			$dataSend = array();
			$dataSend['pos'] = $valueA->pos;
			$dataSend['puntaje'] = $valueA->puntaje;
			$dataSend['mediaPos'] = $valueA->mediaPos;

			$res = $this->db->update("registroatletas", $dataSend, array('id' => $valueA->registroAtletaId));
			

			foreach ($valueA->resultados as $keyR => $valueR)
			{
				if(!$valueR->valid)
					continue;

				$keysRes[] = $valueR->id;

				
				$dataSend2 = array();
				$dataSend2['pos'] = $valueR->pos;

				$res = $this->db->update("resultados", $dataSend2, array('id' => $valueR->id));
				

			}
		}

		$finish = time();

		print_r($finish - $start);
		return;
		*/
		//$this->db->raw("UPDATE registroatletas SET pos = 0, puntaje = 0, mediaPos = 0 WHERE id IN (".implode(", ", array_values($keysReg)).")");
		//$this->db->raw("UPDATE registroatletas SET pos = 0, puntaje = 0, mediaPos = 0 WHERE id IN (".implode(", ", array_values($keysRes)).")");

	}

	public function setPuntajesFinalesWeight(array $data)
	{
		/*
		foreach ($data as $keyA => $valueA)
		{
			$dataSend = array();
			$dataSend['pos'] = $valueA->pos;
			$dataSend['average'] = $valueA->average;
			$dataSend['total'] = $valueA->total;

			$res = $this->db->update("registroatletas", $dataSend, array('id' => $valueA->registroAtletaId));

			foreach ($valueA->resultados as $keyR => $valueR)
			{
				for($i=1; $i<=3; $i++)
				{
					if(!$valueR[$i]->valid)
						continue;

					$dataSend2 = array();
					$dataSend2['pos'] = $valueR[$i]->pos;
					$dataSend2['mayor'] = $valueR[$i]->mayor;

					$res = $this->db->update("resultados", $dataSend2, array('id' => $valueR[$i]->id));
				}

			}
		}
		*/
	}

	public function normalizarPeso($objeto)
	{
		$ktl = 2.2046;

		if($objeto->valor < 0)
		{
			$objeto->tipo = 1;
			$objeto->kg = $objeto->valor / $ktl * -1;
			$objeto->lb = $objeto->valor * -1;
		}
		else
		{
			$objeto->tipo = 0;
			$objeto->kg = $objeto->valor;
			$objeto->lb = $objeto->valor * $ktl;
		}

		$objeto->kgStr = sprintf("%'0.2f Kg", $objeto->kg);
		$objeto->lbStr = sprintf("%'0.2f Lb", $objeto->lb);
		$objeto->str = $objeto->kgStr." / ".$objeto->lbStr;
	}

	public function normalizarTiempos($objeto)
	{
		$mili = 0;
		$seg = 0;
		$min = 0;
		$hor = 0;
		$tiempo = $objeto->valor;

		$mili = $tiempo % 1000;
		$tiempo = ($tiempo - $mili) / 1000;
		$seg = $tiempo % 60;
		$tiempo = ($tiempo - $seg) / 60;
		$min = $tiempo % 60;
		$hor = ($tiempo - $min) / 60;

		$objeto->H = $hor;
		$objeto->M = $min;
		$objeto->S = $seg;
		$objeto->str = sprintf("%'02d", $hor).":".sprintf("%'02d", $min).":".sprintf("%'02d", $seg);
	}

	public static function tiempoValor($H, $M, $S)
	{
		$res = 0;

		if(is_numeric($S) && ($S >= 0))
		{
			if($S >= 60)
			{
				$M += floor($S / 60);
				$S = ($S % 60);
			}

			$res += ($S);
		}

		if(is_numeric($M) && ($M >= 0))
		{
			if($M >= 60)
			{
				$H += floor($M / 60);
				$M = ($M % 60);
			}

			$res += ($M * 60);
		}

		if(is_numeric($H) && ($H >= 0))
		{
			$res += (3600 * $H);
		}
		
		$res = $res * 1000;

		return $res;
	}

	public static function pesoNormal($peso, $tipo)
	{
		$res = 0;

		if(is_numeric($tipo) && ($tipo >= 0) && ($tipo <= 1))
		{
			if(is_numeric($peso) && ($peso >= 0))
			{
				if($tipo == 1)
					$res = 0 - $peso;
				else
					$res = $peso;
			}
		}

		return $res;
	}

	public function prepararCuentas($personas = array())
	{
		foreach ($personas as $key => &$value)
		{
			self::prepararCuenta($value);
		}
		unset($value);
	}

	public function prepararCuenta($persona)
	{
		if(!isset($persona) || empty($persona))
			return;

		$ktl = 2.2046;

		$persona->wkcleanObj = new \stdClass();
		$persona->wkcleanObj->valor = $persona->wkclean;
		self::normalizarPeso($persona->wkcleanObj);
		unset($persona->wkclean);

		$persona->wksnatchObj = new \stdClass();
		$persona->wksnatchObj->valor = $persona->wksnatch;
		self::normalizarPeso($persona->wksnatchObj);
		unset($persona->wksnatch);

		$persona->wkdeadObj = new \stdClass();
		$persona->wkdeadObj->valor = $persona->wkdead;
		self::normalizarPeso($persona->wkdeadObj);
		unset($persona->wkdead);

		$persona->wkbacksquatObj = new \stdClass();
		$persona->wkbacksquatObj->valor = $persona->wkbacksquat;
		self::normalizarPeso($persona->wkbacksquatObj);
		unset($persona->wkbacksquat);

		$persona->wkfranObj = new \stdClass();
		$persona->wkfranObj->valor = $persona->wkfran;
		self::normalizarTiempos($persona->wkfranObj);
		unset($persona->wkfran);

		$persona->wkisabelObj = new \stdClass();
		$persona->wkisabelObj->valor = $persona->wkisabel;
		self::normalizarTiempos($persona->wkisabelObj);
		unset($persona->wkisabel);

		$persona->wkgraceObj = new \stdClass();
		$persona->wkgraceObj->valor = $persona->wkgrace;
		self::normalizarTiempos($persona->wkgraceObj);
		unset($persona->wkgrace);

		$persona->wkrun5kObj = new \stdClass();
		$persona->wkrun5kObj->valor = $persona->wkrun5k;
		self::normalizarTiempos($persona->wkrun5kObj);
		unset($persona->wkrun5k);

		$persona->wkcindyObj = new \stdClass();
		$persona->wkcindyObj->valor = $persona->wkcindy;
		self::normalizarTiempos($persona->wkcindyObj);
		unset($persona->wkcindy);

		if($persona->fechaNacimiento)
			$persona->nacimientoStr = date('d/m/Y',strtotime($persona->fechaNacimiento));

		if(strcmp($persona->sexo,"M") == 0)
			$persona->sexoStr = "Masculino";
		else if(strcmp($persona->sexo, "F") == 0)
			$persona->sexoStr = "Femenino";
		else
			$persona->sexoStr = "N/E";

		if($persona->archivoFoto && file_exists("./".PROFILEPICTUREPATH.$persona->archivoFoto))
			$persona->fotoPath = DIR.PROFILEPICTUREPATH.$persona->archivoFoto;
		else
			$persona->fotoPath = DIR.PROFILEPICTUREPATH."default.jpg";
	}
}

?>