<?php print $conferences; ?>
<script type="text/javascript">
    res=<?php echo $res?>;
    usercache=<?php echo $usercache?>;
    window.onload=function () {request_update(); setTimeout("request_update()",<?php echo $updateinterval ?>); }

    function request_update() {
	
        if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp=new XMLHttpRequest();
        } else {// code for IE6, IE5
                xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=update_notification;
        xmlhttp.open("GET","<?php print $url; ?>",true);
        xmlhttp.send();
    }

    function update_notification() {
        if (xmlhttp.readyState==4) {
		if (xmlhttp.status==200) {
	            res=JSON.parse(xmlhttp.responseText);
        	    update_received(res);
		}
		setTimeout("request_update()",<?php echo $updateinterval ?>);
        }
    }
    function timechunk(seconds) {
	result=(seconds % 60).toString();
	if (result.length<2) {result="0"+result};
	seconds=(seconds/60)|0;
	result=(seconds % 60).toString()+":"+result;
	if (result.length<5) {result="0"+result};
	seconds=(seconds/60)|0;
	result=seconds.toString()+":"+result;
	if (result.length<8) {result="0"+result};
	return result;
    }
    function update_received(res) {
	divs=document.body.getElementsByTagName('div');
	for (i=0; i<divs.length; i++) {
		if (divs[i].className.indexOf('conference_div')!=-1) {
			confid=divs[i].id.substr(5);
			if (res['conf'][confid]==undefined) {
				divs[i].innerHTML='';
			} else {
				for (var device in res['conf'][confid]) {
					devid='conf'+confid+'_device'+device;
					newdiv=document.getElementById(devid);
					if (newdiv==null) {
						newdiv=document.createElement('div');
						newdiv.id=devid;
						newdiv.className='direntry device_div';
						divs[i].appendChild(newdiv);
					}
					jtime=timechunk(res['conf'][confid][device]["join_time"]);
					if (res['conf'][confid][device]["last_talking"]*1000<<?php echo $updateinterval ?>) {
						entryclass='dirname direntryIdle';
					} else {
						entryclass='dirname';
					}
					newdiv.innerHTML="<div class='"+entryclass+"'>"+usercache[device]["ext"]+" "+usercache[device]["name"]+
						"</div><div class='dirextension'>"+jtime+"</div>";
				}
				for (j=divs[i].childNodes.length-1; j>=0; j--) {
					id=divs[i].childNodes[j].id;
					if (res['conf'][confid][id.substr(id.indexOf('_device')+7)]==null) {
						divs[i].removeChild(divs[i].childNodes[j]);
					}
				}
			}
		}
	}
    }

</script>

<?php 
	if (isset($views)) {
		echo subview::renderAsSections($views);
	} 
?>
<div style='clear:both'></div>
