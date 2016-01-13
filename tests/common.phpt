--TEST--
PDO for 4D
--SKIPIF--
<?php # vim:ft=php
  if (!extension_loaded('pdo') || !extension_loaded('pdo_4d')) print 'skip not loaded';
  require dirname(__FILE__) . '/config.inc';
  require './pdo_test.inc';
  PDOTest::skip();
?>
--REDIRECTTEST--
# magic auto-configuration


if (false !== getenv('PDO_4D_TEST_DSN')) {
	# user set them from their shell
	$config['ENV']['PDOTEST_DSN'] = getenv('PDO_4D_TEST_DSN');
	$config['ENV']['PDOTEST_USER'] = getenv('PDO_4D_TEST_USER');
	$config['ENV']['PDOTEST_PASS'] = getenv('PDO_4D_TEST_PASS');
	if (false !== getenv('PDO_4D_TEST_ATTR')) {
		$config['ENV']['PDOTEST_ATTR'] = getenv('PDO_4D_TEST_ATTR');
	}
} else {
	$config['ENV']['PDOTEST_DSN'] = '4D:host=localhost;charset=UTF-8';
//	$config['ENV']['PDOTEST_DSN'] = '4D:host=10.2.0.82;charset=UTF-8';
//	$config['ENV']['PDOTEST_DSN'] = '4D:host=10.2.0.159;charset=UTF-8';
	$config['ENV']['PDOTEST_USER'] = 'test';
	$config['ENV']['PDOTEST_PASS'] = 'test';
	putenv('REDIR_TEST_DIR=./');
}

return $config;
