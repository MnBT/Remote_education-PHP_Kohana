<?php

echo '<div id="curriculum">';
if (isset($data))
  foreach ($data as $item) {
    $type = $item['type']->as_array();

    echo '<div class="curriculum courses_header">'. $type['description']. '</div>';

    echo '<div id="courses_list_'. $type['id']. '">'.
      View::factory('curriculums/_list', array('model' => $item['courses'], 'pagination' => null)) .
    '</div>';
  }
echo '</div>';
