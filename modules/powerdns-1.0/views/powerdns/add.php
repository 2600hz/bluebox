<div id="powerdns_add_header" class="add powerdns module_header">
    <h2><?php echo __($title); ?></h2>
</div>

<div id="powerdns_add_form" class="add powerdns">
    <?php echo form::open(); ?>

    <?php echo form::open_section('Domain Information'); ?>

        <div class="field">
        <?php
            echo form::label('pdnsdomain[name]', 'Name:');
            echo form::input('pdnsdomain[name]');
        ?>
        </div>

    <?php echo form::close_section(); ?>
    
    <?php echo form::open_section('SOA Record Information'); ?>

        <div class="field">
        <?php
            echo form::label('pdnsdomain[soa][primary]', 'Primary Nameserver:');
            echo form::input('pdnsdomain[soa][primary]', $primary);
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('pdnsdomain[soa][hostmaster]', 'Hostmaster:');
            echo form::input('pdnsdomain[soa][hostmaster]', $hostmaster);
        ?>
        </div>

    <?php echo form::close_section(); ?>

    <?php if (isset($views)) echo subview::renderAsSections($views); ?>

    <div class="buttons form_bottom">
        <?php echo form::button(array('name' => 'submit', 'class' => 'cancel small_red_button'), 'Cancel'); ?>
        <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Save'); ?>
    </div>

    <?php echo form::close(); ?>
</div>