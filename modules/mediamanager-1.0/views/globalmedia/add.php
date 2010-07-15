
<input id="fileInput" name="fileInput" type="file" />
<a href="javascript:$('#fileInput').uploadifyUpload();">Upload Files</a> | <a href="javascript:$('#fileInput').uploadifyClearQueue();">Clear Queue</a>

<script type="text/javascript">// <![CDATA[
$(document).ready(function() {
$('#fileInput').uploadify({
'uploader'  : 'uploadify',
'script'    : 'upload/<?php echo session_id(); ?> ',
'cancelImg' : 'cancel.png',
'multi'     : true,
});
});
// ]]></script>
