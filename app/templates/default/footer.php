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
	
<script type="text/javascript">
$(function()
{
	$(document).on('click', '.btn-add', function(e)
	{
		e.preventDefault();

		var controlForm = $('.controls form:first'),
			currentEntry = $(this).parents('.entry:first'),
			newEntry = currentEntry.clone();

		newEntry.find('input').val('');
		newEntry.find('textarea').val('');
		currentEntry.after(newEntry);
		
		controlForm.find('.entry:not(:last) .btn-add')
			.removeClass('btn-add').addClass('btn-remove')
			.removeClass('btn-success').addClass('btn-danger')
			.html('<span class="glyphicon glyphicon-minus"></span>');


	}).on('click', '.btn-remove', function(e)
	{
		$(this).parents('.entry:first').remove();

		e.preventDefault();
		return false;
	});	

	$(document).on('click', '.btn-add-sponsor', function(e)
	{
		e.preventDefault();

		var controlForm = $('.relContainer'),
			currentEntry = $(this).parents('.sponsor:first'),
			newEntry = currentEntry.clone();

		newEntry.find('input').val('');
		currentEntry.after(newEntry);
		
		controlForm.find('.sponsor:not(:last) .btn-add-sponsor')
			.removeClass('btn-add-sponsor').addClass('btn-remove-sponsor')
			.removeClass('btn-success').addClass('btn-danger')
			.html('<span class="glyphicon glyphicon-minus"></span>');


	}).on('click', '.btn-remove-sponsor', function(e)
	{
		$(this).parents('.sponsor:first').remove();

		e.preventDefault();
		return false;
	});

	$(document).on('click', '.btn-up', function(e)
	{
		var control = $(this).parents('.container-fluid');
		control.prev().before(control);
		e.preventDefault();
	}).on('click', '.btn-down', function(e)
	{
		var control = $(this).parents('.container-fluid');
		control.next().after(control);
		e.preventDefault();
		return false;
	});

	$(document).on("change", '#fileupload-sponsor', function(e)
	{
		var parent = $(this).parents('.img-col');
		var label = parent.find('label.status');

		if( $(this).val() < 1)
			label.addClass('hid');
		else
			label.removeClass('hid');
	});

	var parentHeight = $('header').height(),
		childHeight = $('.header-content').height();
	
	if (parentHeight <= childHeight+160) {
		$('header').height(childHeight+160);
	}

	$('div.folderImage').find('img').each(function(){
		var imgClass = (this.width/this.height > 1) ? 'wide' : 'tall';
		$(this).addClass(imgClass);

		var off = 0;

		if(this.width/this.height > 1)
		{
			off = 0-((this.width/2)-60);
			$('div.folderImage').find('img').css({ "left": off+"px" });
		}
		else
		{
			off = 0-((this.height/2)-60);
			$('div.folderImage').find('img').css({ "top": off+"px" });
		}


	});
});

</script>

<?php if ($data['fbableEvento'] || $data['fbablePerfil']): ?>
<div id="fb-root"></div>
<script>
	window.fbAsyncInit = function()
	{
		FB.init(
		{
			appId      : 'your-app-id',
			xfbml      : true,
			version    : 'v2.5'
		});
	};

	(
		function(d, s, id)
		{
			var js, fjs = d.getElementsByTagName(s)[0];
			var theLanguage = $('html').attr('lang');

			if (d.getElementById(id))
				return;

			js = d.createElement(s);
			js.id = id;

			if( theLanguage == "es" )
				js.src = "//connect.facebook.net/es_LA/sdk.js";
			else
				js.src = "//connect.facebook.net/en_US/sdk.js";

			fjs.parentNode.insertBefore(js, fjs);
		}
		(document, 'script', 'facebook-jssdk')
	);
</script>
<?php endif; ?>

<script type="text/javascript">
	function tipoWod(e)
	{
		var fortime = $(e).parent().parent().find('.fortime');
		var amrap = $(e).parent().parent().find('.amrap');
		var weight = $(e).parent().parent().find('.weight');

		$(fortime).hide();
		$(amrap).hide();
		$(weight).hide();


		if(e.value == 3)
		{
			$(fortime).find('.checkbox').prop('checked', true);
			$(fortime).find('.tiempo').show();
			$(fortime).show();
		}

		if(e.value == 4)
		{
			$(amrap).find('.checkbox').prop('checked', false);
			$(amrap).find('.tiempo').show();
			$(amrap).show();
		}

		if(e.value == 9)
		{
			$(weight).find('.checkbox').prop('checked', false);
			$(weight).show();
		}
	}

	function checkChange(e)
	{
		var ch = e.checked;
		var sib = $(e).parent().siblings().find('input');
		//var sib2 = $(e).parent().parent().siblings('.penalfortime');

		if(ch)
		{
			$(sib).show();
			//$(sib2).show();
		}
		else
		{
			$(sib).hide();
			//$(sib2).hide();
		}
	}

	function tipoRegistroChange(e)
	{
		var ch = e.checked;
		var tg = $('#panelEquipo');

		if(ch)
		{
			$(tg).show();
			//$(sib2).show();
		}
		else
		{
			$(tg).hide();
			//$(sib2).hide();
		}
	}
</script>
</body>
</html>
