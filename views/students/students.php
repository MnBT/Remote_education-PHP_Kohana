<?php if (isset($debug)) var_dump($debug); ?>
<div  id="students_main_grid">
    <table class="students_table">

        <thead>
            <tr class="row_header" >
                <th id="grid_header_id" class="grid_header"><div id="create_new_student"></div></th>
                <th id="grid_header_username" class="grid_header"><div class="head_name"><?= __("Student") ?></div><div class="sort_arrow"></div></th>
    <th id="grid_header_filter" class="grid_header"><span class="grid_filter_icon"><img src="media/img/filter_selected_icon.gif"></span><div class="head_name"><?= __("Major") ?></div><div class="sort_arrow"></div></th>
            </tr>
        </thead>
        <!--   <div class="row_filter">
              <div><input id="filter_id" class="filter_field" type="text" name="filter_id" onchange="submit();"></div>
              <div><input id="filter_username" class="filter_field" type="text" name="filter_username" onchange="submit();"></div>
           </div>-->
        <tbody>
            <?php foreach ($students as $student) { ?>
                <tr id="students_main_grid_row_<?= $student["id"] ?>" class="grid_row">
                    <td  class="grid_cell grid_col_id"><?= $student["id"] ?></td>
                    <td class="grid_cell grid_col_username"><div class="grid_coll_value"><?= $student["username"] ?></div></td>
                    <td class="grid_cell grid_col_major"></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <?= $pagination ?>
</div>
<div id="wrap_grid_details"><?= $studentsdetails; ?></div>



<script>
    //initial params
    var selectedUserId = '<?= $selectedUserId; ?>';
    var colNameToSort = '<?= $colNameToSort; ?>';
    var methodToSort = '<?= $methodToSort; ?>';
    var activeFilter = '<?= $activeFilter; ?>';

    //find currently selected user and add corresponding css to it
    $('#students_main_grid_row_' + selectedUserId).addClass('current');
    $('#grid_header_' + colNameToSort).addClass('sorted');
    
    if (activeFilter)
        $('.menuitem .counters .'+activeFilter).addClass('active');
    
    //Add onclick events on rows - to process user select
    $('#students_main_grid .grid_row').each(function(index, value){
        //add onclick event with ajax request
        $(this).on('click', function (event) {
            event.preventDefault();
            var id = $(this).find('.grid_col_id').html();

            studentsScope.selectStudent(id, selectedUserId, function () {
                selectedUserId = id;
            }, $(this));

            return false;
        });
        return;
    });
    $('.menuitem .counters .total,.menuitem .counters .new').bind('click',function(){
        studentsScope.enroll_filter('students', $(this).attr('value'));
    })

    //Add onclick events on columns - to process sorting
    $('#students_main_grid .grid_header').each(function(index, value){
        //add onclick event with ajax request
        $(this).on('click', function(event) {
            event.preventDefault();
            var column = $(this).attr('id').substr(12);
            if (column == 'id') return;
            var method = (colNameToSort == column) ? ((methodToSort == 'ASC') ? 'DESC' : 'ASC') : 'ASC';

            studentsScope.sort(column, method, colNameToSort, function () {
                colNameToSort = column;
                methodToSort = method;
            }, $(this));
            return false;
        });
        return;
    });

    //Add onclick on Filter button with ajax request
    $("#students_filter .filter_button").on('click', function(event) {
        event.preventDefault();
        var column =$("#students_main_grid_filter_column option:selected").text();
        var value = $("#students_main_grid_filter_value").attr('value');
        studentsScope.filter(column, value);
        return false;
    });
   
    $('#create_new_student').bind('click',function() {
        $('#createStudent').css('display','block');
        var gridHeight = $('#createStudent').height();
        $('#saveStuden').css('top',gridHeight-30);
        $('#saveStuden').css('left',90);
        $('#gridClose').css('left',77);
    });
    
    $('.students_tabs .menuitem').live('mouseover',function(){
        if ($(this).attr('class') != 'menuitem_profiles menuitem current')
            $(this).addClass('mousemove');
    })
    $('.students_tabs .menuitem').live('mouseout',function(){
        $(this).removeClass('mousemove');
    })
    $('#create_new_student').live('mouseover',function(){
        $(this).addClass('rotate');
    })
    $('#create_new_student').live('mouseout',function(){
        $(this).removeClass('rotate');
    })
    
</script>
