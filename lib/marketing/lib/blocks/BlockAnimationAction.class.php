<?php
/**
 * @deprecated use marketing_BlockAnimationListAction
 */
class marketing_BlockAnimationAction extends website_BlockAction
{
	/**
	 * @deprecated
	 */
	function execute($request, $response)
	{
		$animation = $this->getAnimation();
		$products = $animation->getDisplayableProductArray();
		if (!$animation->isPublished() || count($products) === 0 )
		{
			return website_BlockView::NONE;
		}
		
		$templateName = $animation->getTemplate();
		if ($this->getUsetypetitle($request))
		{
			$animation->setLabel(f_Locale::translate('&modules.marketing.frontoffice.Animation-' . $templateName . '-title;'));
		}
		
		if ($this->getAddrssfeed($request))
		{
			$this->getPage()->addRssFeed($animation->getLabel(), $animation->getRSSFeedURL());
		}
		
		$request->setAttribute('animation', $animation);
		$request->setAttribute('customer', customer_CustomerService::getInstance()->getCurrentCustomer());
		$displaytype = $this->getDisplaytype($request);
		$request->setAttribute('displaytype', $displaytype);
		
		$packageName = 'modules_marketing';
		$subDirectory = 'animations' . DIRECTORY_SEPARATOR . $displaytype;
		return $this->getTemplateByFullName($packageName, $templateName, $subDirectory);
	}
	
	/**
	 * @deprecated
	 */
	private function getUsetypetitle($request)
	{
		return $request->getParameter('usetypetitle', $this->getConfiguration()->getUsetypetitle());
	}
	
	/**
	 * @deprecated
	 */
	private function getAddrssfeed($request)
	{
		return $request->getParameter('addrssfeed', $this->getConfiguration()->getAddrssfeed());
	}
	
	/**
	 * @deprecated
	 */
	private function getDisplaytype($request)
	{
		return $request->getParameter('displaytype', $this->getConfiguration()->getDisplaytype());
	}
	
	/**
	 * @deprecated
	 */
	private function getAnimation()
	{
		return $this->getRequiredDocumentParameter('cmpref', 'marketing_persistentdocument_animation');
	}
}