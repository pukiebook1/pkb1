<?php
use Core\Language;
use Helpers\Hooks;

$hooks = Hooks::get();

?>

<script type="text/javascript" src="http://www.actualidadjquery.es/ejemplos/js/jquery-1.4.2.min.js"></script>
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
					<a class="page-scroll" target="_blank" href="<?php echo DIR; ?>evento/<?php echo $data['evento']->internalURL; ?>">Ver evento</a>
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
			<h1>Pago online</h1>
			<hr class="light">
			<p>Introduzca los datos personales para solicitar la inscripción.</p>
			<p><strong>IMPORTANTE:</strong> para que tu solicitud de registro sea aprobada debes generar un recibo en el botón epayco, no es obligatorio pagar inmediatamente, pero si no genera el recibo a través de los distintos métodos de pago, su solicitud será rechazada para que cree una nueva.</p>
			

			<script type="text/javascript"> 
				$(document).ready(function(){
					$("#frm_botonePayco").submit(function () {
						if($("#centro_entrenamiento").val().length < 4) {
							alert("Debe ingresar a qué centro de entranamiento pertenece");
							return false;
						}		
						if($("#telefono").val().length < 7 || isNaN($("#telefono").val())) {
							alert("El teléfono debe tener más de 7 caracteres y solo números");
							return false;		
						}
						if($("#edad").val().length < 1 || isNaN($("#telefono").val())) {
							alert("Debe ingresar la edad solo en números");
							return false;
						}

					});
				});
			</script>
			<form id="frm_botonePayco" name="frm_botonePayco" method="post" action="https://secure.payco.co/checkout.php"> 
				<input name="p_cust_id_cliente" type="hidden" value="13438">
				<input name="p_key" type="hidden" value="d0fd84649dcb1b766f47dd34b3e2565259256b3f">
				<input name="p_id_invoice" type="hidden" value="">
				<input name="p_description" type="hidden" value=" Inscripción clasificatorio Cross Games Colombia.">
				<input name="p_currency_code" type="hidden" value="COP">
				<input name="p_amount" id="p_amount" type="hidden" value="35000">
				<input name="p_tax" id="p_tax" type="hidden" value="0">
				<input name="p_amount_base" id="p_amount_base" type="hidden" value="0">
				<input name="p_test_request" type="hidden" value="FALSE">
				<input name="p_url_response" type="hidden" value="http://pukiebook.com/eventos"> 
				<input name="p_url_confirmation" type="hidden" value="http://pukiebook.com/respuesta"> 
				<input name="p_signature" type="hidden" id="signature"  value="fd19454e0d74f271e53ba931f4fdcba9" />
				<input name="p_billing_name" type="hidden" value="<?php echo $data['logeado']['persona']->nombre;?>">
				<input name="p_billing_lastname" type="hidden" value="<?php echo $data['logeado']['persona']->apellido;?>">
				<input name="p_billing_email" type="hidden" value="<?php echo $data['logeado']['persona']->correo;?>">
				<input name="p_billing_document" type="hidden" value="<?php echo $data['logeado']['persona']->ident;?>">
				<input name="p_extra1" type="hidden" value="<?php echo $data['logeado']['persona']->idPersona;?>">
				<input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>" />	
				
				<div class="col-sm-6 col-sm-push-3">
					<?php if (!empty($error['mensajes'])): ?>
						<div class="alert alert-danger">
							<?php foreach ($error['mensajes'] as $value): ?>
								<?php echo $value; ?><br/>
							<?php endforeach ?>
						</div>
					<?php endif ?>	
					<div class="form-group col-sm-12 <?php if (isset($error['campos']['telefono'])) echo 'has-error'; ?>">
						<label class="control-label">Número de teléfono de contacto:</label>
						<input type="text" name="p_extra3" class="form-control" id="telefono" value="<?php echo $error['data']['telefono']; ?>" >
					</div>

					<div class="form-group col-sm-6 <?php if (isset($error['campos']['edad'])) echo 'has-error'; ?>">
						<label class="control-label">Edad del atleta:</label>
						<input type="text" name="p_extra3" class="form-control" id="edad" value="<?php echo $error['data']['edad']; ?>" >
					</div>
					<div class="form-group col-sm-6 <?php if (isset($error['campos']['centro_entrenamiento'])) echo 'has-error'; ?>">
						<label class="control-label">Centro de entrenamiento:</label>
						<input type="text" name="p_extra3" class="form-control" id="centro_entrenamiento" value="<?php echo $error['data']['centro_entrenamiento']; ?>" >
					</div>
					
					<div class="form-group col-sm-12 <?php if (isset($error['campos']['observacion'])) echo 'has-error'; ?>">
						<label class="control-label">Talla de camiseta:</label>
						<select class="form-control" name="p_extra3">
							<option value="S">S</option>
							<option value="M">M</option>
							<option value="L">L</option>
							<option value="XL">XL</option>
						</select>
						<!-- <input type="text" name="observacion" class="form-control" id="" value="<?php echo $error['data']['observacion']; ?>" > -->
					</div>

					<div class="form-group col-sm-12">
						<a class="btn btn-default" href="./"><i class="fa fa-chevron-circle-left"></i> Volver</a>
						<button name="submit" type="submit" class="btn btn-primary"><i class="fa fa-arrow-circle-right"></i> Registrar</button>
					</div>
				</div>
			</form>		
		</div>
	</div>
</header>


