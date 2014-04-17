<?php
  if (isset($errors)) {
    if (isset($htmlOptions) && isset($htmlOptions['class']))
      echo '<div class="'. $htmlOptions['class'].'">';
    else
      echo '<div class="errors">';
    
      echo '<ul>';
      foreach ($errors as $error) {
        if (is_object($error))
          $message = $error->text;
        else
          $message = $error;
        echo '<li>'. $message. '</li>';
      }
      echo '</ul>';
      
    echo '</div>';
  }

