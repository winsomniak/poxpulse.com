<?php
//Prevents direct file access through url
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Condition extends CI_Controller 
{

		function Condition()
		{
				//Constructing the controller base
				parent::__construct();
				
				//Loading appropriate models and helpers
				$this->load->model('Ability_model');
				$this->load->model('Rune_model');
				$this->load->model('Search_model');
				$this->load->model('Condition_model');
				$this->load->helper('main');
				
		} //Constructor
	
		function index()
		{
				echo 'Under Construction...';
				
		} //function index
		
        
        function name($sName = -1)
        {
        	//Querying for subject data
                $condition = $this->Condition_model->select_by_name($sName);
		
                //If the query failed to return something (someone entered an invalid ID in the url) displays an error.
                if (!$condition)
                {
                        $this->load->view('page_not_found');
                }
                else
                {
				//Setting the page title to the name of the subject data
				$header['title'] = $condition['condition'];
		
				//Loading up the page header	
				$this->load->view('header', $header);
				
				$data['condition'] = $condition;
				$data['condition']['icon'] = 0;
				
				//Loading up the subject view
				$this->load->view('condition', $data);
                }
		
		$this->load->view('foot');
				
        } //Function End
	
		
	function id($id = -1)
	{
			$abilities = $this->Ability_model->select_by_id($id);
			
	//If the query failed to return something (someone entered an invalid ID in the url) displays an error.
	if (!$abilities)
	{
			$this->load->view('header');
			$this->load->view('page_not_found');
			$this->load->view('foot');
	}
	else
	{
			//Setting the page title to the name of the ability
			$header['title'] = $abilities[0]['ability'];
			
			//Confirming that there are ads on the page
			$header['ads'] = true;
			
			//Creating the ability description
			$header['description'] = 'Details of Poxnora\'s ' . ucwords($abilities[0]['ability']) . ' ability any champion possessing it.';
	
			//Loading up the page header	
			$this->load->view('header', $header);
			
			$data['abilities'] = $abilities;
			
			//Searching for champions with the ability
			$champions = $this->Rune_model->search_by_ability($id);
			
			$data['champions'] = $champions;
			
			//Loading up the ability view
			$this->load->view('ability', $data);
	}
	
	$this->load->view('foot');
			
	}
	
} // Class End

?>