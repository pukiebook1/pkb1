<?php
use Core\Language;

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

			</ul>
		</div>
		<!-- /.navbar-collapse -->
	</div>
	<!-- /.container-fluid -->
</nav>

<header class="header-autenticacion">
	<div class="header-content">
		<div class="container">
			<div class="row">
				<div class="col-lg-12 text-center">
					<h2 class="section-heading">Cambio de Contrase&ntilde;a</h2>
					<hr class="light">
				</div>
			</div>
		</div>
		<div class="container  text-center">    
			<form role="form" action="" method="POST">
				<input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>" />
				<input type="hidden" name="personaId" value="<?php echo $data['personaId']; ?>" />
				<input type="hidden" name="resetCode" value="<?php echo $data['resetCode']; ?>" />
				<div class="col-md-12 ">
					<div class="col-md-6 col-md-push-3">                 
						<?php if (!empty($error['mensajes'])): ?>
						<div class="alert alert-danger">
							<?php foreach ($error['mensajes'] as $value): ?>
							<?php echo $value; ?><br/>
							<?php endforeach ?>
						</div>
						<?php endif ?>

						<div class="form-group col-md-6 col-md-push-3">
							<label class="control-label">Contrase&ntilde;a Nueva</label>
							<input type="password" name="contrasena" class="form-control" id="" value="" required>
						</div>

						<div class="form-group col-md-12"></div>

						<div class="form-group col-md-6 col-md-push-3">
							<label class="control-label">Confirme Contrase&ntilde;a Nueva</label>
							<input type="password" name="contrasenaC" class="form-control" id="" value="" required>
						</div>

						<div class="form-group col-md-12">
							<button name="submit" type="submit" class="btn btn-primary">Cambiar contrase&ntilde;a</button>
						</div>
						
					</div>
				</div>
			</form>
		</div>
	</div>
</header>