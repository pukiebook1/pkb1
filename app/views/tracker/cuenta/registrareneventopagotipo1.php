<?php
use Core\Language;
use Helpers\Hooks;

$hooks = Hooks::get();

?>

<style type="text/css">
	.btn-file {
		position: relative;
		overflow: hidden;
	}
	.btn-file input[type=file] {
		position: absolute;
		top: 0;
		right: 0;
		min-width: 100%;
		min-height: 100%;
		font-size: 100px;
		text-align: right;
		filter: alpha(opacity=0);
		opacity: 0;
		outline: none;
		background: white;
		cursor: inherit;
		display: block;
	}

	#img-upload{
		width: 100%;
	}
</style>

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
				<li>
					<a class="page-scroll"  target="_blank" href="<?php echo DIR; ?>evento/<?php echo $data['evento']->internalURL; ?>">Ver evento</a>
				</li>

				<?php $hooks->run('menuCuenta', $data); ?>
			</ul>
		</div>
		<!-- /.navbar-collapse -->
	</div>
	<!-- /.container-fluid -->
</nav>

<header class="header-eventos">
	<div class="header-content">
		<div class="header-content-inner">
			<h1>Dep&oacute;sito/Transferencia</h1>
			<hr class="light">
			<p>Introduzca los datos de su transacci&oacute;n para terminar el proceso de registro</p>
			Si el evento es de Weightlifting debe ingresar en observaciones, los pesos iniciales de Snatch y Clean &amp; Jerk.

			<form role="form" action="" method="POST" enctype="multipart/form-data">
				<input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>" />
				<div class="col-sm-6 col-sm-push-3">
					<?php if (!empty($error['mensajes'])): ?>
						<div class="alert alert-danger">
							<?php foreach ($error['mensajes'] as $value): ?>
								<?php echo $value; ?><br/>
							<?php endforeach ?>
						</div>
					<?php endif ?>
					<div class="form-group col-sm-6 <?php if (isset($error['campos']['banco'])) echo 'has-error'; ?>">
						<label class="control-label">Banco emisor</label>
						<input type="text" name="banco" class="form-control" id="" value="<?php echo $error['data']['banco']; ?>" >
					</div>
					<div class="form-group col-sm-6 <?php if (isset($error['campos']['numreferencia'])) echo 'has-error'; ?>">
						<label class="control-label"># de transferencia o dep&oacute;sito</label>
						<input type="text" name="numreferencia" class="form-control" id="" value="<?php echo $error['data']['numreferencia']; ?>" > 			
					</div>				
					<div class="form-group col-xs-12 <?php if (isset($error['campos']['fecha'])) echo 'has-error'; ?>">
						<label class="control-label">Fecha de Pago</label>
						<div class="input-group col-xs-12">
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

					<div class="form-group col-sm-6 <?php if (isset($error['campos']['edad'])) echo 'has-error'; ?>">
						<label class="control-label">Edad del atleta:</label>
						<input type="text" name="edad" class="form-control" id="" value="<?php echo $error['data']['edad']; ?>" >
					</div>
					<div class="form-group col-sm-6 <?php if (isset($error['campos']['centro_entrenamiento'])) echo 'has-error'; ?>">
						<label class="control-label">Centro de entrenamiento:</label>
						<input type="text" name="centro_entrenamiento" class="form-control" id="" value="<?php echo $error['data']['centro_entrenamiento']; ?>" >
					</div>

					<div class="form-group col-sm-12 <?php if (isset($error['campos']['monto'])) echo 'has-error'; ?>">
						<label class="control-label">Monto</label>
						<input type="text" name="monto" class="form-control" id="" value="<?php echo $error['data']['monto']; ?>" >
					</div>
					<div class="form-group col-sm-6 <?php if (isset($error['campos']['address'])) echo 'has-error'; ?>">
						<label class="control-label">Dirección de facturación:</label>
						<input type="text" name="address" class="form-control" id="" value="<?php echo $error['data']['address']; ?>" >
					</div>
					<div class="form-group col-sm-6 <?php if (isset($error['campos']['telefono'])) echo 'has-error'; ?>">
						<label class="control-label">Número de teléfono para facturación:</label>
						<input type="text" name="telefono" class="form-control" id="" value="<?php echo $error['data']['telefono']; ?>" >
					</div>

					<div class="form-group col-sm-12 <?php if (isset($error['campos']['observacion'])) echo 'has-error'; ?>">
						<label class="control-label">Talla de camiseta:</label>
						<select class="form-control" name="observacion">
							<option value="S">S</option>
							<option value="M">M</option>
							<option value="L">L</option>
							<option value="XL">XL</option>
						</select>
						<!-- <input type="text" name="observacion" class="form-control" id="" value="<?php echo $error['data']['observacion']; ?>" > -->
					</div>

					<div class="form-group col-sm-12 <?php if (isset($error['campos']['archivoFoto'])) echo 'has-error'; ?>">
						<label class="control-label">Imagen del comprobante de pago (.png, .jpg):</label>
						<div class="input-group">
							<span class="input-group-btn">
								<span class="btn btn-default btn-file">
									Buscar… <input type="file" name='archivoFoto' id="archivoFoto">
								</span>
							</span>
							<input type="text" class="form-control" readonly>
						</div>
						<img id='img-upload'/>
					</div>

					<div class="form-group col-sm-12">
						<a class="btn btn-default btn-margin" href="./"><i class="fa fa-chevron-circle-left"></i> Volver</a>
						<button name="submit" type="submit" class="btn btn-primary btn-margin"><i class="fa fa-arrow-circle-right"></i> Registrar</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</header>

<script type="text/javascript">
	$(document).ready( function() {
    	$(document).on('change', '.btn-file :file', function() {
		var input = $(this),
			label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
		input.trigger('fileselect', [label]);
		});

		$('.btn-file :file').on('fileselect', function(event, label) {
		    
		    var input = $(this).parents('.input-group').find(':text'),
		        log = label;
		    
		    if( input.length ) {
		        input.val(log);
		    } else {
		        if( log ) alert(log);
		    }
	    
		});
		function readURL(input) {
		    if (input.files && input.files[0]) {
		    	if(!input.files[0].type.match(/.(jpg|jpeg|png)$/i)){
		        	alert('This is not an image file or the file format is not supported!');
		        	$("#archivoFoto").replaceWith($("#archivoFoto").val('').clone(true));
		        } else {
		        	var reader = new FileReader();
		        	reader.onload = function (e) {
		        		$('#img-upload').attr('src', e.target.result);
		        	}
		        	var filename = input.files[0].name;
		        	var size = input.files[0].size;

		        	reader.readAsDataURL(input.files[0]);
		        }
		    }
		}

		$("#archivoFoto").change(function(){
		    readURL(this);
		}); 	
	});
</script>
