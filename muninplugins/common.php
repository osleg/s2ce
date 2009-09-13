<?php

function s2_deserialize(&$text) 
{	
	$type = get_token($text);
	switch ($type[0]) {
		case 'a':
			$nr = get_token($text);
			$text = substr($text, 1);			
			$result = array();
			for($i = 0; $i < $nr; $i++) {
				$key = s2_deserialize($text);
				$result[$key] = s2_deserialize($text);
			}
			$text = substr($text, 1);
			break;
		case 's':
			$length = get_token($text);
			$result = get_token($text);
			$result = substr($result, 1, strlen($result) - 2);
			break;
		case 'i':			
			$result = get_token($text);
			break;
		default:
			$result = "undefined";
	}
	return $result;
}

function get_token(&$text) {
	if ($text[0] == '"')
		$pos = strpos($text, '"', 1) + 1;
	else
		$pos = min(strpos($text, ":"), strpos($text, ";"), strlen($text));
	$token = substr($text, 0, $pos);
	$text = substr($text, $pos + 1);
	return $token;
}