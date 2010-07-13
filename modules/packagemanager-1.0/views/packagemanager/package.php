<div class="package_wrapper">

    <div id="legend_<?php echo $packageName; ?>" class="legend packagemanager index module">

        <span class="module_actions">

            <?php
                echo packagemanager::avaliableActions($identifier);
            ?>

        </span>

        <span>

            <?php echo $displayName; ?><span class="details" style="padding-left: 25px;">(click for details)</span>

        </span>

    </div>

    <div class="module_messages">

        <?php foreach($messages as $type => $messageList): ?>

            <?php if (empty($messageList[$packageName])) $messageList[$packageName] = array(); ?>

            <div id ="<?php echo strtolower($packageName .'_' .$type); ?>" class="
                <?php echo empty($messageList[$packageName]) ? 'hide' : ''; ?>

                <?php echo $type; ?>_message
                
                <?php echo $packageName; ?>_message packagemanager index module">

                <?php if (isset($error) && $type == 'ok') : ?>

                    <?php echo __('Pending'); ?>

                <?php elseif ($type == 'ok') : ?>

                    <?php echo __('Complete'); ?>

                <?php else : ?>

                    <?php echo __(ucfirst($type)); ?>

                <?php endif; ?>

                <ul class="<?php echo $type; ?>_list packagemanager index module">

                <?php foreach($messageList[$packageName] as $message): ?>

                    <li><?php echo $message; ?></li>

                <?php endforeach; ?>

                </ul>

            </div>

        <?php endforeach; ?>

    </div>

    <div class="module_parameters">

        <?php foreach ($displayParameters as $parameter) : ?>

            <?php if (empty($$parameter)) continue; ?>

            <div id="<?php echo strtolower($packageName .'_' . $parameter); ?>" class="parameter parameter_<?php echo $parameter; ?>">

                <span class="parameter_label"><?php echo __(ucfirst($parameter)); ?></span>

                <?php if ($parameter == 'sourceURL'): ?>

                    <span class="parameter_value"><?php echo html::anchor($$parameter); ?></span>

                <?php else: ?>

                    <span class="parameter_value"><?php echo $$parameter; ?></span>

                <?php endif; ?>
            </div>

        <?php endforeach; ?>

        <hr>

    </div>

</div>