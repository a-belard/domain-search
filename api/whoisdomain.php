<?php


error_reporting(E_ALL ^ E_NOTICE);



$connection_timeout = 5;

$default_extension = "";

$header_title = "";

//$header_title_url = $_SERVER["PHP_SELF"] .'?article2'; 

//$internal_style_sheet = 1; 

//$external_style_sheet = 0; 

//$external_style_sheet_location = "/style.css"; 
$body_font_family = "verdana,arial,sans-serif";
$body_font_size = "100%";
$body_background = "#d4d4d4";
$body_color = "#000000";
$body_padding = "0";
$body_border = "0";
$body_margin = "20px";

$hr_background = "#d4d4d4";
$hr_color = "#d4d4d4";

$link_text_decoration = "underline";
$link_color = "#0000ff";
$link_background = "#ffffff";

$link_hover_text_decoration = "underline";
$link_hover_color = "#0000ff";
$link_hover_background = "#ffffff";

$link_visited_text_decoration = "underline";
$link_visited_color = "#0000ff";
$link_visited_background = "#ffffff";

$link_active_text_decoration = "underline";
$link_active_color = "#0000ff";
$link_active_background = "#ffffff";

//$container_background = "#ffffff";

$title_link_font_size = "25px";
$title_link_color = "#000000";
$title_link_text_decoration = "none";
$title_link_background = "#ffffff";

$error_messages_color = "#ff0000";

//$form_background = "#f2f2f2";

//$form_border = "#d4d4d4";

$response_display_background = "#f2f2f2";

$domain_available_message_color = "#009900";

####################################################
#                                                  #
#                                                  #
#           END OF CONFIGURATION OPTIONS           #
#    No need to change anything below this line    #
#                                                  #
#                                                  #
####################################################

$supported_extensions = array(

  ".rw" => array("whois_server" => "whois.ricta.org.rw"),
  ".ac.rw" => array("whois_server" => "whois.ricta.org.rw"),
  ".co.rw" => array("whois_server" => "whois.ricta.org.rw"),
  ".gov.rw" => array("whois_server" => "whois.ricta.org.rw"),
  ".org.rw" => array("whois_server" => "whois.ricta.org.rw"),
  ".coop.rw" => array("whois_server" => "whois.ricta.org.rw"),
  ".net.rw" => array("whois_server" => "whois.ricta.org.rw"),

);

$extensions_array = array_keys($supported_extensions);

if ($_SERVER['REQUEST_METHOD'] == "POST") {

  // Trim post values and make lower-case.

  foreach ($_POST as $key => $value) {
    $_POST[$key] = strtolower(trim($value));
  }

  // Check submitted values.

  $errors = array();

  // Check domain and extension are present and have values.


  if (!isset($domain) || empty($domain) || !isset($extension) || empty($extension)) {
    $errors[] = "Please enter a domain name.";
  }

  // Check domain.

  if (isset($domain) && !empty($domain)) {

    // Remove spaces.

    $domain = str_replace(" ", "", $domain);

    // Check length of domain.

    if (strlen($domain) > 63) {
      $errors[] = "Domain is too long.  Max 63 characters.";
    }

    // Check domain for acceptable characters.

    if (!preg_match('/^[0-9a-zA-Z-]+$/i', $domain)) {
      $errors[] = "Domain may only contain numbers, letters or hyphens.";
    }

    // Check domain doesn't begin or end with a hyphen.

    if (substr(stripslashes($domain), 0, 1) == "-" || substr(stripslashes($domain), -1) == "-") {
      $errors[] = "Domain may not begin or end with a hyphen.";
    }
  }

  // Check extension is acceptable.  Extension should be lower case at this point for testing in the case-sensitive in_array().

  if (!in_array($extension, $extensions_array)) {
    $errors[] = "Domain extension is not supported.";
  }

  if (!count($errors)) {

    $domain = $domain;
    $extension = $extension;


    $whois_servers = array(

      "whois.ricta.org.rw" => array("port" => "43", "query_begin" => "", "query_end" => "\r\n", "redirect" => "0", "redirect_string" => "", "no_match_string" => "Domain status: No Object Found", "match_string" => "Domain status: No Object Found", "encoding" => "UTF-8")

    );

    $whois_server = $supported_extensions[$extension]['whois_server'];
    $port = $whois_servers[$whois_server]['port'];
    $query_begin = $whois_servers[$whois_server]['query_begin'];
    $query_end = $whois_servers[$whois_server]['query_end'];
    $whois_redirect_check = $whois_servers[$whois_server]['redirect'];
    $whois_redirect_string = $whois_servers[$whois_server]['redirect_string'];
    $no_match_string = $whois_servers[$whois_server]['no_match_string'];
    $encoding = $whois_servers[$whois_server]['encoding'];

    $whois_redirect_server = "";
    $response = "";
    $line = "";

    $fp = fsockopen($whois_server, $port, $errno, $errstr, $connection_timeout);

    if (!$fp) {
      print "fsockopen() error when trying to connect to {$whois_server}<br><br>Error number: " . $errno . "<br>" . "Error message: " . $errstr;
      exit;
    }

    fputs($fp, $query_begin . $domain . $extension . $query_end);

    while (!feof($fp)) {

      $line = fgets($fp);

      $response .= $line;

      // Check for whois redirect server.

      if ($whois_redirect_check && stristr($line, $whois_redirect_string)) {

        $whois_redirect_server = trim(str_replace($whois_redirect_string, "", $line));

        break;
      }
    }

    fclose($fp);

    // Query redirect server if set.

    if ($whois_redirect_server) {

      // Query the redirect server.  Might be different values for port etc, so give the option to change them from those set previously.  Using defaults below.

      $whois_server = $whois_redirect_server;
      $port = "43";
      $connection_timeout = 5;
      $query_begin = "";
      $query_end = "\r\n";

      $response = "";

      $fp = fsockopen($whois_server, $port, $errno, $errstr, $connection_timeout);

      if (!$fp) {
        print "fsockopen() error when trying to connect to {$whois_server}<br><br>Error number: " . $errno . "<br>" . "Error message: " . $errstr;
        exit;
      }

      fputs($fp, $query_begin . $domain . $extension . $query_end);

      while (!feof($fp)) {
        $response .= fgets($fp);
      }

      fclose($fp);
    }

    // Check result for no-match phrase.

    $domain_registered_message = "";

    if (stristr($response, $no_match_string)) {

      $domain_registered_message = '<style>a#registrars_link:hover {text-decoration: underline;}</style><span style="color:#6DFF66;">' . $domain . '' . $extension . '</span> is available. <a href="registrars" id="registrars_link">Click here to register with our RW accredited registrars</a>';
    } else {

      $domain_registered_message = '<a style="color: #D30707 !important; background: white;" href="http://' . $domain . '' . $extension . '" title="' . $domain . '" target="_blank"/>' . $domain . '' . $extension . '</a> is not available. Kindly search another name...';
    }
  }
}

if (isset($domain_registered_message) && !empty($domain_registered_message)) {
  print "" . $domain_registered_message . "";
}
