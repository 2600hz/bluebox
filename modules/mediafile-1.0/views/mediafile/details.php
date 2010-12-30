    <?php echo form::open_section('File'); ?>

        <div id="media_files_tabs">

            <?php if (count($mediafiles) > 1): ?>

                <ul>
                     <?php foreach ($mediafiles as $mediafile): ?>

                         <li>
                             <a href="#sample_rate_<?php echo $mediafile['rates']; ?>">
                                 <span><?php echo $mediafile['rates']; ?></span>
                             </a>
                         </li>

                     <?php endforeach; ?>
                </ul>

            <?php endif; ?>

            <?php foreach ($mediafiles as $mediafile): ?>

                <div id="sample_rate_<?php echo $mediafile['rates']; ?>">

                    <div class="field">
                        <label class="label">Length: </label>
                        <span><?php echo $mediafile['length']; ?> sec</span>
                    </div>

                    <div class="field">
                        <label class="label">Filesize: </label>
                        <span><?php echo number_format($mediafile['size'], 0); ?> bytes</span>
                    </div>

                    <div class="field">
                        <label class="label">Type: </label>
                        <span><?php echo $mediafile['type'] . ' (' .$mediafile['bits'] .' bit)'; ?></span>
                    </div>

                    <div class="field">
                        <label class="label">Channels: </label>
                        <span><?php echo $mediafile['channels']; ?></span>
                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php echo form::close_section(); ?>

<?php
    if (count($mediafiles) > 1)
    {
        jquery::addPlugin('tabs');
        javascript::codeBlock('$("#media_files_tabs").tabs();');
    }
?>