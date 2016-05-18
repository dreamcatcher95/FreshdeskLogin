<?php
define('FRESHDESK_SHARED_SECRET','____Place your Single Sign On Shared Secret here_____');
define('FRESHDESK_BASE_URL','http://{{your-account}}.freshdesk.com/');	//With Trailing slashes
ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);



$username = $_POST["username"];
$password = $_POST["password"];

$ldap = ldap_connect("ldap://eqfs-dc08-01.equilease.local") or die ('Could not connect to Northmill network.');
if ($bind = ldap_bind($ldap, $username, $password)) {
    $filter = "(&(objectClass=user)(cn=$*))";
    $base_dn = "DC=equilease, DC=local";
    $search=ldap_search($ldap, $basedn, $filter);
    $info = ldap_get_entries($ldap, $search);

    if($info["count"] > 0) {
        for ($x=0; $x<$info["count"]; $x++) {
            $sam=$info[$x]['samaccountname'][0];
            $giv=$info[$x]['givenname'][0];
            $tel=$info[$x]['telephonenumber'][0];
            $email=$info[$x]['mail'][0];
            $nam=$info[$x]['cn'][0];
            $dir=$info[$x]['homedirectory'][0];
            $dir=strtolower($dir);
            $pos=strpos($dir,"home");
            $pos=$pos+5;
                if (stristr($sam, $SearchFor) && (strlen($dir) > 8)) {
                  print "\nActive Directory says that:\n";
                  print "CN is: ".$nam." \n";
                  print "SAMAccountName is: ".$sam." \n";
                  print "Given Name is: ".$giv." \n";
                  print "Telephone is: ".$tel." \n";
                  print "Home Directory is: ".$dir." \n";
                }   
        }
        }
} else {
  // error message
}

/**function getSSOUrl($strName, $strEmail) {
	$timestamp = time();
	$to_be_hashed = $strName . FRESHDESK_SHARED_SECRET . $strEmail . $timestamp;
	$hash = hash_hmac('md5', $to_be_hashed, FRESHDESK_SHARED_SECRET);
	return FRESHDESK_BASE_URL."login/sso/?name=".urlencode($strName)."&email=".urlencode($strEmail)."&timestamp=".$timestamp."&hash=".$hash;
}
header("Location: ".getSSOUrl("User's Name","username@thecompany.com")); **/