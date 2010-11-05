<?php
/**
 * @deprecated
 */
class generic_GetPreferencesDocumentAction extends f_action_BaseAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$moduleName = $this->getModuleName($request);

		try
		{
		    $preferencesDocumentId = ModuleService::getInstance()->getPreferencesDocumentId($moduleName);
		    $document = $this->getDocumentService()->getDocumentInstance($preferencesDocumentId);
    		$request->setAttribute('document', $document);
		}
		catch (Exception $e)
		{
			Framework::warn('[generic_GetPreferencesDocumentAction] No preferences document found for module '.$moduleName);
		}

		return self::getSuccessView();
	}
}