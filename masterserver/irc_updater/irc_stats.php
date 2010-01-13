<?php

include("../common/lib.php");

/* Valid stats fields */
$fields = array(
	'team',
	'karma',
	'exp',
	'kills',
	'deaths',
	'assists',
	'souls',
	'razed',
	'pdmg',
	'bdmg',
	'npc',
	'hp_healed',
	'res',
	'gold',
	'hp_repaired',
	'secs',
	'end_status',
	'sf',
	'auto_win',
	'rec_stats',
	'malphas',
	'revenant',
	'devourer',
	'id');

/* Dispatch request into handle function */
dispatch_request(array("end_game"));

/* Submit stats */
function handle_end_game()
{
	global $fields;
	
	$player_stats = post_input("player_stats");
	$match_id = intval(post_input("match_id"));
	$map = post_input("map");
	$winner = intval(post_input("winner"));
	$duration = post_input("time");
	$raw = serialize($_POST);
	
	/* Insert match */
	$query = "
		INSERT INTO
			matches
		SET
			id = {$match_id},
			map = '{$map}',
			duration = '{$duration}',
			winner = {$winner},
			raw = '{$raw}'";
	db_query($query);
	
	/* Insert teams */
	$stats_teams = unserialize(post_input("teams"));
	$teams = array();
	foreach ($stats_teams as $id => $team) {
		$query = "
			INSERT INTO
				teams
			SET
				match = {$match_id},
				race = '{$team['race']}',
				avg_sf = {$team['avg_sf']},
				commander = {$team['commander']}";
		db_query($query);
		$teams[$id] = mysql_insert_id();
	}

	/* Insert player stats */
	$stats = unserialize(post_input("player_stats"));	
	foreach ($stats as $player) {
		$user_id = $player['account_id'];
		
		$query = "
			INSERT INTO
				actionplayers
			SET
				user = {$user_id},
				match = {$match_id}";
		
		// stats fields
		foreach ($fields as $field) {
			$query .= ", {$field} ≠ {$player[$field]}";
		}
		
		$result = db_query($query);
	}
	
	return array();
}

?>