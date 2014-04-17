<?php

echo '
  <div id="majors_list" style="float: left; margin: 0 10px 0 0;">
    '. View::factory('majors/list', array('model' => $model, 'uploads_url' => $uploads_url, 'pagination' => $pagination)). '
  </div>';

//echo HTML::anchor('curriculums/create', HTML::image('media/img/curriculum_create.png', array('alt' => 'create curriculum', 'class' => 'curriculum_create'), array('title' => 'Create curriculum')));

if ($model->count() > 0)
  ACL::_('<div>'. 
    HTML::anchor(
      'curriculums/manage/major/'. $model[0]->id, 
      HTML::image('media/img/curriculum_manage.png', array('alt' => 'manage curriculum', 'class' => 'curriculum_manage')),
      array('id' => 'btn_curriculum_manage', 'title' => 'Manage curriculum')
    ).
  '</div>', "manage_curriculum_link");

echo '
  <div id="curriculums_list" style="float: left;">
    '. View::factory('curriculums/list', array('data' => null, 'pagination' => null)). '
  </div>
  ';
echo '
  <div style="clear: both;">';
?>

<script>
  $(document).ready(function() {
    $('table.majors').on('click', 'tr', function(){
      $('table.majors tr.active').removeClass('active');
      
      $(this).addClass('active');
      
      id = $(this).children('input[type="hidden"][name="id"]').val();
      
      $('#btn_curriculum_manage').attr('href', "curriculums/manage/major/" + id);
      
      $.get(
        "curriculums/list/major/" + id,
        {},
        function(data){
          $("#curriculums_list").html(data);
        }
      )
    });
    
    <?php if ($model->count() > 0) { ?>
      //  первая строка подсвечивается как активная
      $('table.majors tr input[type="hidden"][name="id"][value="<?php echo $model[0]->id; ?>"]').parent().addClass("active");
      //  загружаются книги, связанные с курсом из первой строки
      $.get(
        "curriculums/list/major/<?php echo $model[0]->id; ?>",
        {},
        function(data){
          $("#curriculums_list").html(data);
        }
      );
    <?php } ?>
      
  })
</script>
