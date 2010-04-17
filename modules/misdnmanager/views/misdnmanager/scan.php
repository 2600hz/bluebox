<div class="clearfix"></div>
<table class="fancy" width="100%">
	<tr>
		<th width="10%">PCI Address</th>
		<th width="15%">PCI Subsystem ID</th>
		<th width="20%">Vendor</th>
		<th width="35%">Model</th>
		<th width="20%">Action</th>
	</tr>

<?php
if (is_array($cards))
{
    foreach ($cards as $card) {
        $action = html::anchor('misdnmanager/add/' . urlencode($card['subsys']) . '/' . urlencode($card['addr']), 'Add');
//        $action = html::anchor('misdnmanager/misdnmanager/add/123/456', 'Add');
        echo "  <tr>
    <td>{$card['addr']}</td>
    <td>{$card['subsys']}</td>
    <td>{$card['vendor']}</td>
    <td>{$card['model']}</td>
    <td>" . $action . "</td>
  </tr>\n";
    }
}
?>
</table>
