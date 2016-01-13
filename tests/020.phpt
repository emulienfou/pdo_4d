--TEST--
PDO_4D: getting images (needs a testImage table with an 'image' PICTURE field with at least one reccord)
--INI--
pdo_4d.timeout = 3
pdo_4d.prefered_image_types = png
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

$tests_types = array( array('png'),
/*
                      array('gif','png'),
                      array('gif','jpg'),
                      array('jpg','png'),
                      array('jpg'),
                      array('gif'),
                      array('gif','png','jpg'),
                      */
);
//


foreach($tests_types as $types) {

    $r = @$db->query('SELECT image, type FROM testImage');
    $l = $r->fetchall();

    $retour = array();
    foreach($l as $ligne) {
        if (in_array($ligne['type'], $types)) {
            $retour[] = $ligne['type'];
        }
    }
    
    //print_r($l);

    var_dump(count(array_diff($retour, $types)) or count(array_diff($types, $retour)));
}

?>
--EXPECTF--
bool(false)
