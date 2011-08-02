<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Switchboard - a Moded version of ESL... all the real work was done by the team that made ESL :)
 *
 * @author Mell Rosandich
 * @license LGPL
 * @package Switchboard
 * @contact mell@ourace.com
 */

// need to load all the Data for devices/trunks/voicemails.

$swdb = new Database();
$swdb->from('device');
$swdb->select('name,context_id,device_id,plugins');
$swdb->where('account_id',users::getAttr('account_id'));
$result = $swdb->get();
$DeviceRows = $result->as_array();

$swdb->from('trunk');
$swdb->select('name,trunk_id,server,plugins');
$swdb->where('account_id',users::getAttr('account_id'));
$result = $swdb->get();
$TrunkRows = $result->as_array();

$swdb->from('voicemail');
$swdb->select('name,voicemail_id,mailbox');
$swdb->where('account_id',users::getAttr('account_id'));
$result = $swdb->get();
$VoircemailRows = $result->as_array();

$swdb->from('context');
$swdb->select('context_id,account_id');
$swdb->where('account_id',users::getAttr('account_id'));
$result = $swdb->get();
$ContextRows = $result->as_array();


$DevListHeight = count($DeviceRows) * 80;
if( $DevListHeight < 500 ){ $DevListHeight= 500;}
?>
<style>

.calldividercall{background: url('<?php echo url::base(); ?>modules/switchboard-1.0/assets/images/connectgreen.gif');float:left;height:100px;width: 26px;}
.calldividertrunk{
	background: url('<?php echo url::base(); ?>modules/switchboard-1.0/assets/images/connecttrunk.gif');
	float:left;height:100px;width: 26px;font-size: 10px;line-height:12px;
}
.nocallsop{background: url('<?php echo url::base(); ?>modules/switchboard-1.0/assets/images/nocallsop.gif');width: 550px;min-height: <?php echo $DevListHeight;?>px}

.TrunkItemU{width: 150px;min-height: 75px;float: left;background: url('<?php echo url::base(); ?>modules/switchboard-1.0/assets/images/trunk_unknown_bg.gif');}
.TrunkItemG{width: 150px;min-height: 75px;float: left;background: url('<?php echo url::base(); ?>modules/switchboard-1.0/assets/images/trunk_good_bg.gif');}
.TrunkItemB{width: 150px;min-height: 75px;float: left;background: url('<?php echo url::base(); ?>modules/switchboard-1.0/assets/images/trunk_bad_bg.gif');}
.TrunkItemL{width: 150px;min-height: 75px;float: left;background: url('<?php echo url::base(); ?>modules/switchboard-1.0/assets/images/trunk_unknown_bg.gif');}
.TrunkDivider{width: 150px;min-height: 15px;float: left;}
.TrunkLable{width: 150px;font-weight: bold;color: #fff;text-align: center;}
.tsdiv{text-align: left;font-weight: bold;color: #000;width: 150px;}

.DeviceItem{
width: 150px;height: 80px;float: left;
background: url('<?php echo url::base(); ?>modules/switchboard-1.0/assets/images/leftitems_bgsmall.gif');
}
.DeviceItemSel{
width: 150px;height: 80px;float: left;
background: url('<?php echo url::base(); ?>modules/switchboard-1.0/assets/images/leftitems_bgsmall_sel.gif');
}
.DeviceItemhead{width: 150px;height: 14px;color: #fff;text-align: center;font-size: 10px;float: left;}
.DeviceItemlt{width: 7px;height: 66px;float: left;}
.devstat{float: bottom;width: 140px;;height: 20px;text-align: right;}
.devcallin{background: url('<?php echo url::base(); ?>modules/switchboard-1.0/assets/images/devincall.gif');height: 20px;width: 23px;float:right;}
.devcallout{background: url('<?php echo url::base();?>/modules/switchboard-1.0/assets/images/devoutcall.gif');height: 20px;width: 23px;float:right;}

.opspanleft{width: 10px;float: left;}
.opspanleftItem{width: 10px;height: 80px;float: left;}
</style>
<div class="column-container" style="min-height:800px;">
    <div class="column-sides">
	<div id="opActionPan" style="width: 900px;">
	<!--<input type="button" value="Show list" onclick="ShowDebug();" /> -->
	<div id="debugsw"></div>
    
	<input type="hidden" value="" name="suuid" size="50" id="idssuid">
	 <div id="debug_tab" style="width: 900px;padding-bottom:15px;">
            <div style="width: 900px;">
		<!--<input type="button" value=" Clear Selected " onclick="clearselected();" />-->
		<?php echo form::input(array('id' => 'manual_entry_param', 'value' => '','size' =>'80', 'type'=>'hidden')); ?>
		<?php echo form::input(array('id' => 'manual_entry_kill', 'value' => '','size' =>'80', 'type'=>'hidden')); ?>
		<?php echo form::input(array('id' => 'manual_entry_park', 'value' => '','size' =>'80', 'type'=>'hidden')); ?>
		<?php echo form::input(array('id' => 'manual_entry_xfer', 'value' => '','size' =>'80', 'type'=>'hidden')); ?>
		<?php echo form::input(array('id' => 'manual_entry_hold', 'value' => '','size' =>'80', 'type'=>'hidden')); ?>
		<?php echo form::input(array('id' => 'manual_entry_sound', 'value' => '','size' =>'80', 'type'=>'hidden')); ?>
		<?php echo form::input(array('id' => 'manual_entry_record', 'value' => '','size' =>'80', 'type'=>'hidden')); ?>
		
		<?php echo form::button(array('id' => 'manual_entry', 'param' => 'version', 'class' => 'switchboardEvent', 'value' => 'Send','style'=>'display:none'));?>
				
		<?php echo form::button(array('id' => 'manual_kill', 'param' => 'version', 'class' => 'switchboardEvent', 'value' => '--Kill--'));?>
		<?php echo form::button(array('id' => 'manual_park', 'param' => 'version', 'class' => 'switchboardEvent', 'value' => '--Park--'));?>
		<?php echo form::button(array('id' => 'manual_hold', 'param' => 'version', 'class' => 'switchboardEvent', 'value' => '--Put On Hold--'));?>
		<?php echo form::button(array('id' => 'manual_xfer', 'param' => 'version', 'class' => 'switchboardEvent', 'value' => '--Xfer--'));?>
		 <?php echo form::button(array('id' => 'manual_record', 'param' => 'version', 'class' => 'switchboardEvent', 'value' => '--Record--'));?>
		 <div style="float: right;">
		 <?php echo mediafiles::dropdown(array('name' => 'media[mediafile]', 'id' => 'media_widget_file_list')); ?>
		<?php echo form::button(array('id' => 'manual_sound', 'param' => 'version', 'class' => 'switchboardEvent', 'value' => '--Send Audio--'));?></div>
		
            </div>
	    </div>
        </div>
	<div id="sipinterfaces_tab">
            <div id="sip-interfaces"></div>
        </div>
		
        <div id="SwitchBoardContainer" style="width:900px;min-height: 500px;border: 1px solid #000;overflow-x: hidden;">
	
	<div id="DeviceList" style="width:150px;float:left;min-height: 500px;">
	<?php
	for($idex=0;$idex<count($DeviceRows);$idex++) {
		$ExtraArray = unserialize( $DeviceRows[$idex]->plugins );
		$SipOrInternalNumber="";
		
		if( array_key_exists( 'internal_number',$ExtraArray['callerid']) )
		{
			$SipOrInternalNumber= $ExtraArray['callerid']['internal_number'];
		}else {
				
			if( array_key_exists( 'username',$ExtraArray['sip']) )
			{
				$SipOrInternalNumber= $ExtraArray['sip']['username'];
			}
		}
		
		if( $SipOrInternalNumber != "" ) {
		$Ctx = "context_" . $DeviceRows[$idex]->context_id;
		echo "<div class=\"DeviceItem\" id=\"device" . $SipOrInternalNumber . "\" 
		onclick=\"SelectDevice('device" . $SipOrInternalNumber . "','" . $SipOrInternalNumber . "','" . $Ctx . "');\">
		<div class=\"DeviceItemhead\">$SipOrInternalNumber</div>
		<div class=\"DeviceItemlt\"></div>" . $DeviceRows[$idex]->name . 
		"<div id=\"stats$SipOrInternalNumber\" class=\"devstat\"></div></div>";
		}
	}
	?>
	</div>
	<div style="width:595px;min-height:550px;float:left;">
	<div id="op-channels" style="width:595px;min-height:550px;float:left;"></div>
	</div>
	<div id="TrunkList" style="width:150px;float:left;min-height: 500px;">
	<?php
	for($idex=0;$idex<count($TrunkRows);$idex++) {
		echo "<div class=\"TrunkItemL\"  id=\"ts" . $TrunkRows[$idex]->trunk_id  .  "\"><div class=\"TrunkLable\">" . $TrunkRows[$idex]->name . "</div><div class=\"tsdiv\" id=\"tst" . $TrunkRows[$idex]->trunk_id  .  "\"></div></div><div class=\"TrunkDivider\"></div>";
	}
	?>
	</div>
	<div id="trunkraw"></div>
       </div>
	
    </div>
</div>
<script type="text/javascript">

    var g_GroupsHTML = new Array();
    var g_GroupsHTMLTop = -1;
    var g_ChanList = new Array();
    var g_SelectedUUID1 = "";
    var g_SelectItem1 = "";
    var g_DevID = "";
    var g_DevContext = "";
    var g_DevDivID = "";
    var g_EpocDiff = getEpocTimeDiff();
    var g_DeviceCount = 0;
    var g_DevListArray = new Array();
    //--------------------------------------------------------------  Items to javascript
    // could make all this stuff object/classes at some point.
    <?php
	$g_OpsPanLeftPadLength = "<div class=\"opspanleft\">";
	for($idex=0;$idex<count($DeviceRows);$idex++) {
		$ExtraArray = unserialize( $DeviceRows[$idex]->plugins );
		$SipOrInternalNumber= "";
		if( array_key_exists( 'internal_number',$ExtraArray['callerid']) )
		{
			$SipOrInternalNumber= $ExtraArray['callerid']['internal_number'];
		}else {
		
			if( array_key_exists( 'username',$ExtraArray['sip']) )
			{
				$SipOrInternalNumber= $ExtraArray['sip']['username'];
			}
		}
		if( $SipOrInternalNumber!= "" ) {
			$Ctx = "context_" . $DeviceRows[$idex]->context_id;
			echo "g_DevListArray[g_DeviceCount++] = '$SipOrInternalNumber';\n";
			$g_OpsPanLeftPadLength .= "<div class=\"opspanleftItem\"></div>";
			
		}
	}
	$g_OpsPanLeftPadLength .= "</div>";
	?>
	var g_OpsPanLeftFiller = '<?php echo $g_OpsPanLeftPadLength?>';
	var g_TrunkCount = 0;
	var g_TrunkArray = new Array();
	var g_TrunkNameArray = new Array();
	var g_TrunkIpArray = new Array();
	var g_TrunkStatusMathArray = new Array();
	<?php
	for($idex=0;$idex<count($TrunkRows);$idex++) {
		$ExtraArray = unserialize( $TrunkRows[$idex]->plugins );
		echo "g_TrunkArray[g_TrunkCount] = '" . $TrunkRows[$idex]->trunk_id . "';\n";
		echo "g_TrunkNameArray[g_TrunkCount] = '" . addslashes( $TrunkRows[$idex]->name ). "';\n";
		echo "g_TrunkIpArray[g_TrunkCount] = '" . $TrunkRows[$idex]->server . "';\n";
		if( array_key_exists( 'username',$ExtraArray['sip']) )
		{
		echo "g_TrunkStatusMathArray[g_TrunkCount++] = '" . $ExtraArray['sip']['username']   . "@" . $TrunkRows[$idex]->server . "';\n";
		}else{
		echo "g_TrunkStatusMathArray[g_TrunkCount++] = '@" . $TrunkRows[$idex]->server . "';\n";
		}
	}
	?>
	var g_ContextCount = 0;
	var g_ContextArray = new Array();
	<?php
	for($idex=0;$idex<count($ContextRows);$idex++) {
		echo "g_ContextArray[g_ContextCount++] = 'context_" . $ContextRows[$idex]->context_id . "';\n";
	}
	?>
	
//if in this list then it is recording otherwise it is not recording
var g_RecordingList = new Array();
	
function RemoveFromRecordingArray( uuid ) {
	var TempRecArray = new Array();
	var TempChanX = 0;
	for(var idex=0;idex<g_RecordingList.length;idex++) {
		if( g_RecordingList[idex] != uuid ) {
			TempRecArray[TempChanX++] = g_RecordingList[idex];
		}
	}
	g_RecordingList = TempRecArray;
	TempChanArray = "";
}
	
function AddToRecordingArray( uuid ) {
	var ix = g_RecordingList.length +1;
	g_RecordingList[ix] = uuid;
}
function IsRecording( inuuid ) {
	for(var ix=0;ix<g_RecordingList.length;ix++) {
		if(  g_RecordingList[ix] == inuuid) {
			return true;
		}
	}
	return false;
}	
	
function IsInTheContext( InContext ) {
	for(var ix=0;ix<g_ContextArray.length;ix++) {
		if(  g_ContextArray[ix] == InContext) {
			return true;
		}
	}
	return false;
}	

//Disable some stuff until proper selections are made
function CheckButtonStates() {
	document.getElementById('manual_kill').disabled = true;
	document.getElementById('manual_park').disabled = true;
	document.getElementById('manual_hold').disabled = true;
	document.getElementById('manual_xfer').disabled = true;
	document.getElementById('manual_record').disabled = true;
	document.getElementById('manual_sound').disabled = true;
	
	if( g_SelectedUUID1 != "" ) {
	
		document.getElementById('manual_kill').disabled = false;
		document.getElementById('manual_park').disabled = false;
		document.getElementById('manual_hold').disabled = false;
		document.getElementById('manual_record').disabled = false;
		document.getElementById('manual_sound').disabled = false;
		
		if( g_DevID != "" ) {
			document.getElementById('manual_xfer').disabled = false;
		}
	}
}

function inValidateGlobalUUID() {
	if( isUUIDStillValid( g_SelectedUUID1) == false ) {
		g_SelectedUUID1 = "";
	}
}

function isUUIDStillValid( inuuid ) {
	for(var ichan=0;ichan<g_ChanList.length;ichan++) {
		if(  g_ChanList[ichan]['uuid']  == inuuid) {
			return true;
		}
		if(  g_ChanList[ichan]['call_uuid']  == inuuid) {
			return true;
		}
	}
	return false;
}

function UpdateDeviceList() {
	for(var idex=0;idex<g_DevListArray.length;idex++) {
		document.getElementById('stats'+ g_DevListArray[idex]).innerHTML = "idle";
		for(var ichan=0;ichan<g_ChanList.length;ichan++) {
				//		callee_name callee_num cid_name  cid_num
			if( g_DevListArray[idex] == g_ChanList[ichan]['callee_name'] ) {
				document.getElementById('stats'+ g_DevListArray[idex]).innerHTML = '<div class="devcallin"></div>';
			}
				if( g_DevListArray[idex] == g_ChanList[ichan]['callee_num'] ) {
			document.getElementById('stats'+ g_DevListArray[idex]).innerHTML = '<div class="devcallin"></div>';
			}
				if( g_DevListArray[idex] == g_ChanList[ichan]['cid_name'] ) {
			document.getElementById('stats'+ g_DevListArray[idex]).innerHTML =  '<div class="devcallout"></div>';
			}
			if( g_DevListArray[idex] == g_ChanList[ichan]['cid_num'] ) {
				document.getElementById('stats'+ g_DevListArray[idex]).innerHTML = '<div class="devcallout"></div>';
			}
		}
	}
}

function UpdateTrunkList(indata) {
	for(var idex=0;idex<g_TrunkArray.length;idex++) {
		var trunkSipLoc = indata.indexOf( g_TrunkStatusMathArray[idex] ) + g_TrunkStatusMathArray[idex].length;
		var trunkStatsChunk = indata.substr(trunkSipLoc,16);
		var TrunkClassStatus = TrunkParseStatus( trunkStatsChunk );
		var TrunkClassStatusText = TrunkParseStatusText( trunkStatsChunk )
		document.getElementById('ts'+ g_TrunkArray[idex]).setAttribute("class",TrunkClassStatus);
		document.getElementById('tst'+ g_TrunkArray[idex]).innerHTML = '&nbsp;&nbsp;' + TrunkClassStatusText;
	}
}

//this will compare all the trunk ips and the trunk_ids and return a name
function ChanToTrunkName(InnameString,inDataString) {
	var nameString = '' + InnameString; //simple conversion of there is an issue
	var nameStringData = '' + inDataString;//
	for(var idex=0;idex<g_TrunkArray.length;idex++) {
	
		if( nameString.indexOf( g_TrunkIpArray[idex] ) != -1 ) {
			return g_TrunkNameArray[idex];
		}
		var trunkconcate = "trunk_" + g_TrunkArray[idex];
		
		if( nameString.indexOf(  trunkconcate ) != -1 ||  nameStringData.indexOf(  trunkconcate ) != -1) {
			return g_TrunkNameArray[idex];
		}
	}
	return "";
}

//Parse the esl sip respone and look for key words
function TrunkParseStatus( Inval ) {
	if( Inval.indexOf("FAIL_WAIT") != -1){ return 'TrunkItemB'; } 
	if( Inval.indexOf("REGED") != -1){ return 'TrunkItemG'; } 
	if( Inval.indexOf("UNREGED") != -1){ return 'TrunkItemG'; } 
	if( Inval.indexOf("REGISTER") != -1){ return 'TrunkItemU'; } 
	if( Inval.indexOf("FAILED") != -1){ return 'TrunkItemB'; } 
	if( Inval.indexOf("FAILED (retry") != -1){ return 'TrunkItemB'; } 
	return "TrunkItemU";
}

function TrunkParseStatusText( Inval ) {
	if( Inval.indexOf("FAIL_WAIT") != -1){ return 'FAIL_WAIT'; } 
	if( Inval.indexOf("REGED") != -1){ return 'REGED'; } 
	if( Inval.indexOf("UNREGED") != -1){ return 'UNREGED'; } 
	if( Inval.indexOf("REGISTER") != -1){ return 'REGISTER'; } 
	if( Inval.indexOf("FAILED") != -1){ return 'FAILED'; } 
	if( Inval.indexOf("FAILED (retry") != -1){ return 'FAILED (retry)'; } 
	return "Unknown";
}

function ShowDebug() {
	var OutBug = "";
	for(var idex=0;idex<g_GroupsHTML.length;idex++) {
	OutBug = OutBug + g_GroupsHTML[idex] + '<br />';
	}
	for(var idex=0;idex<g_ChanList.length;idex++) {
			
			OutBug = OutBug + "<li>grpname: " + g_ChanList[idex]['grpname'] ;
			OutBug = OutBug + "<li>grptype: " + g_ChanList[idex]['grptype'] ;
	}
	
	document.getElementById('debugsw').innerHTML = OutBug;
}


function SelectDevice( divid,devid,devcontext ) {

	//document.getElementById(divid).style.backgroundColor = "#ffff00";
	if( g_DevDivID == divid ) {
		//we are unslecting the device
		document.getElementById(g_DevDivID).setAttribute("class","DeviceItem");
		divid = "";
		devid = "";
		devcontext = "";
	}
	else
	{
		document.getElementById(divid).setAttribute("class","DeviceItemSel");
		if( g_DevDivID != "" ) {
			//document.getElementById(g_DevDivID).style.backgroundColor = "#ffffcc";
			document.getElementById(g_DevDivID).setAttribute("class","DeviceItem");
		}
	}
	g_DevDivID = divid;
	g_DevID = devid;
	g_DevContext = devcontext;
	CheckButtonStates() ;
	RebuildCommands( g_SelectedUUID1 ); // need to rebuild the string since we changed the xfer or new device
}

function RefreshSelectedDivs() {
	if(  g_SelectedUUID1 != "" ) {
		try{
		document.getElementById(g_SelectedUUID1).style.backgroundColor = "#ffff00";
		}catch(e)
		{
			g_SelectedUUID1 = "";
		}
	}
}

function clearselected() {
	if(  g_SelectedUUID1 != "" ) {
		try{
		document.getElementById(g_SelectedUUID1).style.backgroundColor = "#ffffff";
		}catch(e)
		{
			g_SelectedUUID1 = "";
		}
	}
	g_SelectedUUID1 = "";
}

//this is called to sey up the ESL commands
function RebuildCommands( inuuid ) {
	var HoldStatus = GetHoldStatusOffOn( inuuid );
	document.getElementById('manual_hold').innerHTML = "--Put On Hold--";
	if( HoldStatus == "off " ) {
		document.getElementById('manual_hold').innerHTML = "--Take off Hold--";
	}
	
	document.getElementById('idssuid').value = inuuid;
	document.getElementById('manual_entry_param').value = "uuid_kill " + inuuid;
	document.getElementById('manual_entry_kill').value = "uuid_kill " + inuuid;
	document.getElementById('manual_entry_park'	).value = "uuid_park " + inuuid;
	document.getElementById('manual_entry_hold').value = "uuid_hold " + HoldStatus + inuuid;
	document.getElementById('manual_entry_xfer'	).value = "uuid_transfer " + inuuid + " -both " + g_DevID + " xml " + g_DevContext;
	var sndfile = document.getElementById('media_widget_file_list').value
	document.getElementById('manual_entry_sound').value = "uuid_broadcast " + inuuid + " " + sndfile + " both";
	document.getElementById('manual_entry_record').value = "uuid_record " + inuuid + " start /opt/freeswitch/recordings"+ inuuid + ".wav";
	CheckButtonStates() ;
}

//this will check the callstate and see if we hold or hold off( unhold) the call
function GetHoldStatusOffOn(inuuid) {
	var retVal = "";
	for(var idex=0;idex<g_ChanList.length;idex++) {
			if(g_ChanList[idex]['callstate'] == "HELD" && g_ChanList[idex]['uuid'] == inuuid ) {
			return "off ";
			}
	}
	return retVal;
}

function selectuuid(inelement,inuuid) {

	if( inuuid == g_SelectedUUID1 ) {
		document.getElementById(g_SelectedUUID1).style.backgroundColor = "#ffffff";
		g_SelectedUUID1 = "";
		RebuildCommands( g_SelectedUUID1 ) ;
		return;
	}

	
	document.getElementById(inuuid).style.backgroundColor = "#ffff00";
	if(  g_SelectedUUID1 != "" ) {
		document.getElementById(g_SelectedUUID1).style.backgroundColor = "#ffffff";
	}
	g_SelectedUUID1 = inuuid;
	RebuildCommands( g_SelectedUUID1 ) ;
}
    
    $(function() {

        $(".switchboardEvent").click(function() {
            $.publish("switchboard/" + this.id, []);
        });
	
	$.subscribe("switchboard/manual_entry", function() {
            $.post('eslresponse', 'event=switchboard/manual_entry&param=' + $('#manual_entry_param').val(), function(output) {
                $.jGrowl(output, { theme: 'alert', life: 5000 });
            });
        });
	
	$.subscribe("switchboard/manual_kill", function() {
            $.post('eslresponse', 'event=switchboard/manual_entry&param=' + $('#manual_entry_kill').val(), function(output) {
                $.jGrowl(output, { theme: 'alert', life: 5000 });
            });
        });
	
	$.subscribe("switchboard/manual_park", function() {
            $.post('eslresponse', 'event=switchboard/manual_entry&param=' + $('#manual_entry_park').val(), function(output) {
                $.jGrowl(output, { theme: 'alert', life: 5000 });
            });
        });
	
	$.subscribe("switchboard/manual_xfer", function() {
            $.post('eslresponse', 'event=switchboard/manual_entry&param=' + $('#manual_entry_xfer').val(), function(output) {
                $.jGrowl(output, { theme: 'alert', life: 5000 });
            });
        });
	
	$.subscribe("switchboard/manual_hold", function() {
            $.post('eslresponse', 'event=switchboard/manual_entry&param=' + $('#manual_entry_hold').val(), function(output) {
                $.jGrowl(output, { theme: 'alert', life: 5000 });
            });
        });
	
	$.subscribe("switchboard/manual_sound", function() {
            $.post('eslresponse', 'event=switchboard/manual_entry&param=' + $('#manual_entry_sound').val(), function(output) {
                $.jGrowl(output, { theme: 'alert', life: 5000 });
            });
        });
	
	$.subscribe("switchboard/manual_record", function() {
            $.post('eslresponse', 'event=switchboard/manual_entry&param=' + $('#manual_entry_record').val(), function(output) {
                $.jGrowl(output, { theme: 'alert', life: 5000 });
            });
        });
	
	  $.subscribe("switchboard/channels", function(channels) {
		var BuiltChanHTML = buildchanhtml( channels )
		if( BuiltChanHTML == "" ){ BuiltChanHTML = '<div id=\"divnocallsop\"class="nocallsop"></div>';}
            $("#op-channels").html(g_OpsPanLeftFiller + BuiltChanHTML);
	    inValidateGlobalUUID(); // if the uuid is not it the list anymore set the global selected UUID to nothing
	    RefreshSelectedDivs();	//since we have a new set of data we need to reselect the divs because they have been rewritten
	    UpdateDeviceList();		//This will update the devices on the left status, idle, inbound outbound etc..
	    RebuildCommands();		// this repopulates the commands with the UUID
	    //this is needed because of multiple updates
	    setTimeout( "RebuildCommands();",500); // some times we have lag
        });

	 $.subscribe("switchboard/sipinterfaces", function(interfaces) {
		UpdateTrunkList(interfaces) ;
        });
	
	  $.subscribe("switchboard/error", function(error) {
            $.jGrowl(error, { theme: 'alert', life: 5000 });
        });

        $.flux("fluxresponse");
    });


	// this is where alot of the dirty work is done.
    	function buildchanhtml( indata ) {
		var NewData = indata.split("\n");
		var RetHTML = "";
		var NameCols = "";
		var ChanList = new Array();
		
		for(var idex=0;idex<NewData.length;idex++) {
			if(  NewData[idex].substr(0,4) == "uuid" && NameCols == "")
			{
				NameCols =NewData[idex] + ',grpname,grptype' ;
				continue;
			}
		
			if( NameCols != "" &&  NewData[idex].indexOf(",") != -1)
			{
				ChanList[idex-1] = splitLoadChan( NameCols, NewData[idex]+ ',grpname,grptype' );
			 }
		}
		//lets filter the array based on context
		
		var TempChanArray = new Array();
		var TempChanX = 0;
		for(var idex=0;idex<ChanList.length;idex++) {
			var InCon = IsInTheContext( ChanList[idex]['context'] );
			if( InCon == true ) {
				TempChanArray[TempChanX++] = ChanList[idex];
			}
		}
		ChanList = TempChanArray;
		TempChanArray = "";
		g_GroupsHTML = new Array();
		g_GroupsHTMLTop = -1;
		// lets group all the items by type: conference,auto attendent,call,etc..
		for(var idex=0;idex<ChanList.length;idex++) {
			 var GrpnametypeArray = GroupData(  ChanList[idex]  );
			ChanList[idex]['grpname'] = GrpnametypeArray[0];
			ChanList[idex]['grptype'] = GrpnametypeArray[1];
		}
		g_ChanList = ChanList;
		for(var idex=0;idex<g_GroupsHTML.length;idex++) {
			//build lead in div for type
			var myblocktype = GetBlockType( g_GroupsHTML[idex] ) ;
			var GroupCount = CountItemsInGroup(g_GroupsHTML[idex] );
			
			if( myblocktype == "call" ) {
				RetHTML = RetHTML + '<div class="sb-callcont'+GroupCount+'"><div class="sb-title"><div class="sbarrow"><img src="<?php echo url::base(); ?>modules/switchboard-1.0/assets/images/callintintcat.gif"></div></div>';
			}
			if( myblocktype == "conference" ) {
				RetHTML = RetHTML + '<div class="sb-confcont'+GroupCount+'"><div class="sb-title"><div class="sbarrow"><img src="<?php echo url::base(); ?>modules/switchboard-1.0/assets/images/confcat.gif"></div></div>';
			}
			if( myblocktype == "auto_attendant" ) {
				RetHTML = RetHTML + '<div class="sb-autocont'+GroupCount+'"><div class="sb-title"><div class="sbarrow"><img src="<?php echo url::base(); ?>modules/switchboard-1.0/assets/images/autocat.gif"></div></div>';
			}
			
			if( myblocktype == "voicemail" ) {
				RetHTML = RetHTML + '<div class="sb-vmcont'+GroupCount+'"><div class="sb-title"><div class="sbarrow"><img src="<?php echo url::base(); ?>modules/switchboard-1.0/assets/images/vmcat.gif"></div></div>';
			}
			
			if( myblocktype == "unknown" ) {
				continue;
			}
			for(var ids=0;ids<ChanList.length;ids++) {
			
				if( ChanList[ids]['grpname'] == g_GroupsHTML[idex] ) {
					RetHTML = RetHTML + BuildCallDivHTML( ChanList[ids] ,ids,myblocktype ) ;
				}
			 }
			RetHTML = RetHTML + '</div>';
		}
		return RetHTML;
	}
	
	//This will take the first chunk of data and use for the key part of the value
	function splitLoadChan( inHeaderList,inDataList) {
		var listNamesArray = inHeaderList.split(",");
		var listValuesArray = inDataList.split(",");
		var RetArray = new Array();
		for(var idex=0;idex<listNamesArray.length;idex++) {
			//arrays should be insync cos ESL sends back that data as comma delimited with fill
			if(  listValuesArray[idex] == "" )
			{
				RetArray[ listNamesArray[idex] ] = ""; //"none: " + listNamesArray[idex];
			}
			else
			{
				RetArray[ listNamesArray[idex] ] = listValuesArray[idex];
			}
		}
		return RetArray;
	}
	
	function BuildCallDivHTML( InChanArray,id,DivType ) {
		var RetHTML = "";
		var CurrentID = InChanArray['uuid'] ;
		
		if( CurrentID == "" || CurrentID == "" || CurrentID == "none" || CurrentID == "undefined" ) {
			CurrentID = InChanArray['call_uuid'] ;
		}
		
		if(  InChanArray['direction']  == "inbound" ) {
		
			//let see if we have any of the trunk ips related in name
			var UsingTrunkName = ChanToTrunkName( InChanArray['name'] ,InChanArray['application_data']);
			var newTrunkName = '<br /><br />' + verticalText(UsingTrunkName);
			
			if( DivType != "call" && UsingTrunkName != "") {
				RetHTML = RetHTML + '<div class="calldividertrunk">' + newTrunkName + '</div>';
			}
			if( DivType != "call" && UsingTrunkName == "") {
			RetHTML = RetHTML + '<div class="calldividercall">' + newTrunkName + '</div>';
			}
			
			RetHTML = RetHTML + '<div class="sb-extin" id="'+CurrentID +'" onclick="selectuuid(this,' + "'" +  CurrentID + "'" + ');">';
			RetHTML = RetHTML + "" +  InChanArray['cid_name'] + "<br />";
			RetHTML = RetHTML + "" +  InChanArray['cid_num'] + "<br />";
			RetHTML = RetHTML + '<div id="' + id + '"><script>DivClickTimer(' + InChanArray['created_epoch']+' ,' + id + ');<\/script>' +  '</div>';
			
			if( DivType == "voicemail") {
				RetHTML = RetHTML + "Voicemail: " +  InChanArray['dest'] + "<br />";
			}
			
			RetHTML = RetHTML + "Call State:" +  InChanArray['callstate'] + "<br />";
			
			//if( getTrunkName(InChanArray['application_data']) != false) {
			//	RetHTML = RetHTML + "Trunk: " +  getTrunkName(InChanArray['application_data'] )+ "<br />";
			//}
			RetHTML = RetHTML + "CT: " +  DivType+ "<br />";
			
			if( DivType == "call" && UsingTrunkName == "") {
			RetHTML = RetHTML + '</div><div class="calldividercall">' + newTrunkName + '</div>';
			}
			if( DivType == "call" && UsingTrunkName != "") {
			RetHTML = RetHTML + '</div><div class="calldividertrunk">' + newTrunkName + '</div>';
			}
			if( DivType != "call") {
			RetHTML = RetHTML + '</div>';
			}
		}
		
		if(  InChanArray['direction']  == "outbound" ) {
			var UsingTrunkName = ChanToTrunkName( InChanArray['name'] ,InChanArray['application_data']);
			var newTrunkName = '<br /><br />' + verticalText(UsingTrunkName);
			
			
			if( DivType == "call" && UsingTrunkName != "") {
				RetHTML = RetHTML + '<div class="calldividertrunk">' + newTrunkName + '</div>';
			}
			if( DivType == "call" && UsingTrunkName == "") {
				RetHTML = RetHTML + '<div class="calldividercall">' + newTrunkName + '</div>';
			}
			
			RetHTML = RetHTML + '<div class="sb-extout" id="'+ CurrentID +'" onclick="selectuuid(this,' + "'" +  CurrentID  + "'" + ');">';
			RetHTML = RetHTML + "" +  InChanArray['callee_name'] + "<br />";
			RetHTML = RetHTML + "" +  InChanArray['callee_num'] + "<br />";
			RetHTML = RetHTML + '<div id="' + id + '"><script>DivClickTimer(' + InChanArray['created_epoch']+' ,' + id + ');<\/script>' +  '</div>';
			RetHTML = RetHTML + "CT: " +  DivType+ "<br />";
			if( DivType == "voicemail") {
				RetHTML = RetHTML + "Voicemail: " +  InChanArray['dest'] + "<br />";
			}
			
			
			RetHTML = RetHTML + "Call State:" +  InChanArray['callstate'] + "<br />";
			
			if( getTrunkName(InChanArray['application_data']) != false){
				RetHTML = RetHTML + "Trunk: " +  getTrunkName(InChanArray['application_data']) + "<br />";
			}
			RetHTML = RetHTML + '</div>';
		}
		return RetHTML;
	}
	
	function CountItemsInGroup(GroupNameIn ) {
		var retVal = "";
		var grpoutcnt = 0;
		for(var idex=0;idex<g_ChanList.length;idex++) {
			if(g_ChanList[idex]['grpname'] ==GroupNameIn ) {
			grpoutcnt++; 
			}
		}
		if( grpoutcnt > 3 ){retVal = "2";}
		if( grpoutcnt > 6 ){retVal = "3";}
		if( grpoutcnt > 9 ){retVal = "4";}
		return retVal;
	}
	
	function GroupData(ArrayIn) {
		var grptype 	= "";
		var searchtype 	= "";
		var retgrptype 	= "";
		var TheUUID 	= UUIDSub(ArrayIn['uuid']);

		if( TheUUID == "" || TheUUID == "" || TheUUID == "none" || TheUUID == "undefined" ) {
			TheUUID = UUIDSub(ArrayIn['call_uuid']);
		}
		
		if( ArrayIn['application_data'].substr(0,11) == "conference_" ) {  
			grptype = "conference";searchtype = "conference_" +GetConfUUIDPart(TheUUID );  
		}
		if( ArrayIn['application_data'].substr(0,15) == "auto_attendant_") {  
			grptype = "auto_attendant"; searchtype = "auto_attendant_" + ArrayIn['application_data'];  
		}
		if( ArrayIn['application_data'].substr(0,5) == "sofia") {  
			grptype = "call"; searchtype = TheUUID;  
		}
		if( ArrayIn['application_data'].substr(0,4) == "user") {  
			grptype = "call"; searchtype = TheUUID;  
		}
		if( ArrayIn['application_data'].substr(0,4) == "local") {  
			grptype = "call"; searchtype = TheUUID;  
		}
		if( ArrayIn['application_data'].substr(0,18) == "default voicemail_" 	) {  
			grptype = "voicemail"; searchtype = "voicemail" + TheUUID;  
		}
		
		//lets set it to something so it shows since we didnt find a match we will you call
		if( grptype == "" ) {  
			grptype = "call"; searchtype = TheUUID;  
		}
		
		
		for(var idex=0;idex<g_GroupsHTML.length;idex++) {
			if( g_GroupsHTML[idex] == searchtype )
			{
				retgrptype = g_GroupsHTML[idex];
			}	
		}
		if( retgrptype == "" ) {
			if( g_GroupsHTMLTop == -1 ) { idex = -1;}
			g_GroupsHTML[idex+1] = searchtype;
			g_GroupsHTMLTop++;
			retgrptype= searchtype;
		}
		var retArray = new Array();
		retArray[0] = retgrptype;
		retArray[1] = grptype;
		return retArray;
	}
	
	function getTrunkName(inApplicationData) {
		if( inApplicationData.indexOf("gateway") != -1 ) {
			var SearchItems = inApplicationData.split("\/");
			for(var idex=0;idex<SearchItems.length;idex++) {
				if( SearchItems[idex] == "gateway" )
				{
					return SearchItems[idex+1];
				}
			}
		}
		return false;
	}
	
	//used for determining the type of block the call is in
	function GetBlockType( InVal ) {
		if( typeof ( InVal) == "undefined" 		) { return "unknown"; } 
		if( InVal.substr(0,11) == "conference_" 		) {  return "conference"; }
		if( InVal.substr(0,15) == "auto_attendant_" 	) {  return "auto_attendant";}
		if( InVal.substr(0,9) == "voicemail" 		) {  return "voicemail";}
		return "call";
	}
	
	//this breaks the UUID up by "-" and returns the last part
	function GetConfUUIDPart( InVal ) {
		var RetVal = "";
		var SData = InVal.split("-");
		return SData[SData.length-1];
	}
	
	//this breaks the UUID up by "-" and returns the last part
	function UUIDSub( InUUID ) {
		//this is the same as getconfuuid I seperated just incase I need to change it
		var RetVal = "";
		var SData = InUUID.split("-");
		return SData[SData.length-1];
	}
	
	
	
	//------------------------------------------------------   Misc Functions ------------------------------------------//
	
	//this is used for putting spaces between every letter in a trunk name.
	//so when we render the trunk images the text will flows down
	function verticalText(inText) {
		var retVal = "";
		for(var i=0;i< inText.length;i++) {
			retVal = retVal + " " + inText.substr(i,1);
		}
		return retVal;
	}
	
	
	
	
	
	//------------------------------------------------------   Timer Functions For clocks------------------------------------------//
	function DivClickTimer( InStartPoc,divid ) {
		try{ //need to do this cos the timer might have went away before the funtion got called
		document.getElementById( divid).innerHTML = ShowTime( InStartPoc );
		setTimeout( "DivClickTimer( " + InStartPoc + ", '" + divid + "');",1000);
		}catch(e){}
	}
	function ShowTime( InEpoc ) {
		var d = new Date(); 
		var newtime = (d.getTime()-d.getMilliseconds())/1000;
		newtime = g_EpocDiff + newtime;
		return secondsToTime( newtime - InEpoc);
	}
	
	function secondsToTime(secs )
	{
		var hours = Math.floor(secs / (60 * 60));
		var divisor_for_minutes = secs % (60 * 60);
		var minutes = Math.floor(divisor_for_minutes / 60);
		var divisor_for_seconds = divisor_for_minutes % 60;
		var seconds = Math.ceil(divisor_for_seconds);

		var fm = "";
		var fs = "";
		if(  minutes < 10 ){fm = "0";}
		if(  seconds < 10 ){fs = "0";}
		return hours + ":" + fm + minutes + ":" + fs + seconds;
	}
	
	// this is used to get the epoc time differance from the web server to the client.
	//we get the servers epoc and the client epoc at time of the page render.
	function getEpocTimeDiff() {
		var EpocServer = <?php echo date("U"); ?>;
		var d = new Date();
		var EpocClient = (d.getTime()-d.getMilliseconds())/1000;
		return  EpocServer - EpocClient;
	}
</script>
<?php
    //end of content
?>