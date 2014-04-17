<?php

$columns = array(
  array(
    'field' => 'code',
    'caption' => __('Code'),
    'width' => '50'
  ),
  array(
    'field' => 'name',
    'caption' => __('Name'),
//    'width' => '40%'
  ),
);

$buttons = array(
  'width' => '40',
  'buttons' => array(
    'edit' => array(
      'img' => array('src' => 'media/img/edit.png', 'alt' => __('edit')),
      'url' => 'courses/update/'
    ),
    'del' => array(
      'img' => array('src' => 'media/img/del.png', 'alt' => __('del')),
      'url' => 'courses/delete/',
      'html' => array('class' => 'course_del')
    ),
  )
);

$script = '
<script>
  $(document).ready(function() {
    $(".book_del").click(function(){
      var sender = this;
      if (confirm("'. __('Delete record?'). '")){
        $.post(
        $(this).attr("href"),
        {},
        function(data){
          if (data.result == "success") {
            console.log($(this));
            $(sender).closest("tr").remove();
          }
          else
            if (data.result == "fail")
              alert("'.  __('Sorry, something is wrong!'). '" + data.message);
        },
        "json"
      )
      }
      return false;
    });
  });
</script>';

ACL::_(HTML::anchor('courses/create', HTML::image('media/img/course_create.png', array('alt' => 'create course', 'class' => 'course_create'), array('title' => 'Create course'))), "create_course_link");

$grid = GridView::factory(array(
  'model' => $model,
  'settings' => array('htmlOptions' => array('class' => 'courses')),
  'columns' => $columns,
  'buttons' => $buttons,
  'script' => $script,
  'pagination' => $pagination
));

echo $grid->render();