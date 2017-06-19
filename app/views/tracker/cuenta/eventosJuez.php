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
			<h1>Eventos como juez</h1>
			<hr class="light">
			<p></p>
		</div>
	</div>
</header>

<section class="bg-primary" id="principal">
	<div class="container">
		<div class="row">
			<div class="col-lg-8 col-lg-offset-2 text-center">
				<h2 class="section-heading">Eventos como juez principal</h2>
				<hr class="light">
				<?php if (empty($data['eventosJuezPrincipal'])): ?>
				<p class="text-faded">No posees eventos como juez principal.</p>    
				<?php else: ?>
				<p class="text-faded"><?php echo count($data['eventosJuezPrincipal']); ?> eventos</p>

				<div class="eventos-list">
					<?php foreach ($data['eventosJuezPrincipal'] as $keyE => $valueE): ?>
					<div class="evento">
						<a href="<?php echo DIR; ?>cuenta/modevento/<?php echo $valueE->internalURL;?>/resultados">
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
						</a>
					</div>
					<?php endforeach ?>
				</div>
				<?php endif ?>
			</div>
		</div>
	</div>
</section>

<section id="suplente">
	<div class="container">
		<div class="row">
			<div class="col-lg-8 col-lg-offset-2 text-center">
				<h2 class="section-heading">Eventos como juez suplente</h2>
				<hr>
				<?php if (empty($data['eventosJuezSuplente'])): ?>
				<p>No posees eventos como juez suplente.</p>    
				<?php else: ?>
				<p><?php echo count($data['eventosJuezSuplente']); ?> eventos</p>

				<div class="eventos-list">
					<?php foreach ($data['eventosJuezSuplente'] as $keyE => $valueE): ?>
					<div class="evento">
						<a href="<?php echo DIR; ?>cuenta/modevento/<?php echo $valueE->internalURL;?>/resultados">
							<div class="imgContainer">
								<img src="<?php echo $valueE->fotoPath;?>"/> 
								<span class="helper"></span>
							</div>
							<span class="titulo"><?php echo $valueE->nombre; ?></span>
							<br/>
							<span class="small">
								<?php echo $valueE->fechaHtml; ?>
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