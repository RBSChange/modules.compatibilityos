<?php
/**
 * @deprecated
 */
class generic_RenameAction extends f_action_BaseAction
{	
	protected function getDocumentIdArrayFromRequest($request)
	{
		$docIds = array();
		$labels = $request->getParameter(K::LABEL_ACCESSOR);

		foreach (array_keys($labels) as $id)
		{
			list($id, ) = explode('/', $id);
			$docIds[] = intval($id);
		}
		
		return $docIds;
	}
	
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$labels = $request->getParameter(K::LABEL_ACCESSOR);

		$ds = $this->getDocumentService();
		foreach ($labels as $id => $label)
		{
			list($id, ) = explode('/', $id);
			$document = $ds->getDocumentInstance($id);
			$document->setLabel($label);
			$document->save();
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