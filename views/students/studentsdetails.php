<div class="students_tab_content">
   <?php if (isset($content)) echo $content; ?>&nbsp
</div>
<script>
   var activeTab = '<?= (isset($activeTab)) ? $activeTab : false ?>';
   if(!!activeTab) {
      $(document).ready(function () {
         studentsScope.activateTab(activeTab);
      });
   }
   $('.students_tabs div.menuitem a').on('click', function (event) {
       
      event.preventDefault();
      var action = $(this).attr('href');
         studentsScope.activateTab(action);
      return false;
   });
</script>


