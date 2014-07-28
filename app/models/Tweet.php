<?php

use Jenssegers\Mongodb\Model as Eloquent;

class Tweet extends Eloquent {

//    protected $collection = 'mycollection';
	protected $collection = 'data1';

	public function feeds() {
		return this->belongsToMany('Feed');
	}
}



?>