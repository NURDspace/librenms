<?php

if (!$os || $os == "hytera") {
    if (preg_match("/^lwIP/", $sysDescr)) {
	$os = "hytera";
    }
}

?>
