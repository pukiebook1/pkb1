<?php /* Smarty version Smarty-3.1.19, created on 2017-06-16 18:42:23
         compiled from "C:\servidor_xampp\htdocs\prestashop\themes\default-bootstrap\modules\blockcontact\blockcontact.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2084559440a6f2dbd98-25279653%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '36c5611e78b24a46d9bb63b3146cf4a528764e5b' => 
    array (
      0 => 'C:\\servidor_xampp\\htdocs\\prestashop\\themes\\default-bootstrap\\modules\\blockcontact\\blockcontact.tpl',
      1 => 1491849202,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2084559440a6f2dbd98-25279653',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'telnumber' => 0,
    'email' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_59440a6f369bb0_60483672',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_59440a6f369bb0_60483672')) {function content_59440a6f369bb0_60483672($_smarty_tpl) {?>

<div id="contact_block" class="block">
	<h4 class="title_block">
		<?php echo smartyTranslate(array('s'=>'Contact Us','mod'=>'blockcontact'),$_smarty_tpl);?>

	</h4>
	<div class="block_content clearfix">
		<p>
			<?php echo smartyTranslate(array('s'=>'Our support hotline is available 24/7.','mod'=>'blockcontact'),$_smarty_tpl);?>

		</p>
		<?php if ($_smarty_tpl->tpl_vars['telnumber']->value!='') {?>
			<p class="tel">
				<span class="label"><?php echo smartyTranslate(array('s'=>'Phone:','mod'=>'blockcontact'),$_smarty_tpl);?>
</span><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['telnumber']->value, ENT_QUOTES, 'UTF-8', true);?>

			</p>
		<?php }?>
		<?php if ($_smarty_tpl->tpl_vars['email']->value!='') {?>
			<a href="mailto:<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['email']->value, ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'Contact our expert support team!','mod'=>'blockcontact'),$_smarty_tpl);?>
">
				<?php echo smartyTranslate(array('s'=>'Contact our expert support team!','mod'=>'blockcontact'),$_smarty_tpl);?>

			</a>
		<?php }?>
	</div>
</div><?php }} ?>
