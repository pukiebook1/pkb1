<header class="header-error">
	<div class="header-content">
		<div class="header-content-inner">
			<img src="<?php echo DIR;?>app/templates/<?php echo TEMPLATE;?>/img/logoLetras.png"/>
			<p></p>
			<h1>Error 500</h1>

			<?php echo $data['error'];?>

			<hr />

			<h3>Ha ocurrido un error interno en el servidor</h3>
			<p>Por favor intente m&aacute;s tarde.</p>
		</div>
	</div>
</header>