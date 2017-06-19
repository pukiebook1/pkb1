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
			<h1>Modificaci&oacute;n de Patrocinadores</h1>
			<p>Plan del evento: <?php echo $data['evento']->nombrePlan; ?></p>
			<hr class="light">

			<p>Asigne los patrocinadores para este evento.</p>
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
		border-radius: 12px !important;
	}
	.form-control
	{
		/*margin-bottom: 8px !important;*/
	}
	.sponsorImg
	{
		width: 50px;
		height: 50px;
	}
</style>

<?php
	$isColor = true;
?>
<form role="form" class="form-inline" method="POST" action="" enctype="multipart/form-data">
<input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>" required/>

<section>
	<div class="container">
		<div class="row">
			<div class="col-xs-12 text-center">
				<h2>Patrocinadores</h2>
				<hr class="primary"/>
				<p></p>
				<div class="relContainer">
					<?php foreach ($data['patrocinadores'] as $key => $valueP): ?>
					 	<div class="container-fluid sponsor">
					 		<input type="hidden" name="orden[]" value="<?php echo $valueP->id; ?>"/>
							<div class="row">
								<div class="input-group"  style="min-width: 100%;">
									<span class="input-group-addon" style="width: 50px;">
										<img class="sponsorImg" src="<?php echo DIR.SPONSORPICTUREPATH.$valueP->archivoFoto;?>">
									</span>
									<span class="input-group-addon img-col" style="width: 50px;">
										<label id="fileupload-sponsor-label" class="form-control btn btn-default">
										<input id="fileupload-sponsor" style="display:none;" name="logo[<?php echo $valueP->id; ?>]" type="file" value="">
										<i class="fa fa-file-image-o"></i> Cambiar Logo</label>
										<label class="status hid" style="color:green;"><i class="fa fa-check-square-o"></i></label>
									</span>
									<span class="input-group-addon" style="width:auto;white-space: normal;">
										<label class=""><input type="text" name="nombre[<?php echo $valueP->id; ?>]" value="<?php echo $valueP->nombre; ?>"></label>
									</span>
<!-- 									<span class="input-group-addon" style="width:auto;white-space: normal;">
										<label class=""><input type="text" name="url[<?php echo $valueP->id; ?>]" value="<?php echo $valueP->url; ?>"></label>
									</span> -->
									<span class="input-group-addon" style="width:50px;">
										<button class="btn btn-default btn-remove-sponsor btn-margin" type="button">
											<span class="glyphicon glyphicon-minus"></span>
										</button>
									</span>
									<span class="input-group-addon" style="width:100px;">
										<span class="input-group-btn">
											<button type="button" class="btn btn-default btn-up" aria-label="...">
												<i class="fa fa-arrow-up"></i>
											</button>
											<button type="button" class="btn btn-default btn-down" aria-label="...">
												<i class="fa fa-arrow-down"></i>
											</button>
										</span>
									</span>
								</div><!-- /input-group -->
							</div>
						</div>
					<?php endforeach ?>
					 	<div class="container-fluid sponsor">
					 		<input type="hidden" name="orden[]" value=""/>
							<div class="row">
								<div class="input-group"  style="min-width: 100%;">
									<span class="input-group-addon img-col" style="width: 127px;">
										<!-- <input type="file" name="logoN[]"> -->
										<label id="fileupload-sponsor-label" class="form-control btn btn-default">
										<input id="fileupload-sponsor" style="display:none;" name="logoN[]" type="file" value="">
										<i class="fa fa-file-image-o"></i> Logo</label>
										<label class="status hid" style="color:green;"><i class="fa fa-check-square-o"></i></label>
									</span>
									<span class="input-group-addon" style="width:auto;white-space: normal;">
										<label class=""><input type="text" name="nombreN[]" value=""></label>
									</span>
<!-- 									<span class="input-group-addon" style="width:auto;white-space: normal;">
										<label class=""><input type="text" name="urlN[]" value=""></label>
									</span> -->
									<span class="input-group-addon" style="width:50px;">
										<button class="btn btn-default btn-add-sponsor btn-margin" type="button">
											<span class="glyphicon glyphicon-plus"></span>
										</button>
									</span>
									<span class="input-group-addon" style="width:100px;">
										<span class="input-group-btn">
											<button type="button" class="btn btn-default btn-up" aria-label="...">
												<i class="fa fa-arrow-up"></i>
											</button>
											<button type="button" class="btn btn-default btn-down" aria-label="...">
												<i class="fa fa-arrow-down"></i>
											</button>
										</span>
									</span>
								</div><!-- /input-group -->
							</div>
						</div>

				</div>
			</div>
		</div>
	</div>
</section>

<section class="bg-primary">
	<div class="container">
		<div class="row">
			<div class="col-xs-12 text-center">
				<div class="form-group">
					<a class="btn btn-default btn-margin" href="<?php echo DIR; ?>evento/<?php echo $data['evento']->internalURL; ?>"><i class="fa fa-chevron-circle-left"></i> Volver</a>
					<button name="submit" type="submit" class="btn btn-default btn-margin"><i class="fa fa-check"></i> Actualizar</button>
				</div>
			</div>
		</div>
	</div>
</section>
</form>