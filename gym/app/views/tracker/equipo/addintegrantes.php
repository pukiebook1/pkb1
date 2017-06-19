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
				<?php $hooks->run('menu', $data); ?>
			</ul>
		</div>
	</div>
</nav>

<header class="header-crearevento">
	<div class="header-content">
		<div class="container"> 
			<div class="col-lg-8 col-lg-offset-2 text-center">
				<div class="control-group" id="fields">
					<h2 class="section-heading">Registro de Integrantes en Equipo</h2>
					<hr class="light">
					<?php if (!empty($error['mensajes'])): ?>
						<div class="alert alert-danger">
							<?php foreach ($error['mensajes'] as $value): ?>
							<?php echo $value; ?><br/>
							<?php endforeach ?>
						</div>
					<?php endif ?>
					<?php if (!empty($error['repetidos'])): ?>
					<div class="alert alert-warning alert-dismissable">
						<strong>Aviso: </strong>Ha ingresado atletas que ya se encuentran registrados.
					</div>
					<?php endif ?>

					<?php if (!empty($error['errores'])): ?>
					<div class="alert alert-warning alert-dismissable">
						<strong>Aviso: </strong>Ha ingresado ID de atletas incorrectos: (<?php echo implode(", ", $error['errores']); ?>)
					</div>
					<?php endif ?>

					<?php if (!empty($error['errores']) || !empty($error['repetidos'])): ?>
						<div class="alert alert-warning alert-dismissable">
							Los valores con error y repetidos fueron retirados autom&aacute;ticamente.
						</div>
					<?php endif ?>

					<div class="controls">
						<form role="form" autocomplete="off" action="" method="POST">
							<input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>" required/>

							<?php if(!$data['equipo']->dedicado): ?>
							<div class="col-md-6 col-md-push-3">
								<div class="form-group col-sm-12">
									<label class="control-label">ID o Correo Electr&oacute;nico de participantes (por linea)</label>
									<textarea class="form-control" rows="5" name="ids"><?php echo $error['data']; ?></textarea>
								</div>

								<div class="form-group col-lg-12">
									<a class="btn btn-default" href="<?php echo DIR; ?>equipo/<?php echo $data['equipo']->id; ?>"><i class="fa fa-chevron-circle-left"></i> Volver</a>
									<button name="submit" type="submit" class="btn btn-default"><i class="fa fa-check"></i> Siguiente</button>
								</div>                               
							</div>
							<?php else: ?>
							<div class="relContainer">
								<?php foreach ($data['atletasCategoria'] as $keyA => $valueA): ?>
									<div class="container-fluid">
										<div class="row">
											<div class="input-group"  style="min-width: 100%;">
												<span class="input-group-addon" style="width: 50px;">
												<?php if(!$valueA->ocupado): ?>
													<input style="position: relative;top: 2px;" type="checkbox" name="ids[<?php echo $valueA->personaId; ?>]" value="<?php echo $valueA->personaId; ?>"<?php if($valueA->ocupado) echo "checked"; ?>>
												<?php endif; ?>
												</span>
												<span class="input-group-addon" style="width:auto;white-space: normal;">
													<label class=""><?php echo $valueA->nombre." ".$valueA->apellido; ?></label>
												</span>
											</div><!-- /input-group -->
										</div>
									</div>
								<?php endforeach ?>
							</div>
							<div class="row">
								<div class="col-xs-12 text-center">
									<div class="form-group">
										<a class="btn btn-default btn-margin" href="<?php echo DIR; ?>evento/<?php echo $data['evento']->internalURL; ?>"><i class="fa fa-chevron-circle-left"></i> Volver</a>
										<button name="submit" type="submit" class="btn btn-default btn-margin"><i class="fa fa-check"></i> Actualizar</button>
									</div>
								</div>
							</div>
							<?php endif; ?>
						</form>
					</div>
				</div>
			</div>
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
		/*border-radius: 12px !important;*/
	}

</style>