<?php
		$setup = array(
			"files" => array(
				"systems" => "files/systems.json",
				"software" => "files/software.json",
				"problems" => "files/problems.json",
				"translations" => "files/translations.json"
			),
			"data" => "csv://data/timings.txt", // some kind of identifier, later also sql,...?
			"mode" => "HTMLDoc", // future modes: LaTeX, Plot (?)
			"template" => "templates/htmldoc.php",
			"table" => "tables/default.json", // default table
			"tables" => array(
				"default" => "tables/default.json",
				"reduction" => "tables/reduction.json",
				"release" => "tables/release.json"
			)
		);
?>
