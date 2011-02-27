<div id="externalnumber_update_header" class="update externalnumber module_header">

    <h2><?php echo $title; ?></h2>

</div>

<div id="externalnumber_update_form" class="update externalnumber">

    <?php echo form::open(); ?>

    <?php echo form::open_section('Manage'); ?>

        <div class="field">
        <?php
            echo form::label('number[number]', 'Number:');
            echo form::input('number[number]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('number[account_id]', 'Account:');
            echo externalnumbers::accountsDropdown('number[account_id]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('number[location_id]', 'Location:');
            echo externalnumbers::locationsDropdown('number[location_id]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('number[context_id]', 'Context:');
            echo externalnumbers::contextsDropdown('number[NumberContext][0][context_id]',
                    isset($number['NumberContext'][0]['context_id']) ? $number['NumberContext'][0]['context_id'] : NULL);
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

<?php jquery::addPlugin(array('dependent')); ?>

<?php javascript::codeBlock(); ?>
    $('.dpslctholder').remove();
    $('#number_location_id').dependent({ parent: 'number_account_id', group: 'common_class' });
    $('#number_NumberContext_0_context_id').dependent({ parent: 'number_account_id', group: 'common_class' });
<?php javascript::blockEnd(); ?>