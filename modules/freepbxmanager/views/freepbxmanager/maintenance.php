<div id="freepbxmanager_settings_header" class="settings freepbxmanager module_header">
    <h2><?php echo __('System Maintenance'); ?></h2>
</div>


<div id="freepbxmanager_settings_form" class="settings freepbxmanager">
    <?php echo form::open(); ?>

    <div class="regenerate">
        <div class="field">
            <label>Regenerate core configurations</label>
            <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Regenerate Now'); ?>
        </div>
        <p>Sometimes configuration data can become out of sync with the database settings in your system. This can happen for a variety
        of reasons ranging from bugs in the software to file permission issues. You can force a regeneration of core config files automatically.</p>
    </div>

    <?php echo form::close();?>
</div>