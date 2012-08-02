<?php
/**
 * @package modules.compatibilityos.lib.services
 */
class compatibilityos_ModuleService extends ModuleBaseService
{
	/**
	 * Singleton
	 * @var compatibilityos_ModuleService
	 */
	private static $instance = null;

	/**
	 * @return compatibilityos_ModuleService
	 */
	public static function getInstance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = self::getServiceClassInstance(get_class());
		}
		return self::$instance;
	}

}