<?php
/**
 * @deprecated
 */
class generic_ReActivateAction extends f_action_BaseAction
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
			$document = DocumentHelper::getDocumentInstance($docId);
			if ($document->getPublicationstatus() != 'DEACTIVATED')
			{
				continue;
			}
			
			try
			{
				$ds = $document->getDocumentService();
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
	 * @return Boolean
	 */
	protected function suffixSecureActionByDocument()
	{
		return true;
	}
}