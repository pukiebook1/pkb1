<?php

use Helpers\Assets;
use Helpers\Url;
use Helpers\Hooks;

//initialise hooks
$hooks = Hooks::get();
?>
<!DOCTYPE html>
<html lang="<?php echo LANGUAGE_CODE; ?>">
<head>

	<!-- Site meta -->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="keywords" content="pukie book pukiebook event wod score tracker ranking">
	<meta name="author" content="">
		
	<link rel="apple-touch-icon" sizes="180x180" href="<?php echo DIR; ?>favicons/apple-touch-icon.png?v=rM3rmjl8Oj">
	<link rel="icon" type="image/png" href="<?php echo DIR; ?>favicons/favicon-32x32.png?v=rM3rmjl8Oj" sizes="32x32">
	<link rel="icon" type="image/png" href="<?php echo DIR; ?>favicons/android-chrome-192x192.png?v=rM3rmjl8Oj" sizes="192x192">
	<link rel="icon" type="image/png" href="<?php echo DIR; ?>favicons/favicon-16x16.png?v=rM3rmjl8Oj" sizes="16x16">
	<link rel="manifest" href="<?php echo DIR; ?>favicons/manifest.json?v=rM3rmjl8Oj">
	<link rel="mask-icon" href="<?php echo DIR; ?>favicons/safari-pinned-tab.svg?v=rM3rmjl8Oj" color="#fed104">
	<link rel="shortcut icon" href="<?php echo DIR; ?>favicons/favicon.ico?v=rM3rmjl8Oj">
	<meta name="apple-mobile-web-app-title" content="Pukiebook">
	<meta name="application-name" content="Pukiebook">
	<meta name="msapplication-TileColor" content="#000000">
	<meta name="msapplication-TileImage" content="<?php echo DIR; ?>favicons/mstile-144x144.png?v=rM3rmjl8Oj">
	<meta name="msapplication-config" content="<?php echo DIR; ?>favicons/browserconfig.xml?v=rM3rmjl8Oj">
	<meta name="theme-color" content="#fed104">

	<?php if ($data['fbableEvento']): ?>
		<meta property="og:title" content="<?php echo $data['evento']->nombre;?>" />
		<meta property="og:image" content="<?php echo $data['evento']->fotoPath;?>" />
	<?php endif ?>

	<?php
	//hook for plugging in meta tags
	$hooks->run('meta');
	?>
	<title><?php echo $data['title'].' - '.SITETITLE; //SITETITLE defined in app/Core/Config.php ?></title>

	<!-- CSS -->
	<?php
	Assets::css(array(
		Url::templatePath() . 'css/animate.min.css',
		Url::templatePath() . 'css/bootstrap.min.css',
		Url::templatePath() . 'css/creative.min.css',
		Url::templatePath() . 'css/magnific-popup.min.css',
		Url::templatePath() . 'font-awesome/css/font-awesome.min.css',
		Url::templatePath() . 'fuente-palanquin/stylesheet.min.css',
		Url::templatePath() . 'fuente-montserrat/stylesheet.min.css',
		Url::templatePath() . 'css/style.min.css',
	));

	//hook for plugging in css
	$hooks->run('css');
	?>
	
	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	<?php
	Assets::js(array(
		Url::templatePath() . 'js/html5shiv.min.js',
		Url::templatePath() . 'js/respond.min.js',
	));
	?>
	<![endif]-->

	<?php

	Assets::js(array(
			Url::templatePath() . 'js/jquery.min.js',
	));

	//hook for plugging in javascript
	$hooks->run('js');
	?>

</head>
<body id="page-top">
<?php
//hook for running code after body tag
$hooks->run('afterBody');
?>

