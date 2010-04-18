<?php foreach ($vars as $name) :?>

    <div class="customize_vars">
        <?php echo $name; ?>

        <div class="field custom_var_permissions">
            <?php $fieldName = $name .'[module_permissions]'; ?>

            <?php
                echo form::radio(array(
                    'name' => $fieldName,
                    'id' => $fieldName .'access',
                    'class' => 'std_permissions'
                ), 'full');
            ?>
            <?php echo form::label($fieldName .'access', 'Full Access'); ?>

            <?php
                echo form::radio(array(
                    'name' => $fieldName,
                    'id' => $fieldName .'owner',
                    'class' => 'std_permissions'
                ), 'owner');
            ?>
            <?php echo form::label($fieldName .'owner', 'Owner Only'); ?>


            <?php
                echo form::radio(array(
                    'name' => $fieldName,
                    'id' => $fieldName .'disabled',
                    'class' => 'std_permissions'
                ), 'disabled');
            ?>
            <?php echo form::label($fieldName .'disabled', 'Disabled'); ?>
        </div>
    </div>

<?php endforeach; ?>
