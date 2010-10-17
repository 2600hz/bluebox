<style>
    #ring_group_members { list-style-type: none; margin: 0; padding: 0; width:100%; float:left; margin:10px 0; max-height:225px; }
    #ring_group_members .ringgroup_member { margin: 0 3px 3px 3px; padding: 0.4em 1.5em; background: #FFFFFF; border:1px solid #CCCCCC; }
    #ring_group_members .ringgroup_member .sort_handle { position: absolute; margin-left: -1.3em; }


    #ring_group_members .ringgroup_member ul { width: 100%; }
    #ring_group_members .ringgroup_member ul { margin: 0; padding: 0; list-style-type: none; }
    #ring_group_members .ringgroup_member ul li { display:inline; }
</style>

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
                RingGroup::STRATEGY_ENTERPRISE => 'Ring All',
                RingGroup::STRATEGY_SEQUENTIAL => 'Ring In Order'
            ));
        ?>
        </div>

    <?php echo form::close_section(); ?>

    <?php echo form::open_section('Ring Group Member(s)'); ?>
        <div style="padding: 5px; overflow-y: auto; border: 1px solid #CCCCCC;">

            <ul id="ring_group_members" class="ring_group_members">

            </ul>

        </div>
        (Drag devices up/down to re-order)

    <?php echo form::close_section(); ?>

    <?php
        if (isset($views))
        {
            echo subview::renderAsSections($views);
        }
    ?>

    <div class="buttons form_bottom">

        <?php echo form::button(array('name' => 'submit', 'class' => 'cancel small_red_button'), 'Cancel'); ?>

        <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Save'); ?>

    </div>

    <?php echo form::close(); ?>

</div>

<?php jquery::addPlugin(array('sortable')); ?>

<?php javascript::codeBlock(); ?>

    var member_template = '<?php echo str_replace(array("\n", '  '), '', new View('ringgroup/membertemplate')); ?>';

    var members = <?php echo $members; ?>;

    var avaliable_members = <?php echo $avaliableMembers; ?>;

    $.each(avaliable_members, function(index, value){

        $('#ring_group_members').append(Mustache.to_html(member_template, value));

    });

    $.each(members, function(index, value) {
        $('#checkbox_' + value.type + '_' + value.id).parents('.ringgroup_member').prependTo($('#ring_group_members'));
        $('#checkbox_' + value.type + '_' + value.id).attr('checked', true);
    });

    $('#ring_group_members').sortable({ cursor: 'move', cursorAt: 'top', containment: 'parent', tolerance: 'pointer', delay: 250 });

<?php javascript::blockEnd(); ?>