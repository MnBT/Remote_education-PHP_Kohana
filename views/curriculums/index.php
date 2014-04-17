<?php

echo '
  <div id="curriculums_list" style="float: left; margin: 0 10px 0 0;">
    '. View::factory('curriculums/list', array('model' => $model, 'pagination' => $pagination)). '
  </div>';
echo '
  <div style="clear: both;">';
?>

<script>
  $(document).ready(function() {
    $('table.curriculums').on('click', 'tr', function(){
      $('table.curriculums tr.active').removeClass('active');
      
      $(this).addClass('active');
      
      id = $(this).children('input[type="hidden"][name="id"]').val();
    });
    
    <?php
      if ($model->count() > 0) {
    ?>
        //  первая строка подсвечивается как активная
        $('table.curriculums tr input[type="hidden"][name="id"][value="<?php echo $model[0]->id; ?>"]').parent().addClass("active");
    <?php
      }
    ?>
  })
</script>
