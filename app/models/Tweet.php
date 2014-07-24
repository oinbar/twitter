<?php

use Jenssegers\Mongodb\Model as Eloquent;

class Tweet extends Eloquent {

//    protected $collection = 'mycollection';
	protected $collection = 'tweets';
	
	public function user() {
		return $this->belongsTo('User');	
	}
}



?>