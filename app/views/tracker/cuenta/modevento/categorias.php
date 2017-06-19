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
			<h1>Modificaci&oacute;n de Evento</h1>
			<p>Plan del evento: <?php echo $data['evento']->nombrePlan; ?></p>
			<hr class="light">

			<p>Modifique las categor&iacute;as del evento</p>
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
			<div class="col-lg-8 col-lg-offset-2 text-center">
				<div class="control-group" id="fields">
					<h2 class="section-heading">Categor&iacute;as del evento</h2>
					<hr>
					<?php if (!empty($error['mensajes'])): ?>
						<div class="alert alert-danger">
							<?php foreach ($error['mensajes'] as $value): ?>
							<?php echo $value; ?><br/>
							<?php endforeach ?>
						</div>
					<?php endif ?>
					<div class="controls">    
						<form role="form" autocomplete="off" action="" method="POST">
							<input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>" required/>
							<div class="col-md-6 col-md-push-3">
							
								<?php foreach ($data['categorias'] as $key => $value): ?>
								<div class="entry input-group col-lg-12"> 
									<input class="form-control" name="categorias[<?php echo $key; ?>]" type="text" placeholder="Nombre de categor&iacute;a" value="<?php echo $value->nombre; ?>"/>
									<span class="input-group-btn">
										<label class="btn btn-danger btn-nomargin">
											<span class="glyphicon glyphicon-remove"></span> <input type="checkbox" style="position: relative;top: 2px;" name="borrar[<?php echo $key; ?>]" />
										</label>
									</span>
								</div>
								<?php endforeach ?>

								<div class="entry input-group col-lg-12">
									<input class="form-control" name="categorias[]" type="text" placeholder="Nombre de categor&iacute;a" value=""/>
									<span class="input-group-btn">
										<button class="btn btn-default btn-add btn-nomargin" type="button">
											<span class="glyphicon glyphicon-plus"></span>
										</button>
									</span>
								</div>

								<div style="padding-top: 20px;" class="form-group col-lg-12">
									<a class="btn btn-margin btn-default" href="<?php echo DIR; ?>evento/<?php echo $data['evento']->internalURL; ?>"><i class="fa fa-chevron-circle-left"></i> Volver</a>
									<button name="submit" type="submit" class="btn btn-margin btn-default"><i class="fa fa-check"></i> Actualizar</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>