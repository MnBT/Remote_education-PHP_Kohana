<?php
function buildVerifiedString($field) {
   if(!$field) return "&mdash;";
   $v = !empty($field["verified"]);
   $str = __($v?"Verified":"Updated")." ";
   $str .= date("M. d, Y", $field[$v?"verified":"date"]);
   $str .= " by ";
   $str .= $field[$v?"moder":"editor"]["username"];
   return $str;
}
?>
<div id="tab_profile_middle">
   <div id="tab_profile_container">
      <div id="tab_profile_cont">
      <!-- main info -->
      <h1 class="tab_profile_name"><?php echo $profile["first_name"]." ".$profile["middle_name"]." ".$profile["last_name"];?></h1>
      <div class="tab_profile_main_info">
         <ul class="reset dark edit">
            <li class="first">Birth Date:</li>
            <li class="value"><?php if($profile["birthday"]): echo date("M. d, Y", $profile["birthday"]); ?><span class="age"><?php echo $profile["age"]; ?></span><?php else: ?>&mdash;<?php endif ?></li>
            <li class="verify options"><?php echo buildVerifiedString(Arr::get($history, "birthday")); ?></li>
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
            <li class="verify options"><?php echo buildVerifiedString(Arr::get($history, "sex")); ?></li>
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
         <ul class="reset light admin">
            <li class="first">IP:</li>
            <li><?php echo $_SERVER["REMOTE_ADDR"]; ?></li>
         </ul>
         <ul class="reset dark admin">
            <li class="first">IP Location:</li>
            <li><?php if($location) { echo $location->countryName.($location->region?", ".$location->region.($location->city?", ".$location->city:""):""); } else echo __("Unknown location"); ?></li>
         </ul>
         <ul class="reset light">
            <li class="first">Current Time:</li>
            <li><?php echo __($date->format("l")).", ".__($date->format("F"))." ".$date->format("d, Y H:i:s"); ?></li>
         </ul>
         <ul class="reset dark admin edit edittimezone">
            <li class="first">Time Zone:</li>
            <li><?php echo $profile["tz"]; ?> ( <?php echo round($date->getOffset()/3600,1)." ".__("hours"); ?> )</li>
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
            <li class="verify options"><?php echo @buildVerifiedString($history["citizenship"]); ?></li>
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
            <li class="verify options"><?php echo @buildVerifiedString($history["residence"]); ?></li>
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
         <div class="verify rcol"><?php echo buildVerifiedString(Arr::get($history, "contacts")); ?></div>
      </div>
      <div class="block_bg contactsmd">
         <?php 
         $colors = array("dark", "light"); $cv = 0;
         $first = true;
         foreach($contacts as $name => $type):
            foreach ($type as $contact):
              $cv = 1 - $cv;
          ?>
               <ul class="reset <?php echo $colors[$cv];?> edit editoptions_<?php echo $contact["id"]?>">
                  <?php if ($name == "E-Mail"): ?>
                    <?php if ($first): ?>
                      <li class="first">Primary <?php echo __($contact["type"]["name"]); ?></li>
                      <?php $first = false; ?>
                    <?php else:?>
                      <li class="first"><?php echo __($contact["type"]["name"]); ?></li>
                    <?php endif;?>
                  <?php else: ?>
                    <li class="first">
                      <?php echo __($contact["subtype"]["name"]). ' '. __($contact["type"]["name"]); ?>
                    </li>                    
                  <?php endif;?>
                  <li class="value"><?php echo $contact["value"]; ?></li>
                  <li class="options_li">
                     <div class="lcol" style="display:none;">
                        <?php echo Form::input("contacts[".$contact["id"]."]", $contact["value"], array("class"=>"edit_field lcol tooltip", "id"=>"contacts_".$contact["id"], "title"=>__("Input phone number format:<br><u>+NN (NNN) NNN-NNNN</u>"))); ?>
                        <input type="button" class="edit_field_button lcol">
                     </div>
                     <a href="#" class="options edit lcol"></a>
                     <a href="#" class="options delete lcol"></a>
                  </li>
               </ul>
         <?php 
            endforeach;
         endforeach;
         ?>
         <ul class="reset <?php echo $colors[1-$cv];?>">
            <li class="first"><a href="#" onclick="return addNewContactRecord();"><?php echo __("Add new record");?></a></li>
         </ul>
      </div>
      <div class="block_bottom"></div>

      <!-- Permanent Adress -->
      <?php
      $addr_type = Model_User_address::TYPE_PERMANENT;
      $addr = Arr::get($address, $addr_type);
      ?>                                <div class="block_h"><h2 class="lcol">Permanent Address</h2><div class="verify rcol"><?php echo buildVerifiedString(Arr::get($history, "permanent_address")); ?></div></div>
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

      <!-- Current Address -->
      <?php
      $addr_type = Model_User_address::TYPE_CURRENT;
      $addr = Arr::get($address, $addr_type);
      ?>                                <div class="block_h"><h2 class="lcol">Current Address</h2><div class="verify rcol"><?php echo buildVerifiedString(Arr::get($history, "current_address")); ?></div></div>
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
      <!-------------------------------------------- CURRENT ADDRESS END-------------------------------------------->
      <!-------------------------------------------- GENERAL INFO START---------------------------------------->
      <div class="block_h">
         <h2 class="lcol">General Info</h2>
      </div>
      <div class="block_bg">
         <ul class="reset dark edit">
            <li class="first">Major:</li>
            <li class="value"><?= (!empty($general_info["major"])) ? $majors[$general_info["major"]] : ""; ?></li>
            <li class="options_li">
               <div class="lcol" style="display:none;">
                  <?= Form::select("general_info[major]", array(0=>"&nbsp;")+$majors, $general_info["major"], array("class" => "edit_field lcol", "id" => "general_info_major")); ?>
                  <input type="button" class="edit_field_button lcol">
               </div>
               <a href="#" class="options edit lcol"></a>
            </li>
         </ul>
         <ul class="reset light edit">
            <li class="first">Language of Study:</li>
            <li class="value"><?= __($general_info["study_language"] ? $study_languages[$general_info["study_language"]] : ""); ?></li>
            <li class="options_li">
               <div class="lcol" style="display:none;">
                  <?= Form::select("general_info[study_language]", array(0=>"&nbsp;")+$study_languages, $general_info["study_language"], array("class" => "edit_field lcol", "id" => "general_info_study_language")); ?>
                  <input type="button" class="edit_field_button lcol">
               </div>
               <a href="#" class="options edit lcol"></a>
            </li>
         </ul>
         <ul class="reset dark edit">
            <li class="first">Application Status:</li>
            <li class="value"><?= (!empty($general_info["application_status"])) ? $application_statuses[$general_info["application_status"]] : ""; ?></li>
            <li class="options_li">
               <div class="lcol" style="display:none;">
                  <?= Form::select("general_info[application_status]", array(0=>"&nbsp;")+$application_statuses, $general_info["application_status"], array("class" => "edit_field lcol", "id" => "general_info_application_status")); ?>
                  <input type="button" class="edit_field_button lcol">
               </div>
               <a href="#" class="options edit lcol"></a>
            </li>
         </ul>
         <ul class="reset light edit">
            <li class="first">Admission Date:</li>
            <li class="value"><?= $general_info["admission_date"]; ?></li>
            <li class="options_li">
               <div class="lcol" style="display:none;">
                  <?= Form::input("general_info[admission_date]", $general_info["admission_date"], array("class" => "edit_field lcol editable_datepicker", "id" => "general_info_admission_date")); ?>
                  <input type="button" class="edit_field_button lcol">
               </div>
               <a href="#" class="options edit lcol"></a>
            </li>
         </ul>
         <ul class="reset dark edit">
            <li class="first">Admission Type:</li>
            <li class="value"><?= (!empty($general_info["admission_type"])) ? $admission_types[$general_info["admission_type"]] : ""; ?></li>
            <li class="options_li">
               <div class="lcol" style="display:none;">
                  <?= Form::select("general_info[admission_type]", array(0=>"&nbsp;")+$admission_types, $general_info["admission_type"], array("class" => "edit_field lcol", "id" => "general_info_admission_type")); ?>
                  <input type="button" class="edit_field_button lcol">
               </div>
               <a href="#" class="options edit lcol"></a>
            </li>
         </ul>
         <ul class="reset light edit">
            <li class="first">Admissions Officer:</li>
            <li class="value"><?= ($general_info["admission_officer"] == "1") ? __("Yes") : __("No") ?></li>
            <li class="options_li">
               <div class="lcol" style="display:none;">
                  <?= Form::select("general_info[admission_officer]", array("0" => __("No"), "1" => __("Yes")), $general_info["admission_officer"], array("class" => "edit_field lcol", "id" => "general_info_admission_officer")); ?>
                  <input type="button" class="edit_field_button lcol">
               </div>
               <a href="#" class="options edit lcol"></a>
            </li>
         </ul>
         <ul class="reset dark edit">
            <li class="first">Admission Partner:</li>
            <li class="value"><?= $general_info["partner"]; ?></li>
            <li class="options_li">
               <div class="lcol" style="display:none;">
                  <?= Form::input("general_info[partner]", $general_info["partner"], array("class" => "edit_field lcol", "id" => "general_info_partner")); ?>
                  <input type="button" class="edit_field_button lcol">
               </div>
               <a href="#" class="options edit lcol"></a>
            </li>
         </ul>
         <ul class="reset light edit">
            <li class="first">Personal Consultant:</li>
            <li class="value"><?= $general_info["personal_consultant"] ?></li>
            <li class="options_li">
               <div class="lcol" style="display:none;">
                  <?= Form::input("general_info[personal_consultant]", $general_info["personal_consultant"], array("class" => "edit_field lcol", "id" => "general_info_personal_consultant")); ?>
                  <input type="button" class="edit_field_button lcol">
               </div>
               <a href="#" class="options edit lcol"></a>
            </li>
         </ul>
         <ul class="reset dark edit">
            <li class="first">Promotional Program:</li>
            <li class="value"><?= $general_info["promotional_program"]; ?></li>
            <li class="options_li">
               <div class="lcol" style="display:none;">
                  <?= Form::input("general_info[promotional_program]", $general_info["promotional_program"], array("class" => "edit_field lcol", "id" => "general_info_promotional_program")); ?>
                  <input type="button" class="edit_field_button lcol">
               </div>
               <a href="#" class="options edit lcol"></a>
            </li>
         </ul>
      </div>
      <div class="block_bottom"></div>
      <!-------------------------------------------- GENERAL INFO END------------------------------------------>
      <!-------------------------------------------- EMERGENCY CONTACTS START---------------------------------------->
      <div class="block_h">
         <h2 class="lcol">Emergency Contacts</h2>
      </div>
      <div class="block_bg">

         <?php
         $colors = array("dark", "light");
         $cv = 1;

         foreach($emergency_contacts as $emergency_contact) {
            $cv = 1 - $cv;
            ?>

            <ul class="reset <?= $colors[$cv]?> edit">
               <li class="first"><b><?= __($emergency_contact["type"])?>:</b></li>
               <li class="value"><?= $emergency_contact["name"]; ?></li>
               <li class="options_li">
                  <div class="lcol" style="display:none;">
                     <?= Form::input("emergency_contacts[".$emergency_contact["id"]."][name]", $emergency_contact["name"], array("class" => "edit_field lcol emergency_contacts_".$emergency_contact["id"], "id" => "emergency_contacts_".$emergency_contact["id"]."_name")); ?>
                     <input type="button" class="edit_field_button lcol">
                  </div>
                  <a href="#" class="options edit lcol"></a>
                  <a href="#" class="options delete lcol"></a>
               </li>
            </ul>
            <?php $cv = 1 - $cv; ?>
            <ul class="reset <?= $colors[$cv]?> edit">
               <li class="first">Phone:</li>
               <li class="value"><?= $emergency_contact["phone"]; ?></li>
               <li class="options_li">
                  <div class="lcol" style="display:none;">
                     <?= Form::input("emergency_contacts[".$emergency_contact["id"]."][phone]", $emergency_contact["phone"], array("class" => "edit_field lcol emergency_contacts_".$emergency_contact["id"], "id" => "emergency_contacts_".$emergency_contact["id"]."_phone")); ?>
                     <input type="button" class="edit_field_button lcol">
                  </div>
                  <a href="#" class="options edit lcol"></a>
                  <a href="#" class="options delete lcol"></a>
               </li>
            </ul>
            <?php $cv = 1 - $cv; ?>
            <ul class="reset <?= $colors[$cv]?> edit">
               <li class="first">Email:</li>
               <li class="value"><?= $emergency_contact["email"]; ?></li>
               <li class="options_li">
                  <div class="lcol" style="display:none;">
                     <?= Form::input("emergency_contacts[".$emergency_contact["id"]."][email]", $emergency_contact["email"], array("class" => "edit_field lcol emergency_contacts_".$emergency_contact["id"], "id" => "emergency_contacts_".$emergency_contact["id"]."_email")); ?>
                     <input type="button" class="edit_field_button lcol">
                  </div>
                  <a href="#" class="options edit lcol"></a>
                  <a href="#" class="options delete lcol"></a>
               </li>
            </ul>


            <?php }
         $cv = 1 - $cv;
         ?>
         <ul class="reset <?= $colors[$cv]?>">
            <li class="first"><a href="#" onclick="return addNewEmergencyContactRecord();"><?= __("Add new emergency contact");?></a></li>
         </ul>
      </div>
      <div class="block_bottom"></div>
      <!-------------------------------------------- EMERGENCY CONTACTS END------------------------------------------>
      <!-------------------------------------------- EDUCATION START---------------------------------------->
      <div class="block_h">
         <h2 class="lcol">Education</h2>
      </div>
      <div class="block_bg">
         <ul class="reset dark edit">
            <li class="first">Native English:</li>
            <li class="value"><?= __(($education["native_english"]==1) ? "Yes": "No"); ?></li>
            <li class="options_li">
               <div class="lcol" style="display:none;">
                  <?= Form::select("education[native_english]", array(3 => "&nbsp;", 0 => __("No"), 1 => __("Yes")), $education["native_english"], array("class" => "edit_field lcol", "id" => "education_native_english")); ?>
                  <input type="button" class="edit_field_button lcol">
               </div>
               <a href="#" class="options edit lcol"></a>
            </li>
         </ul>
         <ul class="reset light edit">
            <li class="first">Native Language:</li>
            <li class="value"><?= $education["native_language"]; ?></li>
            <li class="options_li">
               <div class="lcol" style="display:none;">
                  <?= Form::input("education[native_language]", $education["native_language"], array("class" => "edit_field lcol", "id" => "education_native_language")); ?>
                  <input type="button" class="edit_field_button lcol">
               </div>
               <a href="#" class="options edit lcol"></a>
            </li>
         </ul>
         <ul class="reset dark edit">
            <li class="first">TOEFL/IELTS:</li>
            <li class="value"><?= $education["toefl_ielts"]; ?></li>
            <li class="options_li">
               <div class="lcol" style="display:none;">
                  <?= Form::input("education[toefl_ielts]", $education["toefl_ielts"], array("class" => "edit_field lcol", "id" => "education_toefl_ielts")); ?>
                  <input type="button" class="edit_field_button lcol">
               </div>
               <a href="#" class="options edit lcol"></a>
            </li>
         </ul>
         <ul class="reset light edit">
            <li class="first">Current student:</li>
            <li class="value"><?= __(($education["currently_student"] == 1) ? "Yes" : "No"); ?></li>
            <li class="options_li">
               <div class="lcol" style="display:none;">
                  <?= Form::select("education[currently_student]", array(0 => __("No"), 1 => __("Yes")), $education["currently_student"], array("class" => "edit_field lcol", "id" => "education_currently_student")); ?>
                  <input type="button" class="edit_field_button lcol">
               </div>
               <a href="#" class="options edit lcol"></a>
            </li>
         </ul>

         <?php
         $colors = array("dark", "light");
         $cv = 1;
         foreach($education["records"] as $record) {

            $cv = 1 - $cv;
            ?>

            <ul class="reset <?= $colors[$cv]?> edit">
               <li class="first"><b>Name of Institution:</b></li>
               <li class="value"><?= $record["name"]; ?></li>
               <li class="options_li">
                  <div class="lcol" style="display:none;">
                     <?= Form::input("education[".$record["id"]."][name]", $record["name"], array("class" => "edit_field lcol education_".$record["id"], "id" => "education_".$record["id"]."_name")); ?>
                     <input type="button" class="edit_field_button lcol">
                  </div>
                  <a href="#" class="options edit lcol"></a>
                  <a href="#" class="options delete lcol"></a>
               </li>
            </ul>
            <?php $cv = 1-$cv; ?>
            <ul class="reset <?= $colors[$cv]?> edit">
               <li class="first">Location:</li>
               <li class="value"><?= $record["location"]; ?></li>
               <li class="options_li">
                  <div class="lcol" style="display:none;">
                     <?= Form::input("education[".$record["id"]."][location]", $record["location"], array("class" => "edit_field lcol education_".$record["id"], "id" => "education_".$record["id"]."_location")); ?>
                     <input type="button" class="edit_field_button lcol">
                  </div>
                  <a href="#" class="options edit lcol"></a>
                  <a href="#" class="options delete lcol"></a>
               </li>
            </ul>
            <?php $cv = 1-$cv; ?>
            <ul class="reset <?= $colors[$cv]?> edit">
               <li class="first">Entered, Through:</li>
               <li class="value"><?= $record["date_entered"]; ?></li>
               <li class="options_li">
                  <div class="lcol" style="display:none;">
                     <?= Form::input("education[".$record["id"]."][date_entered]", $record["date_entered"], array("class" => "edit_field lcol editable_datepicker education_".$record["id"], "id" => "education_".$record["id"]."_date_entered")); ?>
                     <input type="button" class="edit_field_button lcol">
                  </div>
                  <a href="#" class="options edit lcol"></a>
                  <a href="#" class="options delete lcol"></a>
               </li>
            </ul>
            <?php $cv = 1-$cv; ?>
            <ul class="reset <?= $colors[$cv]?> edit">
               <li class="first">Major of study:</li>
               <li class="value"><?= $record["major"]; ?></li>
               <li class="options_li">
                  <div class="lcol" style="display:none;">
                     <?= Form::input("education[".$record["id"]."][major]", $record["major"], array("class" => "edit_field lcol education_".$record["id"], "id" => "education_".$record["id"]."_major")); ?>
                     <input type="button" class="edit_field_button lcol">
                  </div>
                  <a href="#" class="options edit lcol"></a>
                  <a href="#" class="options delete lcol"></a>
               </li>
            </ul>
            <?php $cv = 1-$cv; ?>
            <ul class="reset <?= $colors[$cv]?> edit">
               <li class="first">Degree expected:</li>
               <li class="value"><?= $record["degree_expected"]; ?></li>
               <li class="options_li">
                  <div class="lcol" style="display:none;">
                     <?= Form::input("education[".$record["id"]."][degree_expected]", $record["degree_expected"], array("class" => "edit_field lcol education_".$record["id"], "id" => "education_".$record["id"]."_degree_expected")); ?>
                     <input type="button" class="edit_field_button lcol">
                  </div>
                  <a href="#" class="options edit lcol"></a>
                  <a href="#" class="options delete lcol"></a>
               </li>
            </ul>
            <?php $cv = 1-$cv; ?>
            <ul class="reset <?= $colors[$cv]?> edit">
               <li class="first">Date expected:</li>
               <li class="value"><?= $record["date_expected"]; ?></li>
               <li class="options_li">
                  <div class="lcol" style="display:none;">
                     <?= Form::input("education[".$record["id"]."][date_expected]", $record["date_expected"], array("class" => "edit_field lcol editable_datepicker education_".$record["id"], "id" => "education_".$record["id"]."_date_expected")); ?>
                     <input type="button" class="edit_field_button lcol">
                  </div>
                  <a href="#" class="options edit lcol"></a>
                  <a href="#" class="options delete lcol"></a>
               </li>
            </ul>
            <?php $cv = 1-$cv; ?>
            <ul class="reset <?= $colors[$cv]?> edit">
               <li class="first">Previous degree:</li>
               <li class="value"><?= $record["prev_degree"]; ?></li>
               <li class="options_li">
                  <div class="lcol" style="display:none;">
                     <?= Form::input("education[".$record["id"]."][prev_degree]", $record["prev_degree"], array("class" => "edit_field lcol education_".$record["id"], "id" => "education_".$record["id"]."_prev_degree")); ?>
                     <input type="button" class="edit_field_button lcol">
                  </div>
                  <a href="#" class="options edit lcol"></a>
                  <a href="#" class="options delete lcol"></a>
               </li>
            </ul>

            <?php }
         $cv = 1-$cv;
         ?>
         <ul class="reset <?= $colors[$cv]?>">
            <li class="first"><a href="#" onclick="return addNewEducationRecord();"><?= __("Add new record");?></a></li>
         </ul>
      </div>
      <div class="block_bottom"></div>
      <!-------------------------------------------- EDUCATION END------------------------------------------>
      <!-------------------------------------------- WORK EXPERIENCE START---------------------------------------->
      <div class="block_h">
         <h2 class="lcol">Work Experience</h2>
      </div>
      <div class="block_bg">

         <?php foreach($work_experience as $record) { ?>

         <ul class="reset dark edit">
            <li class="first"><b>Employer Name:</b></li>
            <li class="value"><?= $record["employer_name"]; ?></li>
            <li class="options_li">
               <div class="lcol" style="display:none;">
                  <?= Form::input("work_experience[".$record["id"]."][employer_name]", $record["employer_name"], array("class" => "edit_field lcol work_experience_".$record["id"], "id" => "work_experience_".$record["id"]."_employer_name")); ?>
                  <input type="button" class="edit_field_button lcol">
               </div>
               <a href="#" class="options edit lcol"></a>
               <a href="#" class="options delete lcol"></a>
            </li>
         </ul>
         <ul class="reset light edit">
            <li class="first">Currently Employed:</li>
            <li class="value"><?= __(($record["current_status"] == 1) ? "Yes" : "No"); ?></li>
            <li class="options_li">
               <div class="lcol" style="display:none;">
                  <?= Form::select("work_experience[".$record["id"]."][current_status]", array(0=>__("No"), 1=>__("Yes")), $record["current_status"], array("class" => "edit_field lcol work_experience_".$record["id"], "id" => "work_experience_".$record["id"]."_current_status")); ?>
                  <input type="button" class="edit_field_button lcol">
               </div>
               <a href="#" class="options edit lcol"></a>
               <a href="#" class="options delete lcol"></a>
            </li>
         </ul>
         <ul class="reset dark edit">
            <li class="first">Job Title:</li>
            <li class="value"><?= $record["job_title"]; ?></li>
            <li class="options_li">
               <div class="lcol" style="display:none;">
                  <?= Form::input("work_experience[".$record["id"]."][job_title]", $record["job_title"], array("class" => "edit_field lcol work_experience_".$record["id"], "id" => "work_experience_".$record["id"]."_job_title")); ?>
                  <input type="button" class="edit_field_button lcol">
               </div>
               <a href="#" class="options edit lcol"></a>
               <a href="#" class="options delete lcol"></a>
            </li>
         </ul>
         <ul class="reset light edit">
            <li class="first">Employed from:</li>
            <li class="value"><?= $record["employed_from"]; ?></li>
            <li class="options_li">
               <div class="lcol" style="display:none;">
                  <?= Form::input("work_experience[".$record["id"]."][employed_from]", $record["employed_from"], array("class" => "edit_field lcol editable_datepicker work_experience_".$record["id"], "id" => "work_experience_".$record["id"]."_employed_from")); ?>
                  <input type="button" class="edit_field_button lcol">
               </div>
               <a href="#" class="options edit lcol"></a>
               <a href="#" class="options delete lcol"></a>
            </li>
         </ul>
         <ul class="reset dark edit">
            <li class="first">Employed to:</li>
            <li class="value"><?= $record["employed_to"]; ?></li>
            <li class="options_li">
               <div class="lcol" style="display:none;">
                  <?= Form::input("work_experience[".$record["id"]."][employed_to]", $record["employed_to"], array("class" => "edit_field lcol editable_datepicker work_experience_".$record["id"], "id" => "work_experience_".$record["id"]."_employed_to")); ?>
                  <input type="button" class="edit_field_button lcol">
               </div>
               <a href="#" class="options edit lcol"></a>
               <a href="#" class="options delete lcol"></a>
            </li>
         </ul>
         <ul class="reset light edit">
            <li class="first">City:</li>
            <li class="value"><?= $record["city"]; ?></li>
            <li class="options_li">
               <div class="lcol" style="display:none;">
                  <?= Form::input("work_experience[".$record["id"]."][city]", $record["city"], array("class" => "edit_field lcol work_experience_".$record["id"], "id" => "work_experience_".$record["id"]."_city")); ?>
                  <input type="button" class="edit_field_button lcol">
               </div>
               <a href="#" class="options edit lcol"></a>
               <a href="#" class="options delete lcol"></a>
            </li>
         </ul>
         <ul class="reset dark edit">
            <li class="first">Country:</li>
            <li class="value"><?= (!empty($record["country"])) ? __($country[$record["country"]]) : ""; ?></li>
            <li class="options_li">
               <div class="lcol" style="display:none;">
                  <?= Form::select("work_experience[".$record["id"]."][country]", array(0=>"&nbsp;")+$country, $record["country"], array("class" => "edit_field lcol work_experience_".$record["id"], "id" => "work_experience_".$record["id"]."_country")); ?>
                  <input type="button" class="edit_field_button lcol">
               </div>
               <a href="#" class="options edit lcol"></a>
               <a href="#" class="options delete lcol"></a>
            </li>
         </ul>
         <ul class="reset light edit">
            <li class="first">Job description:</li>
            <li class="value"><?= $record["job_description"]; ?></li>
            <li class="options_li">
               <div class="lcol" style="display:none;">
                  <?= Form::input("work_experience[".$record["id"]."][job_description]", $record["job_description"], array("class" => "edit_field lcol work_experience_".$record["id"], "id" => "work_experience_".$record["id"]."_job_description")); ?>
                  <input type="button" class="edit_field_button lcol">
               </div>
               <a href="#" class="options edit lcol"></a>
               <a href="#" class="options delete lcol"></a>
            </li>
         </ul>

         <?php } ?>

         <ul class="reset dark">
            <li class="first"><a href="#" onclick="return addNewWorkExperienceRecord();"><?= __("Add new record");?></a></li>
         </ul>
      </div>
      <div class="block_bottom"></div>
      <!-------------------------------------------- WORK EXPERIENCE END------------------------------------------>

   </div><!-- #content-->
</div><!-- #tab_profile_container-->

<div class="sidebar" id="sideLeft">
   <!-- photo -->
   <div class="avatar">
      <img src="<?php echo (is_array($userpic)) ? $userpic[Model_User_photo::SIZE_BIG] : $userpic; ?>" alt="" />
   </div>
   <a href="../profile/photos" class="change_button" title="Change picture"></a>
</div><!-- .sidebar#sideLeft -->

</div><!-- #tab_profile_middle-->
<br class="cleandivider" />
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
<div id="emergency-contact-dialog-form" title="<?= __("Add user emergency contact");?>">
   <p class="validateTips">All form fields are required.</p>

   <form>
      <fieldset>
         <label for="emergency_type"><?= __("Type"); ?></label>
         <?= Form::select("type", array(0=>"&nbsp;")+$emergency_types, NULL, array("class"=>"ui-widget-content ui-corner-all", "id"=>"emergency_type"));?>
         <br />
         <label for="emergency_name"><?= __("Name"); ?></label>
         <?= Form::input("name", NULL, array("class"=>"ui-widget-content ui-corner-all", "id"=>"emergency_name"));?>
         <br />
         <label for="emergency_phone"><?= __("Phone"); ?></label>
         <?= Form::input("phone", NULL, array("class"=>"ui-widget-content ui-corner-all", "id"=>"emergency_phone"));?>
         <br />
         <label for="emergency_email"><?= __("Email"); ?></label>
         <?= Form::input("email", NULL, array("class"=>"ui-widget-content ui-corner-all", "id"=>"emergency_email"));?>
      </fieldset>
   </form>
</div>
<div id="education-dialog-form" title="<?= __("Add education record");?>">
   <p class="validateTips">All form fields are required.</p>

   <form>
      <fieldset>
         <label for="education_name"><?= __("Name of Institution"); ?></label>
         <?= Form::input("name", NULL, array("class"=>"ui-widget-content ui-corner-all", "id"=>"education_name"));?>
         <br />
         <label for="education_location"><?= __("Location"); ?></label>
         <?= Form::input("location", NULL, array("class"=>"ui-widget-content ui-corner-all", "id"=>"education_location"));?>
         <br />
         <label for="education_date_entered"><?= __("Date entered"); ?></label>
         <?= Form::input("date_entered", NULL, array("class"=>"ui-widget-content ui-corner-all", "id"=>"education_date_entered"));?>
         <br />
         <label for="education_major"><?= __("Major of Study"); ?></label>
         <?= Form::input("major", NULL, array("class"=>"ui-widget-content ui-corner-all", "id"=>"education_major"));?>
         <br />
         <label for="education_degree_expected"><?= __("Degree expected"); ?></label>
         <?= Form::input("degree_expected", NULL, array("class"=>"ui-widget-content ui-corner-all", "id"=>"education_degree_expected"));?>
         <br />
         <label for="education_date_expected"><?= __("Date expected"); ?></label>
         <?= Form::input("date_expected", NULL, array("class"=>"ui-widget-content ui-corner-all", "id"=>"education_date_expected"));?>
         <br />
         <label for="education_prev_degree"><?= __("Previous degree"); ?></label>
         <?= Form::input("prev_degree", NULL, array("class"=>"ui-widget-content ui-corner-all", "id"=>"education_prev_degree"));?>
      </fieldset>
   </form>
</div>
<div id="work-experience-dialog-form" title="<?= __("Add user work experience");?>">
   <p class="validateTips">All form fields are required.</p>

   <form>
      <fieldset>
         <label for="work_experience_employer_name"><?= __("Employer name"); ?></label>
         <?= Form::input("employer_name", NULL, array("class"=>"ui-widget-content ui-corner-all", "id"=>"work_experience_employer_name"));?>
         <br />
         <label for="work_experience_current_status"><?= __("Currently Employed"); ?></label>
         <?= Form::select("current_status", array("0" => __("No"), "1" => __("Yes")), NULL, array("class"=>"ui-widget-content ui-corner-all", "id"=>"work_experience_current_status"));?>
         <br />
         <label for="work_experience_job_title"><?= __("Job Title"); ?></label>
         <?= Form::input("job_title", NULL, array("class"=>"ui-widget-content ui-corner-all", "id"=>"work_experience_job_title"));?>
         <br />
         <label for="work_experience_employed_from"><?= __("Employed from"); ?></label>
         <?= Form::input("employed_from", NULL, array("class"=>"ui-widget-content ui-corner-all", "id"=>"work_experience_employed_from"));?>
         <br />
         <label for="work_experience_employed_to"><?= __("Employed to"); ?></label>
         <?= Form::input("employed_to", NULL, array("class"=>"ui-widget-content ui-corner-all", "id"=>"work_experience_employed_to"));?>
         <br />
         <label for="work_experience_city"><?= __("City"); ?></label>
         <?= Form::input("city", NULL, array("class"=>"ui-widget-content ui-corner-all", "id"=>"work_experience_city"));?>
         <br />
         <label for="work_experience_country"><?= __("country"); ?></label>
         <?= Form::select("country", array(0=>"&nbsp;")+$country, NULL, array("class"=>"ui-widget-content ui-corner-all", "id"=>"work_experience_country"));?>
         <br />
         <label for="work_experience_job_description"><?= __("Job description"); ?></label>
         <?= Form::input("job_description", NULL, array("class"=>"ui-widget-content ui-corner-all", "id"=>"work_experience_job_description"));?>
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
   $('input.editable_datepicker_birthday').datepicker({
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

   $('#education-dialog-form #education_date_entered,' +
      '#education-dialog-form #education_date_expected,' +
      '#work-experience-dialog-form #work_experience_employed_from,' +
      '#work-experience-dialog-form #work_experience_employed_to,' +
      'input.editable_datepicker').datepicker({
         dateFormat: 'M. dd, yy',
         changeMonth: true,
         changeYear: true,
         minDate: "-90y",
         maxDate: "+10y",
         yearRange: "c-90:c+90"
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
                     studentsScope.activateTab('profile');
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
                  studentsScope.activateTab('profile');
               }
            });
         },
         "<?php echo __("Cancel"); ?>" : function() {
            $(this).dialog("close");
         }
      }
   });
   $('#emergency-contact-dialog-form').dialog({
      resizable: false,
      modal: true,
      autoOpen: false,
      width: 480,
      buttons: {
         "<?= __("Save"); ?>" : function () {
            var type = $('#emergency_type').val();
            var name = $('#emergency_name').val();
            var phone = $('#emergency_phone').val();
            var email = $('#emergency_email').val();
            if(type==0 || name.length==0 || phone.length==0 || email.length==0) {
               alert('All fields must be filled');
               return false;
            }
            $.post('<?= Kohana::$base_url ?>profile/update/emergency_contacts_add',
               { 'type':type, 'name':name, 'phone':phone, 'email':email },
               function(data) {
                  var data = jQuery.parseJSON(data);
                  if(data.error) showError(data.error);
                  else {
                     $('#emergency-contact-dialog-form').dialog("close");
                     cleanEdit();
                     $.blockUI(bui);
                     window.location.reload(true);
                  }
               }
            );
            $(this).dialog("close");
         },
         "<?= __("Cancel"); ?>" : function() {
            $(this).dialog("close");
         }
      }
   });
   $('#education-dialog-form').dialog({
      resizable: false,
      modal: true,
      autoOpen: false,
      width: 640,
      buttons: {
         "<?= __("Save"); ?>" : function () {
            var name = $('#education_name').val();
            var location = $('#education_location').val();
            var date_entered = $('#education_date_entered').val();
            var major = $('#education_major').val();
            var degree_expected = $('#education_degree_expected').val();
            var date_expected = $('#education_date_expected').val();
            var prev_degree = $('#education_prev_degree').val();
            if(name.length==0 || location.length==0 || date_entered.length==0 || major.lenght==0 ||
               degree_expected.length==0 || date_expected.length==0 || prev_degree.length==0) {
               alert('All fields must be filled');
               return false;
            }
            $.post('<?= Kohana::$base_url ?>profile/update/education_add',
               {
                  'name': name,
                  'location': location,
                  'date_entered': date_entered,
                  'major': major,
                  'degree_expected': degree_expected,
                  'date_expected': date_expected,
                  'prev_degree': prev_degree
               },
               function(data) {
                  var data = jQuery.parseJSON(data);
                  if(data.error) showError(data.error);
                  else {
                     $('#education-dialog-form').dialog("close");
                     cleanEdit();
                     $.blockUI(bui);
                     window.location.reload(true);
                  }
               }
            );
            $(this).dialog("close");
         },
         "<?= __("Cancel"); ?>" : function() {
            $(this).dialog("close");
         }
      }
   });
   $('#work-experience-dialog-form').dialog({
      resizable: false,
      modal: true,
      autoOpen: false,
      width: 480,
      buttons: {
         "<?= __("Save"); ?>" : function () {
            var employer_name = $('#work_experience_employer_name').val();
            var current_status = $('#work_experience_current_status').val();
            var job_title = $('#work_experience_job_title').val();
            var employed_from = $('#work_experience_employed_from').val();
            var employed_to = $('#work_experience_employed_to').val();
            var city = $('#work_experience_city').val();
            var country = $('#work_experience_country').val();
            var job_description = $('#work_experience_job_description').val();
            if(employer_name.length==0 || current_status.length==0 || job_title.length==0 || employed_from.length==0 || employed_to.length==0 ||
               city.length==0 || country==0 || job_description.length==0) {
               alert('All fields must be filled');
               return false;
            }
            $.post('<?= Kohana::$base_url ?>profile/update/work_experience_add',
               {
                  'employer_name': employer_name,
                  'current_status': current_status,
                  'job_title': job_title,
                  'employed_from': employed_from,
                  'employed_to': employed_to,
                  'city': city,
                  'country': country,
                  'job_description': job_description
               },
               function(data) {
                  var data = jQuery.parseJSON(data);
                  if(data.error) showError(data.error);
                  else {
                     $('#work-experience-dialog-form').dialog("close");
                     cleanEdit();
                     $.blockUI(bui);
                     window.location.reload(true);
                  }
               }
            );
            $(this).dialog("close");
         },
         "<?= __("Cancel"); ?>" : function() {
            $(this).dialog("close");
         }
      }
   });
});

function cleanEdit() {
   $('#tab_profile_cont ul.edit a.options.edit').each(function(i,el){
      var p = $(this).parent();
      p.find("div:first").css("display", "none");
      p.parent().find('li.verify').css("display", "");
      p.find('a.options').css("display", "");
   });
}
function makeEditable(el) {
   var p = $(el).parent();
   p.find("div:first").css("display", "block");
   p.parent().find('li.verify').css("display", "none");
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

function addNewEmergencyContactRecord() {
   $('#emergency_type').val(0);
   $('#emergency_name').val('');
   $('#emergency_phone').val('');
   $('#emergency_email').val('');
   $('#emergency-contact-dialog-form').dialog('open');
   return false;
}
function addNewEducationRecord() {
   $('#education_name').val('');
   $('#education_location').val('');
   $('#education_date_entered').val('');
   $('#education_major').val(0);
   $('#education_degree_expected').val('');
   $('#education_date_expected').val('');
   $('#education_prev_degree').val('');
   $('#education-dialog-form').dialog('open');
   return false;
}
function addNewWorkExperienceRecord() {
   $('#work_experience_employer_name').val('');
   $('#work_experience_current_status').val(0);
   $('#work_experience_job_title').val('');
   $('#work_experience_employed_from').val('');
   $('#work_experience_employed_to').val('');
   $('#work_experience_city').val('');
   $('#work_experience_country').val(0);
   $('#work_experience_job_descrition').val('');
   $('#work-experience-dialog-form').dialog('open');
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
$('#tab_profile_cont ul.edit a.options.edit').on('click', function(){
   cleanEdit();
   makeEditable(this);
   return false;
});
$('input.edit_field_button').on("click", function(){
   var el = $(this).parent().find('.edit_field');
   var name = el.attr('name');
   var val = el.val();
   var url = '<?= Kohana::$base_url ?>profile/update';
   if(name.match(/^contacts\[/)) url += '/contacts';
   else if(name.match(/^address/)) url += '/address';
   else if(name.match(/^general_info/)) url += '/general_info';
   else if(name.match(/^emergency_contacts/)) url += '/emergency_contacts';
   else if(name.match(/^education/)) url += '/education';
   else if(name.match(/^work_experience/)) url += '/work_experience';
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
            var el = p.find('.verify');
            if(el.length==0) el = p.parent().prev('.block_h').find('.verify');
            el.html('<?php echo __("Updated"); ?> '+data.verified.date+' by '+data.verified.user);
            cleanEdit();
         }
      }
   );
});
$('#tab_profile_cont ul.edit a.options.delete').on('click', function(){
   var el = $(this).parent().find('.edit_field');
   var p = el.parent().parent().parent();
   var name = el.attr('name');
   var dname = p.find('li.first').text()+' '+p.find('li.value').text();
   var val = el.val();
   var url = '<?= Kohana::$base_url ?>profile/update/';
   var msg = '', group_remove_needed = false;
   if(name.match(/^contacts\[/)) url += 'contacts';
   else if(name.match(/^address/)) url += 'address';
   else if(name.match(/^emergency_contacts/))
   {

      url += 'emergency_contacts';
      msg = '<?= __("Are you sure you want to remove this emergency contact?");?>';
      group_remove_needed = true;
   }
   else if(name.match(/^education/))
   {
      url += 'education';
      msg = '<?= __("Are you sure you want to remove this education record?");?>';
      group_remove_needed = true;
   }
   else if(name.match(/^work_experience/))
   {
      url += 'work_experience';
      msg = '<?= __("Are you sure you want to remove this work experience record?");?>';
      group_remove_needed = true;
   }
   url += '_remove';

   if(confirm((!!msg) ? msg : confirm_delete_msg.replace("%s", dname))) {
      $.post(
         url,
         { name: name, value: val },
         function(data) {
            var data = jQuery.parseJSON(data);
            if(data.error) showError(data.error);
            else {
               $(((group_remove_needed) ? '.' : '#') + data.name).parent().parent().parent().remove();
               cleanEdit();
               if(data.name.match(/^contacts\[/)) {
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
         studentsScope.activateTab('profile');
      });
   }
   return false;
});
$('.sidebar a.change_button').on('click', function(event){
   event.preventDefault();
   studentsScope.changePhoto();
});
</script>