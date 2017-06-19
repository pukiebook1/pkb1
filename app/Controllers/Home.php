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
		//$this->language->load("es");		
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
		$data['home1'] = $this->language->get('home1');
		$data['home2'] = $this->language->get('home2');
		$data['home3'] = $this->language->get('home3');
		$data['home4'] = $this->language->get('home4');
		$data['home5'] = $this->language->get('home5');
		$data['home5'] = $this->language->get('home5');
		$data['home6'] = $this->language->get('home6');
		$data['home7'] = $this->language->get('home7');
		$data['home8'] = $this->language->get('home8');
		$data['home9'] = $this->language->get('home9');
		$data['home10'] = $this->language->get('home10');
		$data['home11'] = $this->language->get('home11');
		$data['home12'] = $this->language->get('home12');
		$data['home13'] = $this->language->get('home13');
		$data['home14'] = $this->language->get('home14');
		$data['home15'] = $this->language->get('home15');
		$data['home16'] = $this->language->get('home16');
		$data['home17'] = $this->language->get('home17');
		$data['home18'] = $this->language->get('home18');
		$data['home19'] = $this->language->get('home19');

		

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