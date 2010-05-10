<div id="ringergroup_update_header" class="update ringergroup module_header">
    <h2>Manage Ring Groups</h2>
</div>

<div id="ringergroup_update_form" class="update ringergroup">
    <?php echo form::open(); ?>

    <?php echo form::open_section('Ring Group'); ?>

        <div class="field">
        <?php
            echo form::label('ringgroup[name]', 'Ring Group Name:');
            echo form::input('ringgroup[name]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('ringgroup[location_id]', 'Location:');
            echo locations::dropdown('ringgroup[location_id]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('ringgroup[strategy]', 'Strategy:');
            echo form::dropdown('ringgroup[strategy]', array(
                '1' => 'Ring All',
                '2' => 'Ring In Order'
            ));
        ?>
        </div>

    <?php echo form::close_section(); ?>

    <?php echo form::open_section('Assign Ring Group Number(s)'); ?>

        <div class="field assign_numbers">
        <?php
            echo form::label(array(
                'for' => '_numbers[assigned][]',
                'hint' => 'Numbers that ring this destination directly',
                'help' => 'Select which numbers, in which contexts, will ring this destination directly when they are called. This is a shortcut way of mapping numbers to destinations (versus using the number manager)'
            ),'Select Number:');
            echo numbering::dropdown('RingGroupNumber', $ringgroup['ring_group_id']);
            echo numbering::nextAvaliableLink('assignRingGroupNumber', 'Next Avaliable Number');
        ?>
        </div>

    <?php echo form::close_section(); ?>

    <?php echo form::open_section('Assign Ring Group Member(s)'); ?>

        <div class="field">
        <?php
            $options = '{addItemTarget: \'bottom\', animate: true, highlight: true, sortable: true }';
            jquery::addPlugin(array('asmselect', 'dragdrop', 'sortable'));
            jquery::addQuery('.multiselect')->asmSelect($options);

            echo form::label('Ring Group Members');
            echo RingGroupManager::memberSelection($ringgroup['ring_group_id']);
        ?>
        </div>
    
    <?php echo form::close_section(); ?>

    <?php if (isset($views)) echo subview::renderAsSections($views); ?>

    <div class="buttons form_bottom">
        <?php echo form::button(array('name' => 'submit', 'class' => 'cancel small_red_button'), 'Cancel'); ?>
        <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Save'); ?>
    </div>

    <?php echo form::close(); ?>
</div>
