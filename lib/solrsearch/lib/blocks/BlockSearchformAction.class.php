<?php
class solrsearch_BlockSearchformAction extends website_BlockAction
{
	private $delegateInstance;
	private $delegateReflectionClass;

	/**
	 * @see website_BlockAction::execute()
	 *
	 * @param website_BlockActionRequest $request
	 * @param website_BlockActionResponse $response
	 * @return String
	 */
	public function execute($request, $response)
	{
		$this->initDelegateInstance();
		$request->setAttribute('defaultRows', 10);
		$request->setAttribute('defaultField', 'score');

		$globalRequest = Controller::getInstance()->getContext()->getRequest();
		if ($globalRequest->hasModuleParameter($this->getParameterModule(), "terms"))
		{
			$request->setAttribute("terms", htmlspecialchars($globalRequest->getModuleParameter($this->getParameterModule(), "terms")));
		}
		
		$detailPage = $this->getResultPage();
		if ($detailPage != null)
		{
			$request->setAttribute('formAction', htmlentities(LinkHelper::getDocumentUrl($detailPage)));
		}
		$request->setAttribute('termsParameterName', $this->getTermsParameterName());
		return block_BlockView::SUCCESS;
	}

	/**
	 * @return void
	 */
	private function initDelegateInstance()
	{
		$delegateClassName = Framework::getConfiguration('modules/solrsearch/searchFormDelegate', false);
		if ($delegateClassName !== false)
		{
			$this->setDelegate($delegateClassName);
		}
	}

	/**
	 * @return String
	 */
	private function getTermsParameterName()
	{
		return $this->getParameterModule() . 'Param[terms]';
	}

	/**
	 * @return String
	 */
	private function getParameterModule()
	{
		if ($this->delegateInstance != null && $this->delegateReflectionClass->hasMethod("getParameterModule"))
		{
			return $this->delegateInstance->getParameterModule();
		}
		return 'solrsearch';
	}

	/**
	 * @return website_persistentdocument_page
	 */
	private function getResultPage()
	{
		if ($this->delegateInstance != null && $this->delegateReflectionClass->hasMethod("getResultPage"))
		{
			return $this->delegateInstance->getResultPage();
		}

		try
		{
			$currentWebsite = website_WebsiteModuleService::getInstance()->getCurrentWebsite();
			return TagService::getInstance()->getDocumentByContextualTag('contextual_website_website_modules_solrsearch_page-results', $currentWebsite);
		}
		catch (TagException $e)
		{
			Framework::exception($e);
			return null;
		}
	}


	/**
	 * @param String $className
	 */
	public function setDelegate($className)
	{
		if (f_util_ClassUtils::classExists($className))
		{
			$this->delegateInstance = new $className();
			$this->delegateReflectionClass = new ReflectionClass($className);
		}
		else
		{
			if (Framework::isWarnEnabled())
			{
				Framework::warn(__METHOD__ . ": specified delegate class $className does not exist!");
			}
		}
	}
}