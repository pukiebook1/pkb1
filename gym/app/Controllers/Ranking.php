<?php
namespace Controllers;

use Core\View;
use Core\Controller;
use Models;
use Helpers\Url;
use Helpers\Paginator;

class Ranking extends Controller
{
	public function __construct()
	{
		parent::__construct();
		//$this->language->load('Ranking');
	}

	public function index($eventoId)
	{
		$eventos = new Models\Eventos();
		$perfiles = new Models\Perfiles();
		$evento = $eventos->getEvento($eventoId);

		if(!$evento)
		{
			Eventos::eventoNotFound();
			return;
		}

		$eventos->prepararEvento($evento);

		$logeado = Models\Cuentas::getSession();
		$persona = null;
		$esOrganizador = false;
		$esAdmin = false;
		$esJuez = false;
		$estoyPendiente = false;
		$estoy = false;

		if($logeado)
		{
			$persona = $logeado['persona'];
			$esAdmin = $persona->esAdmin;
			$esOrganizador = ($evento->creadorId == $persona->idPersona);
			$esJuez = ($evento->juezPrincipal == $persona->idPersona || $evento->juezSuplente == $persona->idPersona);
			$registro = $eventos->getRegistro($evento->id, $persona->idPersona);

			if($registro)
			{
				if($registro->aprobado)
					$estoy = $registro;
				else
					$estoyPendiente = true;
			}
		}

		if( !$evento->aprobado || !$evento->visible )
		{
			if( !($esOrganizador || $esAdmin ) )
			{
				Eventos::eventoNotPermission();
				return;
			}
		}

		$categorias = $eventos->getCategorias($evento->id);
		
		if(!$evento->registroTeam)
			$atletas = $eventos->getAtletasRegistrados($evento->id);
		else
			$atletas = $eventos->getEquiposRegistrados($evento->id);

		$jueces = $eventos->getJueces($evento->id);
		$perfiles->prepararCuentas($jueces);

		usort($categorias, "self::usortCategoriasPorNombre");

		$equipado = false;

		if($evento->openTeam)
		{
			if($logeado)
				$equipado = $eventos->getAtletaEquipado($evento->id, $persona->idPersona);

			foreach ($categorias as $key => $value)
			{
				$cateTeam = new \stdClass();
				$cateTeam->id = "team".$value->id;
				$cateTeam->nombre = $value->nombre." TEAM";
				$cateTeam->eventoId = $evento->id;

				$categorias[] = $cateTeam;
			}
		}

		$dataEvento = new \stdClass();	
		$dataEvento->categorias = $categorias;
		$dataEvento->atletas = $atletas;
		$dataEvento->jueces = $jueces;
		$data["dataEvento"] = $dataEvento;

		$data['title'] = $evento->nombre;
		$data["evento"] = $evento;
		$data["eventoHome"] = true;
		$data["estoy"] = $estoy;
		$data["equipado"] = $equipado;
		$data["estoyPendiente"] = $estoyPendiente;
		$data["logeado"] = $logeado;
		$data["esOrganizador"] = $esOrganizador;
		$data["esJuez"] = $esJuez;
		$data["esAdmin"] = $esAdmin;
		$data["fbableEvento"] = true;

		View::renderTemplate('header', $data);
		View::render('tracker/ranking', $data);
		View::renderTemplate('footerTabla', $data);
	}

	public function tablaResultados($eventoId, $categoriaId = 0, $showWODforced = 0)
	{
		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			Eventos::eventoNotFound();
			return;
		}

		if($evento->disciplina == 9)
		{
			self::tablaResultadosWeight($evento, $categoriaId, $showWODforced);
			return;
		}

		if(!is_int($categoriaId) && (strcmp($categoriaId, 'team') == 0) )
		{
			Eventos::categoriaNotFound($evento);
			return;
		}

		$categoria = $eventos->getCategoria($evento->id, $categoriaId);

		if(!$categoria)
		{
			Eventos::categoriaNotFound($evento);
			return;
		}
		
		$pages = new Paginator('20', 'p');

		$patrocinadores = $eventos->getPatrocinadores($evento->id);
		ksort($patrocinadores);

		$logeado = Models\Cuentas::getSession();
		$persona = null;
		$esOrganizador = false;
		$esAdmin = false;
		$esJuez = false;
		$estoyPendiente = false;
		$estoy = false;
		$miId = false;

		if($logeado)
		{
			$persona = $logeado['persona'];
			$miId = $persona->idPersona;
			$esAdmin = $persona->esAdmin;
			$esOrganizador = ($evento->creadorId == $persona->idPersona);
			$esJuez = ($evento->juezPrincipal == $persona->idPersona || $evento->juezSuplente == $persona->idPersona);
			$registro = $eventos->getRegistro($evento->id, $persona->idPersona);

			if($registro)
			{
				if($registro->aprobado)
					$estoy = $registro;
				else
					$estoyPendiente = true;

				if($registro->categoriaId != $categoriaId)
					$estoy = false;
			}
		}

		if( !$evento->aprobado || !$evento->visible )
		{
			if( !($esOrganizador || $esAdmin))
			{
				Eventos::eventoNotPermission();
				return;
			}
		}

		if( ($esAdmin || $esOrganizador) && ($showWODforced == 1) )
			$evento->wodsvisible = 1;

		$categorias = $eventos->getCategorias($evento->id);
		$wods = $eventos->getWodsCategoria($evento->id, $categoriaId);

		$result = $this->procesaResultados($evento, $categoria, $wods);
		$resCategoria = $result['resultados'];
		$atletas = $result['atletas'];

		if(isset($_GET['buscar']))
		{
			$string = trim($_GET['buscar']);

			if(!empty($string))
			{
				foreach ($atletas as $key => $value)
				{
					$strNombre = $value->nombre." ".$value->apellido;

					if(stripos($strNombre, $string) === FALSE)
					{
						unset($atletas[$key]);

						foreach ($resCategoria as $keyR => $valueR)
						{
							if($valueR->registroAtletaId == $key)
								unset($resCategoria[$keyR]);
						}
					}
				}
			}
		}

		$data['records'] = self::trimResultados($pages->getStart(), $pages->getPerPage(), $resCategoria);
		$pages->setTotal( count($resCategoria) );
		$data['pageLinks'] = $pages->pageLinks();

		$dataRanking = new \stdClass();
		$dataRanking->tablaResultados = $data['records'];
		$dataRanking->wods = $wods;
		$dataRanking->atletas = $atletas;
		$dataRanking->categorias = $categorias;
		$data["dataRanking"] = $dataRanking;

		$data['title'] = $categoria->nombre." (".$evento->nombre.")";
		$data["evento"] = $evento;
		$data["categorias"] = $categorias;
		$data["categoria"] = $categoria;
		$data["juecesRes"] = $juecesRes;
		
		$data["eventoTabla"] = true;
		$data['patrocinadores'] = $patrocinadores;
		$data['busqueda'] = $string;

		$data["estoy"] = $estoy;
		$data["estoyPendiente"] = $estoyPendiente;
		$data["logeado"] = $logeado;
		$data["esOrganizador"] = $esOrganizador;
		$data["esJuez"] = $esJuez;
		$data["esAdmin"] = $esAdmin;
		$data['miId'] = $miId;

		View::renderTemplate('headerNORESPONSIVE', $data);
		View::render('tracker/tabla', $data);
		View::renderTemplate('footerTabla', $data);
	}

	public function procesaResultados($evento, $categoria, &$wods)
	{
		$categoriaId = $categoria->id;

		$eventos = new Models\Eventos();
		$perfiles = new Models\Perfiles();
		$equipos = new Models\Equipos();
		$atletas = array();
		$resultados = array();

		if(!$evento->registroTeam)
		{
			$atletas = $eventos->getAtletasCategoria($evento->id, $categoriaId);
			$perfiles->prepararCuentas($atletas);
		}
		else
		{
			$atletas = $eventos->getEquiposCategoria($evento->id, $categoriaId);
			$equipos->prepararEquipos($atletas);
		}

		if(!$evento->registroTeam)
			$resultados = $eventos->getResultadosCategoria($evento->id, $categoriaId);
		else
			$resultados = $eventos->getResultadosEquipoCategoria($evento->id, $categoriaId);

		ksort($wods);
		ksort($atletas);
		ksort($resultados);
		$resCategoria = array();
		$juecesRes = array();

		foreach ($atletas as $keyA => $valueA)
		{
			if(empty($wods))
				$resultados[$keyA] = array();

			$orden = 1;

			foreach ($wods as $keyW => $valueW)
			{
				if($valueW->orden == null)
					$valueW->orden = $orden++;
				
				if(!isset($resultados[$keyA][$valueW->id]))
				{
					$resNew = new \stdClass();
					$resNew->registroAtletaId = $keyA;
					$resNew->resultado = 0;
					$resNew->fechaResultado = null;
					$resNew->wodId = $valueW->id;
					$resNew->eventoId = $valueA->eventoId;
					$resNew->pos = 0;
					$resNew->mayor = 0;
					$resNew->attempt = 0;
					$resNew->reps = 0;
					$resNew->tiebreak = 0;
					$resNew->judgedBy = 0;
					$resNew->orden = $valueW->orden;
					$resNew->tipoWOD = 0;
					$resNew->scaled = 0;
					$resNew->WOD = $valueW;
					$resNew->valid = false;

					$resultados[$keyA][$valueW->id] = $resNew;
				}
				else
				{
					$resultados[$keyA][$valueW->id]->orden = $valueW->orden;
					$resultados[$keyA][$valueW->id]->tipoWOD = $valueW->tipo;
					$resultados[$keyA][$valueW->id]->WOD = $valueW;

					//REVISAR Y AGREGAR PENALIZACIONES
					if($valueW->tipo == 3)
					{
						if($valueW->permPenalizacion)
						{
							$pen = ($valueW->repsRound - $resultados[$keyA][$valueW->id]->reps) * 1000;
							$resultados[$keyA][$valueW->id]->resultado = $resultados[$keyA][$valueW->id]->resultado + $pen;

							if($pen > 0)
								$resultados[$keyA][$valueW->id]->pen = $pen;
						}
					}

					if($resultados[$keyA][$valueW->id]->judgedBy > 0)
						$juecesRes[$resultados[$keyA][$valueW->id]->judgedBy] = null;
				}
			}

			$objAtl = new \stdClass();
			$objAtl->registroAtletaId = $keyA;
			$objAtl->resultados = $resultados[$keyA];

			$resCategoria[$keyA] = $objAtl;
		}

		foreach ($resCategoria as $key => $value)
		{
			ksort($value->resultados);
		}

		//Asignado resultados por wod $arr[catID][wodID][atlID]
		$resByWOD = array();
		foreach ($resCategoria as $keyA => $valueA)
		{
			foreach ($valueA->resultados as $keyR => $valueR)
			{
				$resByWOD[$valueR->wodId][$keyA] = $valueR;
			}
		}

		foreach ($resByWOD as $keyW => $valueW)
		{
			self::ordenarWOD($valueW, $wods[$keyW]);
			self::asignarPosicionWOD($valueW, $evento);
		}

		foreach ($resCategoria as $keyA => $valueA)
		{
			foreach ($valueA->resultados as $keyR => $valueR)
			{
				if($evento->disciplina == 11 || $evento->disciplina == 12)
					$valueA->puntaje += $valueR->ptos;	
				else
					$valueA->puntaje += $valueR->pos;
			}
		}

		self::ordenarCategoria($resCategoria, $evento);
		self::asignarPosicionCategoria($resCategoria);
		self::aplicarDesempateCategoria($resCategoria);
		self::ordenarCategoria($resCategoria, $evento);
		self::asignarPosicionCategoria($resCategoria);
		self::ordenarCategoria($resCategoria, $evento);

		usort($wods, "self::usortWodsPorOrden");

		foreach ($resCategoria as $key => $value)
		{
			usort($value->resultados, "self::usortWodsPorOrden");
		}

		$result = array();
		$result['resultados'] = $resCategoria;
		$result['atletas'] = $atletas;

		//print_r($resCategoria);

		return $result;
	}

	public function tablaResultadosTeam($eventoId, $categoriaId = 0, $showWODforced = 0)
	{
		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			Eventos::eventoNotFound();
			return;
		}

		$categoria = $eventos->getCategoria($evento->id, $categoriaId);
		$categoria->team = true;

		if(!$categoria)
		{
			Eventos::categoriaNotFound($evento);
			return;
		}
		
		$pages = new Paginator('20', 'p');

		$logeado = Models\Cuentas::getSession();
		$persona = null;
		$esOrganizador = false;
		$esAdmin = false;
		$esJuez = false;
		$estoyPendiente = false;
		$estoy = false;
		$miId = false;

		if($logeado)
		{
			$persona = $logeado['persona'];
			$miId = $persona->idPersona;
			$esAdmin = $persona->esAdmin;
			$esOrganizador = ($evento->creadorId == $persona->idPersona);
			$esJuez = ($evento->juezPrincipal == $persona->idPersona || $evento->juezSuplente == $persona->idPersona);
			$registro = $eventos->getRegistro($evento->id, $persona->idPersona);

			if($registro)
			{
				if($registro->aprobado)
					$estoy = $registro;
				else
					$estoyPendiente = true;

				if($registro->categoriaId != $categoriaId)
					$estoy = false;
			}
		}

		if( !$evento->aprobado || !$evento->visible )
		{
			if( !($esOrganizador || $esAdmin))
			{
				Eventos::eventoNotPermission();
				return;
			}
		}

		if( ($esAdmin || $esOrganizador) && ($showWODforced == 1) )
			$evento->wodsvisible = 1;

		$categorias = $eventos->getCategorias($evento->id);
		$wods = $eventos->getWodsCategoria($evento->id, $categoriaId);

		$resultadosIndividuales = $this->procesaResultados($evento, $categoria, $wods);
		$equipos = $eventos->getEquiposCategoria($evento->id, $categoriaId);
		$integrantes = $eventos->getIntegrantesEquipoEvento($evento->id, $categoriaId);

		$perfiles = new Models\Perfiles();
		$perfiles->prepararCuentas($equipos);

		$resCategoria = array();
		$resIndexado = array();
		$resultados = array();

		foreach ($resultadosIndividuales['resultados'] as $keyRA => $valueRA)
		{
			$resIndexado[$valueRA->registroAtletaId] = $valueRA;
			$tmpRes = array();
 
			foreach ($valueRA->resultados as $keyRES => $valueRES)
			{
				$tmpRes[$valueRES->wodId] = $valueRES;
			}

			unset($valueRA->resultados);
			$valueRA->resultados = $tmpRes;
		}

		//print_r($equipos);
		//return;

		foreach ($equipos as $keyE => $valueE)
		{
			if(empty($wods))
				$resultados[$keyE] = array();

			$orden = 1;

			foreach ($wods as $keyW => $valueW)
			{
				$res = $resIndexado;

				if($valueW->orden == null)
					$valueW->orden = $orden++;

				foreach ($integrantes as $keyIN => $valueIN)
				{

					//print_r($keyE);
					//return;

					if($valueIN->equipoId == $keyE)
					{
						if(!isset($resultados[$keyE][$valueW->id]))
						{
							$resultados[$keyE][$valueW->id] = new \stdClass();

					   		$resultados[$keyE][$valueW->id]->orden = $valueW->orden;
							$resultados[$keyE][$valueW->id]->resultado = 0;
							$resultados[$keyE][$valueW->id]->pos = 0;
							$resultados[$keyE][$valueW->id]->tipoWOD = $valueW->tipo;
							$resultados[$keyE][$valueW->id]->WOD = $valueW;
							$resultados[$keyE][$valueW->id]->wodId = $valueW->id;
							$resultados[$keyE][$valueW->id]->valid = false;
							$resultados[$keyE][$valueW->id]->individuales = array();
						}

						$resultados[$keyE][$valueW->id]->resultado += $res[$keyIN]->resultados[$valueW->id]->resultado;
						$resultados[$keyE][$valueW->id]->pos += $res[$keyIN]->resultados[$valueW->id]->pos;
						$resultados[$keyE][$valueW->id]->orden = $valueW->orden;
						$resultados[$keyE][$valueW->id]->individuales[$keyIN] = $res[$keyIN]->resultados[$valueW->id];
					}
				}
			}

			$objAtl = new \stdClass();
			$objAtl->registroAtletaId = $keyE;
			$objAtl->resultados = $resultados[$keyE];

			if(!empty($resultados[$keyE]))
				$resCategoria[$keyE] = $objAtl;
		}


		foreach ($resCategoria as $key => $value)
		{
			//if(!empty($value->resultados))
				ksort($value->resultados);
		}


		//Asignado resultados por wod $arr[catID][wodID][atlID]
		$resByWOD = array();
		foreach ($resCategoria as $keyA => $valueA)
		{
			foreach ($valueA->resultados as $keyR => $valueR)
			{
				$resByWOD[$valueR->wodId][$keyA] = $valueR;
			}
		}
/*
		print_r($resCategoria);
		return;
*/
		foreach ($resByWOD as $keyW => $valueW)
		{
			self::ordenarWOD($valueW, $wods[$keyW]);
			//self::asignarPosicionWOD($valueW, $evento);
		}

		foreach ($resCategoria as $keyA => $valueA)
		{
			foreach ($valueA->resultados as $keyR => $valueR)
			{
				if($evento->disciplina == 11 || $evento->disciplina == 12)
					$valueA->puntaje += $valueR->ptos;	
				else
					$valueA->puntaje += $valueR->pos;
			}
		}
		
		//print_r($equipos);
		//return;

		//$perfiles = new Models\Perfiles();
		//$perfiles->prepararCuentas($equipos);
		//$resultados = $eventos->getResultadosEquipoEventoCategoria($evento->id, $categoriaId);

		self::ordenarCategoria($resCategoria, $evento);
		$patrocinadores = $eventos->getPatrocinadores($evento->id);

		usort($categorias, "self::usortCategoriasPorNombre");

		$data['records'] = self::trimResultados($pages->getStart(), $pages->getPerPage(), $resCategoria);
		$pages->setTotal( count($resCategoria) );
		$data['pageLinks'] = $pages->pageLinks();

		$dataRanking = new \stdClass();
		$dataRanking->tablaResultados = $data['records'];
		$dataRanking->wods = $wods;
		$dataRanking->atletas = $equipos;
		$dataRanking->atletasIndividuales = $integrantes;
		$dataRanking->categorias = $categorias;
		$data["dataRanking"] = $dataRanking;

		$data['title'] = $categoria->nombre." TEAM(".$evento->nombre.")";
		$data["evento"] = $evento;
		$data["categorias"] = $categorias;
		$data["categoria"] = $categoria;
		$data["juecesRes"] = $juecesRes;
		
		$data["eventoTabla"] = true;
		$data['patrocinadores'] = $patrocinadores;
		$data['busqueda'] = $string;

		$data["estoy"] = $estoy;
		$data["estoyPendiente"] = $estoyPendiente;
		$data["logeado"] = $logeado;
		$data["esOrganizador"] = $esOrganizador;
		$data["esJuez"] = $esJuez;
		$data["esAdmin"] = $esAdmin;
		$data['miId'] = $miId;

		self::ordenarCategoria($resCategoria, $evento);
		self::asignarPosicionCategoria($resCategoria);

		usort($wods, "self::usortWodsPorOrden");

		foreach ($resCategoria as $key => $value)
		{
			usort($value->resultados, "self::usortWodsPorOrden");
		}

		//print_r($dataRanking);
		//return;

		//print_r($resultadosIndividuales);
		//return;

		View::renderTemplate('headerNORESPONSIVE', $data);
		View::render('tracker/tabla', $data);
		View::renderTemplate('footerTabla', $data);
	}

	public function tablaResultadosWeight($evento, $categoriaId = 0, $showWODforced = 0)
	{
		$eventos = new Models\Eventos();

		$categoria = $eventos->getCategoria($evento->id, $categoriaId);

		if(!$categoria)
		{
			Eventos::categoriaNotFound($evento);
			return;
		}
		
		$pages = new Paginator('20', 'p');

		$logeado = Models\Cuentas::getSession();
		$persona = null;
		$esOrganizador = false;
		$esAdmin = false;
		$esJuez = false;
		$estoyPendiente = false;
		$estoy = false;
		$miId = false;

		if($logeado)
		{
			$persona = $logeado['persona'];
			$miId = $persona->idPersona;
			$esAdmin = $persona->esAdmin;
			$esOrganizador = ($evento->creadorId == $persona->idPersona);
			$esJuez = ($evento->juezPrincipal == $persona->idPersona || $evento->juezSuplente == $persona->idPersona);
			$registro = $eventos->getRegistro($evento->id, $persona->idPersona);

			if($registro)
			{
				if($registro->aprobado)
					$estoy = $registro;
				else
					$estoyPendiente = true;

				if($registro->categoriaId != $categoriaId)
					$estoy = false;
			}

		}

		if( !$evento->aprobado || !$evento->visible )
		{
			if( !($esOrganizador || $esAdmin))
			{
				Eventos::eventoNotPermission();
				return;
			}
		}

		if( ($esAdmin || $esOrganizador) && ($showWODforced == 1) )
			$evento->wodsvisible = 1;

		$categorias = $eventos->getCategorias($evento->id);
		$wods = $eventos->getWodsCategoria($evento->id, $categoriaId);
		$atletas = $eventos->getAtletasCategoria($evento->id, $categoriaId);
		$perfiles = new Models\Perfiles();
		$perfiles->prepararCuentas($atletas);
		$resultados = $eventos->getResultadosCategoria($evento->id, $categoriaId);
		$patrocinadores = $eventos->getPatrocinadores($evento->id);

		usort($categorias, "self::usortCategoriasPorNombre");
		ksort($wods);
		ksort($atletas);
		ksort($resultados);
		ksort($patrocinadores);

		$resCategoria = array();

		foreach ($atletas as $keyA => $valueA)
		{
			if(empty($wods))
				$resultados[$keyA] = array();

			$orden = 1;

			foreach ($wods as $keyW => $valueW)
			{
				if($valueW->orden == null)
					$valueW->orden = $orden++;
			
				$mayorTmp = 0;
				$mayorAtt = 0;

				for($i=1; $i<=3; $i++)
				{

					if(!isset($resultados[$keyA][$valueW->id][$i]))
					{
						$resNew = new \stdClass();
						$resNew->id = 0;
						$resNew->registroAtletaId = $keyA;
						$resNew->resultado = 0;
						$resNew->wodId = $valueW->id;
						$resNew->eventoId = $valueA->eventoId;
						$resNew->pos = 0;
						$resNew->videoLink = null;
						$resNew->fecha = null;
						$resNew->attempt = $i;
						$resNew->orden = $valueW->orden;
						$resNew->mayor = false;
						$resNew->reps = 0;
						$resNew->tiebreak = 0;
						$resNew->judgedBy = 0;
						$resNew->tipoWOD = 0;
						$resNew->WOD = $valueW;
						$resNew->valid = false;


						$resultados[$keyA][$valueW->id][$i] = $resNew;
					}
					else
					{
						$resultados[$keyA][$valueW->id][$i]->orden = $valueW->orden;
						$resultados[$keyA][$valueW->id][$i]->mayor = false;
						$resultados[$keyA][$valueW->id][$i]->tipoWOD = $valueW->tipo;
						$resultados[$keyA][$valueW->id][$i]->WOD = $valueW;

						if($resultados[$keyA][$valueW->id][$i]->resultado >= $mayorTmp)
						{
							$mayorTmp = $resultados[$keyA][$valueW->id][$i]->resultado;
							$mayorAtt = $i;
						}
					}

				}

				if(isset($resultados[$keyA][$valueW->id][$mayorAtt]))
					$resultados[$keyA][$valueW->id][$mayorAtt]->mayor = true;

				ksort($resultados[$keyA][$valueW->id]);
			}

			$objAtl = new \stdClass();
			$objAtl->registroAtletaId = $keyA;
			$objAtl->bodyweight = $valueA->bodyweight;
			$objAtl->average = $valueA->average;
			$objAtl->resultados = $resultados[$keyA];

			$resCategoria[$keyA] = $objAtl;
		}

		foreach ($resCategoria as $key => $value)
		{
			ksort($value->resultados);
		}

		$resByWOD = array();
		foreach ($resCategoria as $keyA => $valueA)
		{
			foreach ($valueA->resultados as $keyR => $valueR)
			{
				for($i=1; $i<=3; $i++)
				{
					if($valueR[$i]->mayor)
						$resByWOD[$valueR->wodId][$keyA] = $valueR[$i];
				}
			}
		}

		foreach ($resByWOD as $keyW => $valueW)
		{			
			self::ordenarWOD($valueW, $wods[$keyW]);
			self::asignarPosicionWOD($valueW, $evento);
		}

		foreach ($resCategoria as $keyA => $valueA)
		{
			$valueA->total = 0;

			foreach ($valueA->resultados as $keyR => $valueR)
			{
				$valueA->puntaje += $valueR->pos;

				for($i=1; $i<=3; $i++)
				{
					if($valueR[$i]->mayor)
						$valueA->total += $valueR[$i]->resultado;
				}
			}

			if($valueA->bodyweight == 0)
				$valueA->average = 0;
			else
				$valueA->average =  $valueA->total / $valueA->bodyweight;
		}

		self::ordenarCategoriaWeight($resCategoria);
		self::asignarPosicionCategoriaWeight($resCategoria);
		self::ordenarCategoriaWeight($resCategoria);

		usort($wods, "self::usortWodsPorOrden");

		foreach ($resCategoria as $key => $value)
		{
			usort($value->resultados, "self::usortWodsPorOrdenWeight");
		}

		if(isset($_GET['buscar']))
		{
			$string = trim($_GET['buscar']);

			if(!empty($string))
			{
				foreach ($atletas as $key => $value)
				{
					$strNombre = $value->nombre." ".$value->apellido;

					if(stripos($strNombre, $string) === FALSE)
					{
						unset($atletas[$key]);

						foreach ($resCategoria as $keyR => $valueR)
						{
							if($valueR->registroAtletaId == $key)
								unset($resCategoria[$keyR]);
						}
					}
				}
			}
		}

		$data['records'] = self::trimResultados($pages->getStart(), $pages->getPerPage(), $resCategoria);
		$pages->setTotal( count($resCategoria) );
		$data['pageLinks'] = $pages->pageLinks();

		$dataRanking = new \stdClass();
		$dataRanking->tablaResultados = $data['records'];
		$dataRanking->wods = $wods;
		$dataRanking->atletas = $atletas;
		$dataRanking->categorias = $categorias;
		$data["dataRanking"] = $dataRanking;

		$data['title'] = $categoria->nombre." (".$evento->nombre.")";
		$data["evento"] = $evento;
		$data["categorias"] = $categorias;
		$data["categoria"] = $categoria;
		
		$data["eventoTabla"] = true;
		$data['patrocinadores'] = $patrocinadores;
		$data['busqueda'] = $string;

		$data["estoy"] = $estoy;
		$data["estoyPendiente"] = $estoyPendiente;
		$data["logeado"] = $logeado;
		$data["esOrganizador"] = $esOrganizador;
		$data["esJuez"] = $esJuez;
		$data["esAdmin"] = $esAdmin;
		$data['miId'] = $miId;

		View::renderTemplate('headerNORESPONSIVE', $data);
		View::render('tracker/tabla', $data);
		View::renderTemplate('footerTabla', $data);
	}

	private function trimResultados($start, $cant, $resultados = array())
	{
		$ret = array();

		if(count($resultados) >= $start)
		{
			$i = 0;
			foreach ($resultados as $key => $value)
			{
				if($i >= $start && $i < ($cant+$start))
				{
					$ret[$key] = $value;
				}
				$i++;
			}
		}

		return $ret;

	}

	public function usortWodsPorOrden($a, $b)
	{
		if($a->orden == $b->orden)
			return 0;

		if($a->orden < $b->orden)
			return -1;
		else
			return 1;
	}

	public function usortWodsPorOrdenWeight($a, $b)
	{
		if($a[1]->orden == $b[1]->orden)
			return 0;

		if($a[1]->orden < $b[1]->orden)
			return -1;
		else
			return 1;
	}

	public function liberarResultadosWeight($evento)
	{
		$eventos = new Models\Eventos();
		$cuentas = new Models\Perfiles();
		$resultados = $eventos->getResultadosWeight($evento->id);
		$atletas = $eventos->getAtletasRegistrados($evento->id);

		$resCategoria = array();

		foreach ($atletas as $keyA => $valueA)
		{
			$objAtl = new \stdClass();
			$objAtl->registroAtletaId = $keyA;
			$objAtl->average = 0;
			$objAtl->pos = 0;
			$objAtl->total = 0;
			$objAtl->resultados = $resultados[$keyA];

			if(isset($objAtl->resultados))
			{
				foreach ($objAtl->resultados as $keyR => $valueR)
				{
					foreach ($valueR as $keyN => $valueN)
					{
						$valueN->pos = null;
						$valueN->mayor = false;
					}
				}
			}
			else
			{
				$objAtl->resultados = array();
			}

			$resCategoria[] = $objAtl;
		}


		$ee = $cuentas->setPuntajesFinalesWeight($resCategoria);
		$eev = $eventos->abrirEvento($evento->id);

		$data['title'] = "Evento Abierto";
		$data['titulo'] = "El evento ha sido abierto correctamente";
		$data['href'] = DIR."evento/".$evento->internalURL;
		$data['boton'] = "Volver";

		View::renderTemplate('header', $data);
		View::render('tracker/mensaje', $data);
		View::renderTemplate('footer', $data);
	}

	public function liberarResultados($eventoId)
	{
		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			if(!$soloProc)
				Eventos::eventoNotFound();
		
			return;
		}

		if($evento->disciplina == 9)
		{
			self::liberarResultadosWeight($evento);
			return;
		}

		$cuentas = new Models\Perfiles();
		$resultados = $eventos->getResultados($evento->id);
		$atletas = $eventos->getAtletasRegistrados($evento->id);

		$resCategoria = array();

		foreach ($atletas as $keyA => $valueA)
		{
			$objAtl = new \stdClass();
			$objAtl->registroAtletaId = $keyA;
			$objAtl->puntaje = 0;
			$objAtl->pos = 0;
			$objAtl->mediaPos = 0;
			$objAtl->resultados = $resultados[$keyA];

			if(isset($objAtl->resultados))
			{
				foreach ($objAtl->resultados as $keyR => $valueR)
				{
					$valueR->pos = null;
				}
			}
			else
			{
				$objAtl->resultados = array();
			}

			$resCategoria[] = $objAtl;
		}

		$ee = $cuentas->setPuntajesFinales($resCategoria);
		$eev = $eventos->abrirEvento($evento->id);

		$data['title'] = "Evento Abierto";
		$data['titulo'] = "El evento ha sido abierto correctamente";
		$data['href'] = DIR."evento/".$evento->internalURL;
		$data['boton'] = "Volver";

		View::renderTemplate('header', $data);
		View::render('tracker/mensaje', $data);
		View::renderTemplate('footer', $data);
	}
	
	public function fijarResultados($eventoId, $soloProc = false)
	{
		$eventos = new Models\Eventos();
		$evento = $eventos->getEvento($eventoId);
		$eventos->prepararEvento($evento);

		if(!$evento)
		{
			if(!$soloProc)
				Eventos::eventoNotFound();
		
			return;
		}

		self::fijarResultadosProc($evento, $soloProc);
	}

	public function fijarResultadosEvento($evento, $soloProc = false)
	{
		if(!isset($evento) || empty($evento) )
		{
			if(!$soloProc)
				Eventos::eventoNotFound();

			return;
		}

		self::fijarResultadosProc($evento, $soloProc);
	}

	private function fijarResultadosProcWeight($evento, $soloProc = false)
	{
		$eventos = new Models\Eventos();
		$categorias = $eventos->getCategorias($evento->id);

		foreach ($categorias as $keyCat => $valueCat)
		{
			$wods = $eventos->getWodsCategoria($evento->id, $keyCat);
			$atletas = $eventos->getAtletasCategoria($evento->id, $keyCat);
			$resultados = $eventos->getResultadosCategoria($evento->id, $keyCat);

			$resCategoria = array();


			foreach ($atletas as $keyA => $valueA)
			{
				if(empty($wods))
					$resultados[$keyA] = array();

				$orden = 1;

				foreach ($wods as $keyW => $valueW)
				{
					if($valueW->orden == null)
						$valueW->orden = $orden++;
				
					$mayorTmp = 0;
					$mayorAtt = 0;

					for($i=1; $i<=3; $i++)
					{

						if(!isset($resultados[$keyA][$valueW->id][$i]))
						{
							$resNew = new \stdClass();
							$resNew->id = 0;
							$resNew->registroAtletaId = $keyA;
							$resNew->resultado = 0;
							$resNew->wodId = $valueW->id;
							$resNew->eventoId = $valueA->eventoId;
							$resNew->pos = 0;
							$resNew->videoLink = null;
							$resNew->fecha = null;
							$resNew->attempt = $i;
							$resNew->mayor = false;
							$resNew->reps = 0;
							$resNew->tiebreak = 0;
							$resNew->judgedBy = 0;
							$resNew->orden = $valueW->orden;
							$resNew->valid = false;

							$resultados[$keyA][$valueW->id][$i] = $resNew;
						}
						else
						{
							$resultados[$keyA][$valueW->id][$i]->orden = $valueW->orden;
							$resultados[$keyA][$valueW->id][$i]->mayor = false;
							$resultados[$keyA][$valueW->id][$i]->tipoWOD = $valueW->tipo;

							if($resultados[$keyA][$valueW->id][$i]->resultado >= $mayorTmp)
							{
								$mayorTmp = $resultados[$keyA][$valueW->id][$i]->resultado;
								$mayorAtt = $i;
							}
						}

					}

					if(isset($resultados[$keyA][$valueW->id][$mayorAtt]))
						$resultados[$keyA][$valueW->id][$mayorAtt]->mayor = true;

					ksort($resultados[$keyA][$valueW->id]);
				}

				$objAtl = new \stdClass();
				$objAtl->registroAtletaId = $keyA;
				$objAtl->bodyweight = $valueA->bodyweight;
				$objAtl->average = $valueA->average;
				$objAtl->resultados = $resultados[$keyA];

				$resCategoria[$keyA] = $objAtl;
			}

			foreach ($resCategoria as $key => $value)
			{
				ksort($value->resultados);
			}

			$resByWOD = array();
			foreach ($resCategoria as $keyA => $valueA)
			{
				foreach ($valueA->resultados as $keyR => $valueR)
				{
					for($i=1; $i<=3; $i++)
					{
						if($valueR[$i]->mayor)
							$resByWOD[$valueR->wodId][$keyA] = $valueR[$i];
					}
				}
			}

			foreach ($resByWOD as $keyW => $valueW)
			{			
				self::ordenarWOD($valueW, $wods[$keyW]);
				self::asignarPosicionWOD($valueW, $evento);
			}

			foreach ($resCategoria as $keyA => $valueA)
			{
				$valueA->total = 0;

				foreach ($valueA->resultados as $keyR => $valueR)
				{
					$valueA->puntaje += $valueR->pos;
					
					for($i=1; $i<=3; $i++)
					{
						if($valueR[$i]->mayor)
							$valueA->total += $valueR[$i]->resultado;
					}
				}

				if($valueA->bodyweight == 0)
					$valueA->average = 0;
				else
					$valueA->average =  $valueA->total / $valueA->bodyweight;
			}

			self::ordenarCategoriaWeight($resCategoria);
			self::asignarPosicionCategoriaWeight($resCategoria);
			self::ordenarCategoriaWeight($resCategoria);

			$cuentas = new Models\Perfiles();
			$ee = $cuentas->setPuntajesFinalesWeight($resCategoria);
		}

		$eev = $eventos->terminarEvento($evento->id);

		if(!$soloProc)
		{
			$data['title'] = "Evento Cerrado";
			$data['titulo'] = "El evento ha sido cerrado correctamente";
			$data['href'] = DIR."evento/".$evento->internalURL;
			$data['boton'] = "Volver";

			View::renderTemplate('header', $data);
			View::render('tracker/mensaje', $data);
			View::renderTemplate('footer', $data);
		}
	}

	private function fijarResultadosProc($evento, $soloProc = false)
	{
		if($evento->disciplina == 9)
		{
			self::fijarResultadosProcWeight($evento, $soloProc);
			return;
		}

		$eventos = new Models\Eventos();
		$categorias = $eventos->getCategorias($evento->id);

		foreach ($categorias as $keyCat => $valueCat)
		{
			$wods = $eventos->getWodsCategoria($evento->id, $keyCat);
			$atletas = $eventos->getAtletasCategoria($evento->id, $keyCat);
			$resultados = $eventos->getResultadosCategoria($evento->id, $keyCat);

			$resCategoria = array();

			foreach ($atletas as $keyA => $valueA)
			{
				if(empty($wods))
					$resultados[$keyA] = array();
				
				foreach ($wods as $keyW => $valueW)
				{
					if(!isset($resultados[$keyA][$valueW->id]))
					{
						$resNew = new \stdClass();
						$resNew->registroAtletaId = $keyA;
						$resNew->resultado = 0;
						$resNew->fechaResultado = null;
						$resNew->wodId = $valueW->id;
						$resNew->eventoId = $valueA->eventoId;
						$resNew->pos = 0;
						$resNew->mayor = 0;
						$resNew->attempt = 0;
						$resNew->reps = 0;
						$resNew->tiebreak = 0;
						$resNew->judgedBy = 0;
						$resNew->orden = $valueW->orden;
						$resNew->tipoWOD = 0;
						$resNew->valid = false;

						$resultados[$keyA][$valueW->id] = $resNew;
					}
				}

				$objAtl = new \stdClass();
				$objAtl->registroAtletaId = $keyA;
				$objAtl->resultados = $resultados[$keyA];

				$resCategoria[$keyA] = $objAtl;
			}

			$resByWOD = array();
			foreach ($resCategoria as $keyA => $valueA)
			{
				foreach ($valueA->resultados as $keyR => $valueR)
				{
					$resByWOD[$valueR->wodId][$keyA] = $valueR;
				}
			}

			foreach ($resByWOD as $keyW => $valueW)
			{
				self::ordenarWOD($valueW, $wods[$keyW]);
				self::asignarPosicionWOD($valueW, $evento);
			}

			foreach ($resCategoria as $keyA => $valueA)
			{
				foreach ($valueA->resultados as $keyR => $valueR)
				{
					if($evento->disciplina == 11 || $evento->disciplina == 12)
						$valueA->puntaje += $valueR->ptos;	
					else
						$valueA->puntaje += $valueR->pos;
				}
			}

			self::ordenarCategoria($resCategoria, $evento);
			self::asignarPosicionCategoria($resCategoria);
			self::aplicarDesempateCategoria($resCategoria);
			self::ordenarCategoria($resCategoria, $evento);
			self::asignarPosicionCategoria($resCategoria);

			$cuentas = new Models\Perfiles();
			$ee = $cuentas->setPuntajesFinales($resCategoria);
		}

		$eev = $eventos->terminarEvento($evento->id);

		if(!$soloProc)
		{
			$data['title'] = "Evento Cerrado";
			$data['titulo'] = "El evento ha sido cerrado correctamente";
			$data['href'] = DIR."evento/".$evento->internalURL;
			$data['boton'] = "Volver";

			View::renderTemplate('header', $data);
			View::render('tracker/mensaje', $data);
			View::renderTemplate('footer', $data);
		}
	}

	private function ordenarWOD(&$resultadosWOD, $WOD)
	{
		if($WOD->tipo == 1 || $WOD->tipo == 3 || $WOD->tipo == 8)
			usort($resultadosWOD, "self::usortWODPorTiempo");
		else
			usort($resultadosWOD, "self::usortWODPorPuntos");
	}

	private function usortWODPorPuntos($a, $b)
	{
		if(!$a->valid && $b->valid)
			return 1;

		if(!$b->valid && $a->valid)
			return -1;

		if(!$a->valid && !$b->valid)
			return 0;

		if($a->scaled && !$b->scaled)
			return 1;

		if(!$a->scaled && $b->scaled)
			return -1;

		if ($a->resultado == $b->resultado)
		{
			if( ($a->WOD->tipo == 4) && ($a->WOD->permTieBreak) )
			{
				if ($a->tiebreak == $b->tiebreak)
					return 0;

				return ($a->tiebreak < $b->tiebreak) ? -1 : 1;
			}

			if( ($a->WOD->tipo == 9) && ($a->WOD->permTieBreak) )
			{
				$resA = $a->resultado / $a->tiebreak;
				$resB = $b->resultado / $b->tiebreak;
				$epsilon = 0.0001;

				if(abs($resA-$resB) < $epsilon)
					return 0;

				return ($resA > $resB) ? -1 : 1;
			}
			return 0;
		}

		return ($a->resultado > $b->resultado) ? -1 : 1;
	}

	private function usortWODPorTiempo($a, $b)
	{
		if(!$a->valid && $b->valid)
			return 1;

		if(!$b->valid && $a->valid)
			return -1;

		if(!$a->valid && !$b->valid)
			return 0;

		if($a->scaled && !$b->scaled)
			return 1;

		if(!$a->scaled && $b->scaled)
			return -1;

		if ($a->resultado == $b->resultado)
		{
			if( ($a->WOD->tipo == 3) && ($a->WOD->permPenalizacion) )
			{
				if ($a->reps == $b->reps)
				{
					if($a->WOD->permTieBreak)
					{
						if ($a->tiebreak == $b->tiebreak)
							return 0;

						return ($a->tiebreak < $b->tiebreak) ? -1 : 1;
					}

					return 0;
				}

				return ($a->reps > $b->reps) ? -1 : 1;
			}

			return 0;
		}
		
		return ($a->resultado < $b->resultado) ? -1 : 1;
	}

	private function ordenarCategoria(&$resultadosCategoria, $evento)
	{
		if($evento->disciplina == 11 ||$evento->disciplina == 12)
			usort($resultadosCategoria, 'self::usortCategoriaPorPuntosTabla');
		else
			usort($resultadosCategoria, 'self::usortCategoriaPorPuntos');
	}

	private function ordenarCategoriaWeight(&$resultadosCategoria)
	{
		usort($resultadosCategoria, 'self::usortCategoriaPorAverage');
	}

	private function asignarPosicionCategoria($tabla)
	{
		$pos = 1;
		$contador = 1;
		$tam = count($tabla);

		for($i=0; $i<$tam; $i++)
		{
			$set = $pos;

			if($i > 0)
			{
				if($tabla[$i]->puntaje != $tabla[$i-1]->puntaje)
				{
					$set = $contador;
					$pos = $contador;
				}
				else
				{
					$a = $tabla[$i]->mediaPos;
					$b = $tabla[$i-1]->mediaPos;
					$epsilon = 0.001;

					if(abs($a-$b) > $epsilon)
					{
						$set = $contador;
						$pos = $contador;
					}
				}
			}

			$tabla[$i]->pos = $set;
			$contador++;
		}
	}

	private function asignarPosicionCategoriaWeight($tabla)
	{
		$pos = 1;
		$contador = 1;
		$tam = count($tabla);

		for($i=0; $i<$tam; $i++)
		{
			$set = $pos;

			if($i > 0)
			{
				if($tabla[$i]->average != $tabla[$i-1]->average)
				{
					$set = $contador;
					$pos = $contador;
				}
			}

			$tabla[$i]->pos = $set;
			$contador++;
		}
	}	

	private function aplicarDesempateCategoria($tabla)
	{
		$tam = count($tabla);
		$repetidos = array();

		for($i=1; $i<$tam; $i++)
		{   
			if($tabla[$i]->pos == $tabla[$i-1]->pos)
			{
				$repetidos[$tabla[$i]->pos][] = $tabla[$i];
				$repetidos[$tabla[$i]->pos][] = $tabla[$i-1];
			}
		}

		foreach ($repetidos as $keyA => $valueA)
		{
			foreach ($valueA as $keyB => $valueB)
			{
				$sumaInversos = 0;

				foreach ($valueB->resultados as $keyW => $valueW)
				{
					$sumaInversos += (1/$valueW->pos);
				}

				$valueB->mediaPos = $sumaInversos;
			}
		}
	}

	private function asignarPosicionWOD($resultadosWOD, $evento)
	{
		$pto100_1 = array(1 => 100, 94, 88, 84, 80,76,72,68,64,60,58,56,54,52,50,48,46,44,42,40,38,36,34,32,30,28,26,24,22,20,18,16,14,13,10,8,6,4,2,0 );
		$pto50_1 = array(1 => 50, 47,44,42,40,38,36,34,32,30,29,28,27,26,25,24,23,22,21,20,19,18,17,16,15,14,13,12,11,10,9,8,7,6,5,4,3,2,1,0);
		$pto100_2 = array(1 => 100, 95,90,85,80,75,73,71,69,67,65,63,61,59,57,55,53,51,49,47,45,43,41,39,37,35,33,31,29,27,26,25,24,23,22,21,20,19,18,17,16,15,14,13,12,11,10,9,8,7);

		$pos = 1;
		$contador = 1;
		$a = $resultadosWOD;
		$tam = count($a);

		for($i=0; $i<$tam; $i++)
		{   

			$set = $pos;

			if($i>0)
			{
				if($a[$i]->scaled != $a[$i-1]->scaled)
				{
					$set = $contador;
					$pos = $contador;
				}
				else
				{
					if($a[$i]->resultado != $a[$i-1]->resultado)
					{
						$set = $contador;
						$pos = $contador;
					}
					else
					{
						if($a[$i]->WOD->permPenalizacion && ($a[$i]->reps != $a[$i-1]->reps) )
						{
							$set = $contador;
							$pos = $contador;
						}
						else
						{
							if( $a[$i]->WOD->permTieBreak && ( $a[$i]->tiebreak != $a[$i-1]->tiebreak) )
							{
								$set = $contador;
								$pos = $contador;
							}
							else
								$set = $pos;
						}
					}
				}
			}

			if($evento->disciplina == 11)
			{
				if($set > 100)
					$pto100_1[$set] = 0;

				$a[$i]->ptos = $pto100_1[$set];

				if(!$a[$i]->valid)
					$a[$i]->ptos = 0;
			}

			if($evento->disciplina == 12)
			{
				if($set > 100)
					$pto100_2[$set] = 0;

				$a[$i]->ptos = $pto100_2[$set];

				if(!$a[$i]->valid)
					$a[$i]->ptos = 0;
			}


			$a[$i]->pos = $set;
			$contador++;
		}
	}

	private function usortCategoriaPorPuntos($a, $b)
	{
		if ($a->puntaje == $b->puntaje)
		{
			if($a->mediaPos == $b->mediaPos)
				return 0;
			else
				return ($a->mediaPos > $b->mediaPos) ? -1 : 1;
		}

		return ($a->puntaje < $b->puntaje) ? -1 : 1;
	}

	private function usortCategoriaPorPuntosTabla($a, $b)
	{
		if ($a->puntaje == $b->puntaje)
		{
			if($a->mediaPos == $b->mediaPos)
				return 0;
			else
				return ($a->mediaPos < $b->mediaPos) ? -1 : 1;
		}

		return ($a->puntaje > $b->puntaje) ? -1 : 1;
	}

	private function usortCategoriaPorAverage($a, $b)
	{
		if ($a->average == $b->average)
				return 0;

		return ($a->average > $b->average) ? -1 : 1;
	}

	public function usortCategoriasPorNombre($a, $b)
	{
		$comp = strcasecmp($a->nombre, $b->nombre);

		if($comp == 0)
			return 0;

		if($comp < 0)
			return -1;
		else
			return 1;
	}

	public function fijarResultadosVencidos()
	{
		$eventos = new Models\Eventos();
		$cerrar = $eventos->getEventosCerrar();
		$eventos->prepararEventos($cerrar);

		$eventosACerrar = count($cerrar);
		$eventosCerrados = 0;

		foreach ($cerrar as $key => $value)
		{
			self::fijarResultadosEvento($value, true);
			$eventosCerrados++;
		}

		print($eventosCerrados."/".$eventosACerrar." eventos cerrados");
	}

	public function fijarResultadosVencidosOpen()
	{
		$eventos = new Models\Eventos();
		$cerrar = $eventos->getEventosOpenCerrar();
		$eventos->prepararEventos($cerrar);

		$eventosACerrar = count($cerrar);
		$eventosCerrados = 0;

		foreach ($cerrar as $key => $value)
		{
			self::fijarResultadosEvento($value, true);
			$eventosCerrados++;
		}

		print($eventosCerrados."/".$eventosACerrar." eventos open cerrados");
	}
}

?>