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
			<h1>Mis Boxes</h1>
			<hr class="light">
			<p></p>
			<!-- <a href="#year1" class="btn btn-primary btn-xl page-scroll">Empezar!</a> -->
		</div>
	</div>
</header>

<section class="bg-primary" id="principal">
	<div class="container">
		<div class="row">
			<div class="col-lg-8 col-lg-offset-2 text-center">
				<!-- <h2 class="section-heading"></h2> -->
				<!-- <hr class="light"> -->
				<?php if (empty($data['boxes'])): ?>
				<p class="text-faded">No posees boxes.</p>    
				<?php else: ?>
				<p class="text-faded"><?php echo count($data['boxes']); ?> Boxes</p>

				<div class="eventos-list">
					<?php foreach ($data['boxes'] as $keyE => $valueE): ?>
					<div class="evento">
						<a href="<?php echo DIR; ?>box/<?php echo $valueE->internalURL;?>">
							<div class="imgContainer">
								<img src="<?php echo $valueE->fotoPath;?>"/> 
								<span class="helper"></span>
							</div>
							<span class="titulo"><?php echo $valueE->nombre; ?></span>
							<br/>
							<span class="small"><span style="padding-right: 5px;"><i class="fa fa-globe"></i></span><?php echo $valueE->paisStr; ?></span>
							<span class="small-info">
								<?php if (!$valueE->aprobado): ?>
									<span class="glyphicon glyphicon-minus"></span> Pendiente
								<?php else: ?>
									<span class="glyphicon glyphicon-thumbs-up"></span> Aprobado
								<?php endif ?>
							</span>
						</a>
					</div>
					<?php endforeach ?>
				</div>
				<?php endif ?>
			</div>
		</div>
	</div>
</section>