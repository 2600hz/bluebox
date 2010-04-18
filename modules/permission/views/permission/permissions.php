
<?php foreach ($packages as $name => $package) : ?>
    <div class="module <?php echo text::alternate('', 'alternate'); ?>">
        <?php echo $package['displayName']; ?>

        <div class="fields module_permissions">
            <?php $fieldName = $name .'[module_permissions]'; ?>

            <?php
                echo form::radio(array(
                    'name' => $fieldName,
                    'id' => $fieldName .'custom',
                    'class' => 'custom_radio'
                ), 'custom');
            ?>
            <?php echo form::label($fieldName .'custom', 'Custom'); ?>

            <?php
                echo form::radio(array(
                    'name' => $fieldName,
                    'id' => $fieldName .'disabled',
                    'class' => 'std_permissions'
                ), 'disabled');
            ?>
            <?php echo form::label($fieldName .'disabled', 'Disabled'); ?>

            <?php
                echo form::radio(array(
                    'name' => $fieldName,
                    'id' => $fieldName .'access',
                    'class' => 'std_permissions'
                ), 'full');
            ?>
            <?php echo form::label($fieldName .'access', 'Full Access'); ?>
        </div>

        <div class="custom <?php echo isset($subViews[$name])?'':'hide'; ?>">
            <?php if (isset($subViews[$name])) echo $subViews[$name]; ?>
        </div>
    </div>

<?php endforeach; ?>

<?php echo form::hidden('user_id', $user); ?>

<?php javascript::codeBlock(); ?>
    $('.custom_radio').unbind('click').click(function (e) {
        me = this;
        $.post('<?php echo url::site('permission/customize'); ?>', {
            'module':$(this).attr('id'),
            'user': <?php echo $user; ?>
        },
        function (data) {
            custom = $(me).parent().parent().find('.custom');
            custom.hide().html(data).slideDown();
        }, 'html');
    });
    $('.std_permissions').unbind('click').click(function (e) {
        custom = $(this).parent().parent().find('.custom');
        custom.slideUp().html('');
    });
<?php javascript::blockEnd(); ?>
