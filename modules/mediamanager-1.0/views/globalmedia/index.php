<div id="mediamanager">
    <div id="filelist" style="float:left;width:20%">
    <?php

    echo $filetree;

    ?>
        <p style="margin-top: 10px"><a href="#" onClick="clearPath()"><?php echo __('Show All Files'); ?></a></p>

        <p style="margin-top: 10px"><?php echo html::anchor('globalmedia/scan', 'Rescan Folders', array('class' => 'qtipAjaxForm')); ?></p>
        
        <p style="margin-top: 10px"><?php echo html::anchor('globalmedia/createFolder', 'Create Folder'); ?></p>
    </div>

    <div class="topbar">
        <h1><?php echo __('Media Manager'); ?></h1>
        <p><?php echo __('These files are accessible to all users of the system.'); ?></p>
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

function clearPath() {
    $('#MediaGrid')[0].p.postData._search = false;
    delete $('#MediaGrid')[0].p.postData.searchField;
    delete $('#MediaGrid')[0].p.postData.searchOper;
    delete $('#MediaGrid')[0].p.postData.searchString;
    $('#MediaGrid').trigger('reloadGrid');
    $('#MediaGrid').setCaption('');
}
<?php javascript::blockEnd(); ?>
