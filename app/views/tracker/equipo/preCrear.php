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
			<ul class="nav navbar-nav navbar-right">
				<li>
					<a class="page-scroll" href="<?php echo DIR; ?>eventos">Eventos</a>
				</li>
				<?php $hooks->run('menuCuenta', $data); ?>
			</ul>
		</div>
	</div>
</nav>

<header class="header-crearevento">
	<div class="header-content">
		<div class="header-content-inner">
			<h1>Creaci&oacute;n de Equipo</h1>
			<hr class="light">

			<p><b>Cree su equipo para participar en eventos por equipo.</b></p>
			<a href="<?php echo DIR; ?>cuenta/equipo/crear" class="btn btn-margin btn-primary btn-xl page-scroll"><i class="fa fa-arrow-circle-right"></i> Comenzar!</a>
		</div>
	</div>
</header>