<?php
if (isset($errors))
  echo View::factory('_errors', array('errors' => $errors));

if (isset($model))
  $major_id = $model->major_id;
else
if (!isset($major_id))
  $major_id = 0;

if (isset($model) && !empty($model->id)) {
  $action = Route::get('curriculums_list')->uri(
    array(
      'action' => 'update_course',
      'id' => $model->id)
  );
  $submit = __('Save');
} else {
  $action = Route::get('curriculums_list')->uri(
    array(
      'action' => 'add_course',
      'major' => '',
      'major_id' => $major_id
    )
  );
  $submit = __('Create');
}

echo FORM::open(
  $action, array(
  'id' => 'course_info',
  'method' => 'POST',
  'enctype' => 'multipart/form-data'
  )
);
?>
<div class="row">
  <label for="course_id"><?php echo __('Course'); ?></label>
  <select id="course_id" name="course_id">
    <option value=""><?php echo __('Select course'); ?></option>
    <?php
    $courses = Model::factory('course')->order_by('code')->find_all();
    foreach ($courses as $course) {
      if (isset($model) && $model->course_id == $course->id)
        $selected = ' selected="selected"';
      else
        $selected = '';
      echo '<option value="' . $course->id . '"' . $selected . '>' . $course->code . '</option>';
    }
    ?>
  </select>
</div>

<div class="row">
  <label for="course_type_id"><?php echo __('Type'); ?></label>
  <select id="course_type_id" name="course_type_id">
    <option value=""><?php echo __('Select course type'); ?></option>
    <?php
    $types = Model::factory('course_type')->find_all();
    foreach ($types as $type) {
      if (isset($model) && $model->course_type_id == $type->id)
        $selected = ' selected="selected"';
      else
        $selected = '';
      echo '<option value="' . $type->id . '"' . $selected . '>' . $type->description . '</option>';
    }
    ?>
  </select>
</div>

<div class="row">
  <label for="deactivation_type_id"><?php echo __('Type'); ?></label>
  <select id="deactivation_type_id" name="deactivation_type_id">
    <option value=""><?php echo __('Select deactivation type'); ?></option>
    <?php
    $types = Model::factory('deactivation_type')->find_all();
    foreach ($types as $type) {
      if (isset($model) && $model->deactivation_type_id == $type->id)
        $selected = ' selected="selected"';
      else
        $selected = '';
      echo '<option value="' . $type->id . '"' . $selected . '>' . $type->name . '</option>';
    }
    ?>
  </select>
</div>

<div class="row">
  <label for="order"><?php echo __('Order'); ?></label>
  <input type="text" id="order" name="order" maxlength="2" placeholder="<?php echo __('input order here'); ?>"
         value="<?php if (isset($model)) echo $model->order; ?>" />
</div>

<div class="row">
  <label for="hours"><?php echo __('Hours'); ?></label>
  <input type="text" id="hours" name="hours" maxlength="3" placeholder="<?php echo __('input hours here'); ?>"
         value="<?php if (isset($model)) echo $model->hours; ?>" />
</div>

<div class="row">
  <label for="credit"><?php echo __('Credits'); ?></label>
  <input type="text" id="credits" name="credits" maxlength="3" placeholder="<?php echo __('input credit here'); ?>"
         value="<?php if (isset($model)) echo $model->credits; ?>" />
</div>

<div class="row">
  <label for="course_start"><?php echo __('Start'); ?></label>
  <input type="text" id="course_start" name="course_start" placeholder="<?php echo __('input start date here'); ?>"
         value="<?php if (isset($model) && !empty($model->course_start)) echo date('d.m.Y', strtotime($model->course_start)); ?>"/>
</div>


<div class="row">
  <label for="course_limit"><?php echo __('Time Limit'); ?></label>
  <input type="text" id="course_limit" name="course_limit" maxlength="3" placeholder="<?php echo __('input course time limit here'); ?>"
         value="<?php if (isset($model)) echo $model->course_limit; ?>" />
</div>

<div class="row">
  <label for="control_type_id"><?php echo __('Control type'); ?></label>
  <select id="control_type_id" name="control_type_id">
    <option value=""><?php echo __('Select control type'); ?></option>
    <?php
    $types = Model::factory('control_type')->find_all();
    foreach ($types as $type) {
      if (isset($model) && $model->control_type_id == $type->id)
        $selected = ' selected="selected"';
      else
        $selected = '';
      echo '<option value="' . $type->id . '"' . $selected . '>' . $type->name . '</option>';
    }
    ?>
  </select>
</div>

<div class="row">
  <label for="control_period"><?php echo __('Period'); ?></label>
  <input type="text" id="control_period" name="control_period" maxlength="3" placeholder="<?php echo __('input control period here'); ?>" value="<?php if (isset($model) && !empty($model->control_period)) echo $model->control_period; else echo '10'; ?>" />
</div>

<div class="row">
  <input type="submit" id="submit" value="<?php echo $submit; ?>"/>
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

    $("#hours").change(function() {
      var hours = $(this).val();
      var credits = Math.round(hours / 3);

      $('#credits').val(credits);
    });
  });
</script>