<?php
namespace Models;

use Core\Model;
use Helpers\Codes;

class Eventos extends Model
{
	function __construct()
	{
		parent::__construct();
	}

	public function prepararEventos($eventos = array())
	{
		foreach ($eventos as $key => $value)
		{
			self::prepararEvento($value);
		}
	}

	public function prepararEvento($evento)
	{
		if(!isset($evento) || empty($evento))
			return;

		self::setCustomURL($evento);

		if($evento->zonaHoraria < 0)
			$fecha = $evento->fecha + $evento->zonaHoraria;
		else
			$fecha = $evento->fecha - $evento->zonaHoraria;

		$signo = $evento->zonaHoraria < 0 ? '-' : '+';
		$evento->fechaStr = date("d/m/Y @ H:i", $fecha) ." (GMT ".$signo.date("H:i", abs($evento->zonaHoraria)).")";
		$evento->fechaHtml = "<i class=\"fa fa-calendar\" aria-hidden=\"true\"></i> ".date("d/m/Y", $fecha)." <i class=\"fa fa-clock-o\" aria-hidden=\"true\"></i> ".date("H:i", $fecha)." (GMT ".$signo.date("H:i", abs($evento->zonaHoraria)).")";

		if ($evento->archivoFoto && file_exists("./".EVENTPICTUREPATH.$evento->archivoFoto))
			$evento->fotoPath = DIR.EVENTPICTUREPATH.$evento->archivoFoto;
		else
			$evento->fotoPath = DIR.EVENTPICTUREPATH."default.jpg";

		$evento->disciplinaStr = Codes::getEventoNombre($evento->disciplina);

		$evento->eventoTeam = false;
		$evento->registroTeam = false;
		$evento->openTeam = false;

		if($evento->disciplina != 10 && (strcmp($evento->tipoRegistro,'E') == 0 ))
			$evento->registroTeam = true;

		if($evento->disciplina == 10 && (strcmp($evento->tipoRegistro, 'E') == 0) )
			$evento->openTeam = true;

		if(strcmp($evento->tipoRegistro,'E') == 0 )
		{
			$evento->eventoTeam = true;
			$evento->disciplinaStr .= " (<i class=\"fa fa-users\"></i>)";
		}
	}

	private function setCustomURL($evento)
	{
		if(!isset($evento) || empty($evento))
			return;

		if(!empty($evento->customurl))
			$evento->internalURL = $evento->customurl;
		else
			$evento->internalURL = $evento->id;
	}

	private function setCustomsURLS($eventos = array())
	{
		foreach ($eventos as $keyE => $valueE)
		{
			self::setCustomURL($valueE);
		}
	}

	public function urlRegistrada($url)
	{
		$urlL = strtolower($url);
		$res = $this->db->select("SELECT * FROM eventos WHERE customurl = '".$urlL."'");

		if(empty($res))
			return false;
		else
			return $res[0];
	}

	public function deleteUrl($eventoId)
	{
		$this->db->raw("UPDATE eventos SET customurl = NULL WHERE id = " . $eventoId);
	}

	public function getEvento($eventoId)
	{
		if(is_numeric($eventoId))
			$res = $this->db->select("SELECT EV.*, PL.id AS idPlan, PL.nombre AS nombrePlan, PL.categorias AS categoriasPlan, PL.atletas AS atletasPlan, PL.patrocinadores as patrocinadoresPlan, YEAR(from_unixtime(EV.fecha)) AS anno, MONTH(from_unixtime(EV.fecha)) AS mes, DAY(from_unixtime(EV.fecha)) AS dia FROM eventos EV INNER JOIN planes PL ON (EV.tipoSubscripcion = PL.id) WHERE EV.id = ".$eventoId);
		else
			$res = $this->db->select("SELECT EV.*, PL.id AS idPlan, PL.nombre AS nombrePlan, PL.categorias AS categoriasPlan, PL.atletas AS atletasPlan, PL.patrocinadores as patrocinadoresPlan, YEAR(from_unixtime(EV.fecha)) AS anno, MONTH(from_unixtime(EV.fecha)) AS mes, DAY(from_unixtime(EV.fecha)) AS dia FROM eventos EV INNER JOIN planes PL ON (EV.tipoSubscripcion = PL.id) WHERE EV.customurl = '".$eventoId."'");
		
		if (!empty($res))
			return $res[0];
		else
			return false;
	}

	public function getEventosCerrar()
	{
		$ayer = time() - EVENT_NORMAL_LIFE;
		$res = $this->db->select("SELECT * FROM eventos WHERE fecha <= '".$ayer."' AND eventoFinalizado = 0 AND disciplina <> 10 AND creacionFinalizada = 1");
		return $res;
	}

	public function getEventosOpenCerrar()
	{
		$ayer = time() - EVENT_OPEN_LIFE;
		$res = $this->db->select("SELECT * FROM eventos WHERE fecha <= '".$ayer."' AND eventoFinalizado = 0 AND disciplina = 10 AND creacionFinalizada = 1");
		return $res;
	}

	public function getEventosProximos()
	{
		$presente = time();
		$res = $this->db->select("SELECT *, YEAR(from_unixtime(fecha)) as anno, MONTH(from_unixtime(fecha)) as mes, DAY(from_unixtime(fecha)) as dia FROM eventos WHERE fecha >= '".$presente."' AND eventoFinalizado = 0  AND aprobado = 1");
		return $res;
	}

	public function getEventosEnCurso()
	{
		$presente = time();
		$res = $this->db->select("SELECT *, YEAR(from_unixtime(fecha)) as anno, MONTH(from_unixtime(fecha)) as mes, DAY(from_unixtime(fecha)) as dia FROM eventos WHERE fecha <= '".$presente."' AND eventoFinalizado = 0  AND aprobado = 1");
		return $res;
	}

	public function getEventosActivosRegistro()
	{
		$presente = time() + EVENT_VENTANA_REGISTRO; //1 hora despues de iniciado el evento como maximo
		$res = $this->db->select("SELECT *, YEAR(from_unixtime(fecha)) as anno, MONTH(from_unixtime(fecha)) as mes, DAY(from_unixtime(fecha)) as dia FROM eventos WHERE fecha >= '".$presente."' AND eventoFinalizado = 0 AND aprobado = 1 AND permiteRegistros = 1 AND creacionFinalizada = 1");
		return $res;
	}

	public function getEventosAprobados()
	{
		$res = $this->db->select("SELECT *, YEAR(from_unixtime(fecha)) as anno, MONTH(from_unixtime(fecha)) as mes, DAY(from_unixtime(fecha)) as dia FROM eventos WHERE aprobado = 1 AND creacionFinalizada = 1");
		return $res;
	}

	public function getWODs($eventoId)
	{
		$res = $this->db->select("SELECT * FROM wods WHERE eventoId =".$eventoId." ORDER BY id ASC");
		$wods = array();

		foreach ($res as $keyW => $valueW)
		{
			$wods[$valueW->id] = $valueW;
		}

		return $wods;
	}

	public function getWOD($eventoId, $wodId)
	{
		$res = $this->db->select("SELECT * FROM wods WHERE id = ".$wodId." AND eventoId =".$eventoId." ORDER BY id ASC");

		if(!empty($res))
		{
			return $res[0];
		}

		return false;
	}

	public function getCategorias($eventoId)
	{
		$res = $this->db->select("SELECT * FROM categorias WHERE eventoId = ".$eventoId);

		$categorias = array();

		foreach ($res as $keyC => $valueC)
		{
			$categorias[$valueC->id] = $valueC;
		}

		return $categorias;
	}

	public function getCategoria($eventoId, $categoriaId)
	{
		if(is_null($eventoId) || is_null($categoriaId))
			return false;

		$res = $this->db->select("SELECT * FROM categorias WHERE id = ".$categoriaId." AND eventoId = ".$eventoId);   

		if(!empty($res))
			return $res[0];

		return false;
	}

	public function getWodsCategoria($eventoId, $categoriaId)
	{
		$res = $this->db->select("SELECT W.*, RWC.orden FROM wods W INNER JOIN relwodcategoria RWC ON RWC.wodId = W.id WHERE RWC.categoriaId = ".$categoriaId." AND RWC.eventoId = ".$eventoId);
		$wods = array();

		foreach ($res as $keyC => &$valueC)
		{
			$wods[$valueC->id] = $valueC;
		}
		unset($valueC);

		return $wods;
	}

	public function agregarBox(array $datos)
	{
		$res = $this->db->insert("boxes", $datos);
		return $res;
	}

	public function actualizarBox($data, $boxId)
	{
		$where = array('id' => $boxId);
		$res = $this->db->update("boxes", $data, $where);
		return $res;
	}

	public function actualizarEquipo($data, $equipoId)
	{
		$where = array('id' => $equipoId);
		$res = $this->db->update("equipos", $data, $where);
		return $res;
	}

	public function borrarBox($boxId)
	{
		$where = array('id' => $boxId);
		$res1 = $this->db->delete("boxes", $where, 'a');
		$res2 = $this->db->update("personas", array('boxId' => null), array('boxId' => $boxId));
		
		return ($res1 && $res2);
	}

	public function getBox($boxId)
	{
		$res = $this->db->select("SELECT `bo`.`id` AS `id`, `bo`.`nombre` AS `nombre`, `bo`.`logoBox` AS `logoBox`, `bo`.`urlBox` AS `urlBox`, `bo`.`coach` AS `coach`, `bo`.`pais` AS `pais`, `bo`.`creadorId` AS `creadorId`, `bo`.`aprobado` AS `aprobado`, `bo`.`fechaRegistro` AS `fechaRegistro`, `pa`.`country_name` AS `paisStr` FROM (`boxes` `bo` LEFT JOIN `paises` `pa` ON ((`bo`.`pais` = `pa`.`id`))) WHERE bo.id = " . $boxId);		

		if(empty($res))
			return false;
		else
			return $res[0];
	}

	public function getRelacionWODCategoria($eventoId)
	{
		$res = $this->db->select("SELECT * FROM relwodcategoria WHERE eventoId = ".$eventoId." ORDER BY categoriaId, wodId");
		$relaciones = array();

		foreach ($res as $keyR => $valueR)
		{
			$relaciones[] = $valueR;
		}

		return $relaciones;
	}


	public function getAtletasRegistrados($eventoId)
	{
		$res = $this->db->select("SELECT PER.sexo, PER.nombre, PER.apellido, PER.ident, PER.archivoFoto, PER.pais, PER.estado, PER.fechaNacimiento, PER.correo, PER.telefono_1, PER.boxId as boxId, REG.* FROM registroatletas REG INNER JOIN personas PER ON (PER.id = REG.personaId) WHERE REG.eventoId = ".$eventoId." AND REG.aprobado = 1 ORDER BY PER.nombre ASC");
		$atletas = array();

		foreach ($res as $keyA => $valueA)
		{
			$atletas[$valueA->id] = $valueA;
		}

		return $atletas;
	}

	public function getEquiposRegistrados($eventoId)
	{
		$res = $this->db->select("SELECT EQ.nombre, REG.* FROM registroequipos REG INNER JOIN equipos EQ ON (EQ.id = REG.equipoId) WHERE REG.aprobado = 1 And REG.eventoId = ".$eventoId." ORDER BY EQ.nombre ASC");
		$atletas = array();

		foreach ($res as $keyA => $valueA)
		{
			$atletas[$valueA->id] = $valueA;
		}

		return $atletas;
	}

	public function getAtletasPendientes($eventoId)
	{
		$res = $this->db->select("SELECT ra.personaId, `per`.`id` AS `idPersona`, `per`.`nombre` AS `nombre`, `per`.`apellido` AS `apellido`, `per`.`pais` AS `pais`, `per`.`correo` AS `correo`, `per`.`telefono_1` AS `telefono_1`, `per`.`telefono_2` AS `telefono_2`, `per`.`contrasena` AS `contrasena`, `per`.`subscripcionPlan` AS `subscripcionPlan`, `per`.`archivoFoto` AS `archivoFoto`, `per`.`esAdmin` AS `esAdmin`, `per`.`boxId` AS `boxId`, `per`.`estado` AS `estado`, `per`.`fechaNacimiento` AS `fechaNacimiento`, `per`.`alias` AS `alias`, `per`.`wkclean` AS `wkclean`, `per`.`wksnatch` AS `wksnatch`, `per`.`wkdead` AS `wkdead`, `per`.`wkbacksquat` AS `wkbacksquat`, `per`.`wkfran` AS `wkfran`, `per`.`wkisabel` AS `wkisabel`, `per`.`wkgrace` AS `wkgrace`, `per`.`wkrun5k` AS `wkrun5k`, `per`.`wkcindy` AS `wkcindy`, `per`.`resetCode` AS `resetCode`, `per`.`pic` AS `pic`, `per`.`picId` AS `picId`, `per`.`fechaRegistro` AS `fechaRegistro`, `per`.`ident` AS `ident`, `per`.`cc` AS `cc`, `pa`.`country_name` AS `paisStr`, `bo`.`nombre` AS `boxStr`, `bo`.`id` AS `boxId`, `pl`.`nombre` AS `plan`, `pl`.`atletas` AS `atletas`, `pl`.`categorias` AS `categorias`, `pl`.`patrocinadores` AS `patrocinadores`,`ra`.`id` AS `idRegistro`,`ra`.`pagoId` AS `pagoId`,`ra`.`eventoId` AS `eventoId`,`ra`.`categoriaId` AS `categoriaId`,`ra`.`aprobado` AS `aprobado`,`ra`.`pos` AS `pos`,`ra`.`puntaje` AS `puntaje`,`ra`.`mediaPos` AS `mediaPos`,`ra`.`bodyweight` AS `bodyweight`,`ra`.`average` AS `average`,`ra`.`total` AS `total`  FROM (`personas` `per` LEFT JOIN `paises` `pa` ON `pa`.`id` = `per`.`pais` LEFT JOIN `planes` `pl` ON ((`pl`.`id` = `per`.`subscripcionPlan`)) LEFT JOIN `boxes` `bo` ON ((`bo`.`id` = `per`.`boxId`)) INNER JOIN registroatletas ra ON (ra.personaId = per.id)) WHERE ra.aprobado = 0 AND ra.eventoId = ".$eventoId." ORDER BY per.nombre ASC");
		$atletas = array();

		foreach ($res as $keyA => $valueA)
		{
			$atletas[$valueA->idRegistro] = $valueA;
		}

		return $atletas;
	}

	public function getEquiposPendientes($eventoId)
	{
		$res = $this->db->select("SELECT EQ.id, EQ.nombre, EQ.capitanId, EQ.fechaRegistro, EQ.archivoFoto, REQ.id as registroId, REQ.pagoId, REQ.eventoId, REQ.categoriaId, REQ.aprobado FROM equipos EQ INNER JOIN registroequipos REQ ON (REQ.equipoId = EQ.id) WHERE REQ.aprobado = 0 AND REQ.eventoId = ".$eventoId." ORDER BY EQ.nombre ASC");
		$atletas = array();

		foreach ($res as $keyA => $valueA)
		{
			$atletas[$valueA->registroId] = $valueA;
		}

		return $atletas;
	}

	public function getAtletasCategoria($eventoId, $categoriaId)
	{
		$res = $this->db->select("SELECT `ra`.`personaId`, `per`.`id` AS `idPersona`, `per`.`nombre` AS `nombre`, `per`.`apellido` AS `apellido`, `per`.`pais` AS `pais`, `per`.`correo` AS `correo`, `per`.`telefono_1` AS `telefono_1`, `per`.`telefono_2` AS `telefono_2`, `per`.`contrasena` AS `contrasena`, `per`.`subscripcionPlan` AS `subscripcionPlan`, `per`.`archivoFoto` AS `archivoFoto`, `per`.`esAdmin` AS `esAdmin`, `per`.`boxId` AS `boxId`, `per`.`estado` AS `estado`, `per`.`fechaNacimiento` AS `fechaNacimiento`, `per`.`alias` AS `alias`, `per`.`wkclean` AS `wkclean`, `per`.`wksnatch` AS `wksnatch`, `per`.`wkdead` AS `wkdead`, `per`.`wkbacksquat` AS `wkbacksquat`, `per`.`wkfran` AS `wkfran`, `per`.`wkisabel` AS `wkisabel`, `per`.`wkgrace` AS `wkgrace`, `per`.`wkrun5k` AS `wkrun5k`, `per`.`wkcindy` AS `wkcindy`, `per`.`resetCode` AS `resetCode`, `per`.`pic` AS `pic`, `per`.`picId` AS `picId`, `per`.`fechaRegistro` AS `fechaRegistro`, `per`.`ident` AS `ident`, `per`.`cc` AS `cc`, `pa`.`country_name` AS `paisStr`, `bo`.`nombre` AS `boxStr`, `bo`.`id` AS `boxId`, `pl`.`nombre` AS `plan`, `pl`.`atletas` AS `atletas`, `pl`.`categorias` AS `categorias`, `pl`.`patrocinadores` AS `patrocinadores`,`ra`.`id` AS `idRegistro`,`ra`.`pagoId` AS `pagoId`,`ra`.`eventoId` AS `eventoId`,`ra`.`categoriaId` AS `categoriaId`,`ra`.`aprobado` AS `aprobado`,`ra`.`pos` AS `pos`,`ra`.`puntaje` AS `puntaje`,`ra`.`mediaPos` AS `mediaPos`,`ra`.`bodyweight` AS `bodyweight`,`ra`.`average` AS `average`,`ra`.`total` AS `total`  FROM (`personas` `per` LEFT JOIN `paises` `pa` ON `pa`.`id` = `per`.`pais` LEFT JOIN `planes` `pl` ON ((`pl`.`id` = `per`.`subscripcionPlan`)) LEFT JOIN `boxes` `bo` ON ((`bo`.`id` = `per`.`boxId`)) INNER JOIN registroatletas ra ON (ra.personaId = per.id)) WHERE (ra.aprobado = 1) AND (ra.categoriaId = ".$categoriaId.") AND (ra.eventoId = ".$eventoId.") ORDER BY per.nombre;");
		$atletas = array();

		foreach ($res as $keyA => $valueA)
		{
			$atletas[$valueA->idRegistro] = $valueA;
		}

		return $atletas;
	}

	public function getAtletaEquipado($eventoId, $personaId)
	{
		$res = $this->db->select("SELECT PER.*, RA.id AS idRegistro, EQ.id AS equipoId, EQ.nombre AS equipoStr, EQ.capitanId AS capitanId, RPE.aprobado AS aprobado FROM personas PER INNER JOIN relpersonaequipo RPE ON (RPE.personaId = PER.id) INNER JOIN equipos EQ ON (EQ.id = RPE.equipoId) INNER JOIN eventos EV ON (EQ.eventoId = EV.id) INNER JOIN registroatletas RA ON (RA.personaId = RPE.personaId AND RA.eventoId = EQ.eventoId) WHERE EQ.dedicado = 1 AND EV.id = ".$eventoId." AND PER.id = ".$personaId.";");
		
		if(empty($res))
			return false;
		else
			return $res[0];
	}

	public function getAtletasEquipados($eventoId, $categoriaId)
	{
		$res = $this->db->select("SELECT PER.*, RA.id as idRegistro, EQ.id AS equipoId, EQ.nombre AS equipoStr, RPE.aprobado AS aprobado FROM personas PER INNER JOIN relpersonaequipo RPE ON (RPE.personaId = PER.id) INNER JOIN equipos EQ ON (EQ.id = RPE.equipoId) INNER JOIN eventos EV ON (EQ.eventoId = EV.id) INNER JOIN registroatletas RA ON (RA.personaId = RPE.personaId AND RA.eventoId = EQ.eventoId) WHERE EQ.dedicado = 1 AND EV.id = ".$eventoId." AND RA.categoriaId = ".$categoriaId.";");
		$atletas = array();

		foreach ($res as $keyA => $valueA)
		{
			$atletas[$valueA->idRegistro] = $valueA;
		}

		return $atletas;
	}

	public function getEquiposCategoria($eventoId, $categoriaId)
	{
		$res = $this->db->select("SELECT EQ.*, REE.id as idRegistro FROM registroequipos REE INNER JOIN equipos EQ ON (EQ.id = REE.equipoId) WHERE REE.aprobado = 1 AND REE.categoriaId = $categoriaId AND REE.eventoId = $eventoId ORDER BY EQ.nombre;");
		$atletas = array();

		foreach ($res as $keyA => $valueA)
		{
			$atletas[$valueA->id] = $valueA;
		}

		return $atletas;
	}

	public function getEquiposEventoCategoria($eventoId, $categoriaId)
	{
		$res = $this->db->select("ELECT EQ.*, REE.id as idRegistro FROM registroequipos REE INNER JOIN equipos EQ ON (EQ.id = REE.equipoId) WHERE REE.categoriaId = $categoriaId AND REE.eventoId = $eventoId;");
		$equipos = array();

		foreach ($res as $keyA => $valueA)
		{
			$equipos[$valueA->id] = $valueA;
		}

		return $equipos;
	}

	public function getIntegrantesEquipoEvento($eventoId, $categoriaId)
	{
		$res = $this->db->select("SELECT `ra`.`personaId`, `per`.`id` AS `idPersona`, `per`.`nombre` AS `nombre`, `per`.`apellido` AS `apellido`, `per`.`pais` AS `pais`, `per`.`correo` AS `correo`, `per`.`telefono_1` AS `telefono_1`, `per`.`telefono_2` AS `telefono_2`, `per`.`contrasena` AS `contrasena`, `per`.`subscripcionPlan` AS `subscripcionPlan`, `per`.`archivoFoto` AS `archivoFoto`, `per`.`esAdmin` AS `esAdmin`, `per`.`boxId` AS `boxId`, `per`.`estado` AS `estado`, `per`.`fechaNacimiento` AS `fechaNacimiento`, `per`.`alias` AS `alias`, `per`.`wkclean` AS `wkclean`, `per`.`wksnatch` AS `wksnatch`, `per`.`wkdead` AS `wkdead`, `per`.`wkbacksquat` AS `wkbacksquat`, `per`.`wkfran` AS `wkfran`, `per`.`wkisabel` AS `wkisabel`, `per`.`wkgrace` AS `wkgrace`, `per`.`wkrun5k` AS `wkrun5k`, `per`.`wkcindy` AS `wkcindy`, `per`.`resetCode` AS `resetCode`, `per`.`pic` AS `pic`, `per`.`picId` AS `picId`, `per`.`fechaRegistro` AS `fechaRegistro`, `per`.`ident` AS `ident`, `per`.`cc` AS `cc`, `pa`.`country_name` AS `paisStr`, `bo`.`nombre` AS `boxStr`, `bo`.`id` AS `boxId`, `pl`.`nombre` AS `plan`, `pl`.`atletas` AS `atletas`, `pl`.`categorias` AS `categorias`, `pl`.`patrocinadores` AS `patrocinadores`, `ra`.`id` AS `idRegistro`, `ra`.`pagoId` AS `pagoId`, `ra`.`eventoId` AS `eventoId`, `ra`.`categoriaId` AS `categoriaId`, `ra`.`aprobado` AS `aprobado`, `ra`.`pos` AS `pos`, `ra`.`puntaje` AS `puntaje`, `ra`.`mediaPos` AS `mediaPos`, `ra`.`bodyweight` AS `bodyweight`, `ra`.`average` AS `average`, `ra`.`total` AS `total`, `EQ`.`id` AS `equipoId` FROM `personas` `per` LEFT JOIN `paises` `pa` ON `pa`.`id` = `per`.`pais` LEFT JOIN `planes` `pl` ON (`pl`.`id` = `per`.`subscripcionPlan`) LEFT JOIN `boxes` `bo` ON (`bo`.`id` = `per`.`boxId`) INNER JOIN registroatletas ra ON (ra.personaId = per.id) INNER JOIN equipos EQ ON (ra.eventoId = EQ.eventoId) INNER JOIN relregistroatletasequipo RAE ON (RAE.equipoId = EQ.id) WHERE ra.categoriaId = ".$categoriaId." AND ra.eventoId = ".$eventoId." AND RAE.personaId = ra.personaId AND EQ.dedicado = 1;");
		
		$equipos = array();

		foreach ($res as $keyA => $valueA)
		{
			$equipos[$valueA->idRegistro] = $valueA;
		}

		return $equipos;
	}

	public function getResultados($eventoId)
	{
		$res = $this->db->select("SELECT * FROM resultados WHERE eventoId = ".$eventoId." ORDER BY wodId ASC");
		$tabla = array();

		foreach ($res as $keyR => $valueR)
		{
			$valueR->valid = true;
			$tabla[$valueR->registroAtletaId][$valueR->wodId] = $valueR;
		}
		return $tabla;
	}

	public function getResultadosWeight($eventoId)
	{
		$res = $this->db->select("SELECT * FROM resultados WHERE eventoId = ".$eventoId." ORDER BY wodId ASC");        
		$tabla = array();

		foreach ($res as $keyR => $valueR)
		{
			$valueR->valid = true;
			$tabla[$valueR->registroAtletaId][$valueR->wodId][$valueR->attempt] = $valueR;
		}
		return $tabla;
	}

	public function getResultadosCategoria($eventoId, $categoriaId)
	{
		$res = $this->db->select("SELECT RES.*, EV.disciplina FROM resultados RES INNER JOIN registroatletas RA ON (RES.registroAtletaId = RA.id) INNER JOIN wods W ON (RES.wodId = W.id) INNER JOIN eventos EV ON (EV.id = RA.eventoId) WHERE (RA.categoriaId = ".$categoriaId.") AND (RA.eventoId = ".$eventoId.") AND (RES.eventoId = EV.id) ORDER BY RES.wodId ASC");
		$tabla = array();

		foreach ($res as $keyR => $valueR)
		{
			$valueR->valid = true;

			if($valueR->disciplina == 9)
				$tabla[$valueR->registroAtletaId][$valueR->wodId][$valueR->attempt] = $valueR;
			else
				$tabla[$valueR->registroAtletaId][$valueR->wodId] = $valueR;
		}
		return $tabla;
	}

	public function getResultadosEquipoCategoria($eventoId, $categoriaId)
	{
		$res = $this->db->select("SELECT RES.*, RA.equipoId, EV.disciplina FROM resultados RES INNER JOIN registroequipos RA ON (RES.registroAtletaId = RA.id) INNER JOIN wods W ON (RES.wodId = W.id) INNER JOIN eventos EV ON (EV.id = RA.eventoId) WHERE (RA.categoriaId = ".$categoriaId.") AND (RA.eventoId = ".$eventoId.") AND (RES.eventoId = EV.id) ORDER BY RES.wodId ASC");
		$tabla = array();

		foreach ($res as $keyR => $valueR)
		{
			$valueR->valid = true;

			if($valueR->disciplina == 9)
				$tabla[$valueR->equipoId][$valueR->wodId][$valueR->attempt] = $valueR;
			else
				$tabla[$valueR->equipoId][$valueR->wodId] = $valueR;
		}
		return $tabla;
	}

	public function getResultadosEquipoEventoCategoria($eventoId, $categoriaId)
	{
		/*
		SELECT RPE.equipoId, RPE.aprobado, RA.* FROM registroatletas RA INNER JOIN relpersonaequipo RPE ON (RPE.personaId = RA.personaId);
		WHERE (RPE.equipoEvento = 1) AND RA.eventoId = 71;
		*/

		
		$res = $this->db->select("SELECT RES.*, RPE.equipoId, RPE.aprobado, RA.* FROM registroatletas RA INNER JOIN relpersonaequipo RPE ON (RPE.personaId = RA.personaId) INNER JOIN resultados RES ON (RES.registroAtletaId = RA.id) WHERE (RPE.equipoEvento = 1) AND RA.eventoId = ".$eventoId." AND RA.categoriaId = ".$categoriaId." ORDER BY RES.wodId ASC;");
		$tabla = array();

		foreach ($res as $keyR => $valueR)
		{
			$valueR->valid = true;
			$tabla[$valueR->equipoId][$valueR->wodId][$valueR->registroAtletaId] = $valueR;
		}
		return $tabla;
	}

	public function getResultadosWOD($eventoId, $wodId)
	{
		$res = $this->db->select("SELECT * FROM resultados WHERE eventoId = ".$eventoId." AND wodId = ".$wodId." ORDER BY wodId ASC");
		$tabla = array();

		foreach ($res as $keyR => $valueR)
		{
			$valueR->valid = true;
			$tabla[$valueR->registroAtletaId][$valueR->wodId][$valueR->attempt] = $valueR;
		}
		return $tabla;
	}

	public function crearEvento($data)
	{
		$res = $this->db->insert("eventos", $data);
		return $res;
	}

	public function agregarCategorias($data)
	{
		$res = $this->db->insert("categorias", $data);
		return $res;
	}

	public function actualizarCategoria($categoriaId, $data)
	{
		$where = array('id' => $categoriaId);
		$res = $this->db->update("categorias", $data, $where);
		return $res;
	}

	public function terminarEvento($eventoId)
	{
		$where = array('id' => $eventoId);
		$res = $this->db->update("eventos", array('eventoFinalizado' => 1), $where);
		return $res;
	}

	public function abrirEvento($eventoId)
	{
		$where = array('id' => $eventoId);
		$res = $this->db->update("eventos", array('eventoFinalizado' => 0), $where);
		return $res;
	}

	public function registrarAtleta($data)
	{
		$res = $this->db->insert("registroatletas", $data);
		return $res;
	}

	public function borrarCategorias(array $categorias)
	{
		if(empty($categorias))
			return false;

		$strToDelete = implode(", ", array_keys($categorias));
		$registros = $this->db->select("SELECT id FROM registroatletas WHERE categoriaId IN (".$strToDelete.")");
		$arrRegistroAtletas = array();

		foreach ($registros as $key => $value)
		{
			$arrRegistroAtletas[$value->id] = array();
		}

		if(!empty($arrRegistroAtletas))
		{
			$registroAtletasStr = implode(", ", array_keys($arrRegistroAtletas));
			$this->db->raw("DELETE FROM resultados WHERE registroAtletaId IN (".$registroAtletasStr.")");
			$this->db->raw("UPDATE registroatletas SET categoriaId = '0' WHERE id IN (".$registroAtletasStr.")");
		}

		$this->db->raw("DELETE FROM relwodcategoria WHERE categoriaId IN (".$strToDelete.")");
		$this->db->raw("DELETE FROM categorias WHERE id IN (".$strToDelete.")");

		return true;
	}

	public function agregarWods($data)
	{
		$res = $this->db->insert("wods", $data);
		return $res;
	}

	public function actualizarWod($wodId, $data)
	{
		$where = array('id' => $wodId);
		$res = $this->db->update("wods", $data, $where);
		return $res;
	}

	public function borrarWods(array $wodIds)
	{
		if(empty($wodIds))
			return false;

		$wodsStr = implode(", ", array_keys($wodIds));
		$this->db->raw("DELETE FROM resultados WHERE wodId IN (".$wodsStr.")");
		$this->db->raw("DELETE FROM relwodcategoria WHERE wodId IN (".$wodsStr.")");
		$this->db->raw("DELETE FROM wods WHERE id IN (".$wodsStr.")");
		
		return true;
	}

	public function agregarRelaciones($data)
	{
		$res = $this->db->insert("relwodcategoria", $data);
		return $res;
	}

	public function actualizarRelaciones($data, $where)
	{
		$res = $this->db->update("relwodcategoria", $data, $where);
		return $res;
	}

	public function borrarRelacion($eventoId, $categoriaId, $wodId)
	{
		$relacion = $this->db->select("SELECT * FROM relwodcategoria WHERE eventoId = ".$eventoId." AND categoriaId = ".$categoriaId." AND wodId = ".$wodId)[0];
		$where = array('eventoId' => $eventoId, 'categoriaId' => $categoriaId, 'wodId' => $wodId);
		$res = $this->db->delete("relwodcategoria", $where);
	}

	public function borrarEvento($eventoId)
	{
		$evento = self::getEvento($eventoId);

		$where = array('eventoId' => $eventoId);
		$this->db->delete("categorias", $where, 'a');
		$this->db->delete("registroatletas", $where, 'a');
		$this->db->delete("patrocinadores", $where, 'a');
		$this->db->delete("relwodcategoria", $where, 'a');
		$this->db->delete("resultados", $where, 'a');
		$this->db->delete("wods", $where, 'a');
		$this->db->delete("eventos", array('id' => $eventoId), 'a');
		
		if($evento->archivoFoto && file_exists("./".EVENTPICTUREPATH.$evento->archivoFoto))
			unlink("./".EVENTPICTUREPATH.$evento->archivoFoto);
	}

	public function setPasoCreacion($eventoId, $paso)
	{
		$where = array('id' => $eventoId);
		$data = array('paso' => $paso);
		$res = $this->db->update("eventos", $data, $where);
		return $res;
	}

	public function finalizaCreacion($eventoId)
	{
		$where = array('id' => $eventoId);
		$data = array('creacionFinalizada' => true);
		$res = $this->db->update("eventos", $data, $where);
		return $res;
	}

	public function getProcesoPaso($eventoId)
	{
		$res = $this->db->select('SELECT creacionFinalizada, paso FROM eventos WHERE id = '.$eventoId);

		if(!empty($res))
		{
			$ev = $res[0];
			if(!$ev->creacionFinalizada)
				return $ev->paso;
			else
				return 0;
		}

		return null;
	}

	public function actualizarEventoInfo($data, $where)
	{
		$res = $this->db->update("eventos", $data, $where);
		return $res;
	}

	public function actualizarRegistro($registroId, $data)
	{
		$where = array('id' => $registroId);
		$res = $this->db->update("registroatletas", $data, $where);
		return $res;
	}

	public function actualizarRegistroEquipo($registroId, $data)
	{
		$where = array('id' => $registroId);
		$res = $this->db->update("registroequipos", $data, $where);
		return $res;
	}

	public function actualizarPeso($registroId, $data)
	{
		$where = array('id' => $registroId);
		$res = $this->db->update("registroatletas", $data, $where);
		return $res;
	}

	public function borrarRegistro($registroId)
	{
		$where = array('id' => $registroId);
		$res = $this->db->delete("registroatletas", $where);
		return $res;
	}

	public function borrarRegistroEquipo($registroId)
	{
		$res = $this->db->select("SELECT * FROM registroequipos WHERE id = ". $registroId);

		if(!empty($res))
		{
			foreach ($res as $key => $value)
			{
				$dat['eventoId'] = $value->eventoId;
				$dat['equipoId'] = $value->equipoId;
				$reso = $this->db->delete('relregistroatletasequipo', $dat, 'a');
			}
		}

		$where = array('id' => $registroId);
		$res = $this->db->delete("registroequipos", $where);
		return $res;
	}

	public function getJueces($eventoId)
	{
		$res = $this->db->select("SELECT P.* FROM eventos E, personas P WHERE (E.juezPrincipal = P.id OR E.juezSuplente = P.id) AND E.id = ". $eventoId);
		$jueces = array();

		foreach ($res as $key => $value)
		{
			$jueces[$value->id] = $value;
		}

		return $jueces;
	}

	public function getJuecesById(array &$juecesKeys)
	{
		if(empty($juecesKeys))
			return false;

		$strIDS = implode(",", array_keys($juecesKeys));

		$res = $this->db->select("SELECT `per`.`id` AS `idPersona`, `per`.`nombre` AS `nombre`, `per`.`apellido` AS `apellido`, `per`.`pais` AS `pais`, `per`.`correo` AS `correo`, `per`.`telefono_1` AS `telefono_1`, `per`.`telefono_2` AS `telefono_2`, `per`.`contrasena` AS `contrasena`, `per`.`subscripcionPlan` AS `subscripcionPlan`, `per`.`archivoFoto` AS `archivoFoto`, `per`.`esAdmin` AS `esAdmin`, `per`.`boxId` AS `boxId`, `per`.`estado` AS `estado`, `per`.`fechaNacimiento` AS `fechaNacimiento`, `per`.`alias` AS `alias`, `per`.`wkclean` AS `wkclean`, `per`.`wksnatch` AS `wksnatch`, `per`.`wkdead` AS `wkdead`, `per`.`wkbacksquat` AS `wkbacksquat`, `per`.`wkfran` AS `wkfran`, `per`.`wkisabel` AS `wkisabel`, `per`.`wkgrace` AS `wkgrace`, `per`.`wkrun5k` AS `wkrun5k`, `per`.`wkcindy` AS `wkcindy`, `per`.`resetCode` AS `resetCode`, `per`.`pic` AS `pic`, `per`.`picId` AS `picId`, `per`.`fechaRegistro` AS `fechaRegistro`, `per`.`ident` AS `ident`, `per`.`cc` AS `cc`, `pa`.`country_name` AS `paisStr`, `bo`.`nombre` AS `boxStr`, `bo`.`id` AS `boxId`, `pl`.`nombre` AS `plan`, `pl`.`atletas` AS `atletas`, `pl`.`categorias` AS `categorias`, `pl`.`patrocinadores` AS `patrocinadores` FROM (((`personas` `per` LEFT JOIN `paises` `pa` ON ((`pa`.`id` = `per`.`pais`))) LEFT JOIN `boxes` `bo` ON ((`bo`.`id` = `per`.`boxId`))) LEFT JOIN `planes` `pl` ON ((`pl`.`id` = `per`.`subscripcionPlan`))) WHERE per.id IN (".$strIDS.")");

		if(!empty($res))
		{
			foreach ($res as $key => $value)
			{
				$juecesKeys[$value->id] = $value;
			}

			return true;
		}

		return false;
	}

	public function agregarJuezPrincipal($eventoId, $personaId)
	{
		$preRES = $this->db->select("SELECT `per`.`id` AS `idPersona`, `per`.`nombre` AS `nombre`, `per`.`apellido` AS `apellido`, `per`.`pais` AS `pais`, `per`.`correo` AS `correo`, `per`.`telefono_1` AS `telefono_1`, `per`.`telefono_2` AS `telefono_2`, `per`.`contrasena` AS `contrasena`, `per`.`subscripcionPlan` AS `subscripcionPlan`, `per`.`archivoFoto` AS `archivoFoto`, `per`.`esAdmin` AS `esAdmin`, `per`.`boxId` AS `boxId`, `per`.`estado` AS `estado`, `per`.`fechaNacimiento` AS `fechaNacimiento`, `per`.`alias` AS `alias`, `per`.`wkclean` AS `wkclean`, `per`.`wksnatch` AS `wksnatch`, `per`.`wkdead` AS `wkdead`, `per`.`wkbacksquat` AS `wkbacksquat`, `per`.`wkfran` AS `wkfran`, `per`.`wkisabel` AS `wkisabel`, `per`.`wkgrace` AS `wkgrace`, `per`.`wkrun5k` AS `wkrun5k`, `per`.`wkcindy` AS `wkcindy`, `per`.`resetCode` AS `resetCode`, `per`.`pic` AS `pic`, `per`.`picId` AS `picId`, `per`.`fechaRegistro` AS `fechaRegistro`, `per`.`ident` AS `ident`, `per`.`cc` AS `cc`, `pa`.`country_name` AS `paisStr`, `bo`.`nombre` AS `boxStr`, `pl`.`nombre` AS `plan`, `pl`.`atletas` AS `atletas`, `pl`.`categorias` AS `categorias`, `pl`.`patrocinadores` AS `patrocinadores` FROM (((`personas` `per` LEFT JOIN `paises` `pa` ON ((`pa`.`id` = `per`.`pais`))) LEFT JOIN `boxes` `bo` ON ((`bo`.`id` = `per`.`boxId`))) LEFT JOIN `planes` `pl` ON ((`pl`.`id` = `per`.`subscripcionPlan`))) WHERE per.id = ". $personaId);

		if(!empty($preRES))
			$res = $this->db->update("eventos", array('juezPrincipal' => $personaId), array('id' => $eventoId));        
	}

	public function agregarJuezSuplente($eventoId, $personaId)
	{
		$preRES = $this->db->select("SELECT `per`.`id` AS `idPersona`, `per`.`nombre` AS `nombre`, `per`.`apellido` AS `apellido`, `per`.`pais` AS `pais`, `per`.`correo` AS `correo`, `per`.`telefono_1` AS `telefono_1`, `per`.`telefono_2` AS `telefono_2`, `per`.`contrasena` AS `contrasena`, `per`.`subscripcionPlan` AS `subscripcionPlan`, `per`.`archivoFoto` AS `archivoFoto`, `per`.`esAdmin` AS `esAdmin`, `per`.`boxId` AS `boxId`, `per`.`estado` AS `estado`, `per`.`fechaNacimiento` AS `fechaNacimiento`, `per`.`alias` AS `alias`, `per`.`wkclean` AS `wkclean`, `per`.`wksnatch` AS `wksnatch`, `per`.`wkdead` AS `wkdead`, `per`.`wkbacksquat` AS `wkbacksquat`, `per`.`wkfran` AS `wkfran`, `per`.`wkisabel` AS `wkisabel`, `per`.`wkgrace` AS `wkgrace`, `per`.`wkrun5k` AS `wkrun5k`, `per`.`wkcindy` AS `wkcindy`, `per`.`resetCode` AS `resetCode`, `per`.`pic` AS `pic`, `per`.`picId` AS `picId`, `per`.`fechaRegistro` AS `fechaRegistro`, `per`.`ident` AS `ident`, `per`.`cc` AS `cc`, `pa`.`country_name` AS `paisStr`, `bo`.`nombre` AS `boxStr`, `bo`.`id` AS `boxId`, `pl`.`nombre` AS `plan`, `pl`.`atletas` AS `atletas`, `pl`.`categorias` AS `categorias`, `pl`.`patrocinadores` AS `patrocinadores` FROM (((`personas` `per` LEFT JOIN `paises` `pa` ON ((`pa`.`id` = `per`.`pais`))) LEFT JOIN `boxes` `bo` ON ((`bo`.`id` = `per`.`boxId`))) LEFT JOIN `planes` `pl` ON ((`pl`.`id` = `per`.`subscripcionPlan`))) WHERE per.id = ". $personaId);

		if(!empty($preRES))
			$res = $this->db->update("eventos", array('juezSuplente' => $personaId), array('id' => $eventoId));
	}

	public function borrarJuezPrincipal($eventoId)
	{
		$res = $this->db->update("eventos", array('juezPrincipal' => null), array('id' => $eventoId));
	}

	public function borrarJuezSuplente($eventoId)
	{
		$res = $this->db->update("eventos", array('juezSuplente' => null), array('id' => $eventoId));
	}

	public function getPatrocinadores($eventoId)
	{
		$res = $this->db->select("SELECT * FROM patrocinadores WHERE eventoId = ". $eventoId);
		$patrocinadores = array();

		foreach ($res as $key => $value)
		{
			$patrocinadores[$value->id] = $value;
		}

		return $patrocinadores;
	}

	public function actualizarPatrocinador($patrocinadorId, $data)
	{
		$res = $this->db->update("patrocinadores", $data, array('id' => $patrocinadorId));
		return $res;
	}

	public function borrarPatrocinador($patrocinadorId)
	{
		$res = $this->db->delete("patrocinadores", array('id' => $patrocinadorId));
		return $res;
	}

	public function agregarPatrocinador($eventoId, $data)
	{
		$data['eventoId'] = $eventoId;
		$res = $this->db->insert("patrocinadores", $data);
		return $res;
	}

	public function crearAtletaPendiente($eventoId, $categoriaId, $personaId)
	{
		$data = array();
		$data['eventoId'] = $eventoId;
		$data['categoriaId'] = $categoriaId;
		$data['personaId'] = $personaId;

		$res = $this->db->select("SELECT * FROM registroatletas WHERE eventoId =".$eventoId." AND personaId =".$personaId);

		if(!empty($res))
			return false;

		$res = $this->db->insert("registroatletas", $data);

		return $res;
	}

	public function crearEquipoPendiente($eventoId, $categoriaId, $equipoId, $personas, $pendiente = true)
	{
		$data = array();
		$data['eventoId'] = $eventoId;
		$data['categoriaId'] = $categoriaId;
		$data['equipoId'] = $equipoId;

		if(!$pendiente)
			$data['aprobado'] = true;

		$res = $this->db->select("SELECT * FROM registroequipos WHERE eventoId =".$eventoId." AND equipoId =".$equipoId);

		if(!empty($res))
			return false;

		$res = $this->db->insert("registroequipos", $data);

		foreach ($personas as $key => $value)
		{
			$data2 = array();
			$data2['personaId'] = $value->personaId;
			$data2['equipoId'] = $equipoId;
			$data2['eventoId'] = $eventoId;

			$res2 = $this->db->insert("relregistroatletasequipo", $data2);
		}
		return $res;
	}

	public function crearRegistroAtleta($pendienteId, $categoriaId)
	{
		$resPendiente = $this->db->select("SELECT * FROM registroatletas WHERE aprobado = 0 AND id = " . $pendienteId);

		if(!empty($resPendiente))
		{
			$pendiente = $resPendiente[0];

			$data['categoriaId'] = $categoriaId;
			$data['aprobado'] = 1;
			
			$res = $this->db->update("registroatletas", $data, array('id' => $pendienteId));

			return $res;
		}

		return false;
	}

	public function crearRegistroEquipo($pendienteId, $categoriaId)
	{
		$resPendiente = $this->db->select("SELECT * FROM registroequipos WHERE aprobado = 0 AND id = " . $pendienteId);

		if(!empty($resPendiente))
		{
			$pendiente = $resPendiente[0];

			$data['categoriaId'] = $categoriaId;
			$data['aprobado'] = 1;
			
			$res = $this->db->update("registroequipos", $data, array('id' => $pendienteId));

			return $res;
		}

		return false;
	}

	public function registrarResultados(array $resultados, array $resultadosViejos)
	{
		$i = 0;

		foreach ($resultados as $key => $value)
		{
			if(isset($resultadosViejos[$value['registroAtletaId']][$value['wodId']]))
			{
				$resAtleta = $resultadosViejos[$value['registroAtletaId']][$value['wodId']];
				$where = array('registroAtletaId' => $value['registroAtletaId'], 'wodId' => $value['wodId'], 'attempt' => 0);
		
				if(isset($value['attempt']) && isset($resAtleta[$value['attempt']]))
				{
					if($resAtleta[$value['attempt']]->attempt == $value['attempt'])
					{
						$where['attempt'] = $value['attempt'];
					}
				}
				
				$resDel = $this->db->delete("resultados", $where, 'a');
			}

			$res = $this->db->insert("resultados", $value);
			$i += $res;
		}
		
		return $i;
	}

	public function getRegistro($eventoId, $atletaId)
	{
		$res = $this->db->select("SELECT * FROM registroatletas WHERE eventoId = ".$eventoId." AND personaId = ".$atletaId);

		if(empty($res))
			return false;
		else
			return $res[0];
	}

	public function getRegistroEquipo($eventoId, $equipoId)
	{
		$res = $this->db->select("SELECT * FROM registroequipos WHERE eventoId = ".$eventoId." AND equipoId = ".$equipoId);

		if(empty($res))
			return false;
		else
			return $res[0];
	}

	
	public function getRegistroEquipoEvento($eventoId, $equipoId, $atletaId)
	{
		$res = $this->db->select("SELECT * FROM relpersonaequipo WHERE equipoId = ".$equipoId." AND personaId = ".$atletaId." AND equipoEvento = 1;");

		if(empty($res))
			return false;
		else
			return $res[0];
	}

	public function getAtletaRegistradoEquipo($eventoId, array $atletas)
	{
		if(empty($atletas))
			return false;

		$str = implode(", ", array_keys($atletas));
		$res = $this->db->select("SELECT * FROM relregistroatletasequipo WHERE eventoId = ".$eventoId." AND personaId IN (".$str.")");

		if(empty($res))
			return false;
		else
			return true;
	}

	public function registrarPago(array $datos)
	{
		$res = $this->db->insert("pagosevento", $datos);
		return $res;
	}

	public function asignarPago($pendienteId, $pagoId)
	{
		$res = $this->db->update("registroatletas", array('pagoId' => $pagoId), array('id' => $pendienteId));
		return $res;
	}

	public function asignarPagoEquipo($pendienteId, $pagoId)
	{
		$res = $this->db->update("registroequipos", array('pagoId' => $pagoId), array('id' => $pendienteId));
		return $res;
	}

	public function getPago($pagoId)
	{
		$res = $this->db->select("SELECT * FROM pagosevento WHERE id = ".$pagoId);

		if(empty($res))
			return false;
		else
			return $res[0];
	}

	public function getPagos($atletas)
	{
		if(empty($atletas))
			return array();

		$strIDS = implode(",", array_keys($atletas));
		$res = $this->db->select("SELECT PE.*, RA.id AS idRegistro FROM pagosevento PE INNER JOIN registroatletas RA ON (RA.pagoId = PE.id) WHERE RA.id IN (".$strIDS.")");

		if(empty($res))
			return array();
		else
		{
			$pagos = array();

			foreach ($res as $key => $value)
			{
				$pagos[$value->idRegistro] = $value;
			}

			return $pagos;
		}
	}

	public function getPagosEquipos($equipos)
	{
		if(empty($equipos))
			return array();

		$strIDS = implode(",", array_keys($equipos));
		$res = $this->db->select("SELECT PE.*, RE.id AS idRegistro FROM pagosevento PE INNER JOIN registroequipos RE ON (RE.pagoId = PE.id) WHERE RE.id IN (".$strIDS.")");

		if(empty($res))
			return array();
		else
		{
			$pagos = array();

			foreach ($res as $key => $value)
			{
				$pagos[$value->idRegistro] = $value;
			}

			return $pagos;
		}
	}

	public function getRegistroPorIdPago($pagoId)
	{
		$res = $this->db->select("SELECT RA.* FROM pagosevento PE INNER JOIN registroatletas RA ON (RA.pagoId = PE.id) WHERE PE.id = ".$pagoId);

		if(empty($res))
			return false;
		else
			return $res[0];
	}

	public function getRegistroPorIdPagoEquipo($pagoId)
	{
		$res = $this->db->select("SELECT RA.* FROM pagosevento PE INNER JOIN registroequipos RA ON (RA.pagoId = PE.id) WHERE PE.id = ".$pagoId);

		if(empty($res))
			return false;
		else
			return $res[0];
	}

	public function aprobarEvento($eventoId)
	{
		$res = $this->db->update("eventos", array('aprobado' => 1), array('id' => $eventoId));
		return $res;
	}

	public function desaprobarEvento($eventoId)
	{
		$res = $this->db->update("eventos", array('aprobado' => 0), array('id' => $eventoId));
		return $res;
	}

	public function aprobarBox($boxId)
	{
		$res = $this->db->update("boxes", array('aprobado' => 1), array('id' => $boxId));
		return $res;
	}

	public function desaprobarBox($boxId)
	{
		$res = $this->db->update("boxes", array('aprobado' => 0), array('id' => $boxId));
		return $res;
	}
/*
	public function getAtletasRegistradosEvento($eventoId, $equipoId)
	{
		$res = $this->db->select("SELECT PER.sexo, PER.nombre, PER.apellido, PER.ident, PER.archivoFoto, PER.pais, PER.estado, PER.fechaNacimiento, PER.correo, PER.telefono_1, PER.boxId AS boxId, BOX.nombre AS boxStr, RPE.* FROM relpersonaequipo RPE INNER JOIN personas PER ON (PER.id = RPE.personaId) LEFT JOIN boxes BOX ON (PER.boxId = BOX.id) WHERE RPE.equipoId = ".$equipoId." AND RPE.equipoEvento = 1;");
		$atletas = array();

		foreach ($res as $keyA => $valueA)
		{
			$atletas[$valueA->personaId] = $valueA;
		}

		return $atletas;
	}

*/
}

?>