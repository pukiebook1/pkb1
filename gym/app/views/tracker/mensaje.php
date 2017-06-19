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

<header class="header-autenticacion">
	<div class="header-content">
		<div class="container">
			<div class="row">
				<div class="col-lg-12 text-center">
					<h2 class="section-heading"><?php echo $data['titulo']; ?></h2>
					<hr class="light">
				</div>
			</div>
		</div>
		<div class="container text-center">    
			<div class="col-md-12 ">
				<p><?php echo $data['mensaje']; ?></p>
				<?php if ($data['botones']): ?>
					<?php foreach ($data['botones'] as $key => $value): ?>
					<a class="btn btn-default btn-margin" href="<?php echo $value['href']; ?>"><?php echo $value['boton']; ?></a>
					<?php endforeach ?>
				<?php else: ?>
					<a class="btn btn-default" href="<?php echo $data['href']; ?>"><?php echo $data['boton']; ?></a>
				<?php endif ?>
				
			</div>
		</div>
	</div>
</header>