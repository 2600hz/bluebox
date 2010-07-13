<?php echo form::open_section('Caller ID'); ?>

    <div class="field">
    <?php
        echo form::label(array(
                'for' => 'callerid[internal_name]',
                'hint' => 'Used for on-network calls',
                'help' => 'Caller ID information used when calling other phones within this same PBX/switch network.'
            ),
            'Internal Caller Name:'
        );
        echo form::input('callerid[internal_name]');
    ?>
    </div>

    <div class="field">
    <?php
        echo form::label(array(
                'for' => 'callerid[internal_number]',
                'hint' => 'Used for on-network calls',
                'help' => 'Caller ID information used when calling other phones within this same PBX/switch network.'
            ),
            'Internal Caller Number:'
        );
        echo form::input('callerid[internal_number]');
    ?>
    </div>

    <div class="field">
    <?php
        echo form::label(array(
                'for' => 'callerid[external_name]',
                'hint' => 'Used for outside calls',
                'help' => 'Caller ID information used when calling phones outside this network.'
            ),
            'External Caller Name:'
        );
        echo form::input('callerid[external_name]');
    ?>
    </div>

    <div class="field">
    <?php
        echo form::label(array(
                'for' => 'callerid[external_number]',
                'hint' => 'Used for outside calls',
                'help' => 'Caller ID information used when calling phones outside this network.'
            ),
            'External Caller Number:'
        );
        echo form::input('callerid[external_number]');
    ?>
    </div>

<?php echo form::close_section(); ?>
