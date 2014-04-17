<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" >
<head>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<title>Admin Center | Kingston University</title>
	<base href="<?php echo url::base(TRUE,'http'); ?>">
<?php
echo StaticJs::instance()->getJsAll();
echo StaticCss::instance()->getCssAll();
?>
    <link rel="shortcut icon" href="favicon.ico" />

</head>

<body>
<div id="container">
	<div id="header">
            <div id="students_filter">
                <div class="filter_tab"></div>
                
                <div class="select_filter">
                    <select>
                        <option>Major</option>
                    </select>
                </div>
                <img class="filter_icon" src="/media/img/filter_select_icon.gif">

            </div>
    <div class="menu_wrapper">
            <div class="pad mainmenu">
                <div class="menuitem">
                  <div><a href="students" ><?php echo __("STUDENTS"); ?></a></div>
                  <div class="counters">
                    <span value="total" class="total"><?php if (isset($enrolled_count["enrolled"])) echo count($enrolled_count["enrolled"])?></span>
                    <span value="new" class="new">+<?php if (isset($enrolled_count["last_week_enrolled"])) echo count($enrolled_count["last_week_enrolled"])?></span>
                  </div>
                </div>
                <div class="menuitem"><a href="tutors"><?php echo __("TUTORS"); ?></a></div>
                <div class="menuitem"><a href="consultants"><?php echo __("CONSULTANS"); ?></a></div>
                <div class="menuitem"><a href="translators"><?php echo __("TRANSLATORS"); ?></a></div>
                <div class="menuitem"><a href="managers"><?php echo __("MANAGERS"); ?></a></div>
                <div class="menuitem"><a href="courses"><?php echo __("COURSES"); ?></a></div>
                <div class="menuitem"><a href="majors"><?php echo __("MAJOR"); ?></a></div>
                
            <div class="profile_menu">
                <div id="profile">
                    <div class="profile_image"><img src="<?php echo $userpic[Model_User_photo::SIZE_SMALL]; ?>" height="28"></div>
                    <div class="profile_title"><?= $first_name; ?> <?= $last_name; ?></div>
                    <div class="profile_arrow" id="open_profile_menu"><img src="media/img/arrow_login_bottom.gif"></div>
                </div>
                <div class="profile_items">
                    <a href="auth/logout" class="profile_item"><?php echo __("Log out"); ?></a>
                </div>
            </div>   
            </div>
            
            <div class="student_images_menu">
                <img class="student_image" src="/"></img>
                <img class="student_image" src="/"></img>
                <img class="student_image" src="/"></img>
                <img class="student_image" src="/"></img>
                <img class="student_image" src="/"></img>
                <img class="student_image" src="/"></img>
                <img class="student_image" src="/"></img>
                <img class="student_image" src="/"></img>
                <img class="student_image" src="/"></img>
                <img class="student_image" src="/"></img>
                <img class="student_image" src="/"></img>
                <img class="student_image" src="/"></img>
                <img class="student_image" src="/media/img/next_photo_icon.gif"></img>
                <span class="student_number">125</span>
            </div>
            <div class="indicators">
                <div class="indicator indicator_select" id="indicator_1"><img src="/media/img/indicators_select_icon.png"></div>
                <div class="indicator" id="indicator_2"><img src="/media/img/indicators_icon.gif"></div>
                <div class="indicator" id="indicator_3"><img src="/media/img/indicators_icon.gif"></div>
            </div>
    <!--<div class="clearfix"></div>-->
    <div class="students_tabs">
        <?php ACL::_('<div class="menuitem_profile menuitem"><a href="profile"><div class="menuitem_content"><img src="media/img/profile_icon.gif"></div></a></div>', 'edit_student_link'); ?>
        <div class="menuitem_profiles menuitem"><a href="profiles"><div class="menuitem_content_text"><img src="media/img/profile_icon.gif"><span class="menuitem_title"><?php echo __("Profiles"); ?></span></div></a></div>
        <div class="menuitem_progress menuitem"><a href="progress"><div class="menuitem_content_text"><img src="media/img/progress_icon.gif"><span class="menuitem_title"><?php echo __("Progress"); ?></span></div></a></div>
        <div class="menuitem_timeline menuitem"><a href="curriculum"><div class="menuitem_content"><img src="media/img/progress_icon.gif"></div></a></div>
        <div class="menuitem_graph menuitem"><a href="timeline"><div class="menuitem_content_text"><img src="media/img/graph_icon.gif"><span class="menuitem_title"><?php echo __("Graph"); ?></span></div></a></div>
        <div class="menuitem_exams menuitem"><a href="exams"><div class="menuitem_content_text"><img src="media/img/exams_icon.gif"><span class="menuitem_title"><?php echo __("Exams"); ?></span></div></a></div>
        <!--<div class="menuitem_mygrade menuitem"><a href="mygrade"><?php echo __("My Grade"); ?></a></div>-->
        <div class="menuitem_payment menuitem"><a href="payment"><div class="menuitem_content_text"><img src="media/img/pay_icon.gif"><span class="menuitem_title"><?php echo __("Payment"); ?></span></div></a></div>
        <div class="menuitem_mailing menuitem"><a href="mailing"><div class="menuitem_content_text"><img src="media/img/mail_icon.gif"><span class="menuitem_title"><?php echo __("Mailing"); ?></span></div></a></div>
    </div>
    <div class="students_tabs_line"></div>
</div>
    <div class="clearfix"></div>
<?php echo Message::render(); ?>
	</div><!-- END div#header -->
        <div class="content_gradient"></div>
	<div id="content">
    <?php if (isset($content)) echo $content; ?>
	&nbsp
	</div>
        <div id="createStudent">
            <?php if(isset($studentCreateGrid)) echo $studentCreateGrid ?>
        </div>
	<div id="footer">
		
	</div><!-- END div#footer -->

</div><!-- END div#container -->
<div style="display:none" id="div-dialog-warning">
    <span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><p></p>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#open_profile_menu').live('click',function(){
            if($('.profile_menu .profile_items').css('display') == 'none') {
                $('.profile_menu .profile_items').css('display','block');
                $('#profile .profile_arrow img').attr('src','media/img/arrow_login_top.gif');
            }
            else { 
                $('.profile_menu .profile_items').css('display','none');
                $('#profile .profile_arrow img').attr('src','media/img/arrow_login_bottom.gif')
            }
        })        
    })
    bui = {
        overlayCSS: {backgroundColor:'#000', opacity: '0.3'},
        css: { backgroundColor: 'transparent',border:'none',padding:0,margin:0},
        baseZ:1000000,
        fadeOut:10,
        message:false
    };

    function showError(msg) {
    	$("#div-dialog-warning p").html(msg);
    	$("#div-dialog-warning").dialog("open");
    }
	$(document).ready(function() {
		$("#div-dialog-warning").dialog({
		    title: '<?php echo __("Warning"); ?>',
		    resizable: false,
		    modal: true,
		    autoOpen: false,
		    buttons: {
		        "Ok" : function () {
		            $(this).dialog("close");
		        }
		    }
		});
	});
/*	$(document).ajaxStart(function(){
//		$.blockUI(bui);
	});
//	$.ajaxSetup({dataFilter:preprocessHTML});
	$(document).ajaxStop(function(){
//		$.unblockUI();
	});*/
	$('.mainmenu.menuitem').on('click', function(e){ e.preventDefault(); window.location.href=$(this).find('a').attr('href'); });
</script>
<script>
   (function($){
      var statusCode = {
         400:function () {
            alert('<?= __("The syntax of request is incorrect.")?>');
         },
         403:function () {
            alert('<?= __("Forbidden")?>');
         },
         404:function () {
            alert('<?= __("url not found")?>');
         },
         500:function () {
            alert('<?= __("Internal Error")?>');
         }
      };
      var page = "<?= Request::current()->param('page')?>";
      var studentsScope = {
         refreshFullPage : function(callback, scope) {
            $.ajax({
               type:'GET',
               url:'/students/index/page/'+page,
               success:function (response) {
                  response = $.parseJSON(response);
                  if (!!response.success && response.content.length > 0)
                  {
                     $('#content').html(response.content);
                     if(!!callback) callback.call(scope|window);
                  } else {
                     alert('<?= __("Content is not loaded")?>');
                  }
               },
               statusCode: statusCode
            });
         },
         activateTab : function(tabName, callback, scope) {
            $.ajax({
               type:'GET',
               url:'students/' + tabName,
               success: function (response) {
                  response = $.parseJSON(response);
                  if (!!response.success && response.content.length > 0)
                  {
                     $('.students_tabs div.current').removeClass('current');
                     $('.students_tabs div.menuitem_' + tabName).addClass('current');
                     $('.students_tab_content').html(response.content);
                     if(!!callback) callback.call(scope|window);
                  } else {
                     alert('<?= __("Content is not loaded")?>');
                  }
               },
               statusCode: statusCode
            });
         },
         selectStudent : function(newUserId, previousUserId, callback, scope) {
            $.ajax({
               type:'POST',
               url:'students/select',
               data: {id: newUserId},
               success: function (response) {
                  response = $.parseJSON(response);
                  if (!!response.success && response.content.length > 0)
                  {
                     $('#students_main_grid_row_' + previousUserId).removeClass('current');
                     $('#students_main_grid_row_' + newUserId).addClass('current');
                     $('#profiles_grid_row_' + previousUserId).removeClass('current');
                     $('#profiles_grid_row_' + newUserId).addClass('current');
                     $('.students_tab_content').html(response.content);
                     if(!!callback) callback.call(scope|window);
                  } else {
                     alert('<?= __("Content is not loaded")?>');
                  }
               },
               statusCode: statusCode
            });
         },
         sort : function(columnName, method, prevSortColName, callback, scope) {
            $.ajax({
               type:'POST',
               url:'students/sort',
               data: {
                  column: columnName,
                  method: method
               },
               success: function (response) {
                  response = $.parseJSON(response);
                  if (!!response.success && response.content.length > 0)
                  {
                     $('#grid_header_' + prevSortColName).removeClass('sorted');
                     $('#grid_header_' + columnName).addClass('sorted');
                     $('#content').html(response.content);
                     if(!!callback) callback.call(scope|window);
                  } else {
                     alert('<?= __("Content is not loaded")?>');
                  }
               },
               statusCode: statusCode
            });
         },
         filter : function(columnName, value, callback, scope) {
            $.ajax({
               type:'POST',
               url:'students/filter',
               data: {
                  column: columnName,
                  value: value
               },
               success: function (response) {
                  response = $.parseJSON(response);
                  if (!!response.success && response.content.length > 0)
                  {
                     $('#content').html(response.content);
                     if(!!callback) callback.call(scope|window);
                  } else {
                     alert('<?= __("Content is not loaded")?>');
                  }
               },
               statusCode: statusCode
            });
         },
         changePhoto : function(id, action) {
            console.log("changePhoto", id, action);
            var url = 'profile/photos';
            //var params = {};
            if(!!id && $.inArray(action, ['featured', 'remove']) != -1)
            {
               url += (action == 'featured') ? '/featured' : '/remove';
               //params = {id: id};
               url +='/' + id;
            }
            $.ajax({
               type:'GET',
               url:url,
               //data: params,
               success:function (response) {
                  response = $.parseJSON(response);
                  if (!!response.success && response.content.length > 0) {
                     $('.students_tab_content').html(response.content);
                  } else {
                     alert('<?= __("Content is not loaded")?>');
                  }
               }
            });
         },
         enroll_filter : function(tab,filter) {
            $.ajax({
                type: 'GET',
                url: tab + '/enroll_filter',
                data:{'filter':filter},
                success: function (response) {
                response = $.parseJSON(response);
                if (!!response.success && response.content.length > 0)
                {
                    $('#content').html(response.content);
                } else {
                    alert('<?= __("Content is not loaded") ?>');
                }
            }
        })
         }
      };
      window.studentsScope = studentsScope;
   })(this.jQuery);
    
    $('.menuitem .counters .total ,.menuitem .counters .new').on('click',function(){
        if ($(this).hasClass('active'))
            $(this).removeClass('active');
        else {
            $('.menuitem .counters').children().removeClass('active');
            $(this).addClass('active');
        }
            
        
    })
    $('#profiles_grid .grid_row, .students_table .grid_row').on('mouseover',function(){
        $('#profiles_grid tr:eq('+$(this)[0].rowIndex+')').addClass('grid_row_mouse_over');
        $('.students_table tr:eq('+$(this)[0].rowIndex+')').addClass('grid_row_mouse_over');
        
    })
    $('#profiles_grid .grid_row, .students_table .grid_row').on('mouseout',function(){
        $('#profiles_grid tr:eq('+$(this)[0].rowIndex+')').removeClass('grid_row_mouse_over');
        $('.students_table tr:eq('+$(this)[0].rowIndex+')').removeClass('grid_row_mouse_over');
    })
    

/*   $(function() {
      <?php if ($controller === "students") echo 'studentsScope.refreshFullPage();' ?>
   });*/
</script>

</body>
</html>
