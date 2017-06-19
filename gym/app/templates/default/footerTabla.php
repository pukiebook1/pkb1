<?php

use Helpers\Assets;
use Helpers\Url;
use Helpers\Hooks;

//initialise hooks
$hooks = Hooks::get();

//hook for plugging in code into the footer
$hooks->run('footer');
?>

<?php

Assets::js(array(
		Url::templatePath() . 'js/bootstrap.min.js',
		Url::templatePath() . 'js/jquery.easing.min.js',
		Url::templatePath() . 'js/jquery.magnific-popup.min.js',
		Url::templatePath() . 'js/scrollreveal.min.js',
		Url::templatePath() . 'js/creative.min.js',
));

//hook for plugging in javascript
$hooks->run('js');
?>

<div id="fb-root"></div>
<script>
	(function(d, s, id)
	{
	var js, fjs = d.getElementsByTagName(s)[0];
	var theLanguage = $('html').attr('lang');

	if (d.getElementById(id))
	return;

	js = d.createElement(s);
	js.id = id;
	js.src = "";

	if( theLanguage == "es" )
	js.src = "//connect.facebook.net/es_LA/sdk.js#xfbml=1&version=v2.3";
	else
	js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.3";

	fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));
</script>

<script type="text/javascript">
	$(function()
	{
	    var parentHeight = $('header').height(),
	        childHeight = $('.header-content').height();
	    	
		if (parentHeight <= childHeight+160) {
			$('header').height(childHeight+160);
		}
	});
</script>
</body>
</html>
