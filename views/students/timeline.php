<div id="timeline_canvas_wrap">
   <iframe id="iframe" src="media/raphael.html" frameborder="0"
           style="width: 100%; height: 540px;overflow: hidden; border:none;">
   </iframe>

</div>
<div id="timeline_operations_wrap" style="width: 100%; height:600px;">
   <h2 id="operations_grid_title"><?= __("Statement of Operations") ?></h2>
   <table id="operations_grid" xmlns="http://www.w3.org/1999/html">
      <thead>
      <tr class="row_header">
         <th id="grid_header_date" class="grid_header grid_col_date"><?= __("Date") ?></th>
         <th id="grid_header_time" class="grid_header grid_col_time"><?= __("Time") ?></th>
         <th id="grid_header_type" class="grid_header grid_col_type"><?= __("Type") ?></th>
         <th id="grid_header_description" class="grid_header grid_col_description"><?= __("Description") ?></th>
         <th id="grid_header_income" class="grid_header grid_col_income"><?= __("Income") ?></th>
         <th id="grid_header_expenses" class="grid_header grid_col_expenses"><?= __("Expenses") ?></th>
         <th id="grid_header_blocked" class="grid_header grid_col_blocked"><?= __("Blocked") ?></th>
         <th id="grid_header_balance" class="grid_header grid_col_balance"><?= __("Balance") ?></th>
      </tr>
      </thead>
      <tbody>
      <?php
      foreach ($operations as $operation) {
         ?>

      <tr id="operations_grid_row_<?= $operation["id"]?>" class="grid_row">
         <td class="grid_cell grid_col_date"><?= $operation["date"] ?></td>
         <td class="grid_cell grid_col_time"><?= $operation["time"] ?></td>
         <td class="grid_cell grid_col_type"><?= $operation["type"] ?></td>
         <td class="grid_cell grid_col_description"><?= $operation["description"] ?></td>
         <td class="grid_cell grid_col_income"><?= $operation["income"] ?></td>
         <td class="grid_cell grid_col_expenses"><?= $operation["expenses"] ?></td>
         <td class="grid_cell grid_col_blocked"><?= $operation["blocked"] ?></td>
         <td class="grid_cell grid_col_balance"><?= $operation["balance"] ?></td>
      </tr>

         <?php } ?>
   </table>
</div>
<script>
   item = $.parseJSON('<?= $graphData ?>');
</script>