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

<style type="text/css">
	body
	{
		min-width: 485px;
	}

	.container
	{
		max-width: none !important;
		width: 482px;
		min-height: 100%;
		padding-right: 0px;
		padding-left: 0px;
	}

	.row
	{
		 margin-right: 0px; 
		 margin-left: 0px; 
	}
	section.perfil
	{
		background-size: cover;
	}
</style>

<section class="bg-primary perfil" >
<div class="header-img"></div>
	<div class="container">
		<div class="row">

			<div class="folderContainer" style="">
				<div class="folder" style="">
					<div class="folderImage">						
						<img class="" src="<?php echo $data['persona']->fotoPath; ?>"/>
					</div>

					<div class="folderSide">
						<span>Perfil del Atleta #<?php echo $data['persona']->idPersona; ?></span>
					</div>

					<div class="folderClip">
					</div>

					<div class="folderTopText">
						<table class="infoTable">
							<tbody>
								<tr><th>Nombre</th><td><?php echo $data['persona']->nombre; ?></td></tr>
								<tr><th>Apellido</th><td><?php echo $data['persona']->apellido; ?></td></tr>
								<tr><th>Alias</th><td><?php echo $data['persona']->alias; ?></td></tr>
							</tbody>
						</table>
					</div>

					<div class="folderBodyText">
						<table class="bioTable">
							<tbody>
								<tr><th>Pais</th><td><?php echo $data['persona']->paisStr; ?></td></tr>
								<tr><th>Estado/Provincia</th><td><?php echo $data['persona']->estado; ?></td></tr>
								<tr><th>Nacimiento</th><td><?php echo $data['persona']->nacimientoStr; ?></td></tr>
								<tr><th>Sexo</th><td><?php echo $data['persona']->sexoStr; ?></td></tr>
								<tr><th>Box</th><td><?php echo $data['persona']->boxStr; ?></td></tr>
								<tr><th>Partner in Crime</th><td><?php echo $data['persona']->pic; ?></td></tr>
							</tbody>
						</table>

						<div class="workouts">
							<span class="profileText">Records Personales:</span>

							<table class="table table-striped work work-left">
								<tbody>
									<tr><td class="wk">Clean &amp; Jerk</td><td class="wkRes"><?php echo $data['persona']->wkcleanObj->str; ?></td></tr>
									<tr><td class="wk">Snatch</td><td class="wkRes"><?php echo $data['persona']->wksnatchObj->str; ?></td></tr>
									<tr><td class="wk">Deadlift</td><td class="wkRes"><?php echo $data['persona']->wkdeadObj->str; ?></td></tr>
									<tr><td class="wk">Back Squat</td><td class="wkRes"><?php echo $data['persona']->wkbacksquatObj->str; ?></td></tr>
								</tbody>
							</table>

							<table class="table table-striped work work-right">
								<tbody>
									<tr><td class="wk">Fran</td><td class="wkRes"><?php echo $data['persona']->wkfranObj->str; ?></td></tr>
									<tr><td class="wk">Isabel</td><td class="wkRes"><?php echo $data['persona']->wkisabelObj->str; ?></td></tr>
									<tr><td class="wk">Grace</td><td class="wkRes"><?php echo $data['persona']->wkgraceObj->str; ?></td></tr>
									<tr><td class="wk">Run 5K</td><td class="wkRes"><?php echo $data['persona']->wkrun5kObj->str; ?></td></tr>
									<tr><td class="wk">Cindy</td><td class="wkRes"><?php echo $data['persona']->wkcindyObj->str; ?></td></tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="folderButtons">
						<div class="form-group">
							<a class="btn btn-margin btn-link" href="<?php echo DIR; ?>cuenta/contrasena"><i class="fa fa-key"></i> Cambiar Contrase&ntilde;a</a>
							<a class="btn btn-margin btn-link" href="<?php echo DIR; ?>perfil/<?php echo $data['persona']->idPersona; ?>/edit"><i class="fa fa-pencil-square-o"></i> Editar</a>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>
</section>

<section class="bg-primary" id="resumen">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-8 col-sm-offset-2 text-center">
				<h2 class="section-heading">Resumen de cuenta</h2>
				<p style="color:orange;font-weight: bold;">
					Nro. de registro: <?php echo $data['persona']->idPersona; ?>
				</p> 
				<h3 style="font-size:12px;">
					<?php if ($data['persona']->esAdmin): ?>
						Cuenta Administrador
					<?php else: ?>
						Plan actual: <?php echo $data['persona']->plan; ?>
					<?php endif; ?>
				</h3>
				<hr class="light">

				<br/>

				<?php if ($data['info']->eventosJuezActivo): ?>
					<div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Aviso: </strong>Eres juez activo de uno o m&aacute;s eventos. <a href="<?php echo DIR; ?>cuenta/eventosjuez">Ver eventos</a>.</div>
				<?php endif ?>

				<?php if (!empty($data['info']->eventosFin)): ?>
					<p class="text-faded">Has participado en <?php echo $data['info']->eventosFin; ?> eventos.</p>
				<?php else: ?>
					<p class="text-faded">No has participado en eventos a&uacute;n.</p>
				<?php endif ?>

				<?php if (!empty($data['info']->eventosAct)): ?>
					<p class="text-faded">Actualmente te encuentras registrado en <?php echo $data['info']->eventosAct; ?> eventos.</p>
				<?php endif ?>

				<?php if (!empty($data['info']->eventosSolicitud)): ?>
					<p class="text-faded">Tienes <b><?php echo $data['info']->eventosSolicitud; ?></b> solicitud para participar en evento pendientes.</p>
				<?php endif ?>

				<?php if (!empty($data['info']->eventosOrganizados)): ?>
					<p class="text-faded">Has organizado <?php echo $data['info']->eventosOrganizados; ?> eventos.</p>
				<?php endif ?>

				<?php if (!empty($data['info']->eventosJuezFinalizado)): ?>
					<p class="text-faded">Has sido juez en <?php echo $data['info']->eventosJuezFinalizado; ?> eventos.</p>
				<?php endif ?>
			</div>
		</div>
	</div>
</section>

<!-- <section id="estadisticas">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-8 col-lg-offset-2 text-center">
				<h2 class="section-heading">Estad&iacute;sticas</h2>
				<hr>
				<p>No hay estadisticas.</p>
			</div>
		</div>
	</div>
</section> -->