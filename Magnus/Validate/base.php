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

	class Contains extends Validator {
		// Value being validated must contain the given value

		public $contains;

		public function validate($value, $context = null) {
			if (is_array($value)) {
				if (array_key_exists($this->contains, $value)) {
					return $value;
				}

				if (in_array($this->contains, $value)) {
					return $value;
				}
			}

			if (is_string($value)) {
				if (strpos($value, $this->contains) !== false) {
					return $value;
				}
			}

			return new Concern("Value does not contain {$this->contains}.");
		}

	}

	class Length extends Validator {
		/* Ensures the value has a length within the given range.
		 *
		 * The defined length may represent an integer maximum length or be an
		 * array defining min, max, step.
		 *
		 * For example, array('min' => 2, 'max' => 10, 'step' => 2) would test
		 * that the value is 2, 4, 6, 8 or 10 segments long.
		 */

		public $min;
		public $max;
		public $step;

		public function validate($value, $context = null) {

			$length = null;
			if (is_string($value)) {
				$length = strlen($value);
			}

			if (is_array($value)) {
				$length = count($value);
			}

			if ($length === null) {
				return new Concern("Value's length cannot be measured.");
			}

			if ($this->min && $this->max && !(($this->min <= $length) && ($length <= $this->max))) {
				return new Concern("Out of bounds; must be greater than {$this->min} and less than {$this->max}.");
			} elseif ($this->min && ($length < $this->min)) {
				return new Concern("Too small; must be greater than {$this->min}.");
			} elseif ($this->max && ($length > $this->max)) {
				return new Concern("Too large; must be less than {$this->max}.");
			}

			if ($this->step) {
				if ($this->min) { $length = $length - $this->min; }
				if (($length % $this->step) !== 0) {
					return new Concern("Offstep; must follow a step of {$this->step}.");
				}
			}

			return $value;
		}
	}

}