<?php

$columns = array(
  array(
    'field' => 'degree',
    'caption' => __('Degree'),
    'field' => 'degree->name',
    'width' => '35'
  ),
  array(
    'field' => 'name',
    'caption' => __('Name'),
    'width' => '150'
  ),
  array(
    'field' => 'filename',
    'caption' => __('Syllabus'),
    'callback' => array(
      'function' => function($params) {
        if (!empty($params))
          return HTML::anchor(
            $params['uploads_url']. $params['value'],
            HTML::image('media/img/major_download.png', array(
              'class' => 'download',
              'alt' => $params['value'])
            ),
            array('title' => $params['value'], 'style' => 'text-align: center;')
            );
        else        
          return '';
      },
      'params' => array('uploads_url' => $uploads_url)
    ),
    'width' => '40',
    'htmlOptions' => array('style' => 'text-align: center;')
  ),
  array(
    'field' => 'duration',
    'caption' => __('Duration'),
    'width' => '40',
    'htmlOptions' => array('style' => 'text-align: right;')
  ),
//  array(
//    'field' => 'description',
//    'caption' => __('Description'),
//    'width' => '250'
//  ),
);

$buttons = array( 
  'width' => '30',
  'buttons' => array(
    'edit' => array(
      'img' => array('src' => 'media/img/edit.png', 'alt' => __('edit')),
      'url' => 'majors/update/'
    ),
    'del' => array(
      'img' => array('src' => 'media/img/del.png', 'alt' => __('del')),
      'url' => 'majors/delete/',
      'html' => array('class' => 'major_del')
    ),
  )
);

$script = '
<script>
  $(document).ready(function() {
    $(".major_del").click(function(){
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

ACL::_(HTML::anchor('majors/create', HTML::image('media/img/major_create.png', array('alt' => 'create major', 'class' => 'major_create'), array('title' => 'Create major'))), "create_major_link");

$grid = GridView::factory(array(
  'model' => $model,
  'settings' => array('htmlOptions' => array('class' => 'majors')),
  'columns' => $columns,
  'buttons' => ACL::_($buttons, "major_list_buttons"),
  'script' => $script,
  'pagination' => $pagination
));

echo $grid->render();