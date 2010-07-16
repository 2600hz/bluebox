<p>A background request to scan your media files has been made.</p>
<br/>

<p>Your system will now look through all your media-related folders for any new/changed files. You will see the "last scan" at the top of the media manager page update when the scan is complete.</p>

<br/>

<div style="text-align:center">
    <?php
    echo form::button(array('name' => 'submit',
                            'class' => 'small_green_button',
                            'onClick' => "javascript:$('.qtip').hide();$('#qtip-blanket').fadeOut();"),
                      'Close'
            );
    ?>
</div>
