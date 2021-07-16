<?php

function valid_pass($candidate) {
    echo json_encode($candidate);
    $r1='/[A-Z]/';  //Uppercase
    $r2='/[a-z]/';  //lowercase
    $r3='/[!@#$%^&*()\-_=+{};:,<.>]/';  // whatever you mean by 'special char'
    $r4='/[0-9]/';  //numbers
 
    if(preg_match_all($r1,$candidate, $o)<2) return FALSE;
 
    if(preg_match_all($r2,$candidate, $o)<2) return FALSE;
 
    if(preg_match_all($r3,$candidate, $o)<2) return FALSE;
 
    if(preg_match_all($r4,$candidate, $o)<2) return FALSE;
 
    if(strlen($candidate)<8) return FALSE;
 
    return TRUE;
 }

 echo json_encode(valid_pass('AdmiN1234')) ;