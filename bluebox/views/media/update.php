<style>
    ul.media_tabs {font-family:helvetica,arial,sans-serif;margin:0;padding:0;}
    ul.media_tabs li {margin:0;padding:5px; list-style:none;margin:0; border-bottom:1px solid #CCCCCC;}
    ul.media_tabs li a {text-decoration:none;display:block;padding:0.3em 0.5em;border:1px solid silver;color:#003;background:#fff;}
    ul.media_tabs li a:hover {border:1px solid gray;color:#000;background:#efefef}

    #media_tabs { border:0 !important; }
    #media_tabs .ui-tabs-panel { border:1px solid #CCCCCC !important; }
    #media_tabs .ui-widget-header { background:#FFFFFF !important; border:0 !important; }
</style>

<?php echo form::open_section('Media'); ?>

<div id="media_tabs">

    <ul>

        <?php foreach ($components as $name => $view): ?>

            <?php echo sprintf('<li><a href="#%s">%s</a></li>', html::token($name), $name); ?>

        <?php endforeach; ?>

    </ul>

    <?php foreach ($components as $name => $view): ?>

        <?php echo sprintf('<div id="%s">', html::token($name)); ?>

        <?php echo $view->set('pluginvar', $base .'[plugins][media_widget]'); ?>

        <?php echo '</div>'; ?>

    <?php endforeach; ?>

    <?php echo form::hidden('media[type]'); ?>
</div>

<?php echo form::close_section(); ?>

<?php jquery::addPlugin('tabs'); ?>

<?php javascript::codeBlock(); ?>

    var selected_type = $('#media_type_hidden').val();

    var selected_tab = $('a[href="#' + selected_type + '"]').parent();

    var selected = $('#media_tabs > ul li').index(selected_tab);

    if (selected < 0)
    {
        selected = 0;
    }

    $('#media_tabs').tabs({
        select: function(event, ui) {
            var selection = ui.tab.hash;
            $('#media_type_hidden').val(selection.substr(1));
        },
        selected: selected
    });

    if (!$('#media_type_hidden').val())
    {
        var tab_pos = $('#media_tabs').tabs('option', 'selected');

        var init_value = $($('#media_tabs > ul li')[tab_pos]).find('a').attr('href').substr(1);

        $('#media_type_hidden').val(init_value);
    }
    
<?php javascript::blockEnd(); ?>
