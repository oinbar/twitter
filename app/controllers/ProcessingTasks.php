<?php

class ProcessingTasks extends BaseController {

	public function send_search_query($feed_id, $cache_list_destination = 'pending_calais') {	
		/*
		This is the first stage in the data processing pipeline.  It is responsible for fetching tweets from twitter,
		and then depositing them in a cache list to await further processing. Note that each call retrieves up to 100 results,
		so there is no need for any time delay here, since all results are returned at once.

		NOTE: must still implement since_id, to make sure tweets are fetched redundantly.  
		*/	
		try {
			include __DIR__.'/../twitter-api-php/TwitterAPIExchange.php';
			$settings = array(
			    'oauth_access_token' => "2492151342-mRMDlwJGaij2yZQB5CHyU2FbaymXnIcEhYnhcgC",
			    'oauth_access_token_secret' => "sDCCPbYt39Uii76de2HcSMbcTFffby1BwxjAEheL6b4dk",
			    'consumer_key' => "x393VwuVLnnixX6Ld7panxSp8",
			    'consumer_secret' => "qglHdDR9gcwpyhdFSF37hPpMwXSrIchkmp9DV8TZ8iOzLNt95u"
			);
			// GET THE SEARCH CRITERIA FROM THE DB TO ADD INTO THE QUERY

			$getfield = '?count=100&q=' . urlencode(DB::connection('mysql')
											->table('feeds')->where('id', $feed_id)->orderBy('created_at', 'desc')
											->first()->criteria);

			$url = 'https://api.twitter.com/1.1/search/tweets.json';
			$requestMethod = 'GET';
			$twitter = new TwitterAPIExchange($settings);
			$json = $twitter->setGetfield($getfield)
			             ->buildOauth($url, $requestMethod)
			             ->performRequest();	             

			// LOOP OVER THE RESULTS COLLECTION, AND STORE IN CACHE FOR DATA PIPELINE
			$data = json_decode($json, true);
			$redis = Redis::connection();

			foreach ($data['statuses'] as $status){				
				$status['_id'] = $status['id']; //add mongoID
				$status['feeds'] = array($feed_id); //add reference to feed						
				$redis->rpush($cache_list_destination, json_encode($status));		
			}
		}
		catch (Exception $e) {
			Log::error($e);
		} 	
	}

	public function cache_to_db ($cache_list_origin = 'pending_persistence', $batch_size = 1000) {
		/*
		This is the final stage in the data processing pipeline.  It fetches data from a cache list in batches, 
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
					DB::connection('mongodb')->collection('data1')->insert($record);
				}
			}
			catch (Exceotion $e) {
				Log::error($e);
			}
		}
	}

	public function send_tweet_to_calais ($cache_list_origin = 'pending_calais', 
										  $cache_list_destination = 'pending_persistence', 
										  $batch_size = 10) {
		/*
		passes tweets down the data processing pipeline by fetching it from one cache list, passing it
		through the open calais service, and depositing it in the next cache list.
		does so in batches, and uses the appropraite time interval in between api calls (0.25s)
		*/
		include __DIR__.'/../open_calais_dg/opencalais.php';
		$redis = Redis::connection();
		$batch_size = min($redis->llen($cache_list_origin), $batch_size);
		$contents = $redis->lrange($cache_list_origin, 0, $batch_size-1);
		$redis->ltrim($cache_list_origin, $batch_size, -1);
		$calais_key = 'qupquc5c4qzj7sg9knu5ad4w';

		foreach ($contents as $content) {						
			try{
				$record = json_decode($content, true);
				$content = 'This text is in English. ' . $record['text']; //using a prefix to cirvumvent a languge detection bug

				$oc = new OpenCalais($calais_key);
				$results = json_decode($oc->getResult($content), true);
				
				// fix some issue with the keys
				$i = 0;
				foreach ($results as $key => $val) { 
					$results[$i] = $val;
					unset($results[$key]);
					$i++;
				}

				unset($results['doc']);
				unset($results[0]);				
				$record['opencalais'] = $results;				
				$redis->rpush($cache_list_destination, json_encode($record));

				echo Pre::render($record);

				usleep(250000);
			}
			catch (Exception $e) {
				Log::error($e);
			}
		}		
	}
}
