<?php

use Jenssegers\Mongodb\Model as Eloquent;

class Feed extends Eloquent {

//    protected $collection = 'mycollection';
	protected $collection = 'data1';
	
	public function user() {
		return $this->belongsTo('User');	
	}

	public function tweets() {
		return $this->belongsToMany('Tweet');
	}
}



?>