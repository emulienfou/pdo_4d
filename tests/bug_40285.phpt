--TEST--
PDO Common: Bug #40285 (The prepare parser goes into an infinite loop on ': or ":)
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

$db->exec('CREATE TABLE test (field1 VARCHAR(32), field2 VARCHAR(32), field3 VARCHAR(32), field4 INT)');

$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
$s = $db->prepare("INSERT INTO test VALUES( ':id', 'name', 'section', 22)" );
$s->execute();

echo "Done\n";
?>
--EXPECT--	
Done
