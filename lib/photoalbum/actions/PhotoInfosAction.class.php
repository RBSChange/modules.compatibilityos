<?php
/**
 * @package modules.photoalbum
 */
class photoalbum_PhotoInfosAction extends f_action_BaseAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$photo = $this->getDocumentInstanceFromRequest($request);
		$album = $photo->getAlbum();
		$album->setPageSize($request->getParameter('pagesize'));
		$album->setCurrentPhotoId($photo->getId());
		$request->setAttribute('album', $album);
		
		$currentPageId = $request->getParameter("currentPageId");
		$currentURL = $request->getParameter("currentURL");
		if (f_util_StringUtils::isNotEmpty($currentPageId) && f_util_StringUtils::isNotEmpty($currentURL))
		{
			website_WebsiteModuleService::getInstance()->setCurrentPageId($currentPageId);
			RequestContext::getInstance()->setAjaxMode(true, $currentURL);	
		}
		else
		{
			throw new Exception("Invalid request: missing currentPageId or currentURL parameters");
		}
		
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