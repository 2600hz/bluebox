<div id="quickadd_update_header" class="update quickadd module_header">

    <h2><?php echo $title; ?></h2>

</div>

<div id="quickadd_update_form" class="txt-left form quickadd updates">

    <?php echo form::open(); ?>

    <?php echo form::open_section('User Information'); ?>

        <div class="field">
        <?php
            echo form::label('user[first_name]', 'First Name:');
            echo form::input('user[first_name]', isset($user['first_name']) ? $user['first_name'] : 'Account');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('user[last_name]', 'Last Name:');
            echo form::input('user[last_name]', isset($user['last_name']) ? $user['last_name'] : 'User');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('user[email_address]', 'Email:');
            echo form::input('user[email_address]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('user[create_password]', 'Password:');
            echo form::input('user[create_password]',
                isset($password) ? $password : NULL
            );
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('user[confirm_password]', 'Confirm Password:');
            echo form::input('user[confirm_password]',
                isset($confirm_password) ? $confirm_password : NULL
            );
        ?>
        </div>

    <?php echo form::close_section(); ?>
    
    <?php echo form::open_section('Call Routing'); ?>

        <div class="field">
        <?php
            echo form::label('number[location_id]', 'Location:');
            echo locations::dropdown('number[location_id]');
        ?>
        </div>
    
        <div class="field">
            <?php
                echo form::label(array(
                        'for' => 'device[context_id]',
                        'hint' => 'Default outbound call context',
                        'help' => 'This field determines the phone numbers a user can call. All phone numbers and SIP trunks associated with the selected context can be dialed by this user.<BR><BR>Note that, in most cases, the user\'s device must authenticate in order for this to work. Note that if this is not set, the context for the default interface a call is received on is used instead.'
                    ),
                    'Default Context:'
                );
            ?>
            <?php echo numbering::selectContext('device[context_id]'); ?>
        </div>

    <?php echo form::close_section(); ?>

    <?php echo form::open_section('Number'); ?>

        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'callerid[internal_number]',
                    'hint' => 'Used for on-network calls',
                    'help' => 'Caller ID information used when calling other phones within this same PBX/switch network.'
                ),
                'Internal Extension Number:'
            );
            echo form::input('callerid[internal_number]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'callerid[external_number]',
                    'hint' => 'Used for outside calls',
                    'help' => 'Caller ID information used when calling phones outside this network.'
                ),
                'External CID Number:'
            );
            echo form::input('callerid[external_number]');
        ?>
        </div>
    
    <?php echo form::close_section(); ?>


    <?php echo form::close(TRUE); ?>

</div>