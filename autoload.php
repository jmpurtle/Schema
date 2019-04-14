<?php
namespace Magnus {
	spl_autoload_register(function ($className) {

		$packageDirectory = __DIR__;

		$packageClassMap = array(
			'Magnus\\Schema\\Validator' => '/Magnus/Validate/base.php',
			'Magnus\\Schema\\Always'    => '/Magnus/Validate/base.php'
		);

		if (isset($packageClassMap[$className])) {
			require_once $packageDirectory . $packageClassMap[$className];
		}
		
	});
}