--TEST--
PDO Common: serializing
--SKIPIF--
<?php # vim:ft=php
if (!extension_loaded('pdo')) die('skip no PDO');
if (!extension_loaded('pdo_4d')) die('skip no PDO for 4D extension');

require dirname(__FILE__) . '/../../../ext/pdo/tests/pdo_test.inc';

PDOTest::skip();
?>
--FILE--
<?php
if (getenv('REDIR_TEST_DIR') === false) putenv('REDIR_TEST_DIR='.dirname(__FILE__) . '/../../pdo/tests/');
require_once getenv('REDIR_TEST_DIR') . 'pdo_test.inc';
$db = PDOTest::factory();

class TestBase implements Serializable
{
	public    $BasePub = 'Public';
	protected $BasePro = 'Protected';
	private   $BasePri = 'Private';
	
	function serialize()
	{
		$serialized = array();
		foreach($this as $prop => $val) {
			$serialized[$prop] = $val;
		}
		$serialized = serialize($serialized);
		echo __METHOD__ . "() = '$serialized'\n";
		return $serialized;
	}
	
	function unserialize($serialized)
	{
		echo __METHOD__ . "($serialized)\n";
		foreach(unserialize($serialized) as $prop => $val) {
			$this->$prop = '#'.$val;
		}
		return true;
	}
}

class TestDerived extends TestBase
{
	public    $BasePub    = 'DerivedPublic';
	protected $BasePro    = 'DerivdeProtected';
	public    $DerivedPub = 'Public';
	protected $DerivedPro = 'Protected';
	private   $DerivedPri = 'Private';

	function serialize()
	{
		echo __METHOD__ . "()\n";
		return TestBase::serialize();
	}
	
	function unserialize($serialized)
	{
		echo __METHOD__ . "()\n";
		return TestBase::unserialize($serialized);
	}
}

class TestLeaf extends TestDerived
{
}

$db->exec('CREATE TABLE classtypes(id int NOT NULL, name VARCHAR(20) NOT NULL,  PRIMARY KEY(id))');
$db->exec('INSERT INTO classtypes VALUES(0, \'stdClass\')'); 
$db->exec('INSERT INTO classtypes VALUES(1, \'TestBase\')'); 
$db->exec('INSERT INTO classtypes VALUES(2, \'TestDerived\')'); 
$db->exec('CREATE TABLE test(id int NOT NULL, classtype int, val VARCHAR(255),  PRIMARY KEY(id))');

$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

print_r($db->query('SELECT COUNT(*) FROM classtypes')->fetchColumn());
print_r($db->query('SELECT id, name FROM classtypes ORDER by id')->fetchAll(PDO::FETCH_COLUMN|PDO::FETCH_UNIQUE));

$objs = array();
$objs[0] = new stdClass;
$objs[1] = new TestBase;
$objs[2] = new TestDerived;
$objs[3] = new TestLeaf;

$stmt = $db->prepare('SELECT id FROM classtypes WHERE name=:cname');
$stmt->bindParam(':cname', $cname);

$ctypes = array();

foreach($objs as $obj)
{
	$cname = get_class($obj);
	$ctype = NULL; /* set default for non stored class name */
	$stmt->execute();
	$stmt->bindColumn('id', $ctype);
	$stmt->fetch(PDO::FETCH_BOUND);
	$ctypes[$cname] = $ctype;
}

echo "===TYPES===\n";
print_r($ctypes);

unset($stmt);

echo "===INSERT===\n";
$stmt = $db->prepare('INSERT INTO test VALUES(:id, :classtype, :val)');
$stmt->bindParam(':id', $idx);
$stmt->bindParam(':classtype', $ctype);
$stmt->bindParam(':val', $val);

foreach($objs as $idx => $obj)
{
    $idx += 0;
	$ctype = $ctypes[get_class($obj)] + 0;
	if (method_exists($obj, 'serialize'))
	{
		$val = $obj->serialize();
	}
	else
	{
		$val = '';
	}
	$stmt->execute();	
}

unset($stmt);

echo "===DATA===\n";
print_r($db->query('SELECT test.val FROM test')->fetchAll(PDO::FETCH_COLUMN));

echo "===FAILURE===\n";
try
{
	$db->query('SELECT classtypes.name AS name, test.val AS val FROM test JOIN classtypes ON test.classtype=classtypes.id')->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_CLASSTYPE|PDO::FETCH_SERIALIZE, 'TestLeaf', array());
}
catch (PDOException $e)
{
	echo 'Exception:';
	echo $e->getMessage()."\n";
}

echo "===COUNT===\n";
print_r($db->query('SELECT COUNT(*) FROM test, classtypes WHERE test.classtype=classtypes.id AND (classtypes.id IS NULL OR classtypes.id > 0)')->fetchColumn());

echo "===DATABASE===\n";
$stmt = $db->prepare('SELECT classtypes.name AS name, test.val AS val FROM test, classtypes WHERE test.classtype=classtypes.id AND (classtypes.id IS NULL OR classtypes.id > 0)');

$stmt->execute();
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

echo "===FETCHCLASS===\n";
$stmt->execute();
print_r($stmt->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_CLASSTYPE|PDO::FETCH_SERIALIZE, 'TestLeaf'));


?>
--EXPECTF--
3Array
(
    [0] => stdClass
    [1] => TestBase
    [2] => TestDerived
)
===TYPES===
Array
(
    [stdClass] => 0
    [TestBase] => 1
    [TestDerived] => 2
    [TestLeaf] => 
)
===INSERT===
TestBase::serialize() = 'a:3:{s:7:"BasePub";s:6:"Public";s:7:"BasePro";s:9:"Protected";s:7:"BasePri";s:7:"Private";}'
TestDerived::serialize()
TestBase::serialize() = 'a:5:{s:7:"BasePub";s:13:"DerivedPublic";s:7:"BasePro";s:16:"DerivdeProtected";s:10:"DerivedPub";s:6:"Public";s:10:"DerivedPro";s:9:"Protected";s:7:"BasePri";s:7:"Private";}'
TestDerived::serialize()
TestBase::serialize() = 'a:5:{s:7:"BasePub";s:13:"DerivedPublic";s:7:"BasePro";s:16:"DerivdeProtected";s:10:"DerivedPub";s:6:"Public";s:10:"DerivedPro";s:9:"Protected";s:7:"BasePri";s:7:"Private";}'
===DATA===
Array
(
    [0] => 
    [1] => a:3:{s:7:"BasePub";s:6:"Public";s:7:"BasePro";s:9:"Protected";s:7:"BasePri";s:7:"Private";}
    [2] => a:5:{s:7:"BasePub";s:13:"DerivedPublic";s:7:"BasePro";s:16:"DerivdeProtected";s:10:"DerivedPub";s:6:"Public";s:10:"DerivedPro";s:9:"Protected";s:7:"BasePri";s:7:"Private";}
    [3] => a:5:{s:7:"BasePub";s:13:"DerivedPublic";s:7:"BasePro";s:16:"DerivdeProtected";s:10:"DerivedPub";s:6:"Public";s:10:"DerivedPro";s:9:"Protected";s:7:"BasePri";s:7:"Private";}
)
===FAILURE===
Exception:SQLSTATE[42601]: Syntax error: 1301 Failed to parse statement.
===COUNT===
2===DATABASE===
Array
(
    [0] => Array
        (
            [name] => TestBase
            [val] => a:3:{s:7:"BasePub";s:6:"Public";s:7:"BasePro";s:9:"Protected";s:7:"BasePri";s:7:"Private";}
        )

    [1] => Array
        (
            [name] => TestDerived
            [val] => a:5:{s:7:"BasePub";s:13:"DerivedPublic";s:7:"BasePro";s:16:"DerivdeProtected";s:10:"DerivedPub";s:6:"Public";s:10:"DerivedPro";s:9:"Protected";s:7:"BasePri";s:7:"Private";}
        )

)
===FETCHCLASS===
TestBase::unserialize(a:3:{s:7:"BasePub";s:6:"Public";s:7:"BasePro";s:9:"Protected";s:7:"BasePri";s:7:"Private";})
TestDerived::unserialize()
TestBase::unserialize(a:5:{s:7:"BasePub";s:13:"DerivedPublic";s:7:"BasePro";s:16:"DerivdeProtected";s:10:"DerivedPub";s:6:"Public";s:10:"DerivedPro";s:9:"Protected";s:7:"BasePri";s:7:"Private";})
Array
(
    [0] => TestBase Object
        (
            [BasePub] => #Public
            [BasePro:protected] => #Protected
            [BasePri:private] => #Private
        )

    [1] => TestDerived Object
        (
            [BasePub] => #DerivedPublic
            [BasePro:protected] => #DerivdeProtected
            [DerivedPub] => #Public
            [DerivedPro:protected] => #Protected
            [DerivedPri:private] => Private
            [BasePri:private] => #Private
        )

)