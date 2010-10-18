<?php print $branches; ?>
<script type="text/javascript">
    res=<?php echo $res?>;
    window.onload=function () {setInterval("request_update()",<?php echo $updateinterval ?>); request_update();}

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
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            res=JSON.parse(xmlhttp.responseText);
            update_received(res);
        }
    }
    function update_received(res) {
	divs=document.body.getElementsByTagName('div');
	for (i=0; i<divs.length; i++) {
		if (divs[i].className.indexOf('device_div')!=-1) {
			state=res['ext'][divs[i].id.substr(7)];
			if (state==null) {
				state='Unavailable';
			}
			divs[i].getElementsByTagName('div')[0].className='dirname direntry'+state;
		}
	}
    }

</script>
<?php 
if (isset($lists)) { 
	foreach ($lists AS $listname=>$listitems) {
		echo "<hr><div><h1>$listname</h1><div class='grouping'>";
		foreach ($listitems AS $number=>$data) { ?>
			<div class="direntry">
				<div class="dirname"><?php echo $data['name'] ?></div>
				<div class="dirextension"><?php echo $number ?></div>
			</div>

<?php
		}
		echo "</div></div>";
	}
} ?>

<?php 
	if (isset($views)) {
		echo subview::renderAsSections($views);
	} 
?>
