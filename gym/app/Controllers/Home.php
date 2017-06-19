<?php
namespace Controllers;

use Core\View;
use Core\Controller;
use Models;

class Home extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$eventos = new Models\Eventos();
		$eventosProx = $eventos->getEventosProximos();
		$eventosAct = $eventos->getEventosEnCurso();
		$eventosMostrar = array();
		$eventosCurso = array();
		$eventos->prepararEventos($eventosProx);
		$eventos->prepararEventos($eventosAct);

		$logeado = Models\Cuentas::getSession();

		foreach ($eventosProx as $key => $valueE)
		{
			if(($valueE->visible) || ($logeado && $logeado['persona']->esAdmin && !$valueE->visible))
				$eventosMostrar[$key] = $valueE;
		}
		
		foreach ($eventosAct as $key => $valueE)
		{
			if(($valueE->visible) || ($logeado && $logeado['persona']->esAdmin && !$valueE->visible))
				$eventosCurso[$key] = $valueE;
		}

		$data['title'] = "Inicio";
		$data['proximos'] = $eventosMostrar;
		$data['curso'] = $eventosCurso;
		$data['logeado'] = $logeado;

		View::renderTemplate('header', $data);
		View::render('tracker/home', $data);
		View::renderTemplate('footer', $data);
	}

	public function maintenance()
	{
		$data['title'] = "Mantenimiento";

		View::renderTemplate('header', $data);
		View::render('tracker/maintenance', $data);
		View::renderTemplate('footer', $data);
	}
}

?>