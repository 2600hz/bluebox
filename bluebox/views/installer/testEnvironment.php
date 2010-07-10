<ul class="envroResults">

    <?php foreach($results as $result) : ?>

    <?php
        $fail = $result['required'] ? 'fail ' : 'optional ';
        $class = $result['result'] ? ' pass' : 'result ' . $fail;
    ?>

    <li class="test_group <?php echo text::alternate($class, $class .' alternate'); ?>">
        <div class="test">
            <?php echo $result['name'] ?>
        </div>
        <div class="result">
            <?php echo $result['result'] ? $result['pass_msg'] : $result['fail_msg']; ?>
        </div>
    </li>

    <?php endforeach; ?>

</ul>