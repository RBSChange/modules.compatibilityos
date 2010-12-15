<?php
/**
 * @deprecated
 */
class generic_LoadForTranslationAction extends f_action_BaseAction
{
	/**
	 * Load XML data for translation purpose.
	 *
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$documentId = $this->getDocumentIdFromRequest($request);
		$document = DocumentHelper::getDocumentInstance($documentId);

		$request->setAttribute('document', $document);
		$request->setAttribute('contents', compatibilityos_DocumentHelper::toXmlFormForTranslation($document));
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