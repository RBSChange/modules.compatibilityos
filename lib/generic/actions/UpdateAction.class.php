<?php
/**
 * @deprecated
 */
class generic_UpdateAction extends f_action_BaseAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$document = $this->getDocumentInstanceFromRequest($request);
		$documentService = $document->getDocumentService();

		if ($documentService->correctionNeeded($document))
		{
			$document = $documentService->createDocumentCorrection($document);
		}

		DocumentHelper::setPropertiesFromRequestTo($request, $document);

		$documentService->save($document);
		
		$this->logAction($document);

		$request->setAttribute('document', $document);
		$request->setAttribute('message', f_Locale::translate("&modules.generic.backoffice.form.Updatesuccessmessage;"));

		$parentNodeId = $request->getParameter(K::PARENT_ID_ACCESSOR);
		$request->setAttribute(K::PARENT_ID_ACCESSOR, $parentNodeId);

		return self::getSuccessView();
	}

	/**
	 * @return Boolean
	 */
	protected function suffixSecureActionByDocument()
	{
		return true;
	}
}