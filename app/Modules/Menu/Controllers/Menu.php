<?php 
namespace Modules\Menu\Controllers;

use Core\View;
use Core\Controller;
use Helpers\Url;
use Helpers\Gump;
use Helpers\CsrfNew as Csrf;
use Helpers\Password;
use Models;
 
require 'requirelanguage.php';

class Menu extends Controller
{
	public static $logeado = false;
	public static $cuenta = null;

	public function __construct()
	{
		parent::__construct();

		if(!self::$cuenta)
		{
			$sesion = Models\Cuentas::getSession();

			if($sesion)
			{
				self::$cuenta = $sesion['persona'];
				self::$logeado = true;
			}
		}
	}


	public function menuCuenta($data = array())
	{



		if (self::$logeado)
		{
			echo "


			<li class=\"dropdown\">
				<a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" role=\"button\" aria-haspopup=\"true\" aria-expanded=\"false\">
					<i class=\"fa fa-user\"></i> ".self::$cuenta->nombre." ".self::$cuenta->apellido."<span class=\"caret\"></span>
				</a>

				<ul class=\"dropdown-menu\">";
		}
		else
		{
			echo "
			<li class=\"dropdown\">
				<a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" role=\"button\" aria-haspopup=\"true\" aria-expanded=\"false\">
					<i class=\"fa fa-user\"></i> Cuenta <span class=\"caret\"></span>
				</a>

				<ul class=\"dropdown-menu\">";
		}

		if (self::$logeado)
		{
	        echo "<li class=\"dropdown-header\">Creaci&oacute;n</li>";
			echo "<li><a href=\"".DIR."cuenta/crearevento\"><i class=\"fa fa-plus-circle\" aria-hidden=\"true\"></i> Crear evento</a></li>";
			echo "<li><a href=\"".DIR."cuenta/equipo/preCrear\"><i class=\"fa fa-plus-circle\" aria-hidden=\"true\"></i> Crear equipo</a></li>";
			echo "<li><a href=\"".DIR."cuenta/crearbox\"><i class=\"fa fa-plus-circle\" aria-hidden=\"true\"></i> Crear box</a></li>";
						
			if(self::$cuenta->esAdmin)
			{
				echo "<li role=\"separator\" class=\"divider\"></li>";
	            echo "<li class=\"dropdown-header\">Administrador</li>";
				echo "<li><a href=\"".DIR."admin/reseteoclaves\"><i class=\"fa fa-briefcase\"></i> Reseteo de Claves</a></li>";
				echo "<li><a href=\"".DIR."admin/registrousuarios\"><i class=\"fa fa-briefcase\"></i> Registrar Usuarios</a></li>";
				
			}

			echo "<li role=\"separator\" class=\"divider\"></li>";
	        echo "<li class=\"dropdown-header\">Eventos</li>";
			echo "<li><a href=\"".DIR."cuenta/eventosorg\"><i class=\"fa fa-trophy\" aria-hidden=\"true\"></i> Eventos organizados</a></li>";
			echo "<li><a href=\"".DIR."cuenta/eventosjuez\"><i class=\"fa fa-trophy\" aria-hidden=\"true\"></i> Eventos como juez</a></li>";

			echo "<li role=\"separator\" class=\"divider\"></li>";
	        echo "<li class=\"dropdown-header\">Cuenta</li>";

			//echo "<li><a href=\"".DIR."cuenta/registrarseevento\">Registrarse en evento</a></li>";
			echo "<li><a href=\"".DIR."cuenta\"><i class=\"fa fa-user\" aria-hidden=\"true\"></i> Mi cuenta</a></li>";
			echo "<li><a href=\"".DIR."cuenta/misequipos\"><i class=\"fa fa-user\" aria-hidden=\"true\"></i> Mis equipos</a></li>";
			echo "<li><a href=\"".DIR."cuenta/misboxes\"><i class=\"fa fa-user\" aria-hidden=\"true\"></i> Mis boxes</a></li>";
			echo "<li role=\"separator\" class=\"divider\"></li>";
			echo "<li><a href=\"".DIR."salir\"><i class=\"fa fa-sign-out\" aria-hidden=\"true\"></i> Salir</a></li>";
		}
		else
		{
			echo "<li><a href=\"".DIR."acceder\"><i class=\"fa fa-sign-in\" aria-hidden=\"true\"></i> Acceder</a></li>";
			echo "<li><a href=\"".DIR."registrar\"><i class=\"fa fa-user-plus\" aria-hidden=\"true\"></i> Registrarse</a></li>";
		}


		echo "
				</ul>
			</div>
		</li>";
	}

	public function menuLang($data = array())
	{
		if(isset($data['evento']) && !empty($data['evento']))
		{
			if(!empty($data['evento']->customurl))
				$data['evento']->internalURL = $data['evento']->customurl;
			else
				$data['evento']->internalURL = $data['evento']->id;
		}

		if (isset($data['evento']) && $data['evento']->disciplina == 10 && $data['estoy'])
		{
				echo "
				<li class=\"dropdown\">
					<a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" role=\"button\" aria-haspopup=\"true\" aria-expanded=\"false\">
						<i class=\"fa fa-user\"></i> Participante<span class=\"caret\"></span>
					</a>

					<ul class=\"dropdown-menu\">";

					if(isset($data['estoy']))
					{
						echo "		<li><a href=\"".DIR."cuenta/modevento/".$data['evento']->internalURL."/resultados/".$data['estoy']->categoriaId."\"><i class=\"fa fa-plus-square\" aria-hidden=\"true\"></i> Registrar mi resultado</a></li>";
						
						if($data['evento']->openTeam)
						{
							if(!$data['equipado'])
								echo "		<li><a href=\"".DIR."cuenta/modevento/".$data['evento']->internalURL."/crearEquipo/\"><i class=\"fa fa-plus-square\" aria-hidden=\"true\"></i> Crear Equipo para este Evento</a></li>";
							else
								echo "		<li><a href=\"".DIR."equipo/".$data['equipado']->equipoId."\"><i class=\"fa fa-users\" aria-hidden=\"true\"></i> Ver Mi Equipo</a></li>";
						}
					}

				echo "</ul></li>";
		}

		if (isset($data['evento']) && !$data['evento']->eventoFinalizado && ($data['esJuez'] || self::$cuenta->esAdmin || $data['esOrganizador']))
		{
			echo"
				<li class=\"dropdown\">
					<a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" role=\"button\" aria-haspopup=\"true\" aria-expanded=\"false\">
						<i class=\"fa fa-gavel\" aria-hidden=\"true\"></i> Juez<span class=\"caret\"></span>
					</a>

					<ul class=\"dropdown-menu\">";

						if($data['evento']->disciplina != 10)
							echo "<li><a href=\"".DIR."cuenta/modevento/".$data['evento']->internalURL."/resultados\"><i class=\"fa fa-plus-square\" aria-hidden=\"true\"></i> Ingresar resultados</a></li>";
						else
						{
							if($data['estoy'] || $data['esJuez'] || self::$cuenta->esAdmin || $data['esOrganizador'])
							{
								echo "		<li><a href=\"".DIR."cuenta/modevento/".$data['evento']->internalURL."/resultados/\"><i class=\"fa fa-plus-square\" aria-hidden=\"true\"></i> Registrar resultados</a></li>";
							}
						}

			echo "</ul></li>";
		}
	}

	public function menuCategorias($data = array())
	{
		if(isset($data['evento']) && !empty($data['evento']) && isset($data['categorias']))
		{
			if(isset($data['evento']) && !empty($data['evento']))
			{
				if(!empty($data['evento']->customurl))
					$data['evento']->internalURL = $data['evento']->customurl;
				else
					$data['evento']->internalURL = $data['evento']->id;
			}

			echo "
			<li class=\"dropdown\">
				<a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" role=\"button\" aria-haspopup=\"true\" aria-expanded=\"false\">
					<i class=\"fa fa-list-ol\"></i> Categor&iacute;as<span class=\"caret\"></span>
				</a>

				<ul class=\"dropdown-menu\">";

					foreach ($data['categorias'] as $key => $value)
					{
						echo "<li><a href=\"".DIR."evento/".$data['evento']->internalURL."/".$value->id."\"><i class=\"fa fa-dot-circle-o\" aria-hidden=\"true\"></i> ".$value->nombre."</a></li>";
					}

					if(!$data['evento']->registroTeam && $data['evento']->openTeam)
					{
						foreach ($data['categorias'] as $key => $value)
						{
							echo "<li><a href=\"".DIR."evento/".$data['evento']->internalURL."/team".$value->id."\"><i class=\"fa fa-circle\" aria-hidden=\"true\"></i> ".$value->nombre." (TEAM)</a></li>";
						}
					}

			echo "</ul></li>";
		}
	}

	public function menuJuez($data = array())
	{
		if(isset($data['evento']) && !empty($data['evento']))
		{
			if(!empty($data['evento']->customurl))
				$data['evento']->internalURL = $data['evento']->customurl;
			else
				$data['evento']->internalURL = $data['evento']->id;
		}

		if (isset($data['evento']) && $data['evento']->disciplina == 10 && $data['estoy'])
		{
				echo "
				<li class=\"dropdown\">
					<a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" role=\"button\" aria-haspopup=\"true\" aria-expanded=\"false\">
						<i class=\"fa fa-user\"></i> Participante<span class=\"caret\"></span>
					</a>

					<ul class=\"dropdown-menu\">";

					if(isset($data['estoy']))
					{
						echo "		<li><a href=\"".DIR."cuenta/modevento/".$data['evento']->internalURL."/resultados/".$data['estoy']->categoriaId."\"><i class=\"fa fa-plus-square\" aria-hidden=\"true\"></i> Registrar mi resultado</a></li>";
						
						if($data['evento']->openTeam)
						{
							if(!$data['equipado'])
								echo "		<li><a href=\"".DIR."cuenta/modevento/".$data['evento']->internalURL."/crearEquipo/\"><i class=\"fa fa-plus-square\" aria-hidden=\"true\"></i> Crear Equipo para este Evento</a></li>";
							else
								echo "		<li><a href=\"".DIR."equipo/".$data['equipado']->equipoId."\"><i class=\"fa fa-users\" aria-hidden=\"true\"></i> Ver Mi Equipo</a></li>";
						}
					}

				echo "</ul></li>";
		}

		if (isset($data['evento']) && !$data['evento']->eventoFinalizado && ($data['esJuez'] || self::$cuenta->esAdmin || $data['esOrganizador']))
		{
			echo"
				<li class=\"dropdown\">
					<a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" role=\"button\" aria-haspopup=\"true\" aria-expanded=\"false\">
						<i class=\"fa fa-gavel\" aria-hidden=\"true\"></i> Juez<span class=\"caret\"></span>
					</a>

					<ul class=\"dropdown-menu\">";

						if($data['evento']->disciplina != 10)
							echo "<li><a href=\"".DIR."cuenta/modevento/".$data['evento']->internalURL."/resultados\"><i class=\"fa fa-plus-square\" aria-hidden=\"true\"></i> Ingresar resultados</a></li>";
						else
						{
							if($data['estoy'] || $data['esJuez'] || self::$cuenta->esAdmin || $data['esOrganizador'])
							{
								echo "		<li><a href=\"".DIR."cuenta/modevento/".$data['evento']->internalURL."/resultados/\"><i class=\"fa fa-plus-square\" aria-hidden=\"true\"></i> Registrar resultados</a></li>";
							}
						}

			echo "</ul></li>";
		}
	}

	public function menu($data = array())
	{
		if(isset($data['evento']) && !empty($data['evento']))
		{
			if(!empty($data['evento']->customurl))
				$data['evento']->internalURL = $data['evento']->customurl;
			else
				$data['evento']->internalURL = $data['evento']->id;
		}

		if(isset($data['evento']) && !empty($data['evento']) && !$data['eventoHome'])
		{
			echo "<li><a href=\"".DIR."evento/".$data['evento']->internalURL."\">Evento</a></li>";
		}

		if(isset($data['evento']) && !empty($data['evento']) && $data['eventoHome'])
		{
			if (!$data['estoy'] && !$data['estoyPendiente'] && !$data['evento']->eventoFinalizado && $data['evento']->permiteRegistros)
			{
				echo "<li><a href=\"".DIR."cuenta/registrarseevento/".$data['evento']->internalURL."\">Registrarse en este evento</a></li>";
			}
		}

		if(isset($data['evento']) && !empty($data['evento']) && $data['eventoTabla'])
		{
			self::menuCategorias($data);
		}

		if (isset($data['evento']) && ( ($data['esOrganizador'] && !$data['evento']->eventoFinalizado)  || self::$cuenta->esAdmin) )
		{
			echo "
				<li class=\"dropdown\">
					<a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" role=\"button\" aria-haspopup=\"true\" aria-expanded=\"false\">
						<i class=\"fa fa-cog\" aria-hidden=\"true\"></i> Administraci&oacute;n<span class=\"caret\"></span>
					</a>

					<ul class=\"dropdown-menu\">";

			echo "<li><a href=\"".DIR."cuenta/modevento/".$data['evento']->internalURL."/info\"><i class=\"fa fa-pencil\" aria-hidden=\"true\"></i> Modificar Evento</a></li>";
			echo "<li><a href=\"".DIR."cuenta/modevento/".$data['evento']->internalURL."/categorias\"><i class=\"fa fa-pencil\" aria-hidden=\"true\"></i> Modificar Categor&iacute;as</a></li>";

			if($data['evento']->disciplina != 9)
			echo "<li><a href=\"".DIR."cuenta/modevento/".$data['evento']->internalURL."/wods\"><i class=\"fa fa-pencil\" aria-hidden=\"true\"></i> Modificar WODs</a></li>";
			if($data['evento']->disciplina != 9)
			echo "<li><a href=\"".DIR."cuenta/modevento/".$data['evento']->internalURL."/relaciones\"><i class=\"fa fa-pencil\" aria-hidden=\"true\"></i> Modificar Categoria/WOD</a></li>";
		
			echo "<li><a href=\"".DIR."cuenta/modevento/".$data['evento']->internalURL."/patrocinadores\"><i class=\"fa fa-pencil\" aria-hidden=\"true\"></i> Modificar Patrocinadores</a></li>";

			echo "<li class=\"divider\"></li>";
			echo "<li><a href=\"".DIR."cuenta/modevento/".$data['evento']->internalURL."/participantesaprobar\"><i class=\"fa fa-user-plus\" aria-hidden=\"true\"></i> Aprobar Participantes</a></li>";
			echo "<li><a href=\"".DIR."cuenta/modevento/".$data['evento']->internalURL."/participantesaprobados\"><i class=\"fa fa-users\" aria-hidden=\"true\"></i> Participantes Registrados</a></li>";

			if($data['evento']->disciplina == 9)
				echo "<li><a href=\"".DIR."cuenta/modevento/".$data['evento']->internalURL."/participantespesos\"><i class=\"fa fa-plus-square\"></i> Pesos de Participantes</a></li>";

			echo "<li><a href=\"".DIR."cuenta/modevento/".$data['evento']->internalURL."/juez\"><i class=\"fa fa-balance-scale\" aria-hidden=\"true\"></i> Modificar Jueces</a></li>";
						

			echo "<li class=\"divider\"></li>";
			echo "<li><a href=\"".DIR."cuenta/modevento/".$data['evento']->internalURL."/eliminar\"><i class=\"fa fa-trash-o\" aria-hidden=\"true\"></i> Eliminar evento</a></li>";

			if(self::$cuenta->esAdmin)
			{
				echo "<li class=\"divider\"></li>";
				echo "<li><a href=\"".DIR."cuenta/modevento/".$data['evento']->internalURL."/participantes\"><i class=\"fa fa-briefcase\"></i> Agregar Participantes (por ID)</a></li>";
				echo "<li><a href=\"".DIR."cuenta/modevento/".$data['evento']->internalURL."/plan\"><i class=\"fa fa-briefcase\"></i> Cambiar plan de evento</a></li>";
				echo "<li class=\"divider\"></li>";

				if($data['evento']->eventoFinalizado)
					echo "<li><a href=\"".DIR."cuenta/modevento/".$data['evento']->internalURL."/abrir\"><i class=\"fa fa-briefcase\"></i> Abrir evento</a></li>";
				else
					echo "<li><a href=\"".DIR."cuenta/modevento/".$data['evento']->internalURL."/finalizar\"><i class=\"fa fa-briefcase\"></i> Finalizar evento</a></li>";

				if($data['evento']->aprobado)
					echo "<li><a href=\"".DIR."cuenta/modevento/".$data['evento']->internalURL."/desaprobar\"><i class=\"fa fa-briefcase\"></i> Desaprobar evento</a></li>";
				else
					echo "<li><a href=\"".DIR."cuenta/modevento/".$data['evento']->internalURL."/aprobar\"><i class=\"fa fa-briefcase\"></i> Aprobar evento</a></li>";
			}

			echo "</ul></li>";
		}


		if (isset($data['equipo']) && !$data['equipo']->locked && ( $data['esOrganizador']  || self::$cuenta->esAdmin) )
		{
			echo "
				<li class=\"dropdown\">
					<a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" role=\"button\" aria-haspopup=\"true\" aria-expanded=\"false\">
						<i class=\"fa fa-cog\" aria-hidden=\"true\"></i> Administraci&oacute;n<span class=\"caret\"></span>
					</a>

					<ul class=\"dropdown-menu\">";

			if($data["eventoTeam"])
			{
				/*
				echo "<li><a href=\"".DIR."cuenta/equipoEvento/mod/".$data['equipo']->id."\">Modificar Equipo</a></li>";
				echo "<li><a href=\"".DIR."cuenta/equipoEvento/mod/".$data['equipo']->id."/integrantes\">Integrantes</a></li>";
				echo "<li><a href=\"".DIR."cuenta/equipoEvento/mod/".$data['equipo']->id."/agregarintegrantes\">Agregar Integrantes</a></li>";
				*/
			}
			else
			{
				echo "<li><a href=\"".DIR."cuenta/equipo/mod/".$data['equipo']->id."\"><i class=\"fa fa-pencil\" aria-hidden=\"true\"></i>  Modificar Equipo</a></li>";
				echo "<li><a href=\"".DIR."cuenta/equipo/mod/".$data['equipo']->id."/integrantes\"><i class=\"fa fa-users\" aria-hidden=\"true\"></i> Integrantes</a></li>";
				echo "<li><a href=\"".DIR."cuenta/equipo/mod/".$data['equipo']->id."/agregarintegrantes\"><i class=\"fa fa-user-plus\" aria-hidden=\"true\"></i> Agregar Integrantes</a></li>";
			}

			if($data['equipo']->dedicado && ( $data['esOrganizador']  || self::$cuenta->esAdmin))
			{
				echo "<li class=\"divider\"></li>";
				echo "<li><a href=\"".DIR."cuenta/equipo/mod/".$data['equipo']->id."/formalizar\">Formalizar Equipo</a></li>";
			}
			//echo "<li class=\"divider\"></li>";
			//echo "<li><a href=\"".DIR."cuenta/modevento/".$data['evento']->internalURL."/participantesaprobar\">Aprobar Participantes</a></li>";
			//echo "<li><a href=\"".DIR."cuenta/modevento/".$data['evento']->internalURL."/participantesaprobados\">Participantes Registrados</a></li>";			echo "<li class=\"divider\"></li>";
			//echo "<li><a href=\"".DIR."cuenta/modevento/".$data['evento']->internalURL."/eliminar\">Eliminar evento</a></li>";

			if(self::$cuenta->esAdmin)
			{
				/*
				echo "<li class=\"divider\"></li>";
				echo "<li><a href=\"".DIR."cuenta/modevento/".$data['evento']->internalURL."/participantes\"><i class=\"fa fa-briefcase\"></i> Agregar Participantes (por ID)</a></li>";
				echo "<li><a href=\"".DIR."cuenta/modevento/".$data['evento']->internalURL."/plan\"><i class=\"fa fa-briefcase\"></i> Cambiar plan de evento</a></li>";
				echo "<li class=\"divider\"></li>";

				if($data['evento']->eventoFinalizado)
					echo "<li><a href=\"".DIR."cuenta/modevento/".$data['evento']->internalURL."/abrir\"><i class=\"fa fa-briefcase\"></i> Abrir evento</a></li>";
				else
					echo "<li><a href=\"".DIR."cuenta/modevento/".$data['evento']->internalURL."/finalizar\"><i class=\"fa fa-briefcase\"></i> Finalizar evento</a></li>";

				if($data['evento']->aprobado)
					echo "<li><a href=\"".DIR."cuenta/modevento/".$data['evento']->internalURL."/desaprobar\"><i class=\"fa fa-briefcase\"></i> Desaprobar evento</a></li>";
				else
					echo "<li><a href=\"".DIR."cuenta/modevento/".$data['evento']->internalURL."/aprobar\"><i class=\"fa fa-briefcase\"></i> Aprobar evento</a></li>";
					*/
			}

			echo "</ul></li>";
		}

		if ( isset($data['box']) && (self::$cuenta->esAdmin || ($data['box']->creadorId == self::$cuenta->id) ) )
		{
			echo "
				<li class=\"dropdown\">
					<a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" role=\"button\" aria-haspopup=\"true\" aria-expanded=\"false\">
						<i class=\"fa fa-cog\" aria-hidden=\"true\"></i> Administraci&oacute;n<span class=\"caret\"></span>
					</a>

					<ul class=\"dropdown-menu\">";

			echo "<li><a href=\"".DIR."cuenta/modbox/".$data['box']->id."\"><i class=\"fa fa-pencil\" aria-hidden=\"true\"></i>  Editar box</a></li>";
			echo "<li><a href=\"".DIR."cuenta/modbox/".$data['box']->id."/eliminar\"><i class=\"fa fa-trash-o\" aria-hidden=\"true\"></i> Eliminar box</a></li>";

			if(self::$cuenta->esAdmin)
			{

				if($data['box']->aprobado)
					echo "	<li><a href=\"".DIR."cuenta/modbox/".$data['box']->id."/desaprobar\"><i class=\"fa fa-briefcase\"></i> Desaprobar box</a></li>";
				else
					echo "	<li><a href=\"".DIR."cuenta/modbox/".$data['box']->id."/aprobar\"><i class=\"fa fa-briefcase\"></i> Aprobar box</a></li>";
			}

			echo "</ul></li>";
		}

		self::menuJuez($data);
		self::menuCuenta($data);
		self::menuLang($data);
	}
}

?>	