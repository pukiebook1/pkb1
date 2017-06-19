<?php
use Core\Language;

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
			</ul>
		</div>
	</div>
</nav>

<header class="header-autenticacion">
	<div class="header-content">
		<div class="header-content-inner">
			<div class="col-sm-12 text-center">
				<h2 class="section-heading">Registro</h2>
				<hr class="light">
				<form role="form" action="registrar" method="POST">
					<input type="hidden" name="previous" value="<?php echo $data['previ']; ?>" />
					<input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>" />
					<div class="col-sm-8 col-sm-offset-2">
						<?php if (!empty($error['mensajes'])): ?>
						<div class="alert alert-danger">
							<?php foreach ($error['mensajes'] as $value): ?>
							<?php echo $value; ?><br/>
							<?php endforeach ?>
						</div>
						<?php endif ?>

						<div class="form-group col-sm-6 <?php if (isset($error['campos']['nombre'])) echo 'has-error'; ?>">
							<label class="control-label">Nombre</label>
							<input type="text" name="nombre" class="form-control" value="<?php echo $error['data']['nombre']; ?>" required>
						</div>

						<div class="form-group col-sm-6 <?php if (isset($error['campos']['apellido'])) echo 'has-error'; ?>">
							<label class="control-label">Apellido</label>
							<input type="text" name="apellido" class="form-control" value="<?php echo $error['data']['apellido']; ?>"  required>
						</div>

						<div class="form-group col-sm-6 <?php if (isset($error['campos']['correo'])) echo 'has-error'; ?>">
							<label class="control-label">Correo Electr&oacute;nico</label>
							<input type="email" name="correo" class="form-control" value="<?php echo $error['data']['correo']; ?>"  required>
						</div>

						<div class="form-group col-sm-6 <?php if (isset($error['campos']['ident'])) echo 'has-error'; ?>">
							<label class="control-label">Identificaci&oacute;n (C&eacute;d. / Pasaporte)</label>
							<input type="text" name="ident" class="form-control" value=""  required>
						</div>
						
						<div class="form-group col-sm-6 <?php if (isset($error['campos']['sexo'])) echo 'has-error'; ?>">
							<label class="control-label">Sexo</label>
							<select name="sexo" class="form-control">
								<option value="X">Ninguno</option>
								<option value="F">Femenino</option>
								<option value="M">Masculino</option>
							</select>
						</div>

						<div class="form-group col-sm-6 <?php if (isset($error['campos']['contrasena'])) echo 'has-error'; ?>">
							<label class="control-label">Contrase&ntilde;a</label>
							<input type="password" name="contrasena" class="form-control" value=""  required>
						</div>

						<div class="form-group col-sm-12">
							<button name="submit" type="submit" class="btn btn-margin btn-primary">Registrar</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</header>