<?php

class Home extends CI_Controller {

	function Home()
	{
		parent::__construct();
		$this->load->model('Ability_model');
		$this->load->model('Rune_model');
		$this->load->model('Search_model');
		$this->load->helper('main');
	}
	
	function index()
	{
		$data['expansions'] = $this->Search_model->search_expansions();
		$this->load->view('home', $data);
	}
}

/* End of file home.php */
/* Location: ./system/application/controllers/home.php */