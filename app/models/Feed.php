<?php

use Jenssegers\Mongodb\Model as Eloquent;

class Feed extends Eloquent {

//    protected $collection = 'mycollection';
	protected $table = 'feeds';
	
	public function user() {
		return $this->belongsTo('User');	
	}
}



?>