<div id="trunk_update_header" class="update trunk module_header">

    <h2><?php echo $title; ?></h2>
    
</div>

<div id="trunk_update_form" class="update trunk">

    <?php echo form::open(); ?>
    
    <?php echo form::open_section('Trunk Information'); ?>

        <div class="field">
        <?php
            echo form::label('trunk[name]', 'Trunk Name:');
            echo form::input('trunk[name]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('trunk[type]', 'Trunk Type:');
            echo form::dropdown('trunk[type]', $supportedTrunkTypes);
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('trunk[server]', 'Server:');
            echo form::input('trunk[server]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'trunk[registry][registerProxy]',
                    'hint' => 'Leave blank if same as server'
                ),
                'Register Proxy:'
            );
            echo form::input('trunk[registry][registerProxy]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'trunk[registry][outboundProxy]',
                    'hint' => 'Leave blank if same as server'
                ),
                'Outbound Proxy:'
            );        
            echo form::input('trunk[registry][outboundProxy]');
        ?>
        </div>
    	
        <div class="field">
	<?php
	    echo form::label(array(
		    'for' => 'trunk[registry][allow_media_proxy]',
		    'help' => 'One of the purposes of this option is to allow FreeSwitch to handle codecs that it does not officially support'
		),
		'Allow Proxy Media:'
	    );
	    echo form::checkbox('trunk[registry][allow_media_proxy]');
	?>
	</div>
    <?php 
        if (isset($views))
        {
            echo subview::renderAsSections($views);
        }
    ?>
    
    <?php echo form::close(TRUE); ?>
    
</div>
