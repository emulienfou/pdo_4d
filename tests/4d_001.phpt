--TEST--
PDO_4D: Unicode - SQL INSERT - use text arrays to insert Unicode strings (Japanese, Arabic, German and Hebraic...)
--SKIPIF--
<?php # vim:ft=php
if (!extension_loaded('pdo')) die('skip no PDO');
if (!extension_loaded('pdo_4d')) die('skip no PDO for 4D extension');

require dirname(__FILE__) . '/../../../ext/pdo/tests/pdo_test.inc';

PDOTest::skip();
?>
--FILE--
<?php
/*
$lang = array(
	'Japanese',
	'Arabic',
	'Arabic',
	'English',
	'German',
	'Hebraic',
	'Chinese',
	'?',
	'?',
	'?',
	'?',
	'?',
	'?',
	'?',
	'?',
	'?',
	'?',
	'?',
	'?',
	'Italian',
	'Spanish'
);
*/
require dirname(__FILE__) . '/../../../ext/pdo/tests/pdo_test.inc';
$db = PDOTest::test_factory(dirname(__FILE__) . '/common.phpt');

$db->query('CREATE TABLE T_RemoteUnicode(F_Id INT32, F_Text VARCHAR);');

// Insertion of the Unicode strings
$r = $db->prepare("INSERT INTO T_RemoteUnicode(F_Id,F_Text) VALUES (:vaL_Input, :vaT_Input);");
//$r = $db->prepare("INSERT INTO T_RemoteUnicode(F_Id,F_Text) VALUES (?, ?);");

$i = 0;
$text = '';
$r->bindParam(':vaL_Input', $i, PDO::PARAM_INT);
$r->bindParam(':vaT_Input', $text, PDO::PARAM_STR);
//$r->bindValue(1, $i, PDO::PARAM_INT);
//$r->bindValue(2, $text, PDO::PARAM_STR);

$data = file(dirname(__FILE__) . '/test.data');
$max = count($data);
for ($i = 0;$i < $max; $i++) {
	$text = $data[$i];
	$r->execute();
}

// Read the inserted Unicode strings
$r = $db->prepare('SELECT F_Id,F_Text FROM T_RemoteUnicode ORDER BY F_Id ASC;');
$r->execute();
$r->bindColumn(1, $id);
$r->bindColumn(2, $text);

$x = array();
while ($r->fetch(PDO::FETCH_BOUND)) {
	$x[$id]['id'] = $id;
	$x[$id]['ok'] = (($data[$id] === $text) ? 'Passed' : 'Failed');
//	$x[$id]['ok'] .= ' (' . $lang[$i] . ')';
}

// Cleanup of the test case
$db->query('DROP TABLE T_RemoteUnicode;');

print_r($x);
?>
--EXPECTF--
Array
(
    [0] => Array
        (
            [id] => 0
            [ok] => Passed
        )

    [1] => Array
        (
            [id] => 1
            [ok] => Passed
        )

    [2] => Array
        (
            [id] => 2
            [ok] => Passed
        )

    [3] => Array
        (
            [id] => 3
            [ok] => Passed
        )

    [4] => Array
        (
            [id] => 4
            [ok] => Passed
        )

    [5] => Array
        (
            [id] => 5
            [ok] => Passed
        )

    [6] => Array
        (
            [id] => 6
            [ok] => Passed
        )

    [7] => Array
        (
            [id] => 7
            [ok] => Passed
        )

    [8] => Array
        (
            [id] => 8
            [ok] => Passed
        )

    [9] => Array
        (
            [id] => 9
            [ok] => Passed
        )

    [10] => Array
        (
            [id] => 10
            [ok] => Passed
        )

    [11] => Array
        (
            [id] => 11
            [ok] => Passed
        )

    [12] => Array
        (
            [id] => 12
            [ok] => Passed
        )

    [13] => Array
        (
            [id] => 13
            [ok] => Passed
        )

    [14] => Array
        (
            [id] => 14
            [ok] => Passed
        )

    [15] => Array
        (
            [id] => 15
            [ok] => Passed
        )

    [16] => Array
        (
            [id] => 16
            [ok] => Passed
        )

    [17] => Array
        (
            [id] => 17
            [ok] => Passed
        )

    [18] => Array
        (
            [id] => 18
            [ok] => Passed
        )

    [19] => Array
        (
            [id] => 19
            [ok] => Passed
        )

    [20] => Array
        (
            [id] => 20
            [ok] => Passed
        )

    [21] => Array
        (
            [id] => 21
            [ok] => Passed
        )

    [22] => Array
        (
            [id] => 22
            [ok] => Passed
        )

    [23] => Array
        (
            [id] => 23
            [ok] => Passed
        )

    [24] => Array
        (
            [id] => 24
            [ok] => Passed
        )

    [25] => Array
        (
            [id] => 25
            [ok] => Passed
        )

    [26] => Array
        (
            [id] => 26
            [ok] => Passed
        )

    [27] => Array
        (
            [id] => 27
            [ok] => Passed
        )

    [28] => Array
        (
            [id] => 28
            [ok] => Passed
        )

    [29] => Array
        (
            [id] => 29
            [ok] => Passed
        )

    [30] => Array
        (
            [id] => 30
            [ok] => Passed
        )

    [31] => Array
        (
            [id] => 31
            [ok] => Passed
        )

    [32] => Array
        (
            [id] => 32
            [ok] => Passed
        )

    [33] => Array
        (
            [id] => 33
            [ok] => Passed
        )

    [34] => Array
        (
            [id] => 34
            [ok] => Passed
        )

    [35] => Array
        (
            [id] => 35
            [ok] => Passed
        )

    [36] => Array
        (
            [id] => 36
            [ok] => Passed
        )

    [37] => Array
        (
            [id] => 37
            [ok] => Passed
        )

    [38] => Array
        (
            [id] => 38
            [ok] => Passed
        )

    [39] => Array
        (
            [id] => 39
            [ok] => Passed
        )

    [40] => Array
        (
            [id] => 40
            [ok] => Passed
        )

    [41] => Array
        (
            [id] => 41
            [ok] => Passed
        )

    [42] => Array
        (
            [id] => 42
            [ok] => Passed
        )

    [43] => Array
        (
            [id] => 43
            [ok] => Passed
        )

    [44] => Array
        (
            [id] => 44
            [ok] => Passed
        )

    [45] => Array
        (
            [id] => 45
            [ok] => Passed
        )

    [46] => Array
        (
            [id] => 46
            [ok] => Passed
        )

    [47] => Array
        (
            [id] => 47
            [ok] => Passed
        )

    [48] => Array
        (
            [id] => 48
            [ok] => Passed
        )

    [49] => Array
        (
            [id] => 49
            [ok] => Passed
        )

    [50] => Array
        (
            [id] => 50
            [ok] => Passed
        )

    [51] => Array
        (
            [id] => 51
            [ok] => Passed
        )

    [52] => Array
        (
            [id] => 52
            [ok] => Passed
        )

    [53] => Array
        (
            [id] => 53
            [ok] => Passed
        )

    [54] => Array
        (
            [id] => 54
            [ok] => Passed
        )

    [55] => Array
        (
            [id] => 55
            [ok] => Passed
        )

)
