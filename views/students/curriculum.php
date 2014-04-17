<?php

if (isset($major))
  echo '<h2 class="curriculum_title">'. $major['name']. '</h2>';

echo '<div id="curriculum">';
if (isset($data))
  foreach ($data as $item) {
    $type = $item['type']->as_array();

    echo '<div class="curriculum courses_header">'. $type['description']. '</div>';

    echo '<div id="courses_list_'. $type['id']. '">'.
      View::factory('students/_curriculum_manage', array('model' => $item['courses'], 'pagination' => null)) .
    '</div>';
  }
echo '</div>';

echo '<div id="course_info_dialog" style="display: none;"></div>';
?>

<script>
  $(document).ready(function() {
  
    $('#course_info_dialog').dialog(
    {
      autoOpen: false, 
      width: 335, 
      resizable: false
    });
   
    $('#course_info_dialog').on('submit', '#course_info',
      function(){
        var data = $(this).serialize();
        var sender = $('#course_info_dialog').dialog('option', 'sender');
        var list = $(sender).closest('div table.curriculums');
        
//        console.log(sender);
//        console.log($(sender).closest('table.curriculums'));
//        console.log($(sender).closest('table.curriculums').closest('div'));
//        console.log($(sender).closest('div table.curriculums'));
        
        $('#course_info_dialog div.errors').remove();
        
        var uri = $(this).attr('action');
        var action = uri.split('/')[2];
        
        //  сохранение введенного типа курса
        $.post(
          uri, 
          data, 
          function(data){
            if (data.result === 'success') {
              //  обновление таблицы
              $(list).html(data.content);
              
              $('#course_info_dialog').dialog("close");
            } else {
                $('#course_info_dialog').prepend(data.errors);
            }
          }, 
          'json');
  
        return false;
      }
    );
    
    $('#curriculum').on('click', '.curriculum_update',
      function(){
        var sender = this;
        $.get(
          $(this).attr("href"),
          {},
          function(data){
            $('#course_info_dialog').html(data);
            $('#course_info_dialog').dialog({sender: sender});
            $('#course_info_dialog').dialog({title: "<?php echo __('Edit course'); ?>"});
            $('#course_info_dialog').dialog("open");
          }
        );

        return false;
      }
    );

});
  
  function clear_form(form) {
//  return false;
    $(form).find(':input').each(function() {
      switch(this.type) {
        case 'password':
        case 'select-multiple':
        case 'select-one':
        case 'text':
        case 'textarea':
          $(this).val('');
          break;
        case 'checkbox':
        case 'radio':
          this.checked = false;
      }
    });
  }
</script>