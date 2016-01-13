--TEST--
PDO_4D: Test getting text in ISO-8859-1 and UTF-8
--SKIPIF--
<?php # vim:ft=php
if (!extension_loaded('pdo')) die('skip');
if (!extension_loaded('SPL')) die('skip SPL not available');
//$dir = getenv('REDIR_TEST_DIR');
//if (false == $dir) die('skip no driver');
require_once $dir . 'pdo_test.inc';
if (!class_exists('RecursiveArrayIterator', false)) die('skip Class RecursiveArrayIterator missing');
if (!class_exists('RecursiveTreeIterator', false) && !file_exists(getenv('REDIR_TEST_DIR').'../../spl/examples/recursivetreeiterator.inc')) die('skip Class RecursiveTreeIterator missing');
PDOTest::skip();
?>
--FILE--
<?php
if (getenv('REDIR_TEST_DIR') === false) putenv('REDIR_TEST_DIR='.dirname(__FILE__) . '/../../pdo/tests/'); 
require_once getenv('REDIR_TEST_DIR') . 'pdo_test.inc';
if (!class_exists('RecursiveTreeIterator', false)) require_once(getenv('REDIR_TEST_DIR').'ext/spl/examples/recursivetreeiterator.inc'); 

$db = PDOTest::factory();

$db->exec('CREATE TABLE test (id TEXT)');

$chaine = 'ÉÈÀÇÉÙÉéèàçéùé';
$db->exec("INSERT INTO test values ('".$chaine."')");
unset($db);

$dsn = $config['ENV']['PDOTEST_DSN'];
$dsn = str_replace('UTF-8', '', $dsn);

$charsets = array('UTF-8','iso-8859-1');

foreach($charsets as $c) {
	print $dsn.$c."\n";
	$db = new PDO($dsn.$c, $config['ENV']['PDOTEST_USER'], $config['ENV']['PDOTEST_PASS'], array());

	$r = $db->query('SELECT * FROM test')->fetchAll();

	var_dump($r[0][0] == iconv('UTF-8',$c,$chaine));
	print "=======\n";
}


//$db->exec('DROP TABLE test');

?>
--EXPECTF--
4D:host=%s;charset=UTF-8
bool(true)
=======
4D:host=%s;charset=iso-8859-1
bool(true)
=======
