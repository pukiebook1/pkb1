<?php
use Core\Language;
use Helpers\Hooks;
 
$hooks = Hooks::get();

$box = $data["box"];
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
				<img alt="" src="<?php echo $box->fotoPath; ?>"/>
				<span class="helper"></span>
			</div>

			<h1><?php echo $box->nombre; ?></h1>
			<?php if ($box->coach): ?>
			<p>Coach: <?= $box->coach; ?></p>
			<?php endif ?>

			<?php if ($box->urlBox): ?>
			<a target="_blank" class="btn btn-link" href="<?php echo $box->urlBox; ?>"><i class="fa fa-globe"></i> Web del box</a>
			<?php endif ?>

			<?php if ( ($data['esAdmin'] || $data['esOrganizador']) && !$box->aprobado): ?>
			<div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Aviso: </strong>Este box debe ser aprobado por el administrador para poder ser mostrado en la lista de boxes.</div>
			<?php endif ?>

		</div>
	</div>
</header>

<section class="bg-primary">
	<div class="container">
		<div class="row">
			<div class="col-lg-8 col-lg-offset-2 text-center">

				<h1>Integrantes de <?= $box->nombre; ?></h1>

				<?php if ($box->aprobado): ?>
					<div class="shareLinks">
	 					<div style="height:20px;display:inline-block;vertical-align: middle;" class="fb-share-button" data-href="<?php echo DIR."box/".$box->id; ?>" data-layout="button_count"></div>
						<div style="height:20px;display:inline-block;vertical-align: middle;"><a href="https://twitter.com/share" class="twitter-share-button" data-url="<?php echo DIR."box/".$box->id; ?>" data-count="none">Tweet</a></div>
					</div>
					<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
				<?php endif ?>

				<hr class="light">

				<div class="eventos-list">
					<?php foreach ($data['atletas'] as $keyAt => $valueAt): ?>
					<div class="col-lg-4">
						<div class="evento">
							<a href="<?php echo DIR; ?>perfil/<?php echo $keyAt;?>">
								<div class="imgContainer">
									<img alt="<?= $valueAt->nombre." ".$valueAt->apellido; ?>" src="<?php echo $valueAt->fotoPath;?>"/> 
									<span class="helper"></span>
								</div>
								
								<span class="titulo"><?= $valueAt->nombre." ".$valueAt->apellido; ?></span>
							</a>
						</div>
					</div>
					<?php endforeach ?>
				</div>

			</div>
		</div>
	</div>
</section>