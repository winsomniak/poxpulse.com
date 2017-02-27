<?php
//Prevents direct file access through url
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Search extends CI_Controller 
{

		function Search()
		{
				//Constructing the controller base
				parent::__construct();
				
				//Loading appropriate models and helpers
				$this->load->model('Rune_model');
                                $this->load->model('Ability_model');
                                $this->load->model('Search_model');
				$this->load->helper('main');
				
		} //Constructor
	
		function index()
		{
				if (isset($_POST['search_term']))
                                {
                                        //preparing the search for url
                                        $term = $_POST['search_term'];
                                        $term = str_replace(' ', '-', $term);
                                        $term = str_replace(',', '', $term);
                                        $term = str_replace('\'', '', $term);
                                        $term = str_replace(':', '', $term);
                                        header( 'Location: /search/results/' . $term ) ;
                                }
				$header;
				$data;
				
				//Setting the title for the page
				$header['title'] = 'Search Results';
				
				//Displaying the page's view files
				$this->load->view('header', $header);
				$this->load->view('page_not_found');
				$this->load->view('foot');
				
				
				
		} //function index
		
                function results($str = -1)
                {

                        //Putting spaces back in for the purpose of searching the database
                        $str = str_replace('-', ' ', $str);
                        
                        //Selecting a rune and all related info from the database based on its unique ID
                        $runes = $this->Rune_model->search_by_name($str);
                        $abilities = $this->Ability_model->search_by_name($str);
                        
                        //Initializing counter variables to 0
                        $iRunes = 0;
                        $iAbilities = 0;
                        $iTotalResults = 0;
                        
                        $iRunes = count($runes);
                        $iAbilities = count($abilities);
                        
                        $iTotalResults = $iRunes + $iAbilities;
                        
                        $header['title'] = 'Search Results';
                        
                        //Loading up the page header	
                        $this->load->view('header', $header);
                        
                        //If the query failed to return something (someone entered an invalid ID in the url) displays an error.
                        if ($iTotalResults < 1)
                        {
                                $data['iRunes'] = $iRunes;
                                $data['iAbilities'] = $iAbilities;
                                $data['iTotalResults'] = $iTotalResults;
                                $this->load->view('search_results', $data);
                        }
                        else if ($iTotalResults === 1) //Single result, rune or ability
                        {
                                if ($iRunes > 0)
                                {
                                        header( 'Location: /rune/id/' . $runes[0]['id'] ) ;
                                }
                                else if ($iAbilities > 0)
                                {
                                        header( 'Location: /ability/id/' . $abilities[0]['id'] ) ;
                                }
                                
                        }
                        else //In this situation, we have more than one result
                        {
                                $data['iRunes'] = $iRunes;
                                $data['iAbilities'] = $iAbilities;
                                $data['iTotalResults'] = $iTotalResults;
                                $data['runes'] = $runes;
				if($iAbilities > 0)
				{
					$data['abilities'] = $abilities;
					$data['abilities'] = $this->Ability_model->select_lowest_ranks($data['abilities']);
				}
                                $this->load->view('search_results', $data);
                        }
                        
                        $this->load->view('foot');
                }
		
	function advanced($str = -1)
    {
		//Redirecting to self with appropriate url if the post variables are set
		if(isset($_POST['submit']))
		{
			//Initializing the url string
			$strUrl = '';
			
			foreach($_POST as $key => $value)
			{	
					//redirects to advanced search page if 'rune' field contains illegal characters
					if ($key == 'rune')
					{
							$value = linkify($value);
							if (preg_match('/[^a-zA-Z\-]/', $value) )
							{
									header( 'Location: /search/advanced');
									end;
							}
							$value = strtolower($value);
							
					}
					
					//Ensuring that the fields were not blank before adding them to the url
					if($value != '' && $key != 'submit')
					{
							$strUrl .= $key . '/' . $value . '/';
					}
					
			}	
			//Cutting off the last joining character
			//$strUrl = substr($strUrl, 0, -1);
                        redirect(prep_url(site_url('search/advanced') . '/' . $strUrl));
						
        }
		else
		{
			$header['title'] = 'Advanced Search';
			
			//Assigning style sheets
			$css_files = array('advanced_search.css', 'table.css');
			$js_files = array('combobox.js', 'advanced_search.js');
			$header['css_files'] = $css_files;
			$header['js_files'] = $js_files;
			$header['ads'] = true;
			
			//Loading up the page header	
			$this->load->view('header', $header);
			
			$data['factions'] = $this->Search_model->search_factions();
			$data['expansions'] = $this->Search_model->search_expansions();
			$data['races'] = $this->Search_model->search_races();
			$data['classes'] = $this->Search_model->search_classes();
			$data['types'] = $this->Search_model->search_types();
			$data['rarities'] = $this->Search_model->search_rarities();
			
			if ($str != -1)
			{
				//Creating an array to store all fields currently selected
				$fields = array();
				//$fields = parse_search_url($str);
				$fields = $this->uri->ruri_to_assoc(3);
				$data['fields'] = $fields;

				//Putting spaces back in for the purpose of searching the database
				$str = str_replace('-', ' ', $str);
				
				//Searching the database with the selected filters
				$runes = $this->Search_model->advanced_search($str);
				
				//Initializing counter variables to 0
				$iRunes = 0;
				
				$iRunes = count($runes);
				
				//If the query failed to return something (someone entered an invalid ID in the url) displays an error.
				$data['iRunes'] = $iRunes;
				$data['runes'] = $runes;
						
				$this->load->view('advanced_search', $data);
			}
			else
			{
				$this->load->view('advanced_search', $data);
			}
			
			$this->load->view('foot');
		}
    }
                
                function faction($str = -1)
                {
                        //Default page
                        if ($str === -1)
                        {
                                $data['factions'] = $this->Search_model->search_factions();
                                
                                $header['title'] = 'Factions';
                                
                                $this->load->view('header', $header);
                                $this->load->view('factions', $data);
                                $this->load->view('foot');
                                
                                return;
                        }
                        //Preping search string for database
                        $str = str_replace('-', ' ', $str);
                        $str = str_replace('\'', '', $str);
                        
                        $runes = $this->Search_model->search_runes_by_faction($str);
                        
                        if (count($runes) < 1)
                        {
                                $this->load->view('header');
                                $this->load->view('search_results');
                                $this->load->view('foot');
                        }
                        
                        $data['runes'] = $runes;
                        
                        $faction = str_replace('-', ' ', $str);
			$faction_data = $this->Search_model->search_faction_by_name($faction);
                        $data['faction_data'] = $faction_data;
			
                        $header['title'] = $data['faction_data']['faction'];
                        $header['css_files'] = array('faction_page.css');
                        
                        $this->load->view('header', $header);
                        $this->load->view('faction_runes', $data);
                        $this->load->view('foot');
                }
		
		function expansion($str = -1)
                {
                        //Default page
                        if ($str === -1)
                        {
                                $data['expansions'] = $this->Search_model->search_expansions();
                                
                                $header['title'] = 'Expansions';
                                
                                $this->load->view('header', $header);
                                $this->load->view('expansions', $data);
                                $this->load->view('foot');
                                
                                return;
                        }
                        //Preping search string for database
                        $str = str_replace('-', ' ', $str);
                        $str = str_replace('\'', '', $str);
                        
                        $runes = $this->Search_model->search_runes_by_expansion($str);
                        
                        if (count($runes) < 1)
                        {
                                $this->load->view('header');
                                $this->load->view('search_results');
                                $this->load->view('foot');
                        }
                        
                        $data['runes'] = $runes;
                        
                        $data['expansion'] = str_replace('-', ' ', $str);
                        
                        $header['title'] = $data['expansion'];
                        $header['css_files'] = array('expansion_page.css');
                        
                        $this->load->view('header', $header);
                        $this->load->view('expansion_runes', $data);
                        $this->load->view('foot');
                }
                
                function rune($str = -1)
                {
                        //Selecting a piece of a equipment and all related info from the database based on its unique ID
                        $results = $this->Rune_model->search_by_name($str);
                        
                        //Setting the page title to the name of the piece of equipment
                        $header['title'] = 'Search Results';
                        
                        //Loading up the page header	
                        $this->load->view('header', $header);
                        
                        //If the query failed to return something (someone entered an invalid ID in the url) displays an error.
                        if (!$results)
                        {
                                $this->load->view('search_results');
                        }
                        else if (count($results) === 1)
                        {
                                header( 'Location: /rune/id/' . $results[0]['id'] ) ;
                        }
                        else
                        {
                                $data['results'] = $results;
                                $this->load->view('search_results', $data);
                        }
                        
                        $this->load->view('foot');
                                        
                } //Function rune
	
} // Class Search



/* End of file search.php */
/* Location: ./system/application/controllers/search.php */

?>