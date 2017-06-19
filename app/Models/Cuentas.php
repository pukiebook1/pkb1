<?php
namespace Models;

use Core\Model;
use Helpers\Password;
use Helpers\Sesiones;
use Models;

class Cuentas extends Model
{
	public static $sesion = null;

	public function __construct()
	{
		parent::__construct();
	}

	public static function getSession()
	{
		if(self::$sesion)
			return self::$sesion;
		
		$sesion = array();
		$sesion['logeado'] = Sesiones::$logged;
		$sesion['id'] = Sesiones::$personaId;

		if($sesion['logeado'] && $sesion['id'])
		{
			$cuentas = new Models\Cuentas();
			$perfiles = new Models\Perfiles();

			$persona = $cuentas->getPersona($sesion['id']);
			$perfiles->prepararCuenta($persona);
			$sesion['persona'] = $persona;

			self::$sesion = $sesion;
			return $sesion;
		}

		return false;
	}

	public function correoRegistrado($correo)
	{
		$res = $this->db->select("SELECT `per`.`sexo`, `per`.`id` AS `idPersona`, `per`.`nombre` AS `nombre`, `per`.`apellido` AS `apellido`, `per`.`pais` AS `pais`, `per`.`correo` AS `correo`, `per`.`telefono_1` AS `telefono_1`, `per`.`telefono_2` AS `telefono_2`, `per`.`contrasena` AS `contrasena`, `per`.`subscripcionPlan` AS `subscripcionPlan`, `per`.`archivoFoto` AS `archivoFoto`, `per`.`esAdmin` AS `esAdmin`, `per`.`boxId` AS `boxId`, `per`.`estado` AS `estado`, `per`.`fechaNacimiento` AS `fechaNacimiento`, `per`.`alias` AS `alias`, `per`.`wkclean` AS `wkclean`, `per`.`wksnatch` AS `wksnatch`, `per`.`wkdead` AS `wkdead`, `per`.`wkbacksquat` AS `wkbacksquat`, `per`.`wkfran` AS `wkfran`, `per`.`wkisabel` AS `wkisabel`, `per`.`wkgrace` AS `wkgrace`, `per`.`wkrun5k` AS `wkrun5k`, `per`.`wkcindy` AS `wkcindy`, `per`.`resetCode` AS `resetCode`, `per`.`activationCode` AS `activationCode`, `per`.`cuentaActivada` AS `cuentaActivada`, `per`.`correo_verificado` AS `correo_verificado`, `per`.`pic` AS `pic`, `per`.`picId` AS `picId`, `per`.`fechaRegistro` AS `fechaRegistro`, `per`.`ident` AS `ident`, `per`.`cc` AS `cc`, `pa`.`country_name` AS `paisStr`, `bo`.`nombre` AS `boxStr`, `bo`.`id` AS `boxId`, `pl`.`nombre` AS `plan`, `pl`.`atletas` AS `atletas`, `pl`.`categorias` AS `categorias`, `pl`.`patrocinadores` AS `patrocinadores` FROM (((`personas` `per` LEFT JOIN `paises` `pa` ON ((`pa`.`id` = `per`.`pais`))) LEFT JOIN `boxes` `bo` ON ((`bo`.`id` = `per`.`boxId`))) LEFT JOIN `planes` `pl` ON ((`pl`.`id` = `per`.`subscripcionPlan`))) WHERE per.correo = '".$correo."'");

		if(empty($res))
			return false;
		else
			return $res[0];
	}

	public function setResetCode($personaId, $codigo)
	{
		$where = array('id' => $personaId);
		$data = array('resetCode' => $codigo);
		$res = $this->db->update("personas", $data, $where);

		return $res;
	}

	public function setActivationCode($personaId, $codigo)
	{
		$where = array('id' => $personaId);
		$data = array('activationCode' => $codigo);
		$res = $this->db->update("personas", $data, $where);

		return $res;
	}

	public function registrarUsuario($datos)
	{
		$res = $this->db->insert("personas", $datos);
		return $res;
	}

	public function comprobarAcceso($correo, $contrasena)
	{
		$res = $this->db->select("SELECT `per`.`sexo`, `per`.`id` AS `idPersona`, `per`.`nombre` AS `nombre`, `per`.`apellido` AS `apellido`, `per`.`pais` AS `pais`, `per`.`correo` AS `correo`, `per`.`telefono_1` AS `telefono_1`, `per`.`telefono_2` AS `telefono_2`, `per`.`contrasena` AS `contrasena`, `per`.`subscripcionPlan` AS `subscripcionPlan`, `per`.`archivoFoto` AS `archivoFoto`, `per`.`esAdmin` AS `esAdmin`, `per`.`boxId` AS `boxId`, `per`.`estado` AS `estado`, `per`.`fechaNacimiento` AS `fechaNacimiento`, `per`.`alias` AS `alias`, `per`.`wkclean` AS `wkclean`, `per`.`wksnatch` AS `wksnatch`, `per`.`wkdead` AS `wkdead`, `per`.`wkbacksquat` AS `wkbacksquat`, `per`.`wkfran` AS `wkfran`, `per`.`wkisabel` AS `wkisabel`, `per`.`wkgrace` AS `wkgrace`, `per`.`wkrun5k` AS `wkrun5k`, `per`.`wkcindy` AS `wkcindy`, `per`.`resetCode` AS `resetCode`, `per`.`activationCode` AS `activationCode`, `per`.`cuentaActivada` AS `cuentaActivada`, `per`.`correo_verificado` AS `correo_verificado`, `per`.`pic` AS `pic`, `per`.`picId` AS `picId`, `per`.`fechaRegistro` AS `fechaRegistro`, `per`.`ident` AS `ident`, `per`.`cc` AS `cc`, `pa`.`country_name` AS `paisStr`, `bo`.`nombre` AS `boxStr`, `bo`.`id` AS `boxId`, `pl`.`nombre` AS `plan`, `pl`.`atletas` AS `atletas`, `pl`.`categorias` AS `categorias`, `pl`.`patrocinadores` AS `patrocinadores` FROM (((`personas` `per` LEFT JOIN `paises` `pa` ON ((`pa`.`id` = `per`.`pais`))) LEFT JOIN `boxes` `bo` ON ((`bo`.`id` = `per`.`boxId`))) LEFT JOIN `planes` `pl` ON ((`pl`.`id` = `per`.`subscripcionPlan`))) WHERE per.correo = '".$correo."'");

		if(!empty($res))
		{
			if(Password::verify($contrasena, $res[0]->contrasena) == true)
				return $res[0];
		}

		return false;
	}

	public function getPersona($idPersona)
	{
		$res = $this->db->select("SELECT `per`.`sexo`, `per`.`id` AS `idPersona`, `per`.`nombre` AS `nombre`, `per`.`apellido` AS `apellido`, `per`.`pais` AS `pais`, `per`.`correo` AS `correo`, `per`.`telefono_1` AS `telefono_1`, `per`.`telefono_2` AS `telefono_2`, `per`.`contrasena` AS `contrasena`, `per`.`subscripcionPlan` AS `subscripcionPlan`, `per`.`archivoFoto` AS `archivoFoto`, `per`.`esAdmin` AS `esAdmin`, `per`.`boxId` AS `boxId`, `per`.`estado` AS `estado`, `per`.`fechaNacimiento` AS `fechaNacimiento`, `per`.`alias` AS `alias`, `per`.`wkclean` AS `wkclean`, `per`.`wksnatch` AS `wksnatch`, `per`.`wkdead` AS `wkdead`, `per`.`wkbacksquat` AS `wkbacksquat`, `per`.`wkfran` AS `wkfran`, `per`.`wkisabel` AS `wkisabel`, `per`.`wkgrace` AS `wkgrace`, `per`.`wkrun5k` AS `wkrun5k`, `per`.`wkcindy` AS `wkcindy`, `per`.`resetCode` AS `resetCode`, `per`.`activationCode` AS `activationCode`, `per`.`cuentaActivada` AS `cuentaActivada`, `per`.`correo_verificado` AS `correo_verificado`, `per`.`pic` AS `pic`, `per`.`picId` AS `picId`, `per`.`fechaRegistro` AS `fechaRegistro`, `per`.`ident` AS `ident`, `per`.`cc` AS `cc`, `pa`.`country_name` AS `paisStr`, `bo`.`nombre` AS `boxStr`, `bo`.`id` AS `boxId`, `pl`.`nombre` AS `plan`, `pl`.`atletas` AS `atletas`, `pl`.`categorias` AS `categorias`, `pl`.`patrocinadores` AS `patrocinadores` FROM (((`personas` `per` LEFT JOIN `paises` `pa` ON ((`pa`.`id` = `per`.`pais`))) LEFT JOIN `boxes` `bo` ON ((`bo`.`id` = `per`.`boxId`))) LEFT JOIN `planes` `pl` ON ((`pl`.`id` = `per`.`subscripcionPlan`))) WHERE per.id = ".$idPersona);

		if(!empty($res))
			return $res[0];

		return false;
	}

	public function getPersonaPorCorreo($correo)
	{
		$res = $this->db->select("SELECT `per`.`sexo`, `per`.`id` AS `idPersona`, `per`.`nombre` AS `nombre`, `per`.`apellido` AS `apellido`, `per`.`pais` AS `pais`, `per`.`correo` AS `correo`, `per`.`telefono_1` AS `telefono_1`, `per`.`telefono_2` AS `telefono_2`, `per`.`contrasena` AS `contrasena`, `per`.`subscripcionPlan` AS `subscripcionPlan`, `per`.`archivoFoto` AS `archivoFoto`, `per`.`esAdmin` AS `esAdmin`, `per`.`boxId` AS `boxId`, `per`.`estado` AS `estado`, `per`.`fechaNacimiento` AS `fechaNacimiento`, `per`.`alias` AS `alias`, `per`.`wkclean` AS `wkclean`, `per`.`wksnatch` AS `wksnatch`, `per`.`wkdead` AS `wkdead`, `per`.`wkbacksquat` AS `wkbacksquat`, `per`.`wkfran` AS `wkfran`, `per`.`wkisabel` AS `wkisabel`, `per`.`wkgrace` AS `wkgrace`, `per`.`wkrun5k` AS `wkrun5k`, `per`.`wkcindy` AS `wkcindy`, `per`.`resetCode` AS `resetCode`, `per`.`activationCode` AS `activationCode`, `per`.`cuentaActivada` AS `cuentaActivada`, `per`.`correo_verificado` AS `correo_verificado`, `per`.`pic` AS `pic`, `per`.`picId` AS `picId`, `per`.`fechaRegistro` AS `fechaRegistro`, `per`.`ident` AS `ident`, `per`.`cc` AS `cc`, `pa`.`country_name` AS `paisStr`, `bo`.`nombre` AS `boxStr`, `bo`.`id` AS `boxId`, `pl`.`nombre` AS `plan`, `pl`.`atletas` AS `atletas`, `pl`.`categorias` AS `categorias`, `pl`.`patrocinadores` AS `patrocinadores` FROM (((`personas` `per` LEFT JOIN `paises` `pa` ON ((`pa`.`id` = `per`.`pais`))) LEFT JOIN `boxes` `bo` ON ((`bo`.`id` = `per`.`boxId`))) LEFT JOIN `planes` `pl` ON ((`pl`.`id` = `per`.`subscripcionPlan`))) WHERE `per`.`correo` = '".$correo."'");

		if(!empty($res))
			return $res[0];

		return false;
	}

	public function getPersonaPorRegistro($registroId)
	{
		$res = $this->db->select("SELECT `per`.`sexo`, `ra`.`personaId`, `per`.`id` AS `idPersona`, `per`.`nombre` AS `nombre`, `per`.`apellido` AS `apellido`, `per`.`pais` AS `pais`, `per`.`correo` AS `correo`, `per`.`telefono_1` AS `telefono_1`, `per`.`telefono_2` AS `telefono_2`, `per`.`contrasena` AS `contrasena`, `per`.`subscripcionPlan` AS `subscripcionPlan`, `per`.`archivoFoto` AS `archivoFoto`, `per`.`esAdmin` AS `esAdmin`, `per`.`boxId` AS `boxId`, `per`.`estado` AS `estado`, `per`.`fechaNacimiento` AS `fechaNacimiento`, `per`.`alias` AS `alias`, `per`.`wkclean` AS `wkclean`, `per`.`wksnatch` AS `wksnatch`, `per`.`wkdead` AS `wkdead`, `per`.`wkbacksquat` AS `wkbacksquat`, `per`.`wkfran` AS `wkfran`, `per`.`wkisabel` AS `wkisabel`, `per`.`wkgrace` AS `wkgrace`, `per`.`wkrun5k` AS `wkrun5k`, `per`.`wkcindy` AS `wkcindy`, `per`.`resetCode` AS `resetCode`, `per`.`activationCode` AS `activationCode`, `per`.`cuentaActivada` AS `cuentaActivada`, `per`.`correo_verificado` AS `correo_verificado`, `per`.`pic` AS `pic`, `per`.`picId` AS `picId`, `per`.`fechaRegistro` AS `fechaRegistro`, `per`.`ident` AS `ident`, `per`.`cc` AS `cc`, `pa`.`country_name` AS `paisStr`, `bo`.`nombre` AS `boxStr`, `bo`.`id` AS `boxId`, `pl`.`nombre` AS `plan`, `pl`.`atletas` AS `atletas`, `pl`.`categorias` AS `categorias`, `pl`.`patrocinadores` AS `patrocinadores`,`ra`.`id` AS `idRegistro`,`ra`.`pagoId` AS `pagoId`,`ra`.`eventoId` AS `eventoId`,`ra`.`categoriaId` AS `categoriaId`,`ra`.`aprobado` AS `aprobado`,`ra`.`pos` AS `pos`,`ra`.`puntaje` AS `puntaje`,`ra`.`mediaPos` AS `mediaPos`,`ra`.`bodyweight` AS `bodyweight`,`ra`.`average` AS `average`,`ra`.`total` AS `total`  FROM (`personas` `per` LEFT JOIN `paises` `pa` ON `pa`.`id` = `per`.`pais` LEFT JOIN `planes` `pl` ON ((`pl`.`id` = `per`.`subscripcionPlan`)) LEFT JOIN `boxes` `bo` ON ((`bo`.`id` = `per`.`boxId`)) INNER JOIN registroatletas ra ON (ra.personaId = per.id)) WHERE `ra`.`id` = ".$registroId);

		if(!empty($res))
			return $res[0];

		return false;
	}

	public function actualizarPersona($idPersona, array $datos)
	{
		$where = array('id' => $idPersona);
		$res = $this->db->update("personas", $datos, $where);

		return $res;
	}

	public function actualizarContrasena($correo, $contrasena)
	{
		$data = array('contrasena' => $contrasena);
		$where = array('correo' => $correo);
		
		$res = $this->db->update("personas", $data, $where);

		return $res;
	}

	public function activarCuenta($idPersona)
	{
		$data = array('cuentaActivada' => 1, 'correo_verificado' => 1);
		$where = array('id' => $idPersona);
		
		$res = $this->db->update("personas", $data, $where);

		return $res;
	}

	public function verificarCorreo($idPersona)
	{
		$data = array('correo_verificado' => 0);
		$where = array('id' => $idPersona);
		
		$res = $this->db->update("personas", $data, $where);

		return $res;
	}

	public function correoVerificado($idPersona)
	{
		$data = array('correo_verificado' => 1);
		$where = array('id' => $idPersona);
		
		$res = $this->db->update("personas", $data, $where);

		return $res;
	}
}

?>