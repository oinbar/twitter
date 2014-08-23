<?php

echo "argv[] = ";
print_r($argv);  // just to see what was passed in


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


$out = array();
if  ($argv[1] == 'local') {
	// START QUEUE LISTENERS	
	array_push($results, exec('php artisan queue:listen PendingTwitterQueue --timeout=600'));
	array_push($results, exec('php artisan queue:listen PendingCalaisQueue --timeout=600');
	array_push($results, exec('php artisan queue:listen PendingCalaisQueue --timeout=600');
	array_push($results, exec('php artisan queue:listen PendingSUTimeQueue --timeout=600');
	array_push($results, exec('php artisan queue:listen PendingSUTimeQueue --timeout=600');
	array_push($results, exec('php artisan queue:listen PendingPersistenceQueue --timeout=600');
}

else {
	// START QUEUE LISTENERS
	array_push($results, exec('php php /var/app/current/artisan queue:listen PendingTwitterQueue --timeout=600');
	array_push($results, exec('php php /var/app/current/artisan queue:listen PendingCalaisQueue --timeout=600');
	array_push($results, exec('php php /var/app/current/artisan queue:listen PendingCalaisQueue --timeout=600');
	array_push($results, exec('php php /var/app/current/artisan queue:listen PendingSUTimeQueue --timeout=600');
	array_push($results, exec('php php /var/app/current/artisan queue:listen PendingSUTimeQueue --timeout=600');
	array_push($results, exec('php php /var/app/current/artisan queue:listen PendingPersistenceQueue --timeout=600');
}

echo join('\n', $out);
	// START REDIS
	array_push($results, exec(' /var/app/twitterintelLibs/redis-stable/src/redis-server /var/app/twitterintelLibs/redis-stable/redis.conf');


