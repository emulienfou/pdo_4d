--TEST--
PDO Common: Get rounded value from a FLOAT field
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

$db = PDOTest::factory();

$db->exec('CREATE TABLE test(id FLOAT)');

$n = "1.";
for($i = 2; $i < 10; $i++) {
	$n .= $i;
	var_dump($db->exec('INSERT INTO test values ('.$n.')'));
}

print "\n-- recuperation --\n";

$r = $db->prepare("SELECT ROUND(id, 10) as id0, trunc(id, 10) as id1 FROM test");
$r->execute();

$x1 = $r->fetchall();

print_r($x1);

$db->exec('DROP TABLE test');

?>
--EXPECT--
int(1)
int(1)
int(1)
int(1)
int(1)
int(1)
int(1)
int(1)

-- recuperation --
Array
(
    [0] => Array
        (
            [id0] => 1.200000
            [0] => 1.200000
            [id1] => 1.200000
            [1] => 1.200000
        )

    [1] => Array
        (
            [id0] => 1.230000
            [0] => 1.230000
            [id1] => 1.230000
            [1] => 1.230000
        )

    [2] => Array
        (
            [id0] => 1.234000
            [0] => 1.234000
            [id1] => 1.234000
            [1] => 1.234000
        )

    [3] => Array
        (
            [id0] => 1.234500
            [0] => 1.234500
            [id1] => 1.234500
            [1] => 1.234500
        )

    [4] => Array
        (
            [id0] => 1.234560
            [0] => 1.234560
            [id1] => 1.234560
            [1] => 1.234560
        )

    [5] => Array
        (
            [id0] => 1.234567
            [0] => 1.234567
            [id1] => 1.234567
            [1] => 1.234567
        )

    [6] => Array
        (
            [id0] => 1.234568
            [0] => 1.234568
            [id1] => 1.234568
            [1] => 1.234568
        )

    [7] => Array
        (
            [id0] => 1.234568
            [0] => 1.234568
            [id1] => 1.234568
            [1] => 1.234568
        )

)