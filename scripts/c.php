<?php
# A Simple PHP script to show online user count.

#
# How it works?
# JQuery is used to call this file
# Server side data is stored in qa-cache/mydat.txt
#
# INSTALLATION:
# 1. Copy the below files to the root of Q2A website
#    c.php
#    qa-cache/mydat.txt
# 2. Paste the below code where you need to display the online user count
#    <script>$(window).on('load', function() {$("#_c1").load("/c.php");});</script><span id="_c1">..</span>
# 
# Credit: Internet
# http://www.askdevops.org/
# Update get_client_ip_env function # Thanks to wgergeus
#
$timeout = 300; // 5 minutes
$time = time();

function get_client_ip_env() {
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
     return $ipaddress;
}

$ip = get_client_ip_env();

if(!filter_var($ip, FILTER_VALIDATE_IP)) {echo '...';exit;} # exit if the IP is invalid

$file = "qa-cache/mydat.txt";
$arr = @file($file);
$users = 0;
for ($i = 0; $i < count($arr); $i++){
    if ($time - intval(substr($arr[$i], strpos($arr[$i], "    ") + 4)) > $timeout){
        unset($arr[$i]);
        $arr = array_values($arr);
        @file_put_contents($file, implode($arr));
    }
    $users++;
}
echo "Online: ". ($users);
# Only add entry if user isn't already there, otherwise just edit timestamp
for ($i = 0; $i < count($arr); $i++){
    if (substr($arr[$i], 0, strlen($ip)) == $ip){
        $arr[$i] = substr($arr[$i], 0, strlen($ip))."    ".$time."\n";
        $arr = array_values($arr);
        @file_put_contents($file, implode($arr));
        exit;   
    }
}
@file_put_contents($file, $ip."    ".$time."\n", FILE_APPEND);
