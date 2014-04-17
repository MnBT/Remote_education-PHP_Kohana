<h1>
	Showing/hidden elements
</h1>
<table style="width:100%;">
	<?foreach ($elements as $rule) {?>
	<tr>
		<td>
			<?=$rule["page"]?>
		</td>
		<td>
			<form method="POST" name="<?=$rule['page']?>" class="rule_form">
				<input type="hidden" class="url" value="/roles/edit_rule/<?=$rule['id']?>">
				<h2 style="position:relative;top:10px;left:10px;">Rules</h2>
				<?foreach ($rule["rules"] as $key => $rule) {?>
				<div class="row">
					<?=str_replace("_", " ", ucfirst($key));?> - 
					<?foreach($defined_rules["elements"] as $index => $value):?>
					<label for=""><input type="radio" name="rule_<?=$key?>" value="<?=$value?>" <?=($rule == $value ? 'checked="checked"' : NULL)?>><?=str_replace("_", " ", ucfirst($value));?></label>
					<?endforeach;?>	
				</div>
				<?}?> 
				<input type="submit" value="Save" class="submit_rule">
			</form>
		</td>
	</tr>
	<?}?>
</table>