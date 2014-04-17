<?php

if (isset($errors))
  echo View::factory('_errors', array('errors' => $errors));

if (isset($major))
  echo '<h2 class="curriculum_title">'. 
    $major['name']. HTML::anchor(Route::get('curriculums_list')->uri(
      array(
        'action' => 'delete', 
         'major' => '',
         'major_id' => $major['id']
      )),
      HTML::image('media/img/curriculum_clear.png', array('alt' => 'Clear curriculum')),
      array(
        'class' => 'curriculum_clear',
        'title' => 'Clear curriculum'
      )
    ). '</h2>';

echo '<div id="curriculum">';
if (isset($data))
  foreach ($data as $item) {
    $type = $item['type']->as_array();

    echo '<div class="curriculum courses_header">'. 
      $type['description']. 
      '<input type="hidden" name="type_id" value="'. $type['id']. '"/>'.
      HTML::anchor(
        Route::get('curriculums_list')->uri(array(
            'action' => 'add_course', 
            'major' => '',
            'major_id' => $major['id'],
            'type' => '',
            'type_id' => $type['id']
            )),
        HTML::image('media/img/add.png', array(
          'alt' => 'Add '. $type['name']. ' course'
          )
        ),
        array(
          'class' => 'curriculum_add_course',
          'title' => 'Add '. $type['name']. ' course'
        )
      ). 
    '</div>';

    echo '<div id="courses_list_'. $type['id']. '">'.
      View::factory('curriculums/_manage', array('model' => $item['courses'])) .
    '</div>';
  }
echo '</div>';

if (isset($form))
  echo '<div id="course_info_dialog" style="display: none;">'. $form. '</div>';

?>
<script>
  var CONTROL_PERIOD_DEFAULT_VALUE = 10;
  
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
        var list = $('#courses_list_' + $('form#course_info #course_type_id :selected').val());
        
        $('#course_info_dialog div.errors').remove();
        
        var uri = $(this).attr('action');
        var action = uri.split('/')[2];
        var type_id = $('form#course_info #course_type_id option:selected').val();
        
        //  сохранение введенного типа курса
        $('#course_info_dialog').dialog({course_type_id: type_id});
        
        $.post(
          uri, 
          data, 
          function(data){
            if (data.result === 'success') {
              //  обновление таблицы
              $(list).html(data.content);
              
              //  обработка диалога
              if (action === 'update_course')
                $('#course_info_dialog').dialog("close");
              else {
                clear_form($('form#course_info'));
                
                //  восстановление типа курса, введенного до сохранения                
                var type_id = $('#course_info_dialog').dialog('option', 'course_type_id');
                $('form#course_info #course_type_id option[value="' + type_id + '"]').attr('selected', 'selected');
                
                // восстановление данных по умолчанию
                $('form#course_info #control_period').val(CONTROL_PERIOD_DEFAULT_VALUE);
              }
            } else {
                $('#course_info_dialog').prepend(data.errors);
            }
          }, 
          'json');
  
        return false;
      }
    );
 
    $('.curriculum.courses_header').on('click', '.curriculum_add_course', 
      function(){
        clear_form($('form#course_info'));
        
        $('form#course_info').attr('action', '<?php 
          echo Route::get('curriculums_list')->uri(
            array(
              'action' => 'add_course',
              'major' => '',
              'major_id' => $major['id']
            )); ?>');
        $('form#course_info #submit').val('<?php echo __('Create'); ?>');
        
        var type_id = $(this).siblings('input[type="hidden"][name="type_id"]').val();

        $('form#course_info #course_type_id option[value="' + type_id + '"]').attr('selected', 'selected');
        
        $('form#course_info #control_period').val(CONTROL_PERIOD_DEFAULT_VALUE);

        $('#course_info_dialog').dialog({title: "<?php echo __('Add course'); ?>"});
        $('#course_info_dialog').dialog("open");

        return false;
      }
    );
    
    $(".curriculum_del").click(function(){
      var sender = this;
      
      if (confirm("<?php echo __('Delete record?'); ?>")){
        $.post(
          $(this).attr("href"),
          {},
          function(data){
            if (data.result === "success") {
              $(sender).closest("tr").remove();
            }
            else
              if (data.result === "fail")
                alert("<?php echo __('Sorry, something is wrong!'); ?> " + data.message);
          },
          "json"
        );
      }
      
      return false;
    });
   
    $('#curriculum').on('click', '.curriculum_update',
      function(){
        $.get(
          $(this).attr("href"),
          {},
          function(data){
            $('#course_info_dialog').html(data);
            $('#course_info_dialog').dialog({title: "<?php echo __('Edit course'); ?>"});
            $('#course_info_dialog').dialog("open");
          }
        );

        return false;
      }
    );
    
    $('.curriculum_clear').click(
      function(){
        
        if (confirm("<?php echo __('Delete all records?'); ?>")){
          $.post(
            $(this).attr("href"),
            {}, 
            function(data){
              if (data.result === 'success') {
                $('#content').html(data.content);
              } else
              if (data.result === "fail"){
                alert("<?php echo __('Sorry, something is wrong!'); ?> " + data.message);
              }
            }, 
            'json'
          );
        }
        
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