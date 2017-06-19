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
				<li>
					<a class="page-scroll" href="<?php echo DIR; ?>eventos">Eventos</a>
				</li>
				<li>
					<a class="page-scroll" target="_blank" href="<?php echo DIR; ?>evento/<?php echo $data['evento']->internalURL; ?>">Ver evento</a>
				</li>

				<?php $hooks->run('menuCuenta', $data); ?>
			</ul>
		</div>
		<!-- /.navbar-collapse -->
	</div>
	<!-- /.container-fluid -->
</nav>

<header class="header-eventos">
	<div class="header-content">
		<div class="header-content-inner">
			<h1>Cambio de Plan</h1>
			<hr class="light">
			<p>Selecciones el nuevo plan para este evento</p>
			<form role="form" action="" method="POST">
			<input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>" />
			<div class="col-sm-6 col-sm-push-3">
				<?php if (!empty($error['mensajes'])): ?>
				<div class="alert alert-danger">
					<?php foreach ($error['mensajes'] as $value): ?>
					<?php echo $value; ?><br/>
					<?php endforeach ?>
				</div>
				<?php endif ?>

				<div class="form-group col-xs-12 <?php if (isset($error['campos']['tipoSubscripcion'])) echo 'has-error'; ?>">
					<label class="control-label">Plan</label>
					<div class="input-group col-xs-12">
						<div class="col-xs-12 <?php if (isset($error['campos']['tipoSubscripcion'])) echo 'has-error'; ?>">
							<select class="form-control" name="tipoSubscripcion" >
								<option value>Seleccione un plan</option>
								<?php 
								foreach ($data['planes'] as $key => $value)
								{
									if($data['evento']->tipoSubscripcion == $value->id)
										echo "<option value=".$value->id." selected>".$value->nombre."</option>";
									else
										echo "<option value=".$value->id.">".$value->nombre."</option>";
								}
								?>
							</select>
						</div>
					</div>
				</div>

				<div class="form-group col-sm-12">
					<a class="btn btn-default" href="<?php echo DIR; ?>evento/<?php echo $data['evento']->internalURL; ?>"><i class="fa fa-chevron-circle-left"></i> Volver</a>
					<button name="submit" type="submit" class="btn btn-primary"><i class="fa fa-arrow-circle-right"></i> Cambiar</button>
				</div>
			</div>
		</form>
		</div>
	</div>
</header>

<?php
	$isColor = true;
?>
<?php foreach ($data['planes'] as $keyP => $valueP): ?>
<section <?php if($isColor) echo "class=\"bg-primary\""; ?>>
	<div class="container">
		<div class="row">
			<div class="col-xs-12 text-center">
				<h2><?php echo $valueP->nombre; ?></h2>
				<hr class="<?php if(!$isColor) echo "primary";else echo "light"; ?>"/>
				<p <?php if($isColor) echo "class=\"text-faded\""; ?>>
				<?php echo $valueP->categorias; ?> categor&iacute;as<br/>
				<?php echo $valueP->atletas; ?> atletas<br/>
				<?php echo $valueP->patrocinadores; ?> patrocinadores<br/>
				</p>
			</div>
		</div>
	</div>
</section>
<?php $isColor = !$isColor; endforeach ?>