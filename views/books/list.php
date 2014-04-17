<?php

$columns = array(
  array(
    'field' => 'code',
    'caption' => __('Code'),
    'width' => '160'
  ),
  array(
    'field' => 'file',
    'caption' => __('File'),
    'width' => '180'
  ),
  array(
    'field' => 'type->name',
    'caption' => __('Type'),
    'width' => '50'
  ),
  array(
    'field' => 'number',
    'caption' => __('Number'),
    'width' => '60'
  ),
  array(
    'field' => 'order',
    'caption' => __('Order'),
    'width' => '50'
  ),
  array(
    'field' => 'lang->name',
    'caption' => __('Lang'),
    'width' => '40'
  ),
  array(
    'field' => 'version',
    'caption' => __('Version'),
    'width' => '65'
  ),
  array(
    'field' => 'days',
    'caption' => __('Days'),
    'width' => '40'
  ),
  array(
    'field' => 'progress',
    'caption' => __('Progress'),
    'width' => '70'
  ),
);

$buttons = array(
  'width' => '40',
  'buttons' => array(
//    'add' => array(
//      'img' => array('src' => 'media/img/add.png', 'alt' => __('add version')),
//      'url' => 'books/version/',
//      'html' => array('title' => __('add version'))
//    ),
    'edit' => array(
      'img' => array('src' => 'media/img/edit.png', 'alt' => __('edit')),
      'url' => 'books/update/',
      'html' => array('title' => __('edit'))
    ),
    'del' => array(
      'img' => array('src' => 'media/img/del.png', 'alt' => __('del')),
      'url' => 'books/delete/',
      'html' => array('class' => 'book_del', 'title' => __('del'))
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

ACL::_(HTML::anchor('books/create', HTML::image('media/img/book_create.png', array('alt' => 'create book', 'class' => 'book_create'), array('title' => 'Create book'))), "create_book_link");

$grid = GridView::factory(array(
  'model' => $model,
  'settings' => array('htmlOptions' => array('class' => 'books')),
  'columns' => $columns,
  'buttons' => $buttons,
  'script' => $script,
  'pagination' => $pagination
));

echo $grid->render();
