<?php
/*
Plugin Name: BMKG - Widget Gempa Bumi
* Plugin URI: https://github.com/greenboxindonesia/widget-plugin-bmkg
* Github Plugin URI: https://github.com/greenboxindonesia/widget-plugin-bmkg
* Facebook Page: http://www.fb.com/greenboxindonesia
* Description: Plugin XML Parse php Gempa Bumi Terkini dari BMKG untuk platform wordpress.
* Version: 2.0
* Author: Albert Sukmono
* Author URI: http://www.albert.sukmono.web.id/
* Copyright (c) 2015, Albert Sukmono (http://albert.sukmono.web.id/)
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

class BMKG_Code_Widget extends WP_Widget {
	function BMKG_Code_Widget() {
		load_plugin_textdomain( 'bmkg-widget-gempa', false, dirname( plugin_basename( __FILE__ ) ) );
		$widget_ops = array('classname' => 'widget_bmkg-widget-gempa', 'description' => __('Widget Info dan Peta Gempa terkini dari BMKG', 'bmkg-widget-gempa'));
		$control_ops = array('width' => 400, 'height' => 350);
		$this->WP_Widget('bmkg-widget-gempa', __('BMKG: Gempa Terkini', 'bmkg-widget-gempa'), $widget_ops, $control_ops);
	}
	//Form Input Title di WIdget dan ditampilkan di front page
	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '' ) );
		$title = strip_tags($instance['title']);
		$text = format_to_edit($instance['text']);
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'bmkg-widget-gempa'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
		
<?php
	}
	//Function widget untuk menampilkan data yang di ambil data BMKG
	function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance );
		$text = apply_filters( 'widget_bmkg-widget-gempa', $instance['text'], $instance );
		//Menampilkan Informasi Gempa Terkini lewat parse xml php dari bmkg.go.id
		$url = "http://data.bmkg.go.id/gempaterkini.xml"; // from http://data.bmkg.go.id/ sesuaikan dengan lokasi yang diinginkan
		$sUrl = file_get_contents($url, False);
		$xml = simplexml_load_string($sUrl);
		//End Gempa
		echo $before_widget;
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; }
			ob_start();
			eval('?>'.$text);
			$text = ob_get_contents();
			ob_end_clean();
			?>
			<div class="bmkg-widget-gempa"><?php echo $instance['filter'] ? wpautop($text) : $text; ?></div>
		<?php
		echo $after_widget;
		// dibawah format untuk menampilkan data parse xml GEMPA
		echo "<table style='width:100%, border:1px solid #999;'>";
		echo "<tr>";
			echo "<th colspan='2' scope='col'><a href='http://bmkg.go.id/BMKG_Pusat/Gempabumi_-_Tsunami/Gempabumi/Gempabumi_Terkini.bmkg' target='_blank'><img style='width:100%' src='http://dataweb.bmkg.go.id/INATEWS/eqmap.gif' /></a><br/></th>";
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
		//End Script GEMPA
	}
}

add_action('widgets_init', create_function('', 'return register_widget("BMKG_Code_Widget");'));
