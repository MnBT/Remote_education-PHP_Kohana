<table id="exams_grid" xmlns="http://www.w3.org/1999/html">
   <thead>
      <tr class="row_header" >
         <th id="grid_header_enroll_day" class="grid_header grid_col_enroll_day"><?= __("Day") ?></th>
         <th id="grid_header_current_test" class="grid_header grid_col_current_test"><?= __("Current Test") ?></th>
         <th id="grid_header_type" class="grid_header grid_col_type"><?= __("Type") ?></th>
         <th id="grid_header_attempt" class="grid_header grid_col_attempt"><?= __("Att") ?></th>
         <th id="grid_header_start_date" class="grid_header grid_col_start_date"><?= __("Start Date") ?></th>
         <th id="grid_header_end_date" class="grid_header grid_col_end_date"><?= __("End Date") ?></th>
         <th id="grid_header_checked" class="grid_header grid_col_checked"><?= __("Ch") ?></th>
         <th id="grid_header_check_date" class="grid_header grid_col_check_date"><?= __("Check Date") ?></th>
         <th id="grid_header_checked_by" class="grid_header grid_col_checked_by"><?= __("Checked By") ?></th>
         <th id="grid_header_percentage" class="grid_header grid_col_percentage"><?= __("%") ?></th>
         <th id="grid_header_grade_point" class="grid_header grid_col_grade_point"><?= __("GP") ?></th>
         <th id="grid_header_letter_grade" class="grid_header grid_col_letter_grade"><?= __("LG") ?></th>
         <th id="grid_header_status" class="grid_header grid_col_status"><?= __("Status") ?></th>
         <th id="grid_header_action" class="grid_header grid_col_action"><?= __("Act") ?></th>
      </tr>
   </thead>
   <tbody>
      <?php
      foreach ($students as $student) { ?>

         <tr id="exams_grid_row_<?= $student["id"]?>" class="grid_row">
            <td class="grid_cell grid_col_enroll_day"><?= $student["enroll_day"] ?></td>
            <td class="grid_cell grid_col_current_test"><?= $student["current_test"] ?></td>
            <td class="grid_cell grid_col_type"><?= $student["type"] ?></td>
            <td class="grid_cell grid_col_attempt"><?= $student["attempt"] ?></td>
            <td class="grid_cell grid_col_start_date"><?= $student["start_date"] ?></td>
            <td class="grid_cell grid_col_end_date"><?= $student["end_date"] ?></td>
            <td class="grid_cell grid_col_checked"><?= $student["checked"] ?></td>
            <td class="grid_cell grid_col_check_date"><?= $student["check_date"] ?></td>
            <td class="grid_cell grid_col_checked_by"><?= $student["checked_by"] ?></td>
            <td class="grid_cell grid_col_percentage"><?= $student["percentage"] ?></td>
            <td class="grid_cell grid_col_grade_point"><?= $student["grade_point"] ?></td>
            <td class="grid_cell grid_col_letter_grade"><?= $student["letter_grade"] ?></td>
            <td class="grid_cell grid_col_status"><?= $student["status"] ?></td>
            <td class="grid_cell grid_col_action"><?= ($student["current_test"] !== "&nbsp") ? '<div><a href="#"><img src="' . Kohana::$base_url .'media/img/fail.png"></a></div><div><a href="#"><img src="' . Kohana::$base_url . 'media/img/accept.png"></a></div>' : "&nbsp" ?></td>
         </tr>

      <?php } ?>
</table>
<script>
   var selectedUserId = '<?= $selectedUserId;?>';
   //find currently selected user and add corresponding css to it
   $('#exams_grid_row_' + selectedUserId).addClass('current');

   $('#exams_grid .grid_row').each(function(index, value){
      //add onclick event with ajax request
      $(this).live('click', function(event) {
         event.preventDefault();
         var id =$(this).attr('id').substr(15);

         $.ajax({
            type: 'POST',
            url: 'students/select' ,
            data: {id: id},
            success: function(response){
               $('#exams_grid_row_' + selectedUserId).removeClass('current');
               $('#exams_grid_row_' + id).addClass('current');
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
</script>