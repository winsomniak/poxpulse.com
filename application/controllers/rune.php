<?php
//Prevents direct file access through url
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Rune extends CI_Controller 
{

		function Rune()
		{
				//Constructing the controller base
				parent::__construct();
				
				//Loading appropriate models and helpers
				$this->load->model('Rune_model');
				$this->load->model('Ability_model');
				$this->load->model('Search_model');
				$this->load->model('Comment_model');
				$this->load->helper('main');
				
		} //Constructor
	
		function index()
		{
				//Page not found, no direct script access
				$this->load->view('header');
				$this->load->view('page_not_found');
				$this->load->view('foot');
				
		} //function index
		
        
        function id($id = -1)
        {
        	//Selecting a piece of a equipment and all related info from the database based on its unique ID
                $rune = $this->Rune_model->select_by_id($id);
                
		//Setting the page title to the name of the piece of equipment
                $header['title'] = $rune['rune'];
		
		//Setting the page description
		$header['description'] = 'Poxnora\'s ' . ucwords($rune['rune']) . ' is part of the ' . ucwords($rune['expansion']) . ' release set.';
		$header['description'] .= ' ' . ucwords($rune['rune']) . ' is usable by the ';
		
		if(isset($rune['factions'][1]))
		{
				$header['description'] .= ucwords($rune['factions'][0]['faction']) . ' and ' . ucwords($rune['factions'][1]['faction']) . ' factions. ';
		}
		else
		{
				$header['description'] .= ucwords($rune['factions'][0]['faction']) . ' faction. ';
		}
		
		$header['description'] .= 'It\'s ' . ucwords($rune['rarity']) . '.';
		$header['css_files'] = array('rune.css');
		//Confirming that there are ads on the page
		$header['ads'] = true;
		
		//Loading up the page header	
		$this->load->view('header', $header);
                
		$rune['comments'] = $this->Comment_model->select_comments_by_page($this->uri->ruri_string());
                //If the query failed to return something (someone entered an invalid ID in the url) displays an error.
                if (!$rune)
                {
                        $this->load->view('page_not_found');
                }
                else
                {
                        $this->load->view('rune', $rune);
                }
		
		$this->load->view('foot');
				
        } //Function
	
	function random()
	{
		$rune = $this->Rune_model->select_random_rune();
		header( 'Location: /rune/id/' . $rune );
	} //Function
	
} // Class Rune



/* End of file rune.php */
/* Location: ./system/application/controllers/rune.php */

?>