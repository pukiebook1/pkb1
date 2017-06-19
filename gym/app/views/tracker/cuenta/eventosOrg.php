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
					<a href="<?php echo DIR; ?>eventos">Eventos</a>
				</li>
                <?php $hooks->run('menuCuenta', $data); ?>
			</ul>
		</div>
	</div>
</nav>

<header class="header-eventos">
	<div class="header-content">
		<div class="header-content-inner">
			<h1>Tus eventos organizados</h1>
			<hr class="light">
			<p>Administra tus eventos organizados.</p>
		</div>
	</div>
</header>

<section class="bg-primary" id="eventos">
	<div class="container">
		<div class="row">
			<div class="col-lg-8 col-lg-offset-2 text-center">
				<h2 class="section-heading">Tus eventos</h2>
				<hr class="light">
				<?php if (empty($data['eventosOrganizados'])): ?>
				<p class="text-faded">No posees eventos organizados.</p>    
				<?php else: ?>
				<p class="text-faded"><?php echo "Eventos: ".count($data['eventosOrganizados']); ?></p>

				<div class="eventos-list">
					<?php foreach ($data['eventosOrganizados'] as $keyE => $valueE): ?>
					<div class="evento">
						<a href="<?php echo DIR; ?>evento/<?php echo $valueE->internalURL;?>">
							<div class="imgContainer">
								<img src="<?php echo $valueE->fotoPath;?>"/> 
								<span class="helper"></span>
							</div>

							<span class="titulo"><?php echo $valueE->nombre; ?></span>
							<br/>
							<span class="disciplina"><span style="padding-right: 5px;"><i class="fa fa-tag" aria-hidden="true"></i></span><?php echo $valueE->disciplinaStr; ?></span>
							
							<span class="small">
								<?php echo $valueE->fechaHtml; ?>
							</span>

							<span class="small-info">

								<?php if (!$valueE->creacionFinalizada): ?>
									<span class="glyphicon glyphicon-pencil"></span>
									En Creaci&oacute;n
								<?php endif ?>

								<?php if (!$valueE->aprobado): ?>
									<span class="glyphicon glyphicon-minus"></span>
									Aprobaci&oacute;n Pendiente
								<?php else: ?>
									<span class="glyphicon glyphicon-thumbs-up"></span>
									Aprobado
								<?php endif ?>
								
								<?php if (!$valueE->visible): ?>
									<span class="glyphicon glyphicon-eye-close"></span>
									Oculto
								<?php else: ?>
									<span class="glyphicon glyphicon-eye-open"></span>
									Visible
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