<?php
define('FRESHDESK_SHARED_SECRET','____Place your Single Sign On Shared Secret here_____');
define('FRESHDESK_BASE_URL','http://{{your-account}}.freshdesk.com/');	//With Trailing slashes




$username = "equilease\\" . $_POST["username"];
$sam = $_POST["username"];
$password = $_POST["password"];

$ldap = ldap_connect("ldap://eqfs-dc08-01.equilease.local") or die ('Could not connect to Northmill network.');
ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

if ($bind = ldap_bind($ldap, $username, $password)) {
    $filter = "(samAccountName=" . $sam . ")";
    $base_dn = "DC=equilease, DC=local";
    $search=ldap_search($ldap, $base_dn, $filter);
    $number_returned = ldap_count_entries($ldap,$search);
    $info = ldap_get_entries($ldap, $search);

  echo "The number of entries returned is ". $number_returned."<p>";

for ($i=0; $i<$info["count"]; $i++) {
   echo "Name is: ". $info[$i]["name"][0]."<br>";
    $strName = $info[$i]["name"][0];
   //echo "Display name is: ". $info[$i]["displayname"][0]."<br>";
   echo "Email is: ". $info[$i]["mail"][0]."<br>";
    $strEmail = $info[$i]["mail"][0];
   //echo "Telephone number is: ". $info[$i]["telephonenumber"][0]."<p>";
}
} else {
  print_r("fail");
}

function getSSOUrl($strName, $strEmail) {
	$timestamp = time();
	$to_be_hashed = $strName . "b19973a7c15a49e87120bff94d57785d" . $strEmail . $timestamp;
	$hash = hash_hmac('md5', $to_be_hashed, "b19973a7c15a49e87120bff94d57785d");
	return "http://northmillef.freshdesk.com/" . "login/sso/?name=".urlencode($strName)."&email=".urlencode($strEmail)."&timestamp=".$timestamp."&hash=".$hash;
}
header("Location: ".getSSOUrl($strName,$strEmail)); 