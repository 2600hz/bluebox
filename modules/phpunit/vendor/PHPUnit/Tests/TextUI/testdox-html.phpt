--TEST--
phpunit --testdox-html php://stdout BankAccountTest ../../Samples/BankAccount/BankAccountTest.php
--FILE--
<?php
$_SERVER['argv'][1] = '--testdox-html';
$_SERVER['argv'][2] = 'php://stdout';
$_SERVER['argv'][3] = 'BankAccountTest';
$_SERVER['argv'][4] = '../Samples/BankAccount/BankAccountTest.php';

require_once dirname(dirname(dirname(__FILE__))) . '/TextUI/Command.php';
PHPUnit_TextUI_Command::main();
?>
--EXPECTF--
PHPUnit %s by Sebastian Bergmann.

<html><body><h2 id="BankAccountTest">BankAccount</h2><ul>...<li>Balance is initially zero</li><li>Balance cannot become negative</li></ul></body></html>

Time: %i seconds

OK (3 tests, 3 assertions)
