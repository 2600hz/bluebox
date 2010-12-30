<div id="tts_update_header" class="update tts module_header">

    <h2><?php echo $title; ?></h2>

</div>

<div id="tts_update_form" class="form tts updates">

    <?php echo form::open(); ?>

    <?php echo form::open_section('Engine'); ?>

        <div class="field">
            <?php echo form::label('ttsengine[name]', 'Name:'); ?>
            <?php echo form::input('ttsengine[name]'); ?>
        </div>

        <div class="field">
            <?php echo form::label('ttsengine[description]', 'Description:'); ?>
            <?php echo form::textarea('ttsengine[description]'); ?>
        </div>

    <?php echo form::close_section(); ?>

    <?php echo form::open_section('Speakers'); ?>

        <div id="tts_engine_speakers">
            
            <?php View::factory('tts/speaker.mus')->each($ttsengine, 'speakers'); ?>

        </div>
    
        <span id="add_new_speaker">Add</span>

    <?php echo form::close_section(); ?>

    <?php echo form::close(TRUE); ?>

</div>

<?php javascript::add('mustache'); ?>

<?php javascript::codeBlock(); ?>

    var musTemplate = <?php View::factory('tts/speaker.mus')->template(TRUE); ?>;

    var count = <?php echo arr::max_key($ttsengine['speakers']); ?>

    $('#add_new_speaker').click(function (){
        count++;

        $('#tts_engine_speakers').append(Mustache.to_html(musTemplate, {view_each_key: count}));
    });

    $('#remove_speaker').live('click', function() {
        $(this).parent().empty();
    });

<?php javascript::blockEnd(); ?>
