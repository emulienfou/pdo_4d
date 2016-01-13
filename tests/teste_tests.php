<?php  

$methods = array( 'begintransaction', 'commit', 'errorcode', 'errorinfo', 'exec', 'getattribute', 'getavailabledrivers', 'lastinsertid', 'prepare', 'query', 'quote', 'rollback', 'setattribute', 'bindcolumn', 'bindparam', 'bindvalue', 'closecursor', 'columncount', 'errorcode', 'errorinfo', 'execute', 'fetch', 'fetchall', 'fetchcolumn', 'fetchobject', 'getattribute', 'getcolumnmeta', 'nextrowset', 'rowcount', 'setattribute', 'setfetchmode');

$regex = '#('.strtolower(join('|', $methods)).')#is';

$fichiers = glob('*.phpt');
// inclusion des fichiers de pdo aussi
$fichiers2 = glob('../../pdo/tests/*.phpt');
$fichiers = array_merge($fichiers, $fichiers2);

$trouves = array();
foreach($fichiers as $f) {
	$test = file_get_contents($f);
	$test = strtolower($test);
	
	preg_match_all($regex, $test, $r);
	
	$trouves = array_merge($trouves, $r[1]);
}

$stats = array_count_values($trouves);
asort($stats);
print "trouves\n";
print_r($stats);

print "manques\n";
$manquent = array_diff($methods, array_keys($stats));
print_r($manquent);

?>