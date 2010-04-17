<h2>Groups</h2>
<p>Groups allow you to control access to FreePBX</p>
<?php
echo $table;
echo form::open();
echo form::input('name');
echo form::submit('save', 'Add Group');
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
