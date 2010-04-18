    <?php foreach ($phoneParameterMappings as $section => $parameters) : ?>
        <?php echo form::open_section($section); ?>

        <?php foreach ($parameters as $marker => $parameter) : ?>

        <div class="field">
        <?php
            $name = 'phone[parameters][' . $marker . ']';
            $type = $parameter['type'];
            $label = $parameter['label'];

            unset($parameter['type']);
            unset($parameter['label']);

            $parameter += array(
                'name' => $name
            );

            echo form::label($name, $label);
            echo call_user_func_array(array('form', $type), array($parameter));
        ?>
        </div>


        <?php endforeach; ?>

        <?php echo form::close_section(); ?>
    <?php endforeach; ?>

    <?php echo form::close_section(); // THIS IS CLOSING THE PARENT FORM SECTION !!! BE AWARE! ?>

    <?php for ($lineAppearance = 0; $lineAppearance < $lineCount; $lineAppearance++) : ?>

        <?php echo form::open_section('Line ' . $lineAppearance); ?>

            <?php $lineName = 'lines[' . $lineAppearance . ']'; ?>

            <?php echo form::open_section('Device Assignment'); ?>
                <div class="field">
                <?php
                    echo form::label($lineName.'[device_id]', 'Device');
                    echo form::dropdown($lineName .'[device_id]', $devices);
                ?>
                </div>

                <div class="field">
                <?php
                    echo form::label($lineName .'[parameters][displayName]', 'Display Name:');
                    echo form::input($lineName .'[parameters][displayName]');
                ?>
                </div>
            <?php echo form::close_section(); ?>

            <?php foreach ($lineParameterMappings as $section => $parameters) : ?>
                <?php echo form::open_section($section); ?>

                <?php foreach ($parameters as $marker => $parameter) : ?>

                <div class="field">
                <?php
                    $name = $lineName .'[parameters][' . $marker . ']';
                    $type = $parameter['type'];
                    $label = $parameter['label'];

                    unset($parameter['type']);
                    unset($parameter['label']);

                    $parameter += array(
                        'name' => $name
                    );

                    if (!empty($parameter['class'])) {
                        $parameter['class'] = str_replace(array(
                            '{line}'
                        ),
                        array (
                            $lineAppearance
                        ), $parameter['class']);
                    }

                    echo form::label($name, $label);
                    echo call_user_func_array(array('form', $type), array($parameter));
                ?>
                </div>


                <?php endforeach; ?>

                <?php echo form::close_section(); ?>
            <?php endforeach; ?>
        <?php echo form::close_section(); ?>
   <?php endfor; ?>