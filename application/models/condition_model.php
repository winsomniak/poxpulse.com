<?php

class Condition_model extends CI_Model
{      
	function Condition_model()
	{
		// Call the Model constructor
		parent::__construct();
	}
	
	function insert_condition($inputData)
	{
		$inputData['description'] = mysql_real_escape_string($inputData['description']);
		$inputData['condition'] = mysql_real_escape_string($inputData['condition']);
		$query =
		"
			INSERT INTO conditions
			(
				condition,
				description
			)
			VALUES 
			(
				'{$data['condition']}',
				'{$data['description']}'
			)
		";
		
		$results = mysql_query($query) or die(mysql_error());
	}
	
	function select_conditions()
	{
		$query =
		"
			SELECT *
			FROM conditions
		";
		
		$results = mysql_query($query) or die(mysql_error());
		
		$returnData = array();
		
		while($row = mysql_fetch_array($results))
		{
			array_push($returnData, $row['comment']);
		}
		
		return $returnData;
	}
	
	function select_by_name($name)
	{
		$query =
		"
			SELECT *
			FROM conditions
			WHERE conditions.condition = '$name'
		";
		$results = mysql_query($query) or die(mysql_error());
		
		$returnData = mysql_fetch_array($results);
		
		
		return $returnData;
	}
			
}


?>