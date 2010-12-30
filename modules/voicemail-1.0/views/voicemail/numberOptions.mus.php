<?php echo form::open_section('Options'); ?>

    <div class="field">

        <label for="number{{number_id}}_registry_skip_instructions" class="label" id="label_number{{number_id}}_registry_skip_instructions">Skip Voicemail Instructions:</label>

        <input type="checkbox" value="1" {{#skipInstructions}}checked="checked"{{/skipInstructions}} class="input" name="number{{number_id}}[registry][skipInstructions]" id="number{{number_id}}_registry_skip_instructions" />

        <fieldset class="hidden_inputs">

            <input type="hidden" class=" hidden" value="0" name="__number{{number_id}}[registry][skipInstructions]">

        </fieldset>

    </div>

    <div class="field">

        <label for="number{{number_id}}_registry_skip_greeting" class="label" id="label_number{{number_id}}_registry_skip_greeting">Skip Voicemail Greeting:</label>

        <input type="checkbox" value="1" {{#skipGreeting}}checked="checked"{{/skipGreeting}} class="input" name="number{{number_id}}[registry][skipGreeting]" id="number{{number_id}}_registry_skip_greeting" />

        <fieldset class="hidden_inputs">

            <input type="hidden" class=" hidden" value="0" name="__number{{number_id}}[registry][skipGreeting]">

        </fieldset>

    </div>

<?php echo form::close_section(); ?>