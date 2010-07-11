<?php if (isset($gridMenu)): ?>

    <div class="sub_menu">
        <?php echo $gridMenu; ?>
    </div>

<?php endif; ?>

<div id="mediamanager">
    <div id="filelist" style="float:left;width:20%">
    <?php

    echo $filetree;

    ?>
    </div>

    <div id="grid" style="float:left;width:80%">
        <?php if (isset($grid)) echo $grid; ?>

        <div style="width:100%; margin: 10px;">&nbsp;</div>
    </div>
    
    <div style="clear:both">&nbsp;</div>
</div>

