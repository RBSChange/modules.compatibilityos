<?php
/**
 * @deprecated
 */
class generic_SaveAction extends f_action_BaseAction
{

	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		// Get the name of the module on which this action has been originally requested.
		$moduleName = $this->getModuleName($request);

		$docId = $this->getDocumentIdFromRequest($request);
		if (!$docId) // lang?
		{
			$context->getController()->forward($moduleName, 'Insert');
		}
		else
		{
			$document = DocumentHelper::getDocumentInstance($docId);
			if ($document->getLang() != $this->getLang() && $document->isLocalized())
			{
				$context->getController()->forward($moduleName, 'UpdateTranslation');
			}
			else
			{
				$context->getController()->forward($moduleName, 'Update');
			}
		}
		return View::NONE;
	}

	protected function getSecureActionName($documentId)
	{
		$secureAction = parent::getSecureActionName($documentId);

		$request =  $this->getContext()->getRequest();
		$fullComponentType = $request->getParameter(K::COMPONENT_ACCESSOR);
		$model = f_persistentdocument_PersistentDocumentModel::getInstanceFromDocumentModelName($fullComponentType);

		$secureAction .= '.' . $model->getDocumentName();

		if (Framework::isDebugEnabled())
		{
			Framework::debug(__METHOD__ . "($fullComponentType) -> $secureAction");
		}
		return $secureAction;
	}


	protected function getSecureNodeIds()
	{
		$request = $this->getContext()->getRequest();
		$result =  $this->getDocumentIdArrayFromRequest($request);
		if (count($result) == 0)
		{
			$parentNodeId = $request->getParameter(K::PARENT_ID_ACCESSOR);
			if (empty($parentNodeId) || !is_numeric($parentNodeId) )
			{
				$parentNodeId = ModuleService::getInstance()->getRootFolderId($this->getModuleName($request));
			}
			$result[] = intval($parentNodeId);
		}
		return $result;
	}
}