<div id="accountmanager_update_header" class="update accountmanager module_header">

    <h2><?php echo $title; ?></h2>

</div>

<div id="accountmanager_update_form" class="update accountmanager">
    
    <?php echo form::open(); ?>

    <?php echo form::open_section('Account Information'); ?>

        <div class="field">
        <?php
            echo form::label('account[name]', 'Name:');
            echo form::input('account[name]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('account[type]', 'Type:');
            echo form::dropdown('account[type]', Account::$types);
        ?>
        </div>

    <?php echo form::close_section(); ?>
    
    <?php
        if (isset($views))
        {
            echo subview::renderAsSections($views);
        }
    ?>

    <?php echo form::close(TRUE); ?>

</div>