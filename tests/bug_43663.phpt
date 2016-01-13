--TEST--
PDO Common: Bug #43663 (__call on classes derived from PDO)
--SKIPIF--
<?php # vim:ft=php
if (!extension_loaded('pdo')) die('skip no PDO');
if (!extension_loaded('sqlite')) die('skip no SQLite');
if (!extension_loaded('pdo_4d')) die('skip no PDO for 4D extension');

require dirname(__FILE__) . '/../../../ext/pdo/tests/pdo_test.inc';
?>
--FILE--
<?php
class test extends PDO{
    function __call($name, array $args) {
        echo "Called $name in ".__CLASS__."\n";
    }
    function foo() {
        echo "Called foo in ".__CLASS__."\n";
    }
}

if (getenv('REDIR_TEST_DIR') === false) putenv('REDIR_TEST_DIR='.dirname(__FILE__) . '/../../pdo/tests/');
require_once getenv('REDIR_TEST_DIR') . 'pdo_test.inc';

$a = new test('sqlite::memory:');
$a->foo();
$a->bar();
?>
--EXPECT--
Called foo in test
Called bar in test
