<?php
$packageRoot = dirname(__DIR__);
require_once $packageRoot . '/autoload.php';

// Test Helpers
function printEval($condition) {
	// Just throw your conditional statement in there and it'll auto-var_export, just to make it look nicer
	return var_export($condition, true);
}
function massCheck($validator, $values) {
	$cases = array();
	foreach ($values as $value) {
		$cases[] = $validator->validate($value);
	}
	return $cases;
}
?>
Feature: Magnus Schema
		 As a developer
		 I want to create validators
		 So I can validate data within the application

Scenario: Creating a Validator

	Given an initialized Validator with no arguments:
	<?php $validator = new Magnus\Schema\Validator(); ?>

	The initialization should succeed:
	<?= printEval(get_class($validator) == 'Magnus\\Schema\\Validator') ?>


Scenario: Validating a base Validator

	Given an initialized Validator with no arguments:
	<?php $validator = new Magnus\Schema\Validator(); ?>

	When validated:
	<?php $validatedValue = $validator->validate('foo'); ?>

	The validation should succeed, providing the value back:
	<?= printEval($validatedValue == 'foo'); ?>


Scenario: Creating an Always Validator

	Given an initialized Always with no arguments:
	<?php $validator = new Magnus\Schema\Always(); ?>

	When validated:
	<?php $validatedValue = $validator->validate('foo'); ?>

	The validation should succeed, providing the value back:
	<?= printEval($validatedValue == 'foo'); ?>


Scenario: Creating a Never Validator

	Given an initialized Never with no arguments:
	<?php $validator = new Magnus\Schema\Never(); ?>

	When validated:
	<?php $validatedValue = $validator->validate('foo'); ?>

	The validation should fail, providing a Concern back:
	<?= printEval(get_class($validatedValue) == 'Magnus\\Schema\\Concern'); ?>

	And when turned into a string, the concern should state 'Set to always fail.':
	<?= printEval((string) $validatedValue == 'Set to always fail.') ?>


Scenario: Validating with AlwaysTruthy:

	Given an initialized AlwaysTruthy with no arguments:
	<?php $validator = new Magnus\Schema\AlwaysTruthy(); ?>

	When validated:
	<?php $validatedValue = $validator->validate('foo'); ?>

	The validation should succeed, providing the value back:
	<?= printEval($validatedValue == 'foo'); ?>

	And validated against falsy values should fail with Concerns:
	<?php
		$cases = massCheck($validator, array(false, 0, -0, 0.0, -0.0, '', "0", array(), null));
		$status = true;
		foreach ($cases as $case) {
			if ((string) $case != 'Value is missing or empty') { $status = false; }
		}
		echo printEval($status);
	?>


Scenario: Validating with AlwaysFalsy:

	Given an initialized AlwaysFalsy with no arguments:
	<?php $validator = new Magnus\Schema\AlwaysFalsy(); ?>

	When validated:
	<?php $validatedValue = $validator->validate('foo'); ?>

	The validation should fail, providing a Concern back:
	And when turned into a string, the concern should state 'Value should be falsy.':
	<?= printEval((string) $validatedValue == 'Value should be falsy.') ?>

	And validated against falsy values should succeed with their values:
	<?php
		$cases = massCheck($validator, array(false, 0, -0, 0.0, -0.0, '', "0", array(), null));
		$status = false;
		if ($cases == array(false, 0, -0, 0.0, -0.0, '', "0", array(), null)) {
			$status = true;
		}
		echo printEval($status);
	?>


Scenario: Validating with AlwaysRequired:

	Given an initialized AlwaysRequired with no arguments:
	<?php $validator = new Magnus\Schema\AlwaysRequired(); ?>

	When validated:
	<?php $validatedValue = $validator->validate('foo'); ?>

	The validation should succeed, providing the value back:
	<?= printEval($validatedValue == 'foo'); ?>

	And validated against empty values should fail with Concerns:
	Given Null:
	<?php $validatedValue = $validator->validate(null); ?>

	Fails with "Value is required, but none was provided.":
	<?= printEval((string) $validatedValue == 'Value is required, but none was provided.') ?>

	Given empty string:
	<?php $validatedValue = $validator->validate(''); ?>

	Fails with "Value is required, but provided value is empty.":
	<?= printEval((string) $validatedValue == 'Value is required, but provided value is empty.') ?>

	Given empty array:
	<?php $validatedValue = $validator->validate(array()); ?>

	Fails with "Value is required, but provided value is empty.":
	<?= printEval((string) $validatedValue == 'Value is required, but provided value is empty.') ?>

	Given 0:
	<?php $validatedValue = $validator->validate(0); ?>

	The validation should succeed, providing the value back:
	<?= printEval($validatedValue === 0); ?>


Scenario: Validating with AlwaysMissing:

	Given an initialized AlwaysMissing with no arguments:
	<?php $validator = new Magnus\Schema\AlwaysMissing(); ?>

	When validated:
	<?php $validatedValue = $validator->validate('foo'); ?>

	The validation should fail with "Value must be omitted, but value was provided.":
	<?= printEval((string) $validatedValue == "Value must be omitted, but value was provided."); ?>

	And validated against missing values should succeed:
	Given Null:
	<?php $validatedValue = $validator->validate(null); ?>

	And succeeds, returning null:
	<?= printEval($validatedValue === null) ?>

	Given empty string:
	<?php $validatedValue = $validator->validate(''); ?>

	Succeeds with an empty string:
	<?= printEval($validatedValue == '') ?>

	Given empty array:
	<?php $validatedValue = $validator->validate(array()); ?>

	Succeeds with empty array:
	<?= printEval($validatedValue == array()) ?>

	Given 0:
	<?php $validatedValue = $validator->validate(0); ?>

	The validation should fail with "Value must be omitted, but value was provided.":
	<?= printEval((string) $validatedValue == "Value must be omitted, but value was provided."); ?>


Scenario: Validating with In:

	Given an initialized In with no arguments:
	<?php $validator = new Magnus\Schema\In(); ?>

	When validated:
	<?php $validatedValue = $validator->validate('foo'); ?>

	The validation should fail with "Value cannot exist in empty set.":
	<?= printEval((string) $validatedValue == "Value cannot exist in empty set."); ?>

	Given an initialized In with choices:
	<?php $validator = new Magnus\Schema\In(array('choices' => array('foo', 'baz' => 'thud'))); ?>

	When validated:
	<?php $validatedValue = $validator->validate('foo'); ?>

	The validation should succeed, returning 'foo':
	<?= printEval($validatedValue == 'foo') ?>

	When validated with a value that shouldn't exist:
	<?php $validatedValue = $validator->validate('bar'); ?>

	The validation should fail with "Value is not in allowed list.":
	<?= printEval((string) $validatedValue == "Value is not in allowed list.") ?>

	When validated with a value inside a label-value pair:
	<?php $validatedValue = $validator->validate('thud'); ?>

	The validation should succeed, returning 'thud':
	<?= printEval($validatedValue == 'thud') ?>


Scenario: Validating with Contains:

	Given an initialized Contains with contains:
	<?php $validator = new Magnus\Schema\Contains(array('contains' => 'foo')); ?>

	When validated:
	<?php $validatedValue = $validator->validate('foo bar baz'); ?>

	The validation should succeed, returning the value:
	<?= printEval($validatedValue == 'foo bar baz'); ?>

	When validated with a value that shouldn't be contained:
	<?php $validatedValue = $validator->validate('thud qux'); ?>

	The validation should fail with "Value does not contain foo.":
	<?= printEval((string) $validatedValue == "Value does not contain foo.") ?>

	When validating an array:
	<?php $validatedValue = $validator->validate(array('foo', 'bar', 'baz')); ?>

	The validation should succeed, returning the value:
	<?= printEval($validatedValue == array('foo', 'bar', 'baz')); ?>


Scenario: Validating with Length:

	Given an initialized Length with no arguments:
	<?php $validator = new Magnus\Schema\Length(); ?>

	When validated:
	<?php $validatedValue = $validator->validate('foo'); ?>

	The validation should succeed due to no restrictions, returning 'foo':
	<?= printEval($validatedValue == 'foo'); ?>

	Given an initialized Length with a minimum defined:
	<?php $validator = new Magnus\Schema\Length(array('min' => 5)); ?>

	When validated with a string too short:
	<?php $validatedValue = $validator->validate('foo'); ?>

	The validation should fail with "Too small; must be greater than 5.":
	<?= printEval((string) $validatedValue == "Too small; must be greater than 5."); ?>

	When validated with an array too short:
	<?php $validatedValue = $validator->validate(array('foo')); ?>

	The validation should fail with "Too small; must be greater than 5.":
	<?= printEval((string) $validatedValue == "Too small; must be greater than 5."); ?>

	When validated with a string at the minimum length:
	<?php $validatedValue = $validator->validate('house'); ?>

	The validation should succeed, returning "house":
	<?= printEval($validatedValue == 'house'); ?>

	When validated with an array at the minimum length:
	<?php $validatedValue = $validator->validate(array(1,2,3,4,5)) ?>

	The validation should succeed, returning the given array:
	<?= printEval($validatedValue == array(1,2,3,4,5)) ?>

	Given an initialized Length with a maximum defined:
	<?php $validator = new Magnus\Schema\Length(array('max' => 5)); ?>

	When validated with a string too long:
	<?php $validatedValue = $validator->validate('foobar'); ?>

	The validation should fail with "Too large; must be less than 5.":
	<?= printEval((string) $validatedValue == "Too large; must be less than 5."); ?>

	When validated with an array too long:
	<?php $validatedValue = $validator->validate(array(1,2,3,4,5,6)); ?>

	The validation should fail with "Too large; must be less than 5.":
	<?= printEval((string) $validatedValue == "Too large; must be less than 5."); ?>

	When validated with a string at the maximum length:
	<?php $validatedValue = $validator->validate('house'); ?>

	The validation should succeed, returning "house":
	<?= printEval($validatedValue == 'house'); ?>

	When validated with an array at the maximum length:
	<?php $validatedValue = $validator->validate(array(1,2,3,4,5)) ?>

	The validation should succeed, returning the given array:
	<?= printEval($validatedValue == array(1,2,3,4,5)) ?>

	Given an initialized Length with a minimum and maximum defined:
	<?php $validator = new Magnus\Schema\Length(array('min' => 3, 'max' => 5)); ?>

	When validated with a string too short:
	<?php $validatedValue = $validator->validate('foo'); ?>

	The validation should fail with "Out of bounds; must be greater than 3 and less than 5.":
	<?= printEval((string) $validatedValue == "Out of bounds; must be greater than 3 and less than 5."); ?>

	When validated with an array too short:
	<?php $validatedValue = $validator->validate(array('foo')); ?>

	The validation should fail with "Out of bounds; must be greater than 3 and less than 5.":
	<?= printEval((string) $validatedValue == "Out of bounds; must be greater than 3 and less than 5."); ?>

	When validated with a string too long:
	<?php $validatedValue = $validator->validate('foobar'); ?>

	The validation should fail with "Out of bounds; must be greater than 3 and less than 5.":
	<?= printEval((string) $validatedValue == "Out of bounds; must be greater than 3 and less than 5."); ?>

	When validated with an array too long:
	<?php $validatedValue = $validator->validate(array(1,2,3,4,5,6)); ?>

	The validation should fail with "Out of bounds; must be greater than 3 and less than 5.":
	<?= printEval((string) $validatedValue == "Out of bounds; must be greater than 3 and less than 5."); ?>

	When validated with a string at the minimum length:
	<?php $validatedValue = $validator->validate('foo'); ?>

	The validation should succeed, returning "foo":
	<?= printEval($validatedValue == 'foo'); ?>

	When validated with an array at the minimum length:
	<?php $validatedValue = $validator->validate(array(1,2,3)) ?>

	The validation should succeed, returning the given array:
	<?= printEval($validatedValue == array(1,2,3)) ?>

	When validated with a string at the maximum length:
	<?php $validatedValue = $validator->validate('house'); ?>

	The validation should succeed, returning "house":
	<?= printEval($validatedValue == 'house'); ?>

	When validated with an array at the maximum length:
	<?php $validatedValue = $validator->validate(array(1,2,3,4,5)) ?>

	The validation should succeed, returning the given array:
	<?= printEval($validatedValue == array(1,2,3,4,5)) ?>

	Given an initialized Length with a step defined:
	<?php $validator = new Magnus\Schema\Length(array('step' => 2)); ?>

	When validated with a string onstep:
	<?php $validatedValue = $validator->validate('on'); ?>

	The validation should succeed, returning "on":
	<?php printEval($validatedValue == 'on'); ?>

	When validated with a string offstep:
	<?php $validatedValue = $validator->validate('foo'); ?>

	The validation should fail with "Offstep; must follow a step of 2.":
	<?= printEval((string) $validatedValue == "Offstep; must follow a step of 2."); ?>

	When validated with an array onstep:
	<?php $validatedValue = $validator->validate(array(1,2)); ?>

	The validation should succeed, returning the given array:
	<?php printEval($validatedValue == array(1,2)); ?>

	When validated with an array offstep:
	<?php $validatedValue = $validator->validate(array(1)); ?>

	The validation should fail with "Offstep; must follow a step of 2.":
	<?= printEval((string) $validatedValue == "Offstep; must follow a step of 2."); ?>

	Given an initialized Length with a step and a minimum defined:
	<?php $validator = new Magnus\Schema\Length(array('min' => 1, 'step' => 2)); ?>

	When validated with a string onstep:
	<?php $validatedValue = $validator->validate('one'); ?>

	The validation should succeed, returning "one":
	<?php printEval($validatedValue == 'one'); ?>

	When validated with a string offstep:
	<?php $validatedValue = $validator->validate('on'); ?>

	The validation should fail with "Offstep; must follow a step of 2.":
	<?= printEval((string) $validatedValue == "Offstep; must follow a step of 2."); ?>

	When validated with an array onstep:
	<?php $validatedValue = $validator->validate(array(1,2,3)); ?>

	The validation should succeed, returning the given array:
	<?php printEval($validatedValue == array(1,2,3)); ?>

	When validated with an array offstep:
	<?php $validatedValue = $validator->validate(array(1,2)); ?>

	The validation should fail with "Offstep; must follow a step of 2.":
	<?= printEval((string) $validatedValue == "Offstep; must follow a step of 2."); ?>

<?= "\n\n" ?>