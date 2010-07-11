<div id="contextmanager_update_header" class="txt-center update contextmanager module_header">

    <h2><?php echo $title; ?></h2>

</div>

<div id="contextmanager_update_form" class="update contextmanager">

    <?php echo form::open(); ?>

    <?php echo form::open_section('Context Entry'); ?>

        <div class="field">
        <?php
            echo form::label('context[name]', 'Context Name:');
            echo form::input('context[name]');
        ?>
        </div>

    <?php echo form::close_section(); ?>

    <?php echo form::open_section('Outbound Routes'); ?>
   
        <div class="field">
        <?php
            echo form::label('context[registry][outbound]', 'Allow outbound calls:');
            echo form::checkbox('context[registry][outbound]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('context[registry][domestic]', 'Domestic:');
            echo form::dropdown('context[registry][domestic]', array(
                'Disabled',
                'Enabled',
               // 'Require Pin'
            ));
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('context[registry][directory]', 'Directory Assistance:');
            echo form::dropdown('context[registry][directory]', array(
                'Disabled',
                'Enabled',
               // 'Require Pin'
            ));
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('context[registry][international]', 'International:');
            echo form::dropdown('context[registry][international]', array(
                'Disabled',
                'Enabled',
               // 'Require Pin'
            ));
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('context[registry][tollfree]', 'Tollfree:');
            echo form::dropdown('context[registry][tollfree]', array(
                'Disabled',
                'Enabled',
                //'Require Pin'
            ));
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('context[registry][toll]', 'Toll:');
            echo form::dropdown('context[registry][toll]', array(
                'Disabled',
                'Enabled',
                //'Require Pin'
            ));
        ?>
        </div>

    <?php echo form::close_section(); ?>

    <?php echo form::open_section('Notifications'); ?>
    
        <div class="field">
        <?php
            echo form::label('context[registry][prepend_out]', 'Prepend outbound calls:');
            echo form::checkbox('context[registry][prepend_out]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('context[registry][prepend_in]', 'Prepend inbound calls:');
            echo form::checkbox('context[registry][prepend_in]');
        ?>
        </div>


    <?php echo form::close_section(); ?>

    <?php
        if (isset($views))
        {
            echo subview::renderAsSections($views);
        }
    ?>
    
    <div class="buttons form_bottom">

        <?php echo form::button(array('name' => 'submit', 'class' => 'cancel small_red_button'), 'Cancel'); ?>

        <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Save'); ?>

    </div>

    <?php echo form::close(); ?>
</div>