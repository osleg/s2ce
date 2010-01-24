<?php

include("../common/lib.php");

$matchid = 7967;

/* Load servers and maps */
$servers = db_query_array("SELECT name, id FROM servers");
$maps = db_query_array("SELECT name, id FROM maps");

function get_server($name) {
    global $servers;
    return isset($servers[$name]) ? $servers[$name] : 0;
}

function get_map($name) {
    global $maps;
    $key = strtolower($name);
    return isset($maps[$key]) ? $maps[$key] : 0;
}

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
        return $matches[1][0];
    }, $regexps);
    
$lines = explode("\n", $matchstats);
$servername = trim(substr(trim($lines[488]), 0, -5));
$data['server'] = get_server($servername);
$data['servername'] = addslashes($servername);
$data['map'] = get_map($data['map']);

// save to databases
$query = "
    INSERT INTO 
        matches
    SET 
        id = {$matchid},
        server = {$data['server']},
        servername = '{$data['servername']}',
        winner = {$data['winner']},
        duration = '{$data['duration']}',
        map = {$data['map']}";
//db_query($query);

/* Teams */

// read races from match html
preg_match_all('/mr_race_([a-zA-Z]+).gif/', $matchstats, $races);
$i = 0;

// insert teams
$teamid = array();
foreach($races[1] as $race) {
    $query = "
        INSERT INTO
            teams
        SET
            `match` = {$matchid},
            `race` = '{$race}'";
    db_query($query);
    $teamid[$i++] = mysql_insert_id();
}

/* Commanders */

// read stats
$commstats = file_get_contents('testcomm');

// parse all values into an array
$regexp = '$ width=129 height=30 valign=top>([0-9,:]*)</td$';
preg_match_all($regexp, $commstats, $values);

// set correct keys
$fields = array_combine(
    array('exp', 'orders', 'golds', 'builds', 'repaired', 'razed', 'buffs', 'hp_healed', 'debuffs', 'pdmg', 'kills', 'secs'),
    $values[1]);
    
// repaired doesn't work
unset($fields['repaired']);

// calculate duration in seconds
$parts = explode(":", $fields['secs']);
$fields['secs'] = $parts[2] + 60 * $parts[1] + 60 * 60 * $parts[2];

// create sql from fields
$fieldssql = array_redce

// insert into database
$query = "
    INSERT INTO
        commanders
    SET
        `match`= {$matchid},
        `team` = {$teamid[$i]},
        `user` = {$userid}, ";

$query .= 

/* Players */
