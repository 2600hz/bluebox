<div>
    <h1>File Information</h1>
    <div class="field">
        <label class="label">Filename: </label>
        <span><?php echo basename($media['file']); ?></span>
    </div>

    <div class="field">
        <label class="label">Path: </label>
        <span><?php echo $media['path']; ?></span>
    </div>

    <div class="field">
        <label class="label">Filesize: </label>
        <span><?php echo number_format($media['registry']['size'], 0); ?> bytes</span>
    </div>

    <div class="field">
        <label class="label">Type: </label>
        <span><?php echo $media['registry']['type'] . ' (' . $media['registry']['bits'] . ' bit)'; ?></span>
    </div>

    <div class="field">
        <label class="label">Channels: </label>
        <span><?php echo $media['registry']['channels']; ?></span>
    </div>

    <div class="field">
        <label class="label">Sample Rates: </label>
        <span>
        <?php
        foreach ((array)$media['registry']['rates'] as $rate) {
            echo html::anchor('globalmedia/download/' . $mediaId . '/' . $rate, $rate . 'hz') . '&nbsp;&nbsp;';
        }
        ?>
         (Click to Download)</span>
    </div>

    <br/>

    <div style="text-align:center">
   <?php echo html::image(array('src' => 'globalmedia/visualize/' . $mediaId, 'width' => '400px', 'height' => '250px'), NULL, TRUE); ?>
        <br/>
        <audio src="<?php echo url_Core::file('globalmedia/download/' . $mediaId . '//TRUE', TRUE); ?>" controls="controls">Your browser does not support HTML5 audio.</audio>
    </div>

    <div style="text-align:center">
    <?php
    echo form::button(array('name' => 'submit',
                            'class' => 'small_green_button',
                            'onClick' => "javascript:$('.qtip').hide();$('#qtip-blanket').fadeOut();"),
                      'Close'
            );
    ?>
    </div>

</div>
