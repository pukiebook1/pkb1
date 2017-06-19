<?php
use Core\Language;
use Helpers\Hooks;
 
$hooks = Hooks::get();

?>

<style>
	table
	{
	}

	table tr
	{
		min-height: 4px;
		text-align: left;
	}

	table tr th
	{
		padding-right: 10px;
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
                <?php $hooks->run('menu', $data); ?>
			</ul>
		</div>
		
		<!-- /.navbar-collapse -->
	</div>
	<!-- /.container-fluid -->
</nav>

<header class="header-crearevento">
	<div class="header-content">
		<div class="container">
			<h1>Detalle de Pago</h1>
			<hr class="light">
		</div>
		<div class="container">    
			<?php if ($data['pago']): ?>
				<table style="margin:auto;">
					<tr>
						<th>Evento</th>
						<td><?php echo $data['evento']->nombre; ?></td>
					</tr>
					<tr>
						<th>Categor&iacute;a</th>
						<td><?php echo $data['categoria']->nombre; ?></td>
					</tr>
					<tr>
						<th>&nbsp;</th>
						<td></td>
					</tr>
					<tr>
						<th>Solicitante</th>
						<td><?php echo $data['persona']->nombre." ".$data['persona']->apellido; ?></td>
					</tr>
					<tr>
						<th>Ced/Pasaporte</th>
						<td><?php echo $data['persona']->ident; ?></td>
					</tr>
					<tr>
						<th>Correo</th>
						<td><a href="mailto:<?php echo $data['persona']->correo; ?>"><?php echo $data['persona']->correo; ?></a></td>
					</tr>
					<tr>
						<th>Tel&eacute;fonos</th>
						<td><?php echo $data['pago']->telefono; ?></td>
					</tr>
					<tr>
						<th>&nbsp;</th>
						<td></td>
					</tr>
					<tr>
						<th>M&eacute;todo de pago</th>
						<td>
							<?php if ($data['pago']->formapago == 1): ?>
								Bancario
							<?php else: ?>
								Directo
							<?php endif ?>
						</td>
					</tr>
					<tr>
						<th>N&uacute;m. Referencia/Recibo</th>
						<td><?php echo $data['pago']->numreferencia; ?></td>
					</tr>
					<tr>
						<th>Dirección facturación</th>
						<td><?php echo $data['pago']->address; ?></td>
					</tr>
					<tr>
						<th>Monto</th>
						<td><?php echo $data['pago']->monto; ?></td>
					</tr>
					<tr>
						<th>Fecha</th>
						<td><?php echo $data['pago']->fecha; ?></td>
					</tr>
					<tr>
						<th>&nbsp;</th>
						<td></td>
					</tr>
					<tr>
						<th>Talla de camiseta:</th>
						<td><?php echo $data['pago']->observacion; ?></td>
					</tr>
					<tr>
						<th>Edad del atleta:</th>
						<td><?php echo $data['pago']->edad; ?></td>
					</tr>
					<tr>
						<th>Centro de entrenamiento:</th>
						<td><?php echo $data['pago']->centro_entrenamiento; ?></td>
					</tr>
                                        
				</table>
			<?php else: ?>
				<p style="color:yellow;">Pago no encontrado</p>
			<?php endif ?>
		</div>
	</div>
</header>	