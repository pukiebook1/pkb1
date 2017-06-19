<?php
use Core\Language;
use Helpers\Hooks;
 $hooks = Hooks::get();
 session_start();
require 'requirelanguage.php';
if (isset($_GET["language"])){
  $_SESSION["language"]=$_GET["language"];
  header ('Location:'.$_SERVER['HTTP_REFERER']);
  }
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
				<li>
					<a class="page-scroll" href="#prox">Pr&oacute;x. Eventos</a>
				</li>
				<li>
					<a class="page-scroll" href="#about">Acerca</a>
				</li>
				<li>
					<a class="page-scroll" href="#services">Servicios</a>
				</li>
				<li>
					<a class="page-scroll" href="#contact">Contacto</a>
				</li>
				<li>				
<div class="dropdown closed">
  <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-language" aria-hidden="true"></i>
    <?php echo $cambiarIdioma; ?>
  </button>  
  <div class="dropdown-menu" aria-labelledby="dropdownMenu2"> 
      <a href="?language=es">
    <button class="dropdown-item" type="button"><?php echo $spanish; ?></button>     
      </a>      
      <a href="?language=en">
    <button class="dropdown-item" type="button"><?php echo $english; ?></button>
      </a> 
  </div>
</div>
</li>
				<?php $hooks->run('menuCuenta', $data); ?>
			</ul>
		</div>
	</div>
</nav>

<header>
	<div class="header-img"></div>
	<div class="header-content">
		<div class="header-content-inner">
			<img alt="Pukiebook" class="logo-title" src="<?php echo DIR;?>app/templates/<?php echo TEMPLATE;?>/img/logoLetrasGrande.png"/>
			<br/>
			<h1 class="title2-anim"><?php echo $home1; ?></h1>
			<h4 class="title3-anim"><?php echo $home2; ?></h4>
			<br/>
			<a href="#about" class="btn btn-margin btn-primary page-scroll btn-title-anim"><i class="fa fa-info-circle"></i> <?php echo $home3; ?></a>
			<a href="<?php echo DIR; ?>eventos" class="btn btn-margin btn-primary btn-title-anim"><i class="fa fa-calendar"></i><?php echo $home4; ?></a>
		</div>
	</div>
</header>

<?php if (!empty($data['proximos']) || !empty($data['curso'])): ?>
<section id="prox" class="bg-primary">
	<div class="container text-center">
		<div class="call-to-action">
			<?php if (!empty($data['proximos'])): ?>
				<h2><?php echo $home5; ?></h2>
				<div class="eventos-list">

					<?php foreach ($data['proximos'] as $keyEv => $valueEv): ?>
					<div class="evento">
						<a href="<?php echo DIR; ?>evento/<?php echo $valueEv->internalURL;?>">
							<div class="imgContainer">
								<img alt="<?php echo $valueEv->nombre; ?>" src="<?php echo $valueEv->fotoPath;?>"/> 
								<span class="helper"></span>
							</div>
							
							<span class="titulo"><?php echo $valueEv->nombre; ?></span>
							<br/>
							<span class="disciplina"><span style="padding-right: 5px;"><i class="fa fa-tag" aria-hidden="true"></i></span><?php echo $valueEv->disciplinaStr; ?></span>
							
							<span class="small">

								<?php if(!$valueEv->visible): ?>
									<i class="fa fa-eye-slash" aria-hidden="true"></i> Oculto
								<?php endif; ?>
								
								<?php echo $valueEv->fechaHtml; ?>
							</span>
						</a>
					</div>
					<?php endforeach ?>
				</div>
			<?php endif ?>

			<p><br/><br/></p>

			<?php if (!empty($data['curso'])): ?>
				<h2><?php echo $home6; ?></h2>
				<div class="eventos-list">

					<?php foreach ($data['curso'] as $keyEv => $valueEv): ?>
					<div class="evento">
						<a href="<?php echo DIR; ?>evento/<?php echo $valueEv->internalURL;?>">
							<div class="imgContainer">
								<img alt="<?php echo $valueEv->nombre; ?>" src="<?php echo $valueEv->fotoPath;?>"/> 
								<span class="helper"></span>
							</div>
							
							<span class="titulo"><?php echo $valueEv->nombre; ?></span>
							<br/>
							<span class="disciplina"><span style="padding-right: 5px;"><i class="fa fa-tag" aria-hidden="true"></i></span><?php echo $valueEv->disciplinaStr; ?></span>
							
							<span class="small">

								<?php if(!$valueEv->visible): ?>
									<i class="fa fa-eye-slash" aria-hidden="true"></i> Oculto
								<?php endif; ?>

								<?php echo $valueEv->fechaHtml ?>
							</span>
						</a>
					</div>
					<?php endforeach ?>
				</div>
			<?php endif ?>
		</div>
	</div>
</section>
<?php endif ?>

<section class="bg-light" id="about">
	<div class="container">
		<div class="row">
			<div class="col-sm-8 col-sm-offset-2 text-center">
				<h2 class="section-heading"><?php echo $home7; ?></h2>
				<hr class="">
				<p class="text"><?php echo $home8; ?></p>
				<img class="wow bounceIn" alt="BoxLatino Magazine" src="<?php echo DIR;?>app/templates/<?php echo TEMPLATE;?>/img/logoBoxLatino.jpg" height="180" width="180">
			</div>
		</div>
	</div>
</section>

<section class="bg-primary" id="services">
	<div class="container">
		<div class="row">
			<div class="col-sm-12 text-center">
				<h2 class="section-heading"><?php echo $home9; ?></h2>
				<hr class="light">
			</div>
		</div>
	</div>
	<div class="container">
		<div class="row">
			<div class="col-sm-3 col-sm-6 text-center">
				<div class="service-box">
					<i class="fa fa-4x fa-server text-light sr-icons"></i>
					<h3><?php echo $home10; ?></h3>
					<p class="text-muted"><?php echo $home20; ?></p>
				</div>
			</div>
			<div class="col-sm-3 col-sm-6 text-center">
				<div class="service-box">
					<i class="fa fa-4x fa-line-chart text-light sr-icons"></i>
					<h3><?php echo $home11; ?></h3>
					<p class="text-muted"><?php echo $home12; ?></p>
				</div>
			</div>
			<div class="col-sm-3 col-sm-6 text-center">
				<div class="service-box">
					<i class="fa fa-4x fa-users text-light sr-icons"></i>
					<h3><?php echo $home13; ?></h3>
					<p class="text-muted"><?php echo $home14; ?></p>
				</div>
			</div>
			<div class="col-sm-3 col-sm-6 text-center">
				<div class="service-box">
					<i class="fa fa-4x fa-share-alt-square text-light sr-icons"></i>
					<h3><?php echo $home15; ?></h3>
					<p class="text-muted"><?php echo $home16; ?></p>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="bg-light" id="contact">
	<div class="container">
		<div class="row">
			<div class="col-sm-12 text-center">
				<h2 class="section-heading"><?php echo $home19; ?></h2>
				<hr class="faded">
				<p class="text-primary"><?php echo $home17; ?></p>
			</div>
			<!--<div class="col-sm-4 text-center">
				<i class="fa fa-phone fa-3x sr-contact"></i>
				<p><a class="btn text-primary" href="tel:+573225135940">+573225135940</a></p>
				<p><a class="btn text-primary" href="tel:+584141073461">+584141073461</a></p>
			</div> -->
			<div class="col-sm-4 text-center">
				<i class="fa fa-envelope-o fa-3x sr-contact"></i>
				<p><a class="btn text-primary" href="mailto:info@pukiebook.com">info@pukiebook.com</a></p>				
			</div>
			<div class="col-sm-4 text-center text-primary">
				<i class="fa fa-instagram fa-3x text-primary sr-contact"></i>
				<p><a class="btn text-primary" href="http://i.instagram.com/pukiebook/">@pukiebook</a></p>
			</div>
		</div>
	</div>
</section>

<aside class="bg-dark" style="padding: 10px 0;">
	<div class="container text-center">
		<div class="call-to-action">
			<p style="margin:  0px;font-size: 12px;font-family: monospace;"><?php echo $home18; ?><br/><span style="font-size: 12px;color:#FED101;"></span></p>
		</div>
	</div>
</aside>			