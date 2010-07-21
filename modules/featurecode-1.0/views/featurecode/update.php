<div id="feature_code_update_header" class="update feature_code module_header">
    <h2><span class="helptip"></span><?php echo __($title); ?></h2>
</div>

<div id="feature_code_add_form" class="txt-left form feature_code add">
    <?php echo form::open(); ?>

    <?php echo form::open_section('Feature Code Details'); ?>

        <div class="field">
            <?php echo form::label('featurecode[name]', 'Name:'); ?>
            <?php echo form::input(array('name' => 'featurecode[name]')); ?>
        </div>

        <div class="field">
            <?php echo form::label('featurecode[description]', 'Description:'); ?>
            <?php echo form::input(array('name' => 'featurecode[description]')); ?>
        </div>

    <?php echo form::close_section(); ?>


    <?php echo form::open_section('FreeSWITCH XML'); ?>

        <div class="field">
        <?php
            echo form::label(array(
                'for' => 'net_xml',
                'hint' => 'Network related XML',
                'help' => 'Add XML that changes network-related call properties.'
                ),
                'Add Net XML?'
            );
            echo form::checkbox(array('class' => 'determinant agent_for_netXml',  'name' => 'net_xml'));
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('featurecode[netXml]', 'Network XML:');
            echo form::textarea(array('name' => 'featurecode[netXml]', 'class' => 'dependent_positive rely_on_netXml'));
        ?>
        </div>


        <div class="field">
        <?php
            echo form::label(array(
                'for' => 'condition_xml',
                'hint' => 'Conditioning XML',
                'help' => 'Add XML that conditions FreeSWITCH and call variables.'
                ),
                'Add Conditioning XML?'
            );
            echo form::checkbox(array('class' => 'determinant agent_for_conditionXml',  'name' => 'condition_xml'));
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('featurecode[conditionXml]', 'Condition XML:');
            echo form::textarea(array('name' => 'featurecode[conditionXml]', 'class' => 'dependent_positive rely_on_conditionXml'));
        ?>
        </div>


        <div class="field">
        <?php
            echo form::label(array(
                'for' => 'preroute_xml',
                'hint' => 'Pre-routing XML',
                'help' => 'Add XML that makes routing decisions right off the bat.'
                ),
                'Add Pre-Route XML?'
            );
            echo form::checkbox(array('class' => 'determinant agent_for_prerouteXml',  'name' => 'preroute_xml'));
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('featurecode[prerouteXml]', 'Pre-Route XML:');
            echo form::textarea(array('name' => 'featurecode[netXml]', 'class' => 'dependent_positive rely_on_prerouteXml'));
        ?>
        </div>


    <?php echo form::close_fieldset(); ?>

    <?php echo form::close_section(); ?>

    <div class="buttons form_bottom">
        <?php echo form::button(array('name' => 'submit', 'class' => 'cancel small_red_button'), 'Cancel'); ?>
        <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Save'); ?>
    </div>

    <?php echo form::close(); ?>
</div>
