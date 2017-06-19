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
			<li>
				<a href="<?php echo DIR; ?>eventos">Eventos</a>
			</li>
			<?php $hooks->run('menuCuenta', $data); ?>
			</ul>
		</div>
	</div>
</nav>

<header class="header-autenticacion">
	<div class="header-content">
		<div class="container">
			<div class="row">
				<div class="col-lg-12 text-center">
					<h2 class="section-heading"><?php echo $data['titulo']; ?></h2>
					<hr class="light">
				</div>
			</div>
		</div>
		<div class="container text-center">    
			<div class="col-md-12 ">
				<?php 

					if ($data['evento']->id == 86) {?>	
       
					<p><strong>IMPORTANTE:</strong> para que tu solicitud de registro sea aprobada debes generar un recibo en el botón epayco, no es obligatorio pagar inmediatamente, pero si no genera el recibo a través de los distintos métodos de pago, su solicitud será rechazada para que cree una nueva.</p>
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
					<?php }				else {	?>	
					
				<p><?php echo $data['mensaje']; ?></p>
				<?php if ($data['botones']): ?>
					<?php foreach ($data['botones'] as $key => $value): ?>
					<a class="btn btn-default btn-margin" href="<?php echo $value['href']; ?>"><?php echo $value['boton']; ?></a>
					<?php endforeach ?>
				<?php else: ?>
					<a class="btn btn-default" href="<?php echo $data['href']; ?>"><?php echo $data['boton']; ?></a>
				<?php endif ?>

				<?php }	?>	

				
			</div>
		</div>
	</div>
</header>