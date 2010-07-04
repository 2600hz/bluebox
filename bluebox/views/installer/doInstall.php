    <div id="noJS" class="error">
        <?php echo __('JavaScript is not enabled, please click next ONCE and wait for the page to reload.'); ?>
    </div>

    <?php echo form::open_section('Ready to ' .$process); ?>

                <div class="clickNext">

                    <?php echo __('Click next to ' . strtolower($process) . '...'); ?>
                    
                </div>

		<div class="inProgress" style="display:none;">
                    <div>
			<?php echo __('Please Wait...'); ?>
                    </div>
                    <?php
                        echo html::image('skins/installer/assets/img/installing.gif', 'Installing please wait...');
                    ?>
		</div>

		<input type="hidden" id="hidden_next" name="next" value="" />

    <?php echo form::close_section(); ?>


<?php
    if (class_exists('jquery') )
    {
        jquery::addQuery('#noJS')->hide();
        jquery::addQuery('#next_Next')->click('function () {
                $(\'#next_Next\').hide();
                $(\'#prev_Prev\').hide();
                $(\'.clickNext\').hide();
                $(\'.error\').hide();
                $(\'.inProgress\').show();
                $(\'#hidden_next\').val(\'next\');
                $(\'#installWizard\').submit();
                return true;
            }
        ');
    }
?>