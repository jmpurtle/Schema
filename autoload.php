<?php
namespace Magnus {
	spl_autoload_register(function ($className) {

		$packageDirectory = __DIR__;

		$packageClassMap = array(
			'Magnus\\Schema\\Validator'      => '/Magnus/Validate/base.php',
			'Magnus\\Schema\\Always'         => '/Magnus/Validate/base.php',
			'Magnus\\Schema\\AlwaysTruthy'   => '/Magnus/Validate/base.php',
			'Magnus\\Schema\\AlwaysFalsy'    => '/Magnus/Validate/base.php',
			'Magnus\\Schema\\AlwaysRequired' => '/Magnus/Validate/base.php',
			'Magnus\\Schema\\AlwaysMissing'  => '/Magnus/Validate/base.php',
			'Magnus\\Schema\\In'             => '/Magnus/Validate/base.php',
			'Magnus\\Schema\\Contains'       => '/Magnus/Validate/base.php',
			'Magnus\\Schema\\Length'         => '/Magnus/Validate/base.php',
			'Magnus\\Schema\\Concern'        => '/Magnus/error.php'
		);

		if (isset($packageClassMap[$className])) {
			require_once $packageDirectory . $packageClassMap[$className];
		}
		
	});
}