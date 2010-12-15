<?php
/**
 * @deprecated
 */
class generic_LoadAction extends f_action_BaseAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$docId = $this->getDocumentIdFromRequest($request);
		$document = $this->getDocumentService()->getDocumentInstance($docId);
		$request->setAttribute('document', $document);
		$request->setAttribute('contents', compatibilityos_DocumentHelper::toXmlForm($document));

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