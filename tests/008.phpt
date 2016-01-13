--TEST--
PDO_4D: Create table and insert a record with each supported field type 
--SKIPIF--
<?php # vim:ft=php
if (!extension_loaded('pdo')) die('skip no PDO');
if (!extension_loaded('pdo_4d')) die('skip no PDO for 4D extension');

require dirname(__FILE__) . '/../../../ext/pdo/tests/pdo_test.inc';

PDOTest::skip();
?>
--FILE--
<?php
require dirname(__FILE__) . '/../../../ext/pdo/tests/pdo_test.inc';
$db = PDOTest::test_factory(dirname(__FILE__) . '/common.phpt');

$types = array( 'ALPHA_NUMERIC' => "'a'",
				'VARCHAR'=> "'a'",
				'TEXT'=> "'a'",
				'CHAR'=> false,
				'TIMESTAMP'=> "'20 03 2009'", // 11:38:29:123',
				//Jour Mois Année Parties Date et Heure Heures:Minutes:Secondes:Millisecondes 
				'INTERVAL'=> "'1:11:20:21'",
				//Jour:Heure:Minutes:Secondes: Heure Millisecondes
				'DURATION'=> "'1:11:20:21'",
				'BOOLEAN'=> '1',
				'BIT'=> '1',
				'BYTE'=> '1',
				'INT16'=> '1',
				'SMALLINT'=> '1',
				'INT32'=> '1',
				'INT'=> '1',
				'INT64'=> '1',
				'NUMERIC'=> '1',
				'REAL'=> '1',
				'FLOAT'=> '1',
				'DOUBLE PRECISION'=> '1',
				'BLOB'=> "a",
				'BIT VARYING'=> "a",
				'CLOB'=> "'a'",
				'PICTURE' => "1234567890");

foreach($types as $type => $insertion) {

	$r = @$db->query('CREATE TABLE test (id INT, x '.$type.')');
	if ($r == true) {
		print "$type : OK\n";
		// tests insertions
		switch($type) {
			case 'BLOB':
			case 'BIT VARYING':
			case 'PICTURE':
				$stmt=$db->prepare('INSERT INTO test VALUES (0, ?)');
				$fp = tmpfile();
				fwrite($fp, $insertion);
				rewind($fp);
				$stmt->bindValue(1,$fp,PDO::PARAM_LOB);
				$r=$stmt->execute();
			break;
			default:
				$r = @$db->query('INSERT INTO test VALUES (0, '.$insertion.')');
			break;
		}

		if ($r == true) {
			print "  INSERTION $insertion : OK\n";
		} else {
			print "  INSERTION $insertion : KO\n";
		}
	
	} else {
		print "$type : KO\n";
	}

	$db->query('DROP TABLE IF EXISTS test ');
}

?>
--EXPECTF--
ALPHA_NUMERIC : OK
  INSERTION 'a' : OK
VARCHAR : OK
  INSERTION 'a' : OK
TEXT : OK
  INSERTION 'a' : OK
CHAR : KO
TIMESTAMP : OK
  INSERTION '20 03 2009' : OK
INTERVAL : OK
  INSERTION '1:11:20:21' : OK
DURATION : OK
  INSERTION '1:11:20:21' : OK
BOOLEAN : OK
  INSERTION 1 : OK
BIT : OK
  INSERTION 1 : OK
BYTE : OK
  INSERTION 1 : OK
INT16 : OK
  INSERTION 1 : OK
SMALLINT : OK
  INSERTION 1 : OK
INT32 : OK
  INSERTION 1 : OK
INT : OK
  INSERTION 1 : OK
INT64 : OK
  INSERTION 1 : OK
NUMERIC : OK
  INSERTION 1 : OK
REAL : OK
  INSERTION 1 : OK
FLOAT : OK
  INSERTION 1 : OK
DOUBLE PRECISION : OK
  INSERTION 1 : OK
BLOB : OK
  INSERTION a : OK
BIT VARYING : OK
  INSERTION a : OK
CLOB : OK
  INSERTION 'a' : OK
PICTURE : OK
  INSERTION 1234567890 : OK