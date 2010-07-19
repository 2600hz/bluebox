<form name="blah" enctype="multipart/form-data" action="add" method="POST">
    <input type="file" name="upload"><br>
    <input type="submit">
</form>

<div id="upload_button">Upload</div>

<script type="text/javascript">// <![CDATA[
$(document).ready(function() {

new AjaxUpload('upload_button', {
  // Location of the server-side upload script
  // NOTE: You are not allowed to upload files to another domain
  action: 'add',
  // File upload name
  name: 'upload',
  // Additional data to send
  data: {
    example_key1 : 'example_value',
    example_key2 : 'example_value2'
  },
  // Submit file after selection
  autoSubmit: true,
  // The type of data that you're expecting back from the server.
  // HTML (text) and XML are detected automatically.
  // Useful when you are using JSON data as a response, set to "json" in that case.
  // Also set server response type to text/html, otherwise it will not work in IE6
  responseType: false,
  // Fired after the file is selected
  // Useful when autoSubmit is disabled
  // You can return false to cancel upload
  // @param file basename of uploaded file
  // @param extension of that file
  onChange: function(file, extension){},
  // Fired before the file is uploaded
  // You can return false to cancel upload
  // @param file basename of uploaded file
  // @param extension of that file
  onSubmit: function(file, extension) {
      // Check that the filename and such is OK to upload
  },
  // Fired when file upload is completed
  // WARNING! DO NOT USE "FALSE" STRING AS A RESPONSE!
  // @param file basename of uploaded file
  // @param response server response
  onComplete: function(file, response) {}
});

});
// ]]></script>
