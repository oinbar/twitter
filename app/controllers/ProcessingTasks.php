<?php

class ProcessingTasks extends BaseController {

	public function searchTwitterFeedCriteria($feed_id, $cache_list_destination = 'PendingCalaisList') {	
		/*
		This is the first stage in the data processing pipeline.  Finds the criteria based on the feed_id, and searches twitter api.
		
		Note that each call retrieves up to 100 results,
		so there is no need for any time delay here, since all results are returned at once.

		NOTE: must still implement since_id, to make sure tweets are not fetched redundantly.  
		*/	
		try {
			include __DIR__.'/../twitter-api-php/TwitterAPIExchange.php';
			$redis = Redis::Connection();
			$settings = array(
			    'oauth_access_token' => "2492151342-mRMDlwJGaij2yZQB5CHyU2FbaymXnIcEhYnhcgC",
			    'oauth_access_token_secret' => "sDCCPbYt39Uii76de2HcSMbcTFffby1BwxjAEheL6b4dk",
			    'consumer_key' => "x393VwuVLnnixX6Ld7panxSp8",
			    'consumer_secret' => "qglHdDR9gcwpyhdFSF37hPpMwXSrIchkmp9DV8TZ8iOzLNt95u"
			);				

			// GET THE SEARCH CRITERIA FROM THE DB TO ADD INTO THE QUERY
			$use_since_id = false;

			$since_id = '';			
			if ($use_since_id && $redis->exists('since_id-feedID-' . $feed_id)) {
				$since_id = $redis->get('since_id-feedID-' . $feed_id);
				$since_id = '&since_id=' . $since_id;
			}			

			$getfield = '?count=100' . $since_id . '&q=' . urlencode(DB::connection('mysql')
											->table('feeds')->where('id', $feed_id)->orderBy('created_at', 'desc')
											->first()->criteria);

			$url = 'https://api.twitter.com/1.1/search/tweets.json';
			$requestMethod = 'GET';
			$twitter = new TwitterAPIExchange($settings);
			$json = $twitter->setGetfield($getfield)
			             ->buildOauth($url, $requestMethod)
			             ->performRequest();	             

			$data = json_decode($json, true);
			$redis = Redis::connection();

			// LOOP OVER THE RESULTS COLLECTION, AND STORE IN CACHE FOR DATA PIPELINE
			$max_id = 0;
			foreach ($data['statuses'] as $status){					
				$status['_id'] = $status['id']; //add mongoID
				$status['feeds'] = array($feed_id); //add reference to feed	
				if ($status['_id'] > $max_id) {
					$max_id = ($status['_id']);
				}
				// TEST TO SEE IF IN DB - this is also tested for in the insertion phase, but using it here helps reduce the volume on the pipeline
				$db_record = DB::connection('mongodb')->collection('data1')->where('_id', $status['_id'])->first();
				if (!$db_record) {
					$redis->rpush($cache_list_destination, json_encode($status));		
				}

			$redis->del('since_id-feedID-' . $feed_id);
			$redis->append('since_id-feedID-' . $feed_id, $max_id);	
			}
		}
		catch (Exception $e) {
			Log::error($e);
		} 	
	}

	public function insertJsonToDB ($cache_list_origin = 'PendingPersistenceList', $batch_size = 1000) {
		/*
		This is the final stage in the data processing pipeline.  It pops data from a cache list in batches, 
		and inserts the records into the database, while making sure to deal appropriately with
		any duplicates.
		*/

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
			catch (Exceotion $e) {
				Log::error($e);
			}
		}
	}


	public function runJsonThroughCalais ($calais_key = '',
										  $cache_list_origin = 'PendingCalaisList', 
										  $cache_list_destination = 'PendingSUTimeList', 
										  $batch_size = 100) {
		/*
		pulls a batch of documents off the cache_list_origin list, runs each doc through the opencalais
		service with a delay in between, and then deposits the reformed document in the cache_list_destination list. 
		*/
		include __DIR__.'/../open_calais_dg/opencalais.php';
		$redis = Redis::connection();
		$batch_size = min($redis->llen($cache_list_origin), $batch_size);
		$contents = $redis->lrange($cache_list_origin, 0, $batch_size-1);
		$redis->ltrim($cache_list_origin, $batch_size, -1);

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

	public function runJsonThroughSUTime ($cache_list_origin = 'PendingSUTimeList', 
										  $cache_list_destination = 'PendingPersistenceList', 
										  $batch_size = 50) {
		/*
		pulls a batch of documents off the cache_list_origin list, and runs them through StanfordNLP's
		SUTime module (packaged into a jar file), attempting to find any mentions of dates and times in the text.
		currently this is done by using an intermediary json file and calling the jar directly.
		*/		

		try{
			$redis = Redis::connection();
			$batch_size = min($redis->llen($cache_list_origin), $batch_size);
			$contents = $redis->lrange($cache_list_origin, 0, $batch_size-1);
			$redis->ltrim($cache_list_origin, $batch_size, -1);
			
			// add the normalized datetime field so that SUTime has a reference point 
			$array = array();
			foreach ($contents as $record){
				$record = json_decode($record, true);
				$record['datetime'] = date("Y-m-d H:i:s" , strtotime($record['created_at']));
				array_push($array, $record);
			}
			$array = json_encode($array);

			// save the data to an intermediate file, run SUTime, and save the new data back to the file
			$filename = 'jsonForSUTime' . iterator_count(new DirectoryIterator(__DIR__ . '/temp/')) . '.json';
			file_put_contents(__DIR__ . '/temp/' . $filename, $array);
			$path ='';
			if (App::environment() == 'local') {
				$jarpath = '/Users/Orr/Desktop/SUTime.jar';
			}
			elseif (App::environment() == 'production') {
				$jarpath = '/home/upupup/prod/lib/SUTime.jar';
			}

			
			$result=exec('/usr/bin/java -jar ' . $jarpath . ' ' . __DIR__ . '/temp/' . $filename . ' 2>&1', $err);			
			if ($err){
				throw new Exception((string)$err);
			}

			// retrieve data from file, and for each SUTime instance, normalize and check for is_future, then put
			// the record in the cache
			$file = file_get_contents(__DIR__ . '/temp/' . $filename);
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
			unlink(__DIR__ . '/temp/' . $filename);			
		} 
		catch (Exception $e) {
			Log::error($e);
		}
	}
}
