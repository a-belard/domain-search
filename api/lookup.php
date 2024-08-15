<?php 

if(isset($_POST['domain']))
{
	$domain = $_POST['domain'];
	$tld = $_POST['tld'];
	$domain_parts = explode(".", $domain);
	$domain = $domain_parts[0];
	$domain = $domain.$tld;
	function LookupIP($ip) {
		$whoisservers = array(
			//"whois.afrinic.net", // Africa - returns timeout error :-(
			"whois.lacnic.net", // Latin America and Caribbean - returns data for ALL locations worldwide :-)
			"whois.apnic.net", // Asia/Pacific only
			"whois.arin.net", // North America only
			"whois.ripe.net" // Europe, Middle East and Central Asia only
		);
		$results = array();
		foreach($whoisservers as $whoisserver) {
			$result = QueryWhoisServer($whoisserver, $ip);
			if($result && !in_array($result, $results)) {
				$results[$whoisserver]= $result;
			}
		}
		$res = "RESULTS FOUND: " . count($results);
		foreach($results as $whoisserver=>$result) {
			$res .= "\n\n-------------\nLookup results for " . $ip . " from " . $whoisserver . " server:\n\n" . $result;
		}
		return $res;
	}

	function ValidateIP($ip) {
		$ipnums = explode(".", $ip);
		if(count($ipnums) != 4) {
			return false;
		}
		foreach($ipnums as $ipnum) {
			if(!is_numeric($ipnum) || ($ipnum > 255)) {
				return false;
			}
		}
		return $ip;
	}

	function ValidateDomain($domain) {
		if(!preg_match("/^([-a-z0-9]{2,100})\.([a-z\.]{2,8})$/i", $domain)) {
			return false;
		}
		return $domain;
	}

	 
}
	if(isset($domain)) {
	$domain = trim($domain);
	if(substr(strtolower($domain), 0, 7) == "http://") $domain = substr($domain, 7);
	if(substr(strtolower($domain), 0, 8) == "https://") $domain = substr($domain, 8);
	if(substr(strtolower($domain), 0, 4) == "www.") $domain = substr($domain, 4);
	
	if(ValidateIP($domain)) {
		$result = LookupIP($domain);
		echo "<pre>\n" . $result . "\n</pre>\n";
	}
	elseif(ValidateDomain($domain)) {
		$domain_parts = explode(".", $domain);
		
		$domain = $domain_parts[0];
		$extension = $tld; //'.'. $domain_parts[1];
		//if(isset($domain_parts[2])){$extension = '.'. $domain_parts[1] .'.'. $domain_parts[2];}
		
		if($extension == '.com' || $extension == '.net')
		{
			die("Invalid domaine name... Please add an extension!");
		}
		else{
			require('whoisdomain.php');
		}
		//echo "<script type='text/javascript'>window.top.location='./?page=domains';</script>"; exit;
		
	}
	else die("Invalid domain name... Please add an extension! ". $domain);
	
}
?>