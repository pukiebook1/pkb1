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
		<div class="col-lg-10 col-lg-offset-1 text-center">
			<h2>Participantes Registrados</h2>
			<p>Plan del evento: <?php echo $data['evento']->nombrePlan; ?></p>
			<p>
				<?php echo count($data['atletas']); ?> participantes registrados<br/>
				<?= $data['evento']->atletasPlan - count($data['atletas']); ?> cupos disponibles
			</p>
			<p><a target="_blank" class="btn btn-default" href="<?php echo DIR; ?>cuenta/modevento/<?php echo $data['evento']->internalURL; ?>/participantesExportable">Descargar listado de participantes</a></p>
		
			<hr class="faded"/>
			<!-- <p>--</p> -->
			
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
							<th>Remover</th>
							<th>Pago</th>
						</tr>
					</thead>

					<tbody>
						<?php foreach ($data['atletas'] as $keyAtl => $valueAtl): ?>
						<tr>
							<td>
								<div class="atletaContainer">
									<div class="atleta">
										<a class="btn" target="_blank" href="<?php echo DIR; ?>perfil/<?php echo $valueAtl->personaId;?>"><?php echo $valueAtl->nombre." ".$valueAtl->apellido; ?></a>
									</div>
								</div>
							</td>
							<td>
							<select class="form-control" name="registro[<?php echo $keyAtl; ?>]">
								<option value>Seleccionar...</option>
								<?php foreach ($data['categorias'] as $key => $value): ?>
									<option <?php if($valueAtl->categoriaId == $key) echo "selected" ?> value="<?php echo $key;?>"><?php echo $value->nombre; ?></option>
								<?php endforeach ?>
							</select>
							</td>
							<td><input class="form-control chk1" name="borrar[<?php echo $keyAtl; ?>]" type="checkbox"/></td>

							<?php if (isset($valueAtl->pagoId)): ?>
								<td><a target="_blank" href="<?php echo DIR; ?>pagosEvento/<?php echo $valueAtl->pagoId; ?>" class="btn">Ver</a></td>
							<?php else: ?>
								<td></td>
							<?php endif ?>

						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
				<?php else: ?>
				<p>No hay participantes en este evento.</p>
				<?php endif ?>

				<div class="row"></div>
				
				<div style="margin-top: 35px;" class="col-lg-8 col-lg-offset-2 text-center">
					<div class="form-group">
						<a class="btn btn-default btn-margin" href="<?php echo DIR; ?>evento/<?php echo $data['evento']->internalURL; ?>"><i class="fa fa-chevron-circle-left"></i> Volver</a>
						<button name="submit" type="submit" class="btn btn-default btn-margin"><i class="fa fa-check"></i> Actualizar</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</section>