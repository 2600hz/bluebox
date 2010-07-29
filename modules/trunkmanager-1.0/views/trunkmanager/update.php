<div id="trunk_update_header" class="update trunk module_header">

    <h2><?php echo $title; ?></h2>
    
</div>

<div id="trunk_update_form" class="update trunk">

    <?php echo form::open(); ?>
    
    <?php echo form::open_section('Trunk Information'); ?>

        <div class="field">
        <?php
            echo form::label('trunk[name]', 'Trunk Name:');
            echo form::input('trunk[name]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('trunk[type]', 'Trunk Type:');
            echo form::dropdown('trunk[type]', empty($supportedTrunkTypes) ? array() : $supportedTrunkTypes);
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('trunk[server]', 'Server:');
            echo form::input('trunk[server]');
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