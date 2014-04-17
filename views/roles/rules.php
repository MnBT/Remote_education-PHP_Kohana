<h1>
	Rules by url
</h1>
<table style="width:100%;">
	<?foreach ($pages as $rule) {?>
	<tr>
		<td>
			<?=$rule["page"]?>
		</td>
		<td>
			<form method="POST" name="<?=$rule['page']?>" class="rule_form">
				<input type="hidden" class="url" value="/roles/edit_rule/<?=$rule['id']?>">
				<h2 style="position:relative;top:10px;left:10px;">Rules</h2>
				<div class="row">
					<?foreach($defined_rules["url"] as $index => $value):?>
					<label for="" style="width:150px;"><input type="radio" name="rule" value="<?=$value?>" <?=($rule["rule"] == $value ? 'checked="checked"' : NULL)?>><?=str_replace("_", " ", ucfirst($value));?></label><br>
					<?endforeach;?>	
					<input type="submit" value="Save" class="submit_rule">
				</div>
			</form>
		</td>
	</tr>
	<?}?>
</table>

