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


	    echo '<h1>Database Config (MongoDB)</h1>';
	    print_r(Config::get('database.connections.mongodb'));	
	    try {
	        $results = DB::connection('mongodb')->collection('data1');
	        echo '<strong style="background-color:green; padding:5px;">Connection confirmed</strong>';
	    } 
	    catch (Exception $e) {
	        echo '<strong style="background-color:crimson; padding:5px;">Caught exception: ', $e->getMessage(), "</strong>\n";
	    }


	    echo '<h1>Database Config (MySQL)</h1>';
	    print_r(Config::get('database.connections.mysql'));
	    try {
	        $results = DB::connection('mysql')->select('SHOW DATABASES;');
	        echo '<strong style="background-color:green; padding:5px;">Connection confirmed</strong>';
	    } 
	    catch (Exception $e) {
	        echo '<strong style="background-color:crimson; padding:5px;">Caught exception: ', $e->getMessage(), "</strong>\n";
	    }

	    echo '<h1>Cache Config (Redis)</h1>';
	    print_r(Config::get('database.connections.redis'));
	    try {
	    	$redis = Redis::connection();
	    	echo '<strong style="background-color:green; padding:5px;">Connection confirmed</strong>';
		}
		catch (Exception $e) {
	        echo '<strong style="background-color:crimson; padding:5px;">Caught exception: ', $e->getMessage(), "</strong>\n";
	    }

	    echo '</pre>';	    
	}

	private function runQueueListener ($queue) {
		if (App::environment()=='local') {
		    $command = 'php artisan queue:listen ' . $queue . ' > /dev/null & echo $!';
		    $number = exec($command);			    
		} else {
			exec('cd ~/app-root/repo');
			$php = exec('which php');
			$number = exec($php . ' artisan queue:listen ' . $queue . ' > /dev/null $ echo $!');
		}
		file_put_contents(__DIR__ . '/' . $queue . '_queue.pid', $number);
	}

	public function check_start_queue_listener ($queue) {
		// checks if the queue listener is running, if not it starts it and
		// stores the process id
			if (file_exists(__DIR__ . '/' . $queue . '_queue.pid')) {
	    		$pid = file_get_contents(__DIR__ . '/' . $queue . '_queue.pid');	    		

	    		Log::error('pid   ' . $pid);

			    $result = exec('kill -p ' . $pid);

			    Log::error('result   ' . $result);

			    if ($result == '') {
		        	$this->runQueueListener($queue);
		        }
			} else {
		    	$this->runQueueListener($queue);
		}
	}

	public function start_queue_listeners () {
		$this->check_start_queue_listener('twitter_fetch');
		$this->check_start_queue_listener('cache_to_db');

		//start cache to db
		Queue::connection('cache_to_db')
	}
}

