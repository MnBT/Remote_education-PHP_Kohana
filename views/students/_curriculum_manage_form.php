<?php
if (isset($errors))
  echo View::factory('_errors', array('errors' => $errors));

$action = Route::get('students')->uri(
    array(
      'controller' => 'students',
      'action' => 'update_curriculum_course',
      'id' => $model->id)
  );

echo FORM::open(
  $action, array(
  'id' => 'course_info',
  'method' => 'POST',
  'enctype' => 'multipart/form-data'
  )
);
?>

<div class="row">
  <label for="course_start"><?php echo __('Start'); ?></label>
  <input type="text" id="course_start" name="course_start" placeholder="<?php echo __('input start date here'); ?>"
         value="<?php 
            if (isset($model) && !empty($model->course_start) && $model->course_start != 0) 
              echo date('d.m.Y', strtotime($model->course_start)); 
            else 
              echo date('d.m.Y'); 
           ?>"
  />
</div>


<div class="row">
  <label for="course_limit"><?php echo __('Time Limit'); ?></label>
  <input type="text" id="course_limit" name="course_limit" maxlength="3" placeholder="<?php echo __('input course time limit here'); ?>"
         value="<?php if (isset($model)) echo $model->course_limit; ?>" />
</div>

<div class="row">
  <label for="control_period"><?php echo __('Period'); ?></label>
  <input type="text" id="control_period" name="control_period" maxlength="3" placeholder="<?php echo __('input control period here'); ?>" value="<?php if (isset($model) && !empty($model->control_period)) echo $model->control_period; else echo '10'; ?>" />
</div>

<div class="row">
  <input type="submit" id="submit" value="<?php echo __('Save'); ?>"/>
</div>

<?php
echo Form::close();
?>

<script>
  $(function() {
//    if (!Modernizr.inputtypes.date)
      $("#course_start").datepicker({
        dateFormat: "dd.mm.yy",
        changeMonth: true,
        changeYear: true
      });
  });
</script>