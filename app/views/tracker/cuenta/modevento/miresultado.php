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
			<h1>Registro de resultado</h1>
			<hr class="light"/>

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
	</div>
</header>


<style type="text/css">
	.glyphicon
	{
		font-size: 12px;
	}

	.btn-nomargin
	{
		margin: 0;
		border-width: 0;
	}

	input, select
	{
		margin: 10px 0;
	}
</style>

<section class="bg-primary">
	<div class="container">
		<div class="row">
			<div class="col-xs-10 col-xs-offset-1 text-center">
				<h2>Registre su resultado</h2>
				<hr class="light"/>
				<p class="text-faded"></p>

				<div class="controls">  
					<form role="form" autocomplete="off" action="" method="POST">
						<input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>" required/>

						<table class="score-table" align="center">
							<thead>
								<tr><th>Resultado <?= implode(", ", array_keys($data['resultados'])); ?></th></tr>
							</thead>
							<tbody>
								<tr class="entry">
									<td style="padding: 10px;">
										<div class="col-md-12 text-center">
											<?php if ( $data['wod']->tipo == 3 ): ?>
												<!-- Res. FT -->
												<div class="col-md-10">
													<label>Resultado</label>
												</div>

												<div class="row"></div>

												<div class="col-md-2">
													
													<input name="horasRes" type="num" class="form-control" placeholder="Horas">
												</div>

												<div class="col-md-4">
													<input name="minutosRes" type="num" class="form-control" placeholder="Minutos">
												</div>
												<div class="col-md-4">
													<input name="segundosRes" type="num" class="form-control" placeholder="Segundos">
												</div>
											<?php else: ?>
												<?php if ( $data['wod']->tipo == 4 ): ?>
													<!-- Res. AMRAP -->
													<div class="col-md-10">
                                                                                                                <label>Ingrese el numero de repeticiones completadas.</label>
														<input name="resultado" type="num" class="form-control" placeholder="Reps Realizadas">
													</div>
												<?php else: ?>
													<?php if ( $data['wod']->tipo == 9 ): ?>
														<!-- Res. WEIGHT -->
														<div class="col-md-8">
															<input name="resultado" type="num" class="form-control" placeholder="Peso">
														</div>
														<div class="col-md-2">
															<select name="unidadRes" class="form-control">
																<option value="kg" selected>Kilogramos</option>
																<option value="lb">Libras</option>
															</select>
														</div>
													<?php else: ?>
														<!-- Res. GENERIC -->
														<div class="col-md-10">
															<input name="resultado" type="num" class="form-control" placeholder="Resultado">
														</div>
													<?php endif; ?>
												<?php endif; ?>
											<?php endif; ?>

											<div class="col-md-2" style="height: 54px;display: inline-flex;">
												<label class="control-label checkbox-inline" style="margin: auto auto;">
													
												</label>
											</div>

											<div class="row"></div>

											<?php if ( $data['wod']->tipo == 3 ): ?>
												<!-- Penal. FT -->
												<?php if ( $data['wod']->permPenalizacion ): ?>
													<div class="col-md-2">
														<input name="reps" type="num" class="form-control" placeholder="Reps completadas"/>
													</div>
												<?php endif; ?>
											<?php endif; ?>

											<?php if ( $data['wod']->tipo == 9 ): ?>
												<!-- Penal. FT -->
												<?php if ( $data['wod']->permTieBreak ): ?>
													<div class="col-md-8">
														<input name="bodyw" type="num" class="form-control" placeholder="Peso del Atleta"/>
													</div>

													<div class="col-md-2">
														<select name="unidadAtl" class="form-control">
															<option value="kg" selected>Kilogramos</option>
															<option value="lb">Libras</option>
														</select>
													</div>
												<?php endif; ?>
											<?php endif; ?>

											<?php if ( ($data['wod']->tipo == 3) || ($data['wod']->tipo == 4) ): ?>
												<!-- TB. FT & AMRAP-->
												<?php if ( $data['wod']->permTieBreak ): ?>
													<div class="row"></div>
													<div class="col-md-10">
														<label>Tie-break (Obligatorio)</label>
													</div>
													<div class="row"></div>
													<div class="col-md-2">
														<input name="horasTB" type="num" class="form-control tiempo" placeholder="Horas"/>
													</div>
													<div class="col-md-4">
														<input name="minutosTB" type="num" class="form-control tiempo" placeholder="Minutos"/>
													</div>
													<div class="col-md-4">
														<input name="segundosTB" type="num" class="form-control tiempo" placeholder="Segundos"/>
													</div>
												<?php endif; ?>
											<?php endif; ?>

											<div class="row"></div>

											<!-- COMMON -->
											<div class="col-md-6">
												<label>Link del Video (Obligatorio, copiar y pegar)</label>
												<input name="video" type="text" class="form-control" placeholder="Link del Video">
											</div>

											<div class="col-md-6">
												<label>Juez (Dejar en blanco si no tuvo Juez)</label>
												<input name="juez" type="num" class="form-control" placeholder="Nombre del Juez">
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