<?php
use Core\Language;
use Helpers\Hooks;
 
$hooks = Hooks::get();
?>

<nav id="mainNav" class="navbar navbar-default navbar-fixed-top">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a href="<?php echo DIR;?>"><img alt="Pukiebook" class="navbar-brand" src="<?php echo DIR;?>app/templates/<?php echo TEMPLATE;?>/img/logoLetras.png"/></a>
		</div>

		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<form method="GET" action="" class="navbar-form navbar-right" role="search">
				<div class="input-group">
					<input type="text" name="buscar" class="form-control" placeholder="Buscar evento" value="<?php echo $data['busqueda']; ?>">
					<span class="input-group-btn">
						<button type="submit" class="btn btn-default">
						<span class="glyphicon glyphicon-search"></span>
						</button>
					</span>
				</div>
			</form>
			
			<ul class="nav navbar-nav navbar-right">
				<li class="special">
					<div class="btn-group">
						<button type="button" class="btn dropdown-toggle dropdown-navbar page-scroll" style="padding-left: 14px;padding-top: 14px;padding-bottom: 14px;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						A&ntilde;o<span class="caret"></span>
						</button>
						<ul class="dropdown-menu">
							<?php
								$eventosAnno = $data['byYear'];
								$countEvento = 1;

								foreach ($eventosAnno as $keyE => $valueE)
								{
									echo "<li>";
									echo "<a class=\"page-scroll\" href=\"#year".$countEvento++."\">".$keyE."</a>";
									echo "</li>";
								}
							?>
						</ul>
					</div>
				</li>				

				<?php $hooks->run('menuCuenta', $data); ?>
			</ul>
		</div>
	</div>
</nav>

<header class="header-eventos">
	<div class="header-content">
		<div class="header-content-inner">
			<h1>Eventos</h1>
			<hr class="light">
			<p>Consulta los resultados de los eventos.</p>
			<?php if (empty($data['byYear'])): ?>
				<div class="alert alert-warning">
					<?php if (isset($_GET['buscar']) && !empty($_GET['buscar'])): ?>
						No se encontraron resultados para su b&uacute;squeda.
					<?php else: ?>
						No se encontraron eventos.
					<?php endif ?>
				</div>
			<?php endif ?>
		</div>
	</div>
</header>

<div id="contenedorAnnos">
</div>

<?php
$eventosAnno = $data['byYear'];
$countEvento = 1;
$isColor = true;

foreach ($eventosAnno as $keyE => $valueE): ?>


<section <?php if($isColor) echo "class=\"bg-primary\""; ?> id="year<?php echo $countEvento++; ?>">
	<div class="container">
		<div class="row">
			<div class="col-lg-8 col-lg-offset-2 text-center">
				<h2 class="section-heading">Eventos <?php echo $keyE; ?></h2>
				<p <?php if($isColor) echo "class=\"text-faded\""; ?> ><?php echo count($valueE)." eventos"; ?></p>

				<div class="eventos-list">
					<?php foreach ($valueE as $keyEv => $valueEv): ?>
					<div class="evento">
						<a href="<?php echo DIR; ?>evento/<?php echo $valueEv->internalURL;?>">
							<div class="imgContainer">
								<img  alt="<?php echo $valueEv->nombre; ?>" src="<?php echo $valueEv->fotoPath;?>"/> 
								<span class="helper"></span>
							</div>

							<span class="titulo"><?php echo $valueEv->nombre; ?></span>
							<br/>
							<span class="disciplina"><span style="padding-right: 5px;"><i class="fa fa-tag" aria-hidden="true"></i></span><?php echo $valueEv->disciplinaStr; ?></span>
							
							<span class="small">
								<?php if(!$valueEv->visible): ?>
									<i class="fa fa-eye-slash" aria-hidden="true"></i> Oculto
								<?php endif; ?>

								<?php if (!$valueEv->eventoFinalizado): ?>
									<?php if ($valueEv->fecha < $data['fechaActual']): ?>
										<i class="fa fa-forward" aria-hidden="true"></i>
									<?php else: ?>
										<i class="fa fa-clock-o" aria-hidden="true"></i>
									<?php endif ?>
								<?php else: ?>
									<i class="fa fa-check" aria-hidden="true"></i>
								<?php endif ?>

								<?php echo $valueEv->fechaStr; ?>
							</span>
						</a>
					</div>
					<?php endforeach ?>
				</div>

			</div>
		</div>
	</div>
</section>

<?php $isColor = !$isColor; endforeach; ?>

<?php

use Helpers\Assets;
use Helpers\Url;

//initialise hooks
$hooks = Hooks::get();
?>

<?php
Assets::js(array(
	Url::templatePath() . 'js/jquery.min.js',
	Url::templatePath() . 'js/bootstrap.min.js',
	Url::templatePath() . 'js/jquery.easing.min.js',
	Url::templatePath() . 'js/jquery.fittext.js',
	Url::templatePath() . 'js/wow.min.js',
	Url::templatePath() . 'js/creative.js'
));
?>
<script type="text/javascript">
	var getEventos = function()
	{    
		return $.getJSON("ajaxEventos", function() {console.log( "success" )});   
	}

	getEventos()
	.done(function(response)
	{
		if (response.success)
		{
			var isColor = true;

			$.each(response.data.eventos, function(key, value)
			{
				//alert("anno: " + key);
				var html = "<section id=\"year"+key+"\">";

				html += "<div class=\"container\"><div class=\"row\"><div class=\"col-lg-8 col-lg-offset-2 text-center\">";
				html += "<h2 class=\"section-heading\">Eventos "+key+"</h2>";
				html += "<p>"+value.length+" eventos</p>";
				html += "<ul class=\"eventos-list\">";

				$.each(value, function(keyE, valueE)
				{
					html += "<li>";

					html += "";
					html += "";
					html += "";
					html += "";
					html += "";
					html += "";
					html += "";
					
					html += "</li>";
				});

				html += "";
				html += "";
				html += "";
				html += "";
				html += "";
				html += "</ul>";
				html += "</div></div></div>";

				html += "</section>";
				$("#contenedorAnnos").append(html);
				if(isColor)
				{
					$("#year"+key).addClass("bg-primary");
				}
				isColor = !isColor;
				
				//output += "<h2>Detalles del usuario " + value['ID'] + "</h2>";
				
				// recorremos los valores de cada usuario
				//$.each(value, function(userkey, uservalue) {
					
					//output += '<ul>';
					//output += '<li>' + userkey + ': ' + uservalue + "</li>";
					//output += '</ul>';
					
				//});
			
			});
		}
	})      
	.fail(function(jqXHR, textStatus, errorThrown) {
			
			alert(textStatus);
	});

</script>