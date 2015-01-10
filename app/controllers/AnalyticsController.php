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

	public function getTrendsData ($feed_id, $timepoints = 24, $num_features = 5, $timeframe = 'hour', $make_lower_case='False') {
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

			$temp_file_in = tempnam(__DIR__ . '/temp/', 'emergingTrendsIn_feedId_'. $feed_id . '_timepts_' . $timepoints . '_features_' . $num_features . '_timeframe_' . $timeframe);
            $temp_file_out = tempnam(__DIR__ . '/temp/', 'emergingTrendsOut_feed_id_'. $feed_id . '_timepts_' . $timepoints . '_features_' . $num_features . '_timeframe_' . $timeframe);
			file_put_contents($temp_file_in, json_encode($results['retval']));			

			exec(base_path() . '/../python_venv/bin/python ' . __DIR__ .  '/python_scripts/emerging_trends.py ' . $temp_file_in . ' ' . $temp_file_out . ' ' . $num_features . ' ' . $timeframe . ' ' . $make_lower_case . ' 2>&1', $err);


            Log::error(base_path() . '/../python_venv/bin/python ' . __DIR__ .  '/python_scripts/emerging_trends.py ' . $temp_file_in . ' ' . $temp_file_out . ' ' . $num_features . ' ' . $timeframe . ' ' . $make_lower_case . ' 2>&1', $err);



			if ($err && (strpos(implode(' ', $err),'Exception') !== false)) {
				foreach($err as $line) {
					Log::error($line);
				}
				throw new Exception(Pre::render($err));
			}

			// $im = imagecreatefrompng($temp_file_out);
			// header('Content-Type: image/png');
			// imagepng($im, $temp_file_out);	
				
//			$temp_file_out_name = basename($temp_file_out);

            $json_results = json_decode(file_get_contents($temp_file_out), true);

//            echo Pre::render($json_results);

            return $json_results;
			

			 unlink($temp_file_in);
			 unlink($temp_file_out);
		}		
		catch (Exception $e){
			Log::error($e);
			echo $e;
		}
	}

	public function showAnalytics() {
		// create image and get the name
		$protest_hour_trend = $this->trends('1', '24', '5', 'hour');	
		$protest_day_trend = $this->trends('1', '2', '5', 'day');

		return View::make('analytics')
			->with('protest_hour_trend', $protest_hour_trend)
			->with('protest_day_trend', $protest_day_trend);
	}

    public function getAlertsData ($feed_id) {


        // CURRENTLY THE DATA IS ONLY DISPLAYED ON A TIMELINE

        date_default_timezone_set("EST");
        $current_date_time = date('Y-m-d', time());
        $query =
            'db.data1.aggregate([
	    { $match : { feeds : { $in : [ "' . $feed_id . '" ] },
                   "opencalais._type" : { $in : [ "City", "Facility" ] },
                   "SUTime.future" : { $exists : true },
                   "SUTime.normalized" : { $gt : "' . $current_date_time . '" },
                   text : { $regex : /^((?!(yesterday)|(ago)).)*$/ } } },
	    { $unwind : "$opencalais" },
        { $unwind : "$SUTime" },
	    { $match : { "opencalais._type" : { $in : [ "City", "Facility" ] },
                   "SUTime.future" : {$exists : true} } },
	    { $project : { text : 1,
                     opencalais : 1,
                     SUTime : 1,
                     retweet_count : 1,
                     id : 1 } },
	    { $group: { _id : "$text" ,
                id : { $addToSet : "$id" },
                future_time_norm : { $addToSet : "$SUTime.normalized" },
                future_time_original : { $addToSet : "$SUTime.original" },
                location : { $addToSet : "$opencalais.name" },
                location_type : { $addToSet : "$opencalais._type" },
    	        retweet_count : { $max : "$retweet_count" } } },
		]).toArray()';

        try {
            $db = DB::connection('mongodb')->getMongoDB();
            $results = $db->execute('return ' . $query . ';');
            $temp_file_in = tempnam(__DIR__ . '/temp/', 'alertsAggIn');
            $temp_file_out = tempnam(__DIR__ . '/temp/', 'alertsAggOut');
            file_put_contents($temp_file_in, json_encode($results['retval']));

            exec(base_path() . '/../python_venv/bin/python ' . __DIR__ .  '/python_scripts/twitter_alerts_aggregator.py ' . $temp_file_in . ' ' . $temp_file_out . ' 2>&1', $err);
            if ($err){
                throw new Exception(Pre::render($err));
            }

            // add full datetime format for timeline display
            $timeline_data = json_decode(file_get_contents($temp_file_out), true);
            for ($i = 0; $i < sizeof($timeline_data); $i++) {
                $timeline_data[$i]['full_datetime'] = date(DATE_RFC2822, strtotime($timeline_data[$i]['future_time_norm']));
            }


            unlink($temp_file_in);
            unlink($temp_file_out);


            return json_encode($timeline_data);


        }
        catch (Exception $e){
            Log::error('ALERTS AGGREGATOR :  '. $e);
            echo $e;
        }
    }

}
