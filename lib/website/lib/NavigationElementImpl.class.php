<?php
/**
 * @deprecated
 */
abstract class NavigationElementImpl extends ArrayObject implements NavigationElement
{
	const CSSCLASS_LEVEL_ACCESSOR = '%level';
	const CSSCLASS_UL        = 1;
	const CSSCLASS_LI_PAGE   = 2;
	const CSSCLASS_LI_FOLDER = 3;
	const CSSCLASS_A_PAGE    = 4;
	const CSSCLASS_A_FOLDER  = 5;

	// CSS classes for generated ul/li/a elements.
	protected $levelEntryClass  = null;   // <ul> elements
	protected $pageEntryClass   = null;   // <li> elements for pages
	protected $folderEntryClass = null;   // <li> elements for folders
	protected $pageLinkClass    = 'link'; // <a> elements for links to pages
	protected $folderLinkClass  = 'link'; // <a> elements for links to folders

	protected $xhtmlContents    = null;
	protected $restrictedIds    = array();

	protected $xhtmlCRLF        = '';
	protected $textCRLF         = "\n";

	protected $maxLevel = 2;

	/**
	 * @deprecated
	 */
	public function __toString()
	{
		return $this->renderAsText();
	}

	/**
	 * @deprecated
	 */
	public final function setMaxLevel($maxLevel)
	{
		$this->maxLevel = intval($maxLevel);
	}

	/**
	 * @deprecated
	 */
	public final function getMaxLevel()
	{
		return $this->maxLevel;
	}

	/**
	 * @deprecated
	 */
	protected function getCssClass($cssClass, $level)
	{
		if (strpos($cssClass, self::CSSCLASS_LEVEL_ACCESSOR) !== false)
		{
			$cssClass = str_replace(self::CSSCLASS_LEVEL_ACCESSOR, strval($level+1), $cssClass);
		}
		return $cssClass;
	}

	/**
	 * @deprecated
	 */
	protected function beginElement($tagName, $className, $level, $attributes = null)
	{
		$xhtml = '<'.$tagName;
		if (!is_null($className))
		{
			$xhtml .= ' class="'.$this->getCssClass($className, $level).'"';
		}
		if (is_array($attributes))
		{
			foreach ($attributes as $name => $value)
			{
				if (!empty($value)) { $xhtml .= ' '.$name.'="'.$value.'"'; }
			}
		}
		$xhtml .= '>';
		return $xhtml;
	}

	/**
	 * @deprecated
	 */
	protected function endElement($tagName)
	{
		return '</'.$tagName.'>';
	}

	/**
	 * @deprecated
	 */
	protected function beginUL(website_MenuItem $entry)
	{
		return $this->beginElement('ul', $this->levelEntryClass, $entry->getLevel());
	}

	/**
	 * @deprecated
	 */
	protected function endUL()
	{
		return $this->endElement('ul');
	}

	/**
	 * @deprecated
	 */
	protected function beginLI(website_MenuItem $entry)
	{
		$cssClass = WebsiteHelper::isPage($entry) ? $this->pageEntryClass : $this->folderEntryClass;
		return $this->beginElement('li', $cssClass, $entry->getLevel());
	}

	/**
	 * @deprecated
	 */
	protected function endLI()
	{
		return $this->endElement('li');
	}

	/**
	 * @deprecated
	 */
	protected function buildA(website_MenuItem $entry, $anchor)
	{
		$cssClass = WebsiteHelper::isPage($entry) ? $this->pageLinkClass : $this->folderLinkClass;
		return $this->beginElement('a', $cssClass, $entry->getLevel(), array('href' => $entry->getUrl(), 'name' => $anchor)) . $entry->getLabel() . $this->endElement('a');
	}

	/**
	 * @deprecated
	 */
	protected function isRestricted(website_MenuItem $entry)
	{
		return in_array($entry->getId(), $this->restrictedIds);
	}
	
	/**
	 * @deprecated
	 */
	public function setCssClass($element, $cssClassName, $appendLevelAccessor = false)
	{
		if (!preg_match('/^[a-z0-9_\-%]+$/i', $cssClassName))
		{
			throw new IllegalArgumentException('cssClassName', 'valid CSS class name');
		}
		if ($appendLevelAccessor)
		{
			$cssClassName .= self::CSSCLASS_LEVEL_ACCESSOR;
		}
		switch ($element)
		{
			case self::CSSCLASS_UL :
				$this->levelEntryClass = $cssClassName;
				break;
			case self::CSSCLASS_LI_PAGE :
				$this->pageEntryClass = $cssClassName;
				break;
			case self::CSSCLASS_LI_FOLDER :
				$this->folderEntryClass = $cssClassName;
				break;
			case self::CSSCLASS_A_PAGE :
				$this->pageLinkClass = $cssClassName;
				break;
			case self::CSSCLASS_A_FOLDER :
				$this->folderLinkClass = $cssClassName;
				break;
			default :
				throw new IllegalArgumentException('type', 'integer from '.CSSCLASS_UL.' to '.CSSCLASS_A_FOLDER);
		}
	}
}