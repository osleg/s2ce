<?php

include("../lib.php");

/* Open database connection */
db_open();

/* Parse request */
$action = get_input("f");
$valid_actions = array("auth", "item_list", "clan_roster");
if(!in_array($action, $valid_actions))
	die("Wrong action");

/* Dispatch request */
$func = "handle_".$action;
$data = $func();

/* Display output */
s2_serialize($data);

/* Request handling */
function handle_auth()
{
	global $config;
	
	$email = mysql_real_escape_string(get_input("email"));
	$password = mysql_real_escape_string(get_input("password"));
	
	$query = "
		SELECT * FROM user 
		WHERE nickname = '{$email}' 
		AND password = MD5('{$config['hash']}{$password}')";
	
	$result = mysql_query($query);
	
	if(!mysql_num_rows($result) == 1) {
		/* No user found, return error */
		return array();	
	} else {
		/* Return user data */
		$data = array();
		$row = mysql_fetch_assoc($result);
		$data["demo"] = array("time_remaining" => "18000");
		$data["account_id"] = $row['id'];
		$data["nickname"] = $row['nickname'];
		$data["username"] = $row['username'];
		$data["account_type"] = 1;	
		
		/* Stats */
		$stats = array(
			"overall_r" => "1", 
			"sf" => "999", 
			"lf" => "89", 
			"level" => "50",
			"clan_name" => "S2Community",
			"clan_tag" => "",
			"clan_img" => "png",
			"karma" => "200",
			"account_id" => "{$row['id']}",
			"exp" => "9999999",
			"earned_exp" => "999999",
			"wins" => "42",
			"losses" => "42",
			"d_conns" => "42",
			"kills" => "1337",
			"deaths" => "1337",
			"assis" => "1337",
			"souls" => "1337",
			"razed" => "1337",
			"pdmg" => "123456",
			"bdmg" => "123456",
			"npc" => "23434",
			"hp_healed" => "1337",
			"res" => "42",
			"gold" => "21345",
			"hp_repaired" => "1337",
			"secs" => "1423",
			"total_secs" => "1325",
			"cr_fk" => "0",
			"c_wins" => "0",
			"c_losses" => "0",
			"c_d_conns" => "0",
			"c_exp" => "0",
			"c_earned_exp" => "0",
			"c_builds" => "0",
			"c_gld" => "0",
			"c_razed" => "0",
			"c_hp_healed" => "0",
			"c_hp_repaired" => "0",
			"c_pdmg" => "0",
			"c_kills" => "0",
			"c_assists" => "0",
			"c_debuffs" => "0",
			"c_buffs" => "0",
			"c_orders" => "0",
			"c_secs" => "0",
			"c_winstreak" => "0",
			"malphas" => "0",
			"devourers" => "0",
			"swtch" => "1",
			"c_swtch" => "1"
		);
		/*$data["buddy_list"] = array($row['id'] => array());
		$data["player_stats"] = array($row['id'] => $stats);
		$data["ranked_stats"] = array($row['id'] => $stats);*/
		
		/* Other info */
		$data["avatar"] = "";
		$data["karma"] = "0";
		$data["created"] = "2009-08-23 20:00:00";
		$data["account_reset"] = null;
		$data["karma_reset"] = null;
		return $data;
	}
}

function handle_item_list()
{
		
}

function handle_clan_roster()
{
		
}

?>