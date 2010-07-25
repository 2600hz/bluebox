<h3>

    <a href="#" rel="voicemail">Send to Voicemail</a>

</h3>

<div style="text-align: center;">

    If this call is not answered direct the caller to the voicemail box

    <?php
        echo vm::dropdown('number{{number_id}}[dialplan][terminate][voicemail]',
            isset($terminate['voicemail']) ? $terminate['voicemail'] : NULL
        );
    ?>

</div>