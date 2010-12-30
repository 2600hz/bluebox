    <?php echo form::open_section('Telephony Configuration'); ?>

        <div class="field">
        <?php
            echo form::label('cfg_root', 'Conf Directory:');
            echo form::input('cfg_root');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('audio_root', 'Global Sound File Directory:');
            echo form::input('audio_root');
        ?>
        </div>

    <?php echo form::close_section(); ?>

    <?php echo form::open_section('Event Socket'); ?>

        <div class="field">
        <?php
            echo form::label('esl_host', 'ESL Host:');
            echo form::input('esl_host');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('esl_auth', 'ESL Auth:');
            echo form::input('esl_auth');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('esl_port', 'ESL Port:');
            echo form::input('esl_port');
        ?>
        </div>

    <?php echo form::close_section(); ?>

    <?php if (!empty($conflictXmlFiles)) : ?>

        <?php echo form::open_section('Conflicting Files'); ?>
            <?php echo arr::arrayToUL($conflictXmlFiles, array(), array('class' => 'conflict_files')); ?>
        <?php echo form::close_section(); ?>

    <?php endif; ?>