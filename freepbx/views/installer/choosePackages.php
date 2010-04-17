    <?php echo form::open_section('Packages'); ?>
  
    <?php foreach ($packageList as $name => $parameters): ?>

        <?php echo form::open_fieldset(); ?>

        <legend id="legend_<?php echo $name; ?>" class="legend module <?php echo text::alternate('','alternate'); ?>">

            <span class="module_actions">

                <?php if ($name != $currentDriver): ?>
                    <span class="field enabled">
                    <?php
                        echo form::label('install_' .$name, 'Install?');
                        echo form::checkbox('install_' .$name);
                    ?>
                    </span>
                <?php else: ?>
                    <?php echo form::hidden('install_' .$name, true); ?>
                <?php endif; ?>
                
            </span>
            <span><?php echo $parameters['displayName']; ?><span class="details">(click for details)</span></span>

        </legend>

        <div class="module_messages">
            <?php if (!empty($parameters['errors'])): ?>
                <div class="fail">
                    <?php echo __('ERROR'); ?>
                    <?php echo $parameters['errors']; ?>
                </div>
            <?php endif; ?>

           <?php if (!empty($parameters['warnings'])): ?>
                <div class="warning">
                    <?php echo __('WARNING'); ?>
                    <?php echo $parameters['warnings']; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="module_parameters">
        <?php foreach ($parameters['displayParameters'] as $parameter => $value) : ?>
            <?php if (empty($value)) continue; ?>
            <div id="<?php echo $name .'_' . $parameter; ?>" class="parameter parameter_<?php echo $parameter; ?>">
                <span class="parameter_label"><?php echo __(ucfirst($parameter)); ?></span>
                <span class="parameter_value"><?php echo $value; ?></span>
            </div>

        <?php endforeach; ?>
        <div style="clear:both;"></div>
        </div>

        <?php echo form::close_fieldset(); ?>

    <?php endforeach; ?>


    <?php echo form::close_section(); ?>

<?php
    // Facts of Life by Lazyboy - interesting lyrics
    jquery::addPlugin('blockUI');
?>
<script>
    $(document).ready(function () {
        // This is the effects engine to collapse and expand the modules
        $('.module_actions').click(function (event) { event.stopPropagation(); });
        $('.module').click(function(){
            details = $(this).parent().find('.details');

            parameters = $(this).parent().find('.module_parameters');
            displayed = parameters.attr('displayed');
            if (displayed == 'true') {
                $(details).text('(click for details)');
                parameters.attr('displayed', 'false');
                parameters.hide();
            } else {
                $(details).text('(click to hide details)');
                parameters.attr('displayed', 'true');
                parameters.show();
            }
        });

    });
</script>