<?php
/**
* Plugin Name: Gempa Bumi Terkini BMKG
* Plugin URI: http://greenbox.web.id
* Github Theme URI: https://github.com/greenboxindonesia/widget-plugin-bmkg
* Description: Plugin XML Parse php Gempa Bumi Terkini dari BMKG untuk platform wordpress.
* Version: 1.0
* Author: Albert Sukmono
* Author URI: https://www.facebook.com/greenboxindonesia
* Copyright (c) 2015, Albert Sukmono (http://albert.sukmono.web.id/)
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

add_action('plugins_loaded', 'gempaBmkgInit');

function gempaBmkgInit(){
register_sidebar_widget(__('BMKG Gempa Terkini'),  'widget_gempa_plugin');
}

// Widget plugin yang akan muncul di Appearance->Widgets webmin wordpress

function widget_gempa_plugin($args) {

extract($args);
echo $before_widget;
showWidgetGempaPlugin();
echo $after_widget;

}

//Widget Plugin untuk sidedbar yang biasanya tampil di sisi kiri atau sisi kanan website

function showWidgetGempaPlugin(){
//Tulis skrip disini…..
//echo '<p>Hello, saya adalah widget coba plugin…</p>';
$url = "http://data.bmkg.go.id/gempaterkini.xml"; // from http://data.bmkg.go.id/ sesuaikan dengan lokasi yang diinginkan
$sUrl = file_get_contents($url, False);
$xml = simplexml_load_string($sUrl);
// dibawah format untuk menampilkan data parse xml
echo "<table style='width:100%, border:1px solid #999;'>";
echo "<tr>";
	echo "<th colspan='2' scope='col'><a href='http://bmkg.go.id/BMKG_Pusat/Gempabumi_-_Tsunami/Gempabumi/Gempabumi_Terkini.bmkg' target='_blank'><h4>Peta Gempa Terkini</h4></a></th>";
echo "</tr>";
echo "<tr>";
	echo "<th colspan='2' scope='col'><img style='width:100%' src='http://dataweb.bmkg.go.id/INATEWS/eqmap.gif' /><br/></th>";
echo "</tr>";
echo "<tr>";
	echo "<td width='70'>Tanggal</td>";
	echo "<td width='110'> : " .$xml->gempa->Tanggal. "</b></td>";
echo "</tr>";
echo "<tr>";
	echo "<td>Jam</td>";
	echo "<td> : " .$xml->gempa->Jam. "</b></td>";
echo "</tr>";
echo "<tr>";
	echo "<td>Lintang</td>";
	echo "<td> : " .$xml->gempa->Lintang. "</b></td>";
echo "</tr>";
echo "<tr>";
	echo "<td>Bujur</td>";
	echo "<td> : " .$xml->gempa->Bujur. "</b></td>";
echo "</tr>";
echo "<tr>";
	echo "<td>Magnitude</td>";
	echo "<td> : " .$xml->gempa->Magnitude. "</b></td>";
echo "</tr>";
echo "<tr>";
	echo "<td>Kedalaman</td>";
	echo "<td> : " .$xml->gempa->Kedalaman. "</b></td>";
echo "</tr>";
echo "<tr>";
	echo "<td colspan='2'><div align='center'>Wilayah : " .$xml->gempa->Wilayah. "</b></th>";
echo "</tr>";
echo "<tr>";
	echo "<td colspan='2'><div align='center'>Koordinat : " .$xml->gempa->point->coordinates. "</b></th>";
echo "</tr>";
echo "</table>";
}
//end
?>
