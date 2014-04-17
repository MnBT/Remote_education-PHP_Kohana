<table id="progress_grid">
   <thead>
      <tr class="row_header" >
         <th id="grid_header_enroll_day" class="grid_header grid_col_enroll_day"><?= __("Day") ?></th>
         <th id="grid_header_current_course" class="grid_header grid_col_current_course"><?= __("Current Course") ?></th>
         <th id="grid_header_version" class="grid_header grid_col_version"><?= __("Version") ?></th>
         <th id="grid_header_start_date" class="grid_header grid_col_start_date"><?= __("Start Date") ?></th>
         <th id="grid_header_download_date" class="grid_header grid_col_download_date"><?= __("Download") ?></th>
         <th id="grid_header_time_limit" class="grid_header grid_col_time_limit"><?= __("TL") ?></th>
         <th id="grid_header_deadline" class="grid_header grid_col_deadline"><?= __("Deadline") ?></th>
         <th id="grid_header_end_date" class="grid_header grid_col_end_date"><?= __("End Date") ?></th>
         <th id="grid_header_progress_days" class="grid_header grid_col_progress_days"><?= __("PD") ?></th>
         <th id="grid_header_progress_percents" class="grid_header grid_col_progress_percents"><?= __("P%") ?></th>
      </tr>
   </thead>
<!--   Day - поле дублируется из профиля - количество дней после Enrollment Date, после
   начала обучения. Это число пишется на графике под “сегодняшней датой”.
   По сути это количество учебных дней. Перерывы в обучении останавливают
   счетчик.
   Current Course - текущий курс, который изучает студент или если это первый курс,
   который он будет изучать. В зависимости от текущего курса на главной
   странице студента активирован тьютор этого курса.
   Version - при загрузке курса в систему, указывается версия этого файла. В
   дальнейшем версия данного курса может обновиться. В этом поле
   учитывается какую версию скачал студент на момент скачивания.
   Видимо, если студент не скачал учебник это поле пустое.
   Start Date - дата начала курса. Дата может быть в будущем. Это дата когда курс будет
   доступен для скачивания или был доступен для скачивания. Скачать курс
   студент может и позже но начало курса считается с этой даты.
   Download - дата загрузки курса студентом. После загрузки курса ссылка становится
   не активной. Если студент не смог скачать курс и ссылка стала не активной,
   он должен обратиться к консультанту для повторной активации данной
   ссылки, при этом начало курса Start Date не сдвигается.
   TL (Time Limit) - количество дней, срок на который рассчитан курс. Это число
   проставляется в настройках при загрузке админом нового курса.
   Deadline - дата окончания курса.
   End Date - дата когда студент закончил изучение курса. Студент сам обозначает эту
   дату. В этом случае P% останавливается на достигнутом значении. Пример
   на графике 45%.
   PD (Course Progress in Days) - количество дней с даты начала курса (Start Date). Если
   количество дней “0", то студент еще не может написать письмо тьютору.
   Будет всплывать стандартное сообщение. Связаться с тьютором можно будет
   после начала курса.
   P% (Course Progress in Percentage) - прогресс текущего курса в процентах. Данное
   значение отражается на графике (число 45).-->
   <tbody>
      <?php
      foreach ($students as $student) { ?>

         <tr id="progress_grid_row_<?= $student["id"]?>" class="grid_row">
            <td class="grid_cell grid_col_enroll_day"><?= $student["enroll_day"] ?></td>
            <td class="grid_cell grid_col_current_course"><?= $student["current_course"] ?></td>
            <td class="grid_cell grid_col_version"><?= $student["version"] ?></td>
            <td class="grid_cell grid_col_start_date"><?= $student["start_date"] ?></td>
            <td class="grid_cell grid_col_download_date"><?= $student["download_date"] ?></td>
            <td class="grid_cell grid_col_time_limit"><?= $student["time_limit"] ?></td>
            <td class="grid_cell grid_col_deadline"><?= $student["deadline"] ?></td>
            <td class="grid_cell grid_col_end_date"><?= $student["end_date"] ?></td>
            <td class="grid_cell grid_col_progress_days"><?= $student["progress_days"] ?></td>
            <td class="grid_cell grid_col_progress_percents"><?= $student["progress_percents"] ?></td>
         </tr>

      <?php } ?>
   </tbody>
</table>
<script>
   var selectedUserId = '<?= $selectedUserId;?>';
   //find currently selected user and add corresponding css to it
   $('#progress_grid_row_' + selectedUserId).addClass('current');


   $('#progress_grid .grid_row').each(function(index, value){
      //add onclick event with ajax request
      $(this).live('click', function(event) {
         event.preventDefault();
         var id =$(this).attr('id').substr(18);

         $.ajax({
            type: 'POST',
            url: 'students/select' ,
            data: {id: id},
            success: function(response){
               $('#progress_grid_row_' + selectedUserId).removeClass('current');
               $('#progress_grid_row_' + id).addClass('current');
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