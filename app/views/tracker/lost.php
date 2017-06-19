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
					<h2 class="section-heading">Recuperaci&oacute;n de Contrase&ntilde;a</h2>
					<hr class="light">
					<p>Introduzca su correo electr&oacute;nico asociado y se enviar&aacute; un correo con la solicitud de recuperaci&oacute;n.</p>
				</div>
			</div>
		</div>
		<div class="container  text-center">    
			<form role="form" action="" method="POST">
				<input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>" />
				<div class="col-md-12 ">
					<div class="col-md-6 col-md-push-3">                 
						<?php if (!empty($error['mensajes'])): ?>
						<div class="alert alert-danger">
							<?php foreach ($error['mensajes'] as $value): ?>
							<?php echo $value; ?><br/>
							<?php endforeach ?>
						</div>
						<?php endif ?>

						<div class="form-group col-md-6 col-md-push-3 <?php if (isset($error['campos']['correo'])) echo 'has-error'; ?>">
							<label class="control-label">Correo Electr&oacute;nico</label>
							<input type="email" name="correo" class="form-control" id="" value="<?php echo $error['data']['correo']; ?>" required>
						</div>

						<div class="form-group col-md-12">
							<button name="submit" type="submit" class="btn btn-primary">Continuar</button>
						</div>
						
					</div>
				</div>
			</form>
		</div>
	</div>
</header>