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
	
}