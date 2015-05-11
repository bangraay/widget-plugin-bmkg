<?php
/**
* Plugin Name: Coba Plugin
* Plugin URI: http://magzimp.com/
* Description: Plugin sederhana di wordpress.
* Version: 1.0
* Author: magzimp.com
* Author URI: https://www.facebook.com/pages/magzimpcom/104823932915373?ref=hl
* Copyright (c) 2012, magzimp.com (http://magzimp.com/)
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

add_action(“plugins_loaded”, “myPluginInit”);

function myPluginInit(){

register_sidebar_widget(__(“Coba Plugin v1.0″), “widget_coba_plugin”);

}

// Widget plugin yang akan muncul di Appearance->Widgets webmin wordpress

function widget_coba_plugin($args) {

extract($args);
echo $before_widget;
showWidgetCobaPlugin();
echo $after_widget;

}

//Widget Plugin untuk sidedbar yang biasanya tampil di sisi kiri atau sisi kanan website

function showWidgetCobaPlugin(){

//Tulis skrip disini…..

echo “<p>Hello, saya adalah widget coba plugin…</p>”;

}

?>
