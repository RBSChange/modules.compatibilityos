<?php
/**
 * @deprecated use faq_BlockQuestionListAction
 */
class faq_BlockContextuallistAction extends abstractdirectory_BlockContextuallistAction
{
	/**
	 * @deprecated
	 */
	public function initialize($context, $request)
	{
		parent::initialize($context, $request);
		$this->setModuleName('faq');
		$this->setComponentName('faq');
	}
	
	/**
	 * @deprecated
	 */
	protected function setAdditionalParameters()
	{
		// Get the page with the tag for the form
		$ws = website_WebsiteModuleService::getInstance();
		try 
		{
			$formPage = $ws->getDocumentByContextualTag('contextual_website_website_form', $ws->getCurrentWebsite());
			$this->setParameter('formPage', $formPage);
		}
		catch (TagException $e)
		{
			Framework::exception($e);
		}
		$this->setParameter('msgKeyForEmptyFolder', 'modules.faq.frontoffice.Empty-container');
	}
	
	/**
	 * @deprecated
	 */
	protected function getItemPerPage()
	{
		$nbItemPerPage = $this->getParameter('nbItemsPerPage');
		if ($nbItemPerPage < 1)
		{
			$nbItemPerPage = 10;
		}
		return $nbItemPerPage;
	}
}