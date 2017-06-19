<?php
namespace Controllers;

use Core\View;
use Core\Controller;
use Helpers\Url;
use Helpers\Gump;
use Helpers\CsrfNew as Csrf;
use Helpers\Password;
use Models;

class Equipos extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public static function equipoNotFound()
	{
		$data['title'] = "Equipo no encontrado";
		$data['titulo'] = "Equipo no encontrado";
		$data['mensaje'] = "El equipo solicitado no fue encontrado.";
		$data['href'] = DIR."";
		$data['boton'] = "Ir a Inicio";

		View::renderTemplate('header', $data);
		View::render('tracker/mensaje', $data);
		View::renderTemplate('footer', $data);
	}

	public static function equipoFormalizado()
	{
		$data['title'] = "Equipo Formalizado";
		$data['titulo'] = "Equipo Formalizado";
		$data['mensaje'] = "El equipo ha sido formalizado correctamente..";
		$data['href'] = DIR."";
		$data['boton'] = "Ir a Inicio";

		View::renderTemplate('header', $data);
		View::render('tracker/mensaje', $data);
		View::renderTemplate('footer', $data);
	}

	public static function actualmenteIntegrante($equipoId)
	{
		$data['title'] = "Ya eres integrante de este equipo";
		$data['titulo'] = "Ya eres integrante de este equipo";
		//$data['mensaje'] = "El equipo solicitado no fue encontrado.";
		$data['href'] = DIR."equipo/".$equipoId;
		$data['boton'] = "Volver";

		View::renderTemplate('header', $data);
		View::render('tracker/mensaje', $data);
		View::renderTemplate('footer', $data);
	}

	public static function noInvitado($equipoId)
	{
		$data['title'] = "No posees invitaciones para este equipo";
		$data['titulo'] = "No posees invitaciones para este equipo";
		//$data['mensaje'] = "El equipo solicitado no fue encontrado.";
		$data['href'] = DIR."equipo/".$equipoId;
		$data['boton'] = "Volver";

		View::renderTemplate('header', $data);
		View::render('tracker/mensaje', $data);
		View::renderTemplate('footer', $data);
	}

	public static function noCumpleRequisitos()
	{
		$data['title'] = "No cumple requisitos de evento";
		$data['titulo'] = "No cumple requisitos de evento";
		$data['mensaje'] = "El equipo registrado no cumple los requisitos para este evento.";
		$data['href'] = DIR."";
		$data['boton'] = "Inicio";

		View::renderTemplate('header', $data);
		View::render('tracker/mensaje', $data);
		View::renderTemplate('footer', $data);
	}

	public static function noCumpleCantIntegrantes($cantidad)
	{
		$data['title'] = "No cumple requisitos de equipo";
		$data['titulo'] = "No cumple requisitos de equipo";
		$data['mensaje'] = "El equipo registrado no cumple con la cantidad de integrantes para este evento (".$cantidad." atletas).<br/><br/>Todos los integrantes deben estar en estado 'Aprobado' en el equipo al momento del registro del equipo en el evento.";
		$data['href'] = DIR."";
		$data['boton'] = "Inicio";

		View::renderTemplate('header', $data);
		View::render('tracker/mensaje', $data);
		View::renderTemplate('footer', $data);
	}

	public static function noCumpleGeneroIntegrantes()
	{
		$data['title'] = "No cumple requisitos de equipo";
		$data['titulo'] = "No cumple requisitos de equipo";
		$data['mensaje'] = "Los integrantes del equipo no cumplen con la condici&oacute;n de mismo g&eacute;nero para este evento.";
		$data['href'] = DIR."";
		$data['boton'] = "Inicio";

		View::renderTemplate('header', $data);
		View::render('tracker/mensaje', $data);
		View::renderTemplate('footer', $data);
	}

	public static function noEspecificaGeneroIntegrantes()
	{
		$data['title'] = "No cumple requisitos de equipo";
		$data['titulo'] = "No cumple requisitos de equipo";
		$data['mensaje'] = "Todos los integrantes del equipo deben especificar su g&eacute;nero en su perfil.";
		$data['href'] = DIR."";
		$data['boton'] = "Inicio";

		View::renderTemplate('header', $data);
		View::render('tracker/mensaje', $data);
		View::renderTemplate('footer', $data);
	}

	public static function noCumpleCantIntegrantes2()
	{
		$data['title'] = "No cumple requisitos de equipo";
		$data['titulo'] = "No cumple requisitos de equipo";
		$data['mensaje'] = "Debe seleccionar 3 (tres) participantes por equipo para este evento. <br/><br/>Recuerde seleccionarse como participante.";
		$data['href'] = DIR."";
		$data['boton'] = "Inicio";

		View::renderTemplate('header', $data);
		View::render('tracker/mensaje', $data);
		View::renderTemplate('footer', $data);
	}

	public function ver($equipoId)
	{
		$equipos = new Models\Equipos();
		$equipo = $equipos->get($equipoId);
		$equipos->prepararEquipo($equipo);

		if(!$equipo)
		{
			Equipos::equipoNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();
		$persona = null;
		$esOrganizador = false;
		$esAdmin = false;
		$estoy = false;

		if($logeado)
		{
			$persona = $logeado['persona'];
			$esAdmin = $persona->esAdmin;
			$esOrganizador = ($equipo->capitanId == $persona->idPersona);
			$registro = $equipos->getAtleta($equipo->id, $persona->idPersona);

			if($registro)
				$estoy = $registro;
		}

		$atletas = $equipos->getAtletas($equipo->id);
		$atlApro = array();
		$atlPend = array();
		$perfiles = new Models\Perfiles();
		$perfiles->prepararCuentas($atletas);

		foreach ($atletas as $key => $value)
		{
			if($value->aprobado)
				$atlApro[$key] = $value;
			else
				$atlPend[$key] = $value;
		}

		$participaciones = $equipos->getParticipaciones($equipoId);
		
		foreach ($participaciones as $key => $valueE)
		{
			if(($valueE->visible) || ($logeado && $logeado['persona']->esAdmin && !$valueE->visible))
			{}
			else
				unset($participaciones[$key]);
		}

		$eventos = new Models\Eventos();
		$eventos->prepararEventos($participaciones);

		$data['title'] = $equipo->nombre;
		$data["equipo"] = $equipo;
		$data["equipoHome"] = true;
		$data["estoy"] = $estoy;
		$data["logeado"] = $logeado;
		$data["esOrganizador"] = $esOrganizador;
		$data["esAdmin"] = $esAdmin;
		$data["atletasApro"] = $atlApro;
		$data["atletasPend"] = $atlPend;
		$data["participaciones"] = $participaciones;
		$data["fbableTeam"] = true;

		View::renderTemplate('header', $data);
		View::render('tracker/equipo/equipo', $data);
		View::renderTemplate('footerTabla', $data);
	}

	public function aceptarSolicitud($equipoId)
	{
		$equipos = new Models\Equipos();
		$equipo = $equipos->get($equipoId);
		$equipos->prepararEquipo($equipo);

		if(!$equipo)
		{
			Equipos::equipoNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();
		
		if(!$logeado)
		{
			Cuentas::sectionNeedLogin();
			return;
		}

		$estoy = false;

		$persona = $logeado['persona'];
		$registro = $equipos->getAtleta($equipo->id, $persona->idPersona);

		if($registro)
			$estoy = $registro;
	

		if(!$estoy->aprobado)
		{
			$equipos->aceptarSolicitud($equipo->id, $logeado['persona']->idPersona, $equipo->eventoId);
			self::ver($equipo->id);
		}
		else if($estoy->aprobado)
		{
			self::actualmenteIntegrante($equipo->id);
		}
		else
		{
			self::noInvitado($equipo->id);
		}
	}

	public function rechazarSolicitud($equipoId)
	{
		$equipos = new Models\Equipos();
		$equipo = $equipos->get($equipoId);
		$equipos->prepararEquipo($equipo);

		if(!$equipo)
		{
			Equipos::equipoNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();
		
		if(!$logeado)
		{
			Cuentas::sectionNeedLogin();
			return;
		}

		$estoy = false;

		$persona = $logeado['persona'];
		$registro = $equipos->getAtleta($equipo->id, $persona->idPersona);

		if($registro)
			$estoy = $registro;
	

		if($estoy)
		{
			$equipos->rechazarSolicitud($equipo->id, $logeado['persona']->idPersona, $equipo->eventoId);
			self::ver($equipo->id);
		}
		else
		{
			self::noInvitado($equipo->id);
		}
	}

	public function formalizar($equipoId)
	{
		$equipos = new Models\Equipos();
		$equipo = $equipos->get($equipoId);
		$equipos->prepararEquipo($equipo);

		if(!$equipo)
		{
			Equipos::equipoNotFound();
			return;
		}

		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($equipo->eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			Eventos::eventoNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();
		$persona = null;
		$esOrganizador = false;
		$esAdmin = false;
		$estoy = false;

		if($logeado)
		{
			$persona = $logeado['persona'];
			$esAdmin = $persona->esAdmin;
			$esOrganizador = ($equipo->capitanId == $persona->idPersona);
			$registro = $equipos->getAtleta($equipo->id, $persona->idPersona);

			if($registro)
				$estoy = $registro;
		}

		$atletas = $equipos->getAtletas($equipo->id);
		$perfiles = new Models\Perfiles();
		$perfiles->prepararCuentas($atletas);

		$totAprobados = 0;
		$sameSex = 1;
		$sexTmp = 'N';

		foreach ($atletas as $key => $value)
		{
			if(!$value->aprobado)
				continue;

			$totAprobados++;

			if(strcmp($sexTmp, 'N') == 0)
				$sexTmp = $value->sexo;

			if(strcmp($value->sexo, $sexTmp) != 0)
				$sameSex = 0;

			$sexTmp = $value->sexo;
		}

		if($evento->eqCantIntegrantes != $totAprobados)
		{
			Equipos::noCumpleCantIntegrantes($evento->eqCantIntegrantes);
			return;
		}

		if($evento->eqMismoGenero )
		{
			if(strcmp($sexTmp, 'X') == 0)
			{
				Equipos::noEspecificaGeneroIntegrantes();
				return;
			}
			else
			{
				if(!$sameSex)
				{
					Equipos::noCumpleGeneroIntegrantes();
					return;
				}
			}
		}
		
		$data['title'] = $equipo->nombre;
		$data["equipo"] = $equipo;
		$data["estoy"] = $estoy;
		$data["logeado"] = $logeado;
		$data["esOrganizador"] = $esOrganizador;
		$data["esAdmin"] = $esAdmin;

		View::renderTemplate('header', $data);
		View::render('tracker/equipo/formalizarPre', $data);
		View::renderTemplate('footerTabla', $data);
	}


	public function formalizarPOST($equipoId)
	{
		$equipos = new Models\Equipos();
		$equipo = $equipos->get($equipoId);
		$equipos->prepararEquipo($equipo);

		if(!$equipo)
		{
			Equipos::equipoNotFound();
			return;
		}

		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($equipo->eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			Eventos::eventoNotFound();
			return;
		}

		$logeado = Models\Cuentas::getSession();
		$persona = null;
		$esOrganizador = false;
		$esAdmin = false;
		$estoy = false;

		if($logeado)
		{
			$persona = $logeado['persona'];
			$esAdmin = $persona->esAdmin;
			$esOrganizador = ($equipo->capitanId == $persona->idPersona);
			$registro = $equipos->getAtleta($equipo->id, $persona->idPersona);

			if($registro)
				$estoy = $registro;
		}

		$atletas = $equipos->getAtletas($equipo->id);
		$perfiles = new Models\Perfiles();
		$perfiles->prepararCuentas($atletas);

		$totAprobados = 0;
		$sameSex = 1;
		$sexTmp = 'N';
		$atletasOK = array();

		foreach ($atletas as $key => $value)
		{
			if(!$value->aprobado)
				continue;

			$atletasOK[$key] = $value;

			$totAprobados++;

			if(strcmp($sexTmp, 'N') == 0)
				$sexTmp = $value->sexo;

			if(strcmp($value->sexo, $sexTmp) != 0)
				$sameSex = 0;

			$sexTmp = $value->sexo;
		}

		if($evento->eqCantIntegrantes != $totAprobados)
		{
			Equipos::noCumpleCantIntegrantes($evento->eqCantIntegrantes);
			return;
		}

		if($evento->eqMismoGenero )
		{
			if(strcmp($sexTmp, 'X') == 0)
			{
				Equipos::noEspecificaGeneroIntegrantes();
				return;
			}
			else
			{
				if(!$sameSex)
				{
					Equipos::noCumpleGeneroIntegrantes();
					return;
				}
			}
		}

		if(isset($_POST['submit']))
		{
			if(isset($_POST['formalizar']))
			{
				$regId = $eventos->crearEquipoPendiente($equipo->eventoId, $equipo->categoriaId, $equipo->id, $atletasOK, false);
				$equipos->bloquear($equipo->id);
				
				if($equipo->dedicado)
				{
					$equipos->borrarSolicitudes($equipoId);
				}
				self::equipoFormalizado();
			}
		}
	}
}
?>