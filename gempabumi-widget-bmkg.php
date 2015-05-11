<!DOCTYPE html>
<html lang="en-US">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Gempa Bumi Terkini sumber data dari BMKG</title>
</head>
<body>
<table width="30%" border="1" cellspasing="0" cellpadding="0">
<?php
	//if (class_exists("SimpleXMLElement")) (
		//$contentxml = file_get_contents ("http://data.bmkg.go.id/gempaterkini.xml");
		//$xml = new SimpleXMLElement ($contentxml);
		//) else echo "SimpleXMLElement class is not exists";
	$url = "http://data.bmkg.go.id/gempaterkini.xml"; // from http://data.bmkg.go.id/ sesuaikan dengan lokasi yang diinginkan
	$sUrl = file_get_contents($url, False);
	$xml = simplexml_load_string($sUrl);
?>
<tr>
	<th colspan="2" scope="col"><a href="http://bmkg.go.id/BMKG_Pusat/Gempabumi_-_Tsunami/Gempabumi/Gempabumi_Terkini.bmkg" target="_blank">Informasi Gempa Terkini</a></th>
</tr>
<tr>
	<td colspan="2" scope="col"><? echo "<img style='width:100%' src='http://dataweb.bmkg.go.id/INATEWS/eqmap.gif'><br/>"; ?></td>
</tr>
<tr>
	<td width="70">Tanggal</td>
	<td width="110"><? echo ": " .$xml->gempa->Tanggal. "</b>"; ?></td>
</tr>
<tr>
	<td>Jam</td>
	<td><? echo ": " .$xml->gempa->Jam. "</b>"; ?></td>
</tr>
<tr>
	<td>Lintang</td>
	<td><? echo ": " .$xml->gempa->Lintang. "</b>"; ?></td>
</tr>
<tr>
	<td>Bujur</td>
	<td><? echo ": " .$xml->gempa->Bujur. "</b>"; ?></td>
</tr>
<tr>
	<td>Magnitude</td>
	<td><? echo ": " .$xml->gempa->Magnitude. "</b>"; ?></td>
</tr>
<tr>
	<td>Kedalaman</td>
	<td><? echo ": " .$xml->gempa->Kedalaman. "</b>"; ?></td>
</tr>
<tr>
	<td colspan="2"><div align="center"><? echo "Wilayah : " .$xml->gempa->Wilayah. "</b>"; ?></th>
</tr>
<tr>
	<td colspan="2"><div align="center"><? echo "Koordinat : " .$xml->gempa->point->coordinates. "</b>"; ?></th>
</tr>
</table>
</body>
</html>
