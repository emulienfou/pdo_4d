--TEST--
PDO Common: prepared statements and integers when strings are finally provided
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
$db = PDOTest::factory();
$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT );

$db->exec('CREATE TABLE test(id int NOT NULL PRIMARY KEY, classtype int, val VARCHAR(255))');

$stmt = $db->prepare('INSERT INTO test VALUES(:id, :classtype, :val)');
$stmt->bindParam(':id', $idx, PDO::PARAM_INT);
$stmt->bindParam(':classtype', $ctype, PDO::PARAM_INT);
$stmt->bindParam(':val', $val, PDO::PARAM_STR);

$ctype = 0;
$val = 'dur';

$objs = range(0, 1);

foreach($objs as $idx => $obj)
{
	$ctype = "$idx";
    
    @$stmt->execute();	

	$db->query("INSERT INTO test VALUES($idx + 10, $ctype, '$val')");
}

unset($stmt);

echo "===DATA===\n";
$result = $db->query('SELECT * FROM test');
if (is_object($result)) {
    print_r($result->fetchAll());
} else {
    print_r('Empty table');
}

/*

On 4D v11.4 (and not in 11.3).
-- EXPECT --
===DATA===
Array
(
    [0] => Array
        (
            [id] => 0
            [0] => 0
            [classtype] => 6621660
            [1] => 6621660
            [val] => dur
            [2] => dur
        )

    [1] => Array
        (
            [id] => 10
            [0] => 10
            [classtype] => 0
            [1] => 0
            [val] => dur
            [2] => dur
        )

    [2] => Array
        (
            [id] => 1
            [0] => 1
            [classtype] => 6621424
            [1] => 6621424
            [val] => dur
            [2] => dur
        )

    [3] => Array
        (
            [id] => 11
            [0] => 11
            [classtype] => 1
            [1] => 1
            [val] => dur
            [2] => dur
        )

)
*/

?>
--XFAIL--
This bug might be still open on aix5.2-ppc64 and hpux11.23-ia64
--EXPECT--
===DATA===
Empty table
