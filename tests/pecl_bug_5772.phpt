--TEST--
PDO Common: PECL Bug #5772 (PDO::FETCH_FUNC breaks on mixed case func name)
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

$db->exec("CREATE TABLE test (id int NOT NULL, PRIMARY KEY (id))");
$db->exec("INSERT INTO test (id) VALUES (1)");

function heLLO($row) {
	return $row;
}

foreach ($db->query("SELECT * FROM test")->fetchAll(PDO::FETCH_FUNC, 'heLLO') as $row) {
	var_dump($row);
}

$db->exec("DROP TABLE test");

--EXPECT--
string(1) "1"
