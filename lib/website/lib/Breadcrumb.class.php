<?php
/**
 * @deprecated
 */
class Breadcrumb extends NavigationElementImpl
{

	private $xhtmlSeparator = ' &gt; ';
	private $textSeparator = ' > ';


	/**
	 * @deprecated
	 */
	public function setHtmlSeparator($separator)
	{
	    $this->xhtmlSeparator = $separator;
	    return $this;
	}


	/**
	 * @deprecated
	 */
	public function setTextSeparator($separator)
	{
	    $this->textSeparator = $separator;
	    return $this;
	}

	/**
	 * @deprecated
	 */
	public function renderAsXhtml()
	{
		$parts = array();
		foreach ($this as $entry)
		{
			$parts[] = $this->buildA($entry, '');
		}
		return join($this->xhtmlSeparator, $parts);
	}

	/**
	 * @deprecated
	 */
	public function renderAsText()
	{
		$parts = array();
		foreach ($this as $entry)
		{
			$parts[] = $entry->getLabel();
		}
		return join($this->textSeparator, $parts);
	}

	/**
	 * @deprecated
	 */
	public function renderAsJavascript()
	{
		$jsPagePath = array();
		$jsPagePath[] = sprintf(
			'"%s"',	addslashes(f_Locale::translate('&modules.website.frontoffice.thread.Homepage-href-name;'))
			);
		foreach ($this as $entry)
		{
			$jsPagePath[] = sprintf('"%s"', addslashes($entry->getLabel()));
		}
		return sprintf('[%s]', implode(',', $jsPagePath));
	}
}