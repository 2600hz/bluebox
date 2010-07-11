<?php echo form::open_section('Contexts'); ?>

    <p>
        Select the groups of callers who can dial or call this number.  By placing numbers in to
        certain contexts you can controll what numbers can call eachother.
    </p>

    <ul>

        <?php foreach ($contexts['contexts'] as $context): ?>

            <li>

                <div class="field">

                    <?php echo form::label('context_' .$context['context_id'], $context['name']); ?>

                    <?php
                        echo form::checkbox(array(
                                'name' => 'number[NumberContext][][context_id]',
                                'id' => 'context_' .$context['context_id'],
                            ),
                            $context['context_id']
                        );
                    ?>

                </div>

            </li>

        <?php endforeach; ?>

    </ul>

<?php echo form::close_section(); ?>