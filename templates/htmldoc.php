<!DOCTYPE html> <html lang="en"><head><meta charset="utf-8"/><title></title>
<link rel="stylesheet" type="text/css" href="templates/default.css" />
<script src="templates/jquery.js"></script>
</head><body>
<?php
	if(isset($tables)) {
?>
<form method="GET" id="formtable">
Switch table: <select name="table" id="selecttable">
<?php
	foreach($tables as $table => $value) :
?>
	<option <?php if($_GET["table"] == $table) echo 'selected="selected"'; ?>><?php echo $table ?></option>
<?php
	endforeach;
?>
</select>
<input id="nojsbutton" type="submit" value="Change." />
</form>
<?php
	}
?>
<table border="1">
<tr>
<?php

	foreach($data['table']['row_id'] as $head) {
		echo "<th>".$this->translate($head)."</th>";
	}
	echo '<th colspan="'.count($data["data_columns"]).'">';
	foreach($data['table']['data_column'] as $head) {
		echo $this->translate($head)."&nbsp;";
	}
	echo '</th>';
?>
</tr><tr>
<?php
	foreach($data['table']['row_id'] as $head) {
		echo "<th>&nbsp;</th>";
	}
	foreach($data['data_columns'] as $subheader) {
	echo '<th>';
		foreach($subheader as $subhead) {
			echo $subhead." ";
		}
	echo '</th>';
	}
?>
<tr>
<?php
	$c = 0;

	foreach($data['rows'] as $row) {
		echo "<tr>";
		foreach($row as $head => $entry) {
			if($head != "data") {
				$hasDetails = isset($entry["long"]);
					echo '<td><span '.($hasDetails ? 'class="hasdetails" onclick="showDetails(\'#details_'.$c.'\');"': '').'>'.$entry["short"]."</span><br />";
				if($hasDetails) {
					echo '<span id="details_'.$c.'" class="details">'.$entry["long"].'</span>';
				}
				echo "</td>";
				$c++;
			} else {
				foreach($data['data_columns'] as $id => $subheader) {
					echo "<td>";
					if(isset($entry[$id])) {
						$e = $this->format($entry[$id], $data['table']['data_format']);
						$hasDetails = isset($e["long"]);
						echo '<span '.($hasDetails ? 'class="hasdetails" onclick="showDetails(\'#details_'.$c.'\');"': '').'>'.$e["short"]."</span><br />";
						if($hasDetails) {
							echo '<span id="details_'.$c.'" class="details">'.$e["long"].'</span>';
						}
						$c++;
					} else {
						echo 'n.a.';
					}
					echo "</td>";
				}
			}
		}
		echo "</tr>";
	}
?>	
</table>

<p class="description">
<?php
if(isset($data['table']['description'])) {
	echo $data['table']['description'];
}
?>
</p>

<script>
function showDetails(id) {
	$(id).toggle();
}
$("#nojsbutton").hide();
$("#selecttable").change(function() {
	$("#formtable").submit();
}
);
</script>
</body>
