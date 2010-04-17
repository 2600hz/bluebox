<?php
echo form::open();
echo form::open_fieldset();
echo form::legend('Permissions');

echo $table;
echo form::button('save', 'Save Permissions');
echo form::close_fieldset();
echo form::close();
?>
  <script>
  $(document).ready(function(){
    $("tr:even").css("background-color", "#bbbbff");
  });
  </script>
  <style>
  table {
    background:#eeeeee;
  }
  </style>

