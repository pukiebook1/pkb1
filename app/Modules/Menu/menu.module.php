<?php
use Helpers\Hooks;
	 
Hooks::addHook('menu', 'Modules\Menu\Controllers\Menu@menu');
Hooks::addHook('menuCuenta', 'Modules\Menu\Controllers\Menu@menuCuenta');
Hooks::addHook('menuJuez', 'Modules\Menu\Controllers\Menu@menuJuez');
Hooks::addHook('routes', 'Modules\Menu\Controllers\Menu@menuLang');

?>