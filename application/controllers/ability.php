<?php
//Prevents direct file access through url
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Ability extends CI_Controller 
{

		function Ability()
		{
				//Constructing the controller base
				parent::__construct();
				
				//Loading appropriate models and helpers
				$this->load->model('Ability_model');
				$this->load->model('Rune_model');
				$this->load->model('Search_model');
				$this->load->helper('main');
				
		} //Constructor
	
		function index()
		{
			$header['title'] = 'Ability List';
			$header['css_files'] = array('ability_list.css');
			
			$data['abilities'] = $this->Ability_model->ability_list();
			
			$this->load->view('header', $header);
			$this->load->view('ability_list', $data);
			$this->load->view('foot');
				
		} //function index
		
        
        function name($sName = -1)
        {
        	//Selecting a piece of a equipment and all related info from the database based on its unique ID
                $abilities = $this->Ability_model->select_by_name($sName);
		
                //If the query failed to return something (someone entered an invalid ID in the url) displays an error.
                if (!$abilities)
                {
                        $this->load->view('page_not_found');
                }
                else
                {
				//Setting the page title to the name of the ability
				$header['title'] = $abilities[0]['ability'];
		
				//Loading up the page header	
				$this->load->view('header', $header);
				
				$data['abilities'] = $abilities;
				
				//Loading up the ability view
				$this->load->view('ability', $data);
                }
		
		$this->load->view('foot');
				
        } //Function view
		
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
				
				//Linking css file
				$header['css_files'] = array('ability.css');
				
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
	
} // Class Equipment



/* End of file ability.php */
/* Location: ./system/application/controllers/ability.php */

?>