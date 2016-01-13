--TEST--
PDO Common: PDOStatement SPL iterator
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
    array('10', 'Abc', 'zxy'),
    array('20', 'Def', 'wvu'),
    array('30', 'Ghi', 'tsr'),
);

$db = PDOTest::factory();

$db->exec('CREATE TABLE test(id TEXT, val TEXT, val2 TEXT)');

$stmt = $db->prepare("INSERT INTO test VALUES(?, ?, ?)");
$a = $b = $c = 'd';
$stmt->bindParam(1, $a); 
$stmt->bindParam(2, $b); 
$stmt->bindParam(3, $c); 

foreach ($data as $row) {
	list($a, $b, $c) = $row;
    $stmt->execute();
}

/*
foreach ($data as $row) {
    $stmt->execute($row);
}
*/

unset($stmt);

echo "===QUERY===\n";

$stmt = $db->query('SELECT * FROM test');

foreach(new RecursiveTreeIterator(new RecursiveArrayIterator($stmt->fetchAll(PDO::FETCH_ASSOC)), RecursiveTreeIterator::BYPASS_KEY) as $c=>$v)
{
	echo "$v [$c]\n";
}

echo "===DONE===\n";
exit(0);
?>
--EXPECT--
===QUERY===
|-Array [0]
| |-10 [id]
| |-Abc [val]
| \-zxy [val2]
|-Array [1]
| |-20 [id]
| |-Def [val]
| \-wvu [val2]
\-Array [2]
  |-30 [id]
  |-Ghi [val]
  \-tsr [val2]
===DONE===
