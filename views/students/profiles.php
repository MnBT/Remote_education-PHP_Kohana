<table id="profiles_grid">
   <thead>
      <tr class="row_header" >
         <th id="grid_header_age" class="grid_header grid_col_age"><div class="head_name"><?= __("Age") ?></div><span class="sort_arrow_profiles"></span></th>
         <th id="grid_header_sex" class="grid_header grid_col_sex"><div class="head_name"><?= __("Sex") ?></div><span class="sort_arrow_profiles"></span></th>
         <th id="grid_header_country" class="grid_header grid_col_country"><div class="head_name"><?= __("Country") ?></div><span class="sort_arrow_profiles"></span></th>
         <th id="grid_header_city" class="grid_header grid_col_city"><div class="head_name"><?= __("City") ?></div><span class="sort_arrow_profiles"></span></th>
         <th id="grid_header_location" class="grid_header grid_col_location"><div class="head_name"><?= __("Location") ?></div><span class="sort_arrow_profiles"></span></th>
         <th id="grid_header_phone" class="grid_header grid_col_phone"><div class="head_name"><?= __("Phone") ?></div><span class="sort_arrow_profiles"></span></th>
         <th id="grid_header_email" class="grid_header grid_col_email"><div class="head_name"><?= __("Email") ?></div><span class="sort_arrow_profiles"></span></th>
         <th id="grid_header_lang" class="grid_header grid_col_lang"><div class="head_name"><?= __("Lang") ?></div><span class="sort_arrow_profiles"></span></th>
         <th id="grid_header_admit_date" class="grid_header grid_col_admit_date"><div class="head_name"><?= __("Admit Date") ?></div><span class="sort_arrow_profiles"></span></th>
         <th id="grid_header_admit_day" class="grid_header grid_col_admit_day"><div class="head_name"><?= __("Days") ?></div></th>
         <th id="grid_header_enroll_date" class="grid_header grid_col_enroll_date"><div class="head_name"><?= __("Enroll Date") ?></div><span class="sort_arrow_profiles"></span></th>
         <th id="grid_header_enroll_day" class="grid_header grid_col_enroll_day"><div class="head_name"><?= __("Day") ?></div><span class="sort_arrow_profiles"></span></th>
         <th id="grid_header_del" class="grid_header grid_col_del"><div class="head_name"><?= __("Del") ?></div></th>
         <th id="grid_header_re" class="grid_header grid_col_re"><div class="head_name"><?= __("Re.") ?></div><span class="sort_arrow_profiles"></span></th>
         <th id="grid_header_partner" class="grid_header grid_col_partner"><div class="head_name"><?= __("Partner") ?></div><span class="sort_arrow_profiles"></span></th>
      </tr>
   </thead>
   <tbody>
      <?php
      foreach ($students as $student) { ?>

         <tr id="profiles_grid_row_<?= $student["id"]?>" class="grid_row">
            <td class="grid_cell grid_col_age"><div class="grid_coll_value"><?= $student["birthday"] ?></div></td>
            <td class="grid_cell grid_col_sex"><div class="grid_coll_value"><?= $student["sex"] ?></div></td>
            <td class="grid_cell grid_col_country"><div class="grid_coll_value"><?= $student["country"] ?></div></td>
            <td class="grid_cell grid_col_city"><div class="grid_coll_value"><?= $student["city"] ?></div></td>
            <td class="grid_cell grid_col_location"><div class="grid_coll_value"><?= $student["location"] ?></div></td>
            <td class="grid_cell grid_col_phone"><div class="grid_coll_value"><?= $student["phone"] ?></div></td>
            <td class="grid_cell grid_col_email"><div class="grid_coll_value"><?= $student["email"] ?></div></td>
            <td class="grid_cell grid_col_lang"><div class="grid_coll_value"><?= $student["language"] ?></div></td>
            <td class="grid_cell grid_col_admit_date"><div class="grid_coll_value"><?= $student["admit_date"] ?></div></td>
            <td class="grid_cell grid_col_admit_day"><div class="grid_coll_value"><?= $student["admit_day"] ?></div></td>
            <td class="grid_cell grid_col_enroll_date"><div class="grid_coll_value"><?= $student["enroll_date"] ?></div></td>
            <td class="grid_cell grid_col_enroll_day"><div class="grid_coll_value"><?= $student["enroll_day"] ?></div></td>
            <td class="grid_cell grid_col_del"><div class="grid_coll_value"><?= $student["del"] ?></div></td>
            <td class="grid_cell grid_col_re"><div class="grid_coll_value"><?= $student["re"] ?></div></td>
            <td class="grid_cell grid_col_partner"><div class="grid_coll_value"><?= $student["partner"] ?></div></td>
         </tr>

      <?php } ?>
   </tbody>
</table>
<script>
   var selectedUserId = '<?= $selectedUserId;?>';
   //find currently selected user and add corresponding css to it
   $('#profiles_grid_row_' + selectedUserId).addClass('current');


   $('#profiles_grid .grid_row').each(function(index, value){
      //add onclick event with ajax request
      $(this).live('click', function(event) {
         event.preventDefault();
         var id =$(this).attr('id').substr(18);

         $.ajax({
            type: 'POST',
            url: 'students/select' ,
            data: {id: id},
            success: function(response){
               $('#profiles_grid_row_' + selectedUserId).removeClass('current');
               $('#profiles_grid_row_' + id).addClass('current');
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
   $('.students_tab_content .grid_row').each(function(i) {
        if (i % 2 === 0) {
            //$(this).css('background-color', '#ffffff');
            $(this).addClass('odd');
        } else {
            $(this).addClass('even');
        }
    })
</script>