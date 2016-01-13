--TEST--
PDO_4D: no FLOAT support in PHP
--SKIPIF--
<?php # vim:ft=php
if (!extension_loaded('pdo')) die('skip no PDO');
if (!extension_loaded('pdo_4d')) die('skip no PDO for 4D extension');

require dirname(__FILE__) . '/../../../ext/pdo/tests/pdo_test.inc';

PDOTest::skip();
?>
--FILE--
<?php
require dirname(__FILE__) . '/../../../ext/pdo/tests/pdo_test.inc';
$db = PDOTest::test_factory(dirname(__FILE__) . '/common.phpt');

	$r = @$db->query('CREATE TABLE test (id INT, x FLOAT)');
	if ($r == true) {
		// tests insertions
		$r = $db->query('INSERT INTO test VALUES (0, 1)');

		if ($r == true) {
			var_dump(true);
			$r = @$db->query('SELECT * FROM test');
			$l = $r->fetchall();
			
			print_r($l);
			
		} else {
			print "  INSERTION $insertion : KO\n";
		}
	$db->query('DROP TABLE IF EXISTS test ');
}

?>
--EXPECTF--
bool(true)
Array
(
    [0] => Array
        (
            [id] => 0
            [0] => 0
            [x] => 
            [1] => 
        )

)