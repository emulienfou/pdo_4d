--TEST--
PDO Common: Bug #38394 (Prepared statement error stops subsequent statements)
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
$db->exec("CREATE TABLE test (a VARCHAR(10), b VARCHAR(10), c VARCHAR(10))");
$s = $db->prepare("INSERT INTO test VALUES (:a,:b,:c)");

$s->execute(array('a' => 1, 'b' => 2, 'c' => 3));

@$s->execute(array('a' => 5, 'b' => 6, 'c' => 7, 'd' => 8));

$s->execute(array('a' => 9, 'b' => 10, 'c' => 11));

var_dump($db->query("SELECT * FROM test")->fetchAll(PDO::FETCH_ASSOC));

?>
===DONE===
--EXPECTF--
array(2) {
  [0]=>
  array(3) {
    ["a"]=>
    string(1) "1"
    ["b"]=>
    string(1) "2"
    ["c"]=>
    string(1) "3"
  }
  [1]=>
  array(3) {
    ["a"]=>
    string(1) "9"
    ["b"]=>
    string(2) "10"
    ["c"]=>
    string(2) "11"
  }
}
===DONE===
