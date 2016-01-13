--TEST--
PDO Common: PDOStatement SPL iterator with integers
--SKIPIF--
<?php # vim:ft=php
if (!extension_loaded('pdo')) die('skip no PDO');
if (!extension_loaded('pdo_4d')) die('skip no PDO for 4D extension');
if (!extension_loaded('SPL')) die('skip SPL not available');
if (!class_exists('RecursiveArrayIterator', false)) die('skip Class RecursiveArrayIterator missing');
if (!class_exists('RecursiveTreeIterator', false) && !file_exists(getenv('REDIR_TEST_DIR').'../../spl/examples/recursivetreeiterator.inc')) die('skip Class RecursiveTreeIterator missing');

require dirname(__FILE__) . '/../../../ext/pdo/tests/pdo_test.inc';

PDOTest::skip();
?>
--FILE--
<?php
if (getenv('REDIR_TEST_DIR') === false) putenv('REDIR_TEST_DIR='.dirname(__FILE__) . '/../../pdo/tests/'); 
require_once getenv('REDIR_TEST_DIR') . 'pdo_test.inc';
if (!class_exists('RecursiveTreeIterator', false)) require_once(getenv('REDIR_TEST_DIR').'ext/spl/examples/recursivetreeiterator.inc'); 

$data = array(
    array(1, 2, 3),
    array(4,5,6),
    array(7,8, 9),
);

$db = PDOTest::factory();

$db->exec('CREATE TABLE test(id INT NOT NULL, val INT, val2 INT,  PRIMARY KEY(id))');

$stmt = $db->prepare("INSERT INTO test VALUES(?, ?, ?)");
foreach ($data as $row) {
    $stmt->execute($row);
}

unset($stmt);

echo "===QUERY===\n";

$stmt = $db->query('SELECT * FROM test');

foreach(new RecursiveTreeIterator(new RecursiveArrayIterator($stmt->fetchAll(PDO::FETCH_ASSOC)), RecursiveTreeIterator::BYPASS_KEY) as $c=>$v)
{
	echo "$v [$c]\n";
}

echo "===DONE===\n";
exit(0);
/*
-- EXPECT --
===QUERY===
|-Array [0]
| |-1 [id]
| |-2 [val]
| \-3 [val2]
|-Array [1]
| |-4 [id]
| |-5 [val]
| \-6 [val2]
\-Array [2]
  |-7 [id]
  |-8 [val]
  \-9 [val2]
===DONE===
*/
?>
--XFAIL--
This is a bug where PDO sends all data as string, and 4d enforce the right type upon reception
--EXPECTF--
Warning: PDOStatement::execute(): SQLSTATE[HY004]: Invalid SQL data type: 1108 Failed to execute statement. in %s on line %d

Warning: PDOStatement::execute(): SQLSTATE[HY004]: Invalid SQL data type: 1108 Failed to execute statement. in %s on line %d

Warning: PDOStatement::execute(): SQLSTATE[HY004]: Invalid SQL data type: 1108 Failed to execute statement. in %s on line %d
===QUERY===
===DONE===
