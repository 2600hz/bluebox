<?php echo form::open_section('Welcome Mail'); ?>
    <div class="field">
    <?php
        echo form::label(array('for'=>'welcomemail[flag]'),'Send welcome email');
        echo form::checkbox(array('class'=>'determinant agent_for_welcomemail','name'=>'welcomemail[flag]'));
    ?>
    </div>

    <div class="field">
    <?php
        echo form::label(array('for'=>'welcomemail[address]'),'Email address');
	echo form::input(array('class'=>'dependent_positive rely_on_welcomemail','name'=>'welcomemail[emailaddress]'));
    ?>
    </div>

<?php echo form::close_section(); ?>
