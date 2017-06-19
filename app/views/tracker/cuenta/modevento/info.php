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
				<?php $hooks->run('menu', $data); ?>
			</ul>
		</div>
	</div>
</nav>

<section class="bg-primary header-crearevento">
	<div class="container">
		<div class="col-lg-12 text-center">
			<div class="imgContainerRanking">
				<img src="<?php echo $data['evento']->fotoPath;?>"/>    
				<span class="helper"></span>
			</div>
			<br/>
			<h1>Modificaci&oacute;n de Evento</h1>
			<p>
				Plan del evento: <?php echo $data['evento']->nombrePlan; ?>
				<br/>
				<i class="fa fa-tag" aria-hidden="true"></i> <?php echo $data['evento']->disciplinaStr; ?>
			</p>

			<form role="form" action="" method="POST" enctype="multipart/form-data">
				<input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>" />
				<div class="col-md-10 col-md-push-1">
					<?php if (!empty($error['mensajes'])): ?>
					<div class="alert alert-danger">
						<?php foreach ($error['mensajes'] as $value): ?>
						<?php echo $value; ?><br/>
						<?php endforeach ?>
					</div>
					<?php endif ?>

					<div class="col-xs-12 otras-opciones">
						<?php if($data['logeado']['persona']->esAdmin): ?>
						<div class="col-lg-12 form-group <?php if (isset($error['campos']['disciplina'])) echo 'has-error'; ?>">
							<div class="col-lg-12">
								<label class="control-label">Disciplina del evento</label>
							</div>

							<div class="col-lg-12">
								<select class="form-control" name="disciplina">
									<option value="1" <?php if($error['data']['disciplina'] == 1) echo "selected"; ?>><?php echo Codes::getEventoNombre(1); ?></option>
									<option value="2" <?php if($error['data']['disciplina'] == 2) echo "selected"; ?>><?php echo Codes::getEventoNombre(2); ?></option>
									<option value="3" <?php if($error['data']['disciplina'] == 3) echo "selected"; ?>><?php echo Codes::getEventoNombre(3); ?></option>
									<option value="4" <?php if($error['data']['disciplina'] == 4) echo "selected"; ?>><?php echo Codes::getEventoNombre(4); ?></option>
									<option value="5" <?php if($error['data']['disciplina'] == 5) echo "selected"; ?>><?php echo Codes::getEventoNombre(5); ?></option>
									<option value="6" <?php if($error['data']['disciplina'] == 6) echo "selected"; ?>><?php echo Codes::getEventoNombre(6); ?></option>
									<option value="7" <?php if($error['data']['disciplina'] == 7) echo "selected"; ?>><?php echo Codes::getEventoNombre(7); ?></option>
									<option value="8" <?php if($error['data']['disciplina'] == 8) echo "selected"; ?>><?php echo Codes::getEventoNombre(8); ?></option>
									<option value="9" <?php if($error['data']['disciplina'] == 9) echo "selected"; ?>><?php echo Codes::getEventoNombre(9); ?></option>
									<?php if ($error['data']['disciplina'] == 10): ?>
									<option value="10" selected><?php echo Codes::getEventoNombre(10); ?></option>
									<?php endif; ?>
									<option value="11" <?php if($error['data']['disciplina'] == 11) echo "selected"; ?>><?php echo Codes::getEventoNombre(11); ?></option>
									<option value="12" <?php if($error['data']['disciplina'] == 12) echo "selected"; ?>><?php echo Codes::getEventoNombre(12); ?></option>
								</select>
							</div>
						</div>
						<?php endif ?>
						
						<div class="col-lg-6 form-group <?php if (isset($error['campos']['nombre'])) echo 'has-error'; ?>">
							<div class="col-lg-12">
								<label class="control-label">Nombre del evento</label>
							</div>
							<div class="col-lg-12">
								<input type="text" name="nombre" class="form-control" id="" value="<?php echo $error['data']['nombre']; ?>" >
							</div>
						</div>

						<div class="col-lg-6 form-group <?php if (isset($error['campos']['urlEvento'])) echo 'has-error'; ?>">
							<div class="col-lg-12">
								<label class="control-label">P&aacute;gina web del evento</label>
							</div>
							<div class="col-lg-12">
								<input type="text" name="urlEvento" class="form-control" id="" value="<?php echo $error['data']['urlEvento']; ?>" >
							</div>
						</div> 

						<div class="col-lg-12 form-group <?php if (isset($error['campos']['customurl'])) echo 'has-error'; ?>">
							<div class="col-lg-12">
								<label class="control-label">
								Nombre de URL personalizada<br/>
								<span style="font-size: 10px;">Ej: pkb -> http://pukiebook.com/evento/pkb - SOLO LETRAS.</span>
								</label>
							</div>
							<div class="col-lg-12">
								<input type="text" name="customurl" class="form-control" id="" value="<?php echo $error['data']['customurl']; ?>" >
							</div>
						</div>                    

						<div class="col-lg-12 <?php if (isset($error['campos']['fechaEvento'])) echo 'has-error'; ?>">
							<div class="col-xs-12 form-group">
								<label class="control-label">Fecha del evento</label>
							</div>

							<div class="row" style="margin:2px;">
								<div class="col-xs-2 form-group <?php if (isset($error['campos']['fechaD'])) echo 'has-error'; ?>">
									<select class="form-control" name="fechaD" >
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
								<div class="col-xs-2 form-group <?php if (isset($error['campos']['fechaM'])) echo 'has-error'; ?>">
									<select class="form-control" name="fechaM" >
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
								<div class="col-xs-2 form-group <?php if (isset($error['campos']['fechaA'])) echo 'has-error'; ?>">
									<select class="form-control" name="fechaA">
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

								<div class="col-xs-6 form-group <?php if (isset($error['campos']['zonaHoraria'])) echo 'has-error'; ?>">
									<!-- <input name="horaM" class="form-control" type="number" min="0" max="59" value="0"/> -->
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

						<div class="col-lg-12 form-group <?php if (isset($error['campos']['horaEvento'])) echo 'has-error'; ?>">
							<div class="col-lg-12">
								<label class="control-label">Hora del evento</label>
							</div>
							<div class="row">
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
						</div>
					</div>

					<div class="col-xs-12 otras-opciones">
						<div class="col-xs-12">
							<h4>Otros Ajustes</h4>
						</div>

						<div class="col-xs-12" style="margin: 10px 0;">
							<div class="col-md-4 form-group checkbox">
								<div class="col-xs-8"><label for="ch1" class="control-label">Permitir registros</label></div>
								<div class="col-xs-4"><input id="ch1" name="permiteRegistros" type="checkbox" <?php if($data['evento']->permiteRegistros) echo "checked"; ?>/></div>
							</div>

							<div class="col-md-4 form-group checkbox">
								<div class="col-xs-8"><label for="ch2" class="control-label ">Evento visible</label></div>	
								<div class="col-xs-4"><input id="ch2" name="visible" type="checkbox" <?php if($data['evento']->visible) echo "checked"; ?>/></div>
							</div>

							<?php if($data['evento']->disciplina != 10): ?>
							<div class="col-md-4 form-group checkbox">
								<div class="col-xs-8"><label for="ch3" class="control-label">WODs visibles</label></div>
								<div class="col-xs-4"><input id="ch3" name="wodsvisible" type="checkbox" <?php if($data['evento']->wodsvisible) echo "checked"; ?>/></div>
							</div>
							<?php else: ?>
							<div class="col-md-4 form-group checkbox">
								<div class="col-xs-8"><label for="ch4" class="control-label">Videos de participantes visibles</label></div>
								<div class="col-xs-4"><input id="ch4" name="showVideo" type="checkbox" <?php if($data['evento']->showVideo) echo "checked"; ?>/></div>
							</div>
							<?php endif; ?>

							<div class="col-md-4 form-group checkbox">
								<div class="col-xs-8"><label for="ch5" class="control-label">Solicitar detalles de pago</label></div>
								<div class="col-xs-4"><input id="ch5" name="reqPago" type="checkbox" <?php if($data['evento']->reqPago) echo "checked"; ?>/></div>
							</div>

							<div class="col-md-4 form-group checkbox">
								<div class="col-xs-8"><label for="ch6" class="control-label">Evento por equipos</label></div>
								<div class="col-xs-4"><input id="ch6" onchange="tipoRegistroChange(this)" name="tipoRegistro" type="checkbox" <?php if(strcmp($data['evento']->tipoRegistro, 'E')==0) echo "checked"; ?>/></div>
							</div>
						</div>

						<div class="col-xs-12">
							<div class="col-md-4 col-md-offset-2 form-group">
								<label id="fileupload-evento-label" for="fileupload-evento" class="form-control btn btn-default">
								<input id="fileupload-evento" style="display:none;" name="archivoFoto" type="file" value=""><i class="fa fa-file-image-o"></i> Cambiar logo del evento</label>
							</div>

							<div class="col-md-4 form-group checkbox">
								<label for="logoRemove" class="form-control btn btn-default">
								<input id="logoRemove" name="removerLogo" type="checkbox"/>Eliminar logo</label>
							</div>
						</div>
					</div>


					<div class="col-xs-12 otras-opciones" id="panelEquipo">
						<div class="col-xs-12">
							<h4>Ajustes de Equipo</h4>
						</div>

						<div class="col-xs-12">
							<div class="col-lg-6">
								<div class="col-lg-3 form-group">
									<label class="control-label">Cant. Integrantes</label>
								</div>

								<div class="col-lg-3 form-group">
									<select class="form-control" name="eqCantIntegrantes">
										<option value="0" <?php if($data['evento']->eqCantIntegrantes == 0) echo "selected"; ?>>Libre</option>
										<option value="2" <?php if($data['evento']->eqCantIntegrantes == 2) echo "selected"; ?>>2</option>
										<option value="3" <?php if($data['evento']->eqCantIntegrantes == 3) echo "selected"; ?>>3</option>
										<option value="4" <?php if($data['evento']->eqCantIntegrantes == 4) echo "selected"; ?>>4</option>
										<option value="5" <?php if($data['evento']->eqCantIntegrantes == 5) echo "selected"; ?>>5</option>
										<option value="6" <?php if($data['evento']->eqCantIntegrantes == 6) echo "selected"; ?>>6</option>
									</select>
								</div>
							</div>

							<div class="col-lg-6 col-xs-12">
								<div class="col-xs-12 checkbox">
									<div class="col-xs-8"><label for="ch7" class="control-label">Integrantes del mismo g&eacute;nero</label></div>
									<div class="col-xs-4"><input id="ch7" name="eqMismoGenero" type="checkbox" <?php if($data['evento']->eqMismoGenero) echo "checked"; ?>/></div>
								</div>							
							</div>					
						</div>
					</div>

					<script type="text/javascript">
					$(function()
					{
						var ch = $('#ch6').is(':checked');

						if(!ch)
						{
							$('#panelEquipo').hide();
						}
					});
					</script>
					<div class="col-xs-12 form-group">
						<a class="btn btn-margin btn-default" href="<?php echo DIR; ?>evento/<?php echo $data['evento']->internalURL; ?>"><i class="fa fa-chevron-circle-left"></i> Volver</a>
						<button name="submit" type="submit" class="btn btn-margin btn-default"><i class="fa fa-check"></i> Actualizar</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</section>