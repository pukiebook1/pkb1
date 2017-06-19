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
			<h1>Modificaci&oacute;n de Equipo</h1>
			<hr class="light">

			<p>Asigne los participantes al equipo.</p>
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
</style>

<?php
	$isColor = true;
?>
<form role="form" class="form-inline" method="POST" action="">
<input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>" required/>
<section <?php if($isColor) echo "class=\"bg-primary\""; ?>>
	<div class="container">
		<div class="row">
			<div class="col-xs-12 text-center">
				<hr class="<?php if(!$isColor) echo "primary";else echo "light"; ?>"/>
				<p <?php if($isColor) echo "class=\"text-faded\""; ?>></p>
				<div class="relContainer">
					<?php foreach ($data['atletas'] as $keyA => $valueA): ?>
					 	<div class="container-fluid">
							<div class="row">
								<div class="input-group"  style="min-width: 100%;">
									<span class="input-group-addon" style="width: 50px;">
									<?php if(!$valueA->ocupado): ?>
										<input style="position: relative;top: 2px;" type="checkbox" name="wodR[<?php echo $valueA->personaId; ?>]" value="<?php echo $valueA->personaId; ?>"<?php if($valueA->ocupado) echo "checked"; ?>>
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
			</div>
		</div>
	</div>
</section>
<?php $isColor = !$isColor; ?>
<section <?php if($isColor) echo "class=\"bg-primary\""; ?>>
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