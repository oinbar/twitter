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
	// array_push($out, exec('php artisan queue:listen PendingTwitterQueue --timeout=0 &> /dev/null &'));
	// // exec('disown');
	// array_push($out, exec('php artisan queue:listen PendingCalaisQueue --timeout=0 &> /dev/null &'));
	// // exec('disown');
	// array_push($out, exec('php artisan queue:listen PendingCalaisQueue --timeout=0 &> /dev/null &'));
	// // exec('disown');
	// array_push($out, exec('php artisan queue:listen PendingSUTimeQueue --timeout=0 &> /dev/null &'));
	// // exec('disown');
	// array_push($out, exec('php artisan queue:listen PendingSUTimeQueue --timeout=0 &> /dev/null &'));
	// // exec('disown');
	// array_push($out, exec('php artisan queue:listen PendingPersistenceQueue --timeout=0 &> /dev/null &'));
	// exec('disown');
// }

// elseif ($argv[1] == 'production') {
	// START QUEUE LISTENERS
array_push($out, exec('sudo -u www-data nice -10 php /home/upupup/prod/twitterintel/artisan queue:listen PendingTwitterQueue --timeout=600 &> /dev/null &> /dev/null &'));
exec('disown');
array_push($out, exec('sudo -u www-data nice -10 php /home/upupup/prod/twitterintel/artisan queue:listen PendingCalaisQueue --timeout=600 &> /dev/null &> /dev/null &'));
exec('disown');
array_push($out, exec('sudo -u www-data nice -10 php /home/upupup/prod/twitterintel/artisan queue:listen PendingCalaisQueue --timeout=600 &> /dev/null &> /dev/null &'));
exec('disown');
array_push($out, exec('sudo -u www-data nice -10 php /home/upupup/prod/twitterintel/artisan queue:listen PendingSUTimeQueue --timeout=600 &> /dev/null &> /dev/null &'));
exec('disown');
array_push($out, exec('sudo -u www-data nice -19 php /home/upupup/prod/twitterintel/artisan queue:listen PendingSUTimeQueue --timeout=600 &> /dev/null &> /dev/null &'));
exec('disown');
array_push($out, exec('sudo -u www-data nice -10 php /home/upupup/prod/twitterintel/artisan queue:listen PendingPersistenceQueue --timeout=600 &> /dev/null &> /dev/null &'));
// START REDIS
array_push($out, exec('sudo -u www-data nice -10 /usr/bin/redis-server /etc/redis/redis.conf &'));
exec('disown');
// }

// CREATE MONGO INDICES
$db = DB::connection('mongodb')->getMongoDB();								
$command = $db->execute('return db.data1.ensureIndex({ _id : -1 }, { background : true }).toArray() ;');
$command = $db->execute('return db.data1.ensureIndex({ feed_id : 1 }, { background : true }).toArray() ;');
$command = $db->execute('return db.data1.ensureIndex({ created_at : -1 }, { background : true }).toArray() ;');
$command = $db->execute('return db.data1.ensureIndex({ datetime : -1 }, { background : true }).toArray() ;');
$command = $db->execute('return db.data1.ensureIndex({ "opencalais._type" : 1 }, { background : true }).toArray() ;');
$command = $db->execute('return db.data1.ensureIndex({ retweet_count : -1 }, { background : true }).toArray() ;');
$command = $db->execute('return  .toArray() ;');

// // echo join('\n', $out);

// echo 'DONE';
