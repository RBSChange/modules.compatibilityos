<?php
class PHPTAL_Php_Attribute_CHANGE_menuitem extends PHPTAL_Php_Attribute_TAL_Repeat
{
	private $separator;
	
	public function start()
	{
		$this->item = '$ctx->item';
		
		// reset item var into template context
		$this->tag->generator->doIf('!isset(' . $this->item . ')');
		$this->tag->generator->doSetVar($this->item, 'false');
		$this->tag->generator->doEnd();
		
		// the following line makes the tag not visible, just like if it
		// contains a tal:omit-tag="".
		$this->tag->headFootDisabled = true;
		
		$this->tag->generator->pushCode('ob_start()');
		$g = $this->tag->generator;
		$this->separator = 'null';
		foreach ($g->splitExpression($this->expression) as $exp)
		{
			list ($attribute, $value) = $this->parseSetExpression($exp);
			//echo $attribute."\n";
			if ($attribute == "separator")
			{
				$this->separator = $this->evaluate($value);
			}
		}
	}
	
	public function end()
	{
		$this->tag->generator->pushCode('echo PHPTAL_Php_Attribute_CHANGE_menuitem::render(' . $this->item . ', ' . var_export($this->tag->attributes, true) . ', ' . self::REPEAT . '->item, trim(ob_get_clean()), ' . $this->separator . ')');
	}
	
	public static function render($menuItem, $attributes, $controller, $content, $separator = null)
	{
		foreach ($attributes as $name => $value)
		{
			$attributes[$name] = str_replace(array('%id', '%level', '%index'), array($controller->itemId, $controller->currentLevel, 
				$controller->index), $value);
		}
		if ($content)
		{
			$menuItem->setLabel($content);
		}
		if ($controller->isCurrent)
		{
			$html = "<strong class=\"current\">" . $menuItem->getLabel() . "</strong>";
		}
		else if ($menuItem->hasUrl())
		{
			$onClick = $menuItem->getOnClick();
			if ($onClick)
			{
				$attributes['onclick'] = $onClick;
			}
			if ($menuItem->getPopup())
			{
				$param = $menuItem->getPopupParameters();
				$html = LinkHelper::getPopupLink($menuItem, null, 'link', '', $attributes, $param['width'], $param['height']);
			}
			else
			{
				$html = LinkHelper::getLink($menuItem, null, 'link', '', $attributes);
			}
		}
		else
		{
			$html = $menuItem->getLabel();
		}
		
		if ($separator != "" && !$controller->end)
		{
			$html .= "<span>" . $separator . "</span>";
		}
		
		return $html;
	}
	
	/**
     * Build a full link (<a/> element) for the given document.
     *
     * @param f_persistentdocument_PersistentDocument $document
     * @param string $lang
     * @param string $class
     * @param string $title
     * @param array<string=>string> $attributes
     * @return string
     */
	public static function getLink($document, $lang = null, $class = 'link', $title = '', $attributes = null)
	{
		return self::_buildLink($document, $lang, $class, $title, false, $attributes);
	}
	
	/**
     * Build a full POPUP link (<a/> element) for the given document.
     *
     * @param f_persistentdocument_PersistentDocument $document
     * @param string $lang
     * @param string $class
     * @param string $title
     * @return string
     */
	public static function getPopupLink($document, $lang = null, $class = 'link', $title = '', $attributes = null, $width = null, $height = null)
	{
		if (!is_null($width) || !is_null($height))
		{
			$popup = array();
			if (!is_null($width)) $popup['width'] = $width;
			if (!is_null($height)) $popup['height'] = $height;
		}
		else
		{
			$popup = true;
		}
		return self::_buildLink($document, $lang, $class, $title, $popup, $attributes);
	}
	
	/**
     * Build a full link (<a/> element) for the given document.
     *
     * @param f_persistentdocument_PersistentDocument $document
     * @param string $lang
     * @param string $class
     * @param string $title
     * @param boolean $popup
     * @param array<string=>string> $attributes
     * @return string
     */
	public static function _buildLink($document, $lang = null, $class = 'link', $title = '', $popup = false, $attributes = null)
	{
		if (is_null($lang))
		{
			$lang = RequestContext::getInstance()->getLang();
		}
		
		if (f_util_ClassUtils::methodExists($document, 'getUrl'))
		{
			$url = $document->getUrl();
		}
		else
		{
			$url = LinkHelper::getDocumentUrl($document, $lang);
		}
		
		if (f_util_ClassUtils::methodExists($document, 'getNavigationtitle'))
		{
			$label = $document->getNavigationtitle();
		}
		else if (f_util_ClassUtils::methodExists($document, 'getLabelAsHtml'))
		{
			$label = $document->getLabelAsHtml();
		}
		else
		{
			$label = $document->getLabel();
		}
		
		if (!empty($title))
		{
			$title = ' title="' . substr($title, 0, 80) . '"';
		}
		
		if ($popup !== false)
		{
			$onclick = ' onclick="return accessiblePopup(this';
			if (is_array($popup))
			{
				if (isset($popup['width']) && is_numeric($popup['width']))
				{
					$onclick .= ',' . $popup['width'];
					if (isset($popup['height']) && is_numeric($popup['height']))
					{
						$onclick .= ',' . $popup['height'];
					}
				}
				else if (isset($popup['height']) && is_numeric($popup['height']))
				{
					$onclick .= ',null,' . $popup['height'];
				}
			
			}
			$onclick .= ')"';
			if (is_array($attributes))
			{
				if (isset($attributes['class']))
				{
					$attributes['class'] .= ' popup';
				}
				else
				{
					$attributes['class'] = 'popup';
				}
			}
			else
			{
				$attributes = array('class' => 'popup');
			}
		}
		else
		{
			$onclick = '';
		}
		
		$attributesString = '';
		if (is_array($attributes))
		{
			foreach ($attributes as $name => $value)
			{
				if ($name == 'class')
				{
					$class .= ' ' . $value;
				}
				else if ($name != 'lang' && $name != 'href')
				{
					$attributesString .= " $name=\"" . addcslashes($value, '"') . "\"";
				}
			}
		}
		
		$html = '<a href="' . $url . '" lang="' . $lang . '" xml:lang="' . $lang . '"';
		if ($class !== null)
		{
			$html .= ' class="' . $class . '"';
		}
		$html .= $title . $onclick . $attributesString;
		$html .= '>' . f_Locale::translate($label, null, $lang) . '</a>';
		return $html;
	}
}
