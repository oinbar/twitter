<?php

class UserController extends BaseController {

	public function getSignup (){

        return View::make('signup');
    }

    public function postSignup () {

    	$rules = array('username' => 'unique:users,username',
    				   'email' => 'unique:users,email');
    	$validator = Validator::make(Input::all(), $rules);

    	if ($validator->fails()) {
    		return Redirect::to('/signup')
    			->withInput()
    			->withErrors($validator);

    	} else {
	    	$user = new User;
	    	$user->username = Input::get('username');
	    	$user->password = Hash::make(Input::get('password'));
	    	$user->email = Input::get('email');

	    	try {
	    		$user->save();    		
	    	} catch (Exception $e) {
	    		return Redirect::to('/signup')->with('flash_message', 'Sign up failed; please try again.')->withInput();
	    	}	
	    	Auth::login($user);
	    	return Redirect::to('/feeds');
    	}
    }	

    public function getLogin () {
    	return View::make('login');
    }

    public function postLogin () {

        $rules = array('username' => 'required',
                       'password' => 'required');
        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return Redirect::to('/login')
                ->withInput()
                ->withErrors($validator);
        
        } else {
            $credentials = Input::only('username', 'password');

            if (Auth::attempt($credentials)) {
                return Redirect::intended('/')->with('flash_message', 'login successful!');
            }
            else {
                return Redirect::to('/login')->with('flash_message', 'Log in failed');
            }
        }    	
    }

    public function getLogout () {
    	Auth::logout();

    	return Redirect::to('/');
    }		
}

