--TEST--
PDO_4D: multi-request test (forbiden)
--INI--
pdo_4d.timeout = 3
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
$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

$db->query('CREATE TABLE test ( c TEXT )');
$db->query("insert into test values ( 'sa;df' )");

$requetes = array('SELECT image, type FROM testImage;SELECT image, type FROM testImage',
                  "select *,[id';d],'to'';to','coucou[]' from toto;insert tata",
                  "select *,[id';d],'to'';to','coucou[]' from toto;\ninsert tata",
                  "select *,[id'une chaine'[]]tto;d],'to'';to','coucou[]' from toto; insert tata"
                    );


foreach($requetes as $id => $requete) {
		try {
            @$db->exec($requete);
		} catch (PDOException $e) {
//			print("message : " . $e->getMessage()."\n");
			print("$id) code : " . $e->getCode()."\n");
		}
}

// ces requetes doivent passer
$requetes = array('SELECT c as [oui;oui] FROM test',
                  'SELECT c as [oui;oui] FROM test;',
                  'SELECT c as [oui;oui] FROM test as [non;non]',
                  "SELECT c as [oui;oui] FROM test as [non;non] where c = ';'",
                    );


foreach($requetes as $id => $requete) {
		try {
            $r = $db->query($requete);
            var_dump(is_object($r));
		} catch (PDOException $e) {
//			print("message : " . $e->getMessage()."\n");
			print("$id) code : " . $e->getCode()."\n");
		}
}




$db->query('DROP TABLE test');

?>
--EXPECTF--
0) code : 0LP01
1) code : 0LP01
2) code : 0LP01
3) code : 0LP01
bool(true)
bool(true)
bool(true)
bool(true)