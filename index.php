<?php
		ini_set("display_errors", 1);
		ini_set("display_startup_errors", 1);
		ini_set("error_reporting", E_ALL);
		
		require_once("setup.php");		
	
		$dicts = array();
		foreach($setup["files"] as $name => $file) {
			if(is_readable($file)) {
				$dicts[$name] = json_decode( file_get_contents($file), true );
			} else {
				$dicts[$name] = array();
			}
		}

		$data = array();
		list($dataType,$dataSrc) = explode("://", $setup['data'], 2);
		switch($dataType) {
			case "csv":
				$f = file($dataSrc);
				$head = explode(";", trim(array_shift($f)));
				foreach($f as $line) {
					$r = explode(";", trim($line));
					$data[] = array_combine($head, $r);
				}
			break;
		}

		$modeClass = $setup["mode"]."Mode";
		require_once("modes".DIRECTORY_SEPARATOR."AbstractMode.php");
		require_once("modes".DIRECTORY_SEPARATOR.$modeClass.".php");
		
		$mode = new $modeClass($dicts);

		$table = json_decode( file_get_contents($setup['table']), true);
		if(isset($_GET["table"])) {
				$i = $_GET["table"];
				$t = $setup['tables'][$i];
				if(!empty($t)) {
					$table = json_decode( file_get_contents($t), true);
				}
		}


		$mode->render($data, $table, $setup['template'], $setup['tables']);
?>
