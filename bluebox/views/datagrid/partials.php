<h1 id="runtime">PHP Run Time: <?php echo $time; ?> seconds</h1>

<div id="table"></div>

<?php javascript::codeBlock(); ?>
    var template = '<?php echo $template; ?>';
    var partials = <?php echo $partials; ?>;
    var data = <?php echo $data; ?>;

    var date = new Date();
    var start = date.getTime();

    $('#table').append(Mustache.to_html(template, data, partials));

    var date = new Date();
    var stop = date.getTime();

    var difference = stop - start;
    $('#runtime').append('<br />JS Run Time: ' + (difference * 0.001) + ' seconds');

    var newRow = {"key1":"value7","key2":"value8"};
    $('#table #test tbody').append(Mustache.to_html(partials.body, newRow));

<?php javascript::blockEnd(); ?>
