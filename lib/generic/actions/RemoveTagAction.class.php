<?php
/**
 * generic_RemoveTagsAction
 * This class is used to execute the RemoveTags action that is able to remove
 * tags to multiple documents at once.
 */
class generic_RemoveTagAction extends f_action_BaseAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$doc = $this->getDocumentInstanceFromRequest($request);
		$tag  = $request->getParameter('tag');
		TagService::getInstance()->removeTag($doc, $tag);
		$this->logAction($doc, array('tag' => $tag));
	
		return self::getSuccessView();
	}

	/**
	 * @return Boolean
	 */
	protected function isDocumentAction()
	{
		return true;
	}
}