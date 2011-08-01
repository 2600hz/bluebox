<div id="numbermanager_update_form" class="update numbermanager">

    <?php echo form::open(); ?>

    <?php echo form::open_section('Configure Phone'); ?>

    Configure your phone by clicking an area on the phone.<br />

    <img src="http://imakys.teachoip.com/us/6757-carr%C3%A9+logo-aastra-%C3%A9cra.png" style="padding: 10px 10px 10px 10px">

    <div class="buttons form_bottom">

        <?php echo form::button(array('name' => 'submit', 'class' => 'cancel small_red_button'), 'Cancel'); ?>

        <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Save'); ?>

    </div>

    <?php echo form::close(); ?>

</div>
