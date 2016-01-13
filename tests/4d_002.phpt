--TEST--
PDO 4D: Unicode Table Names
--SKIPIF--
<?php # vim:ft=php
if (!extension_loaded('pdo')) die('skip');
if (!interface_exists('Serializable')) die('skip no Serializable interface');
$dir = getenv('REDIR_TEST_DIR');
//if (false == $dir) die('skip no driver');
require_once $dir . 'pdo_test.inc';
PDOTest::skip();
?>
--FILE--
<?php
if (getenv('REDIR_TEST_DIR') === false) putenv('REDIR_TEST_DIR='.dirname(__FILE__) . '/../../pdo/tests/');
require_once getenv('REDIR_TEST_DIR') . 'pdo_test.inc';
$db = PDOTest::factory();

$db->setAttribute(PDO::FOURD_ATTR_CHARSET, 'UTF-8');
mb_internal_encoding("UTF-8");

$data = file(dirname(__FILE__) . '/test.data');
$x = array();
for ($i = 0;$i < 21; $i++) {
	$table = mb_substr($data[$i],0,3);

    $table = str_replace(array('"','('),'', $table);
    if (empty($table)){ continue;}

    $req = "CREATE TABLE [$table] (field1 INT, field2 VARCHAR(10), field3 BOOLEAN);";
//	print "$req\n";
	$db->query($req);

    $req = "INSERT INTO [$table] (field1,field2,field3) VALUES (1, 'ok', 2);";
//	print "$req\n";
    $db->query($req);

   	$q = $db->prepare("SELECT field1, field2, field3 FROM [$table]");
	$q->execute();
	$x[$i] = $q->fetch(PDO::FETCH_NUM);
	$db->query("DROP TABLE [$table];");
}
print_r($x);
?>
--EXPECTF--
Array
(
    [0] => Array
        (
            [0] => 1
            [1] => ok
            [2] => 0
        )

    [1] => Array
        (
            [0] => 1
            [1] => ok
            [2] => 0
        )

    [2] => Array
        (
            [0] => 1
            [1] => ok
            [2] => 0
        )

    [3] => Array
        (
            [0] => 1
            [1] => ok
            [2] => 0
        )

    [4] => Array
        (
            [0] => 1
            [1] => ok
            [2] => 0
        )

    [5] => Array
        (
            [0] => 1
            [1] => ok
            [2] => 0
        )

    [6] => Array
        (
            [0] => 1
            [1] => ok
            [2] => 0
        )

    [7] => Array
        (
            [0] => 1
            [1] => ok
            [2] => 0
        )

    [8] => Array
        (
            [0] => 1
            [1] => ok
            [2] => 0
        )

    [9] => Array
        (
            [0] => 1
            [1] => ok
            [2] => 0
        )

    [10] => Array
        (
            [0] => 1
            [1] => ok
            [2] => 0
        )

    [11] => Array
        (
            [0] => 1
            [1] => ok
            [2] => 0
        )

    [12] => Array
        (
            [0] => 1
            [1] => ok
            [2] => 0
        )

    [13] => Array
        (
            [0] => 1
            [1] => ok
            [2] => 0
        )

    [14] => Array
        (
            [0] => 1
            [1] => ok
            [2] => 0
        )

    [15] => Array
        (
            [0] => 1
            [1] => ok
            [2] => 0
        )

    [16] => Array
        (
            [0] => 1
            [1] => ok
            [2] => 0
        )

    [17] => Array
        (
            [0] => 1
            [1] => ok
            [2] => 0
        )

    [18] => Array
        (
            [0] => 1
            [1] => ok
            [2] => 0
        )

    [19] => Array
        (
            [0] => 1
            [1] => ok
            [2] => 0
        )

    [20] => Array
        (
            [0] => 1
            [1] => ok
            [2] => 0
        )

)