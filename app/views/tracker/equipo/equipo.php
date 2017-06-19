<?php
use Core\Language;
use Helpers\Hooks;
 
$hooks = Hooks::get();

$equipo = $data["equipo"];
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
			<a href="<?php echo DIR;?>"><img alt="" class="navbar-brand" src="<?php echo DIR;?>app/templates/<?php echo TEMPLATE;?>/img/logoLetras.png"/></a>
		</div>

		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav navbar-right">
				<?php $hooks->run('menu', $data); ?>
			</ul>
		</div>
	</div>
</nav>

<header class="header-evento">
	<div class="header-content">
		<div class="header-content-inner">

			<div class="imgContainerRanking">
				<img alt="" src="<?php echo $equipo->fotoPath; ?>"/>
				<span class="helper"></span>
			</div>

			<br/>
			<h1><?php echo $equipo->nombre; ?></h1>
			<br/>
			<p>
			<?= count($data['atletasApro']); ?> integrantes
			</p>
			<?php if($equipo->dedicado): ?>
				Equipo del evento:<br/><a class="btn btn-link" href="<?= DIR; ?>evento/<?= $equipo->eventoId; ?>"><?= $equipo->eventoStr; ?></a>
			<?php endif; ?>

			<?php if($data['estoy'] && !$data['estoy']->aprobado): ?>
				<p>
					Tienes una solicitud para participar en este equipo<br/>
					<a class="btn btn-default btn-margin" href="<?php echo DIR; ?>equipo/<?php echo $data['equipo']->id; ?>/rechazar"><i class="fa fa-times" aria-hidden="true"></i> Rechazar</a>
					<a class="btn btn-default btn-margin" href="<?php echo DIR; ?>equipo/<?php echo $data['equipo']->id; ?>/aceptar"><i class="fa fa-check" aria-hidden="true"></i> Aceptar</a>
				</p>
			<?php endif; ?>
		</div>
	</div>
</header>

<section class="bg-primary" id="resultados">
	<div class="container">
		<div class="row">
			<div class="col-lg-8 col-lg-offset-2 text-center">
				<h2 class="section-heading">Atletas</h2>
				<hr class="light">

				<div class="eventos-list">
					<?php foreach ($data['atletasApro'] as $keyAt => $valueAt): ?>
					<div class="evento">
						<a href="<?php echo DIR; ?>perfil/<?php echo $valueAt->personaId;?>">
							<div class="imgContainer">
								<img  alt="<?php echo $valueAt->nombre; ?>" src="<?php echo $valueAt->fotoPath;?>"/> 
								<span class="helper"></span>
							</div>

							<span class="titulo"><?php echo $valueAt->nombre." ".$valueAt->apellido; ?></span>
							<br/>
							<?php if($valueAt->boxId): ?>
							<span class="disciplina"><span style="padding-right: 5px;"><i class="fa fa-tag" aria-hidden="true"></i></span><?php echo $valueAt->boxStr; ?></span>
							<?php endif; ?>
							<span class="small">
								G&eacute;nero: <?php echo $valueAt->sexoStr; ?>
							</span>
						</a>
					</div>
					<?php endforeach ?>

					<?php //if($data['esOrganizador'] || $data['esAdmin']): ?>
					<?php foreach ($data['atletasPend'] as $keyAt => $valueAt): ?>
					<div class="evento">
						<a href="<?php echo DIR; ?>perfil/<?php echo $valueAt->personaId;?>">
							<div class="imgContainer">
								<img  alt="<?php echo $valueAt->nombre; ?>" src="<?php echo $valueAt->fotoPath;?>"/> 
								<span class="helper"></span>
							</div>

							<span class="titulo"><?php echo $valueAt->nombre." ".$valueAt->apellido; ?></span>
							<br/>
							<?php if($valueAt->boxId): ?>
							<span class="disciplina"><span style="padding-right: 5px;"><i class="fa fa-tag" aria-hidden="true"></i></span><?php echo $valueAt->boxStr; ?></span>
							<?php endif; ?>

							<span class="small">
							<i class="fa fa-clock-o" aria-hidden="true"></i> Aprobaci&oacute;n Pendiente / G&eacute;nero: <?php echo $valueAt->sexoStr; ?>
							</span>
						</a>
					</div>
					<?php endforeach ?>
					<?php //endif;?>

				</div>
			</div>
		</div>
	</div>
</section>

<?php if(!empty($data['participaciones'])): ?>
<section class="bg" id="resultados">
	<div class="container">
		<div class="row">
			<div class="col-lg-8 col-lg-offset-2 text-center">
				<h2 class="section-heading">Participaciones En Eventos</h2>
				<hr class="">

				<div class="eventos-list">
					<?php foreach ($data['participaciones'] as $keyEv => $valueEv): ?>
					<div class="evento">
						<a href="<?php echo DIR; ?>evento/<?php echo $valueEv->internalURL;?>">
							<div class="imgContainer">
								<img alt="<?php echo $valueEv->nombre; ?>" src="<?php echo $valueEv->fotoPath;?>"/> 
								<span class="helper"></span>
							</div>
							
							<span class="titulo"><?php echo $valueEv->nombre; ?></span>
							<br/>
							<span class="disciplina"><span style="padding-right: 5px;"><i class="fa fa-tag" aria-hidden="true"></i></span><?php echo $valueEv->disciplinaStr; ?></span>
							
							<span class="small">

								<?php if(!$valueEv->visible): ?>
									<i class="fa fa-eye-slash" aria-hidden="true"></i> Oculto
								<?php endif; ?>
								
								<?php echo $valueEv->fechaHtml; ?>
							</span>
						</a>
					</div>
					<?php endforeach ?>
				</div>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>