<?php

/* Try to log in to the auth server */

require("common.php");
$URLS = array(
	"karma" => "http://savage2.com/en/karmahistory.php?aid=6057",
	"stats" => "http://savage2.com/en/get_period_stats.php?aid=6057"
);

// check if valid param
if (!isset($URLS[$argv[1]])) {
	print -1;
	die;
}

// open connection
$ch = curl_init();

// set the url, number of POST vars, POST data
curl_setopt($ch, CURLOPT_URL, $URLS[$argv[1]]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

// start time
$time_start = microtime(true);

// execute get
$result = curl_exec($ch);

// parse result
$time_end = microtime(true);
$time = $time_end - $time_start;

print floor($time);