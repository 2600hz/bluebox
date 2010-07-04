<h1 id="runtime">PHP Run Time: <?php echo $time; ?> seconds</h1>

<div id="table"></div>

<?php javascript::codeBlock(); ?>
    var template = '<?php echo $template; ?>';
    var data = <?php echo $data; ?>;

    var date = new Date();
    var start = date.getTime();

    $('#table').append(Mustache.to_html(template, data));

    var date = new Date();
    var stop = date.getTime();

    var difference = stop - start;
    $('#runtime').append('<br />JS Run Time: ' + (difference * 0.001) + ' seconds');

<?php javascript::blockEnd(); ?>