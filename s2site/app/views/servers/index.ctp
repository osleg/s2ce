<h1>Active servers</h1>
<table>
	<tr>
		<th>IP</th>
		<th>Port</th>
		<th>Name</th>
		<th>Players</th>
	</tr>
	<? foreach($servers as $esrver): ?>
	<tr>
		<td><?= $server['Server']['ip'] ?></td>
		<td><?= $server['Server']['port'] ?></td>
		<td><?= $server['Server']['name'] ?></td>
		<td><?= $server['Server']['num_conns'] ?> / <?= $server['Server']['max_conns'] ?></td>		
	</tr>
	<? endforeach; ?>
</table>