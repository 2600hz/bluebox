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

        <?php echo packagemanager::getPackageMessages($messages, $identifier); ?>

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