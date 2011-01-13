<div id="locationmanager_update_header" class="update locationmanager module_header">

    <h2><?php echo $title; ?></h2>

</div>

<div id="locationmanager_update_form" class="update locationmanager">

    <?php echo form::open(); ?>

    <?php echo form::open_section('Location Information'); ?>

        <div class="field">
        <?php
            echo form::label('location[name]', 'Location Name:');
            echo form::input('location[name]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('location[domain]', 'Domain Name:');
            echo form::input('location[domain]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('location[registry][areacode]', 'Area code:');
            echo form::input('location[registry][areacode]', isset($location['registry']['areacode']) ? $location['registry']['areacode'] : '');
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