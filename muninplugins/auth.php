<?php

/* Try to log in to the auth server */

require("common.php");
$TARGET = "http://masterserver.savage2.s2games.com/irc_updater/irc_requester.php";

// open connection
$ch = curl_init();

// set the url, number of POST vars, POST data
curl_setopt($ch, CURLOPT_URL, $TARGET);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "f=auth&email=ChatBot&password=roboter");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);


// execute post
$result = curl_exec($ch);

// parse result
$result = s2_deserialize($result);

if (isset($result['account_id']) && $result['account_id'] == 350570)
	print 1;
else
	print 0;