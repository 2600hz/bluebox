<div id="contents"> </div>
<script type="text/javascript">
    window.onload=function () {setInterval("request_update()",5000); request_update();}

    function request_update() {
	
        if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp=new XMLHttpRequest();
        } else {// code for IE6, IE5
                xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=update_notification;
        xmlhttp.open("GET","<?php print $url; ?>?cascade=true&nocache="+(new Date).valueOf(),true);
        xmlhttp.send();
    }

    function update_notification() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            res=JSON.parse(xmlhttp.responseText);
            update_received();
        }
    }

    function update_received() {
        recurse(res.value,1,document.getElementById("contents"));
    }

    function recurse(res,depth,div) {
        var newdiv,newh;
        div.innerHTML='';
        for (var r=0; r<res.length; r++) {
            if (res[r].tag=='grouping') {
                newh=document.createElement('h'+depth.toString());
                newh.innerHTML=res[r].attributes.description;
                div.appendChild(newh);
                newdiv=document.createElement('div');
                newdiv.className="grouping";
                div.appendChild(newdiv);
                if (typeof(res[r].value)!='undefined') {
                    recurse(res[r].value,depth+1,newdiv);
                }
            } else {
                var newdiv2;
                newdiv=document.createElement('div');
                newdiv.className='direntry';

                newdiv2=document.createElement('div');
                newdiv2.className='dirname direntry'+res[r].attributes.state;
                newdiv2.innerHTML=res[r].attributes.description;

                newdiv.appendChild(newdiv2);
                newdiv2=document.createElement('div');
                newdiv2.className='dirextension';
                newdiv2.innerHTML=res[r].attributes.extension;

                newdiv.appendChild(newdiv2);

                
                div.appendChild(newdiv);
            }
        }
    }

</script>
<?php 
if (isset($lists)) { 
	foreach ($lists AS $listname=>$listitems) {
		echo "<hr><div><h1>$listname</h1><div class='grouping'>";
		foreach ($listitems AS $number=>$name) { ?>
			<div class="direntry">
				<div class="dirname"><?php echo $name ?></div>
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
