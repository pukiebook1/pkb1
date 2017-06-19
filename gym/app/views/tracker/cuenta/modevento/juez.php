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
		<div class="row">
			<div class="col-lg-8 col-lg-offset-2 text-center">
				<div class="control-group" id="fields">
					<h2 class="section-heading">Jueces del evento</h2>
					<hr class="light">
					<?php if (!empty($error['mensajes'])): ?>
						<div class="alert alert-danger">
							<?php foreach ($error['mensajes'] as $value): ?>
							<?php echo $value; ?><br/>
							<?php endforeach ?>
						</div>
					<?php endif ?>
					<div class="controls">    
						<form role="form" autocomplete="off" action="" method="POST">
							<input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>" required/>
							<div class="col-md-6 col-md-push-3">

								<?php if ($data['juezP']): ?>
									<div class="juezContainer">

										<img class="img-circle" width="45px" height="45px" src="<?php echo $data['juezP']->fotoPath; ?>"/>
										<div class="nombreJuez"><a class="btn btn-link" target="_blank" href="<?php echo DIR; ?>perfil/<?php echo $data['juezP']->id;?>"><?php echo $data['juezP']->nombre." ".$data['juezP']->apellido; ?></a></div>
										
										<label>
											<span class="btn btn-default" >
												<input style="" name="borrar[0]" type="checkbox"/> Remover Juez
											</span>
										</label>
									</div>
								<?php else: ?>
									<div class="form-group">
										<label class="control-label">Juez Principal (Num. de cuenta Pukiebook)</label>
										<input class="form-control" name="juez[0]" type="text"/>
									</div>
								<?php endif ?>


								<?php if ($data['juezS']): ?>
									<div class="juezContainer">
										<img class="img-circle" width="45px" height="45px" src="<?php echo $data['juezS']->fotoPath; ?>"/>
										<div class="nombreJuez"><a class="btn btn-link" target="_blank" href="<?php echo DIR; ?>perfil/<?php echo $data['juezS']->id;?>"><?php echo $data['juezS']->nombre." ".$data['juezS']->apellido; ?></a></div>
										
										<label>
											<span class="btn btn-default" >
												<input style="" name="borrar[1]" type="checkbox"/> Remover Juez
											</span>
										</label>
									</div>
								<?php else: ?>
									<div class="form-group">
										<label class="control-label">Juez Suplente (Num. de cuenta Pukiebook)</label>
										<input class="form-control" name="juez[1]" type="text"/>
									</div>
								<?php endif ?>

								<div style="padding-top: 20px;" class="form-group col-lg-12">
									<a class="btn btn-default btn-margin" href="<?php echo DIR; ?>evento/<?php echo $data['evento']->internalURL; ?>"><i class="fa fa-chevron-circle-left"></i> Volver</a>
									<button name="submit" type="submit" class="btn btn-default btn-margin"><i class="fa fa-check"></i> Actualizar</button>
								</div>                               
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
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
	}
</style>
