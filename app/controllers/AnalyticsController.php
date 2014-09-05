<?php

class AnalyticsController extends BaseController {

	public function trends ($feed_id, $timepoints = 48, $num_features = 5, $timeframe = 'hour') {		
		date_default_timezone_set("EST");
		if ($timeframe == 'hour') {
			$since_time =  date('Y-m-d H:i:s', time()-$timepoints*60*60);		
		} 
		elseif ($timeframe == 'day') {
			$since_time =  date('Y-m-d H:i:s', time()-$timepoints*60*60*24);		
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
			$db = DB::connection('mongodb')->getMongoDB();								
			$results = $db->execute('return ' . $query . ';');
			$temp_file_in = tempnam(__DIR__ . '/temp/', 'emergingTrendsIn');
			$temp_file_out = tempnam(__DIR__ . '/temp/', 'emergingTrendsOut') . '.png';
			file_put_contents($temp_file_in, json_encode($results['retval']));
			
			exec(base_path() . '/../python_venv/bin/python ' . __DIR__ .  '/python_scripts/emerging_trends.py ' . $temp_file_in . ' ' . $temp_file_out . ' ' . $num_features . ' ' . $timeframe . ' 2>&1', $err);
			if ($err){
				throw new Exception(Pre::render($err));
			}			
						
			$im = imagecreatefrompng($temp_file_out);
			header('Content-Type: image/png');
			imagepng($im);

			unset($temp_file_in);
			unset($temp_file_out);			
		}		
		catch (Exception $e){
			Log::error('ALERTS AGGREGATOR :  '. $e);
			echo $e;
		}
	}
}
