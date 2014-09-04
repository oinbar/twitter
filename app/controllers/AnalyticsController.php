<?php

class AnalyticsController extends BaseController {

	public function emergingTrends ($feed_id) {

		// TWEET TEXT
		$query = 
		'db.data1.aggregate([
		    { $match : { feeds : { $in : [ "' . $feed_id . '" ] },
	                     datetime : { $gte : "2014-08-01" } } },
		    { $project : { _id : 1,
	                      datetime : 1,
	                      text : 1 } },
		]).toArray()';	

		// TWEET HASHTAGS
		$query = 
		'db.data1.aggregate([
		    { $match : { feeds : { $in : [ "' . $feed_id . '" ] },
                     datetime : { $gte : "2014-08-01" } } },
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
			
			exec(base_path() . '/../python_venv/bin/python ' . __DIR__ .  '/python_scripts/emerging_trends.py ' . $temp_file_in . ' ' . $temp_file_out . ' 2>&1', $err);
			if ($err){
				throw new Exception(Pre::render($err));
			}			
			
			// echo $temp_file_out;
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
