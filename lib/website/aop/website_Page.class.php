<?php
class compatibilityos_website_Page extends website_Page
{
	/**
	 * @deprecated use block meta mechanism instead and implement getMeta() on your block
	 */
	public function setKeywords($string)
	{
		$this->setAttribute('keywords', $string);
	}

	/**
	 * @deprecated use block meta mechanism instead and implement getMeta() on your block
	 */
	public function setDescription($string)
	{
		$this->setAttribute('description', $string);
	}

	/**
	 * @deprecated use block meta mechanism instead and implement getMeta() on your block
	 */
	public function setNavigationtitle($string)
	{
		$this->page->setNavigationtitle($string);
	}
	
	/**
	 * @deprecated use block meta mechanism instead and implement getMeta() on your block
	 */
	public function appendToDescription($string)
	{
		if (isset($this->hasAttribute('description')))
		{
			$this->setAttribute('description', $this->getAttribute('description') . ' ' . $string);;
		}
		else
		{
			$this->setAttribute('description', $string);
		}
	}
	
	/**
	 * @deprecated in favor to setTitle()
	 */
	public function setMetatitle($string)
	{
		$this->setTitle($string);
	}

	/**
	 * @deprecated use block meta mechanism instead and implement getMeta() on your block
	 */
	public function setTitle($string)
	{
		$this->setAttribute('title', $string);
	}
	
	/**
	 * @deprecated use block meta mechanism instead and implement getMeta() on your block
	 */
	public function addKeyword($string)
	{
		if (isset($this->hasAttribute('keywords')))
		{
			$this->setAttribute('keywords', $this->getAttribute('keywords') . ', ' . $string);
		}
		else
		{
			$this->setAttribute('keywords', $string);
		}
	}

	/**
	 * @deprecated use getPersistentPage
	 */
	public function getPageDocument()
	{
		return $this->getPersistentPage();
	}

	/**
	 * @deprecated
	 */
	public function inBackofficeMode()
	{
		return $this->getAttribute(website_BlockAction::BLOCK_BO_MODE_ATTRIBUTE, false);
	}

	/**
	 * @deprecated use getAncestorIds
	 */
	public function getAncestors()
	{
		return $this->getAncestorIds();
	}

	/**
	 * @deprecated
	 */
	public function getNearestContainerId()
	{
		// if ancestors size is 2 then you retrieve the website !
		return f_util_ArrayUtils::lastElement($this->getAncestorIds());
	}

	/**
	 * @deprecated
	 */
	public function getGlobalRequest()
	{
		return HttpController::getInstance()->getContext()->getRequest();
	}

	/**
	 * @deprecated
	 */
	public function getGlobalContext()
	{
		return HttpController::getInstance()->getContext();
	}

	/**
	 * @deprecated
	 */
	public function inIndexingMode()
	{
		return false;
	}

	/**
	 * @deprecated use getSessionUser
	 */
	public function getUser()
	{
		return $this->getSessionUser();
	}
}