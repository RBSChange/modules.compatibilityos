<?php
/**
 * @deprecated
 */
class generic_ShowDocumentPropertiesAction extends f_action_BaseAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$ds = $this->getDocumentService();

		$docId = $this->getDocumentIdFromRequest($request);
		$document = $ds->getDocumentInstance($docId);
		
		$request->setAttribute('document', $document);

		return View::SUCCESS;
	}
	
	public function getRequestMethods()
	{
		return Request::POST;
	}
}