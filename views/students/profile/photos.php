<script type="text/javascript" src="media/js/jquery.nyroModal.custom.js"></script>
<!--[if IE 6]>
	<script type="text/javascript" src="media/js/jquery.nyroModal-ie6.min.js"></script>
<![endif]-->
<link rel="stylesheet" href="media/css/nyroModal.css" type="text/css" media="screen" />
   <div id="tab_profile_middle" class="middle middle_light">
      <div id="tab_profile_container">
         <?php if ($error != ""): ?>
            <div style="width: 100%; text-align: center; background-color:#bD0707; color: #fff"><br/><?php echo $error;?>
            <br>&nbsp;</div>
         <?php endif;
         if (count($photos) > 0):
            $i = 0;
            foreach ($photos as $photo):
               ?>
               <div style="float:left;margin-left: 15px;  margin-top: 15px; " id="img_<?php echo $photo->id; ?>"class="img_">
                  <div class="for_buttons" style="margin-bottom: 10px;">
                     <div class="buttons">
                        <img src="media/img/right-btn.png" alt="" class="right-btn"/>
                        <img src="media/img/left-btn.png" alt="" class="left-btn"/>
                        <a href="profile/photos/remove/<?= $photo->id ?>">
                           <img src="media/img/delete-btn.png" alt="" class="delete-btn" title="<?php echo __("remove");?>"/>
                        </a>
                     </div>
                  </div>
                  <div class="for_photo">
                     <div style="margin-bottom: 5px; width:150px; height:180px; ">
                        <a href="<?php  echo $photo->getURL(Model_User_photo::SIZE_FULL); ?>" class="nyroModal"
                           rel="gal" title="<?= $photo->description ?>">
                           <img src="<?php echo $photo->getURL(Model_User_photo::SIZE_THUMBNAIL); ?>"/>
                        </a>
                     </div>
                  </div>
                  <div style="margin-bottom: 16px" class="for_bottom">
                     <?php if (!$photo->isFeatured()): ?>
                        <a href="profile/photos/featured/<?= $photo->id ?>" class="icon-people icon-people_light" title="<?php echo __("make featured");?>"></a>
                     <?php endif; ?>
                     <p class="img_name header_light"><?= $photo->description ?></p>
                  </div>
               </div>

         <?php
             $i++;
      if($i % 3 == 0) echo '<div style="clear: both;"></div>';
            endforeach;
      else: ?>
                                <div style="margin-left: 15px; margin-top: 5px;"><?php echo __("No images upload."); ?></div>
<?php endif; ?>
                            <?php if (count($photos) < 25):?>
                                <div style="clear: both; margin-left: 250px; position:absolute; bottom: 0">
                                    <?= Form::open('profile/photos', array("id" => "upload_photo_form", 'enctype' => 'multipart/form-data', 'accept-charset' => 'utf-8')); ?>
                                    <p class="form_name"><?php echo __("Upload Photo"); ?></p>
                                    <div><span><?php echo __("Description"); ?></span> <?= Form::input('description');?> </div>
                                    <div class="for_file"><?= Form::file('image',array('id' => 'image')); ?> </div>
                                    <div ><?= Form::submit('upload', "", array("id" =>"upload_photo_submit_button")) ?></div>
                                    <?= Form::close(); ?>
                                    <br class="cleandivider" />
                                </div>
                            <?php endif;?>
                        </div><!-- END div#container -->
                    </div> <!-- END div.middle -->
<script type="text/javascript">
$(document).ready(function() {
	$('.nyroModal').nyroModal();
	$(".right-btn, .left-btn").click(function(){
		var direction = ($(this).attr('class') == 'right-btn') ? 'clockwise' : 'counterclockwise';
		var img_id = $(this).parents().eq(2).attr('id').substr(4);
		var img_src = $("#img_" + img_id + " .for_photo div a img").attr('src');
		$.post(
			'<?= Kohana::$base_url ?>profile/photos/rotate',
			{'direction': direction, 'img_id': img_id},
			function(data){
				if(!data) return;
				d = new Date();
				$("#img_" + img_id + " .for_photo div a img").attr('src', img_src + "?"+d.getTime());
			}
		);
	});
	$(".color_selector .btn").click(function(){
		var classN = $("span", this).attr('class');
		var classP = (classN == 'light') ? 'dark' : 'light';
		$("#tab_profile_middle").removeClass('middle_' + classP).addClass('middle_' + classN);
		$(".for_bottom .img_name").removeClass('header_' + classP).addClass('header_' + classN);
		$(".img_ .for_bottom .icon-people").removeClass('icon-people_' + classP).addClass('icon-people_' + classN);
	});
});

$('#upload_photo_form').ajaxForm({
   beforeSubmit: function (formData, jqForm, options) {
      $('#upload_photo_submit_button').text('Uploading file...');
      return true;
   },
   success: function (responseText, statusText) {
      studentsScope.changePhoto();
   },
   error: function () {
      alert('Ajax error during file upload');
   }
});
$('.for_bottom a.icon-people').on('click', function (event) {
   event.preventDefault();
   var id = $(this).attr('href').substr(24);
   console.log("featured ", id);
   studentsScope.changePhoto(id, 'featured');

});
$('.for_buttons a').on('click', function (event) {
   event.preventDefault();
   var id = $(this).attr('href').substr(22);
   console.log("deleted ", id);
   studentsScope.changePhoto(id, 'remove');
});




</script>
                            