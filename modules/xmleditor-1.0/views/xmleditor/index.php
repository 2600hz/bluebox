<div id="xmleditor">
    <div id="filelist">
    <?php

    echo $filetree;

    ?>
    </div>

    <div id="fileedit">
        <B>Current File: </B><span id="filename">None</span>
        <textarea id="textarea_1" cols="92" rows="45"></textarea>
    </div>
</div>

<small>Thanks to <a href="http://www.cdolivet.com/index.php?page=editArea" target="_blank">the folks who developed EditArea</a> for this great javascript editor.</small>

<?php javascript::codeBlock(); ?>
$(document).ready(function() {
    FileManager = {
        curfile:'',
        load: function(filename) {
            $.ajax({
              url: "load",
              data: "filename=" + filename,
              cache: false,
              success: function(html){
                editAreaLoader.setValue('textarea_1', html);
                FileManager.curfile = filename;
                $('span#filename').html(filename);
              }
            });
        },
        save: function() {
            if (FileManager.curfile != '') {
                //  attempt save
                $.ajax({
                   type: "POST",
                   url: "save",
                   contextType : "text/xml",
                   data: { filename : FileManager.curfile, data: editAreaLoader.getValue('textarea_1') } ,
                   success: function(msg){
                     alert( msg );
                   }
                 });
            } else {
                alert ('Need to load a file first.');
            }
        }
    }

    editAreaLoader.init({
            id : "textarea_1"		// textarea id
            ,syntax: "css"			// syntax to be uses for highgliting
            ,start_highlight: true		// to display with highlight mode on start-up
            ,font_size: 9
            ,save_callback: "FileManager.save"
            ,toolbar: "save, |, search, go_to_line, fullscreen, |, undo, redo, |, select_font,|, change_smooth_selection, highlight, reset_highlight, word_wrap, |, help"
    });


});

<?php javascript::blockEnd(); ?>
