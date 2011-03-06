<div id="redbox_update_form" class="txt-left form redbox update">

    <?php echo form::open(); ?>

    <?php echo form::open_section('Redbox'); ?>

    <?php echo form::label('redbox[name]', 'Name:'); ?>
    <?php echo form::input('redbox[name]'); ?>

    <div class="ports">
        <?php
              $numOfPorts = 3;
              $port = 0;
              while(++$port < ($numOfPorts + 1))
              {
        ?>
                    <span class="port">
                        <?php echo form::open_section('Port '. $port); ?>
                            <div class="field">
                                <?php 
                                    echo form::label('redbox[port' .$port . '_label]', 'Label:');
                                    echo form::input('redbox[port' .$port . '_label]');
                                ?>
                            </div>
                            <div class="field">
                                <?php
                                      echo form::label('redbox[ports][' .$port . '][type]', 'Type:');
                                      echo form::dropdown('redbox[ports][' .$port . '][type]', array('lan' => 'LAN (Phones)',
                                                                                                     'wan.pri' => 'Primary WAN (Internet)',
                                                                                                     'wan.sec' => 'Secondary WAN (Internet)'));
                                ?>
                            </div>
                            <div class="field">
                                <?php
                                    echo form::label('redbox[ports][' .$port . '][sipport]', 'Sip Port:');
                                    echo form::input('redbox[ports][' .$port . '][sipport]');
                                ?>
                            </div>
                            <div class="field">
                                <?php
                                    echo form::label('redbox[ports][' .$port . '][dhcp]', 'Use DHCP?');
                                    echo form::checkbox(array('name' => 'redbox[ports][' .$port . '][dhcp]', 'class' => 'dhcp_checkbox', 'id' => 'redbox[ports][' .$port . '][dhcp]'));
                                ?>
                            </div>
                            <div class="network_config">
                                <div class="field">
                                    <?php
                                        echo form::label('redbox[ports][' .$port . '][ip]', 'IP:');
                                        echo form::input('redbox[ports][' .$port . '][ip]');
                                    ?>
                                </div>
                                <div class="field">
                                    <?php
                                        echo form::label('redbox[ports][' .$port . '][netmask]', 'Netmask:');
                                        echo form::input('redbox[ports][' .$port . '][netmask]');
                                    ?>
                                </div>
                                <div class="field">
                                    <?php
                                        echo form::label('redbox[ports][' .$port . '][gateway]', 'Gateway:');
                                        echo form::input('redbox[ports][' .$port . '][gateway]');
                                    ?>
                                </div>
                                <div class="field">
                                    <?php
                                        echo form::label('redbox[ports][' .$port . '][dns]', 'DNS:');
                                        echo form::input('redbox[ports][' .$port . '][dns]');
                                    ?>
                                </div>
                            </div>
                            <div class="field">
                                <?php
                                    echo form::label('redbox[ports][' .$port . '][vlan]', 'Enable VLAN:');
                                    echo form::checkbox(array('name' => 'redbox[ports][' .$port . '][vlan]', 'class' => 'vlan_checkbox', 'id' => 'redbox[ports][' .$port . '][vlan]'));
                                ?>
                            </div>
                            <div class="vlan_config">
                                <div class="field">
                                    <?php
                                        echo form::label('redbox[ports][' .$port . '][vlan_voice]', 'Voice VLAN:');
                                        echo form::input('redbox[ports][' .$port . '][vlan_voice]');
                                    ?>
                                </div>
                                <div class="field">
                                    <?php
                                        echo form::label('redbox[ports][' .$port . '][vlan_data]', 'Data VLAN:');
                                        echo form::input('redbox[ports][' .$port . '][vlan_data]');
                                    ?>
                                </div>
                            </div>
                            
                        <?php echo form::close_section(); ?>
                    </span>
        <?php } ?>
    </div>

    <?php echo form::close_section(); ?>

    <?php
        if (isset($views))
        {
            echo subview::renderAsSections($views);
        }
    ?>

    <?php echo form::close(TRUE); ?>

</div>

<script type="text/javascript">
    $(function()
    {
        $('.network_config').hide();

        $('.dhcp_checkbox').each(function()
        {
            if(!$(this).is(':checked'))
            {
                $(this).parents('.port').find('.network_config').show();
            }
        });

        $('.dhcp_checkbox').click(function()
        {
            if(!$(this).is(':checked'))
            {
                $(this).parents('.port').find('.network_config').slideDown();
            }
            else
            {
                $(this).parents('.port').find('.network_config').slideUp();
            }
        });

        // This is messy - I should just use one function to do vlan and network
        $('.vlan_config').hide();

        $('.vlan_checkbox').each(function()
        {
            if($(this).is(':checked'))
            {
                $(this).parents('.port').find('.vlan_config').show();
            }
        });

        $('.vlan_checkbox').click(function()
        {
            if($(this).is(':checked'))
            {
                $(this).parents('.port').find('.vlan_config').slideDown();
            }
            else
            {
                $(this).parents('.port').find('.vlan_config').slideUp();
            }
        });
    });
</script>