<?php
return array (
	'type' => 'file',
	'debug' => true,	
	'timeout' => 0,
	'file_cache' => array(
		'suf' => '.cache.php',
		'type' => 'array',
	),
	'memcache' => array(
		'pconnect' => 0,
		'autoconnect' => 0,
		'hostname' => '127.0.0.1',
		'port' => 11211,
	),
);