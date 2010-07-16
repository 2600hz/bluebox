<div id="mediamanager">
    <div id="filelist" style="float:left;width:20%">
    <?php

    echo $filetree;

    ?>
        <br/>
        <a href="#">Create Folder</a><br/>
    </div>

    <div id="grid" style="float:right;width:80%">
        <?php if (isset($gridMenu)): ?>

            <div class="sub_menu">
                <?php echo $gridMenu; ?>
            </div>

        <?php endif; ?>

        <?php if (isset($grid)) echo $grid; ?>

        <div style="width:100%; margin: 10px;">&nbsp;</div>
    </div>
    
    <div style="clear:both">&nbsp;</div>
</div>

<?php javascript::codeBlock(NULL, FALSE); ?>
function filterPath(path) {
    $('#MediaGrid')[0].p.postData._search = true;
    $('#MediaGrid')[0].p.postData.searchField="m.path";
    $('#MediaGrid')[0].p.postData.searchOper="eq";
    $('#MediaGrid')[0].p.postData.searchString=path;
    $('#MediaGrid').trigger('reloadGrid');
    $('#MediaGrid').setCaption(path);
}
<?php javascript::blockEnd(); ?>
