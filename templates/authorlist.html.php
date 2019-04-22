<?php//Template for the author list?>

<h2>User List</h2>

<table>
	<thread>
		<th>Name</th>
		<th>Email</th>
		<th>Edit</th>
	</thread>
	
	<body>
		<?php foreach ($authors as $author): ?>
		<tr>
			<td><?=$author->name;?></td>
			<td><?=$author->email;?></td>
			<td>
				<a href="/author/permissions?id=<?=$author->id;?>">Edit Permissions</a>
			</td>
		</tr>
		<?php endforeach; ?>
	</body>
</table>