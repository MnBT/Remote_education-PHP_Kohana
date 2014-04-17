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
  'id' => 'major',
  'method' => 'POST',
  'enctype' => 'multipart/form-data'
  )
);

if (isset($model))
  echo FORM::input('id', $model->id, array('type' => 'hidden'));
?>

<div class="row">
  <label for="degree_id"><?php echo __('Degree'); ?></label>
  <select id="degree_id" name="degree_id">
    <option value=""><?php echo __('Select degree'); ?></option>
  <?php
    $degrees = Model::factory('degree')->find_all();
    foreach ($degrees as $degree) {
      if(isset($model) && $model->degree_id == $degree->id)
        $selected = ' selected="selected"';
      else
        $selected = '';
      echo '<option value="'. $degree->id. '"'. $selected. '>'. $degree->name .'</option>';
    }
  ?>
  </select>
</div>

<div class="row">
  <label for="name"><?php echo __('Name'); ?></label>
  <?php
  $params = array(
    'id' => 'name',
    'maxlength' => '200',
    'placeholder' => __('input name here')
  );

  if (isset($model))
    $value = $model->name;
  else
    $value = '';

  echo FORM::input('name', $value, $params);
  ?>
</div>

<div class="row">
  <label for="duration"><?php echo __('Duration'); ?></label>
  <?php
  $params = array(
    'id' => 'duration',
    'maxlength' => '3',
    'placeholder' => __('input duration here')
  );

  if (isset($model) && !empty($model->duration))
    $value = $model->duration;
  else
    $value = '24';

  echo FORM::input('duration', $value, $params);
  ?>
</div>

<?php if ($submit == __('Create')) { ?>
<div class="row">
  <label for="file"><?php echo __('File'); ?></label>
  <?php
  $params = array(
    'id' => 'file'
  );

  echo FORM::file('file', $params);
  ?>
</div>
<?php } ?>

<div class="row">&nbsp;</div>

<div class="row">
  <input type="submit" value="<?php echo $submit; ?>"/>
</div>

<?php
echo Form::close();
?>

<div class="clearfix"></div>
