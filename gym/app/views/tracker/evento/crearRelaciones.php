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
			<hr class="light">

			<p>Finalice la creaci&oacute;n del evento asignando los WOD a las categor&iacute;as correspondientes.</p>
		</div>
		<div class="container"> 
			<?php if (!empty($error['mensajes'])): ?>
				<div class="alert alert-danger">
					<?php foreach ($error['mensajes'] as $value): ?>
					<?php echo $value; ?><br/>
					<?php endforeach ?>
				</div>
			<?php endif ?>
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
		margin-bottom: 8px !important;
	}
</style>

<?php
	$countCaetgoria = 1;
	$isColor = true;
?>
<form role="form" class="form-inline" method="POST" action="">
<input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>" required/>
<?php foreach ($data['categorias'] as $key => $value): ?>
<section <?php if($isColor) echo "class=\"bg-primary\""; ?>>
	<div class="container">
		<div class="row">
			<div class="col-lg-8 col-lg-offset-2 text-center">
				<h2><?php echo $value->nombre; ?></h2>
				<hr class="<?php if(!$isColor) echo "primary";else echo "light"; ?>"/>
				<p <?php if($isColor) echo "class=\"text-faded\""; ?>></p>
				<div class="relContainer">
				<?php foreach ($data['wods'] as $keyW => $valueW): ?>
					<div class="container-fluid">
						<div class="row">
							<div class="input-group"  style="min-width: 100%;">
								<span class="input-group-addon" style="width: 50px;">
									<input style="position: relative;top: 2px;" type="checkbox" name="wodR[<?php echo $key; ?>][]" value="<?php echo $keyW; ?>">
								</span>
								<span class="input-group-addon" style="width:auto;white-space: normal;">
									<label class=""><?php echo $valueW->nombre; ?></label>
								</span>
								<span class="input-group-addon" style="width:100px;">
									<span class="input-group-btn">
										<button type="button" class="btn btn-up" aria-label="...">
											<i class="fa fa-arrow-up"></i>
										</button>
										<button type="button" class="btn btn-down" aria-label="...">
											<i class="fa fa-arrow-down"></i>
										</button>
									</span>
								</span>
							</div><!-- /input-group -->
						</div>
					</div>
				<?php endforeach ?>
				</div>
			</div>
		</div>
	</div>
</section>
<?php $isColor = !$isColor; endforeach ?>
<section <?php if($isColor) echo "class=\"bg-primary\""; ?>>
	<div class="container">
		<div class="row">
			<div class="col-lg-8 col-lg-offset-2 text-center">
				<h2>Finalizar!</h2>
				<hr class="<?php if(!$isColor) echo "primary";else echo "light"; ?>"/>
				<p <?php if($isColor) echo "class=\"text-faded\""; ?>>Ya has finalizado la creacion del evento!</p>

				<div class="form-group">
					<button name="submit" type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Terminar</button>
				</div>
			</div>
		</div>
	</div>
</section>
</form>