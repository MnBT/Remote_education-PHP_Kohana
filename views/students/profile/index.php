<div id="contentwrap">
	<div id="content">
		<div class="pad">
            <div class="col1 ">
                <!--div class="pad"-->
                    <div class="section first">
                        <ul class="first">
                            <li class="search">
                                <a href="">My Major<span class="more"></span></a>
                                <div class="moredrop">
                                    <ul>
                                        <li class=""><a href="index/courses/">Courses</a></li>
                                        <li class=""><a href="index/curriculum/">Personal Curriculum</a></li>
                                        <li class=""><a href="index/grades/">Grades</a></li>
                                        <li class=""><a href="index/instructor/">Instructor</a></li>
                                        <li class=""><a href="index/major/">About Major</a></li>
                                    </ul>
                                </div>
                            </li>
                            <li class="adhub on">
                                <a href="">My Profile<span class="more logmein"></span></a>
                                <div class="moredrop">
                                <ul>
                                    <li class=""><a href="#">Student Data</a></li>
                                    <li class=""><a href="profile/photos">Student Pictures</a></li>
                                </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <br class="cleandivider" />
                <!--/div-->
            </div>

            <div class="col2 ">
                <div class="wrapper">
                    <!--------Bread crumbs-------->
                    <div class="bread_crumb">
                        <div id='secondaryNav'>
                            <ul class='breadcrumb reset'>
                                <li class="first_b link_profile">
                                    <span><a href=""><img  style="background:none;" src="media/img/profile.png" alt="" /></a></span>
                                </li>
                                <li><a href=""><span>My Profile</span></a></li>
                                <li><a href=""><span>Student Data</span></a></li>
                            </ul>
                        </div>
                    </div>
                    <!------End bread crumbs------>
                    <div id="middle">
                        <div id="container">
                            <div id="cont">
                            <!-- main info -->
                                <h1 class="name"><?php echo $profile["first_name"]." ".$profile["middle_name"]." ".$profile["last_name"];?></h1>
                                <div class="main_info">
                                    <ul class="reset dark edit">
                                        <li class="first">Birth Date:</li>
                                        <li class="value"><?php if($profile["birthday"]): echo date("M. d, Y", $profile["birthday"]); ?><span class="age"><?php echo $profile["age"]; ?></span><?php else: ?>&mdash;<?php endif ?></li>
                                        <li class="options_li">
										  <div class="lcol" style="display:none;">
										  	<input type="text" class="edit_field lcol editable_datepicker" name="birthday" id="birthday" value="<?php echo date("M. d, Y", $profile["birthday"]); ?>" readonly="readonly">
										  	<input type="button" class="edit_field_button lcol">
										  </div>
										  <a href="#" class="options edit lcol"></a>
										</li>
                                    </ul>
                                    <ul class="reset light edit">
                                        <li class="first">Gender:</li>
                                        <li class="value"><?php echo ($profile["sex"]=="n")?"":($profile["sex"]=="m")?__("Male"):__("Female"); ?></li>
                                        <li class="options_li">
										  <div class="lcol" style="display:none;">
										  	<?php echo Form::select("sex", array("m"=>__("Male"), "f"=>__("Female")), $profile["sex"], array("class"=>"edit_field lcol", "id"=>"sex")); ?>
										  	<input type="button" class="edit_field_button lcol">
										  </div>
										  <a href="#" class="options edit lcol"></a>
										</li>
                                    </ul>
                                    <ul class="reset dark">
                                        <li class="first">Current Location:</li>
                                        <li><?php if($current_location) { echo $current_location["country"]["name"].", ".$current_location["city"]; } else echo __("Unknown location"); ?></li>
                                    </ul>
                                    <ul class="reset light">
                                        <li class="first">Current Time:</li>
                                        <li><?php echo __($date->format("l")).", ".__($date->format("F"))." ".$date->format("d, Y H:i:s"); ?></li>
                                    </ul>
                                </div>

                                <!-- add new location -->
                                <a href="#" class="add_button" title="add_location">Add new location</a>

                                <!-- countries -->
                                <div class="cblock_h">
<?php foreach($locations as $lid => $loc):?>
                                <div class="head lcol"><h2 class="<?php echo ($lid==$current_location["id"])?"":"not_"?>active lcol"><?php echo $loc["country"]["name"]; ?></h2><a href="<?php echo Kohana::$base_url; ?>profile/update/location_delete?lid=<?php echo $lid; ?>" class="close rcol lid<?php echo $lid;?>"></a></div>
<?php endforeach;?>
                                </div>
                                <div class="cblock_bg">
<?php foreach($locations as $lid => $loc):?>
                                	<div class="cblocks lcol <?php echo ($lid==$current_location["id"])?"":"not_"?>active">
<?php if($lid!=$current_location["id"]): ?>
                                        <a href="#" class="choose_btn lid<?php echo $lid;?>"></a>
<?php endif;?>
                                        <span><?php echo $loc["city"]; ?></span>
                                        <p><?php echo $loc["type"]["name"]; if($loc["type"]["id"]==Model_User_location::TYPE_OTHER):?> ( <?php echo $loc["type"]["description"];?> )<?php endif; if($loc["date_to"]):?><br/>Break from <?php echo __(date("M", $loc["date_from"])).date(". d, Y", $loc["date_from"]);?> till <?php echo __(date("M", $loc["date_to"])).date(". d, Y", $loc["date_to"]);?><?php else: ?><br>From <?php echo __(date("M", $loc["date_from"])).date(". d, Y", $loc["date_from"]); endif;?></p>
                                	</div>
<?php endforeach;?>
                                    <div class="clr"></div>
                                </div>
                                <div class="cblock_bottom"></div>

                                <!-- residency -->
                                <div class="block_h">
                                    <h2>Residency</h2>
                                </div>
                                <div class="block_bg">
	                                <ul class="reset light edit editsex">
	                                	   <li class="first">Country of citizenship:</li>
	                                    <li class="value"><?php echo __($profile["citizenship"]?$country[$profile["citizenship"]]:"Unknown location"); ?></li>
	                                    <li class="options_li">
											<div class="lcol" style="display:none;">
									  			<?php echo Form::select("citizenship", $country, $profile["citizenship"], array("class"=>"edit_field lcol", "id"=>"citizenship")); ?>
											  	<input type="button" class="edit_field_button lcol">
											</div>
											<a href="#" class="options edit lcol"></a>
										</li>
	                                </ul>
                                	<ul class="reset dark edit editsex">
	                                	<li class="first">Country of residence:</li>
	                                    <li class="value"><?php echo __($profile["residence"]?$country[$profile["residence"]]:"Unknown location"); ?></li>
	                                    <li class="options_li">
											<div class="lcol" style="display:none;">
									  			<?php echo Form::select("residence", $country, $profile["residence"], array("class"=>"edit_field lcol", "id"=>"residence")); ?>
											  	<input type="button" class="edit_field_button lcol">
											</div>
											<a href="#" class="options edit lcol"></a>
										</li>
	                                </ul>
                                </div>
                                <div class="block_bottom"></div>

                                <!-- contact info -->
                                <div class="block_h">
                                    <h2 class="lcol">Contact Information</h2>
                                </div>
                                <div class="block_bg contactsmd">
<?php $colors = array("dark", "light"); $cv = 0;
foreach($contacts as $contact):
$cv = 1 - $cv;
?>
                                    <ul class="reset <?php echo $colors[$cv];?> edit editoptions_<?php echo $contact["id"]?>">
	                                	<li class="first"><?php echo __($contact["type"]["name"]); ?></li>
	                                    <li class="value"><?php echo $contact["value"]; ?> <?php if($contact["subtype"]) echo __($contact["subtype"]["name"]); ?></li>
	                                    <li class="options_li">
											<div class="lcol" style="display:none;">
									  			<?php echo Form::input("contacts[".$contact["id"]."]", $contact["value"], array("class"=>"edit_field lcol tooltip", "id"=>"contacts_".$contact["id"], "title"=>__("Input phone number format:<br><u>+NN (NNN) NNN-NNNN</u>"))); ?>
											  	<input type="button" class="edit_field_button lcol">
											</div>
											<a href="#" class="options edit lcol"></a>
											<a href="#" class="options delete lcol"></a>
										</li>
	                                </ul>
<?php endforeach; ?>
									<ul class="reset <?php echo $colors[1-$cv];?>">
										<li class="first"><a href="#" onclick="return addNewContactRecord();"><?php echo __("Add new record");?></a></li>
									</ul>
                                </div>
                                <div class="block_bottom"></div>

                                <!-- Permanent Adress -->
<?php
	$addr_type = Model_User_address::TYPE_PERMANENT;
	$addr = Arr::get($address, $addr_type);
?>                                <div class="block_h"><h2 class="lcol">Permanent Address</h2></div>
                                   <div class="block_bg">
                                   <p class="alert_1"><span class="lcol"></span>All correspondence from Student Administration Office will be mailed to this address.</p>
                                     <ul class="reset dark edit">
                                       <li class="first">Country:</li>
                                       <li class="value"><?php echo $addr["country"]["name"]; ?></li>
	                                    <li class="options_li">
											<div class="lcol" style="display:none;">
									  			<?php echo Form::select("address[".$addr_type."][country]", $country, $addr["country"]["id"], array("class"=>"edit_field lcol", "id"=>"address_".$addr_type."_country")); ?>
											  	<input type="button" class="edit_field_button lcol">
											</div>
											<a href="#" class="options edit lcol"></a>
										</li>
                                    </ul>
                                   <ul class="reset light edit">
                                       <li class="first">Street and Number:</li>
                                       <li class="value"><?php echo $addr["address"]; ?></li>
	                                    <li class="options_li">
											<div class="lcol" style="display:none;">
									  			<?php echo Form::input("address[".$addr_type."][address]", $addr["address"], array("class"=>"edit_field lcol", "id"=>"address_".$addr_type."_address")); ?>
											  	<input type="button" class="edit_field_button lcol">
											</div>
											<a href="#" class="options edit lcol"></a>
										</li>
                                    </ul>
                                     <ul class="reset dark edit">
                                       <li class="first">City:</li>
                                       <li class="value"><?php echo $addr["city"]; ?></li>
	                                    <li class="options_li">
											<div class="lcol" style="display:none;">
									  			<?php echo Form::input("address[".$addr_type."][city]", $addr["city"], array("class"=>"edit_field lcol", "id"=>"address_".$addr_type."_city")); ?>
											  	<input type="button" class="edit_field_button lcol">
											</div>
											<a href="#" class="options edit lcol"></a>
										</li>
                                    </ul>
                                     <ul class="reset light edit">
                                       <li class="first">State:</li>
                                       <li class="value"><?php echo $addr["state"]; ?></li>
	                                    <li class="options_li">
											<div class="lcol" style="display:none;">
									  			<?php echo Form::input("address[".$addr_type."][state]", $addr["state"], array("class"=>"edit_field lcol", "id"=>"address_".$addr_type."_state")); ?>
											  	<input type="button" class="edit_field_button lcol">
											</div>
											<a href="#" class="options edit lcol"></a>
										</li>
                                    </ul>
                                     <ul class="reset dark edit">
                                       <li class="first">Zip:</li>
                                       <li class="value"><?php echo $addr["zip"]; ?></li>
	                                    <li class="options_li">
											<div class="lcol" style="display:none;">
									  			<?php echo Form::input("address[".$addr_type."][zip]", $addr["zip"], array("class"=>"edit_field lcol", "id"=>"address_".$addr_type."_zip")); ?>
											  	<input type="button" class="edit_field_button lcol">
											</div>
											<a href="#" class="options edit lcol"></a>
										</li>
                                    </ul>
                                     <ul class="reset light edit">
                                       <li class="first">Adress Type:</li>
                                       <li class="value"><?php echo $addr["location"]["name"]; ?></li>
	                                    <li class="options_li">
											<div class="lcol" style="display:none;">
									  			<?php echo Form::select("address[".$addr_type."][location]", Model_User_address::locations(), $addr["location"]["id"], array("class"=>"edit_field lcol", "id"=>"address_".$addr_type."_location")); ?>
											  	<input type="button" class="edit_field_button lcol">
											</div>
											<a href="#" class="options edit lcol"></a>
										</li>
                                    </ul>
                                   <p class="alert_2"><span class="lcol"></span>Permananent Adress is the same as Current Addres</p>
                                   </div>
                                <div class="block_bottom"></div>

                                <!-- Current Adress -->
<?php
	$addr_type = Model_User_address::TYPE_CURRENT;
	$addr = Arr::get($address, $addr_type);
?>                                <div class="block_h"><h2 class="lcol">Current Address</h2></div>
                                   <div class="block_bg">
                                     <ul class="reset dark edit">
                                       <li class="first">Country:</li>
                                       <li class="value"><?php echo $addr["country"]["name"]; ?></li>
	                                    <li class="options_li">
											<div class="lcol" style="display:none;">
									  			<?php echo Form::select("address[".$addr_type."][country]", $country, $addr["country"]["id"], array("class"=>"edit_field lcol", "id"=>"address_".$addr_type."_country")); ?>
											  	<input type="button" class="edit_field_button lcol">
											</div>
											<a href="#" class="options edit lcol"></a>
										</li>
                                    </ul>
                                   <ul class="reset light edit">
                                       <li class="first">Street and Number:</li>
                                       <li class="value"><?php echo $addr["address"]; ?></li>
	                                    <li class="options_li">
											<div class="lcol" style="display:none;">
									  			<?php echo Form::input("address[".$addr_type."][address]", $addr["address"], array("class"=>"edit_field lcol", "id"=>"address_".$addr_type."_address")); ?>
											  	<input type="button" class="edit_field_button lcol">
											</div>
											<a href="#" class="options edit lcol"></a>
										</li>
                                    </ul>
                                     <ul class="reset dark edit">
                                       <li class="first">City:</li>
                                       <li class="value"><?php echo $addr["city"]; ?></li>
	                                    <li class="options_li">
											<div class="lcol" style="display:none;">
									  			<?php echo Form::input("address[".$addr_type."][city]", $addr["city"], array("class"=>"edit_field lcol", "id"=>"address_".$addr_type."_city")); ?>
											  	<input type="button" class="edit_field_button lcol">
											</div>
											<a href="#" class="options edit lcol"></a>
										</li>
                                    </ul>
                                     <ul class="reset light edit">
                                       <li class="first">State:</li>
                                       <li class="value"><?php echo $addr["state"]; ?></li>
	                                    <li class="options_li">
											<div class="lcol" style="display:none;">
									  			<?php echo Form::input("address[".$addr_type."][state]", $addr["state"], array("class"=>"edit_field lcol", "id"=>"address_".$addr_type."_state")); ?>
											  	<input type="button" class="edit_field_button lcol">
											</div>
											<a href="#" class="options edit lcol"></a>
										</li>
                                    </ul>
                                     <ul class="reset dark edit">
                                       <li class="first">Zip:</li>
                                       <li class="value"><?php echo $addr["zip"]; ?></li>
	                                    <li class="options_li">
											<div class="lcol" style="display:none;">
									  			<?php echo Form::input("address[".$addr_type."][zip]", $addr["zip"], array("class"=>"edit_field lcol", "id"=>"address_".$addr_type."_zip")); ?>
											  	<input type="button" class="edit_field_button lcol">
											</div>
											<a href="#" class="options edit lcol"></a>
										</li>
                                    </ul>
                                     <ul class="reset light edit">
                                       <li class="first">Adress Type:</li>
                                       <li class="value"><?php echo $addr["location"]["name"]; ?></li>
	                                    <li class="options_li">
											<div class="lcol" style="display:none;">
									  			<?php echo Form::select("address[".$addr_type."][location]", Model_User_address::locations(), $addr["location"]["id"], array("class"=>"edit_field lcol", "id"=>"address_".$addr_type."_location")); ?>
											  	<input type="button" class="edit_field_button lcol">
											</div>
											<a href="#" class="options edit lcol"></a>
										</li>
                                    </ul>
                                   </div>
                                <div class="block_bottom"></div>
                            </div><!-- #content-->
                        </div><!-- #container-->

                        <div class="sidebar" id="sideLeft">
                            <!-- photo -->
                            <div class="avatar">
                              <img src="<?php echo $userpic[Model_User_photo::SIZE_BIG]; ?>" alt="" />
                            </div>
                            <a href="profile/photos" class="change_button" title="Change picture"></a>
                        </div><!-- .sidebar#sideLeft -->

                    </div><!-- #middle-->
                        <br class="cleandivider" />
                    </div>
                </div>
            </div>	<!-- END div.col2 -->
		</div><!-- END div.pad -->
	</div><!-- END div#content -->
</div><!-- END div#contentwrap -->
<div id="contact-dialog-form" title="<?php echo __("Add user contact");?>">
    <p class="validateTips">All form fields are required.</p>

    <form>
    <fieldset>
        <label for="newtype"><?php echo __("Type"); ?></label>
        <?php echo Form::select("type", array(0=>"&nbsp;")+Model_User_contact::$types, null, array("class"=>"ui-widget-content ui-corner-all", "id"=>"newtype"));?>
        <br />
        <label for="newsubtype"><?php echo __("Subtype"); ?></label>
        <?php echo Form::select("subtype", null, null, array("class"=>"ui-widget-content ui-corner-all", "id"=>"newsubtype"));?>
        <br />
        <label for="newvalue"><?php echo __("Value"); ?></label>
        <?php echo Form::input("value", null, array("class"=>"ui-widget-content ui-corner-all", "id"=>"newvalue"));?>
     </fieldset>
    </form>
</div>
<div id="location-dialog-form" title="<?php echo __("Location management");?>">
    <form method="post" action="<?php echo Kohana::$base_url; ?>profile/update/location">
    <?php echo Form::hidden("lid", null, array("id"=>"lfid"));?>
    <fieldset>
        <label for="lfcountry"><?php echo __("Country"); ?></label>
        <?php echo Form::select("country", array(0=>"&nbsp;")+$country, null, array("class"=>"ui-widget-content ui-corner-all", "id"=>"lfcountry"));?>
        <br />
        <label for="lfcity"><?php echo __("City"); ?></label>
        <?php echo Form::input("city", null, array("class"=>"ui-widget-content ui-corner-all", "id"=>"lfcountry"));?>
        <br>
        <label for="lftype"><?php echo __("Type"); ?></label>
        <?php echo Form::select("type", Model_User_location::types(), null, array("class"=>"ui-widget-content ui-corner-all", "id"=>"lftype"));?>
        <?php echo Form::input("typeother", null, array("class"=>"ui-widget-content ui-corner-all", "id"=>"lftypeother", "style"=>"visibility:hidden;"));?>
    	<fieldset>
            <?php echo Form::select("break", array("yes"=>__("Add a break"), "no"=>__("Change new location")), false, array("class"=>"ui-widget-content ui-corner-all", "id"=>"lfbreak"));?>
	        <br>
	        <label for="lfdatefrom"><?php echo __("from"); ?></label>
        	<?php echo Form::input("date_from", null, array("class"=>"ui-widget-content ui-corner-all", "id"=>"lfdatefrom"));?>
	        <br>
   	        <label for="lfdateto" class="lfyesbreak"><?php echo __("to"); ?></label>
        	<?php echo Form::input("date_to", null, array("class"=>"ui-widget-content ui-corner-all lfyesbreak", "id"=>"lfdateto"));?>
        	</fieldset>
    </fieldset>
    </form>
</div>
<script type="text/javascript">
var subtypes = {
<?php
	$cct = count(Model_User_contact::$types);
	$ct = 0;
	foreach(array_keys(Model_User_contact::$typeSubtypes) as $t): $ct++; $s = Model_User_contact::typeSubtypes($t); ?><?php echo $t;?>: {<?php $cst = 0; foreach($s as $k=>$v): $cst++;?><?php echo $k;?>: '<?php echo $v;?>'<?php if($cst<count($s)) echo ",";?><?php endforeach;?>}<?php if($ct<$cct) echo ",";?><?php endforeach;?>
};
var confirm_delete_msg = '<?php echo __("Are you sure to delete %s?")?>';
var locations = <?php echo json_encode($locations);	?>;

$(document).ready(function(){
	$('input.editable_datepicker').datepicker({
		dateFormat: 'M. dd, yy',
		changeMonth: true,
        changeYear: true,
        minDate: "-90y",
        maxDate: "-5y",
        yearRange: "c-90:c+90"
    });

	$('#location-dialog-form #lfdatefrom, #location-dialog-form #lfdateto').datepicker({
		dateFormat: 'M. dd, yy',
		changeMonth: true,
		minDate: '0'
    }).css('width', '150px');

	$('#location-dialog-form #lftype').on('change', function(){
		if($(this).val()==<?php echo Model_User_location::TYPE_OTHER; ?>) {
			$('#location-dialog-form #lftypeother').css('visibility', 'visible').effect("highlight");
		} else {
			$('#location-dialog-form #lftypeother').css('visibility', 'hidden');
		}
	});
	$('#location-dialog-form #lfbreak').on('change', function(){
		$('#location-dialog-form input.lfyesbreak').val('');
		if($(this).val()=="no") {
			$('#location-dialog-form .lfyesbreak').hide();
		} else {
			$('#location-dialog-form .lfyesbreak').show().effect("highlight");
		}

	});
    $('.tooltip').tooltip({
		track: true,
	    delay: 0,
	    showURL: false,
	    opacity: 0.8,
	    fixPNG: true,
	    showBody: " - ",
	    extraClass: "pretty fancy",
	    top: -15,
	    left: 5
	});

	$('#contact-dialog-form').dialog({
	    resizable: false,
	    modal: true,
	    autoOpen: false,
	    width: 350,
	    buttons: {
	        "<?php echo __("Save"); ?>" : function () {
				var t = $('#newtype').val();
				var st = $('#newsubtype').val();
				var v = $('#newvalue').val();
				if(t==0 || st==0 || v.length==0) {
					alert('All fields must be filled');
					return false;
				}
				$.post('<?php echo Kohana::$base_url ?>profile/update/contacts_add',
					{ 'type':t, 'subtype':st, 'value':v },
					function(data) {
						var data = jQuery.parseJSON(data);
						if(data.error) showError(data.error);
						else {
					        $('#contact-dialog-form').dialog("close");
							cleanEdit();
							$.blockUI(bui);
							window.location.reload(true);
						}
					}
				);
		        $(this).dialog("close");
	        },
	        "<?php echo __("Cancel"); ?>" : function() {
	            $(this).dialog("close");
			}
	    }
	});

	$('#location-dialog-form').dialog({
	    resizable: false,
	    modal: true,
	    autoOpen: false,
	    width: 500,
	    buttons: {
	        "<?php echo __("Save"); ?>" : function () {
		        $('#location-dialog-form form').ajaxSubmit({
			        success: function(data){
						data = $.parseJSON(data);
						if(data.error) {
							alert(data.error);
							return;
						}
			        	$('#location-dialog-form').dialog("close");
			        	$.blockUI(bui);
			        	window.location.reload(true);
					}
			    });
	        },
	        "<?php echo __("Cancel"); ?>" : function() {
	            $(this).dialog("close");
			}
	    }
	});
});
function cleanEdit() {
	$('#cont ul.edit a.options.edit').each(function(i,el){
		var p = $(this).parent();
		p.find("div:first").css("display", "none");
		p.find('a.options').css("display", "");
	});
}
function makeEditable(el) {
	var p = $(el).parent();
	p.find("div:first").css("display", "block");
	p.find('a.options').css("display", "none");
	return false;
}
function addNewContactRecord() {
	$('#newtype').val(0);
	$('#newsubtype option').remove();
	$('#newvalue').val('');
	$('#contact-dialog-form').dialog('open');
	return false;
}

$('#newtype').on('change', function(){
	var type =  $(this).val();
	if(type == 0) alert('<?php echo __("Please select contact type")?>');
	else {
		var el = $('#newsubtype');
		el.find("option").remove();
		for(var i in subtypes[type]) {
			el.append('<option value="'+i+'">'+subtypes[type][i]+'</option>');
		};
	}
});
$('#cont ul.edit a.options.edit').on('click', function(){
	cleanEdit();
	makeEditable(this);
	return false;
});
$('input.edit_field_button').on("click", function(){
	var el = $(this).parent().find('.edit_field');
	var name = el.attr('name');
	var val = el.val();
	var url = '<?= Kohana::$base_url ?>profile/update';
	if(name.match(/^contacts/)) url += '/contacts';
	if(name.match(/^address/)) url += '/address';
		$.post(
		url,
		{ name: name, value: val },
		function(data) {
			var data = jQuery.parseJSON(data);
			if(data.error) showError(data.error);
			else {
				var p = $('#'+data.name).parent().parent().parent();
				$('#'+data.name).val(data.value);
				p.find('li.value').html(data.displayValue);
				cleanEdit();
			}
		}
	);
});
$('#cont ul.edit a.options.delete').on('click', function(){
	var el = $(this).parent().find('.edit_field');
	var p = el.parent().parent().parent();
	var name = el.attr('name');
	var dname = p.find('li.first').text()+' '+p.find('li.value').text();
	var val = el.val();
	var url = '<?= Kohana::$base_url ?>profile/update/remove';
	if(name.match(/^contacts/)) url += '_contacts';
	else if(name.match(/^address/)) url += '_address';

	if(confirm(confirm_delete_msg.replace("%s", dname))) {
		$.post(
			url,
			{ name: name, value: val },
			function(data) {
				var data = jQuery.parseJSON(data);
				if(data.error) showError(data.error);
				else {
					$('#'+data.name).parent().parent().parent().remove();
					cleanEdit();
					if(data.name.match(/^contacts/)) {
						$("div.contactsmd ul:odd").removeClass("light").addClass("dark");
						$("div.contactsmd ul:even").removeClass("dark").addClass("light");
					}
				}
			}
		);
	}
	return false;
});
$('.add_button').on('click', function() {
	$('#location-dialog-form input[name=lid]').val('');
	$('#location-dialog-form select[name=country]').val('');
	$('#location-dialog-form input[name=city]').val('');
	$('#location-dialog-form select[name=type]').val('').change();
	$('#location-dialog-form input[name=typeother]').val('');
	$('#location-dialog-form input[name=break]').val('yes').change();
	$('#location-dialog-form input[name=date_from]').val('');

	$('#location-dialog-form').dialog('open');
	return false;
});
$('.cblocks .choose_btn').on('click', function() {
	var lid = $(this).attr("class").replace(/^.*lid([0-9]+).*$/, '$1');
	var date_from = new Date(locations[lid].date_from*1000);
	var date_to = null;
	if(locations[lid].date_to) date_to = new Date(locations[lid].date_to*1000);
	$('#location-dialog-form input[name=lid]').val(lid);
	$('#location-dialog-form select[name=country]').val(locations[lid]['country']['id']);
	$('#location-dialog-form input[name=city]').val(locations[lid]['city']);
	$('#location-dialog-form select[name=type]').val(locations[lid].type.id).change();
	$('#location-dialog-form input[name=typeother]').val(locations[lid]['typeother']);
	$('#location-dialog-form select[name=break]').val(date_to?"yes":"no").change();
	$('#location-dialog-form input[name=date_from]').val(date_from.toDateString().replace(/^.+ (.+) (.+) (.+)$/, "$1. $2, $3"));
	if(date_to) $('#location-dialog-form input[name=date_to]').val(date_to.toDateString().replace(/^.+ (.+) (.+) (.+)$/, "$1. $2, $3"));

	$('#location-dialog-form').dialog('open');
	return false;
});

$('.cblock_h a.close').on('click', function(){
	var lid = $(this).attr("class").replace(/^.*lid([0-9]+).*$/, '$1');
	if(confirm(confirm_delete_msg.replace('%s', locations[lid].country.name+", "+locations[lid].city))) {
		$.get($(this).attr('href'), function(data){
			data = $.parseJSON(data);
			if(data.error) {
				alert(data.error);
				return;
			}
	    	$.blockUI(bui);
	    	window.location.reload(true);
		});
	}
	return false;
});
</script>