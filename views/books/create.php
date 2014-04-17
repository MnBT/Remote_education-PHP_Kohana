<?php
if (isset($errors)) {
  echo '<div class="validation_errors">';
  foreach ($errors as $error)
    echo '<div class="validation_error">' . $error . '</div>';
  echo '</div>';
}
?>

<?php
if (isset($model) && !empty($model->id)) {
  $action = Request::$current->uri(array('action' => 'update'));
  $submit = __('Save');
} else {
  $action = Request::$current->uri(array('action' => 'create'));
  $submit = __('Create');
}

echo FORM::open(
  $action, array(
  'id' => 'book',
  'method' => 'POST',
  'enctype' => 'multipart/form-data'
  )
);
?>

<div class="row">
  <label for="file"><?php echo __('File'); ?></label>
  <?php
  $params = array(
    'id' => 'file'
  );

  echo FORM::file('file', $params);
  ?>
  <!--<div class="tip">Input path to book's file</div>-->
</div>

<div class="row">
  <label for="progress"><?php echo __('Progress'); ?></label>
  <?php
  $params = array(
    'id' => 'days',
    'maxlength' => '3',
    'placeholder' => __('input progress percentage here')
  );
  
  $value = '';

  echo FORM::input('progress', $value, $params);
  ?>
</div>

<div class="row">&nbsp;</div>

<div id="book_info" class="row">
<!--
    <label for="course_id"><?php echo __('Course'); ?></label>
    <select id="course_id" name="course_id">
      <option value=""><?php echo __('Select course'); ?></option>
        <?php
        $courses = Model::factory('course')->find_all();
        foreach ($courses as $course) {
          if (isset($model) && $model->course_id == $course->id)
            $selected = ' selected="selected"';
          else
            $selected = '';
          echo '<option value="' . $course->id . '"' . $selected . '>' . $course->code . ' - ' . $course->name . '</option>';
        }
        ?>
    </select>
-->
  <ul>
    <li><span class="key">Code</span><span id="book_info_code" class="value"></span>
    <li><span class="key">Type</span><span id="book_info_type" class="value"></span>
    <li><span class="key">Number</span><span id="book_info_number" class="value"></span>
    <li><span class="key">Order</span><span id="book_info_order" class="value"></span>
    <li><span class="key">Lang</span><span id="book_info_lang" class="value"></span>
    <li><span class="key">Version</span><span id="book_info_version" class="value"></span>
    <li><span class="key">Days</span><span id="book_info_days" class="value"></span>
  </ul>
</div>

<div class="row">
  <input type="submit" value="<?php echo $submit; ?>"/>
</div>

<?php
echo Form::close();
?>

<div class="clearfix"></div>

<script>
  $(function() {
       
    $("form#book").on('change', 'input[type="file"]', 
      function(){
        var name = $(this).val();
        var parts = name.split('-');
        var days = parts[5].split('.');
                
        $('#book_info_code').text(parts[0]);
        $('#book_info_type').text(parts[1].match(/[A-Za-z]{1,3}/));
        $('#book_info_number').text(parts[1].match(/\d/g));
        $('#book_info_order').text(parts[2]);
        $('#book_info_lang').text(parts[3]);
        $('#book_info_version').text(parts[4].match(/\d/g));
        $('#book_info_days').text(days[0]);
        
        $('#book_info').show('slow');
      }
    );
  });
</script>


