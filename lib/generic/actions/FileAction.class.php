<?php
/**
 * @deprecated
 */
class generic_FileAction extends f_action_BaseAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$docIds = $this->getDocumentIdArrayFromRequest($request);
		$ds = $this->getDocumentService();
		foreach ($docIds as $docId)
		{
			$ds->file($docId);
		}

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