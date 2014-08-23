<?php

// echo "argv[] = ";
// print_r($argv);  // just to see what was passed in


// if ($argc > 0)
// {
//   for ($i=1;$i < $argc;$i++)
//   {
//     parse_str($argv[$i],$tmp);
//     $_REQUEST = array_merge($_REQUEST, $tmp);
//   }
// }

// echo "\$_REQUEST = ";
// print_r($_REQUEST);

// if ($argc == 0) {
// 	echo 'no args';
// 	die();
// }


$out = array();
// if  ($argv[1] == 'local') {
	// START QUEUE LISTENERS	
	// array_push($out, exec('php artisan queue:listen PendingTwitterQueue --timeout=600 &'));
	// // exec('disown');
	// array_push($out, exec('php artisan queue:listen PendingCalaisQueue --timeout=600 &'));
	// // exec('disown');
	// array_push($out, exec('php artisan queue:listen PendingCalaisQueue --timeout=600 &'));
	// // exec('disown');
	// array_push($out, exec('php artisan queue:listen PendingSUTimeQueue --timeout=600 &'));
	// // exec('disown');
	// array_push($out, exec('php artisan queue:listen PendingSUTimeQueue --timeout=600 &'));
	// // exec('disown');
	// array_push($out, exec('php artisan queue:listen PendingPersistenceQueue --timeout=600 &'));
	// exec('disown');
// }

// elseif ($argv[1] == 'production') {
	// START QUEUE LISTENERS
array_push($out, exec('php /var/app/current/artisan queue:listen PendingTwitterQueue --timeout=600 &'));
exec('disown');
array_push($out, exec('php /var/app/current/artisan queue:listen PendingCalaisQueue --timeout=600 &'));
exec('disown');
array_push($out, exec('php /var/app/current/artisan queue:listen PendingCalaisQueue --timeout=600 &'));
exec('disown');
array_push($out, exec('php /var/app/current/artisan queue:listen PendingSUTimeQueue --timeout=600 &'));
exec('disown');
array_push($out, exec('php /var/app/current/artisan queue:listen PendingSUTimeQueue --timeout=600 &'));
exec('disown');
array_push($out, exec('php /var/app/current/artisan queue:listen PendingPersistenceQueue --timeout=600 &'));
// START REDIS
array_push($out, exec(' /var/app/twitterintelLibs/redis-stable/src/redis-server /var/app/twitterintelLibs/redis-stable/redis.conf &'));
exec('disown');
// }

// // echo join('\n', $out);

// echo 'DONE';
