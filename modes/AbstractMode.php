<?php
abstract class AbstractMode {
	
	protected static $sort = array();
	protected $dicts;
	protected $defaultText;
	protected $detailText;

	public function __construct($dicts) {
		$this->dicts = $dicts;
	}

	public function render($data, $table, $template, $tables = array()) {
		if(!is_readable($template)) {
			echo "Template is not readable: ".$template."\n";
			return;
		}
		$data = $this->data2Table($data, $table);
		$this->renderTemplate($data, $template, $tables);
	}

	public function format($data, $format) {
		$result = array();
		foreach($format as $key => $value) {
			foreach($data as $s => $r) {
				$value = str_replace("{".$s."}", $r, $value);
			}
			$result[$key] = $value;
		}
		return $this->modeFormat($result);
	}

	protected static function setSort($v) {
		self::$sort = $v;
	}

	protected static function compare($a, $b) {
		foreach(self::$sort as $s) {
			if($a[$s] > $b[$s]) {
				return 1;
			} elseif($a[$s] < $b[$s]) {
				return -1;
			}
		}
		return 0;
	}

	public function data2Table($data, $table) {
		$result = array();
		$header = array();

		foreach($data as $line) {
			$id = "";
			$data = array();
			$column = "";
			$data_column = array();
			$row_id = array();
			foreach($line as $head => $entry) {
				if(in_array($head, $table["row_id"])) {
					$id .= $head."=".$entry.";";
					$row_ID[$head] = $this->resolve($line, $head);
					$header[] = $head;
				} elseif(in_array($head, $table["data_column"])) {
					$column .= $head."=".$entry.";";
					$data_column[$head] = $entry;
				} elseif(in_array($head, $table["data"])) {
					$data[$head] = $entry;
				}
			}
			$id = md5($id); // generate a kind of "unique" identifier which is also useable as array index
			$column = md5($column);

			if(!isset($result[$id])) {
				$result[$id] = $row_ID;
				$result[$id]["data"] = array();
			}
			if(!isset($result[$id]["data"][$column]) || $data < $result[$id]["data"][$column]) { // TODO: provide method to handle override
				$result[$id]["data"][$column] = $data;
			}
			
			$columns[$column] = $data_column;
		}

		if(isset($table["sort"])) {
			self::setSort($table["sort"]);
			usort($result, array("AbstractMode", "compare"));
		}

		return array("table" => $table, "rows" => $result, "data_columns" => $columns);
	}

	public function translate($word) {
		$dict = $this->dicts["translations"]["en"];
		if(isset($dict[$word])) {
			return $dict[$word];
		} else {
			return $word;
		}
	}

	public function getDictionary($name, $row, $parent) {
		$dict = array();
		if(strpos($name, "::") !== FALSE) {
			list($parentName, $name) = explode("::", $name, 2);
			$parent = $this->getDictionary($parentName, $row, $parent);
			if(isset($parent[$row[$parentName]])) {
				return $this->getDictionary($name, $row, $parent[$row[$parentName]]);
			} else {
				return $dict;
			}
		} else {
			if(isset($parent[$name])) {
				$dict = $parent[$name];
			} elseif(isset($parent[$name."s"])) {
				$dict = $parent[$name."s"];
			}
			
			return $dict;
		}
	}

	public function autoLink($r) {
		return preg_replace('`([^"=\'>])((http|https|ftp)://[^\s<]+[^\s<\.)])`i', '$1<a href="$2">$2</a>', $r);
	}
	
	public function resolve($row, $dictionary) {
		$word = $dictionary;
		if(!is_array($row[$word])) {
			$dict = $this->getDictionary($dictionary, $row, $this->dicts);
			if(!empty($dict[ $row[$word] ])) {
				$r = $dict[ $row[$word] ];
				return $r;
			} else {
				return array("short" => $row[$word]);
			}
		} else {
			return $row[$word];
		}
	}

	protected abstract function renderTemplate($data, $template, $tables = array());

	protected abstract function modeFormat($data); 
}
?>
