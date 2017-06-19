<?php
namespace Controllers;

use Core\View;
use Core\Controller;
use Models;

class Box extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	private static function showInTemplate($data, $info = null)
	{
		View::renderTemplate('header', $data);
		View::render('tracker/mensaje', $data);
		View::renderTemplate('footer', $data);
	}

	public static function boxNotFound()
	{
		$data['title'] = "Box No Encontrado";
		$data['titulo'] = "Box No Encontrado";
		$data['mensaje'] = "No se encontr&oacute; el box al cual intentas acceder.";
		$data['href'] = DIR;
		$data['boton'] = "Inicio";

		self::showInTemplate($data);
	}

	public function index($boxId)
	{
		$boxes = new Models\Boxes();
		$box = $boxes->getBox($boxId);

		if(!$box)
		{
			self::boxNotFound();
			return;
		}

		$boxes->prepararBox($box);

		$logeado = Models\Cuentas::getSession();
		$persona = null;
		$esOrganizador = false;
		$esAdmin = false;

		if($logeado)
		{
			$persona = $logeado['persona'];
			$esAdmin = $persona->esAdmin;
			$esOrganizador = ($box->creadorId == $persona->idPersona);
		}

		$atletas = $boxes->getAtletasBox($box->id);
		$perfiles = new Models\Perfiles();
		$perfiles->prepararCuentas($atletas);

		$data['title'] = $box->nombre;
		$data['box'] = $box;
		$data['atletas'] = $atletas;
		$data["logeado"] = $logeado;
		$data["esOrganizador"] = $esOrganizador;
		$data["esAdmin"] = $esAdmin;
		$data["fbableEvento"] = true;

		View::renderTemplate('header', $data);
		View::render('tracker/box', $data);
		View::renderTemplate('footer', $data);
	}
}

?>