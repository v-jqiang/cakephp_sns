<?php
class OauthController extends AppController {
    public $helpers = array('Html', 'Form');
	
		
	public function index(){

	}
	
	public function login($site){
		//echo($site);
		$this->set('site', $site);
	}
	
	public function callback() {
       
    }
}