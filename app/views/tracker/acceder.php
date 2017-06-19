<?php
use Core\Language;
 session_start();
require 'requirelanguage.php';
if (isset($_GET["language"])){
  $_SESSION["language"]=$_GET["language"];
  header ('Location:'.$_SERVER['HTTP_REFERER']);
  }
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
		<div class="container">
			<div class="row">
				<div class="col-lg-12 text-center">
					<h2 class="section-heading"><?php echo $acceder1; ?></h2>
					<hr class="light">
				</div>
			</div>
		</div>
		<div class="container  text-center">    
			<form action="acceder" method="POST">
				<input type="hidden" name="previous" value="<?php echo $data['previ']; ?>" />
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
							<label class="control-label"><?php echo $acceder2; ?></label>
							<input type="email" name="correo" class="form-control" value="<?php echo $error['data']['correo']; ?>" required>
						</div>

						<div class="form-group col-md-12"></div>

						<div class="form-group col-md-6 col-md-push-3 <?php if (isset($error['campos']['contrasena'])) echo 'has-error'; ?>">
							<label class="control-label"><?php echo $acceder3; ?></label>
							<input type="password" name="contrasena" class="form-control" value="" required>
						</div>

						<div class="form-group col-md-12">
							<a class="btn btn-link" href="<?php echo DIR; ?>lost"><?php echo $acceder4; ?></a>
						</div>

						<div class="form-group col-md-12">
							<button name="submit" type="submit" class="btn btn-primary"><?php echo $acceder1; ?></button>
						</div>
						
					</div>
				</div>
			</form>
		</div>
	</div>
</header>