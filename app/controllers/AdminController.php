<?php

class AdminController extends BaseController {

	public function debug() {

	    echo '<pre>';

	    echo '<h1>environment.php</h1>';
	    $path   = base_path().'/environment.php';

	    try {
	        $contents = 'Contents: '.File::getRequire($path);
	        $exists = 'Yes';
	    }
	    catch (Exception $e) {
	        $exists = 'No. Defaulting to `production`';
	        $contents = '';
	    }


	    echo "Checking for: ".$path.'<br>';
	    echo 'Exists: '.$exists.'<br>';
	    echo $contents;
	    echo '<br>';



	    echo '<h1>Environment</h1>';
	    echo App::environment().'</h1>';

	    echo '<h1>Debugging?</h1>';
	    if(Config::get('app.debug')) echo "Yes"; else echo "No";


	    echo '<h1>Database Config</h1>';
	    print_r(Config::get('database.connections.mongodb'));

	    echo '<h1>Test Database Connection</h1>';

	    try {
	        $results = DB::connection('mongodb')->collection('data1');
	        echo '<strong style="background-color:green; padding:5px;">Connection confirmed</strong>';
	        //echo "<br><br>Your Databases:<br><br>";
	        //print_r($results);
	    } 
	    catch (Exception $e) {
	        echo '<strong style="background-color:crimson; padding:5px;">Caught exception: ', $e->getMessage(), "</strong>\n";
	    }

	    echo '<h1>Database Config</h1>';
	    print_r(Config::get('database.connections.mysql'));

	    echo '<h1>Test Database Connection</h1>';

	    try {
	        $results = DB::connection('mysql')->select('SHOW DATABASES;');
	        echo '<strong style="background-color:green; padding:5px;">Connection confirmed</strong>';
	        echo "<br><br>Your Databases:<br><br>";
	        print_r($results);
	    } 
	    catch (Exception $e) {
	        echo '<strong style="background-color:crimson; padding:5px;">Caught exception: ', $e->getMessage(), "</strong>\n";
	    }
	    echo '</pre>';
	}

	public function runQueueListen () {
		if (App::environment()=='local') {
		    $command = 'php artisan queue:listen > /dev/null & echo $!';
		    $number = exec($command);			    
		} else {
			exec('cd ~/app-root/repo');
			$php = exec('which php');
			$number = exec($php . 'artisan queue:listen > dev/null $ echo $!');
		}
		file_put_contents(__DIR__ . '/queue.pid', $number);
	}

	public function check_start_queue_listener () {

		if (file_exists(__DIR__ . '/queue.pid')) {
    		$pid = file_get_contents(__DIR__ . '/queue.pid');
    		$result = exec('pid | grep ' . $pid);
	    	if ($result == '') {
	        	$this->runQueueListen();
	        }
		} else {
	    	$this->runQueueListen();
		}
		return 'listening on queue';
	}

	public function test() {
	    $feed_status = DB::connection('mysql')->table('users_feeds')->where('feed_id', '1')->first()->feed_status;
	    echo $feed_status;

	    $command = 'php artisan queue:listen > /dev/null & echo $!';
	    $number = exec($command);
	    file_put_contents(__DIR__ . '/queue.pid', $number);
	}
}
