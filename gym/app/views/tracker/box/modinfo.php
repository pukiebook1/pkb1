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
			<div class="imgContainerRanking">
				<img src="<?php echo $data['box']->fotoPath;?>"/>    
				<span class="helper"></span>
			</div>
			<br/>
			<h1>Modificaci&oacute;n de Box</h1>
			<hr class="light">

			<p>Modifique el nombre del box y sus detalles</p>
		</div>
		<div class="container">    
			<form role="form" action="" method="POST" enctype="multipart/form-data">
				<input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>" />
				<div class="col-md-6 col-md-push-3">
					<?php if (!empty($error['mensajes'])): ?>
					<div class="alert alert-danger">
						<?php foreach ($error['mensajes'] as $value): ?>
						<?php echo $value; ?><br/>
						<?php endforeach ?>
					</div>
					<?php endif ?>

					<div class="form-group col-lg-6 <?php if (isset($error['campos']['nombre'])) echo 'has-error'; ?>">
						<label class="control-label">Nombre del Box</label>
						<input type="text" name="nombre" class="form-control" id="" value="<?php echo $error['data']['nombre']; ?>" >
					</div>

					<div class="form-group col-lg-6 <?php if (isset($error['campos']['urlBox'])) echo 'has-error'; ?>">
						<label class="control-label">P&aacute;gina web del Box</label>
						<input type="text" name="urlBox" class="form-control" id="" value="<?php echo $error['data']['urlBox']; ?>" >
					</div> 

					<div class="form-group col-lg-12 <?php if (isset($error['campos']['coach'])) echo 'has-error'; ?>">
						<label class="control-label">Coach del Box</label>
						<input type="text" name="coach" class="form-control" id="" value="<?php echo $error['data']['coach']; ?>" >
					</div> 

					<div class="form-group col-sm-12 <?php if (isset($error['campos']['pais'])) echo 'has-error'; ?>">
						<label class="control-label">Pa&iacute;s del Box</label>
						<select class="form-control" name="pais">
							<option value>Seleccionar...</option>
							<?php foreach ($data['paises'] as $key => $value): ?>
								<?php if ($value->id == $error['data']['pais']): ?>
									<option value="<?php echo $value->id;?>" selected><?php echo $value->country_name;?></option>
								<?php else: ?>
									<option value="<?php echo $value->id;?>"><?php echo $value->country_name;?></option>
								<?php endif ?>
							
							<?php endforeach ?>
						</select>
					</div>  

					<div class="form-group col-lg-8 <?php if (isset($error['campos']['logoBox'])) echo 'has-error'; ?>">
						<label id="fileupload-evento-label" for="fileupload-evento" class="form-control btn btn-default"><i class="fa fa-file-image-o"></i> Cambiar logo del box</label>
						<input id="fileupload-evento" style="display:none;" name="logoBox" type="file" value="">
					</div>

					<div style="margin-top: 0px" class="form-group col-lg-4 checkbox <?php if (isset($error['campos']['logoBox'])) echo 'has-error'; ?>">
						<label class="form-control btn btn-default"><input name="removerLogo" type="checkbox"/>Eliminar logo</label>
					</div>

					<div class="form-group col-lg-12">
						<a class="btn btn-margin btn-default" href="<?php echo DIR; ?>cuenta/misboxes/"><i class="fa fa-chevron-circle-left"></i> Volver</a>
						<button name="submit" type="submit" class="btn btn-margin btn-default"><i class="fa fa-check"></i> Actualizar</button>
					</div>                               
				</div>
			</form>
		</div>
	</div>
</header>