<?php
/**
 * THIS IS NASTY BUT FUNCTIONAL
 */
 function createTree($array, $base = '')
{
    foreach ($array as $key => $value)
    {
        if (is_array($value)) {
            echo '<li><span class="folder">' .$key .'</span><ul>';
            if (!empty($base)) $key = $base . $key;
            createTree($value, $key .'/');
            echo '</ul></li>';
        } else if (is_null($value)) {
            echo '<li><span class="folder">' .$key .'</span></li>';
        } else {
            extract(pathinfo($key));
            echo '<li><span class="file"><a href="' . $base . $key . '" class="show_file">' . $key .'</a></span></li>';
        }
    }
}

echo '<div style="float:left; display: block; margin-right: 10px; padding-right: 10px; border-right: 1px solid gray;">';
echo '<ul id="provisioner" class="filetree">';
createTree($tree);
echo '</ul></div>';
echo '<div id="file_contents" style="overflow: auto;"></div>';
echo '<div style="clear:both;">&nbsp;</div>';

    jquery::addPlugin(array('treeview', 'blockUI'));
?>

<?php javascript::codeBlock(); ?>
    // when an ajax query starts block the UI and ends unblock
    $(document).ajaxStart(function () {
        $.blockUI({ message: '<div class="thinking"><?php  echo __('Please Wait...'); ?></div>' })
        }).ajaxStop($.unblockUI);
    $('.show_file').click(function (e) {
        e.preventDefault();
        $.post('<?php echo url::site('provisioner/get'); ?>', {
            'type':'file',
            'mac':'<?php echo $mac; ?>',
            'file':$(this).attr('href')
        },
        function (data) { $('#file_contents').html(data); }, 'html');
    });
    $('#provisioner').treeview({ collapsed: true });
<?php javascript::blockEnd(); ?>