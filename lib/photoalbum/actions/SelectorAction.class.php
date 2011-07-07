<?php
/**
 * @package modules.photoalbum
 */
class photoalbum_SelectorAction extends f_action_BaseAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$album = $this->getDocumentInstanceFromRequest($request);
		$pageSize = intval($request->getParameter('pagesize'));
		if ($pageSize > 0)
        {
            $album->setPageSize($pageSize);
        }
		
	    $currentPageIndex = intval($request->getParameter('currentpageindex', 0));
        $album->setCurrentPageIndex($currentPageIndex);
		$request->setAttribute('item', $album);
		return View::SUCCESS;
	}	
	
	/**
	 * @return boolean
	 */
	public function isSecure()
	{
		return false;
	}	
}