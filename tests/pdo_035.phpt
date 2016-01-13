--TEST--
PDO Common: PDORow + get_parent_class()
--SKIPIF--
<?php # vim:ft=php
if (!extension_loaded('pdo')) die('skip no PDO');
if (!extension_loaded('pdo_4d')) die('skip no PDO for 4D extension');

die('skip : this is a SQLite Test');


if (!extension_loaded('pdo_sqlite')) die ("skip Need PDO_SQlite support");

require dirname(__FILE__) . '/../../../ext/pdo/tests/pdo_test.inc';

PDOTest::skip();
?>
--FILE--
<?php
$db = new PDO('sqlite::memory:');
$db->exec('CREATE TABLE test (id int)');
$db->exec('INSERT INTO test VALUES (23)');

$stmt = $db->prepare('SELECT id FROM test');
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_LAZY);

echo get_class($result), "\n";
var_dump(get_parent_class($result));
?>
--EXPECT--
PDORow
string(6) "PDORow"