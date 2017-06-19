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
				<li>
					<a class="page-scroll"  target="_blank" href="<?php echo DIR; ?>evento/<?php echo $data['evento']->internalURL; ?>">Ver evento</a>
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
			<h1>Registro de pago</h1>
			<hr class="light">
			<p>Seleccione una forma de pago</p>
			<form role="form" action="crear" method="POST">
			<input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>" />
			<div class="col-sm-6 col-sm-push-3">
				<div class="col-sm-12">
					<a href="<?php echo DIR; ?>cuenta/registrarseevento/<?php echo $data['evento']->internalURL; ?>/<?php echo $data['categoria']->id; ?>/registrarPago/1" class="btn btn-default btn-margin"><i class="fa fa-university"></i> Dep&oacute;sito/Transferencia</a><br/>
					<a href="<?php echo DIR; ?>cuenta/registrarseevento/<?php echo $data['evento']->internalURL;?>/<?php echo $data['categoria']->id; ?>/registrarPago/2" class="btn btn-default btn-margin"><i class="fa fa-money"></i> Pago Directo</a>
				</div>

				<div class="col-sm-12">
					<p></p>
				</div>

				<div class="col-sm-12">
					<a class="btn btn-default" href="../"><i class="fa fa-chevron-circle-left"></i> Volver</a>
				</div>                        
			</div>
		</form>
		</div>
	</div>
</header>
