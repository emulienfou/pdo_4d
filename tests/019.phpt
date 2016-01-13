--TEST--
PDO_4D: Test getting images (needs a testImage table with an 'image' PICTURE field with at least one reccord)
--SKIPIF--
<?php # vim:ft=php
if (!extension_loaded('pdo')) die('skip no PDO');
if (!extension_loaded('gd')) die('skip no GD');
if (!extension_loaded('pdo_4d')) die('skip no PDO for 4D extension');

require dirname(__FILE__) . '/../../../ext/pdo/tests/pdo_test.inc';
?>
--FILE--
<?php
$db = PDOTest::test_factory(dirname(__FILE__) . '/common.phpt');

$tests_types = array( array('gif','png'),
                      array('gif','jpg'),
                      array('jpg','png'),
                      array('jpg'),
                      array('png'),
                      array('gif'),
                      array('gif','png','jpg'),
);
//


foreach($tests_types as $types) {
    $db->setAttribute(PDO::FOURD_ATTR_PREFERRED_IMAGE_TYPES,join(' ', $types));

    $r = @$db->query('SELECT image, type FROM testImage');
    $l = $r->fetchall();

    $retour = array();
    foreach($l as $ligne) {
        if (in_array($ligne['type'], $types)) {
            $retour[] = $ligne['type'];
        }
    }

    var_dump(count(array_diff($retour, $types)) or count(array_diff($types, $retour)));
}

?>
--EXPECTF--
bool(false)
bool(false)
bool(false)
bool(false)
bool(false)
bool(false)
bool(false)