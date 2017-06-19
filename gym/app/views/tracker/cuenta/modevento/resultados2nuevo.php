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
				<?php $hooks->run('menu', $data); ?>
			</ul>
		</div>
	</div>
</nav>

<header class="header-crearevento">
	<div class="header-content">
		<div class="container">
			<h1>Registro de resultados</h1>
			<p>Evento: <?php echo $data['evento']->nombre; ?></p>
			<p><i class="fa fa-tag" aria-hidden="true"></i> <?php echo $data['categoria']->nombre; ?></p>
			<hr class="light">
			
			<p>Seleccione el WOD</p>
		</div>
		<div class="container">
			<div class="col-md-12">
				<?php $i=0; foreach ($data['wods'] as $key => $value): $i++;?>

					<?php if ( ($data['evento']->disciplina == 10) && $data['estoy'] && !$data['esAdmin'] && !$data['esOrg'] && !$data['esJuez']): ?>
						<?php if ( ($value->apertura <= time()) && ($value->cierre >= time()) ): ?>
							<a href="<?php echo DIR; ?>cuenta/modevento/<?php echo $data['evento']->internalURL; ?>/miresultado/<?php echo $data['categoria']->id; ?>/<?php echo $key; ?>" class="btn btn-margin btn-primary"><i class="fa fa-link"></i> <?php echo $value->nombre; ?></a>
						<?php else: ?>
							<label class="btn btn-margin btn-disabled"><i class="fa fa-link"></i> WOD <?php echo $i; ?></label>	
						<?php endif ?>
					<?php else: ?>
						<a href="<?php echo DIR; ?>cuenta/modevento/<?php echo $data['evento']->internalURL; ?>/resultados/<?php echo $data['categoria']->id; ?>/<?php echo $key; ?>" class="btn btn-margin btn-primary"><i class="fa fa-link"></i> <?php echo $value->nombre; ?></a>	
					<?php endif ?>					
				<?php endforeach ?>                            
			</div>
			<p>	</p>
            <div class="col-md-12">
                <div class="form-group">
                    <a class="btn btn-margin btn-default" href="<?php echo DIR; ?>cuenta/modevento/<?php echo $data['evento']->internalURL; ?>/resultados"><i class="fa fa-chevron-circle-left"></i> Volver</a>
                </div>
            </div>
		</div>
	</div>
</header>