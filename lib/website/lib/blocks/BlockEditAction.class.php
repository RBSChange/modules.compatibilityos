<?php
/**
 * website_BlockEditAction
 * @package modules.website.lib.blocks
 */
class website_BlockEditAction extends website_TaggerBlockAction
{
	/**
	 * @return array<String, String>
	 */
	function getBeanInfo()
	{
		//echo "<pre>".ProcessUtils::getBackTrace()."</pre>";
		if ($this->isInBackoffice())
		{
			return null;
		}
		$document = $this->getRequiredDocumentParameter("beanId");
		return array("className" => get_class($document), "beanName" => $document->getPersistentModel()->getDocumentName());
	}
	
	/**
	 * @see website_BlockAction::execute()
	 *
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return String
	 */
	function executeMessage($request, $response)
	{
		return "Message";
	}
	
	// InsertForm

	/**
	 * @see website_BlockAction::execute()
	 *
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return String
	 */
	function executeInsertForm($request, $response)
	{
		return $this->getInsertInputViewName($request);
	}

	// Insert

	/**
	 * @see website_BlockAction::execute()
	 *
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @param f_persistentdocument_PersistentDocument
	 * @return String
	 */
	function executeInsert($request, $response, $document)
	{
		$parent = $this->getRequiredDocumentParameter("parentId");
		$this->checkPermission("insert", $parent, $document->getPersistentModel());
		$document->save($parent->getId());
		$this->logAction("insert", $document);
		$this->goBack($request);
	}

	function getInsertBeanInfo($request)
	{
		$modelName = "modules_".$request->getParameter("documentModel");
		$persistentModel = f_persistentdocument_PersistentDocumentModel::getInstanceFromDocumentModelName($modelName);
		return array("className" => $persistentModel->getDocumentClassName(), "beanName" => $persistentModel->getDocumentName());
	}

	function getInsertInputValidationRules($request, $document)
	{
		$model = $document->getPersistentModel();
		$fileResolver = FileResolver::getInstance()->setPackageName('modules_'.$model->getModuleName())->setDirectory('config/foedition');
		$include = null;
		$exclude = null;
		$includePath = $fileResolver->getPath('insert-validate-props.txt');
		if ($includePath !== null)
		{
			$include = explode(',', f_util_FileUtils::read($includePath));
		}
		$excludePath = $fileResolver->getPath('insert-validate-props-exclude.txt');
		if ($excludePath !== null)
		{
			$exclude = explode(',', f_util_FileUtils::read($excludePath));
		}

		return BeanUtils::getBeanValidationRules(get_class($document), $include, $exclude);
	}

	function getInsertInputViewName($request)
	{
		$modelName = "modules_".$request->getParameter("documentModel");
		$persistentModel = f_persistentdocument_PersistentDocumentModel::getInstanceFromDocumentModelName($modelName);
		$parent = $this->getRequiredDocumentParameter("parentId");
		$request->setAttribute("parent", $parent);
		return $this->getTemplateByFullName('modules_'.$persistentModel->getModuleName(), 'create-'.$persistentModel->getDocumentName(), 'form');
	}

	function insertNeedTransaction()
	{
		// empty
	}

	// UpdateForm

	/**
	 * @see website_BlockAction::execute()
	 *
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return String
	 */
	function execute($request, $response, $document)
	{
		if ($this->isInBackoffice())
		{
			return "Backoffice";
		}
		$document = $this->getCorrection($document);
		$persistentModel = $document->getPersistentModel();
		$request->setAttribute($persistentModel->getDocumentName(), $document);

		return $this->getTemplateByFullName('modules_'.$persistentModel->getModuleName(), 'edit-'.$persistentModel->getDocumentName(), 'form');
	}

	// Update

	function getUpdateInputValidationRules($request, $document)
	{
		$model = $document->getPersistentModel();
		$fileResolver = FileResolver::getInstance()->setPackageName('modules_'.$model->getModuleName())->setDirectory('config/foedition');
		$include = null;
		$exclude = null;
		$includePath = $fileResolver->getPath('update-validate-props.txt');
		if ($includePath !== null)
		{
			$include = explode(',', f_util_FileUtils::read($includePath));
		}
		$excludePath = $fileResolver->getPath('update-validate-props-exclude.txt');
		if ($excludePath !== null)
		{
			$exclude = explode(',', f_util_FileUtils::read($excludePath));
		}
	
		return BeanUtils::getBeanValidationRules(get_class($document), $include, $exclude);
	}

	/**
	 * @return String|Template
	 */
	function getUpdateInputViewName()
	{
		$persistentModel = $this->getRequiredDocumentParameter("beanId")->getPersistentModel();
		return $this->getTemplateByFullName('modules_'.$persistentModel->getModuleName(), 'edit-'.$persistentModel->getDocumentName(), 'form');
	}

	/**
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @param f_persistentdocument_PersistentDocument $document
	 * @return String
	 */
	function executeUpdate($request, $response, $document)
	{
		$this->checkPermission("update", $document);
		$this->getCorrection($document, true)->save();
		$this->logAction("update", $document);
		$this->goBack($request);
	}

	/**
	 * @return void
	 */
	function updateNeedTransaction()
	{
		// empty
	}

	// Activate

	/**
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @param f_persistentdocument_PersistentDocument $document
	 * @return String
	 */
	function executeActivate($request, $response, $document)
	{
		if ($document->hasCorrection())
		{
			$correction = $this->getCorrection($document);
			$this->checkPermission("activate", $correction);
			$document->getDocumentService()->activate($correction->getId());
			$this->logAction("activate", $correction);
		}
		else
		{
			$document->getDocumentService()->activate($document->getId());
			$this->logAction("activate", $document);
		}
		$this->goBack($request);
	}

	// Re-Activate

	/**
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @param f_persistentdocument_PersistentDocument $document
	 * @return String
	 */
	function executeReActivate($request, $response, $document)
	{
		return $this->executeActivate($request, $response, $document);
	}

	// Deactivate

	/**
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @param f_persistentdocument_PersistentDocument $document
	 * @return String
	 */
	function executeDeactivated($request, $response, $document)
	{
		$this->checkPermission("deactivated", $document);
		$document->getDocumentService()->deactivate($document->getId());
		$this->logAction("deactivated", $document);
		$this->goBack($request);
	}

	// Delete

	/**
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @param f_persistentdocument_PersistentDocument $document
	 * @return String
	 */
	function executeDelete($request, $response, $document)
	{
		$this->checkPermission("delete", $document);
		$fromDetailPage = strpos(LinkHelper::getDocumentUrl($document), $request->getParameter("edit_from_url")) !== false;
		$message = f_Locale::translate("&modules.generic.frontoffice.action.delete.success;", array("label" => $document->getLabel()));
		$document->delete();
		$this->logAction("delete", $document);
		
		if ($fromDetailPage)
		{
			$this->redirect("website", "edit", array("website_BlockAction_submit" => array("edit" => array("message" => "true")), "message" => $message));	
		}
		else
		{
			$this->goBack($request);
		}
	}

	// DeleteCorrection

	/**
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @param f_persistentdocument_PersistentDocument $document
	 * @return String
	 */
	function executeDeleteCorrection($request, $response, $document)
	{
		if ($document->hasCorrection())
		{
			$correction = $this->getCorrection($document);
			$this->checkPermission("delete", $correction);
			$correction->delete();
			$this->logAction("delete", $correction);
			return $this->redirectToUrl(LinkHelper::getDocumentUrl($document));
		}
		$this->goBack($request);
		return null;
	}

	// private methods

	/**
	 *
	 * @param String $actioName
	 * @param f_persistentdocument_PersistentDocument $document
	 * @param array<String, String> $info
	 * @return void
	 */
	private function logAction($actionName, $document, $info = array())
	{
		$ids = website_BlockEditlistAction::getStoredDocumentIds(true);
		$documentId = ($document->isCorrection())? $document->getCorrectionofid() : $document->getId();
		foreach ($ids as $key => $value)
		{
			if ($value["i"] === $documentId)
			{
				unset($ids[$key]);
				break;
			}
		}
		$ids[] = array("i" => $document->getId(), "a" => $actionName, "t" => time());
		website_BlockEditlistAction::storeDocumentIds($ids);
		$persistentModel = $document->getPersistentModel();
		$moduleName = $persistentModel->getModuleName();
		$actionName .= '.' . strtolower($persistentModel->getDocumentName());
		UserActionLoggerService::getInstance()->addCurrentUserDocumentEntry($actionName, $document, $info, $moduleName);
	}

	/**
	 * @param f_mvc_Request $request
	 * @return void
	 */
	private function goBack($request)
	{
		$this->redirectToUrl($request->getParameter("edit_from_url"));
	}

	/**
	 * @return f_persistentdocument_PersistentDocument
	 */
	private function getCorrection($document, $create = false)
	{
		$documentService = $document->getDocumentService();
		if ($documentService->correctionNeeded($document))
		{
			if ($document->hasCorrection())
			{
				return $documentService->getDocumentInstance($document->getCorrectionId());
			}
			if ($create)
			{
				return $documentService->createDocumentCorrection($document);
			}
		}
		return $document;
	}

	/**
	 * @param String $actionName short action name, completed with
	 * @param f_persistentdocument_PersistentDocument $document
	 * @param f_persistentdocument_PersistentDocumentModel $documentModel
	 * @throws Exception if the current backenduser does not have the permission for the action
	 * @return void
	 */
	private function checkPermission($actionName, $document, $documentModel = null)
	{
		$user = users_UserService::getInstance()->getCurrentBackEndUser();
		if ($user === null)
		{
			throw new Exception("User is not logged");
		}
		if ($documentModel === null)
		{
			$documentModel = $document->getPersistentModel();
		}
		$permission = "modules_".$documentModel->getModuleName().".".ucfirst($actionName).".".$documentModel->getDocumentName();
		f_permission_PermissionService::getInstance()->checkPermission($user, $permission, $document->getId());
	}
}
