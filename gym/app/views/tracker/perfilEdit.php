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
				<?php $hooks->run('menuCuenta', $data); ?>
			</ul>
		</div>
		<!-- /.navbar-collapse -->
	</div>
	<!-- /.container-fluid -->
</nav>

<style type="text/css">
	body
	{
		min-width: 485px;
	}

	.container
	{
		max-width: none !important;
		width: 482px;
		/*min-height: 500px;*/
		min-height: 100%;
		padding-right: 0px;
		padding-left: 0px;
	}

	.row
	{
		 margin-right: 0px; 
		 margin-left: 0px; 
	}
	section.perfil
	{
		background-size: cover;
	}



	.radio input[type="radio"], .radio-inline input[type="radio"], .checkbox input[type="checkbox"], .checkbox-inline input[type="checkbox"]
	{
		/*
		position: absolute;
		margin-top: 4px \9;
		margin-left: -13px;
		*/
	}

	div.folder div.folderBodyText table.bioTable td, div.folder table.infoTable td, div.folder .profileText
	{
    	font-weight: normal;
    }
</style>

<section class="bg-primary perfil" >
<div class="header-img"></div>
	<div class="container">
		<div class="row">
		<form class="form-group" role="form" enctype="multipart/form-data" action="" method="POST">
			<input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>" />
			<div class="folderContainer" style="">
				<div class="folder" style="">
					<div class="folderImage">						
						<img class="" src="<?php echo $data['persona']->fotoPath; ?>"/>
					</div>

					<div class="folderSide">
						<span>Perfil del Atleta #<?php echo $data['persona']->idPersona; ?></span>
					</div>

					<div class="folderClip">
					</div>

					<div class="folderImageTools">
						<div class="form-group btn-img-1">
							<label id="fileupload-perfil-label" for="fileupload-perfil" class="control-label"><i class="fa fa-camera" aria-hidden="true"></i>
								<input id="fileupload-perfil" name="archivoPerfil" type="file" value=""/>
							</label>
						</div>
						<div class="form-group btn-img-2">
							<label class="control-label checkbox">
								<input name="removerImagen" type="checkbox"/><i class="fa fa-trash" aria-hidden="true"></i>
							</label>
						</div>
					</div>

					<div class="folderTopText">
						<table class="infoTable">
							<tbody>
								<tr><th>Nombre</th><td><div class="col-xs-12 <?php if (isset($error['campos']['nombre'])) echo 'has-error'; ?>"><input class="form-control" name="nombre" type="text" value="<?php echo $data['persona']->nombre; ?>" required/></div></td></tr>
								<tr><th>Apellido</th><td><div class="col-xs-12 <?php if (isset($error['campos']['apellido'])) echo 'has-error'; ?>"><input class="form-control" name="apellido" type="text" value="<?php echo $data['persona']->apellido; ?>" required/></div></td></tr>
								<tr><th>Alias</th><td><div class="col-xs-12"><input name="alias" type="text" value="<?php echo $data['persona']->alias; ?>"/></div></td></tr>
								<tr><th>Ced/Pasaporte</th><td><div class="col-xs-12 <?php if (isset($error['campos']['ident'])) echo 'has-error'; ?>"><input class="form-control" name="ident" type="text" value="<?php echo $data['persona']->ident; ?>" required/></div></td></tr>
								<tr><th>Correo</th><td><div class="col-xs-12 <?php if (isset($error['campos']['correo'])) echo 'has-error'; ?>"><input class="form-control" name="correo" type="text" value="<?php echo $data['persona']->correo; ?>" required/></div></td></tr>
							</tbody>
						</table>
					</div>

					<div class="folderBodyText">
						<table class="bioTable">
							<tbody>
								<tr>
									<th>Pais</th>
									<td>
										<div class="col-xs-12">
											<select name="pais">
												<option value>Seleccionar...</option>
												<?php foreach ($data['paises'] as $key => $value): ?>
													<?php if ($value->id == $data['persona']->pais): ?>
														<option value="<?php echo $value->id;?>" selected><?php echo $value->country_name;?></option>
													<?php else: ?>
														<option value="<?php echo $value->id;?>"><?php echo $value->country_name;?></option>
													<?php endif ?>
												
												<?php endforeach ?>
											</select>
										</div>
									</td>
								</tr>
								<tr><th>Estado/Provincia</th><td><div class="col-xs-12"><input name="estado" type="text" value="<?php echo $data['persona']->estado; ?>"/></div></td></tr>
								<tr>
									<th>Nacimiento</th>
									<td>
										<div class="input-group col-xs-12">
											<div class="col-xs-4">
												<select name="dd">
													<option value="0">D&iacute;a</option>
													<?php 
														$dd = 1;
														while ($dd <= 31)
														{
															if($data['persona']->fechaNacimiento && ($dd == date('d', strtotime($data['persona']->fechaNacimiento))))
																echo "<option value=".$dd." selected>".$dd."</option>";
															else
																echo "<option value=".$dd.">".$dd."</option>";
															$dd++;
														}
													?>
												</select>
											</div>
											<div class="col-xs-4">
												<select name="mm">
													<option value="0">Mes</option>
													<?php 
														$mm = 1;
														while ($mm <= 12)
														{
															if($data['persona']->fechaNacimiento && ($mm == date('m', strtotime($data['persona']->fechaNacimiento))))
																echo "<option value=".$mm." selected>".$mm."</option>";
															else
																echo "<option value=".$mm.">".$mm."</option>";
															$mm++;
														}
													?>
												</select>
											</div>
											<div class="col-xs-4">
												<select name="yyyy">
													<option value="0">A&ntilde;o</option>
													<?php 
														$yy = 1900;
														while ($yy <= date('Y'))
														{
															if($data['persona']->fechaNacimiento && ($yy == date('Y', strtotime($data['persona']->fechaNacimiento))))
																echo "<option value=".$yy." selected>".$yy."</option>";
															else
																echo "<option value=".$yy.">".$yy."</option>";
															$yy++;
														}
													?>
												</select>
											</div>
										</div>
									</td>
								</tr>
								<tr>
									<th>Sexo</th>
									<td>
										<div class="col-xs-12">
											<select name="sexo">
												<option value="X" <?php if(strcmp($data['persona']->sexo, 'X') == 0) echo "selected";?>>Ninguno</option>
												<option value="F" <?php if(strcmp($data['persona']->sexo, 'F') == 0) echo "selected";?>>Femenino</option>
												<option value="M" <?php if(strcmp($data['persona']->sexo, 'M') == 0) echo "selected";?>>Masculino</option>
											</select>
										</div>
											
									</td>
								</tr>
								<tr>
									<th>Box</th>
									<td>
										<div class="col-xs-12">
											<select name="box">
												<option value="0">Ninguno</option>
												<?php foreach ($data['boxes'] as $key => $value): ?>
													<?php if ($value->id == $data['persona']->boxId): ?>
														<option value="<?php echo $value->id;?>" selected><?php echo $value->nombre;?></option>
													<?php else: ?>
														<option value="<?php echo $value->id;?>"><?php echo $value->nombre;?></option>
													<?php endif ?>
												
												<?php endforeach ?>
											</select>
										</div>
									</td>
								</tr>
								<tr>
									<th>Partner in Crime</th>
									<td><div class="col-xs-12"><input name="pic" type="text" value="<?php echo $data['persona']->pic; ?>"/></div></td>
								</tr>
							</tbody>
						</table>

						<div class="workouts">
							<span class="profileText">Records Personales:</span>

							<table class="table table-striped work work-left">
								<tbody>
									<tr>
										<?php 
											$ej = $data['persona']->wkcleanObj;
											$tipo = $ej->tipo;
											if($tipo == 1)
												$valor = $ej->lb;
											else
												$valor = $ej->kg;
										?>
										<td class="wk col-xs-5">Clean &amp; Jerk</td>
										<td class="wkRes col-xs-7">
											<div class="input-group">
												<div class="col-xs-6 nopadding">
													<input name="cleanV" type="text" value="<?php echo $valor; ?>"/>
												</div>
												<div class="col-xs-6 nopadding">
													<select name="cleanT">
														<option value="0">Kg</option>
														<option value="1" <?php if($tipo == 1) echo "selected"; ?>>Lb</option>
													</select>
												</div>
											</div>
										</td>
									</tr>
									<tr>
										<?php 
											$ej = $data['persona']->wksnatchObj;
											$tipo = $ej->tipo;
											if($tipo == 1)
												$valor = $ej->lb;
											else
												$valor = $ej->kg;
										?>
										<td class="wk col-xs-5">Snatch</td>
										<td class="wkRes col-xs-7">
											<div class="input-group">
												<div class="col-xs-6 nopadding">
													<input name="snatchV" type="text" value="<?php echo $valor; ?>"/>
												</div>
												<div class="col-xs-6 nopadding">
													<select name="snatchT">
														<option value="0">Kg</option>
														<option value="1" <?php if($tipo == 1) echo "selected"; ?>>Lb</option>
													</select>
												</div>
											</div>
										</td>
									</tr>
									<tr>
										<?php 
											$ej = $data['persona']->wkdeadObj;
											$tipo = $ej->tipo;
											if($tipo == 1)
												$valor = $ej->lb;
											else
												$valor = $ej->kg;
										?>
										<td class="wk col-xs-5">Deadlift</td>
										<td class="wkRes col-xs-7">
											<div class="input-group">
												<div class="col-xs-6 nopadding">
													<input name="deadV" type="text" value="<?php echo $valor; ?>"/>
												</div>
												<div class="col-xs-6 nopadding">
													<select name="deadT">
														<option value="0">Kg</option>
														<option value="1" <?php if($tipo == 1) echo "selected"; ?>>Lb</option>
													</select>
												</div>
											</div>
										</td>
									</tr>
									<tr>
										<?php 
											$ej = $data['persona']->wkbacksquatObj;
											$tipo = $ej->tipo;
											if($tipo == 1)
												$valor = $ej->lb;
											else
												$valor = $ej->kg;
										?>
										<td class="wk col-xs-5">Back Squat</td>
										<td class="wkRes col-xs-7">
											<div class="input-group">
												<div class="col-xs-6 nopadding">
													<input name="backV" type="text" value="<?php echo $valor; ?>"/>
												</div>
												<div class="col-xs-6 nopadding">
													<select name="backT">
														<option value="0">Kg</option>
														<option value="1" <?php if($tipo == 1) echo "selected"; ?>>Lb</option>
													</select>
												</div>
											</div>
										</td>
									</tr>
								</tbody>
							</table>

							<table class="table table-striped work work-right">
								<tbody>
									<tr>
										<?php 
											$ej = $data['persona']->wkfranObj;
										?>
										<td class="wk col-xs-5">Fran</td>
										<td class="wkRes col-xs-7">
											<div class="input-group">
												<div class="col-xs-4 nopadding">
													<input name="franH" type="text" value="<?php echo $ej->H; ?>"/>
												</div>
												<div class="col-xs-1 timeSeparator nopadding">:</div>
												<div class="col-xs-3 nopadding">
													<input name="franM" type="text" value="<?php echo $ej->M; ?>"/>
												</div>
												<div class="col-xs-1 timeSeparator nopadding">:</div>
												<div class="col-xs-3 nopadding">
													<input name="franS" type="text" value="<?php echo $ej->S; ?>"/>
												</div>
											</div>
										</td>
									</tr>
									<tr>
										<?php 
											$ej = $data['persona']->wkisabelObj;
										?>
										<td class="wk col-xs-5">Isabel</td>
										<td class="wkRes col-xs-7">
											<div class="input-group">
												<div class="col-xs-4 nopadding">
													<input name="isabelH" type="text" value="<?php echo $ej->H; ?>"/>
												</div>
												<div class="col-xs-1 timeSeparator nopadding">:</div>
												<div class="col-xs-3 nopadding">
													<input name="isabelM" type="text" value="<?php echo $ej->M; ?>"/>
												</div>
												<div class="col-xs-1 timeSeparator nopadding">:</div>
												<div class="col-xs-3 nopadding">
													<input name="isabelS" type="text" value="<?php echo $ej->S; ?>"/>
												</div>
											</div>
										</td>
									</tr>
									<tr>
										<?php 
											$ej = $data['persona']->wkgraceObj;
										?>
										<td class="wk col-xs-5">Grace</td>
										<td class="wkRes col-xs-7">
											<div class="input-group">
												<div class="col-xs-4 nopadding">
													<input name="graceH" type="text" value="<?php echo $ej->H; ?>"/>
												</div>
												<div class="col-xs-1 timeSeparator nopadding">:</div>
												<div class="col-xs-3 nopadding">
													<input name="graceM" type="text" value="<?php echo $ej->M; ?>"/>
												</div>
												<div class="col-xs-1 timeSeparator nopadding">:</div>
												<div class="col-xs-3 nopadding">
													<input name="graceS" type="text" value="<?php echo $ej->S; ?>"/>
												</div>
											</div>
										</td>
									</tr>
									<tr>
										<?php 
											$ej = $data['persona']->wkrun5kObj;
										?>
										<td class="wk col-xs-5">Run 5K</td>
										<td class="wkRes col-xs-7">
											<div class="input-group">
												<div class="col-xs-4 nopadding">
													<input name="run5kH" type="text" value="<?php echo $ej->H; ?>"/>
												</div>
												<div class="col-xs-1 timeSeparator nopadding">:</div>
												<div class="col-xs-3 nopadding">
													<input name="run5kM" type="text" value="<?php echo $ej->M; ?>"/>
												</div>
												<div class="col-xs-1 timeSeparator nopadding">:</div>
												<div class="col-xs-3 nopadding">
													<input name="run5kS" type="text" value="<?php echo $ej->S; ?>"/>
												</div>
											</div>
										</td>
									</tr>
									<tr>
										<?php 
											$ej = $data['persona']->wkcindyObj;
										?>
										<td class="wk col-xs-5">Cindy</td>
										<td class="wkRes col-xs-7">
											<div class="input-group">
												<div class="col-xs-4 nopadding">
													<input name="cindyH" type="text" value="<?php echo $ej->H; ?>"/>
												</div>
												<div class="col-xs-1 timeSeparator nopadding">:</div>
												<div class="col-xs-3 nopadding">
													<input name="cindyM" type="text" value="<?php echo $ej->M; ?>"/>
												</div>
												<div class="col-xs-1 timeSeparator nopadding">:</div>
												<div class="col-xs-3 nopadding">
													<input name="cindyS" type="text" value="<?php echo $ej->S; ?>"/>
												</div>
											</div>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>

					<div class="folderButtons">
						<div class="form-group">
							<a class="btn btn-margin btn-link" href="<?php echo DIR; ?>cuenta"><i class="fa fa-chevron-circle-left"></i> Volver</a>
							<button name="submit" type="submit" class="btn btn-margin btn-link"><i class="fa fa-check"></i> Actualizar</button>
						</div>
					</div>
				</div>
			</div>
		</form>
		</div>
	</div>
</section>