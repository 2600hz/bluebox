<?php
echo "All dates ";
print form::radio('date_range', 'false', true);
echo " Date Range ";
print form::radio('date_range', 'true');
?>
<?php echo $grid ?>

<p class="loud">Export Options</p>
<?php
echo "XLS ";
print form::radio('export_type', 'xls', true);
echo "PDF ";
print form::radio('export_type', 'pdf');
echo "CSV ";
print form::radio('export_type', 'csv');
echo "HTML ";
print form::radio('export_type', 'html');
?>
<hr />
<a href="#lame" class="button">Export Report</a>

<fieldset>
<legend>RSS Feed Settings</legend>
<p>Monitor call log history using a RSS client</p>
<ul>
<li>http://www.awesome.com/rss/auth/1qazxsw23edcvfr45tgbnhy67ujm</li>
</ul>
<hr />
<a href="#lame" class="button">Regenerate Authentication Token</a>
</fieldset>

