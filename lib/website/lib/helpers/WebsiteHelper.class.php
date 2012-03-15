<?php
/**
 * @deprecated
 */
abstract class WebsiteHelper
{
	private static $folderDocuments = array('modules_website/topic', 'modules_website/systemtopic', 'modules_website/website');

	/**
	 * @deprecated
	 */
	public static function isVisible($item)
	{
		return self::isVisibleInMenu($item);
	}
		
	/**
	 * @deprecated
	 */
	public static function isVisibleInMenu($item)
	{
		if ($item->isPublished())
		{
			if ($item instanceof website_persistentdocument_menuitem)
			{
				$itemVisibility = $item->getNavigationVisibility();
			}
			else if ($item instanceof website_PublishableElement)
			{
				$itemVisibility = $item->getNavigationVisibility();
			}
			else
			{
				$itemVisibility = WebsiteConstants::VISIBILITY_HIDDEN;
			}
			
			return (WebsiteConstants::VISIBILITY_VISIBLE == $itemVisibility || 
					WebsiteConstants::VISIBILITY_HIDDEN_IN_SITEMAP_ONLY == $itemVisibility);
		}
		return false;
	}

	/**
	 * @deprecated
	 */
	public static function isVisibleInSitemap($item)
	{
		if ($item instanceof website_PublishableElement && $item->isPublished())
		{
			$itemVisibility = $item->getNavigationVisibility();
			return WebsiteConstants::VISIBILITY_VISIBLE == $itemVisibility || 
					WebsiteConstants::VISIBILITY_HIDDEN_IN_MENU_ONLY == $itemVisibility;
		}
		return false;		
	}


	/**
	 * @deprecated
	 */
	public static function isHidden($item)
	{
		if ($item instanceof website_persistentdocument_menuitem)
		{
			return false;
		}
		
		$v = self::getVisibility($item);
		return $v == WebsiteConstants::VISIBILITY_HIDDEN;
	}


	/**
	 * @deprecated
	 */
	public static function isFolder($entry)
	{
		if (!method_exists($entry, 'getDocumentModelName'))
		{
			throw new IllegalArgumentException('$entry', 'Must be a valid Document or website_MenuItem.');
		}
		return in_array($entry->getDocumentModelName(), self::$folderDocuments);
	}

	/**
	 * @deprecated
	 */
	public static function isPage($entry)
	{
		return !self::isFolder($entry);
	}


	/**
	 * @deprecated
	 */
	public static function setFolderDocuments($folderDocuments)
	{
		if (!is_array($folderDocuments))
		{
			throw new IllegalArgumentException('folderDocuments Must be a valid array of document model names.');
		}
		self::$folderDocuments = $folderDocuments;
	}

	/**
	 * @deprecated
	 */
	private static function getVisibility($document)
	{
		if (!is_object($document) || !method_exists($document, 'getNavigationVisibility') )
		{
			return WebsiteConstants::VISIBILITY_HIDDEN;
		}
		return $document->getNavigationVisibility();
	}


	/**
	 * @deprecated
	 */
	public static function getCurrentPageAttributeForMenu()
	{
    	$ws = website_WebsiteModuleService::getInstance();
		$currentPageDoc = DocumentHelper::getDocumentInstance($ws->getCurrentPageId());
    	$currentPageAttr = array(
    		'id' => $ws->getCurrentPageId(),
    		'ancestors' => $ws->getCurrentPageAncestorsIds()
    		);

    	if ($currentPageDoc->getIsIndexPage())
    	{
    		$currentPageAttr['indexOf'] = $currentPageDoc->getDocumentService()->getParentOf($currentPageDoc)->getId();
    	}
    	return $currentPageAttr;
	}
}