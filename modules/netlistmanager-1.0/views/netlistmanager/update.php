<?php
    $netmask = array();
    for ($i = 32; $i > 0; $i--) {
        $netmask[$i] = '/' . $i;
    }
?>

<div id="netlistmanager_update_header" class="clmanager module_header">

    <h2><?php echo __($title); ?></h2>

</div>

<div id="netlistmanager_update_form" class="update netlistmanager">
    
    <?php echo form::open(); ?>

    <?php echo form::open_section('Network List Information'); ?>

        <div class="field">
        <?php
            echo form::label('netlist[name]', 'Network List Name:');
            echo form::input('netlist[name]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('netlist[allow]', 'Default:');
            echo form::dropdown('netlist[allow]', array('Deny', 'Allow'));
        ?>
        </div>

    <?php echo form::close_section(); ?>

    <?php echo form::open_section('Allow Network List Members', 'net_list_members'); ?>

    <div class="field netlist_allow">
    <?php
        echo form::label('ips[allow]', 'Add Predefined IP Ranges:');
        echo form::dropdown('ips[allow][]', $netListItems, $netListAllow);
    ?>
    </div>
    
    <div class="field">
    <?php
        echo form::label('allow_range', '...Or Add a New Range:');
        echo form::input('allow_range_in', '');
        echo form::dropdown('allow_range_mask', $netmask);
        echo form::button('add_allow_btn', 'Add');
    ?>
    </div>

    <?php echo form::close_section(); ?>

    <?php echo form::open_section('Deny Network List Members', 'net_list_members'); ?>

    <div class="field netlist_deny">
    <?php
        echo form::label('ips[deny]', 'Add Predefined IP Ranges:');
        echo form::dropdown('ips[deny][]', $netListItems, $netListDeny);
    ?>
    </div>

    <div class="field">
    <?php
        echo form::label('deny_range', '...Or Add a New Range:');
        echo form::input('deny_range_in', '');
        echo form::dropdown('deny_range_mask', $netmask);
        echo form::button('add_deny_btn', 'Add');
    ?>
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

<?php jquery::addPlugin(array('asmselect', 'dragdrop', 'sortable', 'ipaddress')); ?>

<?php javascript::codeBlock(); ?>
    $("#ips_allow").asmSelect({
        animate: true,
        highlight: true,
        sortable: true
    });

    $("#ips_deny").asmSelect({
        animate: true,
        highlight: true,
        sortable: true
    });

    $("#add_allow_btn_Add").click(function(e) {
        e.stopPropagation();

        var mask = $('#allow_range_mask').val();
        var range = $("#allow_range_in").val();
        var network = range + '/' + mask;

        var option = $("<option value=" + network + "></option>").text(network).attr("selected", true);
        $("#ips_allow").append(option).change();

        return false;
    });

    $("#add_deny_btn_Add").click(function(e) {
        e.stopPropagation();

        var mask = $('#deny_range_mask').val();
        var range = $("#deny_range_in").val();
        var network = range + '/' + mask;

        var option = $("<option value=" + network + "></option>").text(network).attr("selected", true);
        $("#ips_deny").append(option).change();

        return false;
    });

    $('#allow_range_in').ipaddress();
    $('#deny_range_in').ipaddress();
<?php javascript::blockEnd(); ?>
