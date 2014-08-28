<?php

class AdminController extends BaseController {

	private $calais_key1 = 'qupquc5c4qzj7sg9knu5ad4w';
	private $calais_key2 = 'cxxf222kq5thbjcmtmxw8hgv';	
	private $calais_key3 = 'cytb2vdruh7r3wwx6vemvgft';
	private $calais_key4 = 'fvtjme9xyx2r54kuqxy9d9zf';


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
	    	$redis->ping();
	    	echo '<strong style="background-color:green; padding:5px;">Connection confirmed</strong>';
		}
		catch (Exception $e) {
	        echo '<strong style="background-color:crimson; padding:5px;">Caught exception: ', $e->getMessage(), "</strong>\n";
	    }

	    echo '</pre>';	    
	}

	private function runQueueListener ($queue) {
		if (App::environment()=='local') {
		    $command = 'php artisan queue:listen ' . $queue . ' --timeout=600 > /dev/null & echo $!';
		    $pid = exec($command);			    
		} else {
			$command = 'php /var/app/current/artisan queue:listen ' . $queue . ' --timeout=600';
			$pid = exec($command);			
		}
	}

	public function check_start_queue_listener ($queue) {		
		// checks if the queue listener is running, if not it starts it and
		// stores the process id
			// if (file_exists(__DIR__ . '/temp/' . $queue . '_queue.pid')) {
	  //   		$pid = file_get_contents(__DIR__ . '/temp/' . $queue . '_queue.pid');	    		

			//     $result = exec('kill -p ' . $pid);

			//     if ($result == '') {
		 //        	$this->runQueueListener($queue);
		 //        }
			// } else {
    	$this->runQueueListener($queue);
	}



	public function load_queues () {
		try {
			// this triggers the necessary jobs in QueueTasks by calling initiating them (they are cyclic).
			Queue::connection('PendingCalaisQueue')->push('QueueTasks@runJsonThroughCalaisJob', array('calais_key' =>  $this->calais_key3));
			Queue::connection('PendingCalaisQueue')->push('QueueTasks@runJsonThroughCalaisJob', array('calais_key' =>  $this->calais_key4));
			Queue::connection('PendingSUTimeQueue')->push('QueueTasks@runJsonThroughSUTimeJob');
			Queue::connection('PendingSUTimeQueue')->push('QueueTasks@runJsonThroughSUTimeJob');
			Queue::connection('PendingPersistenceQueue')->push('QueueTasks@insertJsonToDBJob');   
		} catch (Ecxeption $e) {
			Log::error('LOAD QUEUE ERROR: ' . $e);
		}
	}

	public function start_queue_listeners () {
		try {
			$this->check_start_queue_listener('PendingTwitterQueue');
			$this->check_start_queue_listener('PendingCalaisQueue');
			$this->check_start_queue_listener('PendingCalaisQueue');
			$this->check_start_queue_listener('PendingSUTimeQueue');
			$this->check_start_queue_listener('PendingSUTimeQueue');
			$this->check_start_queue_listener('PendingPersistenceQueue');
		} catch (Exception $e) {
			Log::error('START LISTENER ERROR: ' . $e);
		} 
		echo 'done';
	}

	private function convertMongoJavascriptToPhp ($string) {
		$string = str_replace('.', '->', $string);
		$string = str_replace(':', '=>', $string);
		$string = str_replace('{', 'array(', $string);
		$string = str_replace('}', ')', $string);
		return $string;
	} 

	public function mongoQuery() {
		try{
			$query = Input::get('query');
			if ($query) {				
				$db = DB::connection('mongodb')->getMongoDB();								
				$results = $db->execute('return ' . $query . ';');
				return View::make('adminViews/mongo_query')
							->with('query', $query)
							->with('results', $results);							
			}
			else {
				return View::make('adminViews/mongo_query')
							->with('query', '')
							->with('results', '');
			}			
		}
		catch (Exception $e){
			Log::error('MONGO QUERY:  '. $e);
		}	
	}

	public function cacheView () {		
		$redis = Redis::connection();
		return View::make('adminViews/cache_view')
			->with('sizePendingCalaisList',$redis->llen('PendingCalaisList'))
			->with('sizePendingSUTimeList',$redis->llen('PendingSUTimeList'))
			->with('sizePendingPersistenceList',$redis->llen('PendingPersistenceList'));
	}

	public function test () {
		include __DIR__.'/../open_calais_dg/opencalais.php';

		$content = 'Next week I am flying to washington dc for an AIPAC conference';
		$oc = new OpenCalais($this->calais_key1);
		$results = json_decode($oc->getResult($content), true);

		echo Pre::render($results);		

		// file_put_contents('/Users/Orr/Desktop/.json', json_encode($results));
	}

	private function fixSUTime ($date_str) {
		$date_str = str_replace('TMO', ' 08:00' ,$date_str);
		$date_str = str_replace('TAF', ' 15:00' ,$date_str);
		$date_str = str_replace('TEV', ' 19:00' ,$date_str);
		$date_str = str_replace('TNI', ' 21:00' ,$date_str);
		$date_str = preg_replace('/(?<=\d)T/', ' ' ,$date_str);		
		$date_str = preg_replace('/-W\d\d/', '', $date_str); //-W35
		$date_str = preg_replace('/-W..-\d/', '', $date_str); //-WXX-5		
		return $date_str;
	}

	public function dateTimeDiffDays ($date_str1, $date_str2){
		// echo $this->fixSUTime($date_str1).'<br>';
		// $date_str1 = new DateTime(date('Y-m-d H:i:s', strtotime($this->fixSUTime($date_str1))));

		// echo $this->fixSUTime($date_str2).'<br>';		
		// $date_str2 = new DateTime(date('Y-m-d H:i:s', strtotime($this->fixSUTime($date_str2))));		
		
		// $interval = $date_str2->diff($date_str1);
		// return $interval->format('%h');
		return (strtotime($this->fixSUTime($date_str2))-strtotime($this->fixSUTime($date_str1)))/60/60/24;
	}

	public function test2 () {

		// echo $this->fixSUTime('2014-08-23T15:00');
		//echo $this->dateTimeDiffDays('Wed Aug 20 18:52:33 +0000 2014', '2014-08-23-WXX-6T15:00');

		// echo date("Y-m-d H:i:s" , strtotime(time())); 
		// echo date('Y-m-d H:i:s');

		$result=exec('/usr/bin/java -jar /home/ubuntu/prod/lib/SUTime.jar /home/ubuntu/prod/lib/sutimedata.txt');
		echo Pre::render($result);

	}
}

