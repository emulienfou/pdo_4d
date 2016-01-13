--TEST--
PDO Common: PDO::FETCH_INTO
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

$db->exec('CREATE TABLE test(id INT NOT NULL, val VARCHAR(10), val2 VARCHAR(16),  PRIMARY KEY(id))');

$data = array(
    array(10, 'Abc', 'zxy'),
    array(20, 'Def', 'wvu'),
    array(30, 'Ghi', 'tsr'),
    array(40, 'Jkl', 'qpo'),
    array(50, 'Mno', 'nml'),
    array(60, 'Pqr', 'kji'),
);


// Insert using question mark placeholders
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

class Test {
	public $id, $val, $val2;
}

$stmt = $db->prepare('SELECT * FROM test');
$stmt->setFetchMode(PDO::FETCH_INTO, new Test);
$stmt->execute();

foreach($stmt as $obj) {
	var_dump($obj);
}

echo "===FAIL===\n";

class Fail {
	protected $id;
	public $val, $val2;
}

$stmt->setFetchMode(PDO::FETCH_INTO, new Fail);
$stmt->execute();

foreach($stmt as $obj) {
	var_dump($obj);
}

?>
--EXPECTF--
object(Test)#%d (3) {
  ["id"]=>
  string(2) "10"
  ["val"]=>
  string(3) "Abc"
  ["val2"]=>
  string(3) "zxy"
}
object(Test)#%d (3) {
  ["id"]=>
  string(2) "20"
  ["val"]=>
  string(3) "Def"
  ["val2"]=>
  string(3) "wvu"
}
object(Test)#%d (3) {
  ["id"]=>
  string(2) "30"
  ["val"]=>
  string(3) "Ghi"
  ["val2"]=>
  string(3) "tsr"
}
object(Test)#%d (3) {
  ["id"]=>
  string(2) "40"
  ["val"]=>
  string(3) "Jkl"
  ["val2"]=>
  string(3) "qpo"
}
object(Test)#%d (3) {
  ["id"]=>
  string(2) "50"
  ["val"]=>
  string(3) "Mno"
  ["val2"]=>
  string(3) "nml"
}
object(Test)#%d (3) {
  ["id"]=>
  string(2) "60"
  ["val"]=>
  string(3) "Pqr"
  ["val2"]=>
  string(3) "kji"
}
===FAIL===

Fatal error: Cannot access protected property Fail::$id in %spdo_025.php on line %d%a