<?php
	/**
	 * XML Project
	 * @version		1.0.0
	 * @author 		Frad Ali and Jmal Chadly	 	 
	 */	
	class xmlMap extends xmlNode {
		protected $doc;
		protected $map = array();
		protected $many = array();
		protected $rootName;
		private $file;
		public function __construct($file = null) {
			if (!is_null($file)) {
				$this->load($file);
			} else {
				$this->rootName = 'root';
			}
		}
		/**
		 * Map a tag to a class
		 */		 		
		protected function mapTag($name, $class) {
			if (!isset($this->map[$name])) $this->map[$name] = $class;
		}
		/**
		 * Defines multiple entries
		 */		 		
		protected function hasMany($tag) {
			$this->many[$tag] = true;
		}		
		/**
		 * Loads a file to a class
		 */		 		
		public function load($file) {
			$this->doc = new DOMDocument();
			if (!file_exists($file)) {
				throw new Exception('Unable to load xml file : '.$file);
			}
			$this->doc->load($file);			
			$root = $this->doc->firstChild;
			$this->file = $file;
			$this->rootName = $root->nodeName;
			// READING ATTRIBUTES
			if ($root->hasAttributes()) {
				$a = 0; 
				while($root->attributes->item($a)) {
					$name = $root->attributes->item($a)->nodeName;
					$value = $root->attributes->item($a)->nodeValue;
					$this->$name = $value;
					$a++;
				}
			}
			// READING CHILDS
			$this->readNodes($root->childNodes, $this);
		}
		/**
		 * Save the file
		 */		 		
		public function save($file = null) {
			if (!is_null($file)) $this->file = $file;
			$f = fopen($this->file, 'w+');
			if (!$f) {
				throw new Exception('Unable to save the file '.$this->file);
			}
			fputs($f, '<?xml version="1.0"?'.">\n");
			fputs($f, '<'.$this->rootName.'>'."\n");
			fputs($f, $this->serialize($this->__properties));
			fputs($f, '</'.$this->rootName.'>'."\n");
			fclose($f);
		}
		// SERIALIZE A COLLECTION OF PROPERTIES
		private function serialize($array, $level = 1) {
			$ret = '';
			foreach($array as $key => $value) {
				if (is_array($value)) {
					foreach($value as $val) {
						$ret .= $this->serializeItem($key, $val, $level);
					}
				} else $ret .= $this->serializeItem($key, $value, $level);
			}
			return $ret;
		}
		// SERIALIZE AN ITEM
		private function serializeItem($key, $value, $level) {
			$ret = str_repeat("\t", $level).'<'.$key.'>';
			if (is_a($value, 'xmlNode')) {
				$ret .= "\n".$this->serialize($value->getProperties(), $level + 1).str_repeat("\t", $level);					
			} else {
				$ret .= $this->getNodeValue($value);					
			}		
			$ret .= '</'.$key.">\n";
			return $ret;
		}
		// SERIALIZE AN VALUE
		private function getNodeValue($value) {
			if (is_numeric($value)) {
				return $value;
			} else {
				if (strpos($value, '&') === false && strpos($value, '<') === false) {
					return $value;
				} else return '<![CDATA['.$value.']]>';
			}
		} 
		/**
		 * Read nodes
		 */		 		
		private function readNodes($nodes, &$target) {		
			foreach($nodes as $node) {
				$name = $node->nodeName;
				if (substr($name,0,1) !=  '#') {				
					// HANDLE NODE
					if (isset($target->$name) || isset($this->many[$name])) {
						if (isset($target->$name)) {
							$val = $target->$name;
							if (!is_array($target->$name)) {
								$val = array($val);
							}
						} else $val = array();
						$val[] = $this->addNode($node, $target);
						$target->$name = $val;
					} else {
						$target->$name = $this->addNode($node, $target);
					}							
					// HANDLE ATTRIBUTES
					if ($node->hasAttributes()) {
						$target->$name = $this->addAttributes($node, $target->$name);		
					}					
				}
			}
		}
		/**
		 * Reads and add a node
		 */ 		 		
		private function addNode($node, &$target) {
			$name = $node->nodeName;
			if ($this->hasChilds($node)) {
				$ret = $this->loadNode($name);											
				$this->readNodes($node->childNodes, $ret);
			} else {
				$ret = $node->nodeValue;
			} 
			return $ret;
		}
		/**
		 * Verify if node have childs
		 */		 		 
		private function hasChilds($node) {
			if (!$node->hasChildNodes() || $node->childNodes->length < 2) {
				return false;
			} else {
				foreach($node->childNodes as $child) {
					if (substr($child->nodeName, 0, 1) != '#') return true;
				}
				return false;
			}
		}
		/**
		 * Read attributes and add to target
		 */		 		
		private function addAttributes($node, $target) {
			$name = $node->nodeName;
			if (!$target) $target = $this->loadNode($name);
			if (is_array($target)) {
				if (!$target[sizeof($target) - 1]) {
					$target[sizeof($target) - 1] = $this->loadNode($name);
				}
			}
			$a = 0; 
			while($node->attributes->item($a)) {
				$name = $node->attributes->item($a)->nodeName;
				$value = $node->attributes->item($a)->nodeValue;
				if (is_array($target)) {
					$target[sizeof($target) - 1]->$name = $value;
				} else {
					$target->$name = $value;
				}													
				$a++;
			}	
			return $target;			
		}
		/**
		 * Initialize a node
		 */		 		
		private function loadNode($name) {
			if (isset($this->map[$name])) {
				return new $this->map[$name]();
			} else {
				return new xmlNode();
			}
		}
	}

	/**
	 * Représente un noeud XML
	 */	 	
	class xmlNode {
		/**
		 * Propriétées
		 */		 		
		protected $__properties = array();
		/**
		 * Add an item 
		 */		 		
		public function add($name, $value) {
			if (is_array($this->__properties[$name])) {
				$this->__properties[$name][] = $value;
				return true;
			} else return false;
		}
		/**
		 * Add an item 
		 */		 		
		public function item($name, $value) {
			if (is_array($this->__properties[$name])) {
				$this->__properties[$name][$item] = $value;
				return true;
			} else return false;
		}		
		/**
		 * Removes an item
		 */		 		
		public function remove($name, $index) {
			if (is_array($this->__properties[$name])) {
				if ($index == 0) {
					array_shift($this->__properties[$name]);
					return true; 
				} elseif ($index == sizeof($this->__properties[$name]) - 1) {
					array_pop($this->__properties[$name]);
					return true;
				} else {
					if (isset($this->__properties[$name][$index])) {
						$this->__properties[$name] = array_merge(
							array_slice($this->__properties[$name], 0, $index), 
							array_slice($this->__properties[$name], $index + 1)
						);
						return true;				
					} else return false;
				}
			} else return false;
		}
		/**
		 * Verify if property is defined
		 */		 		
		public function contains($key) {
			return (isset($this->__properties[$key]));
		}
		/**
		 * Get all properties
		 */		 		
		public function getProperties() {
			return $this->__properties;
		}
		/**
		 * Vérifie l'existance d'une propriétée
		 */		 		
		public function __isset($key) {
			return (isset($this->__properties[$key]));
		}
		/**
		 * Lit la valeur d'une propriétée
		 */		 		
		public function __get($key) {
			if (isset($this->__properties[$key])) {
				return $this->__properties[$key];
			} else {
				return null;
			}
		} 
		/**
		 * Enregistre la valeur d'une propriétée
		 */		 		
		public function __set($key, $value) {
			$this->__properties[$key] = $value;
		}
	}

?>