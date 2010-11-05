<?php
/**
 * @deprecated
 */
class generic_InsertAction extends f_action_BaseAction
{

	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		// Get the name of the module on which this action has been originally requested.
		$moduleName = $this->getModuleName($request);

		//---------------------------------------------------------------------
		// 1. Instanciate or create the document to save.
		//---------------------------------------------------------------------
		$fullComponentType = $request->getParameter(K::COMPONENT_ACCESSOR);
		$componentObject = ComponentTypeObject::getInstance($fullComponentType);
		$componentType = $componentObject->getComponentType();

		$ds = ServiceLoader::getServiceByDocumentModelName($fullComponentType);
		$document = $ds->getNewDocumentInstance();

		//---------------------------------------------------------------------
		// 2. Prepare and set the values.
		//---------------------------------------------------------------------
		DocumentHelper::setPropertiesFromRequestTo($request, $document);

		//---------------------------------------------------------------------
		// 3. Save the document and insert it into the tree.
		//---------------------------------------------------------------------
		$parentNodeId = null;
		if ($componentType != ModuleService::SETTING_PREFERENCES_DOCUMENT_TYPE)
		{
			$parentNodeId = intval($request->getParameter(K::PARENT_ID_ACCESSOR));
			if ($parentNodeId <= 0)
			{
				$parentNodeId = null;
			}
		}
		
		// If $document is a preferences document, $parentNodeId will be null and
		// the $document will not be inserted into a tree.
		$document->save($parentNodeId);
		$this->logAction($document);

		//---------------------------------------------------------------------
		// 4. Register preferences document.
		//---------------------------------------------------------------------
		if ($componentType == ModuleService::SETTING_PREFERENCES_DOCUMENT_TYPE)
		{
			// Register this document as the preferences document for the module.
			if (Framework::isDebugEnabled())
			{
				Framework::debug(__METHOD__ . "($fullComponentType) -> setPreferencesDocumentId");
			}
			ModuleService::getInstance()->setPreferencesDocumentId($moduleName, $document->getId());
		}

		//---------------------------------------------------------------------
		// 5. Prepare the view
		//---------------------------------------------------------------------
		$request->setAttribute('document', $document);
		$request->setAttribute('message', f_Locale::translate("&modules.generic.backoffice.form.Insertsuccessmessage;"));
		$request->setAttribute(K::PARENT_ID_ACCESSOR, $parentNodeId);

		return self::getSuccessView();
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
	
	/**
	 * Returns an array of the documents IDs received by this action.
	 * All the IDs contained in the resulting array are REAL integer values, not strings.
	 * @param Request $request
	 * @return array<integer>
	 */
	protected function getDocumentIdArrayFromRequest($request)
	{	
		$docIds = array();
		$parentNodeId = $request->getParameter(K::PARENT_ID_ACCESSOR);
		if (empty($parentNodeId) || !is_numeric($parentNodeId) )
		{
			$parentNodeId = ModuleService::getInstance()->getRootFolderId($this->getModuleName($request));
		}
		$docIds[] = intval($parentNodeId);

		return $docIds;
	}
}