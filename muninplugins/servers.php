<?php

/* Query the master server and return number of servers */

require("common.php");
$TARGET = "http://masterserver.savage2.s2games.com/irc_updater/svr_request_pub.php";

// open connection
$ch = curl_init();

// set the url, number of POST vars, POST data
curl_setopt($ch, CURLOPT_URL, $TARGET);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "f=get_online");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);

// execute post
$result = curl_exec($ch);

// parse result
if (strlen($result) > 0) {
	$result = s2_deserialize($result);
	print count($result);
} else {
	print 0;
}