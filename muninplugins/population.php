#!/usr/bin/php
<?php

/* Query the master server, get list of servers, sum up player numbers */

require("common.php");
$TARGET = "http://masterserver.savage2.s2games.com/irc_updater/svr_request_pub.php";


$SERVERS_EU = array(10, 7, 21, 12224, 23, 22761, 32554, 85);
$SERVERS_US = array(12220, 14, 34, 6, 39, 48, 5, 13, 15, 16, 29, 36, 35, 29722);

if ($argv[1] == "config") {
	print "graph_title Player population\n";
	print "graph_args --base 1000 -l 0\n";
	print "graph_vlabel players\n";
	print "graph_category Masterserver\n";
	print "graph_scale no\n";
	print "population.label total\n";
	print "official.label official\n";
	print "eu.label europe\n";
	print "us.label us\n";
} else {
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
	
	if (strlen($result) > 0) {
		// parse result
		$result = s2_deserialize($result);
		
		// calculate sum of players
		$total = 0;
		$total_official = 0;
		$total_eu = 0;
		$total_us = 0;
		foreach($result as $server) {
			$total += $server['num_conn'];
			if ($server['official'])
				$total_official += $server['num_conn'];
			if (in_array($server['id'], $SERVERS_EU))
				$total_eu += $server['num_conn'];
			if (in_array($server['id'], $SERVERS_US)) {
				$total_us += $server['num_conn'];
			}
		}
		
		print "population.value $total\n";
		print "official.value $total_official\n";
		print "us.value $total_us\n";
		print "eu.value $total_eu\n";
	} else {
		print "population.value 0\n";
		print "official.value 0\n";
		print "us.value 0\n";
		print "eu.value 0\n";
	}
}