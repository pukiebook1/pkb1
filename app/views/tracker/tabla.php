<?php
use Helpers\Date;
use Helpers\Hooks;
 
$hooks = Hooks::get();

$evento = $data["evento"];
$categoria = $data['categoria'];
?>

<style type="text/css">
	.container
	{
		max-width: none !important;
		min-width: 1000px;
		width: 100%;
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

	span.desc
	{
		display: none;
		position: absolute;
		top: 40px;
		left: 20px;
		padding: 12px 12px;
		width: 200px;
		text-align: left;
		font-size: 12px;
		color: #FFD200;
		background-color: rgb(35, 30, 26);
		border-radius: 3px;
		border: 1px solid #808080;
		line-height: 1em;
		z-index: 99;

		opacity: 0;

		-webkit-transition: opacity .35s;
	    -moz-transition: opacity .35s;
    	transition: opacity .35s;
	}

	th, td
	{
		position: relative;
	}

	th:hover span.desc,
	td:hover span.desc
	{
		display: block;
		opacity: 1;
	}

	td:hover span.desc
	{
		top: 65px;
	}

	th:hover span.desc br,
	td:hover span.desc br
	{
		display: block;
		margin-bottom: 13px;
		content: " ";
	}

	.heading-patro
	{
		color: #F0FFFF;
		font-weight: bold;
		font-size: 12px;
		background-color: #231E1A;
		padding: 10px;
		border: 1px solid #808080;
	}

	.mayorRes
	{
	    font-weight: bold;
	    font-size: 16px;
	    color: #FED101;
	}

	.less {
	    font-size: 9px;
	}
	.less .mayorRes{
	    font-size: 11px;
	}

</style>

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


<section class="bg-primary container tabla" id="resultados">
	<div class="container">
		<div class="row">
			<div class="col-xs-12 text-center">

				<div class="resultadosTabla sinPatrocinadores" style="display: flex; align-items: center;height: 180px;margin:auto;">

					<!-- Logo PKB -->
					<div class="col-xs-2">
						<div class="imgContainerRanking1">
							<span class="helper"></span>
							<img alt="Pukiebook" style="max-width: 100%;" src="<?php echo DIR;?>app/templates/<?php echo TEMPLATE;?>/img/logoTransparente.png"/>    
							
						</div>
					</div>

					<!-- Info evento/categoria -->
					<div class="col-xs-8">
						<div class="head-tabla">
							<h2 class="section-heading" style="color: #FED101;font-weight: bold;font-size: 22px;"><?php echo $data['evento']->nombre; ?></h2>
							<h2 class="section-heading" style="color: #FED101;font-size: 12px;"><i class="fa fa-tag" aria-hidden="true"></i> <?php echo $data['evento']->disciplinaStr; ?></h2>
							<h2 class="section-heading" style="color: #FED101;font-weight: bold;font-size: 18px;"><?php echo $data['categoria']->nombre; ?></h2>

							<div class="shareLinks">
								<div style="height:20px;display:inline-block;vertical-align: middle;" class="fb-share-button" data-href="<?php echo DIR."evento/".$evento->internalURL."/".$categoria->id; ?>" data-layout="button_count"></div>
								<div style="height:20px;display:inline-block;vertical-align: middle;"><a href="https://twitter.com/share" class="twitter-share-button" data-url="<?php echo DIR."evento/".$evento->internalURL."/".$categoria->id; ?>" data-count="none">Tweet</a></div>
							</div>
							<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
							
							<p class="text-faded miresText">
								<?php if ( $data['estoy'] ): ?>
									<a class="btn btn-primary btn-ms" href="#mires"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i> Mi resultado</a>
								<?php endif; ?>
								<?php if ( !$data['evento']->wodsvisible && ($data['esAdmin'] || $data['esOrganizador']) ): ?>
									<?php if (!$evento->registroTeam && $data['categoria']->team): ?>
										<a class="btn btn-primary" href="team<?php echo $data['categoria']->id; ?>/1">Ver WODs</a>
									<?php else: ?>
										<a class="btn btn-primary" href="<?php echo $data['categoria']->id; ?>/1">Ver WODs</a>
									<?php endif; ?>
								<?php endif; ?>
							</p>
						</div>
					</div>

					<!-- Logo evento -->
					<div class="col-xs-2">
						<div class="imgContainerRanking1">
							<span class="helper"></span><img alt="Logo del Evento" style="max-width: 100%;" src="<?php echo $data['evento']->fotoPath; ?>"/>
						</div>
					</div>
				</div>

				<div class="containerTabla" style="margin-top: 20px;">
					<div class="text-center resultadosTabla <?php if (!isset($data['patrocinadores']) || count($data['patrocinadores']) < 1) echo "sinPatrocinadores"; ?>">	
						<div class="buscador-tabla">
							<form method="GET" action="" class="navbar-form navbar-right" role="search">
								<div class="input-group">
									<input type="text" name="buscar" class="form-control" placeholder="Buscar por nombre" value="<?php echo $data['busqueda']; ?>">
									<span class="input-group-btn">
										<button type="submit" class="btn btn-default">
										<span class="glyphicon glyphicon-search"></span>
										</button>
									</span>
								</div>
							</form>
						</div>

						<table class="score-table" style="width:100%;" align="center">
							<thead>
								<tr>
									<th>Athlete</th>
									<th>Place</th>
									<?php if ($data['evento']->disciplina == 9): ?>
									<th>Bodyweight</th>
									<?php endif ?>

									<?php $i=0; foreach($data['dataRanking']->wods as $key => $value): $i++; ?>
										<th class="wodHeader">
											<?php if( (($data['evento']->disciplina == 10) && ($value->apertura <= time())) || $data['evento']->wodsvisible ): ?>

										<span class="wod">
											<?php
												if(strlen($value->nombre) >= 13)
												{
													echo substr($value->nombre, 0, 9)."...";
												}
												else
													echo $value->nombre;
											?>
										</span>
										<br/>
										<span class="desc">
											<h4><?= $value->nombre; ?></h4>
											<br/>
											<br/>
											<?php if(!empty($value->descripcion)) echo $value->descripcion; ?>
											
											<?php if( ($value->tipo == 3) && ($value->timeCap > 0) ): ?>
												<br/>
												<span>Time Cap: <?php echo Date::timeToFullString($value->timeCap*1000); ?></span>
											<?php endif; ?>

											<?php if(($value->tipo == 4) && ($value->time > 0)): ?>
												<br/>
												<span>Tiempo de WOD: <?php echo Date::timeToFullString($value->time*1000); ?></span>
											<?php endif; ?>
										</span>
											<?php else: ?>
											<span class="wod">WOD <?php echo $i; ?></span>
											<?php endif; ?>
										</th>
									<?php endforeach; ?>

									<?php if ($data['evento']->disciplina == 9): ?>
									<th>Total</th>
									<th>Average</th>
									<?php else: ?>
									<?php if(!empty($data['dataRanking']->wods)): ?>
									<th>Total<br/>Points</th>
									<?php endif ?>
									<?php endif ?>
								</tr>
							</thead>

							<?php if (!empty($data['dataRanking']->tablaResultados)): ?>
							<tbody <?php if ($data['evento']->disciplina == 9) echo "style=\"line-height: 1.0;\"" ; ?> >
								<?php foreach ($data['dataRanking']->tablaResultados as $keyAtl => $valueAtl): ?>
									<?php if ($data['miId'] != false && $data['miId'] == $data['dataRanking']->atletas[$valueAtl->registroAtletaId]->personaId ): ?>
									<tr class="actual P" id="mires">
									<?php else: ?>
									<tr class="P<?php echo $valueAtl->pos; ?>">
									<?php endif ?>								

									<th> 
										<div class="atletaContainer">
											<div class="imgContainer2">
												<img class="img-circle" width="35px" height="35px" src="<?php echo $data['dataRanking']->atletas[$valueAtl->registroAtletaId]->fotoPath; ?>"/>
												<span class="helper"></span>
											</div>
											<div class="atleta">
												<div class="atlLink">
												<?php if ((!$evento->registroTeam && $data['categoria']->team) || ($evento->registroTeam)): ?>
														<a class="btn" style="padding: 0;padding-left: 5px;" href="<?php echo DIR; ?>equipo/<?php echo $data['dataRanking']->atletas[$valueAtl->registroAtletaId]->id;?>"><?php echo $data['dataRanking']->atletas[$valueAtl->registroAtletaId]->nombre; ?></a>
													<?php else: ?>
														<a class="btn" style="padding: 0;padding-left: 5px;" href="<?php echo DIR; ?>perfil/<?php echo $data['dataRanking']->atletas[$valueAtl->registroAtletaId]->personaId;?>"><?php echo $data['dataRanking']->atletas[$valueAtl->registroAtletaId]->nombre." ".$data['dataRanking']->atletas[$valueAtl->registroAtletaId]->apellido; ?></a>
													<?php endif; ?>
												</div>
												<?php if ($data['dataRanking']->atletas[$valueAtl->registroAtletaId]->boxStr): ?>
												<div class="atlBox" style="padding: 0;margin: 0;line-height: 10px;"><span style="font-size: 10px;padding-left: 6px;"><a href="<?= DIR."box/".$data['dataRanking']->atletas[$valueAtl->registroAtletaId]->boxId;?>" target="_blank"><?php echo $data['dataRanking']->atletas[$valueAtl->registroAtletaId]->boxStr; ?></a></span></div>
												<?php endif ?>
											</div>
										</div>
									</th>
									<td><?php echo "<span class=\"catPos P".$valueAtl->pos."\">".$valueAtl->pos."</span>"; ?></td>


									<?php if ($data['evento']->disciplina == 9): ?>
									<td><?php echo sprintf("%'0.2f", $valueAtl->bodyweight)." Kg. <br/><br/><span class=\"less\">". sprintf("%'0.2f", $valueAtl->bodyweight*2.20462)." Lb.</span>"; ?></td>
									<?php endif; ?>

									<?php foreach($valueAtl->resultados as $keyRes => $valueRes): ?>
										<td>
											<?php $value = $data['dataRanking']->wods[$keyRes]; ?>
											<?php if( (($data['evento']->disciplina == 10) && ($value->apertura <= time())) || $data['evento']->wodsvisible ): ?>
												<?php

													if($data['evento']->disciplina == 9)
													{
														for($i=1; $i<=3; $i++)
														{
															$resu = $valueRes[$i];

															if($resu->valid)
															{
																if($resu->mayor)
																	echo "<span class=\"mayorRes\">";

																echo sprintf("%'0.2f", $resu->resultado);

																if($resu->mayor)
																	echo "</span>";
															}
															else
															{
																echo "-";
															}

															if($i < 3)
																echo " / ";
														}

														echo "<br/>Kg.<br/><br/><span class=\"less\">";

														for($i=1; $i<=3; $i++)
														{
															$resu = $valueRes[$i];

															if($resu->valid)
															{
																if($resu->mayor)
																	echo "<span class=\"mayorRes\">";

																echo sprintf("%'0.2f", $resu->resultado*2.20462);

																if($resu->scaled)
																	echo " (s)";

																if($resu->mayor)
																	echo "</span>";
																
															}
															else
															{
																echo "-";
															}

															if($i < 3)
																echo " / ";
														}

														echo "<br/>Lb.</span>";
													}
													else
													{
														if($valueRes->wd)
															echo "WD";
														else
														{
															echo "<span class=\"wodPos\">".$valueRes->pos."</span><br/>";

															if($data['evento']->disciplina == 11 || $data['evento']->disciplina == 12)
																echo "<span class=\"wodPtos\">Ptos: ".$valueRes->ptos."</span><br/>";

															if( ($data['esAdmin'] || $data['esOrganizador'] || $data['esJuez'] || $data['evento']->showVideo) && $valueRes->videoLink)
																echo "<a target=\"_blank\" href=\"".$valueRes->videoLink."\"><i class=\"fa fa-video-camera\"></i> </a>";

															if($valueRes->valid)
															{

																//print_r($data['dataRanking']->wods);
																if( ($data['dataRanking']->wods[$valueRes->orden-1]->tipo == 1) || ($data['dataRanking']->wods[$valueRes->orden-1]->tipo == 3))
																{
																	echo "<span class=\"wodRes\">".Date::timeToFullString($valueRes->resultado)."</span>";

																	if($valueRes->scaled)
																		echo " (s)";
																}
																else
																{
																	if($data['dataRanking']->wods[$valueRes->orden-1]->tipo == 9)
																	{
																		echo "<span class=\"wodRes\">".sprintf("%'0.2f", $valueRes->resultado)." Kg<br/>".sprintf("%'0.2f", $valueRes->resultado * 2.20462)." Lb</span>";

																		if($valueRes->scaled)
																			echo " (s)";
																	}
																	else
																	{
																		echo "<span class=\"wodRes\">".$valueRes->resultado."</span>";

																		if($valueRes->scaled)
																			echo " (s)";
																	}
																}
															}
															else
															{
																if( !$data['categoria']->team )
																	echo "<i class=\"fa fa-minus\" aria-hidden=\"true\"></i>";
															}
														}
													}
												?>

												<?php if( ($valueRes->valid) ): ?>
												<br/>

												<?php
												$thisWOD = $data['dataRanking']->wods[$keyRes];
												$str = "";

													if( $valueRes->tipoWOD == 3 )
													{
														if($thisWOD->permPenalizacion && isset($valueRes->pen ) && ($valueRes->pen > 0) )
															$str .= Date::timeToFullString($valueRes->resultado - $valueRes->pen)." + ".Date::timeToFullString($valueRes->pen)." (".($thisWOD->repsRound - $valueRes->reps)." Reps.)<br/>";

														if($thisWOD->permPenalizacion)
															$str .= $valueRes->reps." Reps.<br/>";

														if($thisWOD->permTieBreak && ($valueRes->tiebreak > 0) )
															$str .= "Tie Break (".Date::timeToFullString($valueRes->tiebreak).")<br/>";
													}

													if($valueRes->tipoWOD == 4)
													{
														if($thisWOD->repsRound > 0)
														{
															$rounds = $valueRes->resultado / $data['dataRanking']->wods[$keyRes]->repsRound;
															$reps = $data['dataRanking']->wods[$keyRes]->repsRound * floor($rounds);

															$str .= floor($rounds)." Full Rounds + ".($valueRes->resultado - $reps)." Reps.<br/>";
														}
														
														if($thisWOD->permTieBreak && ($valueRes->tiebreak > 0) )
															$str .= "Tie Break (".Date::timeToFullString($valueRes->tiebreak).")<br/>";
													}

													if($valueRes->tipoWOD == 9)
													{
														if($thisWOD->permTieBreak && ($valueRes->tiebreak > 0) )
														{
															$str .= "<span class=\"wodRes\"> Bodyweight: <br/>  ".sprintf("%'0.2f", $valueRes->tiebreak)." Kg. <br/>  ".sprintf("%'0.2f", $valueRes->tiebreak * 2.20462)." Lb</span><br/>";
														}
													}

													if(!empty($valueRes->judgedBy))
													{
														$str .= "Juez: ".$valueRes->judgedBy;
													}

													if(!empty($str))
													{
														echo "<span class=\"desc\">";
														echo $str;
														echo "</span>";
													}
												?>
												<?php else: ?>
												<?php
												$str = "";

													if($data['categoria']->team)
													{
														$indi = $valueRes->individuales;

														foreach ($indi as $key => $value)
														{
															$atleta = $data['dataRanking']->atletasIndividuales[$key];
															$str .= $atleta->nombre." ".$atleta->apellido." (".$value->pos.")<br/>";
														}
													}

													if(!empty($str))
													{
														echo "<span class=\"desc\">";
														echo $str;
														echo "</span>";
													}
												?>
												<?php endif; ?>
											<?php else: ?>
												<?php echo "<span class=\"wodPos\">".$valueRes->pos."</span><br/>"; ?>
											<?php endif; ?>
									</td>
									<?php endforeach; ?>

									<?php if ($data['evento']->disciplina == 9): ?>
									<td>
										<?php echo sprintf("%'0.2f", $valueAtl->total); ?> Kg.<br/><br/>
										<span class="less"><?php echo sprintf("%'0.2f", $valueAtl->total * 2.20462); ?> Lb.</span><br/>
									</td>
									<td><?php echo sprintf("%'0.5f", $valueAtl->average); ?></td>
									<?php else: ?>
										
									<?php if(!empty($data['dataRanking']->wods)): ?>
									<td style="line-height:1em;"><b><?php echo $valueAtl->puntaje; ?></b><br/><span style="font-size:10px">Points</span></td>
									<?php endif ?>
									<?php endif ?>

								</tr>
								<?php endforeach; ?>
							</tbody>
							<?php endif; ?>
						</table>
						<div class="paginacion"><?php echo $data['pageLinks']; ?></div>


					<?php if (empty($data['dataRanking']->tablaResultados)): ?>
						<div class="alert alert-warning">
						<?php if (isset($_GET['buscar']) && !empty($_GET['buscar'])): ?>
							No se encontraron resultados para su b&uacute;squeda.
						<?php else: ?>
							No hay participantes en esta categor&iacute;a.
						<?php endif ?>
						</div>
					<?php endif; ?>

					</div>
					<?php if (isset($data['patrocinadores']) && count($data['patrocinadores']) > 0): ?>
						<div class="text-center patrocinadores">
							<h2 class="section-heading heading-patro" style="">Patrocinadores</h2></p>
							<?php foreach ($data['patrocinadores'] as $key => $value): ?>
								<?php if ($value->archivoFoto): ?>
									<a href="<?php echo $value->url; ?>" alt="<?php echo $value->nombre; ?>"><img src="/imagenes/patrocinadores/<?php echo $value->archivoFoto; ?>" width="80%" /></a>	
								<?php endif; ?>
							<?php endforeach; ?>
						</div>
					<?php endif ?>

					</div>
				</div>

			</div>
		</div>
	</div>
</section>