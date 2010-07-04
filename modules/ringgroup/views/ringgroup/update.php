<style>
    ul.ring_group_members {font-family:helvetica,arial,sans-serif;margin:0;padding:0;}
    ul.ring_group_members li {margin:-1px 0 0;padding:5px; list-style:none;border-bottom:1px solid #CCCCCC; border-top:1px solid #CCCCCC; background: #FFFFFF;}
    ul.ring_group_members li a {text-decoration:none;display:block;padding:0.3em 0.5em;border:1px solid silver;color:#003;background:#fff;}
    ul.ring_group_members li a:hover {border:1px solid gray;color:#000;background:#efefef}
    .ring_group_members { margin: 10px; }
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

        <div style="height: 225px; padding: 5px; overflow-y: auto; border: 1px solid #CCCCCC;">

            <ul id="ring_group_members" class="ring_group_members">

            </ul>

        </div>

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
        $('#checkbox_' + value.type + '_' + value.id).parents('li').prependTo($('#ring_group_members'));
        $('#checkbox_' + value.type + '_' + value.id).attr('checked', true);
    });

    $('#ring_group_members').sortable({ cursor: 'crosshair', delay: 250 });

<?php javascript::blockEnd(); ?>