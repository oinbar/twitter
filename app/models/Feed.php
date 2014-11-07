<?php

class Feed extends Eloquent {

//    protected $collection = 'mycollection';
	protected $table = 'feeds';
	
	public function user() {
		return $this->belongsTo('User');	
	}
}

?>