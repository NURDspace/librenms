<?php

function hytera_h2f($number,$nd) {
    if (strlen(str_replace(" ","",$number)) == 4) 
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

if ($device['os'] == "hytera")
{
  $oids = snmp_walk($device, "rptPaTemprature", "-OsqnU","HYTERA-REPEATER-MIB");
  if ($debug) { echo($oids."\n"); }
  if ($oids !== false) echo("HYTERA-REPEATER-MIB ");
  $divisor = 1;
  $type = "hytera";

  foreach (explode("\n", $oids) as $data)
  {
    $data = trim($data);
    if ($data)
    {
      list($oid,$descr) = explode(" ", $data,2);
      $split_oid = explode('.',$oid);
      $index = $split_oid[count($split_oid)-1];
      $descr = "Voltage " . $index;
      $oid  = ".1.3.6.1.4.1.40297.1.2.1.2.2." . $index;
      $temperature = hytera_h2f(snmp_get($device, $oid, "-Oqv"),2);

      discover_sensor($valid['sensor'], 'temperature', $device, $oid, $index, $type, $descr, $divisor, '1', NULL, NULL, NULL, NULL, $temperature);
    }
  }
}

