<?
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
$result = $swdb->get();
$DeviceRows = $result->as_array();

$swdb->from('trunk');
$swdb->select('name,trunk_id,server,plugins');
$result = $swdb->get();
$TrunkRows = $result->as_array();

$swdb->from('voicemail');
$swdb->select('name,voicemail_id,mailbox');
$result = $swdb->get();
$VoircemailRows = $result->as_array();

?>

<style>
<?php $DivHt = "100"; ?>
.sb-extin{float:left;width: 155px;height:<?=$DivHt?>px;background-color: #fff;padding-left:5px;}
.sb-extout{float:left;width: 155px;height:<?=$DivHt?>px;background-color: #fff;padding-left:5px;}
.sb-callcont{border: 2px solid #0088cc;height: <?=$DivHt?>px;float: left;}
.sb-confcont{border: 2px solid #00cccc;height: <?=$DivHt?>px;float: left;}
.sb-autocont{border: 2px solid #88ff99;height: <?=$DivHt?>px;float: left;}
.sb-vmcont{border: 2px solid #00ff88;height: <?=$DivHt?>px;float: left;}

.sbarrow{float:left;height:<?=$DivHt?>px;}
.sb-grp{height:<?=$DivHt?>px;border: 1px solid #000;float: left;}
.sb-title{height:<?=$DivHt?>px;float: left;}
.sb-trunk{width: 125px;height: 175px;border: 1px solid #000;float: left;background-color: #acacac;}

.DeviceItem{width: 150px;height: 40px;border: 1px solid #000;float: left;background-color: #ffffcc;padding-top: 10px;}
.TrunkItem{width: 150px;min-height: 60px;border: 1px solid #000;float: left;background-color: #99cccc;}
.TrunkLable{width: 150px;border-bottom: 1px solid #acacac;padding-top: 2px;padding-right: 2px;font-weight: bold;color: #fff;}
.tsdiv{text-align: left;font-weight: bold;color: #fff;width: 150px;}

.devcallin{background: url('<?php url::base(); ?>/modules/switchboard-1.0/assets/images/devincall.gif');height: 20px;width: 23px;}
.devcallout{background: url('<?php url::base(); ?>/modules/switchboard-1.0/assets/images/devoutcall.gif');height: 20px;width: 23px;}

.calldividercall{background: url('<?php url::base(); ?>/modules/switchboard-1.0/assets/images/connectgreen.gif');float:left;height:<?=$DivHt?>px;width: 26px;}
.calldividertrunk{
background: url('<?php url::base(); ?>/modules/switchboard-1.0/assets/images/connecttrunk.gif');
float:left;height:<?=$DivHt?>px;width: 26px;font-size: 10px;line-height:12px;
}

.nocallsop{background: url('<?php url::base(); ?>/modules/switchboard-1.0/assets/images/nocallsop.gif');width: 550px;height:550px;}
.devstat{float: right;padding-left: 3px;}

</style>
<div class="column-container" style="min-height:800px;">
    <div class="column-sides">
	<div id="opActionPan" style="width: 900px;">
	<!--<input type="button" value="Show list" onclick="ShowDebug();" /> -->
	<div id="debugsw"></div>
    
	<input type="hidden" value="" name="suuid" size="50" id="idssuid">
	 <div id="debug_tab" style="width: 900px;padding-bottom:15px;">
            <div style="width: 900px;">
		<input type="button" value=" Clear Selected " onclick="clearselected();" />
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
		<?php echo form::button(array('id' => 'manual_hold', 'param' => 'version', 'class' => 'switchboardEvent', 'value' => '--Hold--'));?>
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
		
		$t= $ExtraArray['callerid']['internal_number'];
		$Ctx = "context_" . $DeviceRows[$idex]->context_id;
		echo "<div class=\"DeviceItem\" id=\"device" . $t . "\" onclick=\"SelectDevice('device" . $t . "','" . $t . "','" . $Ctx . "');\">" . $DeviceRows[$idex]->name . "<div id=\"stats$t\" class=\"devstat\"></div></div>";
	}
	?>
	</div>
	<div style="width:590px;min-height:550px;float:left;border: 1px solid #000;">
	<div id="op-channels"></div>
	</div>
	<div id="TrunkList" style="width:150px;float:left;min-height: 500px;">
	<?php
	for($idex=0;$idex<count($TrunkRows);$idex++) {
		echo "<div class=\"TrunkItem\"><div class=\"TrunkLable\">" . $TrunkRows[$idex]->name . "</div><div class=\"tsdiv\" id=\"ts" . $TrunkRows[$idex]->trunk_id  .  "\"></div></div>";
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
    
    // this is used to get the epoc time differance from the web server to the client.
    var EpocServer = <?=date("U"); ?>;
    var d = new Date();
    var EpocClient = (d.getTime()-d.getMilliseconds())/1000;
    var EpocDiff = EpocServer - EpocClient;
    
    var g_DeviceCount = 0;
    var g_DevListArray = new Array();
    //--------------------------------------------------------------  Items to javascript
    // could make all this stuff object/classes at some point.
    <?php
	for($idex=0;$idex<count($DeviceRows);$idex++) {
		$ExtraArray = unserialize( $DeviceRows[$idex]->plugins );
		
		$t= $ExtraArray['callerid']['internal_number'];
		$Ctx = "context_" . $DeviceRows[$idex]->context_id;
		echo "g_DevListArray[g_DeviceCount++] = '$t';\n";
	}
	?>
	
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
		echo "g_TrunkStatusMathArray[g_TrunkCount++] = '" . $ExtraArray['sip']['username']   . "@" . $TrunkRows[$idex]->server . "';\n";
	}
	?>

    
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
		document.getElementById('ts'+ g_TrunkArray[idex]).innerHTML ="" + TrunkParseStatus( trunkStatsChunk );
	}
}

function ChanToTrunkName(InnameString) {
	var nameString = '' + InnameString; //simple conversion of there is an issue
	for(var idex=0;idex<g_TrunkArray.length;idex++) {
		if( nameString.indexOf( g_TrunkIpArray[idex] ) != -1 ) {
			return g_TrunkNameArray[idex];
		}
		if( nameString.indexOf( 'trunk_' + g_TrunkArray[idex] ) != -1 ) {
			return g_TrunkNameArray[idex];
		}
	}
	return "";
}

function TrunkParseStatus( Inval ) {
	if( Inval.indexOf("FAIL_WAIT") != -1){ return '<img src="<?php url::base(); ?>/modules/switchboard-1.0/assets/images/trunkred.gif" /> Failed: Waiting'; } 
	if( Inval.indexOf("REGED") != -1){ return '<img src="<?php url::base(); ?>/modules/switchboard-1.0/assets/images/trunkgreen.gif" /> UP'; } 
	if( Inval.indexOf("UNREGED") != -1){ return '<img src="<?php url::base(); ?>/modules/switchboard-1.0/assets/images/trunkyellow.gif" /> Failed: Waiting'; } 
	if( Inval.indexOf("REGISTER") != -1){ return '<img src="<?php url::base(); ?>/modules/switchboard-1.0/assets/images/trunkyellow.gif" /> Failed: Waiting'; } 
	if( Inval.indexOf("FAILED") != -1){ return '<img src="<?php url::base(); ?>/modules/switchboard-1.0/assets/images/trunkred.gif" />Failed'; } 
	if( Inval.indexOf("FAILED (retry") != -1){ return '<img src="<?php url::base(); ?>/modules/switchboard-1.0/assets/images/trunkred.gif" /> Failed: Retry'; } 
	return "unknown";
}

function ShowDebug() {
	var OutBug = "";
	for(var idex=0;idex<g_GroupsHTML.length;idex++) {
	OutBug = OutBug + g_GroupsHTML[idex] + '<br />';
	}
	for(var idex=0;idex<g_ChanList.length;idex++) {
			
			OutBug = OutBug + "grpname: " + g_ChanList[idex]['grpname'] ;
			OutBug = OutBug + "grptype: " + g_ChanList[idex]['grptype'] ;
	}
	
	document.getElementById('debugsw').innerHTML = OutBug;
}


function SelectDevice( divid,devid,devcontext ) {
	document.getElementById(divid).style.backgroundColor = "#ffff00";
	if( g_DevDivID != "" ) {
		document.getElementById(g_DevDivID).style.backgroundColor = "#ffffcc";
	}
	g_DevDivID = divid;
	g_DevID = devid;
	g_DevContext = devcontext;
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


function RebuildCommands( inuuid ) {
	document.getElementById('idssuid').value = inuuid;
	document.getElementById('manual_entry_param').value = "uuid_kill " + inuuid;
	document.getElementById('manual_entry_park').value = "uuid_kill " + inuuid;
	document.getElementById('manual_entry_kill'	).value = "uuid_park " + inuuid;
	document.getElementById('manual_entry_hold').value = "uuid_hold " + inuuid;
	document.getElementById('manual_entry_xfer'	).value = "uuid_transfer " + inuuid + " -both " + g_DevID + " xml " + g_DevContext;
	var sndfile = document.getElementById('media_widget_file_list').value
	document.getElementById('manual_entry_sound').value = "uuid_broadcast " + inuuid + " " + sndfile + " both";
	document.getElementById('manual_entry_record').value = "uuid_record " + inuuid + " start /usr/local/freeswitch/recordings"+ inuuid + "wav";


}

function selectuuid(inelement,inuuid) {
	RebuildCommands( inuuid ) ;
	document.getElementById(inuuid).style.backgroundColor = "#ffff00";
	if(  g_SelectedUUID1 != "" ) {
		document.getElementById(g_SelectedUUID1).style.backgroundColor = "#ffffff";
	}
	g_SelectedUUID1 = inuuid;
}
    
    $(function() {
       // $("#tabs").tabs();

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
		if( BuiltChanHTML == "" ){ BuiltChanHTML = '<div class="nocallsop"></div>';}
            $("#op-channels").html(BuiltChanHTML);
	    RefreshSelectedDivs();
	    UpdateDeviceList() ;
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
			if( NewData[idex].substr(0,4) == "uuid" && NameCols == "")
			{
				NameCols =NewData[idex] + ',grpname,grptype' ;
				continue;
			}
		
			if( NameCols != "" &&  NewData[idex].indexOf(",") != -1)
			{
				ChanList[idex-1] = splitLoadChan( NameCols, NewData[idex]+ ',grpname,grptype' );
			 }
		}
		
	
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
			if( myblocktype == "call" ) {
				RetHTML = RetHTML + '<div class="sb-callcont"><div class="sb-title"><div class="sbarrow"><img src="<?php url::base(); ?>/modules/switchboard-1.0/assets/images/callintintcat.gif"></div></div>';
			}
			if( myblocktype == "conference" ) {
				RetHTML = RetHTML + '<div class="sb-confcont"><div class="sb-title"><div class="sbarrow"><img src="<?php url::base(); ?>/modules/switchboard-1.0/assets/images/confcat.gif"></div></div>';
			}
			if( myblocktype == "auto_attendant" ) {
				RetHTML = RetHTML + '<div class="sb-autocont"><div class="sb-title"><div class="sbarrow"><img src="<?php url::base(); ?>/modules/switchboard-1.0/assets/images/autocat.gif"></div></div>';
			}
			
			if( myblocktype == "voicemail" ) {
				RetHTML = RetHTML + '<div class="sb-vmcont"><div class="sb-title"><div class="sbarrow"><img src="<?php url::base(); ?>/modules/switchboard-1.0/assets/images/vmcat.gif"></div></div>';
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
		var CurrentID = "";
		
		if( InChanArray['call_uuid'] == ""){
			CurrentID = InChanArray['uuid'] ;
		}
		else
		{
			CurrentID = InChanArray['call_uuid'] ;
		}

		if(  InChanArray['direction']  == "inbound" ) {
		
			//let see if we have any of the trunk ips related in name
			var UsingTrunkName = ChanToTrunkName( InChanArray['name'] );
			var newTrunkName = '<br /><br />' + verticalText(UsingTrunkName);
			if( DivType != "call" && UsingTrunkName != "") {
			RetHTML = RetHTML + '</div><div class="calldividertrunk">' + newTrunkName + '</div>';
			}
			if( DivType != "call" && UsingTrunkName == "") {
			RetHTML = RetHTML + '</div><div class="calldividercall">' + newTrunkName + '</div>';
			}
			RetHTML = RetHTML + '<div class="sb-extin" id="'+CurrentID +'" onclick="selectuuid(this,' + "'" +  CurrentID + "'" + ');">';
			RetHTML = RetHTML + "" +  InChanArray['cid_name'] + "<br />";
			RetHTML = RetHTML + "" +  InChanArray['cid_num'] + "<br />";
			RetHTML = RetHTML + '<div id="' + id + '"><script>DivClickTimer(' + InChanArray['created_epoch']+' ,' + id + ');<\/script>' +  '</div>';
			
			if( DivType == "voicemail") {
				RetHTML = RetHTML + "Voicemail: " +  InChanArray['dest'] + "<br />";
			}
			
			RetHTML = RetHTML + "Call State:" +  InChanArray['callstate'] + "<br />";
			
			if( getTrunkName(InChanArray['application_data']) != false) {
				RetHTML = RetHTML + "Trunk: " +  getTrunkName(InChanArray['application_data'] )+ "<br />";
			}
			
			
			if( DivType == "call" && UsingTrunkName == "") {
			RetHTML = RetHTML + '</div><div class="calldividercall">' + newTrunkName + '</div>';
			}
			if( DivType == "call" && UsingTrunkName != "") {
			RetHTML = RetHTML + '</div><div class="calldividertrunk">' + newTrunkName + '</div>';
			}
		}
		
		if(  InChanArray['direction']  == "outbound" ) {
			RetHTML = RetHTML + '<div class="sb-extout" id="'+ CurrentID +'" onclick="selectuuid(this,' + "'" +  CurrentID  + "'" + ');">';
			RetHTML = RetHTML + "" +  InChanArray['callee_name'] + "<br />";
			RetHTML = RetHTML + "" +  InChanArray['callee_num'] + "<br />";
			RetHTML = RetHTML + '<div id="' + id + '"><script>DivClickTimer(' + InChanArray['created_epoch']+' ,' + id + ');<\/script>' +  '</div>';
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
	
	function GroupData(ArrayIn) {
		var grptype 	= "";
		var searchtype 	= "";
		var retgrptype 	= "";
		if( ArrayIn['application_data'].substr(0,11) == "conference_" 		) {  grptype = "conference";searchtype = "conference_" +GetConfUUIDPart(ArrayIn['uuid'] );  }
		if( ArrayIn['application_data'].substr(0,15) == "auto_attendant_" 	) {  grptype = "auto_attendant"; searchtype = "auto_attendant_" + ArrayIn['application_data'];  }
		if( ArrayIn['application_data'].substr(0,5) == "sofia" 			) {  grptype = "call"; searchtype = ArrayIn['call_uuid'];  }
		if( ArrayIn['application_data'].substr(0,4) == "user" 			) {  grptype = "call"; searchtype = ArrayIn['call_uuid'];  }
		if( ArrayIn['application_data'].substr(0,4) == "local" 			) {  grptype = "call"; searchtype = ArrayIn['call_uuid'];  }
		if( ArrayIn['application_data'].substr(0,18) == "default voicemail_" 	) {  grptype = "voicemail"; searchtype = "voicemail" + ArrayIn['uuid'];  }
		if( grptype == "" 									) {  grptype = "call"; searchtype = ArrayIn['call_uuid'];  }
		
		
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
	
	function GetBlockType( InVal ) {
		if( typeof ( InVal) == "undefined" 		) { return "unknown"; } 
		if( InVal.substr(0,11) == "conference_" 		) {  return "conference"; }
		if( InVal.substr(0,15) == "auto_attendant_" 	) {  return "auto_attendant";}
		if( InVal.substr(0,9) == "voicemail" 		) {  return "voicemail";}
		return "call";
	}
	function GetConfUUIDPart( InVal ) {
		var RetVal = "";
		var SData = InVal.split("-");
		
		return SData[SData.length];
	}
	
	function verticalText(inText) {
		var retVal = "";
		for(var i=0;i< inText.length;i++) {
			retVal = retVal + " " + inText.substr(i,1);
		}
		return retVal;
	}
	
	//------------------------------------------------------   Timer Functions ------------------------------------------//
	function DivClickTimer( InStartPoc,divid ) {
		try{ //need to do this cos the timer might have went away before the funtion got called
		document.getElementById( divid).innerHTML = ShowTime( InStartPoc );
		setTimeout( "DivClickTimer( " + InStartPoc + ", '" + divid + "');",1000);
		}catch(e){}
	}
	function ShowTime( InEpoc ) {
		var d = new Date(); 
		var newtime = (d.getTime()-d.getMilliseconds())/1000;
		newtime = EpocDiff + newtime;
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
</script>
<?php
    jquery::addPlugin('tabs');
?>