<?php

class Appreciation extends CI_Controller {

	function Appreciation()
	{
		parent::__construct();
		$this->load->model('Ability_model');
		$this->load->model('Rune_model');
		$this->load->model('Search_model');
		$this->load->helper('main');
	}
	
	function index()
	{
		$header['title'] = 'Buy Insomniac1 a Draft Game!';
		$this->load->view('header', $header);
		$this->load->view('ticket_donation_page');
		$this->load->view('foot');
	}
}