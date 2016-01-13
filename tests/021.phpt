--TEST--
PDO_4D: getting images (needs a testImage table with an 'image' PICTURE field with at least one reccord)
--INI--
pdo_4d.preferred_image_types = png
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
require dirname(__FILE__) . '/../../../ext/pdo/tests/pdo_test.inc';
$db = PDOTest::test_factory(dirname(__FILE__) . '/common.phpt');

var_dump($db->getAttribute(PDO::FOURD_ATTR_PREFERRED_IMAGE_TYPES) == 'png');

$types = array('gif','png','jpg','tiff','psd','pdf','unknown');

foreach($types as $type) {
  print "$type\n";
  var_dump($db->setAttribute(PDO::FOURD_ATTR_PREFERRED_IMAGE_TYPES, $type));
  var_dump($db->getAttribute(PDO::FOURD_ATTR_PREFERRED_IMAGE_TYPES) == $type);

}

?>
--EXPECTF--
bool(true)
gif
bool(true)
bool(true)
png
bool(true)
bool(true)
jpg
bool(true)
bool(true)
tiff
bool(true)
bool(true)
psd
bool(true)
bool(true)
pdf
bool(true)
bool(true)
unknown
bool(true)
bool(true)