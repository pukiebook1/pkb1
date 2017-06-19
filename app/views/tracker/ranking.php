<?php
use Core\Language;
use Helpers\Hooks;
 
$hooks = Hooks::get();

$evento = $data["evento"];
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
				<li>
					<a href="<?php echo DIR; ?>eventos">Eventos</a>
				</li>
				<?php $hooks->run('menu', $data); ?>
			</ul>
		</div>
	</div>
</nav>

<header class="header-evento">
	<div class="header-img"></div>
	<div class="header-content">
		<div class="header-content-inner">

			<div class="imgContainerRanking">
				<img alt="" src="<?php echo $evento->fotoPath; ?>"/>
				<span class="helper"></span>
			</div>

			<h1><?php echo $evento->nombre; ?></h1>
			<i class="fa fa-tag" aria-hidden="true"></i> <?php echo $evento->disciplinaStr; ?>
			<br/>
			 <?php echo $evento->fechaHtml; ?>
			<br/>
			<?php echo $evento->infobank; ?>
			<br/>
			<?php if ($evento->urlEvento): ?>
			<a target="_blank" class="btn btn-link" href="<?php echo $evento->urlEvento; ?>"><i class="fa fa-globe"></i> Web del evento</a>
			<?php endif ?>

			<div style="margin-top: 20px;">
				<p style="margin-bottom: 0px;">
				<?php if ($evento->juezPrincipal && $evento->juezSuplente): ?>
					<b>Jueces</b>
				<?php else: ?>
					<?php if ($evento->juezPrincipal || $evento->juezSuplente): ?>
						<b>Juez</b>
					<?php endif; ?>
				<?php endif; ?>
				</p>

				<div style="margin-bottom: 20px;">
					<?php if ($evento->juezPrincipal): ?>
					<?php $juezP = $data['dataEvento']->jueces[$evento->juezPrincipal]; ?>
						<a class="btn btn-link btn-jueces" target="_blank" href="<?php echo DIR; ?>perfil/<?php echo $juezP->id;?>"><?php echo $juezP->nombre." ".$juezP->apellido; ?></a>
					<?php endif ?>

					<?php if ($evento->juezPrincipal && $evento->juezSuplente): ?>
						<div class="row">&amp;</div>
					<?php endif ?>

					<?php if ($evento->juezSuplente): ?>
					<?php $juezS = $data['dataEvento']->jueces[$evento->juezSuplente]; ?>
						<a class="btn btn-link btn-jueces" target="_blank" href="<?php echo DIR; ?>perfil/<?php echo $juezS->id;?>"><?php echo $juezS->nombre." ".$juezS->apellido; ?></a>
					<?php endif; ?>
				</div>
			</div>

			<?php if (!$evento->visible): ?>
			<div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Aviso: </strong>El evento se encuentra oculto. S&oacute;lo se permiten los registros en este estado.</div>
			<?php endif ?>

			<?php if ( ($data['esAdmin'] || $data['esOrganizador']) && !$evento->creacionFinalizada && $evento->aprobado): ?>
			<div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Aviso: </strong>El evento ha sido aprobado pero no se ha finalizado su creacion. <a class="page-scroll" href="<?php echo DIR; ?>cuenta/crearevento/continuar/<?php echo $evento->internalURL; ?>">Continuar</a> </div>
			<?php endif ?>

			<?php if ( ($data['esAdmin'] || $data['esOrganizador']) && !$evento->creacionFinalizada && !$evento->aprobado): ?>
			<div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Aviso: </strong>La creaci&oacute;n del evento se encuentra en progreso. <a class="page-scroll" href="<?php echo DIR; ?>cuenta/crearevento/continuar/<?php echo $evento->internalURL; ?>">Continuar</a> </div>
			<?php endif ?>

			<?php if ( ($data['esAdmin'] || $data['esOrganizador']) && !$evento->aprobado): ?>
			<div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Aviso: </strong>Este evento debe ser aprobado por el administrador para poder ser mostrado en la lista de eventos.</div>
			<?php endif ?>

			<?php if ( ($data['esAdmin'] || $data['esOrganizador']) && $evento->aprobado && $evento->visible && !$evento->wodsvisible && ($evento->disciplina != 10) ): ?>
			<div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Aviso: </strong>Recuerde hacer sus WODs visibles.</div>
			<?php endif ?>

			<?php if ($data['estoyPendiente']): ?>
			<div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Aviso: </strong>Tu registro para este evento se encuentra en proceso. Se te notificar&aacute; v&iacute;a correo su aprobaci&oacute;n.</div>
			<?php endif ?>
		</div>
	</div>
</header>

<section class="bg-primary" id="resultados">
	<div class="container">
		<div class="row">
			<div class="col-lg-8 col-lg-offset-2 text-center">

				<h1><?= $evento->nombre; ?></h1>

				<?php if ($evento->aprobado && $evento->visible): ?>
					<div class="shareLinks">
	 					<div style="height:20px;display:inline-block;vertical-align: middle;" class="fb-share-button" data-href="<?php echo DIR."evento/".$evento->internalURL; ?>" data-layout="button_count"></div>
						<div style="height:20px;display:inline-block;vertical-align: middle;"><a href="https://twitter.com/share" class="twitter-share-button" data-url="<?php echo DIR."evento/".$evento->internalURL; ?>" data-count="none">Tweet</a></div>
					</div>
					<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
				<?php endif ?>

				<br/>
				<?= count($data['dataEvento']->categorias); ?> categor&iacute;as
				<br/>
				<?= count($data['dataEvento']->atletas); ?> atletas
				<br/>

				<hr class="light">

				<p class="text-faded">Seleccione la categor&iacute;a para ver la tabla de resultados</p>
				<?php if ($data['estoy'] && $data['estoy']->categoriaId != null): ?>
					<p class="text-faded">Est&aacute;s registrado en este evento. Puedes ir a tu resultado presionando <a href="<?php echo DIR; ?>evento/<?php echo $evento->internalURL;?>/<?php echo $data["estoy"]->categoriaId;?>#mires">aqu&iacute;</a></p>
				<?php endif ?>

				<?php foreach ($data['dataEvento']->categorias as $key => $value): ?>
					<a href="<?= DIR; ?>evento/<?php echo $evento->internalURL; ?>/<?php echo $value->id; ?>" class="btn btn-margin btn-default"><i class="fa fa-list-ol"></i> <?= $value->nombre; ?></a>
				<?php endforeach ?>
			</div>
		</div>
	</div>
</section>