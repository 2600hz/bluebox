<?php echo form::open_section('Options'); ?>

    <div class="field">

        <label for="number{{number_id}}_registry_ringtype" class="label" id="label_number{{number_id}}_registry_ringtype">
            <?php echo __('Ring Type'); ?>:
        </label>

        <select class="dropdown" name="number{{number_id}}[registry][ringtype]" id="number{{number_id}}_registry_ringtype">

            <option value="ringing" {{#ringtype_ringing}}selected="selected"{{/ringtype_ringing}}><?php echo __('Ringing'); ?></option>

            <option value="moh" {{#ringtype_moh}}selected="selected"{{/ringtype_moh}}><?php echo __('Hold Music'); ?></option>

        </select>

    </div>

    <div class="field">

        <label for="number{{number_id}}_registry_timeout" class="label" id="label_number{{number_id}}_registry_timeout">
            <?php echo __('Ring this device for'); ?>:
        </label>

        <input type="text" value="{{timeout}}" class="input" name="number{{number_id}}[registry][timeout]" id="number{{number_id}}_registry_timeout" /> seconds

    </div>

    <div class="field">

        <label for="number{{number_id}}_registry_ignore_fwd" class="label" id="label_number{{number_id}}_registry_ignore_fwd">
            <?php echo __('Disable Call Forwarding'); ?>:
        </label>

        <input type="checkbox" value="1" {{#ignoreFWD}}checked="checked"{{/ignoreFWD}} class="input" name="number{{number_id}}[registry][ignoreFWD]" id="number{{number_id}}_registry_ignore_fwd" />

        <fieldset class="hidden_inputs">

            <input type="hidden" class=" hidden" value="0" name="__number{{number_id}}[registry][ignoreFWD]">

        </fieldset>

    </div>

<?php echo form::close_section(); ?>