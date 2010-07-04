<?php echo form::open_section('Number Pools'); ?>

    <p>
        Number pools allow you to keep similar types of numbers grouped together.
        For example, you can block out 2XXX for Devices, 30XX for Auto-Attendants, 31XX for Ring Groups, etc.
        Check the boxes below to specify what types of features can be assigned to this number.
    </p>

    <ul>

        <?php foreach ($numberTypes['numberTypes'] as $numberType): ?>

            <li>

                <div class="field">

                    <?php echo form::label('numberType_' .$numberType['number_type_id'], $numberType['class']); ?>

                    <?php
                        echo form::checkbox(array(
                                'name' => 'number[NumberPool][][number_type_id]',
                                'id' => 'numberType_' .$numberType['number_type_id'],
                            ),
                            $numberType['number_type_id']
                        );
                    ?>

                </div>

            </li>

        <?php endforeach; ?>

    </ul>

<?php echo form::close_section(); ?>