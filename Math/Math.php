<?php

namespace isCat\Math;

abstract class Math
{
	static function lastDEC( $num )
	{
		return $num % 10;
	}

	static function lastHEX( $num )
	{
		return $num % 16;
	}
}