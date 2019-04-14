<?php
$packageRoot = dirname(__DIR__);
require_once $packageRoot . '/autoload.php';

// Test Helpers
function printEval($condition) {
	// Just throw your conditional statement in there and it'll auto-var_export, just to make it look nicer
	return var_export($condition, true);
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

<?= "\n\n" ?>