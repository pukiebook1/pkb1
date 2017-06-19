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
                <?php $hooks->run('menuCuenta', $data); ?>
			</ul>
		</div>
	</div>
</nav>

<header class="header-crearevento">
	<div class="header-content">
		<div class="container">
			<h1>Creaci&oacute;n de Equipo</h1>
			<hr class="light">

			<!-- <p>Especifique el nombre del equipo.</p> -->
		</div>
		<div class="container">    
			<form role="form" method="POST">
				<input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>" />
				<div class="col-sm-6 col-sm-push-3">
					<?php if (!empty($error['mensajes'])): ?>
					<div class="alert alert-danger">
						<?php foreach ($error['mensajes'] as $value): ?>
						<?php echo $value; ?><br/>
						<?php endforeach ?>
					</div>
					<?php endif ?>
					<div class="form-group col-sm-12 <?php if (isset($error['campos']['nombre'])) echo 'has-error'; ?>">
						<label class="control-label">Nombre del Equipo</label>
						<input type="text" name="nombre" class="form-control" id="" value="<?php echo $error['data']['nombre']; ?>" >
					</div>
              		<?php if(isset($data["eventoTeam"]) && false): ?>
					<div class="form-group col-sm-12">
						<label class="control-label">Categoria del Equipo</label>
						<div class="row"></div>
						<?php foreach ($data['categorias'] as $key => $value): ?>
						<div class="radio ">
						 	<label><input type="radio" name="categoria" value="<?= $key; ?>"><?= $value->nombre; ?></label>
						</div>
						<?php endforeach; ?>
					</div>
					<?php endif; ?>
              
					<div class="form-group col-sm-12">
						<a class="btn btn-margin btn-default" href="<?php echo DIR; ?>cuenta"><i class="fa fa-chevron-circle-left"></i> Cancelar</a>
						<button name="submit" type="submit" class="btn btn-margin btn-primary"><i class="fa fa-arrow-circle-right"></i> Registrar</button>
					</div>                               
				</div>
			</form>
		</div>
	</div>
</header>