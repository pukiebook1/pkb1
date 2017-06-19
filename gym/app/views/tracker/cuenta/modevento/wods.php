<?php
use Core\Language;
use Helpers\Hooks;
 
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
				<?php $hooks->run('menu', $data); ?>
			</ul>
		</div>
	</div>
</nav>

<header class="header-crearevento">
	<div class="header-content">
		<div class="container">
			<h1>Modificaci&oacute;n de WODs</h1>
			<p>Plan de evento: <?php echo $data['evento']->nombrePlan; ?></p>
			<hr class="light">

			<p>Modifique los WODs del evento</p>
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
	}

	.form-control
	{
		margin-bottom: 5px !important;
	}

	.entry
	{
		padding-bottom: 9px;
		background-color: rgb(241, 198, 0);
		padding-top: 4px;
		border-radius: 10px 10px 10px 10px;
		-moz-border-radius: 10px 10px 10px 10px;
		-webkit-border-radius: 10px 10px 10px 10px;
		border: 2px solid #C7A300;
	}

	.wodHead
	{
		font-weight: bold;
		padding: 5px;
		margin-bottom: 15px;
		border-radius: 10px 10px 10px 10px;
		-moz-border-radius: 10px 10px 10px 10px;
		-webkit-border-radius: 10px 10px 10px 10px;
		border-bottom: 1px solid #C7A300;
	}

</style>

<section>
	<div class="container">
		<div class="row">
			<div class="col-md-8 col-md-offset-2 text-center">
				<div class="control-group" id="fields">
					<h2 class="section-heading">WODs del evento</h2>
					<hr>
					<div class="controls">    
						<form role="form" autocomplete="off" action="" method="POST">
							<input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>" required/>
							<div class="col-md-8 col-md-push-2">
								
								<?php if (!empty($error['mensajes']['general'])): ?>
								<div class="alert alert-danger">
									<?php foreach ($error['mensajes']['general'] as $value): ?>
									<?php echo $value; ?><br/>
									<?php endforeach ?>
								</div>
								<?php endif ?>

								<?php foreach ($data['wods'] as $key => $value): ?>
								<div class="entry form-group col-md-12">
									<div class="col-md-12 wodHead">
										<span>WOD</span>
									</div>

									<?php if (!empty($error['mensajes'][$key])): ?>
									<div class="col-md-12">
										<div class="alert alert-danger">
											<?php foreach ($error['mensajes'][$key] as $keyE => $valueE): ?>
											<?php echo $valueE; ?><br/>
											<?php endforeach ?>
										</div>
									</div>
									<?php endif ?>

									<div class="form-group col-md-7">
										<label class="control-label">Nombre del WOD</label>
										<input class="form-control" name="wod[<?php echo $key; ?>]" type="hidden" value="<?php echo $key; ?>"/>
										<input class="form-control" name="nombres[<?php echo $key; ?>]" type="text" value="<?php echo $value->nombre; ?>"/>
									</div>

									<div class="wodTipo">
										<div class="form-group col-md-5">
											<label class="control-label">Tipo de WOD</label>
											<select onchange="tipoWod(this)" class="form-control" name="tipos[<?php echo $key; ?>]">
												<option value>Seleccionar...</option>
												<option value="3" <?php if($value->tipo == 3) echo "selected" ?>>For Time</option>
												<option value="4" <?php if($value->tipo == 4) echo "selected" ?>>AMRAP</option>
												<option value="5" <?php if($value->tipo == 5) echo "selected" ?>>Repeticiones</option>
												<option value="8" <?php if($value->tipo == 8) echo "selected" ?>>Posici&oacute;n</option>
												<option value="9" <?php if($value->tipo == 9) echo "selected" ?>>Weightlifting</option>
											</select>
										</div>

										<div <?php if( $value->tipo != 9 ) echo "style=\"display: none;\"" ?> class="form-group col-md-12 weight">
											<label class="control-label checkbox-inline">
												<input class="checkbox" name="pesoCorporal[<?php echo $key;?>]" type="checkbox" value="1" <?php if($value->tienePenalizacion) echo "checked"; ?>/> Peso Corporal
											</label>
										</div>

										<div <?php if( ($value->tipo != 4) && ($value->tipo != 3) ) echo "style=\"display: none;\"" ?> class="form-group col-md-12 amrap fortime">
											<label class="control-label checkbox-inline">
												<input class="checkbox" name="permTieBreak[<?php echo $key;?>]" type="checkbox" value="1" <?php if($value->permTieBreak) echo "checked"; ?>/> Permitir Tie Break
											</label>
										</div>

										<div <?php if( ($value->tipo != 3) && ($value->tipo != 4) ) echo "style=\"display: none;\"" ?> class="form-group col-md-12 fortime amrap">
											<label class="control-label amrap"  <?php if( $value->tipo != 4  ) echo "style=\"display: none;\"" ?>>Repeticiones Por Ronda</label>
											<label class="control-label fortime"  <?php if(  $value->tipo != 3 ) echo "style=\"display: none;\"" ?>>Repeticiones</label>
											<input class="form-control" name="repsRounds[<?php echo $key;?>]" type="num" value="<?php echo $value->repsRound; ?>"/>
										</div>

										<div <?php if( ($value->tipo != 3) && ($value->tipo != 4) ) echo "style=\"display: none;\"" ?> class="form-group col-md-12 fortime amrap">
											<label <?php if($value->tipo != 3) echo "style=\"display: none;\"" ?> class="control-label checkbox-inline fortime">
												<input name="permPenalizaciones[<?php echo $key; ?>]" type="hidden" value="0"/>
												<input onchange="checkChange(this)" class="checkbox" name="permPenalizaciones[<?php echo $key; ?>]" type="checkbox" value="1" <?php if($value->permPenalizacion) echo "checked"; ?>/>
												 Time Cap (<span class="small">Para Penalizaci&oacute;n</span>)
											</label>
											
											<label <?php if($value->tipo != 4) echo "style=\"display: none;\"" ?> class="control-label amrap">Tiempo</label>

											<div class="input-group col-xs-12">
												<div class="col-xs-4">
													<input <?php if( ($value->tipo != 3) || (!$value->permPenalizacion) ) echo "style=\"display: none;\"" ?> name="horas[<?php echo $key;?>]" type="num" class="form-control tiempo" placeholder="HH" value="<?php echo $value->horas; ?>">
												</div>
												<div class="col-xs-4">
													<input <?php if( ($value->tipo != 3) || (!$value->permPenalizacion) ) echo "style=\"display: none;\"" ?> name="minutos[<?php echo $key;?>]" type="num" class="form-control tiempo" placeholder="MM" value="<?php echo $value->minutos; ?>">
												</div>
												<div class="col-xs-4">
													<input <?php if( ($value->tipo != 3) || (!$value->permPenalizacion) ) echo "style=\"display: none;\"" ?> name="segundos[<?php echo $key;?>]" type="num" class="form-control tiempo" placeholder="SS" value="<?php echo $value->segundos; ?>">
												</div>
											</div>
										</div>
									</div>

									<div class="row"></div>
									<hr/>

									<?php if($data['evento']->disciplina == 10): ?>
									<div class="form-group col-md-12">
										<label class="control-label amrap">Apertura de WOD</label>
										<div class="input-group col-xs-12">
											<div class="col-xs-4">
												<input name="diaA[<?php echo $key;?>]" type="num" class="form-control tiempo" placeholder="D&iacute;a" value="<?php echo $value->diaA; ?>"/>
											</div>
											<div class="col-xs-4">
												<input name="mesA[<?php echo $key;?>]" type="num" class="form-control tiempo" placeholder="Mes" value="<?php echo $value->mesA; ?>"/>
											</div>
											<div class="col-xs-4">
												<input name="annoA[<?php echo $key;?>]" type="num" class="form-control tiempo" placeholder="A&ntilde;o" value="<?php echo $value->annoA; ?>"/>
											</div>
										</div>

										<div class="input-group col-xs-12">
											<div class="col-xs-3 col-xs-offset-2">
												<select class="form-control" name="horaA[<?php echo $key;?>]" >
													<?php 
														$hh = 0;
														while ($hh <= 23)
														{
															if($hh == $value->horaA)
																echo "<option value='".$hh."' selected>".($hh)."</option>";
															else
																echo "<option value='".$hh."'>".($hh)."</option>";
															$hh++;
														}
													?>
												</select>
											</div>
											<div class="col-xs-2"><p>:</p></div>
											<div class="col-xs-3">
												<select class="form-control" name="minutoA[<?php echo $key;?>]">
													<?php 
														$mm = 0;
														while ($mm <= 59)
														{
															if($mm == $value->minutoA)
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

									<div class="form-group col-md-12">
										<label class="control-label amrap">Cierre de WOD</label>
										<div class="input-group col-xs-12">
											<div class="col-xs-4">
												<input name="diaC[<?php echo $key;?>]" type="num" class="form-control tiempo" placeholder="D&iacute;a" value="<?php echo $value->diaC; ?>"/>
											</div>
											<div class="col-xs-4">
												<input name="mesC[<?php echo $key;?>]" type="num" class="form-control tiempo" placeholder="Mes" value="<?php echo $value->mesC; ?>"/>
											</div>
											<div class="col-xs-4">
												<input name="annoC[<?php echo $key;?>]" type="num" class="form-control tiempo" placeholder="A&ntilde;o" value="<?php echo $value->annoC; ?>"/>
											</div>
										</div>

										<div class="input-group col-xs-12">
											<div class="col-xs-3 col-xs-offset-2">
												<select class="form-control" name="horaC[<?php echo $key;?>]" >
													<?php 
														$hh = 0;
														while ($hh <= 23)
														{
															if($hh == $value->horaC)
																echo "<option value='".$hh."' selected>".($hh)."</option>";
															else
																echo "<option value='".$hh."'>".($hh)."</option>";
															$hh++;
														}
													?>
												</select>
											</div>
											<div class="col-xs-2"><p>:</p></div>
											<div class="col-xs-3">
												<select class="form-control" name="minutoC[<?php echo $key;?>]" >
													<?php 
														$mm = 0;
														while ($mm <= 59)
														{
															if($mm == $value->minutoC)
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
									<?php endif; ?>

									<div class="form-group">
										<label class="control-label">Descripci&oacute;n</label>
										<textarea rows="5" class="form-control" name="descripciones[<?php echo $key; ?>]"><?php echo $value->descripcion; ?></textarea>
									</div>

									<span class="col-md-12">
										<label>
											<span class="btn btn-default" >
												<input style="position: relative;top: 2px;" name="borrar[<?php echo $key; ?>]" type="checkbox"/> Remover WOD
											</span>
										</label>
									</span>
								</div>
								<?php endforeach ?>

								<div class="entry form-group col-md-12">
									<div class="col-md-12 wodHead">
										<span>Nuevo WOD</span>
									</div>

									<div class="form-group col-md-7">
										<label class="control-label">Nombre del WOD</label>
										<input class="form-control" name="wod[<?php echo $key; ?>]" type="hidden" value="<?php echo $key; ?>"/>
										<input class="form-control" name="nombres[]" type="text" placeholder="Nombre" />
									</div>

									<div class="wodTipo">
										<div class="form-group col-md-5">
											<label class="control-label">Tipo de WOD</label>
											<select onchange="tipoWod(this)" class="form-control" name="tipos[]">
												<option value>Seleccionar...</option>
												<option value="3">For Time</option>
												<option value="4">AMRAP</option>
												<option value="5">Repeticiones</option>
												<option value="8">Posici&oacute;n</option>
												<option value="9">Weightlifting</option>
											</select>
										</div>

										<div style="display: none;" class="form-group col-md-12 weight">
											<label class="control-label checkbox-inline">
												<input class="checkbox" name="pesoCorporal[]" type="checkbox" value="1"/> Peso Corporal
											</label>
										</div>

										<div style="display: none;" class="form-group col-md-12 amrap fortime">
											<label class="control-label checkbox-inline">
												<input class="checkbox" name="permTieBreak[]" type="checkbox" value="1"/> Permitir Tie Break
											</label>
										</div>

										<div style="display: none;" class="form-group col-md-12 fortime amrap penalfortime">
											<label class="control-label amrap">Repeticiones Por Ronda</label>
											<label class="control-label fortime">Repeticiones</label>
											<input class="form-control" name="repsRounds[]" type="num" placeholder="Repeticiones" />
										</div>

										<div style="display: none;" class="form-group col-md-12 fortime amrap">
											<label class="control-label checkbox-inline fortime">
												<input onchange="checkChange(this)" class="checkbox" name="permPenalizaciones[]" type="checkbox" value="1"/>
												 Time Cap (<span class="small">Para Penalizaci&oacute;n</span>)
											</label>

											<label class="control-label amrap">Tiempo</label>

											<div class="input-group col-xs-12">
												<div class="col-xs-4">
													<input name="horas[]" type="num" class="form-control tiempo" placeholder="HH"/>
												</div>
												<div class="col-xs-4">
													<input name="minutos[]" type="num" class="form-control tiempo" placeholder="MM"/>
												</div>
												<div class="col-xs-4">
													<input name="segundos[]" type="num" class="form-control tiempo" placeholder="SS"/>
												</div>
											</div>
										</div>
									</div>

									<div class="row"></div>
									<hr/>

									<?php if($data['evento']->disciplina == 10): ?>
									<div class="form-group col-md-12">
										<label class="control-label amrap">Apertura de WOD</label>
										<div class="input-group col-xs-12">
											<div class="col-xs-4">
												<input name="diaA[]" type="num" class="form-control tiempo" placeholder="D&iacute;a" value=""/>
											</div>
											<div class="col-xs-4">
												<input name="mesA[]" type="num" class="form-control tiempo" placeholder="Mes" value=""/>
											</div>
											<div class="col-xs-4">
												<input name="annoA[]" type="num" class="form-control tiempo" placeholder="A&ntilde;o" value=""/>
											</div>
										</div>

										<div class="input-group col-xs-12">
											<div class="col-xs-3 col-xs-offset-2">
												<select class="form-control" name="horaA[]" >
													<?php 
														$hh = 0;
														while ($hh <= 23)
														{
															echo "<option value='".$hh."'>".($hh)."</option>";
															$hh++;
														}
													?>
												</select>
											</div>
											<div class="col-xs-2"><p>:</p></div>
											<div class="col-xs-3">
												<select class="form-control" name="minutoA[]">
													<?php 
														$mm = 0;
														while ($mm <= 59)
														{
															echo "<option value='".$mm."'>".($mm)."</option>";
															$mm++;
														}
													?>
												</select>
											</div>
										</div>
									</div>

									<div class="form-group col-md-12">
										<label class="control-label amrap">Cierre de WOD</label>
										<div class="input-group col-xs-12">
											<div class="col-xs-4">
												<input name="diaC[]" type="num" class="form-control tiempo" placeholder="D&iacute;a" value=""/>
											</div>
											<div class="col-xs-4">
												<input name="mesC[]" type="num" class="form-control tiempo" placeholder="Mes" value=""/>
											</div>
											<div class="col-xs-4">
												<input name="annoC[]" type="num" class="form-control tiempo" placeholder="A&ntilde;o" value=""/>
											</div>
										</div>

										<div class="input-group col-xs-12">
											<div class="col-xs-3 col-xs-offset-2">
												<select class="form-control" name="horaC[]" >
													<?php 
														$hh = 0;
														while ($hh <= 23)
														{
															echo "<option value='".$hh."'>".($hh)."</option>";
															$hh++;
														}
													?>
												</select>
											</div>
											<div class="col-xs-2"><p>:</p></div>
											<div class="col-xs-3">
												<select class="form-control" name="minutoC[]" >
													<?php 
														$mm = 0;
														while ($mm <= 59)
														{
															echo "<option value='".$mm."'>".($mm)."</option>";
															$mm++;
														}
													?>
												</select>
											</div>
										</div>
									</div>
									<?php endif; ?>

									<div class="form-group">
										<label class="control-label">Descripci&oacute;n</label>
										<textarea rows="5" class="form-control" name="descripciones[]"></textarea>
									</div>

									<span class="col-sm-12" style="width:100%;">
										<button class="btn btn-default btn-add btn-margin" type="button">
											<span class="glyphicon glyphicon-plus"></span> Agregar WOD
										</button>
									</span>
								</div>

								<div class="form-group col-md-12">
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
</section>