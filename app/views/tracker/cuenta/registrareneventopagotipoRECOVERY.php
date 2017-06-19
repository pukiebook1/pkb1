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
			<h1>Registro de pago</h1>
			<hr class="light">
			<p>Seleccione una forma de pago</p>
			<form role="form" action="crear" method="POST">
			<input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>" />
			<div class="col-sm-6 col-sm-push-3">
				<div class="col-sm-12">
					<a href="<?php echo DIR; ?>cuenta/registrarseevento/<?php echo $data['evento']->internalURL; ?>/<?php echo $data['categoria']->id; ?>/registrarPago/1" class="btn btn-default btn-margin"><i class="fa fa-university"></i> Dep&oacute;sito/Transferencia</a><br/>
					<a href="<?php echo DIR; ?>cuenta/registrarseevento/<?php echo $data['evento']->internalURL;?>/<?php echo $data['categoria']->id; ?>/registrarPago/2" class="btn btn-default btn-margin"><i class="fa fa-money"></i> Pago Directo</a><br>
					</form>
					<?php 

					if ($data['evento']->id == 86) {
						
												 

					?>	
			
       

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
    <input name="p_url_response" type="hidden" value=""> 
    <input name="p_url_confirmation" type="hidden" value=""> 
    <input name="p_signature" type="hidden" id="signature"  value="fd19454e0d74f271e53ba931f4fdcba9" />
    <input name="idboton"type="hidden" id="idboton"  value="2165" />  
    <input type="image" id="imagen" src="https://369969691f476073508a-60bf0867add971908d4f26a64519c2aa.ssl.cf5.rackcdn.com/btns/btn4.png" />
    <input name="p_billing_name" type="hidden" value="<?php echo $data['logeado']['persona']->nombre;?>">
    <input name="p_billing_lastname" type="hidden" value="<?php echo $data['logeado']['persona']->apellido;?>">
    <input name="p_billing_email" type="hidden" value="<?php echo $data['logeado']['persona']->correo;?>">
    <input name="p_billing_document" type="hidden" value="<?php echo $data['logeado']['persona']->ident;?>">
    <input name="p_extra1" type="hidden" value="<?php echo $data['logeado']['persona']->idPersona;?>">
    
   
	
</form>
					<?php }
					?>	
				</div>

				<div class="col-sm-12">
					<p></p>
				</div>
						<div class="col-sm-12">
					<a class="btn btn-default" href="../"><i class="fa fa-chevron-circle-left"></i> Volver</a>
				</div>                        
			</div>
		</form>
		</div>
	</div>
</header>
