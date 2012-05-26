<div id="simpleroutes_update_header" class="update simpleroute module_header">

    <h2><?php echo $title; ?></h2>

</div>

<div id="simpleroutes_update_form" class="update simpleroute">

    <?php echo form::open(); ?>

    <?php echo form::open_section('Simple Route'); ?>

        <div class="field">
        <?php
            echo form::label('simpleroute[name]', 'Route Name:');
            echo form::input('simpleroute[name]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('simpleroute[description]', 'Description:');
            echo form::input('simpleroute[description]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('simpleroute[type]', 'Type:');
            echo form::dropdown('simpleroute[type]', $types);
        ?>
        </div>
    
    <?php echo form::close_section(); ?>

    <?php echo form::open_section('Patterns'); ?>

        <div id="simple_route_patterns">
            <?php foreach ($simpleroute['patterns'] as $key => $pattern): ?>

                <?php echo new View('simpleroute/pattern.mus', array('patternCount' => $key, 'pattern' => $pattern, 'displayCount' => $key + 1)); ?>

            <?php endforeach; ?>
        </div>

        <div class="new_simple_route_pattern_container">
            <?php echo '<a href="#" id="new_simple_route_pattern" class="button add"><span>Add New Pattern</span></a>'; ?>
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

<?php javascript::codeBlock(); ?>

    var patternCount = <?php echo count($simpleroute['patterns']); ?>

    $('#new_simple_route_pattern').click(function (ev){

        ev.preventDefault();

        patternCount += 1;

        var data = {
            displayCount: $('.remove_pattern').length + 1,
            patternCount: patternCount
        };

        $('#simple_route_patterns').append(Mustache.to_html(<?php echo $patternTemplate; ?>, data));
    });

    $('.remove_pattern').live('click', function (ev){

        ev.preventDefault();

        $(this).parent('.field').remove();

        var pattern = 1;
        
        $('#simple_route_patterns .field label').each(function() {
            $(this).text('Pattern ' + pattern);
            pattern += 1;
        });
    });
    
<?php javascript::blockEnd(); ?>
