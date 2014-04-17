
<script type="text/javascript">
// $(function(){
// 	$(".role_name").click(function(e){
// 		e.preventDefault();
// 		self = $(this);
// 		$.ajax({
// 			url: $(this).attr("href"),
// 			success: function(response){
// 				$(".roles_options").html(response);
// 				$(".role_name").css("left", "0");
// 				self.css("position", "relative").css("left", "10px");
// 			}
// 		});
// 	});
// })
</script>

<script type="text/javascript">
$(function(){
	// $(".rule_form").live("submit", function(e){
	// 	e.preventDefault();
	// 	$.ajax({
	// 		url: $(this).find(".url").val(),
	// 		data: $(this).serialize(),
	// 		type: "POST",
	// 		success: function(response){
	// 			console.log(response);
	// 		}
	// 	})
	// });
$("#modale").dialog({
	autoOpen : false,
	modale: true,
	buttons: {
		Ok:function(){
			var data = $(".acl_form").serialize();
			var url = $(".acl_form").attr("action");
			$.ajax({
				url: url,
				data: data,
				type: "POST",
				success: function(response){
					$("#modale").dialog("close");
				}
			})
		},
		Cancel:function(){
			console.log(document.checkbox);
			document.checkbox.attr("checked", false)
			$("#modale").dialog("close");
		}
	}
});

$(".granted").live("click", function(){
	if(!$(this).is(":checked")){
		document.checkbox = $(this);
		message = $(this).val().split("|");
		message = $(this).data("role-name").toUpperCase() + " can't " + message[2].toLowerCase() + " now";
		$(".modale-content").html(message);
		$("#modale").dialog("open");
		$(this).parent().append($('<input>').attr({type: "hidden", name: "rule[denied][]", value: $(this).val()}));
	} else {
		document.checkbox = $(this);
		message = $(this).val().split("|");
		message = $(this).data("role-name").toUpperCase() + " can " + message[2].toLowerCase() + " now";
		$(".modale-content").html(message);
		$("#modale").dialog("open");
		$(this).parent().find("input[type=hidden]").remove();
	}
});
});
</script>
<form method="POST" class="acl_form" action="/roles/save_rules">
	<table class="acl_rules">
		<thead>
			<tr>
				<th>Actions</th>
				<?foreach ($list as $role) {?>
				<th>
					<?=ucfirst($role["name"])?>
				</th>	
				<?}?>
			</tr>
		</thead>
		<tbody>
			<?foreach ($pages as $index => $page) {?>
			<tr>
				<td>
					<b><?=$page["description"]?></b>
				</td>
				<?foreach ($list as $role) {?>
				<td>
					<?if(isset($matrix[$page["page"]])){
						if(in_array($role['id'], $matrix[$page["page"]])){
							$checked='checked="checked"';
						} else {
							$checked = NULL;
						}
					} else {
						$checked = NULL;
					}?>
					<label for="" style="width:150px;"><input type="checkbox" name="rule[granted][]" data-role-name="<?=$role["name"]?>" value="<?=$page['page']?>|<?=$role['id']?>|<?=$page['description']?>" <?=$checked?> class="granted">
					</label><br>
				</td>	
				<?}?>
			</tr>
			<?}?>
		</tbody>
	</table>
</form>

<div id="modale" title="ACL Confirmation">
	<div class="modale-content">
		
	</div>
</div>