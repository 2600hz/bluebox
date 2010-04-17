<div class="border margin">
<table border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <th scope="col"><div align="center">Sun</div></th>
    <th scope="col"><div align="center">Mon</div></th>
    <th scope="col"><div align="center">Tue</div></th>
    <th scope="col"><div align="center">Wed</div></th>
    <th scope="col"><div align="center">Thu</div></th>
    <th scope="col"><div align="center">Fri</div></th>
    <th scope="col"><div align="center">Sat</div></th>
    <th scope="col">Routes</th>
    <th scope="col">From</th>
    <th scope="col">To</th>
    <th scope="col"><div align="center"></div></th>
  </tr>
<?php 
  foreach($timeofday as $i => $time_of_day_value) {
?>
  <tr>
    <td><div align="center"><?php echo form::checkbox("timeofday[$i][wday][]", '1', TimeOfDayManager::isDayActive($timeofday[$i]['wday'], 1));?></div></td>
    <td><div align="center"><?php echo form::checkbox("timeofday[$i][wday][]", '2', TimeOfDayManager::isDayActive($timeofday[$i]['wday'], 2));?></div></td>
    <td><div align="center"><?php echo form::checkbox("timeofday[$i][wday][]", '3', TimeOfDayManager::isDayActive($timeofday[$i]['wday'], 3));?></div></td>
    <td><div align="center"><?php echo form::checkbox("timeofday[$i][wday][]", '4', TimeOfDayManager::isDayActive($timeofday[$i]['wday'], 4));?></div></td>
    <td><div align="center"><?php echo form::checkbox("timeofday[$i][wday][]", '5', TimeOfDayManager::isDayActive($timeofday[$i]['wday'], 5));?></div></td>
    <td><div align="center"><?php echo form::checkbox("timeofday[$i][wday][]", '6', TimeOfDayManager::isDayActive($timeofday[$i]['wday'], 6));?></div></td>
    <td><div align="center"><?php echo form::checkbox("timeofday[$i][wday][]", '7', TimeOfDayManager::isDayActive($timeofday[$i]['wday'], 7));?></div></td>
    <td><?php echo numbering::destinationsDropdown("timeofday[$i][routes_to]") ;?></td>
    <td><?php echo form::hourmin("timeofday[$i][from_hr]", "timeofday[$i][from_min]", "timeofday[$i][from_pm]", $from_hval[$i], $from_mval[$i], $from_pval[$i]);?></td>
    <td><?php echo form::hourmin("timeofday[$i][to_hr]", "timeofday[$i][to_min]", "timeofday[$i][to_pm]", $to_hval[$i], $to_mval[$i], $to_pval[$i]);?></td>
    <td><div align="center"></div></td>
<?php
   }
?>
  </tr>
</table>
<input type="hidden" id="timeofday_elements_id" value="$timeOfDayCount">
</div>
