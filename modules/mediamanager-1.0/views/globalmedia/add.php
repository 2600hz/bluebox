<form name="upload" enctype="multipart/form-data" action="add" method="POST">
    Upload an audio file of format MP3 or WAV.<br/>
    <input type="file" name="upload"><br>



    <input type="submit">
    <b>Important: </b>On FreeSWITCH systems, the sample rate will be analyzed and your file will automatically be placed
    in a sub-folder on disk, such as 8000/ for 8000hz files, 16000/ for 16000hz files, etc. This helps to avoid transcoding when
    FreeSWITCH is handling calls on those same sample rates.<br/>
    <br/>
    To replace all existing sample rates with the file you are uploading, check the "replace file" box.<br/>
</form>
