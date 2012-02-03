<?php
/**
 * compatibilityos_patch_0350
 * @package modules.compatibilityos
 */
class compatibilityos_patch_0350 extends patch_BasePatch
{
    /**
     * Returns true if the patch modify code that is versionned.
     * If your patch modify code that is versionned AND database structure or content,
     * you must split it into two different patches.
     * @return Boolean true if the patch modify code that is versionned.
     */
	public function isCodePatch()
	{
		return true;
	}
 
	/**
	 * Entry point of the patch execution.
	 */
	public function execute()
	{
		$nnsName = Framework::getConfigurationValue('injection/TemplateResolver', 'TemplateResolver');
		if ($nnsName != 'TemplateResolver' && $nnsName != 'compatibilityos_InjectedTemplateResolver')
		{
			$this->logWarning($nnsName . ' must be extend compatibilityos_InjectedTemplateResolver !');
		}
		else
		{
			$this->addProjectConfigurationEntry('injection/TemplateResolver', 'compatibilityos_InjectedTemplateResolver');
			$this->execChangeCommand('compile-config');
		}		
	}
}