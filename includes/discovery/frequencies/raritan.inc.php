<?php

if ($device['os'] == 'raritan')
{
  // Check Inlets
  $inlet_oids = snmp_walk($device,"inletLabel","-Osqn","PDU2-MIB");
  $inlet_oids = trim($inlet_oids);
  if ($inlet_oids) echo("PDU Inlet ");
  foreach (explode("\n", $inlet_oids) as $inlet_data)
  {
    $inlet_data = trim($inlet_data);
    if ($inlet_data)
    {
      list($inlet_oid,$inlet_descr) = explode(" ", $inlet_data,2);
      $inlet_split_oid = explode('.', $inlet_oid);
      $inlet_index = $inlet_split_oid[count($inlet_split_oid)-1];

      $inletsuffix = "$inlet_index";
      $inlet_insert_index = $inlet_index;

      $inlet_oid       = ".1.3.6.1.4.1.13742.6.5.2.3.1.4.1.$inletsuffix.23";
      $inlet_divisor   = pow(10,snmp_get($device, "inletSensorDecimalDigits.1.$inletsuffix.activePower", "-Ovq","PDU2-MIB");
      $inlet_frequency   = snmp_get($device, "measurementsInletSensorValue.$inletsuffix.1.frequency", "-Ovq", "PDU2-MIB") / $inlet_divisor;

      if ($inlet_frequency >= 0) {
       discover_sensor($valid['sensor'], 'frequency', $device, $inlet_oid, $inlet_insert_index, 'raritan', $inlet_descr, $inlet_divisor, 1, NULL, NULL, NULL, NULL, $inlet_frequency);
      }
  }
 }
}
