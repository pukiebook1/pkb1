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
                <?php $hooks->run('menuCuenta', $data); ?>
			</ul>
		</div>
		<!-- /.navbar-collapse -->
	</div>
	<!-- /.container-fluid -->
</nav>

<header class="header-crearevento">
	<div class="header-content">
		<div class="container">
			<h1>Creaci&oacute;n de Evento</h1>
			<p>Plan del evento: <?php echo $data['evento']->nombrePlan; ?></p>
			<hr class="light">

			<p>Especifique las categor&iacute;as del evento</p>
			<p>Puedes registrar hasta <span style="color:yellow;"><strong><?php echo $data['evento']->categoriasPlan; ?></strong></span> categor&iacute;as</p>
		</div>
		<div class="container"> 

		</div>
	</div>
</header>


<style type="text/css">
	.entry:not(:first-of-type)
	{
		margin-top: 10px;
	}

	.glyphicon
	{
		font-size: 12px;
	}

	.btn-nomargin{
		margin: 0;
		border-width: 0;
	}
</style>

<section>
	<div class="container">
		<div class="row">
			<div class="col-sm-8 col-sm-offset-2 text-center">
				<div class="control-group" id="fields">
					<h2 class="section-heading">Categor&iacute;as del eventos</h2>
					<hr>
					<div class="controls">    
						<form role="form" autocomplete="off" action="" method="POST">
							<input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>" required/>
							<div class="col-sm-6 col-sm-push-3">
							<?php if (!empty($error['mensajes'])): ?>
								<div class="alert alert-danger">
									<?php foreach ($error['mensajes'] as $value): ?>
									<?php echo $value; ?><br/>
									<?php endforeach ?>
								</div>
							<?php endif ?>
							<?php foreach ($error['data'] as $key => $value): ?>
								<div class="entry input-group col-sm-12">
									<input class="form-control" name="categorias[]" type="text" placeholder="Nombre de categor&iacute;a" value="<?php echo $value; ?>" />
									<span class="input-group-btn">
										<button class="btn btn-default btn-remove btn-nomargin" type="button">
											<span class="glyphicon glyphicon-minus"></span>
										</button>
									</span>
								</div>
							<?php endforeach ?>

								<div class="entry input-group col-sm-12">
									<input class="form-control" name="categorias[]" type="text" placeholder="Nombre de categor&iacute;a" />
									<span class="input-group-btn">
										<button class="btn btn-default btn-add" type="button">
											<span class="glyphicon glyphicon-plus"></span>
										</button>
									</span>
								</div>
								<div class="form-group col-sm-12">
									<button name="submit" type="submit" class="btn btn-margin btn-primary"><i class="fa fa-arrow-circle-right"></i> Siguiente</button>
								</div>                               
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>