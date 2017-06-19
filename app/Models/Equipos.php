<?php
namespace Models;

use Core\Model;

class Equipos extends Model
{
	function __construct()
	{
		parent::__construct();
	}

	public function get($equipoId)
	{
		$res = $this->db->select("SELECT EQ.*, EV.nombre AS eventoStr FROM equipos EQ LEFT JOIN eventos EV ON (EV.id = EQ.eventoID) WHERE EQ.id = ".$equipoId.";");

		if(empty($res))
			return false;
		else
			return $res[0];
	}

	//Remover DEDICADO
	public function getOrganizados($idPersona)
	{
		$equipos = array();

		$res = $this->db->select("SELECT EQ.*, EV.nombre as eventoStr FROM equipos EQ LEFT JOIN eventos EV ON (EV.id = EQ.eventoID) WHERE EQ.capitanId =" . $idPersona.";");
		
		foreach ($res as $key => $value)
		{
			$equipos[] = $value;
		}

		return $equipos;
	}

	//Remover DEDICADO
	public function getEquiposCuenta($idPersona)
	{
		$equipos = array();
		
		$res = $this->db->select("SELECT * FROM equipos EQ INNER JOIN relpersonaequipo REP ON REP.equipoId = EQ.id WHERE (EQ.capitanId <> ".$idPersona." AND REP.personaId = ".$idPersona.");");
		
		foreach ($res as $key => $value)
		{
			$equipos[] = $value;
		}

		return $equipos;
	}
	
	public function getAtleta($equipoId, $atletaId)
	{
		$res = $this->db->select("SELECT * FROM relpersonaequipo WHERE equipoId = ".$equipoId." AND personaId = ".$atletaId.";");

		if(empty($res))
			return false;
		else
			return $res[0];
	}

	//Integrantes actuales
	public function getAtletas($equipoId)
	{
		$res = $this->db->select("SELECT PER.sexo, PER.nombre, PER.apellido, PER.ident, PER.archivoFoto, PER.pais, PER.estado, PER.fechaNacimiento, PER.correo, PER.telefono_1, PER.boxId AS boxId, BOX.nombre AS boxStr, RPE.* FROM relpersonaequipo RPE INNER JOIN personas PER ON (PER.id = RPE.personaId) LEFT JOIN boxes BOX ON (PER.boxId = BOX.id) WHERE RPE.equipoId = ". $equipoId.";");
		$atletas = array();

		foreach ($res as $keyA => $valueA)
		{
			$atletas[$valueA->personaId] = $valueA;
		}

		return $atletas;
	}

	public function getParticipaciones($equipoId)
	{
		$res = $this->db->select("SELECT EV.* FROM eventos EV INNER JOIN registroequipos RE ON (RE.eventoId = EV.id) AND RE.aprobado = 1 AND EV.aprobado = 1 AND RE.equipoId = ". $equipoId.";");
		$eventos = array();

		foreach ($res as $keyA => $valueA)
		{
			$eventos[$valueA->id] = $valueA;
		}

		return $eventos;
	}

	//AGREGAR: Integrantes al momento de registro

	public function crearEquipo(array $datos)
	{
		$res = $this->db->insert("equipos", $datos);
		return $res;
	}

	public function prepararEquipos($equipos = array())
	{
		foreach ($equipos as $key => &$value)
		{
			self::prepararEquipo($value);
		}
		unset($value);
	}

	public function prepararEquipo($equipo)
	{
		if(!isset($equipo) || empty($equipo))
			return;

		if($equipo->archivoFoto && file_exists("./".TEAMPICTUREPATH.$equipo->archivoFoto))
			$equipo->fotoPath = DIR.TEAMPICTUREPATH.$equipo->archivoFoto;
		else
			$equipo->fotoPath = DIR.TEAMPICTUREPATH."default.jpg";
	}

	public function agregarAtleta($equipoId, $personaId, $self = false)
	{
		$data = array();
		$data['personaId'] = $personaId;
		$data['equipoId'] = $equipoId;
		
		if($self)
			$data['aprobado'] = true;

		$res = $this->db->insert("relpersonaequipo", $data);
		return $res;
	}

	public function borrarAtleta($equipoId, $personaId)
	{
		$data = array();
		$data['personaId'] = $personaId;
		$data['equipoId'] = $equipoId;

		$res = $this->db->delete("relpersonaequipo", $data);
		return $res;
	}

	public function borrarSolicitudes($equipoId)
	{
		$where = array('equipoId' => $equipoId, 'aprobado' => false);
		$res = $this->db->delete("relpersonaequipo", $where);

		return $res;
	}

	public function aceptarSolicitud($equipoId, $personaId, $eventoId = null)
	{
		$where = array('equipoId' => $equipoId, 'personaId' => $personaId);
		$data = array('aprobado' => true);
		$res = $this->db->update("relpersonaequipo", $data, $where);

		if($res && !is_null($eventoId))
		{
			$res = $this->db->select("SELECT RPE.* FROM relpersonaequipo RPE INNER JOIN equipos EQ ON (RPE.equipoId = EQ.id) WHERE EQ.dedicado = 1 AND RPE.personaId = ".$personaId." AND EQ.eventoId = ".$eventoId." AND EQ.id <> ".$equipoId.";");
			$ids = array();

			foreach ($res as $key => $value)
			{
				$ids[$value->equipoId] = $value;
			}
			$str = implode(", ", array_keys($ids));

			if($str)
				$reso = $this->db->raw("DELETE FROM relpersonaequipo WHERE equipoId IN (".$str.") AND personaId =".$personaId.";");
		}

		return $res;
	}

	public function rechazarSolicitud($equipoId, $personaId, $eventoId = null)
	{
		$where = array('equipoId' => $equipoId, 'personaId' => $personaId);
		$res = $this->db->delete("relpersonaequipo", $where);

		return $res;
	}

	public function bloquear($equipoId)
	{
		$where = array('id' => $equipoId);
		$data = array('locked' => true);
		$res = $this->db->update("equipos", $data, $where);

		return $res;
	}

}

?>