<?php

/**
 * Hooks for RadiantApiTests extension
 *
 * @file
 * @ingroup Extensions
 */
class RadiantApiTestsHooks
{

	public static function onExtensionLoad()
	{
		
	}

	public static function UnitTestsList( &$paths )
	{
		$files = array_merge( $paths, glob( __DIR__ . '/tests/phpunit/*Test.php' ) );
		return true;
	}

}
