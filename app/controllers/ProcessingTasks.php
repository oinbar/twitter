<?php

class ProcessingTasks extends BaseController {

	// application level
	private $twitter_consumer = array(
			'key1' => array(
				'consumer_key' => 'x393VwuVLnnixX6Ld7panxSp8',
				'consumer_secret' => 'qglHdDR9gcwpyhdFSF37hPpMwXSrIchkmp9DV8TZ8iOzLNt95u'
			)
		);

	// FETCH FROM DATABASE
	// private $twitter_tokens = array(
	// 		'key1' => array(
	// 			'oauth_access_token' => '2492151342-mRMDlwJGaij2yZQB5CHyU2FbaymXnIcEhYnhcgC',
	// 			'oauth_access_token_secret' => 'sDCCPbYt39Uii76de2HcSMbcTFffby1BwxjAEheL6b4dk'
	// 		)
	// 	);


	public function searchTwitterFeedCriteria($feed_id) {
		try {
			$redis = Redis::Connection();

            $user_twitter_credentials = DB::connection('mysql')->table('feeds')
                ->join('users_feeds', 'feeds.id', '=', 'users_feeds.feed_id')
                ->join('users', 'users_feeds.user_id', '=', 'users.id')
                ->where('feed_id', '=', $feed_id)
                ->select('twitter_oauth_access_token', 'twitter_oauth_access_token_secret')
                ->first();
            $access_token = get_object_vars($user_twitter_credentials)['twitter_oauth_access_token'];
            $access_token_secret = get_object_vars($user_twitter_credentials)['twitter_oauth_access_token_secret'];

            $consumer_key = $this->twitter_consumer['key1']['consumer_key'];
            $consumer_secret = $this->twitter_consumer['key1']['consumer_secret'];
            $search_criteria = urlencode(DB::connection('mysql')
											->table('feeds')->where('id', $feed_id)
											->first()->params);

            // build the array $transformations_params = array('transformation' => 'param')
            $transformations = DB::connection('mysql')->table('transformations_feeds')->where('feed_id', $feed_id)->lists('transformation');
            $params = DB::connection('mysql')->table('transformations_feeds')->where('feed_id', $feed_id)->lists('params');
            $transformations_params = array();
            foreach($transformations as $key=>$trans) {
                $transformations_params[$trans] = $params[$key];
            }

//            Log::error($transformations_params);
//            Log::error('feedId: '. $feed_id);
//            Log::error($access_token);
//            Log::error($access_token_secret);
//            Log::error($consumer_key);
//            Log::error($consumer_secret);
//            Log::error($search_criteria);

            $twitterSearchInitializer = new TwitterSearchInitializer($transformations_params, $feed_id, $access_token, $access_token_secret, $consumer_key, $consumer_secret, $search_criteria);

            // oauth credentials should go in intializer criteria/params.
            // since_ID should go into the initializer criteria/params. or should it? its redis dependent
            $use_since_id = true;
            $since_id = '';
            if ($use_since_id == true && $redis->exists('since_id-feedID-' . $feed_id)) {
                $since_id = $redis->get('since_id-feedID-' . $feed_id);
            }

            $data = $twitterSearchInitializer->run($since_id);
            $data = json_decode($data, true);

//            Log::error($data);
//            error_log($data, 3, app_path().'/storage/logs/logfile1.log');

            // test to see if in DB, and push to next transformation list
            // MAX_ID IS A PARAMETER RETURNED IN THE TWITTER RESULTS!!
			$max_id = 0;
			foreach ($data['statuses'] as $status){
				if ($status['_id'] > $max_id) {
					$max_id = ($status['_id']);
				}
				// TEST TO SEE IF IN DB - this is also tested for in the insertion phase, but using it here helps reduce the volume on the pipeline
				$db_record = DB::connection('mongodb')->collection('data1')->where('_id', $status['_id'])->first();
				if (!$db_record) {
					$transformation = $twitterSearchInitializer->getNextTransformation();
                    $redis->rpush('pending' . $transformation . 'list', json_encode($status));
				}

			$redis->del('since_id-feedID-' . $feed_id);
			$redis->append('since_id-feedID-' . $feed_id, $max_id);
			}
		}
		catch (Exception $e) {
			Log::error($e);
		} 	
	}

	public function insertJsonToDB ($cache_list_origin = 'pendingpersistencelist', $batch_size = 1000) {
		/*
		This is the final stage in the data processing pipeline.  It pops data from a cache list in batches, 
		and inserts the records into the database, while making sure to deal appropriately with
		any duplicates.
		*/

//		 Log::error('INSERTDB CALLED');


		$redis = Redis::connection();
		$batch_size = min($redis->llen($cache_list_origin), $batch_size);		
		$records = $redis->lrange($cache_list_origin, 0, $batch_size-1);
		$redis->ltrim($cache_list_origin, $batch_size, -1);
		
		foreach($records as $record) {
			try{
				//check if record already exists in db
				$record = json_decode($record, true);
                $db_record = DB::connection('mongodb')->collection('data1')->where('_id', $record['_id'])->first();
				if ($db_record) {
					// IF ALREADY EXISTS IN DB COMBINE REFERENCES TO FEED AND UPDATE RECORD
					$record['feeds'] = array_merge($db_record['feeds'], $record['feeds']);				
					$record['feeds'] = array_unique($record['feeds']);
					unset($record['_id']); // remove _id so it does not try to update mongo id
					DB::connection('mongodb')->collection('data1')->where('_id', $db_record['_id'])->update($record);
				} 
				else {
					$record['datetime'] = date('Y-m-d H:i:s'); 
					DB::connection('mongodb')->collection('data1')->insert($record);
				}
			}
			catch (Exception $e) {
				Log::error($e);
			}
		}
	}


	public function runJsonThroughCalais ($calais_key = '',
										  $cache_list_origin = 'pendingcalaislist',
										  $cache_list_destination = 'pendingsutimelist',
										  $batch_size = 100) {
		/*
		pulls a batch of documents off the cache_list_origin list, runs each doc through the opencalais
		service with a delay in between, and then deposits the reformed document in the cache_list_destination list. 
		*/

		Log::error('CALAIS CALLED');

		include __DIR__.'/../open_calais_dg/opencalais.php';
		$redis = Redis::connection();
		$batch_size = min($redis->llen($cache_list_origin), $batch_size);
		$contents = $redis->lrange($cache_list_origin, 0, $batch_size-1);
		$redis->ltrim($cache_list_origin, $batch_size, -1);

		Log::error('CALAIS INPUT SIZE: ' .sizeof($contents));

		foreach ($contents as $content) {						
			try{
				$record = json_decode($content, true);
				$content = str_replace('#', '',$record['text']); //strip characters that screw up calais
				$content = str_replace('@', '',$record['text']); //strip characters that screw up calais

				$oc = new OpenCalais($calais_key);
				$results = json_decode($oc->getResult($content), true);

				unset($results['doc']);
				unset($results[0]);

				// fix some issue with the keys	
				foreach ($results as $key => $val) { 
					unset($results[$key]);					
					array_push($results, $val);
				}				

				$record['opencalais'] = $results;				
				$redis->rpush($cache_list_destination, json_encode($record));

				usleep(250000);
			}
			catch (Exception $e) {
				Log::error($e);
			}
		}
	}

	public function runJsonThroughSUTime ($cache_list_origin = 'pendingsutimelist',
										  $cache_list_destination = 'pendingpersistencelist',
										  $batch_size = 10) {
		/*
		pulls a batch of documents off the cache_list_origin list, and runs them through StanfordNLP's
		SUTime module (packaged into a jar file), attempting to find any mentions of dates and times in the text.
		currently this is done by using an intermediary json file and calling the jar directly.
		*/		

		Log::error('SUTIME CALLED');

		try{

            ini_set('memory_limit', '1024M');


			$redis = Redis::connection();
			$batch_size = min($redis->llen($cache_list_origin), $batch_size);
			$contents = $redis->lrange($cache_list_origin, 0, $batch_size-1);
			$redis->ltrim($cache_list_origin, $batch_size, -1);
			
			// add the normalized datetime field so that SUTime has a reference point 
			$array = array();

			Log::error('SUTIME INPUT SIZE: ' . sizeof($contents));

			foreach ($contents as $record){
				$record = json_decode($record, true);
				$record['datetime'] = date("Y-m-d H:i:s" , strtotime($record['created_at']));
				array_push($array, $record);
			}
			$array = json_encode($array);

			// save the data to an intermediate file, run SUTime, and save the new data back to the file		
			$input_file = tempnam(__DIR__ . '/temp/', 'jsonForSUTime') . '.json';
			file_put_contents($input_file, $array);
			$path ='';
			if (App::environment() == 'local') {
				$jarpath = '/Users/Orr/Desktop/SUTime.jar';
			}
			elseif (App::environment() == 'production') {
				$jarpath = '/home/upupup/prod/lib/SUTime.jar';
			}

			// THIS CODE SEPARATES STDOUT FROM STDERR.  BUT IT DID NOT WORK AS INTENDED
			// $descriptorspec = array(
			// 	   0 => array("pipe", "r"),  // stdin
			// 	   1 => array("pipe", "w"),  // stdout
			// 	   2 => array("pipe", "w"),  // stderr
			// 	);
			// $process = proc_open('/usr/bin/java -jar ' . $jarpath . ' ' . __DIR__ . '/temp/' . $file, $descriptorspec, $pipes);
			// $stderr = stream_get_contents($pipes[2]);
			// if ($stderr) {
			// 	throw new Exception($stderr);
			// }
			// fclose($pipes[2]);
			// proc_close($process);


			// SUTIME returns stdout with stderr.  Currently this checks to see if there actually was a java excption by checking
			// for the word "Exception"...  Since the error is in an array, it is printed to the log line by line
			exec('/usr/bin/java -jar ' . $jarpath . ' ' . $input_file . ' 2>&1', $err);
//			 if ($err && (strpos(implode(' ', $err),'Exception') !== false)) {
//			 	foreach($err as $line) {
//			 		Log::error($line);
//			 	}
//			 }

			// retrieve data from file, and for each SUTime instance, normalize and check for is_future, then put
			// the record in the cache
			$file = file_get_contents($input_file);
			$file = json_decode($file, true);			

			for ($i = 0; $i < sizeof($file); $i++) {
				if (array_key_exists('SUTime', $file[$i])){
					for ($j = 0; $j < sizeof($file[$i]['SUTime']); $j++) {
						// numerize daytimes (morning, afternoon, evening, night)
						$a = new AdminController();

						$file[$i]['SUTime'][$j]['normalized'] = $a->fixSUTime($file[$i]['SUTime'][$j]['normalized']);

						$interval = $a->dateTimeDiffDays($file[$i]['created_at'], $file[$i]['SUTime'][$j]['normalized']);
						if ($interval > 0) {
							$file[$i]['SUTime'][$j]['future'] = true;
						}
					}
				}
				$redis->rpush($cache_list_destination, json_encode($file[$i]));
			}

			unlink($input_file);
		} 
		catch (Exception $e) {
			Log::error(Pre::render($e));
		}
	}
}
