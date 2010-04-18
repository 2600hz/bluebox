<?php foreach ($customizable as $name => $url) :?>

    <div class="customize">
        <div class="field">
            <?php echo form::label($module .'[customize][' .basename($url) .']', $name) ?>
            <?php
                echo form::dropdown($module .'[customize][' .basename($url) .']', array (
                    'full' => 'Full Access',
                    'disabled' => 'Disabled'
                ));
            ?>
        </div>
    </div>

<?php endforeach; ?>