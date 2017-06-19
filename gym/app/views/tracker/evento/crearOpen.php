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
			<h1>Creaci&oacute;n de Evento (Open)</h1>
			<hr class="light">

			<p>Especifique el nombre del evento y sus detalles</p>
		</div>
		<div class="container">    
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
					<div class="form-group col-sm-6 <?php if (isset($error['campos']['nombre'])) echo 'has-error'; ?>">
						<label class="control-label">Nombre del evento</label>
						<input type="text" name="nombre" class="form-control" id="" value="<?php echo $error['data']['nombre']; ?>" >
					</div>

					<div class="form-group col-sm-6 <?php if (isset($error['campos']['urlEvento'])) echo 'has-error'; ?>">
						<label class="control-label">P&aacute;gina web del evento</label>
						<input type="text" name="urlEvento" class="form-control" id="" value="<?php echo $error['data']['urlEvento']; ?>" >
					</div>                    

<!-- 					<div class="form-group col-sm-12 <?php if (isset($error['campos']['fechaEvento'])) echo 'has-error'; ?>">
						<label class="control-label">Fecha del evento</label>
						<div class="input-append date form_datetime">
							<input class="form-control"  size="16" type="text" value="<?php echo $error['data']['fechaEvento']; ?>" readonly>
							<span class="add-on"><i class="icon-th"></i></span>
						</div>
						<input name="fechaEvento" type="hidden" id="mirror_field" value="<?php echo $error['data']['fechaEvento']; ?>" readonly />
					</div>    -->  

					<div class="form-group col-xs-12 <?php if (isset($error['campos']['fechaEvento'])) echo 'has-error'; ?>">
						<label class="control-label">Fecha del evento</label>
						<div class="col-xs-12">
							<div class="col-xs-4 <?php if (isset($error['campos']['fechaD'])) echo 'has-error'; ?>">
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
							<div class="col-xs-4  <?php if (isset($error['campos']['fechaM'])) echo 'has-error'; ?>">
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
							<div class="col-xs-4 <?php if (isset($error['campos']['fechaA'])) echo 'has-error'; ?>">
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
						</div>
					</div>

					<div class="form-group col-xs-12 <?php if (isset($error['campos']['horaEvento'])) echo 'has-error'; ?>">
						<label class="control-label">Hora del evento</label>
						<div class="col-xs-12">
							<div class="col-xs-2 col-xs-offset-3 <?php if (isset($error['campos']['horaH'])) echo 'has-error'; ?>">
								<!-- <input name="horaH" class="form-control" type="number" min="0" max="23" value="0"/> -->
								<select class="form-control" name="horaH" >
									<!-- <option value="0">Hora</option> -->
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
								<!-- <input name="horaM" class="form-control" type="number" min="0" max="59" value="0"/> -->
								<select class="form-control" name="horaM" >
									<!-- <option value="0">Minutos</option> -->
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

					<div class="form-group col-sm-12">
						<a class="btn btn-margin btn-default" href="<?php echo DIR; ?>cuenta"><i class="fa fa-chevron-circle-left"></i> Cancelar</a>
						<button name="submit" type="submit" class="btn btn-margin btn-primary"><i class="fa fa-arrow-circle-right"></i> Siguiente</button>
					</div>                               
				</div>
			</form>
		</div>
	</div>
</header>