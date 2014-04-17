<?php
  if (isset($errors))
    echo View::factory('_errors', array('errors' => $errors));

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

<?php
if (isset($model))
  echo FORM::input('id', $model->id, array('type' => 'hidden'));
?>

<div class="row">
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
</div>

<div class="row">
  <label for="type_id"><?php echo __('Type'); ?></label>
  <select id="type_id" name="type_id">
    <option value=""><?php echo __('Select type'); ?></option>
      <?php
      $types = Model::factory('book_type')->find_all();
      foreach ($types as $type) {
        if (isset($model) && $model->type_id == $type->id)
          $selected = ' selected="selected"';
        else
          $selected = '';
        echo '<option value="' . $type->id . '"' . $selected . '>' . $type->name . '</option>';
      }
      ?>
  </select>
</div>

<div class="row">
  <label for="lang_id"><?php echo __('Language'); ?></label>
  <select id="lang_id" name="lang_id">
    <option value=""><?php echo __('Select language'); ?></option>
      <?php
      $langs = Model::factory('book_lang')->find_all();
      foreach ($langs as $lang) {
        if (isset($model) && $model->lang_id == $lang->code)
          $selected = ' selected="selected"';
        else
          $selected = '';

        echo '<option value="' . $lang->id . '"' . $selected . '>' . __($lang->name. ' - '. $lang->description) . '</option>';
      }
      ?>
  </select>
</div>

<div class="row">
  <label for="version"><?php echo __('Version'); ?></label>
  <?php
  $params = array(
    'id' => 'version',
    'maxlength' => '3',
    'placeholder' => __('input version here')
  );

  if (isset($model))
    $value = $model->version;
  else
    $value = '';

  echo FORM::input('version', $value, $params);
  ?>
</div>

<div class="row">
  <label for="days"><?php echo __('Days'); ?></label>
  <?php
  $params = array(
    'id' => 'days',
    'maxlength' => '3',
    'placeholder' => __('input count of days here')
  );

  if (isset($model))
    $value = $model->days;
  else
    $value = '';

  echo FORM::input('days', $value, $params);
  ?>
</div>

<div class="row">
  <label for="progress"><?php echo __('Progress'); ?></label>
  <?php
  $params = array(
    'id' => 'days',
    'maxlength' => '3',
    'placeholder' => __('input progress percentage here')
  );

  if (isset($model))
    $value = $model->progress;
  else
    $value = '';

  echo FORM::input('progress', $value, $params);
  ?>
</div>

<div class="row">&nbsp;</div>

<div class="row">
  <input type="submit" value="<?php echo $submit; ?>"/>
</div>

<?php
echo Form::close();
?>

<div class="clearfix"></div>

<script>
  $(function() {
    $("#started" ).datepicker({ dateFormat: "dd.mm.yy" });
  });
</script>