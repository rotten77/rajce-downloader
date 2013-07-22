<?php
/**
 * Settings
 */
$albumUrl = "";
$credentials = array();
// $credentials['login'] = "";
// $credentials['password'] = "";
$folder = dirname(__FILE__) . "/photos/";

/**
 * Script
 */
$albumNameArray = explode("/",$albumUrl);
$albumName = $albumNameArray[count($albumNameArray)-2];
$downloadFolder = $folder.$albumName."/";

if(!is_dir($downloadFolder)) {
	mkdir($downloadFolder, 0777);	
	chmod($downloadFolder, 0777);	
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $albumUrl);
curl_setopt($ch, CURLOPT_HEADER, 0); 
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);	
if(count($credentials)>0) {
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $credentials);	
}

$rajceHtml = curl_exec($ch);
curl_close($ch);

$dom = new DOMDocument();
@$dom->loadHTML($rajceHtml);

$links = $dom->getElementById('photoList')->getElementsByTagName('a');

$photoList = array();

foreach ($links as $link){
	$photoList[] = $link->getAttribute('href');;
}

if(count($photoList)>0) {
	foreach($photoList as $id=>$link) {
		$fileUrl = $link;
		$fileNameArray = explode("/", $fileUrl);
		$fileName = $fileNameArray[count($fileNameArray)-1];

		echo ($id+1)."/".count($photoList)." ";

		if(file_put_contents($downloadFolder.$fileName, fopen($fileUrl, 'r'))) echo $fileName.": downloaded\n";
		else echo $fileName.": NOT downloaded\n";
	}
}