<?php
/*
 * FreePBX Modular Telephony Software Library / Application
 *
 * Module:
 *
 * The contents of this file are subject to the Mozilla Public License
 * Version 1.1 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS"
 * basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
 * License for the specific language governing rights and limitations
 * under the License.
 *
 * The Initial Developer of the Original Code is Michael Phillips <michael.j.phillips@gmail.com>.
 *
 * Portions created by the Initial Developer are Copyright (C)
 * the Initial Developer. All Rights Reserved.
 *
 * Contributor(s):
 * Michael Phillips
 *
 *
 */
?>
<h3>CDR</h3>
<table>
<?php

foreach ($detailfields as $label => $key)
{
	echo '<tr><td width=\"300px\">' . $label . '</td><td>' . $details[$key] . '</td></tr>';
}
?>
</table>
<?php 
echo html::anchor('xmlcdr/downloadrec/' . $details['xml_cdr_id'] . '/csv', 'Export to CSV (Tab Delimited)') . '<br>';
echo html::anchor('xmlcdr/downloadrec/' . $details['xml_cdr_id'] . '/xml', 'Export to XML') . '<br>';

if (isset($views)) {
    echo subview::renderAsSections($views);
}
echo form::open();
echo form::close('ok_only');

?>
