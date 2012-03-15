<?php

/**
 * @deprecated
 */
class website_MenuItem
{
	private $id;
	private $label;
	private $description;
	private $documentModelName;
	private $url;
	private $visibility = 3;
	private $level = 0;
	private $popup = false;
	private $popupParameters = array();
	private $onClick;
	private $type;

	const TYPE_TOPIC = 1;
	const TYPE_PAGE = 2;

	/**
	 * @deprecated
	 */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

	/**
	 * @deprecated
	 */
    public function getId()
    {
        return $this->id;
    }

	/**
	 * @deprecated
	 */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

	/**
	 * @deprecated
	 */
    public function isTopic()
    {
        return $this->type === self::TYPE_TOPIC;
    }

	/**
	 * @deprecated
	 */
    public function isPage()
    {
        return $this->type === self::TYPE_PAGE;
    }

	/**
	 * @deprecated
	 */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

	/**
	 * @deprecated
	 */
    public function getLabel()
    {
        return $this->label;
    }

	/**
	 * @deprecated
	 */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

	/**
	 * @deprecated
	 */
    public function getDescription()
    {
        return $this->description;
    }

	/**
	 * @deprecated
	 */
    public function setDocumentModelName($documentModelName)
    {
        $this->documentModelName = $documentModelName;
        return $this;
    }

	/**
	 * @deprecated
	 */
    public function getDocumentModelName()
    {
        return $this->documentModelName;
    }

	/**
	 * @deprecated
	 */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

	/**
	 * @deprecated
	 */
    public function hasUrl()
    {
        return $this->url !== null;
    }

	/**
	 * @deprecated
	 */
    public function getUrl()
    {
        return $this->url !== null ? $this->url : website_WebsiteModuleService::EMPTY_URL;
    }

	/**
	 * @deprecated
	 */
    public function setOnClick($onClick)
    {
        $this->onClick = $onClick;
        return $this;
    }

	/**
	 * @deprecated
	 */
    public function getOnClick()
    {
        return $this->onClick;
    }

	/**
	 * @deprecated
	 */
    public function setLevel($level)
    {
        $this->level = $level;
        return $this;
    }

	/**
	 * @deprecated
	 */
    public function getLevel()
    {
        return $this->level;
    }

	/**
	 * @deprecated
	 */
    public function setNavigationVisibility($visibility)
    {
        $this->visibility = $visibility;
        return $this;
    }

	/**
	 * @deprecated
	 */
    public function getNavigationVisibility()
    {
        return $this->visibility;
    }

	/**
	 * @deprecated
	 */
    public function getPopup()
    {
    	return $this->popup;
    }

	/**
	 * @deprecated
	 */
    public function setPopup($bool)
    {
    	$this->popup = (bool)$bool;
    	return $this;
    }

	/**
	 * @deprecated
	 */
    public function getPopupParameters()
    {
    	if ( ! isset($this->popupParameters['width']) || ! $this->popupParameters['width'])
    	{
    		$this->popupParameters['width'] = null;
    	}
    	if ( ! isset($this->popupParameters['height']) || ! $this->popupParameters['height'])
    	{
    		$this->popupParameters['height'] = null;
    	}
    	return $this->popupParameters;
    }

	/**
	 * @deprecated
	 */
    public function getPopupWidth()
    {
    	return isset($this->popupParameters['width']) ? intval($this->popupParameters['width']) : null;
    }

	/**
	 * @deprecated
	 */
    public function getPopupHeight()
    {
    	return isset($this->popupParameters['height']) ? intval($this->popupParameters['height']) : null;
    }

	/**
	 * @deprecated
	 */
    public function setPopupParameters($parameters)
    {
    	if ( is_array($parameters) )
    	{
    		$this->popupParameters = $parameters;
    	}
    	else if ( is_string($parameters) && strlen($parameters) > 0 )
    	{
    		$this->popupParameters = array();
    		$paramArray = explode(',', $parameters);
    		foreach ($paramArray as $p)
    		{
    			list($n, $v) = explode(':', $p);
    			$this->popupParameters[trim($n)] = trim($v);
    		}
    	}
    	return $this;
    }
}

/**
 * @deprecated
 */
class TopicEntry extends website_MenuItem
{
}
