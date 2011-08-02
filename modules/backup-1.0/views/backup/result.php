<?php
if($state == 1 || !isset ($state)){
	echo "Success ! <br />";
}else{
	echo $state."<br />";
}

echo html::anchor('backup/index', 'Import/Export');
?>
