<?php

$request = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_ENCODED);

if ($request == 'POST') {

	$data = file_get_contents('php://input');

	$json = json_decode($data, true);

	echo $json;

	$title = $json['title'];
	$link = $json['link'];
	$os_version = $json['os_version'];
	$build_version = $json['build_version'];
	$updated = $json['updated'];
	$device_type = $json['device_type'];

	$text = $title."(".$device_type.") ".$os_version."(".$build_version.")版本已发布。下载地址：".$link." @channel";
	
	sendToMatermost($text);
}

function sendToMatermost($text) {

	// Get cURL resource
	$ch = curl_init();

	// Set url
	curl_setopt($ch, CURLOPT_URL, $MM_WEBHOCK);

	// Set method
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

	// Set options
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	// Set headers
	curl_setopt($ch, CURLOPT_HTTPHEADER, [
	  "Content-Type: application/json",
	 ]
	);
	// Create body
	$json_array = [
	            "text" => $text
	        ]; 
	$body = json_encode($json_array);

	// Set body
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

	// Send the request & save response to $resp
	$resp = curl_exec($ch);

	if(!$resp) {
	  die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
	} else {
	  echo "Response HTTP Status Code : " . curl_getinfo($ch, CURLINFO_HTTP_CODE);
	  echo "\nResponse HTTP Body : " . $resp;
	}

	// Close request to clear up some resources
	curl_close($ch);
}

