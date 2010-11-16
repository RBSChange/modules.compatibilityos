<?php
/**
 * generic_AddTagsAction
 * This class is used to execute the AddTags action that is able to add
 * tags to multiple documents at once.
 */
class generic_AddTagAction extends f_action_BaseAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$doc = $this->getDocumentInstanceFromRequest($request);
		$tag   = $request->getParameter('tag');
		TagService::getInstance()->addTag($doc, $tag);
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

	/**
	 * @return integer[]
	 */
	protected function getSecureNodeIds()
	{
		$request = $this->getContext()->getRequest();

		$docIds = $this->getDocumentIdArrayFromRequest($request);
		$tag   = $request->getParameter('tag');

		if (f_util_ArrayUtils::isNotEmpty($docIds) && f_util_StringUtils::isNotEmpty($tag))
		{
			$ts = TagService::getInstance();

			if ($ts->isExclusiveTag($tag))
			{
				$oldDocument = $ts->getDocumentByExclusiveTag($tag, false);
				if ($oldDocument !== null && !in_array($oldDocument->getId(), $docIds))
					{
						$docIds[] = $oldDocument->getId();
					}
				}
			else if ($ts->isContextualTag($tag))
			{
				foreach ($docIds as $docId)
				{
							$contextualDocument = DocumentHelper::getDocumentInstance($ts->getContextualDocumentIdByTag($docId, $tag));
					$oldDocument = $ts->getDocumentByContextualTag($tag, $contextualDocument, false);
					if ($oldDocument !== null && !in_array($oldDocument->getId(), $docIds))
							{
								$docIds[] = $oldDocument->getId();
							}
						}
					}
				}
		return $docIds;
	}
}