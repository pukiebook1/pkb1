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
                <?php $hooks->run('menuCuenta', $data); ?>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container-fluid -->
</nav>

<header class="header-crearevento">
    <div class="header-content">
        <div class="header-content-inner">
            <h1>Creaci&oacute;n de Box</h1>
            <hr class="light">

<!--             <p>Este evento tendr&aacute; las siguientes restricci&oacute;nes en cantidad de atletas, categor&iacute;as y logos de patrocinadores</p>
            <p>
                <?php echo $data['persona']->atletas; ?> atletas<br/>
                <?php echo $data['persona']->categorias; ?> categoria<br/>
                <?php echo $data['persona']->patrocinadores; ?> logos de patrocinadores<br/>
            </p> -->
            <p><b>El box deber&aacute; ser aprobado luego de su creaci&oacute;n.</b></p>
            <a href="#info" class="btn btn-margin btn-primary btn-xl page-scroll"><i class="fa fa-info-circle"></i> M&aacute;s Informaci&oacute;n</a>
            <a href="<?php echo DIR; ?>cuenta/crearbox/crear" class="btn btn-margin btn-primary btn-xl page-scroll"><i class="fa fa-arrow-circle-right"></i> Comenzar!</a>
        </div>
    </div>
</header>

<section class="bg-primary" id="info">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-lg-offset-2 text-center">
                <h2 class="section-heading">Informaci&oacute;n sobre los eventos</h2>
                <hr class="light">
                <p class="text-faded">
                    Si deseas m&aacute;s informaci&oacute;n o ampliar las capacidades del evento, puedes contactar por los siguientes medios.
                </p>
                    <div class="col-sm-6 text-center">
                        <i class="fa fa-phone fa-3x wow bounceIn"></i>
                        <p><a class="btn btn-link" href="tel:+584249465770">+58 (424)946-5770</a></p>
                    </div>
                    <div class="col-sm-6 text-center">
                        <i class="fa fa-envelope-o fa-3x wow bounceIn" data-wow-delay=".1s"></i>
                        <p><a class="btn btn-link" href="mailto:info@pukiebook.com">info@pukiebook.com</a></p>
                    </div>
            </div>
        </div>
    </div>
</section>