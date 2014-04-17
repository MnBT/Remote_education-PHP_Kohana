<?php
  if (isset($errors))
    echo View::factory('_errors', array('errors' => $errors));

  if (isset($model) && !empty($model->id))
    $submit = __('Save');
  else
    $submit = __('Create');
  
echo FORM::open(
  Request::current()->uri(), 
  array(
    'id' => 'course',
    'method' => 'POST',
    'enctype' => 'multipart/form-data'
  )
);

?>

  <div class="row">
    <label for="code"><?php echo __('Code'); ?></label>
    <?php 
      if(isset($model)) $value = $model->code; else $value = '';
      echo Form::input(
        'code', 
        $value, 
        array(
          'id' => 'code',
          'maxlength' => '50', 
          'placeholder' => __('input code here')
          )
        );
      ?>
  </div>

  <div class="row">
    <label for="name"><?php echo __('Name'); ?></label>
    <input type="text" id="name" name="name" maxlength="255" placeholder="<?php echo __('input name here'); ?>"
           value="<?php if(isset($model)) echo $model->name; ?>" />
  </div>

  <div class="row">
    <label for="description"><?php echo __('Description'); ?></label>
    <input type="text" id="description" name="description" maxlength="255" placeholder="<?php echo __('input description here'); ?>" value="<?php if(isset($model)) echo $model->description; ?>" />
  </div>
  
  <div class="row">
    &nbsp;
  </div>

  <div class="row">
    <?php echo Form::submit('', $submit); ?>
  </div>

<?php echo Form::close(); ?>


