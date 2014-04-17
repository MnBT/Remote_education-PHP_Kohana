<?php

echo '
  <div id="courses_list" style="float: left; margin: 0 10px 0 0;">
    '. View::factory('courses/list', array('model' => $model, 'pagination' => $pagination)). '
  </div>';
echo '
  <div id="books_list" style="float: left; width: 600px;">
    '. View::factory('books/list', array('model' => null, 'pagination' => null)). '
  </div>
  ';
echo '
  <div style="clear: both;">';

?>

<script>
  $(document).ready(function() {
    $('table.courses').on('click', 'tr', function(){
      $('table.courses tr.active').removeClass('active');
      
      $(this).addClass('active');
      
      id = $(this).children('input[type="hidden"][name="id"]').val();
      
      $.get(
        "books/list/" + id,
        {},
        function(data){
          $("#books_list").html(data);
        }
      )
    });
    
    //  первая строка подсвечивается как активная
    $('table.courses tr input[type="hidden"][name="id"][value="<?php echo $model[0]->id; ?>"]').parent().addClass('active');
    
    //  загружаются книги, связанные с курсом из первой строки
    $.get(
      "books/list/<?php echo $model[0]->id; ?>",
      {},
      function(data){
        $("#books_list").html(data);
      }
    );
  })
</script>
