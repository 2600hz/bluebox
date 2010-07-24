<?php echo form::open_section('Contexts'); ?>

    <p>
        Select the groups of callers who can dial or call this number.  By placing numbers in to
        certain contexts you can controll what numbers can call eachother.
    </p>

    <ul>

        <?php foreach ($order as $context_id => $pos): ?>

            <li>

                <div class="field">

                    <?php echo form::label('context_' .$context_id, $contexts[$context_id]); ?>

                    <?php
                        echo form::checkbox(array(
                                'name' => 'number[NumberContext][' .$pos .'][context_id]',
                                'id' => 'context_' .$context_id,
                            ),
                            $context_id
                        );
                    ?>

                </div>

            </li>

        <?php endforeach; ?>

    </ul>

<?php echo form::close_section(); ?>