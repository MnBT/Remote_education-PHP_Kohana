<ul style="list-style-type: none;">
	<?foreach($roles as $role):?>
	<li>
		<a href="/roles/get_rules/<?=$role["id"]?>" class="role_name"><?=ucfirst($role["name"])?></a>
	</li>
	<?endforeach;?>
</ul>