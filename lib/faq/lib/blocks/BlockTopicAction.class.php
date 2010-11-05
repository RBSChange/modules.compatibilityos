<?php
/**
 * @deprecated use faq_BlockQuestionListAction with the 'container' parameter
 */
class faq_BlockTopicAction extends abstractdirectory_BlockTopicAction
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
	}
}