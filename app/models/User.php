<?php
use Jenssegers\Mongodb\Model as Eloquent;


use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $collection = 'data1';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token');

	public function getAuthIdentifier() {
       return $this->getKey();
     }
    
    public function getAuthPassword() {
       return $this->password;
     }
	//relationship method
	public function feeds() {
		return this->hasMany('Feed')
	}

}
