<?php

namespace Models;

use Models\Base\Config as BaseConfig;

/**
 * Skeleton subclass for representing a row from the 'configs' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class Config extends BaseConfig {	
	const TYPE_STRING 	= 'string';
	const TYPE_INT 		= 'int';
	const TYPE_DOUBLE 	= 'double';
	const TYPE_BOOLEAN 	= 'boolean';	

	public static function GetConfig($key) {
		$config = ConfigQuery::create()->findOneByKey($key);
		if (!empty($config)) {
			return Config::ParseValue($config);
		}
	}

	private static function ParseValue($config){
		switch ($config->getType()) {
			case Config::TYPE_STRING: {
				return $config->getValue();
			} break;
			case Config::TYPE_INT: {
				return intval($config->getValue());
			} break;
			case Config::TYPE_DOUBLE: {
				return doubleval($config->getValue());
			} break;
			case Config::TYPE_BOOLEAN: {
				return filter_var($config->getValue(), FILTER_VALIDATE_BOOLEAN);
			} break;
		};
	}
}
