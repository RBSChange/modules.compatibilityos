<?php
/**
 * @package modules.compatibilityos.setup
 */
class compatibilityos_Setup extends object_InitDataSetup
{
	public function install()
	{
		$nnsName = Framework::getConfigurationValue('injection/TemplateResolver', 'TemplateResolver');
		if ($nnsName != 'TemplateResolver' && $nnsName != 'compatibilityos_InjectedTemplateResolver')
		{
			$this->addWarning($nnsName . ' must be extend compatibilityos_InjectedTemplateResolver !');
		}
		else
		{
			$this->addProjectConfigurationEntry('injection/TemplateResolver', 'compatibilityos_InjectedTemplateResolver');
		}
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