--TEST--
PDO_4D: send/receive image in a blob field
--SKIPIF--
<?php # vim:ft=php
if (!extension_loaded('pdo')) die('skip no PDO');
if (!extension_loaded('pdo_4d')) die('skip no PDO for 4D extension');
if (!extension_loaded('gd')) die('skip no gd extension');

require dirname(__FILE__) . '/../../../ext/pdo/tests/pdo_test.inc';

PDOTest::skip();
?>
--FILE--
<?php
require dirname(__FILE__) . '/../../../ext/pdo/tests/pdo_test.inc';
$db = PDOTest::test_factory(dirname(__FILE__) . '/common.phpt');

$im = @imagecreate(110, 20);

$background_color = imagecolorallocate($im, 0, 0, 0);
$text_color = imagecolorallocate($im, 233, 14, 91);

imagestring($im, 1, 5, 5,  "A Simple Test String", $text_color);
$image_path = "/tmp/test.4d.png";
imagepng($im,$image_path);

$db->query('CREATE TABLE test (id INT, img BLOB )');

$image_q = fopen($image_path, "r");

$stmt = $db->prepare('INSERT INTO test VALUES (0, ?)');
//$stmt->bindParam(1, $image_q); 

$stmt->bindValue(1,$image_q,PDO::PARAM_LOB);
$stmt->execute();

$r = @$db->query('SELECT * FROM test');

$l = $r->fetchall();

$image = file_get_contents($image_path);
var_dump($l[0]['img'] == $image);

$db->query('DROP TABLE IF EXISTS test ');
unlink($image_path);

?>
--EXPECTF--
bool(true)