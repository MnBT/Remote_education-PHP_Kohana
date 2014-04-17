<script src="media/js/js_crop/jquery.Jcrop.min.js"></script>
<link rel="stylesheet" href="media/css/css_crop/jquery.Jcrop.css" type="text/css" />

<script type="text/javascript">

   jQuery(function () {
      jQuery('#target').Jcrop({
         <?php if ($height > 600): ?>
            boxHeight:600,
         <?php endif;?>
         onChange:showCoords,
         onSelect:showCoords,
         aspectRatio:1
      });
   });

   // Our simple event handler, called from onChange and onSelect
   // event handlers, as per the Jcrop invocation above
   function showCoords(c) {
      $('#x').val(c.x);
      $('#y').val(c.y);
      $('#w').val(c.w);
      $('#h').val(c.h);
   }
   ;
</script>
<script type="text/javascript">
   function check() {
   //check that image cropped
      var xx = document.forms[0].x.value,
         yy = document.forms[0].y.value,
         hh = document.forms[0].h.value,
         ww = document.forms[0].w.value;

      if (xx != '' && yy != '' && ww != '' && hh != '') {
         document.forms[0].submit();
      }
      else alert('Crop the image pls.');
   }
</script>



<div id="middle" class="middle middle_light">
   <div id="container">
      <div style="margin-top: 5px; margin-bottom: 30px;">
         <img src='<?=$imageurl?>' id="target"/>
      </div>
      <div style="margin: 0px auto; width: 470px;">
         <?= Form::open(Request::$current->url()); ?>
            <?php echo Form::hidden('x', "", array("id" => "x"))?>
            <?php echo Form::hidden('y', "", array("id" => "y"))?>
            <?php echo Form::hidden('w', "", array("id" => "w"))?>
            <?php echo Form::hidden('h', "", array("id" => "h"))?>
            <p class="form_name"><?php echo __("Crop Photo"); ?></p>

            <div><?= Form::submit('button', "", array("onclick" => "check()")) ?></div>
         <?= Form::close(); ?>
         <br class="cleandivider"/>
      </div>
   </div>
</div>
<!--------Bread crumbs-------->
<div class="bread_crumb">
   <div id='secondaryNav'>
      <div class="color_selector">
         <span class="btn"><span class="dark"></span></span>
         <span class="btn"><span class="light"></span></span>
      </div>
      <!--//TODO implement-->
      <ul class='breadcrumb reset'>
         <li class="first_b link_profile">
            <span><a href=""><img  style="background:none;" src="<?= Kohana::$base_url ?>media/img/profile.png" alt="" /></a></span>
         </li>
         <li><a href="profile"><span>My Profile</span></a></li>
         <li><a href="profile/photos"><span>Student Pictures</span></a></li>
      </ul>
   </div>
</div>
<!------End bread crumbs------>
