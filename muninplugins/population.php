<?php

/* Query the master server, get list of servers, sum up player numbers */

require("common.php");
$TARGET = "http://masterserver.savage2.s2games.com/irc_updater/svr_request_pub.php";

// open connection
$ch = curl_init();

// set the url, number of POST vars, POST data
curl_setopt($ch, CURLOPT_URL, $TARGET);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "f=get_online");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

// execute post
$result = curl_exec($ch);

// parse result
$result = s2_deserialize($result);

// calculate sum of players
$total = 0;
foreach($result as $server) {
	$total += $server['num_conn'];
}
print $total;