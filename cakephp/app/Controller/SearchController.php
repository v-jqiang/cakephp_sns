<?php
class SearchController extends AppController {
    public $helpers = array('Html', 'Form');
	
		
	public function suggestions($name){
		$this->set('name', $name);
		if($name=='users'){
			$this->redirect("/search/users");
		}
	}

	public function users(){
		
	}

}