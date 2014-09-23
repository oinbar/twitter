<?php

class AnalyticsController extends BaseController {

	private function maxtime ($feed_id) {
		$query = 
		'db.data1.aggregate([
		{ $match : { feeds : { $in : [ "' . $feed_id . '" ] } } },
		{ $sort : { datetime : -1 } },
		{ $limit : 1 },
		{ $project : { _id : 0 , datetime : 1 } }
		]).toArray()';

		$db = DB::connection('mongodb')->getMongoDB();								
		$results = $db->execute('return ' . $query . ';');
		return $results['retval'][0]['datetime'];
	}

	public function trends ($feed_id, $timepoints = 24, $num_features = 5, $timeframe = 'hour') {		
		$maxtime = $this->maxtime($feed_id);

		date_default_timezone_set("EST");
		if ($timeframe == 'hour') {
			$since_time =  date('Y-m-d H:i:s', strtotime($maxtime)-$timepoints*60*60);		
		} 
		elseif ($timeframe == 'day') {
			$since_time =  date('Y-m-d H:i:s', strtotime($maxtime)-$timepoints*60*60*24);		
		}

		// TWEET TEXT
		$query = 
		'db.data1.aggregate([
		    { $match : { feeds : { $in : [ "' . $feed_id . '" ] },
	                     datetime : { $gte : "' . $since_time . '" } } },
		    { $project : { _id : 1,
	                      datetime : 1,
	                      text : 1 } },
		]).toArray()';	

		// TWEET HASHTAGS
		$query = 
		'db.data1.aggregate([
		    { $match : { feeds : { $in : [ "' . $feed_id . '" ] },
                     datetime : { $gte : "' . $since_time . '" } } },
            { $unwind : "$entities.hashtags" }, 
		    { $group : { _id : "$_id",
                     datetime : { $first : "$datetime" },
                     text : { $push : "$entities.hashtags.text" } } },
		]).toArray()';

		try {			

			ini_set('memory_limit','256M');

			
			$db = DB::connection('mongodb')->getMongoDB();								
			$results = $db->execute('return ' . $query . ';');
			$temp_file_in = tempnam(__DIR__ . '/temp/', 'emergingTrendsIn');
			$temp_file_out = tempnam(Config::get('assets.images'), 'emergingTrendsOut') . '.png';			
			file_put_contents($temp_file_in, json_encode($results['retval']));
			

			exec(base_path() . '/../python_venv/bin/python ' . __DIR__ .  '/python_scripts/emerging_trends.py ' . $temp_file_in . ' ' . $temp_file_out . ' ' . $num_features . ' ' . $timeframe . ' 2>&1', $err);
			if ($err){
				throw new Exception(Pre::render($err));
			}			
						
			// $im = imagecreatefrompng($temp_file_out);
			// header('Content-Type: image/png');
			// imagepng($im, $temp_file_out);	
				
			
			$temp_file_out_name = basename($temp_file_out);

			return Redirect::to('/images/' . $temp_file_out_name);
			

			// unset($temp_file_in);
			// unset($temp_file_out);			
		}		
		catch (Exception $e){
			Log::error('ALERTS AGGREGATOR :  '. $e);
			echo $e;
		}
	}

	public function showAnalytics() {
		$protest_hour_trend = $this->trends('1', '24', '5', 'hour');	
		$protest_day_trend = $this->trends('1', '2', '5', 'day');

		return View::make('analytics')
			->with('protest_hour_trend', $protest_hour_trend)
			->with('protest_day_trend', $protest_day_trend);
	}
}
