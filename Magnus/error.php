<?php
namespace Magnus\Schema {
	
	class Concern {

		public $message;
		public $kwargs;
		
		public function __construct($message = 'Unspecified error', Array $kwargs = array()) {
			$this->message = $message;
			$this->kwargs = $kwargs;
		}

		public function __toString() {
			return str_replace(array_keys($this->kwargs), array_values($this->kwargs), $this->message);
		}

	}

}