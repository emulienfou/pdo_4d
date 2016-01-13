--TEST--
PDO_4D: tests des configuration de retour d'erreur
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
$dbh = PDOTest::factory();

echo "silence\n";

$dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT );
$dbh->exec('erreur flagrante SQL');

echo "---\n";
$dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );

@$dbh->exec('erreur flagrante SQL');
$erreur = $dbh->errorInfo();
print_r($erreur);

echo "---\n";
echo "exceptions\n";

$dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

		try {
$dbh->exec('erreur flagrante SQL');
		} catch (PDOException $e) {
			print("message : " . $e->getMessage()."\n");
			print("code : " . $e->getCode()."\n");
		}
?>
--EXPECT--
silence
---
Array
(
    [0] => 42601
    [1] => 1301
    [2] => Failed to parse statement.
)
---
exceptions
message : SQLSTATE[42601]: Syntax error: 1301 Failed to parse statement.
code : 42601