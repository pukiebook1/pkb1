<?php
use Core\Language;
use Helpers\Hooks;
use Helpers\Date;
 
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
			<h1>Registro de resultados</h1>
			<hr class="light">

			<p>Evento: <?php echo $data['evento']->nombre; ?></p>
			<p><i class="fa fa-tag" aria-hidden="true"></i> <?php echo $data['categoria']->nombre; ?></p>
			<p><i class="fa fa-link"></i> <?php echo $data['wod']->nombre; ?></p>
			<?php if( ($data['wod']->tipo == 3) && $data['wod']->permPenalizacion ): ?>
			<p>Repeticiones: <?php echo $data['wod']->repsRound; ?></p>
			<p>Time Cap: <?php echo Date::timeToFullString($data['wod']->timeCap*1000); ?></p>
			<?php endif; ?>
			<?php if( $data['wod']->tipo == 4 ): ?>

			<?php if($data['wod']->repsRound > 0): ?>
			<p>Repeticiones Por Ronda: <?php echo $data['wod']->repsRound; ?></p>
			<?php endif; ?>

			<?php if($data['wod']->time > 0): ?>
			<p>Tiempo del WOD: <?php echo Date::timeToFullString($data['wod']->time*1000); ?></p>
			<?php endif; ?>
			
			<?php endif; ?>
			<a target="_blank" class="btn btn-default" href="<?php echo DIR; ?>evento/<?php echo $data['evento']->internalURL; ?>/<?php echo $data['categoria']->id; ?>">Ver tabla de resultados</a>
		</div>
		<div class="container"> 

		</div>
	</div>
</header>


<style type="text/css">
	.entry:not(:first-of-type)
	{
		/*margin-top: 10px;*/
	}

	.glyphicon
	{
		font-size: 12px;
	}

	.btn-nomargin{
		margin: 0;
		border-width: 0;
	}

	.form-group
	{
		margin-bottom: 0px;
	}

	input, select
	{
		margin: 5px 0;
	}


</style>

<section class="bg-primary">
	<div class="container">
		<div class="row">
			<div class="col-xs-10 col-xs-offset-1 text-center">
				<h2>Registre los resultados</h2>
				<hr class="light"/>
				<p class="text-faded"></p>

				<div class="controls">  
					<form role="form" autocomplete="off" action="" method="POST">
						<input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>" required/>

						<table class="score-table" align="center">
							<thead>
								<tr><th>Resultados</th></tr>
							</thead>

							<tbody>
								<tr class="entry">
									<td style="padding: 10px;">
										<div class="col-md-12 text-center" style="display: inline-flex;">
											<div class="col-md-4" style="margin: auto 0px;">
												<select name="participante[]" class="form-control">
													<option value>Seleccionar Participante...</option>
													<?php foreach ($data['atletas'] as $key => $value): ?>
															
														<option value="<?php echo $value->id; ?>">
															<?php if ($value->resCargado): ?>
																[*] 
															<?php endif; ?>
															<?php echo $value->personaId." - ".$value->nombre." ".$value->apellido; ?>
														</option>

													<?php endforeach ?>
												</select>

												<div class="row"></div>
												
												<div class="col-md-12">
													<input name="juez[]" type="num" class="form-control" placeholder="Juez">
												</div>
											</div>

											<div class="col-md-7" style="margin: auto 0px;">								
												<?php if ($data['evento']->disciplina == 9): ?>
												<div class="col-md-2">
													<select class="form-control" name="unidad[]">
														<option value="1" selected>Kg.</option>
														<option value="2">Lb.</option>
													</select>
												</div>
												<?php else: ?>
												<div class="col-md-2">
													<?php if ($data['wod']->tipo == 3): ?>
														<input name="reps[]" type="num" class="form-control" placeholder="Reps Realizadas" <?php if(!$data['wod']->permPenalizacion) echo "disabled"; ?>>
													<?php endif; ?>
													<?php if ($data['wod']->tipo == 4): ?>
														<input name="resultado[]" type="num" class="form-control" placeholder="Reps Realizadas">
													<?php endif; ?>
													<?php if ($data['wod']->tipo == 9): ?>
														<input name="peso[]" type="num" class="form-control" placeholder="Bodyweight" <?php if(!$data['wod']->permTieBreak) echo "disabled"; ?>>
													<?php endif; ?>
												</div>
												<?php endif; ?>

												<div class="form-group col-md-10">
												<?php if ($data['evento']->disciplina == 9): ?>
													<div class="input-group col-xs-12">
														<div class="col-xs-4">
															<input name="intento1[]" type="num" class="form-control" placeholder="Attempt 1">
														</div>
														<div class="col-xs-4">
															<input name="intento2[]" type="num" class="form-control" placeholder="Attempt 2">
														</div>
														<div class="col-xs-4">
															<input name="intento3[]" type="num" class="form-control" placeholder="Attempt 3">
														</div>
													</div>
												<?php else: ?>
													<?php if ( $data['wod']->tipo == 3 ): ?>
														<div class="input-group col-xs-12">
															<div class="col-xs-4">
																<input name="horas[]" type="num" class="form-control tiempo" placeholder="HH">
															</div>
															<div class="col-xs-4">
																<input name="minutos[]" type="num" class="form-control tiempo" placeholder="MM">
															</div>
															<div class="col-xs-4">
																<input name="segundos[]" type="num" class="form-control tiempo" placeholder="SS">
															</div>
														</div>
													<?php else: ?>
														<?php if ($data['wod']->tipo == 4): ?>
															<div class="input-group col-xs-12">
																<div class="col-xs-4">
																	<input name="horas[]" type="num" class="form-control tiempo" placeholder="HH (Tie Break)" <?php if(!$data['wod']->permTieBreak) echo "disabled"; ?>>
																</div>
																<div class="col-xs-4">
																	<input name="minutos[]" type="num" class="form-control tiempo" placeholder="MM (Tie Break)" <?php if(!$data['wod']->permTieBreak) echo "disabled"; ?>>
																</div>
																<div class="col-xs-4">
																	<input name="segundos[]" type="num" class="form-control tiempo" placeholder="SS (Tie Break)" <?php if(!$data['wod']->permTieBreak) echo "disabled"; ?>>
																</div>
															</div>
														<?php else: ?>
															<div class="input-group col-xs-12">
																<div class="col-xs-12">
																	<input type="num" name="resultado[]" class="form-control resultado" value="" placeholder="Resultado">
																</div>
															</div>
														<?php endif; ?>
													<?php endif; ?>
												<?php endif; ?>
												</div>

												<?php if($data['evento']->disciplina == 10): ?>
												<div class="row"></div>

												<div class="col-md-2" style="height: 44px;display: inline-flex;">
													<label class="control-label checkbox-inline" style="margin: auto auto;">
														<input class="checkbox" name="scaled[]" type="checkbox" value="1"/> Scaled
													</label>
												</div>

												<div class="col-md-10">
													<input name="video[]" type="text" class="form-control" placeholder="Video">
												</div>


												<?php endif; ?>
											</div>

											<div class="col-md-1" style="margin:auto 0px;">
												<button class="btn btn-default btn-add" type="button">
													<span class="glyphicon glyphicon-plus"></span>
												</button>
											</div>

										</div>
									</td>

								</tr>
							</tbody>
						</table>
				        <div class="row" style="margin-top:20px;">
				            <div class="col-lg-8 col-lg-offset-2 text-center">
				                <div class="form-group">
				                    <a class="btn btn-margin btn-default" href="<?php echo DIR; ?>cuenta/modevento/<?php echo $data['evento']->internalURL; ?>/resultados/<?php echo $data['categoria']->id; ?>"><i class="fa fa-chevron-circle-left"></i> Volver</a>
				                    <button name="submit" type="submit" class="btn btn-margin btn-default"><i class="fa fa-check"></i> Actualizar</button>
				                </div>
				            </div>
				        </div>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>