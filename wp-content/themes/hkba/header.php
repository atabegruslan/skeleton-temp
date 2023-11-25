<?php wp_head(); ?>

<?php

$args = array(
	'theme_location' => 'uacmenu',
	'menu_class' => 'uac_menu'
);
wp_nav_menu($args);

?>