<?php

function hytera_h2f($number,$nd) {
    if (strlen($number) == 4)
    {
        $hex = '';
        for ($i = 0; $i < strlen($number); $i++) {
            $byte = strtoupper(dechex(ord($number{$i})));
            $byte = str_repeat('0', 2 - strlen($byte)).$byte;
            $hex.=$byte." ";
        }
        $number = $hex;
        unset($hex);
    }
    $r = '';
    $y = explode(' ', $number);
    foreach ($y as $z) {
        $r = $z . '' . $r;
    }
    $number = substr($r, 0, -1);
    //$number = str_replace(" ", "", $number);
    for ($i=0; $i<strlen($number); $i++)
    {
        $hex[]=substr($number,$i,1);
    }

    for ($i=0; $i<count($hex); $i++)
    {
        $dec[]=hexdec($hex[$i]);
    }

    for ($i=0; $i<count($dec); $i++)
    {
        $binfinal.=sprintf("%04d",decbin($dec[$i]));
    }

    $sign=substr($binfinal,0,1);
    $exp=substr($binfinal,1,8);
    $exp=bindec($exp);
    $exp-=127;
    $scibin=substr($binfinal,9);
    $binint=substr($scibin,0,$exp);
    $binpoint=substr($scibin,$exp);
    $intnumber=bindec("1".$binint);

    for ($i=0; $i<strlen($binpoint); $i++) {
        $tmppoint[]=substr($binpoint,$i,1);
    }

    $tmppoint=array_reverse($tmppoint);
    $tpointnumber=number_format($tmppoint[0]/2,strlen($binpoint),'.','');

    for ($i=1; $i<strlen($binpoint); $i++) {
        $pointnumber=number_format($tpointnumber/2,strlen($binpoint),'.','');
        $tpointnumber=$tmppoint[$i+1].substr($pointnumber,1);
    }

    $floatfinal=$intnumber+$pointnumber;

    if ($sign==1)
    {
        $floatfinal=-$floatfinal;
    }

    return number_format($floatfinal,$nd,'.','');
}

$sensor_value = hytera_h2f(str_replace("\"", "",snmp_get($device, $sensor['sensor_oid'], "-OUqnv", "")),2);
