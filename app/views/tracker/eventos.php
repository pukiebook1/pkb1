<?php
use Core\Language;
use Helpers\Hooks;
session_start();
require 'requirelanguage.php';
if (isset($_GET["language"])){
  $_SESSION["language"]=$_GET["language"];
  header ('Location:'.$_SERVER['HTTP_REFERER']);
  }
 
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
				<?php $hooks->run('menuCuenta', $data); ?>
			</ul>
		</div>
	</div>
</nav>

<header>
	<div class="header-img"></div>
	<div class="header-content">
		<div class="header-content-inner">
			<h1><?php echo $eventos1; ?></h1>
			<hr class="light">
			<p>
				<?php echo $eventos2; ?>
				<form method="GET" action="" class="" role="search">
					<div class="input-group">
						<input type="text" name="buscar" class="form-control" placeholder="<?php echo $eventos3; ?>" value="<?php echo $data['busqueda']; ?>">
						<span class="input-group-btn">
							<button type="submit" class="btn btn-default">
							<span class="glyphicon glyphicon-search"></span>
							</button>
						</span>
					</div>
				</form>
			</p>
			<p>
				<?php echo $eventos4; ?>
				<br/>
				<?php
					$eventosAnno = $data['byYear'];
					$countEvento = 1;

					foreach ($eventosAnno as $keyE => $valueE)
					{
						echo "<a class=\"btn btn-margin btn-primary page-scroll\" href=\"#year".$countEvento++."\">".$keyE."</a>";
					}
				?>
			</p>
		</div>
	</div>
</header>

<?php
$eventosAnno = $data['byYear'];
$countEvento = 1;
$isColor = true;

foreach ($eventosAnno as $keyE => $valueE): ?>


<section <?php if($isColor) echo "class=\"bg-primary\""; ?> id="year<?php echo $countEvento++; ?>">
	<div class="container">
		<div class="row">
			<div class="col-lg-10 col-lg-offset-1 text-center">
				<h2 class="section-heading"><?php echo $eventos5; ?> <?php echo $keyE; ?></h2>
				<p <?php if($isColor) echo "class=\"text-faded\""; ?> ><?php echo count($valueE)." "; ?><?php echo $eventos5; ?></p>

				<div class="eventos-list col-md-12">
					<?php foreach ($valueE as $keyEv => $valueEv): ?>
					<div class="col-md-12">
						<div class="evento">
							<a href="<?php echo DIR; ?>evento/<?php echo $valueEv->internalURL;?>">
								<div class="imgContainer">
									<img  alt="<?php echo $valueEv->nombre; ?>" src="<?php echo $valueEv->fotoPath;?>"/> 
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
					</div>
					<?php endforeach ?>
				</div>
			</div>
		</div>
	</div>
</section>

<?php $isColor = !$isColor; endforeach; ?>
