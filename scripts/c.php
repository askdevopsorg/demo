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
#
$timeout = 300; // 5 minutes
$time = time();
$ip = $_SERVER["REMOTE_ADDR"];

if(!filter_var($ip, FILTER_VALIDATE_IP)) {echo '...';exit;} # exit is IP is invalid

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