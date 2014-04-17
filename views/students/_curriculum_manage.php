<?php

$columns = array(
  array(
    'field' => 'order',
    'caption' => __('Order'),
    'width' => '40'
  ),
  array(
    'field' => 'course->code',
    'caption' => __('Code'),
    'width' => '65'
  ),
  array(
    'field' => 'title',
    'caption' => __('Title'),
    'field' => 'course->name',
    'width' => '130'
  ),
  array(
    'field' => 'deactivation_type->name',
    'caption' => __('Type'),
    'width' => '35'
  ),
  array(
    'field' => 'credits',
    'caption' => __('Credits'),
    'width' => '50'
  ),
  array(
    'field' => 'hours',
    'caption' => __('Hours'),
    'width' => '40'
  ),
  array(
    'field' => 'course_start',
    'caption' => __('Started'),
    'callback' => array(
      'function' => function($params) {
        if (!empty($params['value']) && $params['value'] > 0)
          return date('d.m.Y', strtotime($params['value']));
        else        
          return '';
      }
    ),
    'width' => '70'
  ),
  array(
    'field' => 'course_end',
    'caption' => __('Ended'),
    'callback' => array(
      'function' => function($params) {
        if (!empty($params['value']) && $params['value'] > 0)
          return date('d.m.Y', strtotime($params['value']));
        else        
          return '';
      }
    ),
    'width' => '70'
  ),
  array(
    'field' => 'control_type->name',
    'caption' => __('Control type'),
    'width' => '50'
  ),
  array(
    'field' => 'control_start',
    'caption' => __('Test started'),
    'callback' => array(
      'function' => function($params) {
        if (!empty($params['value']) && $params['value'] > 0)
          return date('d.m.Y', strtotime($params['value']));
        else        
          return '';
      }
    ),
    'width' => '70'
  ),
  array(
    'field' => 'control_period',
    'caption' => __('Period'),
    'width' => '50'
  ),
);

$buttons = array(
  'width' => '20',
  'buttons' => array(
    'edit' => array(
      'img' => array('src' => 'media/img/edit.png', 'alt' => __('edit')),
      'url' => 'students/update_curriculum_course/',
      'html' => array('class' => 'curriculum_update')
    ),
  )
);

$grid = GridView::factory(array(
  'model' => $model,
  'settings' => array('htmlOptions' => array('class' => 'curriculums')),
  'columns' => $columns,
  'buttons' => $buttons,
));

echo $grid->render();
