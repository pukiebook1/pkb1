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
		/*border-radius: 12px !important;*/
	}

</style>

<section>
	<div class="container">
		<div class="row">
			<div class="col-lg-8 col-lg-offset-2 text-center">
				<h2>Equipos Pendientes</h2>			
				<p>
					<?= count($data['atletas']); ?> equipos pendientes<br/>
					<?= $data['evento']->atletasPlan - count($data['atletasRegistrados']); ?> cupos disponibles
				</p>
				<hr class="faded"/>
				<!-- <p class="text-faded">--</p> -->

				<?php if (!empty($error['mensajes'])): ?>
				<div class="alert alert-danger">
					<?php foreach ($error['mensajes'] as $value): ?>
					<?php echo $value; ?><br/>
					<?php endforeach ?>
				</div>
				<?php endif ?>

				<form role="form" autocomplete="off" action="" method="POST">
					<input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>" required/>

					<?php if (!empty($data['atletas'])): ?>
					<table class="score-table" align="center">
						<thead>
							<tr>
								<th>Nombre</th>
								<th>Categor&iacute;a</th>
								<th>Aprobar</th>
								<th>Rechazar</th>
								<?php if ($data['evento']->reqPago): ?>
								<th>Pago</th>
								<?php else: ?>
								<th>Observaciones</th>
								<?php endif ?>
							</tr>
						</thead>

						<tbody>
							<?php foreach ($data['atletas'] as $keyAtl => $valueAtl): ?>
							<tr>
								<td>
									<div class="atletaContainer">
										<div class="atleta">
											<a class="btn" target="_blank" href="<?php echo DIR; ?>equipo/<?php echo $valueAtl->id;?>"><?php echo $valueAtl->nombre; ?></a>
										</div>
									</div>
								</td>
								<td>
									<select class="form-control" name="pendiente[<?php echo $keyAtl; ?>]">
										<option value>Seleccionar...</option>
										<?php foreach ($data['categorias'] as $key => $value): ?>
										<option value="<?php echo $key;?>" <?php if($valueAtl->categoriaId == $key) echo "selected"; ?>><?php echo $value->nombre; ?></option>
										<?php endforeach ?>
									</select>
								</td>
								<td><input class="form-control" name="opcion[<?php echo $keyAtl; ?>]" type="radio" value="ok"/></td>
								<td><input class="form-control" name="opcion[<?php echo $keyAtl; ?>]" type="radio" value="rechazar"/></td>

									<?php if (isset($data['pagos'][$keyAtl]->id)): ?>
									<td><a target="_blank" href="<?php echo DIR; ?>pagosEventoEquipo/<?php echo $data['pagos'][$keyAtl]->id; ?>" class="btn">Ver</a></td>
									<?php else: ?>
										<td></td>
									<?php endif ?>
								
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
					<?php else: ?>
					<p>No se encontraron solicitudes de registro para este evento.</p>
					<?php endif ?>

					<div class="row"></div>
					
					<div style="margin-top: 35px;" class="col-lg-12 text-center">
						<div class="form-group">
							<a class="btn btn-default btn-margin" href="<?php echo DIR; ?>evento/<?php echo $data['evento']->internalURL; ?>"><i class="fa fa-chevron-circle-left"></i> Volver</a>
							<button name="submit" type="submit" class="btn btn-default btn-margin"><i class="fa fa-check"></i> Actualizar</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>

	<section class="bg-primary">
		<div class="container">
			<div class="row">
			</div>
		</div>
	</section>
</form>