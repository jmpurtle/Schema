<?php
namespace Magnus\Schema {
	
	class Validator {
		/* Validate a value against one or more rules.
		 *
		 * Validators are generally run on PHP native data types, after or 
		 * before in the case of outgoing values, any transformations.
		 *
		 * Subclass and override the `validate` method to implement your own 
		 * simple validators.
		 */

		public $kwargs;

		public function __construct(Array $kwargs = array()) {
			$this->kwargs = $kwargs;
			$this->loadAttributes();
		}

		public function validate($value, $context = null) {
			/* Attempt to validate the given value.
			 * 
			 * Return the value (possibly mutated in some way) or a Concern.
			 * Reasons to alter during validation might be to localize the
			 * format of a phone number, or normalize text.
			 *
			 * Context represents the current processing context, i.e. the 
			 * whose property is being inspected.
			 */

			return $value;
		}

		public function __get($name) {
			if (array_key_exists($this->name, $this->kwargs)) {
				return $this->kwargs[$this->name];
			}

			return null;
		}

		public function loadAttributes() {

			$objVars = array_keys(get_object_vars($this));

			foreach ($objVars as $attr) {
				if (isset($this->kwargs[$attr])) {
					$this->$attr = $this->kwargs[$attr];
					unset($this->kwargs[$attr]);
				}
			}

		}

	}

	class Always extends Validator {
		/* Always pass validation.
		 *
		 * Primarily useful to replace other validators for debugging purposes
		 */

		public function validate($value = null, $context = null) {
			return $value;
		}

	}

	class Never extends Validator {
		/* Never pass validation.
		 *
		 * Primarily useful to replace other validators for debugging purposes
		 */

		public function validate($value = null, $context = null) {
			return new Concern("Set to always fail.");
		}

	}

	class AlwaysTruthy extends Validator {
		// Value must always be truthy.

		public function validate($value, $context = null) {
			
			if ((bool) $value) {
				return $value;
			}

			return new Concern("Value is missing or empty");
		}

	}

	class AlwaysFalsy extends Validator {
		// Value must always be falsy.

		public function validate($value, $context = null) {

			if ((bool) $value) {
				return new Concern("Value should be falsy.");
			}

			return $value;
		}

	}

	class AlwaysRequired extends Validator {
		// A value must always be provided

		public function validate($value, $context = null) {
			if ($value === null) {
				return new Concern("Value is required, but none was provided.");
			}

			if (is_string($value) && !strlen($value)) {
				return new Concern("Value is required, but provided value is empty.");
			} elseif (is_array($value) && count($value) == 0) {
				return new Concern("Value is required, but provided value is empty.");
			}

			return $value;
		}

	}

	class AlwaysMissing extends Validator {
		// A value must not be provided

		public function validate($value, $context = null) {
			if ($value === null) {
				return $value;
			}

			if (is_string($value) && !strlen($value)) {
				return $value;
			} elseif (is_array($value) && count($value) == 0) {
				return $value;
			}

			return new Concern("Value must be omitted, but value was provided.");
		}

	}

	class In extends Validator {
		/* Value must be contained within the provided iterable.
		 *
		 * The iterable may either be a regular array filled with values or an
		 * associative array indicating labels and values
		 */

		public $choices;

		public function validate($value, $context = null) {
			if (!$this->choices) {
				return new Concern("Value cannot exist in empty set.");
			}

			if (!in_array($value, $this->choices)) {
				return new Concern("Value is not in allowed list.");
			}

			return $value;
		}

	}

}