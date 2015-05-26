<?php
/*
Plugin Name: BMKG - Widget Cuaca Jawa Timur
* Plugin URI: https://github.com/greenboxindonesia/widget-plugin-bmkg
* Github Plugin URI: https://github.com/greenboxindonesia/widget-plugin-bmkg
* Facebook Page: http://www.fb.com/greenboxindonesia
* Description: Plugin XML Parse php Infromasi Prakiraan Cuaca Propinsi Jawa Timur Terkini dari BMKG untuk platform wordpress.
* Version: 2.0
* Author: Albert Sukmono
* Author URI: http://www.albert.sukmono.web.id/
* Copyright (c) 2015, Albert Sukmono (http://albert.sukmono.web.id/)
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

class BMKG_Prakiraan_Cuaca extends WP_Widget {
	function BMKG_Prakiraan_Cuaca() {
		load_plugin_textdomain( 'bmkg-widget-cuaca', false, dirname( plugin_basename( __FILE__ ) ) );
		$widget_ops = array('classname' => 'widget-title', 'description' => __('Widget Informasi Prakiraan Cuaca Jawa Timur dari BMKG', 'bmkg-widget-cuaca'));
		$control_ops = array('width' => 400, 'height' => 350);
		$this->WP_Widget('bmkg-widget-cuaca', __('BMKG: Cuaca Jatim', 'bmkg-widget-cuaca'), $widget_ops, $control_ops);
	}

	//Form Input Title di WIdget dan ditampilkan di front page
	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '' ) );
		$title = strip_tags($instance['title']);
		$text = format_to_edit($instance['text']);
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'bmkg-widget-cuaca'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
		
<?php
	}
	
	//Function widget untuk menampilkan data yang di ambil data BMKG
	function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance );
		$text = apply_filters( 'widget_bmkg-widget-cuaca', $instance['text'], $instance );
		//Menampilkan Informasi Cuaca Jawa Timur Terkini lewat parse xml php dari bmkg.go.id
		$url = "http://data.bmkg.go.id/propinsi_16_1.xml";
		$sUrl = file_get_contents($url, False);
		$xml = simplexml_load_string($sUrl);
		for ($i=0; $i<sizeof($xml->Tanggal); $i++) {
		    $row = $xml->Tanggal[$i];
		//End Cuaca
		echo $before_widget;
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; }
			ob_start();
			eval('?>'.$text);
			$text = ob_get_contents();
			ob_end_clean();
			?>
			<div class="bmkg-widget-cuaca"><?php echo $instance['filter'] ? wpautop($text) : $text; ?></div>
		<?php
		echo $after_widget;
		// dibawah format untuk menampilkan data parse xml Cuaca - Keterangan WaktuCuaca 
		echo "<div style='color:#555;font-size:15px;text-align:center;'>Berlaku mulai tanggal " . $row->Mulai . " - " . $row->Sampai . " Pada Pukul " . $row->MulaiPukul . " WIB</div><br/>";
		//HTML Tag untuk menampilkan table
		?>
			<style>
			.table {
			  width: 100%;
			  margin-bottom: 20px;
			}
			.table-bordered, .table-bordered>tbody>tr>td, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>td, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>thead>tr>th {
			  border: 1px solid #ddd;
			}
			th {
			  text-align: left;
			}
			.ft12 {
			  font-size: 13px;
			  color:#555;
			}
			.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
			  padding: 8px;
			  line-height: 1.42857143;
			  vertical-align: top;
			  border-top: 1px solid #ddd;
			}
			.table>thead>tr>th {
			  vertical-align: bottom;
			  border-bottom: 2px solid #ddd;
			}
			</style>
			<div style="width:100%;height:345px;overflow:auto;margin-bottom: 20px;">
			<table class="table table-hover table-bordered table-striped ft12">
			<thead>
			  <tr>
			    <th>Kota</th>
			    <th>Cuaca Hari Ini</th>
				<th>Cuaca Esok Hari</th>
			  </tr>
			</thead>
				<tbody>
					<!--- Cuaca Kota -->
					<?php
					$url = "http://data.bmkg.go.id/propinsi_16_1.xml";
					$url2 = "http://data.bmkg.go.id/propinsi_16_2.xml";
					$sUrl = file_get_contents($url, False);
					$sUrl2 = file_get_contents($url2, False);
					$xml = simplexml_load_string($sUrl);
					$xml2 = simplexml_load_string($sUrl2);
					for ($i,$i2=0; $i<sizeof($xml->Isi->Row), $i2<sizeof($xml2->Isi->Row);$i++, $i2++) {
						$row = $xml->Isi->Row[$i];
						$row2 = $xml2->Isi->Row[$i2];
					    	if(strtolower($row->Kota) == "blitar")  {
							echo "<tr>";
							//================Kota propinsi=======================
						    	echo"<td><b>" . strtoupper($row->Kota) . "</b><br/>Koordinat<br/>";
							echo "Lintang : " . $row->Lintang . "<br/>";
							echo "Bujur : " . $row->Bujur . "<br/>";
							echo"</td>";
							//cuaca hari ini
						    	echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row->Cuaca . ".gif' alt='" . $row->Cuaca . "' /><br/>";
						    	echo " " . $row->Cuaca . "<br/>";
						    	echo "Suhu : " . $row->SuhuMin . " - ".$row->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row->KelembapanMin . " - " . $row->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row->ArahAngin . "<br/>";
							echo"</td>";
							//cuaca esok hari
							echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row2->Cuaca . ".gif' alt='" . $row2->Cuaca . "' /><br/>";
						    	echo " " . $row2->Cuaca . "<br/>";
						    	echo "Suhu : " . $row2->SuhuMin . " - ".$row2->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row2->KelembapanMin . " - " . $row2->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row2->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row2->ArahAngin . "<br/>";
							echo"</td>";
							echo "</tr>";
							}
							//================//Kota propinsi=======================
							if(strtolower($row->Kota) == "kediri")  {						
							echo "<tr>";
						    	echo"<td><b>" . strtoupper($row->Kota) . "</b><br/>Koordinat<br/>";
							echo "Lintang : " . $row->Lintang . "<br/>";
							echo "Bujur : " . $row->Bujur . "<br/>";
							echo"</td>";
							//cuaca hari ini
						    	echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row->Cuaca . ".gif' alt='" . $row->Cuaca . "' /><br/>";
						    	echo " " . $row->Cuaca . "<br/>";
						    	echo "Suhu : " . $row->SuhuMin . " - ".$row->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row->KelembapanMin . " - " . $row->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row->ArahAngin . "<br/>";
							echo"</td>";
							//cuaca esok hari
							echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row2->Cuaca . ".gif' alt='" . $row2->Cuaca . "' /><br/>";
						    	echo " " . $row2->Cuaca . "<br/>";
						    	echo "Suhu : " . $row2->SuhuMin . " - ".$row2->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row2->KelembapanMin . " - " . $row2->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row2->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row2->ArahAngin . "<br/>";
							echo"</td>";
							echo "</tr>";
							}
							//================//Kota propinsi=======================
							if(strtolower($row->Kota) == "tulungagung")  {						
							echo "<tr>";
						    	echo"<td><b>" . strtoupper($row->Kota) . "</b><br/>Koordinat<br/>";
							echo "Lintang : " . $row->Lintang . "<br/>";
							echo "Bujur : " . $row->Bujur . "<br/>";
							echo"</td>";
							//cuaca hari ini
						    	echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row->Cuaca . ".gif' alt='" . $row->Cuaca . "' /><br/>";
						    	echo " " . $row->Cuaca . "<br/>";
						    	echo "Suhu : " . $row->SuhuMin . " - ".$row->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row->KelembapanMin . " - " . $row->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row->ArahAngin . "<br/>";
							echo"</td>";
							//cuaca esok hari
							echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row2->Cuaca . ".gif' alt='" . $row2->Cuaca . "' /><br/>";
						    	echo " " . $row2->Cuaca . "<br/>";
						    	echo "Suhu : " . $row2->SuhuMin . " - ".$row2->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row2->KelembapanMin . " - " . $row2->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row2->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row2->ArahAngin . "<br/>";
							echo"</td>";
							echo "</tr>";
							}
							//================//Kota propinsi=======================
							if(strtolower($row->Kota) == "kepanjen")  {						
							echo "<tr>";
						    	echo"<td><b>" . strtoupper($row->Kota) . "</b><br/>Koordinat<br/>";
							echo "Lintang : " . $row->Lintang . "<br/>";
							echo "Bujur : " . $row->Bujur . "<br/>";
							echo"</td>";
							//cuaca hari ini
						    	echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row->Cuaca . ".gif' alt='" . $row->Cuaca . "' /><br/>";
						    	echo " " . $row->Cuaca . "<br/>";
						    	echo "Suhu : " . $row->SuhuMin . " - ".$row->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row->KelembapanMin . " - " . $row->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row->ArahAngin . "<br/>";
							echo"</td>";
							//cuaca esok hari
							echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row2->Cuaca . ".gif' alt='" . $row2->Cuaca . "' /><br/>";
						    	echo " " . $row2->Cuaca . "<br/>";
						    	echo "Suhu : " . $row2->SuhuMin . " - ".$row2->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row2->KelembapanMin . " - " . $row2->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row2->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row2->ArahAngin . "<br/>";
							echo"</td>";
							echo "</tr>";
							}
							//================//Kota propinsi=======================
							if(strtolower($row->Kota) == "malang")  {						
							echo "<tr>";
						    	echo"<td><b>" . strtoupper($row->Kota) . "</b><br/>Koordinat<br/>";
							echo "Lintang : " . $row->Lintang . "<br/>";
							echo "Bujur : " . $row->Bujur . "<br/>";
							echo"</td>";
							//cuaca hari ini
						    	echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row->Cuaca . ".gif' alt='" . $row->Cuaca . "' /><br/>";
						    	echo " " . $row->Cuaca . "<br/>";
						    	echo "Suhu : " . $row->SuhuMin . " - ".$row->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row->KelembapanMin . " - " . $row->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row->ArahAngin . "<br/>";
							echo"</td>";
							//cuaca esok hari
							echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row2->Cuaca . ".gif' alt='" . $row2->Cuaca . "' /><br/>";
						    	echo " " . $row2->Cuaca . "<br/>";
						    	echo "Suhu : " . $row2->SuhuMin . " - ".$row2->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row2->KelembapanMin . " - " . $row2->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row2->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row2->ArahAngin . "<br/>";
							echo"</td>";
							echo "</tr>";
							}
							//================//Kota propinsi=======================
							if(strtolower($row->Kota) == "batu")  {						
							echo "<tr>";
						    	echo"<td><b>" . strtoupper($row->Kota) . "</b><br/>Koordinat<br/>";
							echo "Lintang : " . $row->Lintang . "<br/>";
							echo "Bujur : " . $row->Bujur . "<br/>";
							echo"</td>";
							//cuaca hari ini
						    	echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row->Cuaca . ".gif' alt='" . $row->Cuaca . "' /><br/>";
						    	echo " " . $row->Cuaca . "<br/>";
						    	echo "Suhu : " . $row->SuhuMin . " - ".$row->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row->KelembapanMin . " - " . $row->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row->ArahAngin . "<br/>";
							echo"</td>";
							//cuaca esok hari
							echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row2->Cuaca . ".gif' alt='" . $row2->Cuaca . "' /><br/>";
						    	echo " " . $row2->Cuaca . "<br/>";
						    	echo "Suhu : " . $row2->SuhuMin . " - ".$row2->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row2->KelembapanMin . " - " . $row2->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row2->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row2->ArahAngin . "<br/>";
							echo"</td>";
							echo "</tr>";
							}
							//================//Kota propinsi=======================
							if(strtolower($row->Kota) == "ponorogo")  {						
							echo "<tr>";
						    	echo"<td><b>" . strtoupper($row->Kota) . "</b><br/>Koordinat<br/>";
							echo "Lintang : " . $row->Lintang . "<br/>";
							echo "Bujur : " . $row->Bujur . "<br/>";
							echo"</td>";
							//cuaca hari ini
						    	echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row->Cuaca . ".gif' alt='" . $row->Cuaca . "' /><br/>";
						    	echo " " . $row->Cuaca . "<br/>";
						    	echo "Suhu : " . $row->SuhuMin . " - ".$row->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row->KelembapanMin . " - " . $row->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row->ArahAngin . "<br/>";
							echo"</td>";
							//cuaca esok hari
							echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row2->Cuaca . ".gif' alt='" . $row2->Cuaca . "' /><br/>";
						    	echo " " . $row2->Cuaca . "<br/>";
						    	echo "Suhu : " . $row2->SuhuMin . " - ".$row2->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row2->KelembapanMin . " - " . $row2->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row2->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row2->ArahAngin . "<br/>";
							echo"</td>";
							echo "</tr>";
							}
							//================//Kota propinsi=======================
							if(strtolower($row->Kota) == "trenggalek")  {						
							echo "<tr>";
						    	echo"<td><b>" . strtoupper($row->Kota) . "</b><br/>Koordinat<br/>";
							echo "Lintang : " . $row->Lintang . "<br/>";
							echo "Bujur : " . $row->Bujur . "<br/>";
							echo"</td>";
							//cuaca hari ini
						    	echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row->Cuaca . ".gif' alt='" . $row->Cuaca . "' /><br/>";
						    	echo " " . $row->Cuaca . "<br/>";
						    	echo "Suhu : " . $row->SuhuMin . " - ".$row->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row->KelembapanMin . " - " . $row->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row->ArahAngin . "<br/>";
							echo"</td>";
							//cuaca esok hari
							echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row2->Cuaca . ".gif' alt='" . $row2->Cuaca . "' /><br/>";
						    	echo " " . $row2->Cuaca . "<br/>";
						    	echo "Suhu : " . $row2->SuhuMin . " - ".$row2->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row2->KelembapanMin . " - " . $row2->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row2->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row2->ArahAngin . "<br/>";
							echo"</td>";
							echo "</tr>";
							}
							//================//Kota propinsi=======================
							if(strtolower($row->Kota) == "pacitan")  {						
							echo "<tr>";
						    	echo"<td><b>" . strtoupper($row->Kota) . "</b><br/>Koordinat<br/>";
							echo "Lintang : " . $row->Lintang . "<br/>";
							echo "Bujur : " . $row->Bujur . "<br/>";
							echo"</td>";
							//cuaca hari ini
						    	echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row->Cuaca . ".gif' alt='" . $row->Cuaca . "' /><br/>";
						    	echo " " . $row->Cuaca . "<br/>";
						    	echo "Suhu : " . $row->SuhuMin . " - ".$row->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row->KelembapanMin . " - " . $row->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row->ArahAngin . "<br/>";
							echo"</td>";
							//cuaca esok hari
							echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row2->Cuaca . ".gif' alt='" . $row2->Cuaca . "' /><br/>";
						    	echo " " . $row2->Cuaca . "<br/>";
						    	echo "Suhu : " . $row2->SuhuMin . " - ".$row2->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row2->KelembapanMin . " - " . $row2->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row2->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row2->ArahAngin . "<br/>";
							echo"</td>";
							echo "</tr>";
							}
							//================//Kota propinsi=======================
							if(strtolower($row->Kota) == "lumajang")  {						
							echo "<tr>";
						    	echo"<td><b>" . strtoupper($row->Kota) . "</b><br/>Koordinat<br/>";
							echo "Lintang : " . $row->Lintang . "<br/>";
							echo "Bujur : " . $row->Bujur . "<br/>";
							echo"</td>";
							//cuaca hari ini
						    	echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row->Cuaca . ".gif' alt='" . $row->Cuaca . "' /><br/>";
						    	echo " " . $row->Cuaca . "<br/>";
						    	echo "Suhu : " . $row->SuhuMin . " - ".$row->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row->KelembapanMin . " - " . $row->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row->ArahAngin . "<br/>";
							echo"</td>";
							//cuaca esok hari
							echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row2->Cuaca . ".gif' alt='" . $row2->Cuaca . "' /><br/>";
						    	echo " " . $row2->Cuaca . "<br/>";
						    	echo "Suhu : " . $row2->SuhuMin . " - ".$row2->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row2->KelembapanMin . " - " . $row2->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row2->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row2->ArahAngin . "<br/>";
							echo"</td>";
							echo "</tr>";
							}
							//================//Kota propinsi=======================
							if(strtolower($row->Kota) == "jember")  {						
							echo "<tr>";
						    	echo"<td><b>" . strtoupper($row->Kota) . "</b><br/>Koordinat<br/>";
							echo "Lintang : " . $row->Lintang . "<br/>";
							echo "Bujur : " . $row->Bujur . "<br/>";
							echo"</td>";
							//cuaca hari ini
						    	echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row->Cuaca . ".gif' alt='" . $row->Cuaca . "' /><br/>";
						    	echo " " . $row->Cuaca . "<br/>";
						    	echo "Suhu : " . $row->SuhuMin . " - ".$row->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row->KelembapanMin . " - " . $row->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row->ArahAngin . "<br/>";
							echo"</td>";
							//cuaca esok hari
							echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row2->Cuaca . ".gif' alt='" . $row2->Cuaca . "' /><br/>";
						    	echo " " . $row2->Cuaca . "<br/>";
						    	echo "Suhu : " . $row2->SuhuMin . " - ".$row2->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row2->KelembapanMin . " - " . $row2->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row2->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row2->ArahAngin . "<br/>";
							echo"</td>";
							echo "</tr>";
							}
							//================//Kota propinsi=======================
							if(strtolower($row->Kota) == "banyuwangi")  {						
							echo "<tr>";
						    	echo"<td><b>" . strtoupper($row->Kota) . "</b><br/>Koordinat<br/>";
							echo "Lintang : " . $row->Lintang . "<br/>";
							echo "Bujur : " . $row->Bujur . "<br/>";
							echo"</td>";
							//cuaca hari ini
						    	echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row->Cuaca . ".gif' alt='" . $row->Cuaca . "' /><br/>";
						    	echo " " . $row->Cuaca . "<br/>";
						    	echo "Suhu : " . $row->SuhuMin . " - ".$row->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row->KelembapanMin . " - " . $row->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row->ArahAngin . "<br/>";
							echo"</td>";
							//cuaca esok hari
							echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row2->Cuaca . ".gif' alt='" . $row2->Cuaca . "' /><br/>";
						    	echo " " . $row2->Cuaca . "<br/>";
						    	echo "Suhu : " . $row2->SuhuMin . " - ".$row2->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row2->KelembapanMin . " - " . $row2->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row2->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row2->ArahAngin . "<br/>";
							echo"</td>";
							echo "</tr>";
							}
							//================//Kota propinsi=======================
							if(strtolower($row->Kota) == "bondowoso")  {						
							echo "<tr>";
						    	echo"<td><b>" . strtoupper($row->Kota) . "</b><br/>Koordinat<br/>";
							echo "Lintang : " . $row->Lintang . "<br/>";
							echo "Bujur : " . $row->Bujur . "<br/>";
							echo"</td>";
							//cuaca hari ini
						    	echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row->Cuaca . ".gif' alt='" . $row->Cuaca . "' /><br/>";
						    	echo " " . $row->Cuaca . "<br/>";
						    	echo "Suhu : " . $row->SuhuMin . " - ".$row->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row->KelembapanMin . " - " . $row->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row->ArahAngin . "<br/>";
							echo"</td>";
							//cuaca esok hari
							echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row2->Cuaca . ".gif' alt='" . $row2->Cuaca . "' /><br/>";
						    	echo " " . $row2->Cuaca . "<br/>";
						    	echo "Suhu : " . $row2->SuhuMin . " - ".$row2->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row2->KelembapanMin . " - " . $row2->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row2->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row2->ArahAngin . "<br/>";
							echo"</td>";
							echo "</tr>";
							}
							//================//Kota propinsi=======================
							if(strtolower($row->Kota) == "situbondo")  {						
							echo "<tr>";
						    	echo"<td><b>" . strtoupper($row->Kota) . "</b><br/>Koordinat<br/>";
							echo "Lintang : " . $row->Lintang . "<br/>";
							echo "Bujur : " . $row->Bujur . "<br/>";
							echo"</td>";
							//cuaca hari ini
						    	echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row->Cuaca . ".gif' alt='" . $row->Cuaca . "' /><br/>";
						    	echo " " . $row->Cuaca . "<br/>";
						    	echo "Suhu : " . $row->SuhuMin . " - ".$row->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row->KelembapanMin . " - " . $row->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row->ArahAngin . "<br/>";
							echo"</td>";
							//cuaca esok hari
							echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row2->Cuaca . ".gif' alt='" . $row2->Cuaca . "' /><br/>";
						    	echo " " . $row2->Cuaca . "<br/>";
						    	echo "Suhu : " . $row2->SuhuMin . " - ".$row2->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row2->KelembapanMin . " - " . $row2->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row2->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row2->ArahAngin . "<br/>";
							echo"</td>";
							echo "</tr>";
							}
							//================//Kota propinsi=======================
							if(strtolower($row->Kota) == "probolinggo")  {						
							echo "<tr>";
						    	echo"<td><b>" . strtoupper($row->Kota) . "</b><br/>Koordinat<br/>";
							echo "Lintang : " . $row->Lintang . "<br/>";
							echo "Bujur : " . $row->Bujur . "<br/>";
							echo"</td>";
							//cuaca hari ini
						    	echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row->Cuaca . ".gif' alt='" . $row->Cuaca . "' /><br/>";
						    	echo " " . $row->Cuaca . "<br/>";
						    	echo "Suhu : " . $row->SuhuMin . " - ".$row->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row->KelembapanMin . " - " . $row->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row->ArahAngin . "<br/>";
							echo"</td>";
							//cuaca esok hari
							echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row2->Cuaca . ".gif' alt='" . $row2->Cuaca . "' /><br/>";
						    	echo " " . $row2->Cuaca . "<br/>";
						    	echo "Suhu : " . $row2->SuhuMin . " - ".$row2->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row2->KelembapanMin . " - " . $row2->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row2->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row2->ArahAngin . "<br/>";
							echo"</td>";
							echo "</tr>";
							}
							//================//Kota propinsi=======================
							if(strtolower($row->Kota) == "pasuruan")  {						
							echo "<tr>";
						    	echo"<td><b>" . strtoupper($row->Kota) . "</b><br/>Koordinat<br/>";
							echo "Lintang : " . $row->Lintang . "<br/>";
							echo "Bujur : " . $row->Bujur . "<br/>";
							echo"</td>";
							//cuaca hari ini
						    	echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row->Cuaca . ".gif' alt='" . $row->Cuaca . "' /><br/>";
						    	echo " " . $row->Cuaca . "<br/>";
						    	echo "Suhu : " . $row->SuhuMin . " - ".$row->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row->KelembapanMin . " - " . $row->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row->ArahAngin . "<br/>";
							echo"</td>";
							//cuaca esok hari
							echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row2->Cuaca . ".gif' alt='" . $row2->Cuaca . "' /><br/>";
						    	echo " " . $row2->Cuaca . "<br/>";
						    	echo "Suhu : " . $row2->SuhuMin . " - ".$row2->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row2->KelembapanMin . " - " . $row2->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row2->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row2->ArahAngin . "<br/>";
							echo"</td>";
							echo "</tr>";
							}
							//================//Kota propinsi=======================
							if(strtolower($row->Kota) == "sidoarjo")  {						
							echo "<tr>";
						    	echo"<td><b>" . strtoupper($row->Kota) . "</b><br/>Koordinat<br/>";
							echo "Lintang : " . $row->Lintang . "<br/>";
							echo "Bujur : " . $row->Bujur . "<br/>";
							echo"</td>";
							//cuaca hari ini
						    	echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row->Cuaca . ".gif' alt='" . $row->Cuaca . "' /><br/>";
						    	echo " " . $row->Cuaca . "<br/>";
						    	echo "Suhu : " . $row->SuhuMin . " - ".$row->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row->KelembapanMin . " - " . $row->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row->ArahAngin . "<br/>";
							echo"</td>";
							//cuaca esok hari
							echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row2->Cuaca . ".gif' alt='" . $row2->Cuaca . "' /><br/>";
						    	echo " " . $row2->Cuaca . "<br/>";
						    	echo "Suhu : " . $row2->SuhuMin . " - ".$row2->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row2->KelembapanMin . " - " . $row2->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row2->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row2->ArahAngin . "<br/>";
							echo"</td>";
							echo "</tr>";
							}
							//================//Kota propinsi=======================
							if(strtolower($row->Kota) == "mojokerto")  {						
							echo "<tr>";
						    	echo"<td><b>" . strtoupper($row->Kota) . "</b><br/>Koordinat<br/>";
							echo "Lintang : " . $row->Lintang . "<br/>";
							echo "Bujur : " . $row->Bujur . "<br/>";
							echo"</td>";
							//cuaca hari ini
						    	echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row->Cuaca . ".gif' alt='" . $row->Cuaca . "' /><br/>";
						    	echo " " . $row->Cuaca . "<br/>";
						    	echo "Suhu : " . $row->SuhuMin . " - ".$row->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row->KelembapanMin . " - " . $row->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row->ArahAngin . "<br/>";
							echo"</td>";
							//cuaca esok hari
							echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row2->Cuaca . ".gif' alt='" . $row2->Cuaca . "' /><br/>";
						    	echo " " . $row2->Cuaca . "<br/>";
						    	echo "Suhu : " . $row2->SuhuMin . " - ".$row2->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row2->KelembapanMin . " - " . $row2->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row2->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row2->ArahAngin . "<br/>";
							echo"</td>";
							echo "</tr>";
							}
							//================//Kota propinsi=======================
							if(strtolower($row->Kota) == "jombang")  {						
							echo "<tr>";
						    	echo"<td><b>" . strtoupper($row->Kota) . "</b><br/>Koordinat<br/>";
							echo "Lintang : " . $row->Lintang . "<br/>";
							echo "Bujur : " . $row->Bujur . "<br/>";
							echo"</td>";
							//cuaca hari ini
						    	echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row->Cuaca . ".gif' alt='" . $row->Cuaca . "' /><br/>";
						    	echo " " . $row->Cuaca . "<br/>";
						    	echo "Suhu : " . $row->SuhuMin . " - ".$row->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row->KelembapanMin . " - " . $row->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row->ArahAngin . "<br/>";
							echo"</td>";
							//cuaca esok hari
							echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row2->Cuaca . ".gif' alt='" . $row2->Cuaca . "' /><br/>";
						    	echo " " . $row2->Cuaca . "<br/>";
						    	echo "Suhu : " . $row2->SuhuMin . " - ".$row2->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row2->KelembapanMin . " - " . $row2->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row2->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row2->ArahAngin . "<br/>";
							echo"</td>";
							echo "</tr>";
							}
							//================//Kota propinsi=======================
							if(strtolower($row->Kota) == "nganjuk")  {						
							echo "<tr>";
						    	echo"<td><b>" . strtoupper($row->Kota) . "</b><br/>Koordinat<br/>";
							echo "Lintang : " . $row->Lintang . "<br/>";
							echo "Bujur : " . $row->Bujur . "<br/>";
							echo"</td>";
							//cuaca hari ini
						    	echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row->Cuaca . ".gif' alt='" . $row->Cuaca . "' /><br/>";
						    	echo " " . $row->Cuaca . "<br/>";
						    	echo "Suhu : " . $row->SuhuMin . " - ".$row->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row->KelembapanMin . " - " . $row->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row->ArahAngin . "<br/>";
							echo"</td>";
							//cuaca esok hari
							echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row2->Cuaca . ".gif' alt='" . $row2->Cuaca . "' /><br/>";
						    	echo " " . $row2->Cuaca . "<br/>";
						    	echo "Suhu : " . $row2->SuhuMin . " - ".$row2->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row2->KelembapanMin . " - " . $row2->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row2->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row2->ArahAngin . "<br/>";
							echo"</td>";
							echo "</tr>";
							}
							//================//Kota propinsi=======================
							if(strtolower($row->Kota) == "madiun")  {						
							echo "<tr>";
						    	echo"<td><b>" . strtoupper($row->Kota) . "</b><br/>Koordinat<br/>";
							echo "Lintang : " . $row->Lintang . "<br/>";
							echo "Bujur : " . $row->Bujur . "<br/>";
							echo"</td>";
							//cuaca hari ini
						    	echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row->Cuaca . ".gif' alt='" . $row->Cuaca . "' /><br/>";
						    	echo " " . $row->Cuaca . "<br/>";
						    	echo "Suhu : " . $row->SuhuMin . " - ".$row->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row->KelembapanMin . " - " . $row->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row->ArahAngin . "<br/>";
							echo"</td>";
							//cuaca esok hari
							echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row2->Cuaca . ".gif' alt='" . $row2->Cuaca . "' /><br/>";
						    	echo " " . $row2->Cuaca . "<br/>";
						    	echo "Suhu : " . $row2->SuhuMin . " - ".$row2->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row2->KelembapanMin . " - " . $row2->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row2->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row2->ArahAngin . "<br/>";
							echo"</td>";
							echo "</tr>";
							}
							//================//Kota propinsi=======================
							if(strtolower($row->Kota) == "magetan")  {						
							echo "<tr>";
						    	echo"<td><b>" . strtoupper($row->Kota) . "</b><br/>Koordinat<br/>";
							echo "Lintang : " . $row->Lintang . "<br/>";
							echo "Bujur : " . $row->Bujur . "<br/>";
							echo"</td>";
							//cuaca hari ini
						    	echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row->Cuaca . ".gif' alt='" . $row->Cuaca . "' /><br/>";
						    	echo " " . $row->Cuaca . "<br/>";
						    	echo "Suhu : " . $row->SuhuMin . " - ".$row->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row->KelembapanMin . " - " . $row->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row->ArahAngin . "<br/>";
							echo"</td>";
							//cuaca esok hari
							echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row2->Cuaca . ".gif' alt='" . $row2->Cuaca . "' /><br/>";
						    	echo " " . $row2->Cuaca . "<br/>";
						    	echo "Suhu : " . $row2->SuhuMin . " - ".$row2->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row2->KelembapanMin . " - " . $row2->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row2->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row2->ArahAngin . "<br/>";
							echo"</td>";
							echo "</tr>";
							}
							//================//Kota propinsi=======================
							if(strtolower($row->Kota) == "ngawi")  {						
							echo "<tr>";
						    	echo"<td><b>" . strtoupper($row->Kota) . "</b><br/>Koordinat<br/>";
							echo "Lintang : " . $row->Lintang . "<br/>";
							echo "Bujur : " . $row->Bujur . "<br/>";
							echo"</td>";
							//cuaca hari ini
						    	echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row->Cuaca . ".gif' alt='" . $row->Cuaca . "' /><br/>";
						    	echo " " . $row->Cuaca . "<br/>";
						    	echo "Suhu : " . $row->SuhuMin . " - ".$row->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row->KelembapanMin . " - " . $row->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row->ArahAngin . "<br/>";
							echo"</td>";
							//cuaca esok hari
							echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row2->Cuaca . ".gif' alt='" . $row2->Cuaca . "' /><br/>";
						    	echo " " . $row2->Cuaca . "<br/>";
						    	echo "Suhu : " . $row2->SuhuMin . " - ".$row2->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row2->KelembapanMin . " - " . $row2->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row2->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row2->ArahAngin . "<br/>";
							echo"</td>";
							echo "</tr>";
							}
							//================//Kota propinsi=======================
							if(strtolower($row->Kota) == "bojonegoro")  {						
							echo "<tr>";
						    	echo"<td><b>" . strtoupper($row->Kota) . "</b><br/>Koordinat<br/>";
							echo "Lintang : " . $row->Lintang . "<br/>";
							echo "Bujur : " . $row->Bujur . "<br/>";
							echo"</td>";
							//cuaca hari ini
						    	echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row->Cuaca . ".gif' alt='" . $row->Cuaca . "' /><br/>";
						    	echo " " . $row->Cuaca . "<br/>";
						    	echo "Suhu : " . $row->SuhuMin . " - ".$row->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row->KelembapanMin . " - " . $row->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row->ArahAngin . "<br/>";
							echo"</td>";
							//cuaca esok hari
							echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row2->Cuaca . ".gif' alt='" . $row2->Cuaca . "' /><br/>";
						    	echo " " . $row2->Cuaca . "<br/>";
						    	echo "Suhu : " . $row2->SuhuMin . " - ".$row2->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row2->KelembapanMin . " - " . $row2->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row2->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row2->ArahAngin . "<br/>";
							echo"</td>";
							echo "</tr>";
							}
							//================//Kota propinsi=======================
							if(strtolower($row->Kota) == "tuban")  {						
							echo "<tr>";
						    	echo"<td><b>" . strtoupper($row->Kota) . "</b><br/>Koordinat<br/>";
							echo "Lintang : " . $row->Lintang . "<br/>";
							echo "Bujur : " . $row->Bujur . "<br/>";
							echo"</td>";
							//cuaca hari ini
						    	echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row->Cuaca . ".gif' alt='" . $row->Cuaca . "' /><br/>";
						    	echo " " . $row->Cuaca . "<br/>";
						    	echo "Suhu : " . $row->SuhuMin . " - ".$row->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row->KelembapanMin . " - " . $row->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row->ArahAngin . "<br/>";
							echo"</td>";
							//cuaca esok hari
							echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row2->Cuaca . ".gif' alt='" . $row2->Cuaca . "' /><br/>";
						    	echo " " . $row2->Cuaca . "<br/>";
						    	echo "Suhu : " . $row2->SuhuMin . " - ".$row2->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row2->KelembapanMin . " - " . $row2->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row2->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row2->ArahAngin . "<br/>";
							echo"</td>";
							echo "</tr>";
							}
							//================//Kota propinsi=======================
							if(strtolower($row->Kota) == "lamongan")  {						
							echo "<tr>";
						    	echo"<td><b>" . strtoupper($row->Kota) . "</b><br/>Koordinat<br/>";
							echo "Lintang : " . $row->Lintang . "<br/>";
							echo "Bujur : " . $row->Bujur . "<br/>";
							echo"</td>";
							//cuaca hari ini
						    	echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row->Cuaca . ".gif' alt='" . $row->Cuaca . "' /><br/>";
						    	echo " " . $row->Cuaca . "<br/>";
						    	echo "Suhu : " . $row->SuhuMin . " - ".$row->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row->KelembapanMin . " - " . $row->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row->ArahAngin . "<br/>";
							echo"</td>";
							//cuaca esok hari
							echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row2->Cuaca . ".gif' alt='" . $row2->Cuaca . "' /><br/>";
						    	echo " " . $row2->Cuaca . "<br/>";
						    	echo "Suhu : " . $row2->SuhuMin . " - ".$row2->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row2->KelembapanMin . " - " . $row2->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row2->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row2->ArahAngin . "<br/>";
							echo"</td>";
							echo "</tr>";
							}
							//================//Kota propinsi=======================
							if(strtolower($row->Kota) == "gresik")  {						
							echo "<tr>";
						    	echo"<td><b>" . strtoupper($row->Kota) . "</b><br/>Koordinat<br/>";
							echo "Lintang : " . $row->Lintang . "<br/>";
							echo "Bujur : " . $row->Bujur . "<br/>";
							echo"</td>";
							//cuaca hari ini
						    	echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row->Cuaca . ".gif' alt='" . $row->Cuaca . "' /><br/>";
						    	echo " " . $row->Cuaca . "<br/>";
						    	echo "Suhu : " . $row->SuhuMin . " - ".$row->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row->KelembapanMin . " - " . $row->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row->ArahAngin . "<br/>";
							echo"</td>";
							//cuaca esok hari
							echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row2->Cuaca . ".gif' alt='" . $row2->Cuaca . "' /><br/>";
						    	echo " " . $row2->Cuaca . "<br/>";
						    	echo "Suhu : " . $row2->SuhuMin . " - ".$row2->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row2->KelembapanMin . " - " . $row2->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row2->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row2->ArahAngin . "<br/>";
							echo"</td>";
							echo "</tr>";
							}
							//================//Kota propinsi=======================
							if(strtolower($row->Kota) == "bangkalan")  {						
							echo "<tr>";
						    	echo"<td><b>" . strtoupper($row->Kota) . "</b><br/>Koordinat<br/>";
							echo "Lintang : " . $row->Lintang . "<br/>";
							echo "Bujur : " . $row->Bujur . "<br/>";
							echo"</td>";
							//cuaca hari ini
						    	echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row->Cuaca . ".gif' alt='" . $row->Cuaca . "' /><br/>";
						    	echo " " . $row->Cuaca . "<br/>";
						    	echo "Suhu : " . $row->SuhuMin . " - ".$row->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row->KelembapanMin . " - " . $row->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row->ArahAngin . "<br/>";
							echo"</td>";
							//cuaca esok hari
							echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row2->Cuaca . ".gif' alt='" . $row2->Cuaca . "' /><br/>";
						    	echo " " . $row2->Cuaca . "<br/>";
						    	echo "Suhu : " . $row2->SuhuMin . " - ".$row2->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row2->KelembapanMin . " - " . $row2->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row2->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row2->ArahAngin . "<br/>";
							echo"</td>";
							echo "</tr>";
							}
							//================//Kota propinsi=======================
							if(strtolower($row->Kota) == "sampang")  {						
							echo "<tr>";
						    	echo"<td><b>" . strtoupper($row->Kota) . "</b><br/>Koordinat<br/>";
							echo "Lintang : " . $row->Lintang . "<br/>";
							echo "Bujur : " . $row->Bujur . "<br/>";
							echo"</td>";
							//cuaca hari ini
						    	echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row->Cuaca . ".gif' alt='" . $row->Cuaca . "' /><br/>";
						    	echo " " . $row->Cuaca . "<br/>";
						    	echo "Suhu : " . $row->SuhuMin . " - ".$row->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row->KelembapanMin . " - " . $row->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row->ArahAngin . "<br/>";
							echo"</td>";
							//cuaca esok hari
							echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row2->Cuaca . ".gif' alt='" . $row2->Cuaca . "' /><br/>";
						    	echo " " . $row2->Cuaca . "<br/>";
						    	echo "Suhu : " . $row2->SuhuMin . " - ".$row2->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row2->KelembapanMin . " - " . $row2->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row2->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row2->ArahAngin . "<br/>";
							echo"</td>";
							echo "</tr>";
							}
							//================//Kota propinsi=======================
							if(strtolower($row->Kota) == "pamekasan")  {						
							echo "<tr>";
						    	echo"<td><b>" . strtoupper($row->Kota) . "</b><br/>Koordinat<br/>";
							echo "Lintang : " . $row->Lintang . "<br/>";
							echo "Bujur : " . $row->Bujur . "<br/>";
							echo"</td>";
							//cuaca hari ini
						    	echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row->Cuaca . ".gif' alt='" . $row->Cuaca . "' /><br/>";
						    	echo " " . $row->Cuaca . "<br/>";
						    	echo "Suhu : " . $row->SuhuMin . " - ".$row->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row->KelembapanMin . " - " . $row->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row->ArahAngin . "<br/>";
							echo"</td>";
							//cuaca esok hari
							echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row2->Cuaca . ".gif' alt='" . $row2->Cuaca . "' /><br/>";
						    	echo " " . $row2->Cuaca . "<br/>";
						    	echo "Suhu : " . $row2->SuhuMin . " - ".$row2->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row2->KelembapanMin . " - " . $row2->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row2->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row2->ArahAngin . "<br/>";
							echo"</td>";
							echo "</tr>";
							}
							//================//Kota propinsi=======================
							if(strtolower($row->Kota) == "sumenep")  {						
							echo "<tr>";
						    	echo"<td><b>" . strtoupper($row->Kota) . "</b><br/>Koordinat<br/>";
							echo "Lintang : " . $row->Lintang . "<br/>";
							echo "Bujur : " . $row->Bujur . "<br/>";
							echo"</td>";
							//cuaca hari ini
						    	echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row->Cuaca . ".gif' alt='" . $row->Cuaca . "' /><br/>";
						    	echo " " . $row->Cuaca . "<br/>";
						    	echo "Suhu : " . $row->SuhuMin . " - ".$row->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row->KelembapanMin . " - " . $row->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row->ArahAngin . "<br/>";
							echo"</td>";
							//cuaca esok hari
							echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row2->Cuaca . ".gif' alt='" . $row2->Cuaca . "' /><br/>";
						    	echo " " . $row2->Cuaca . "<br/>";
						    	echo "Suhu : " . $row2->SuhuMin . " - ".$row2->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row2->KelembapanMin . " - " . $row2->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row2->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row2->ArahAngin . "<br/>";
							echo"</td>";
							echo "</tr>";
							}
							//================//Kota propinsi=======================
							if(strtolower($row->Kota) == "surabaya")  {						
							echo "<tr>";
						    	echo"<td><b>" . strtoupper($row->Kota) . "</b><br/>Koordinat<br/>";
							echo "Lintang : " . $row->Lintang . "<br/>";
							echo "Bujur : " . $row->Bujur . "<br/>";
							echo"</td>";
							//cuaca hari ini
						    	echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row->Cuaca . ".gif' alt='" . $row->Cuaca . "' /><br/>";
						    	echo " " . $row->Cuaca . "<br/>";
						    	echo "Suhu : " . $row->SuhuMin . " - ".$row->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row->KelembapanMin . " - " . $row->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row->ArahAngin . "<br/>";
							echo"</td>";
							//cuaca esok hari
							echo"<td align='center'>";
							echo"<img width='50' src='http://www.bmkg.go.id/ImagesStatus/" . $row2->Cuaca . ".gif' alt='" . $row2->Cuaca . "' /><br/>";
						    	echo " " . $row2->Cuaca . "<br/>";
						    	echo "Suhu : " . $row2->SuhuMin . " - ".$row2->SuhuMax . " &deg;C<br/>";
						 	echo "Kelembapan : " . $row2->KelembapanMin . " - " . $row2->KelembapanMax . " %<br/>";
							echo "Kecepatan Angin : " . $row2->KecepatanAngin . " (km/jam)<br/>";
							echo "Arah Angin : " . $row2->ArahAngin . "<br/>";
							echo"</td>";
							echo "</tr>";
							//end table
							break;
							}
						}
					?>
				</tbody>
			</table>
			</div>
		<?php 
		}
	}
}

add_action('widgets_init', create_function('', 'return register_widget("BMKG_Prakiraan_Cuaca");'));
