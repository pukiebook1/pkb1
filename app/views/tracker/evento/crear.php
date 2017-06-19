<?php
use Core\Language;
use Helpers\Hooks;
use Helpers\Codes;
 
$hooks = Hooks::get();

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
				<?php $hooks->run('menuCuenta', $data); ?>
			</ul>
		</div>
	</div>
</nav>

<header class="header-crearevento">
	<div class="header-content">
		<div class="container">
			<h1>Creaci&oacute;n de Evento</h1>
			<hr class="light">

			<p>Especifique el nombre del evento y sus detalles</p>
		</div>
		<div class="container">    
			<form action="crear" method="POST">
				<input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>" />
				<div class="col-sm-6 col-sm-push-3">
					<?php if (!empty($error['mensajes'])): ?>
					<div class="alert alert-danger">
						<?php foreach ($error['mensajes'] as $value): ?>
						<?php echo $value; ?><br/>
						<?php endforeach ?>
					</div>
					<?php endif ?>
					<div class="form-group col-sm-12 <?php if (isset($error['campos']['disciplina'])) echo 'has-error'; ?>">
						<label class="control-label">Disciplina del evento</label>
							<select class="form-control" name="disciplina">
								<option value selected>Seleccionar...</option>
								<option value="1"><?php echo Codes::getEventoNombre(1); ?></option>
								<option value="2"><?php echo Codes::getEventoNombre(2); ?></option>
								<option value="3"><?php echo Codes::getEventoNombre(3); ?></option>
								<option value="4"><?php echo Codes::getEventoNombre(4); ?></option>
								<option value="5"><?php echo Codes::getEventoNombre(5); ?></option>
								<option value="6"><?php echo Codes::getEventoNombre(6); ?></option>
								<option value="7"><?php echo Codes::getEventoNombre(7); ?></option>
								<option value="8"><?php echo Codes::getEventoNombre(8); ?></option>
								<option value="9"><?php echo Codes::getEventoNombre(9); ?></option>
								<option value="10"><?php echo Codes::getEventoNombre(10); ?></option>
								<option value="11"><?php echo Codes::getEventoNombre(11); ?></option>
								<option value="12"><?php echo Codes::getEventoNombre(12); ?></option>
							</select>
					</div>

					<div class="form-group col-sm-6 <?php if (isset($error['campos']['nombre'])) echo 'has-error'; ?>">
						<label class="control-label">Nombre del evento</label>
						<input type="text" name="nombre" class="form-control" id="" value="<?php echo $error['data']['nombre']; ?>" >
					</div>

					<div class="form-group col-sm-6 <?php if (isset($error['campos']['urlEvento'])) echo 'has-error'; ?>">
						<label class="control-label">P&aacute;gina web del evento</label>
						<input type="text" name="urlEvento" class="form-control" id="" value="<?php echo $error['data']['urlEvento']; ?>" >
					</div>

					<div class="form-group col-sm-12 <?php if (isset($error['campos']['infobank'])) echo 'has-error'; ?>">
						<label class="control-label">Informacion bancaria de pago (Si no quiere que sea publica dejar en blanco).</label>
						<input type="text" name="infobank" class="form-control" id="" value="<?php echo $error['data']['infobank']; ?>" >
					</div>                     

					<div class="form-group col-xs-12 <?php if (isset($error['campos']['fechaEvento'])) echo 'has-error'; ?>">
						<label class="control-label">Fecha del evento</label>
						<div class="col-xs-12">
							<div class="col-xs-2 <?php if (isset($error['campos']['fechaD'])) echo 'has-error'; ?>">
								<select class="form-control" name="fechaD" >
									<option value>D&iacute;a</option>
									<?php 
										$dd = 1;
										while ($dd <= 31)
										{
											if($dd == $error['data']['fechaD'])
												echo "<option value=".$dd." selected>".$dd."</option>";
											else
												echo "<option value=".$dd.">".$dd."</option>";
											$dd++;
										}
									?>
								</select>
							</div>
							<div class="col-xs-2  <?php if (isset($error['campos']['fechaM'])) echo 'has-error'; ?>">
								<select class="form-control" name="fechaM" >
									<option value>Mes</option>
									<?php 
										$mm = 1;
										while ($mm <= 12)
										{
											if($mm == $error['data']['fechaM'])
												echo "<option value=".$mm." selected>".$mm."</option>";
											else
												echo "<option value=".$mm.">".$mm."</option>";
											$mm++;
										}
									?>
								</select>
							</div>
							<div class="col-xs-2 <?php if (isset($error['campos']['fechaA'])) echo 'has-error'; ?>">
								<select class="form-control" name="fechaA">
									<option value>A&ntilde;o</option>
									<?php 
										$yy = date('Y') - 1;
										while ($yy <= date('Y')+3)
										{
											if($yy == $error['data']['fechaA'])
												echo "<option value=".$yy." selected>".$yy."</option>";
											else
												echo "<option value=".$yy.">".$yy."</option>";
											$yy++;
										}
									?>
								</select>
							</div>
							<div class="col-xs-6  <?php if (isset($error['campos']['zonaHoraria'])) echo 'has-error'; ?>">
								<select class="form-control" name="zonaHoraria">
									<option value selected>Seleccione zona horaria del evento</option>
									<?php foreach ($data['zonas'] as $key => $value): ?>
										<?php if ($value->offset == $error['data']['zonaHoraria']): ?>
											<option value="<?php echo $value->offset; ?>" selected><?php echo $value->nombre; ?></option>
										<?php else: ?>
											<option value="<?php echo $value->offset; ?>"><?php echo $value->nombre; ?></option>
										<?php endif ?>
									<?php endforeach ?>
								</select>
							</div>
						</div>
					</div>

					<div class="form-group col-xs-12 <?php if (isset($error['campos']['horaEvento'])) echo 'has-error'; ?>">
						<label class="control-label">Hora del evento</label>
						<div class="col-xs-12">
							<div class="col-xs-2 col-xs-offset-3 <?php if (isset($error['campos']['horaH'])) echo 'has-error'; ?>">
								<select class="form-control" name="horaH" >
									<?php 
										$hh = 0;
										while ($hh <= 23)
										{
											if($hh == $error['data']['horaH'])
												echo "<option value='".$hh."' selected>".($hh)."</option>";
											else
												echo "<option value='".$hh."'>".($hh)."</option>";
											$hh++;
										}
									?>
								</select>
							</div>
							<div class="col-xs-2"><p>:</p></div>
							<div class="col-xs-2  <?php if (isset($error['campos']['horaM'])) echo 'has-error'; ?>">
								<select class="form-control" name="horaM" >
									<?php 
										$mm = 0;
										while ($mm <= 59)
										{
											if($mm == $error['data']['horaM'])
												echo "<option value='".$mm."' selected>".($mm)."</option>";
											else
												echo "<option value='".$mm."'>".($mm)."</option>";
											$mm++;
										}
									?>
								</select>
							</div>
						</div>
					</div>

					<div class="form-group col-xs-12">
						<a class="btn btn-margin btn-default" href="<?php echo DIR; ?>cuenta"><i class="fa fa-chevron-circle-left"></i> Cancelar</a>
						<button name="submit" type="submit" class="btn btn-margin btn-primary"><i class="fa fa-arrow-circle-right"></i> Siguiente</button>
					</div>                               
				</div>
			</form>
		</div>
	</div>
</header>