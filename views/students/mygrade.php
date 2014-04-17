<h1 id="tab_mygrade_header_tab_name"><?= __("My Grades");?></h1>
<h2 id="tab_mygrade_header_student_name"><?= __("Student: ") . $studentName ?></h2>
<h2 id="tab_mygrade_header_student_id"><?= __("ID: ") . $studentID ?></h2>
<h2 id="tab_mygrade_header_major"><?= __("Major: ") . $major ?></h2>
<table id="mygrade_grid">
   <thead>
      <tr class="row_header" >
         <th id="grid_header_course_code" class="grid_header grid_col_course_code"><?= __("Course") ?></th>
         <th id="grid_header_course_name" class="grid_header grid_col_course_name"><?= __("Title") ?></th>
         <th id="grid_header_test_type" class="grid_header grid_col_test_type"><?= __("Type") ?></th>
         <th id="grid_header_attempt" class="grid_header grid_col_attempt"><?= __("Att") ?></th>
         <th id="grid_header_start_date" class="grid_header grid_col_start_date"><?= __("Start Date") ?></th>
         <th id="grid_header_end_date" class="grid_header grid_col_end_date"><?= __("End Date") ?></th>
         <th id="grid_header_checked" class="grid_header grid_col_checked"><?= __("Ch") ?></th>
         <th id="grid_header_check_date" class="grid_header grid_col_check_date"><?= __("Check Date") ?></th>
         <th id="grid_header_checked_by" class="grid_header grid_col_checked_by"><?= __("Checked By") ?></th>
         <th id="grid_header_num_of_questions" class="grid_header grid_col_num_of_questions"><?= __("NQ") ?></th>
         <th id="grid_header_score" class="grid_header grid_col_score"><?= __("Score") ?></th>
         <th id="grid_header_percentage" class="grid_header grid_col_percentage"><?= __("%") ?></th>
         <th id="grid_header_grade_point" class="grid_header grid_col_grade_point"><?= __("GP") ?></th>
         <th id="grid_header_letter_grade" class="grid_header grid_col_letter_grade"><?= __("LG") ?></th>
         <th id="grid_header_status" class="grid_header grid_col_status"><?= __("Status") ?></th>
         <th id="grid_header_num_of_comments" class="grid_header grid_col_num_of_comments"><?= __("Com") ?></th>
      </tr>
   </thead>
   <tfoot>
   <tr id="mygrade_grid_row_total" class="gris_row">
      <td colspan=11 ><?= __("Total: ") ?></td>
      <td ><?= $avgPercentage ?></td>
      <td ><?= $avgGradePoint ?></td>
      <td ><?= $avgLetterGrade ?></td>
      <td colspan=2>&nbsp</td>
   </tr>
   </tfoot>
   <tbody>
      <?php
      foreach ($grades as $grade) { ?>

         <tr id="mygrade_grid_row_<?= $grade["id"]?>" class="grid_row">
            <td class="grid_cell grid_col_course_code"><?= $grade["course_code"] ?></td>
            <td class="grid_cell grid_col_course_name"><?= $grade["course_name"] ?></td>
            <td class="grid_cell grid_col_test_type"><?= $grade["test_type"] ?></td>
            <td class="grid_cell grid_col_attempt"><?= $grade["attempt"] ?></td>
            <td class="grid_cell grid_col_start_date"><?= $grade["start_date"] ?></td>
            <td class="grid_cell grid_col_end_date"><?= $grade["end_date"] ?></td>
            <td class="grid_cell grid_col_checked"><?= $grade["checked"] ?></td>
            <td class="grid_cell grid_col_check_date"><?= $grade["check_date"] ?></td>
            <td class="grid_cell grid_col_checked_by"><?= $grade["checked_by"] ?></td>
            <td class="grid_cell grid_col_num_of_questions"><?= $grade["num_of_questions"] ?></td>
            <td class="grid_cell grid_col_score"><?= $grade["score"] ?></td>
            <td class="grid_cell grid_col_percentage"><?= $grade["percentage"] ?></td>
            <td class="grid_cell grid_col_grade_point"><?= $grade["grade_point"] ?></td>
            <td class="grid_cell grid_col_letter_grade"><?= $grade["letter_grade"] ?></td>
            <td class="grid_cell grid_col_status"><?= $grade["status"] ?></td>
            <td class="grid_cell grid_col_num_of_comments"><?= $grade["num_of_comments"] ?></td>
            <td class="grid_cell grid_col_action">
               <?= (isset($grade["id"])) ?
               '<div><a href="#"><img src="' . Kohana::$base_url .'media/img/view_result.png"></a></div>' .
               '<div><a href="#"><img src="' . Kohana::$base_url . 'media/img/add_comment.png"></a></div>' .
               '<div><a href="#"><img src="' . Kohana::$base_url . 'media/img/view_wrong.png"></a></div>' .
               '<div><a href="#"><img src="' . Kohana::$base_url . 'media/img/student_comment.png"></a></div>' .
               '<div><a href="#"><img src="' . Kohana::$base_url . 'media/img/appeal.gif"></a></div>' .
               '<div><a href="#"><img src="' . Kohana::$base_url . 'media/img/ask.png"></a></div>' .
               '<div><a href="#"><img src="' . Kohana::$base_url . 'media/img/add_correction.gif"></a></div>' : "&nbsp" ?>
            </td>
         </tr>
         <?/*= Kohana_Form::textarea('comment_grade_'.$grade["id"], $grade["comment"]) */?>
         <tr>
            <td colspan="16">
               <fieldset>
                  <legend> <?=__("Списано")/*$grade["comment_status"]*/ ?></legend>
                  <a href="#"><?= "+ " . __("Expand comment")?></a>
               </fieldset>
            </td>
         </tr>
      <?php } ?>
   </tbody>
</table>
<!--<script>
   var selectedUserId = '<?/*= $selectedUserId;*/?>';
   //find currently selected user and add corresponding css to it
   $('#mygrade_grid_row_' + selectedUserId).addClass('current');


   $('#mygrade_grid .grid_row').each(function(index, value){
      //add onclick event with ajax request
      $(this).live('click', function(event) {
         event.preventDefault();
         var id =$(this).attr('id').substr(17);

         $.ajax({
            type: 'POST',
            url: 'students/select' ,
            data: {id: id},
            success: function(response){
               $('#mygrade_grid_row_' + selectedUserId).removeClass('current');
               $('#mygrade_grid_row_' + id).addClass('current');
               $('#students_main_grid_row_' + selectedUserId).removeClass('current');
               $('#students_main_grid_row_' + id).addClass('current');
               selectedUserId = id;
            },
            statusCode: {
               404: function() {
                  alert("url not found");
               }
            }
         });
         return false;
      });
      return;
   });
</script>-->