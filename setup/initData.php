<?php
/**
 * @package modules.compatibilityos.setup
 */
class compatibilityos_Setup extends object_InitDataSetup
{
	public function install()
	{
		// Add injection for TemplateResolver.
		$this->addInjectionInProjectConfiguration('TemplateResolver', 'compatibilityos_InjectedTemplateResolver');
	}

	/**
	 * @return String[]
	 */
	public function getRequiredPackages()
	{
		// Return an array of packages name if the data you are inserting in
		// this file depend on the data of other packages.
		// Example:
		// return array('modules_website', 'modules_users');
		return array();
	}
}