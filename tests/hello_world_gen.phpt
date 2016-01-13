--TEST--
hello_world_gen() function
--SKIPIF--
<?php 

if(!extension_loaded('pdo_4d')) die('skip ');

 ?>
--FILE--
<?php
echo 'OK'; // no test case for this function yet
?>
--EXPECT--
OK