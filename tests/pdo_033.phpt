--TEST--
PDO Common: PDO::quote()
--SKIPIF--
<?php # vim:ft=php
if (!extension_loaded('pdo')) die('skip no PDO');
if (!extension_loaded('pdo_4d')) die('skip no PDO for 4D extension');

require dirname(__FILE__) . '/../../../ext/pdo/tests/pdo_test.inc';

PDOTest::skip();
?>
--FILE--
<?php
if (getenv('REDIR_TEST_DIR') === false) putenv('REDIR_TEST_DIR='.dirname(__FILE__) . '/../../pdo/tests/');
require_once getenv('REDIR_TEST_DIR') . 'pdo_test.inc';
$db = PDOTest::factory();

$unquoted = ' !"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}~';

$quoted = $db->quote($unquoted);
//	$quoted = "'".str_replace("'","''",$unquoted)."'";

	$db->query("CREATE TABLE test (t varchar(100))");
	$db->query("INSERT INTO test (t) VALUES($quoted)");

	$stmt = $db->prepare('SELECT * from test');
	$stmt->execute();

	print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
	$db->query("DROP TABLE test");

?>
--EXPECT--
Array
(
    [0] => Array
        (
            [t] =>  !"#$%&'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}~
        )

)
