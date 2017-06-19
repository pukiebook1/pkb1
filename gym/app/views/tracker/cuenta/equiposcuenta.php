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
			<h1>Mis Equipos</h1>
			<hr class="light">
			<p>Consulte sus equipos creados y los equipos en los que participa.</p>
			<!-- <a href="#year1" class="btn btn-primary btn-xl page-scroll">Empezar!</a> -->
		</div>
	</div>
</header>

<section class="bg-primary" id="principal">
	<div class="container">
		<div class="row">
			<div class="col-lg-8 col-lg-offset-2 text-center">
				<h2 class="section-heading">Mis Equipos</h2>
				<hr class="light">
				<?php if (empty($data['equipos'])): ?>
				<p class="text-faded">No posees equipos.</p>    
				<?php else: ?>
				<p class="text-faded"><?php echo count($data['equipos']); ?> Equipos</p>

				<div class="eventos-list">
					<?php foreach ($data['equipos'] as $keyE => $valueE): ?>
					<div class="col-md-6">
						<div class="evento">
							<a href="<?php echo DIR; ?>equipo/<?php echo $valueE->id;?>">
								<div class="imgContainer">
									<img src="<?php echo $valueE->fotoPath;?>"/> 
									<span class="helper"></span>
								</div>
								<span class="titulo"><?php echo $valueE->nombre; ?></span>

								<span class="small">
									<?php if($valueE->dedicado): ?>
										<i class="fa fa-chevron-right" aria-hidden="true"></i> <?= $valueE->eventoStr ?>
									<?php else: ?>
										<i class="fa fa-chevron-right" aria-hidden="true"></i> <i class="fa fa-globe" aria-hidden="true"></i>
									<?php endif; ?>
								</span>
							</a>
						</div>
					</div>
					<?php endforeach ?>
				</div>
				<?php endif ?>
			</div>
		</div>
	</div>
</section>

<section class="bg-light" id="principal">
	<div class="container">
		<div class="row">
			<div class="col-lg-8 col-lg-offset-2 text-center">
				<h2 class="section-heading">Equipos En Los Que Participo</h2>
				<hr class="faded">
				<?php if (empty($data['equiposParticipo'])): ?>
				<p class="">No participas en equipos.</p>    
				<?php else: ?>
				<p class=""><?php echo count($data['equiposParticipo']); ?> Equipos</p>

				<div class="eventos-list">
					<?php foreach ($data['equiposParticipo'] as $keyE => $valueE): ?>
					<div class="col-md-6">
						<div class="evento">
							<a href="<?php echo DIR; ?>equipo/<?php echo $valueE->id;?>">
								<div class="imgContainer">
									<img src="<?php echo $valueE->fotoPath;?>"/> 
									<span class="helper"></span>
								</div>
								<span class="titulo"><?php echo $valueE->nombre; ?></span>

								<span class="small">
									<?php if($valueE->dedicado): ?>
										<i class="fa fa-chevron-right" aria-hidden="true"></i> <?= $valueE->eventoStr ?>
									<?php else: ?>
										<i class="fa fa-chevron-right" aria-hidden="true"></i> <i class="fa fa-globe" aria-hidden="true"></i>
									<?php endif; ?>
								</span>
							</a>
						</div>
					</div>
					<?php endforeach ?>
				</div>
				<?php endif ?>
			</div>
		</div>
	</div>
</section>