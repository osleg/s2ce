<?php

include("../common/lib.php");

$matchid = 7967;

/* Load servers and maps */
$servers = db_query_array("SELECT name, id FROM servers");
$maps = db_query_array("SELECT name, id FROM maps");

var_dump($servers);
var_dump($maps);

/* Fetch match page */
/*$curl_handle = curl_init();
curl_setopt($curl_handle, CURLOPT_URL, 'http://www.savage2replays.com/match_replay.php?mid='.$matchid);
curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
$matchstats = curl_exec($curl_handle);
curl_close($curl_handle); */
$matchstats = file_get_contents('test');

if (empty($matchstats)) {
    throw new Exception("Could not read match");
}

$regexps = array(
    'date' => '/>([0-9]{2}\/[0-9]{2}\/[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2})</',
    'duration' => '/>([0-9]{2}:[0-9]{2}:[0-9]{2})</',
    'winner' => '/><b>Team ([0-9])</',
    'map' => '/<b>Map Name:<\\/b> <span class=my12>([a-zA-Z ]*)<\\/span></'
);

$data = array_map(function($regexp) { 
        global $matchstats;
        preg_match_all($regexp, $matchstats, $matches);
        return $matches[1];
    }, $regexps);
    
$lines = explode("\n", $matchstats);
$data['server'] = trim(substr(trim($lines[488]), 0, -5));

if (!isset($servers[$data['server']])) {
    throw new Exception("Could not find server");
}
$data['servers'] = $servers[$data['server']];

if (!isset($maps[$data['map']])) {
    throw new Exception("Could not find map");
}
$data['map'] = $maps[$data['map']];

var_dump($data);

die;
$query = "
    INSERT INTO 
        matches
    SET 
        
    ";

/* Teams */

/* Commanders */

/* Players */
