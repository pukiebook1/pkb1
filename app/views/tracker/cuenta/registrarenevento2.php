<?php
use Core\Language;
use Helpers\Hooks;
 
$hooks = Hooks::get();

?>


<nav id="mainNav" class="navbar navbar-default navbar-fixed-top">
	<div class="container-fluid">
		<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a href="<?php echo DIR;?>"><img alt="Pukiebook" class="navbar-brand" src="<?php echo DIR;?>app/templates/<?php echo TEMPLATE;?>/img/logoLetras.png"/></a>
		</div>

		<!-- Collect the nav links, forms, and other content for toggling -->
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav navbar-right">
				<li>
					<a class="page-scroll" href="<?php echo DIR; ?>eventos">Eventos</a>
				</li>
				<li>
					<a class="page-scroll"  target="_blank" href="<?php echo DIR; ?>evento/<?php echo $data['evento']->internalURL; ?>">Ver evento</a>
				</li>

				<?php $hooks->run('menuCuenta', $data); ?>
			</ul>
		</div>
		<!-- /.navbar-collapse -->
	</div>
	<!-- /.container-fluid -->
</nav>

<header class="header-eventos">
	<div class="header-content">
		<div class="header-content-inner">
			<h1>Registrarse en evento</h1>
			<hr class="light">
			<p>Seleccione la categor&iacute;a en la cual participar&aacute;.</p>
			<!-- <a href="#year1" class="btn btn-primary btn-xl page-scroll">Empezar!</a> -->
		</div>
	</div>
</header>

<section class="bg-primary" id="eventos">
	<div class="container">
		<div class="row">
			<div class="col-lg-8 col-lg-offset-2 text-center">
				<h2 class="section-heading">Categor&iacute;as</h2>
				<hr class="light">
				<?php if (empty($data['categorias'])): ?>
				<p class="text-faded">No se encontraron eventos para registrarse.</p>
				<?php else: ?>
				<p class="text-faded"><?php echo count($data['categorias'])." categor&iacute;as"; ?></p>

					<?php foreach ($data['categorias'] as $keyE => $valueE): ?>
						<a class="btn btn-margin btn-default" href="<?php echo DIR; ?>cuenta/registrarseevento/<?php echo $data['evento']->internalURL; ?>/<?php echo $keyE; ?>"><i class="fa fa-list-ol"></i> <?php echo $valueE->nombre; ?></a>
					<?php endforeach ?>

				<?php endif ?>
			</div>
		</div>
	</div>
</section>