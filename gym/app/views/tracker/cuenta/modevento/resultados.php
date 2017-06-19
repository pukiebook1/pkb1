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
				<?php $hooks->run('menu', $data); ?>
			</ul>
		</div>
		<!-- /.navbar-collapse -->
	</div>
	<!-- /.container-fluid -->
</nav>

<header class="header-crearevento">
	<div class="header-content">
		<div class="container">
			<h1>Registro de resultados</h1>
			<p>Evento: <?php echo $data['evento']->nombre; ?></p>
			<hr class="light">
			
			<p>Seleccione la categor&iacute;a</p>
		</div>
		<div class="container">
			<div class="col-md-12">
				<?php foreach ($data['categorias'] as $key => $value): ?>
					<a href="resultados/<?php echo $key; ?>" class="btn btn-margin btn-primary"><i class="fa fa-list-ol"></i> <?php echo $value->nombre; ?></a>
				<?php endforeach ?>                            
			</div>
            <div class="col-md-12">
                <div class="form-group">
                    <a class="btn btn-margin btn-default" href="<?php echo DIR; ?>evento/<?php echo $data['evento']->internalURL; ?>"><i class="fa fa-chevron-circle-left"></i> Volver</a>
                </div>
            </div>
		</div>
	</div>
</header>