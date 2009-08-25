<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-gb" lang="en-gb" >
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="keywords" content="savage2,savage2,s2,free-to-play,rpg,fps" />
	<title>Savage 2 - Community Edition</title>
	<link rel="stylesheet" href="/css/style.css" type="text/css" />
</head>
<body>
	<div id="wrapper">
		<div id="header">
			<div id="header1"></div>
			<div id="header2"></div>
		</div>
		<table id="content">
			<tr>
				<td id="sidebar">
					<div class="box login">
						<h3>Login</h3>
						<? if($isAuthed): ?>
						Logged in as:<br/>
						<? echo $auth['User']['username'] ?><br/><br/>
						<a href="/users/logout">Logout</a><br/>
						<? else: ?>
						<a href="/users/login">Login</a><br/>
						<a href="/users/register">Register new account</a><br/>
						<? endif; ?>
					</div>
					<? if($isAuthed): ?>
					<div class="box">
						<h3>Buddies</h3>
						<? if(empty($auth["Buddies"])): ?>
						You haven't added any buddies yet.
						<? else: ?>
						<? foreach($auth["Buddies"] as $buddy): ?>
						<?= $buddy["username"] ?><br/>
						<? endforeach; ?>
						<? endif; ?>
					</div>
					<? endif; ?>

					<div class="box">
						<h3>Servers</h3>
						No servers online.
					</div>
					<div class="box">
						<h3>Ranking</h3>
						Show top players
					</div>
				</td>
				<td id="main">
					<? if($session->check('Message.flash')): $session->flash(); endif; ?>
					<? echo $content_for_layout ?>	
				</td>
			</tr>
		</table>
		</div>
		<div id="footer">
			Savage 2: A Tortured Soul (c) 2009 S2 Games, All Rights Reserved.
		</div>
	</div>
</body>
</html>