--TEST--
PDO_4D: connection test
--SKIPIF--
<?php # vim:ft=php
if (!extension_loaded('pdo')) die('skip no PDO');
if (!extension_loaded('pdo_4d')) die('skip no PDO for 4D extension');

require dirname(__FILE__) . '/../../../ext/pdo/tests/pdo_test.inc';

PDOTest::skip();
?>
--FILE--
<?php
require dirname(__FILE__) . '/../../../ext/pdo/tests/pdo_test.inc';


//echo "DSN: $dsn<br>\n";
try {
       $db = PDOTest::test_factory(dirname(__FILE__) . '/common.phpt');
       echo "PDO object created!\n";
} catch (PDOException $e) {
       echo 'Connection failed: ' . $e->getMessage()."<br/>\n";
}
//echo "<br>\n";
?>
--EXPECTF--
PDO object created!
