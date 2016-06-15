<?php

class Arena {

	var $apiBase = '/api.svc/';
	var $domainWithProtocol = '';
	var $sessionID = '';
	
	var $returnJson = TRUE;
	
	public function __construct($opts = [])
	{
		foreach ((array)$opts AS $key => $value)
		{
			if (in_array($key, ['domainWithProtocol', 'apiKey', 'apiSecret', 'username', 'password',]))
			{
				$this->$key = $value;
			}
		}
	}
	
	public function setDomainWithProtocol($domain)
	{
		$this->domainWithProtocol = $domain;
	}

	public function login($opts = [])
	{
		$oldReturnJson = $this->returnJson;
		$this->returnJson = FALSE;
		
		$opts['api_key'] = $this->apiKey;
		$opts['username'] = $opts['username'] ? $opts['username'] : $this->username;
		$opts['password'] = $opts['password'] ? $opts['password'] : $this->password;
	
		$ret = $this->doRequest([
		   'path' => 'Login',
		   'method' => 'POST',
		   'params' => $opts,
		   'signRequest' => FALSE,
		]);
		
		$this->returnJson = $oldReturnJson;
	
		if ($ret->SessionID)
		{
			$this->sessionID = (string)$ret->SessionID;
		}
		else
		{
			throw new Exception('Login Failed');
		}
		
		return $ret;
	}
	
	public function listGroups($opts = [])
	{
		$ret = $this->doRequest([
		   'path' => 'group/list?categoryid='.$opts['categoryid'],
		   'method' => 'GET',
		   'signRequest' => TRUE,
		]);
		
		return $ret;
	}
	
	public function listPersons($opts = [])
	{
		$ret = $this->doRequest([
		   'path' => 'person/list?'.http_build_query($opts),
		   'method' => 'GET',
		   'signRequest' => TRUE,
		]);
		
		return $ret;
	}
	
	public function addPerson($opts = [])
	{	
		$params = [
		   'CampusID' => 1,
		   'FirstName' => $opts['firstName'],
		   'LastName' => $opts['lastName'],
	   ];
	   
	   if ($opts['phoneCell'])
	   {
		   $params['Phones'][] = [
			   'PhoneTypeID' => 282,
			   'Number' => $opts['phoneCell'],
		   ];
	   }
	   
	   if ($opts['email'])
	   {
		   $params['Emails'][] = [
			   'Address' => $opts['email'],
		   ];
	   }
				
		$ret = $this->doRequest([
		   'path' => 'person/add',
		   'method' => 'POST',
		   'params' => $params,
		   'json' => TRUE,
		   'signRequest' => TRUE,
		]);
		
		return $ret;
	}
	
	public function addGroupMember($opts = [])
	{
		$ret = $this->doRequest([
		   'path' => 'group/'.$opts['groupId'].'/member/'.$opts['personId'].'/add',
		   'method' => 'POST',
		   'params' => [
		      'RoleID' => 24,
		      'UniformNumber' => -1,
		      'MemberNotes' => '',
		   ],
		   'json' => TRUE,
		   'signRequest' => TRUE,
		]);
		
		return $ret;
	}
	
	/*
	public function addPersonToProfile($opts = [])
	{
		$ret = $this->doRequest([
		   'path' => 'profile/'.$opts['profileId'].'/member/'.$opts['personId'].'/add',
		   'method' => 'POST',
		   'params' => [
		      'StatusID' => 255,
		   ],
		   'json' => TRUE,
		   'signRequest' => TRUE,
		]);
		
		return $ret;
	}
	*/
	private function doRequest($opts = [])
	{
		$headers = [];
		
		if ($this->returnJson)
		{
			$opts['path'] = 'json/'.$opts['path'];
		}
		
		if ($opts['signRequest'])
		{
			$opts['path'] .= strpos($opts['path'], '?') === FALSE ? '?' : '&';
			$opts['path'] .= 'api_session='.$this->sessionID;
			$opts['path'] .= '&api_sig='.$this->getSignature($opts['path']);
		}
		
		$content = '';
		
		if ($opts['json'])
		{
			$content = json_encode($opts['params']);
			$headers[] = 'Content-type: application/json';
		}
		else if ($opts['xml'])
		{
			$content = $opts['xml'];
			$headers[] = 'Content-type: text/xml';
		}
		else if ($opts['params'])
		{
			$content = http_build_query($opts['params']);
			
			if ($opts['method'] == 'POST')
			{
				$headers[] = 'Content-type: application/x-www-form-urlencoded';
			}
		}
		
		if ($opts['method'] == 'POST')
		{
			$headers[] = 'Content-Length: '.strlen($content);
		}
		
		$streamOpts = [
		   'http' => [
		      'method'  => $opts['method'],
		      'header'  => $headers,
		      'content' => $content,
		   ],
		];
		//print_r($streamOpts);
		$context  = stream_context_create($streamOpts);
		
		$url = $this->domainWithProtocol.$this->apiBase.$opts['path'];
		
		$result = file_get_contents($url, FALSE, $context);
		
		if ($this->returnJson)
		{
			return json_decode($result);
		}
		else
		{
			return simplexml_load_string($result);
		}
	}

	private function getSignature($request)
	{
		return md5($this->apiSecret.'_'.strtolower($request));
	}
}