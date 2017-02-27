<?php
        //takes a string and turns it into a Pox Pulse friendly string url ending
        //Example: k'thir forest becomes kthir-forest
        function linkify($str)
        {
                $str = str_replace(' ', '-', $str);
                $str = str_replace('\'', '', $str);
                $str = str_replace(',', '', $str);
                
                return $str;
        }
	
	function getUserIP() 
	{
		if(isset($_SERVER['HTTP_CLIENT_IP']))  // check for shared servers
		{
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']))  // check for proxy servers
		{
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} 
		else 
		{
			$ip = $_SERVER['REMOTE_ADDR']; // general method
		}
		list($ip) = explode(',',$ip);
		return $ip;
	}

	//Compares the array structure of two arrays. If the same, returns true. If not the same, returns false.
	function array_structure_matches($arrSubject, $arrModel)
	{
		//First ensuring that we are comparing two arrays
		if(!is_array($arrSubject))
		{
			return false;
		}
		
		//Ensuring that both arrays have the same number of keys
		if( count(array_keys($arrSubject)) != count(array_keys($arrModel)) )
		{
			return false;
		}
		
		//Checking that each key of the model array is present in the subject
		foreach($arrModel as $key => $value)
		{
			if(!array_key_exists($key, $arrSubject))
			{
				return false;
			}
		}
		
		return true;
	}
	
	function parse_search_url_old($str)
	{
		//Setting the number of underscores, to determine how many search parameters there are
		$scoreCount = substr_count($str, '_');
		
		//finding the position of the first colon
		$colonPos = strpos($str, ':');
		
		//Creating an array to store search parameters
		$parameters = array();
		
		//Setting the remaining str value variable
		$strRemaining = $str;
		
		if ($scoreCount < 1)
		{
			$field = substr($str, 0, $colonPos);
			$value = substr($str, $colonPos + 1, strlen($str));
			
			$parameters[$field] = $value;
		}
		else
		{
			for ($i = 0; $i <= $scoreCount; $i++)
			{
				//finding the position of the underscore
				$scorePos = strpos($strRemaining, '_');
			
				if ($scorePos == '')
				{
					$scorePos = strlen($strRemaining);
				}
			
				$field = substr($strRemaining, 0, $colonPos);
				$value = substr($strRemaining, $colonPos + 1, $scorePos - $colonPos - 1);
				
				$parameters[$field] = $value;
				
				$strRemaining = substr($strRemaining, $scorePos + 1);
				
				//finding the next colon position
				$colonPos = strpos($strRemaining, ':');
			}
		}
		return $parameters;
	}

?>