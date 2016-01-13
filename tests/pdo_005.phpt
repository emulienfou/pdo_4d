--TEST--
PDO Common: PDO::FETCH_CLASS
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

$db->exec('CREATE TABLE test(id int NOT NULL, val VARCHAR(10), val2 VARCHAR(10), PRIMARY KEY(id))');
$db->exec("INSERT INTO test VALUES(1, 'A', 'AA')");
$db->exec("INSERT INTO test VALUES(2, 'B', 'BB')");
$db->exec("INSERT INTO test VALUES(3, 'C', 'CC')");

$stmt = $db->prepare('SELECT id, val, val2 from test');

class TestBase
{
	public $id;
	protected $val;
	private $val2;
}

class TestDerived extends TestBase
{
	protected $row;

	public function __construct(&$row)
	{
		echo __METHOD__ . "($row,{$this->id})\n";
		$this->row = $row++;
	}
}

$stmt->execute();
var_dump($stmt->fetchAll(PDO::FETCH_CLASS));

$stmt->execute();
var_dump($stmt->fetchAll(PDO::FETCH_CLASS, 'TestBase'));

$stmt->execute();
print_r($stmt->fetchAll(PDO::FETCH_CLASS, 'TestDerived', array(0)));

?>
--EXPECTF--
array(3) {
  [0]=>
  object(stdClass)#3 (3) {
    ["id"]=>
    string(1) "1"
    ["val"]=>
    string(1) "A"
    ["val2"]=>
    string(2) "AA"
  }
  [1]=>
  object(stdClass)#4 (3) {
    ["id"]=>
    string(1) "2"
    ["val"]=>
    string(1) "B"
    ["val2"]=>
    string(2) "BB"
  }
  [2]=>
  object(stdClass)#5 (3) {
    ["id"]=>
    string(1) "3"
    ["val"]=>
    string(1) "C"
    ["val2"]=>
    string(2) "CC"
  }
}
array(3) {
  [0]=>
  object(TestBase)#5 (3) {
    ["id"]=>
    string(1) "1"
    ["val:protected"]=>
    string(1) "A"
    ["val2:private"]=>
    string(2) "AA"
  }
  [1]=>
  object(TestBase)#4 (3) {
    ["id"]=>
    string(1) "2"
    ["val:protected"]=>
    string(1) "B"
    ["val2:private"]=>
    string(2) "BB"
  }
  [2]=>
  object(TestBase)#3 (3) {
    ["id"]=>
    string(1) "3"
    ["val:protected"]=>
    string(1) "C"
    ["val2:private"]=>
    string(2) "CC"
  }
}
TestDerived::__construct(0,1)
TestDerived::__construct(1,2)
TestDerived::__construct(2,3)
Array
(
    [0] => TestDerived Object
        (
            [row:protected] => 0
            [id] => 1
            [val:protected] => A
            [val2:private] => 
            [val2] => AA
        )

    [1] => TestDerived Object
        (
            [row:protected] => 1
            [id] => 2
            [val:protected] => B
            [val2:private] => 
            [val2] => BB
        )

    [2] => TestDerived Object
        (
            [row:protected] => 2
            [id] => 3
            [val:protected] => C
            [val2:private] => 
            [val2] => CC
        )

)