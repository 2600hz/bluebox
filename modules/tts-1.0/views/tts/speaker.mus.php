    <div class="field">
        <?php echo form::label('ttsengine[speakers][{{view_each_key}}]', 'Name'); ?>
        <?php echo form::input('ttsengine[speakers][{{view_each_key}}]', '{{view_each_value}}'); ?>
        <span id="remove_speaker">Remove</span>
    </div>