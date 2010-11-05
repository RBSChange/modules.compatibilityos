<?php
/**
 * @deprecated
 */
class generic_ActivateAction extends f_action_BaseAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$docIds = $this->getDocumentIdArrayFromRequest($request);

		$success = true;

		foreach ($docIds as $docId)
		{
		    $ds = $this->getDocumentServiceByDocId($docId);

		    try
			{
			    $ds->activate($docId);
			    $this->logAction(DocumentHelper::getDocumentInstance($docId));
			}
			catch (IllegalTransitionException $e)
			{
			    $request->setAttribute('message', $e->getMessage());
			    $success = false;
			}
		}

		if ($success)
		{
		    return self::getSuccessView();
		}

		return self::getErrorView();
	}
	
	/**
	 * @see f_action_BaseAction::getSecureNodeIds()
	 *
	 * @return Integer[]
	 */
	protected function getSecureNodeIds()
	{
		$id = $this->getDocumentIdFromRequest($this->getContext()->getRequest());
		$doc = DocumentHelper::getByCorrectionId($id);
		return array($doc->getId());
	}

	
	/**
	 * @return Boolean
	 */
	protected function suffixSecureActionByDocument()
	{
		return true;
	}
}