<?php if (count($contexts) == 1) : ?>

    <?php echo form::hidden('number[NumberContext][][context_id]', key($contexts)); ?>

<?php else: ?>

    <?php echo form::open_section('Contexts'); ?>

        <p>
            <?php 
                echo __('
                    Contexts are containers that allow you to restrict what each number has access to.
                    For each context you will be able so specify zero or more outbound trunks, feature codes, devices and more.
                    If you want the same functionality in multiple contexts your number must be of type \'Internal\' and you\'ll be able to select more than one context.
                ');
            ?>
        </p>

        <ul>

            <?php foreach ($contexts as $context_id => $name): ?>

                <li>

                    <div class="field">

                        <?php echo form::label('context_' .$context_id, $name); ?>

                        <?php
                            echo form::checkbox(array(
                                    'name' => 'number[NumberContext][][context_id]',
                                    'id' => 'context_' .$context_id,
                                    'class' => 'number_context_option'
                                ),
                                $context_id,
                                (bool)arr::filter_collection($number['NumberContext'], 'context_id', $context_id)
                            );
                        ?>

                    </div>

                </li>

            <?php endforeach; ?>

        </ul>

    <?php echo form::close_section(); ?>

<?php endif; ?>