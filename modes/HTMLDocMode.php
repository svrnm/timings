<?php
	class HTMLDocMode extends AbstractMode {

		public function renderTemplate($data, $template, $tables = array()) {
			require_once($template);
		}

		public function modeFormat($data) {
			$result = array();
			foreach($data as $k => $d) {
				$result[$k] = str_replace("{newline}", "<br />", $d);
			}	
			return $result;
		}
	};
?>
