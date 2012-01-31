<?php
class PHPTAL_Php_Attribute_CHANGE_menuitem extends PHPTAL_Php_Attribute_TAL_Repeat
{
	private $separator;
	
    public function start()
    {
		$this->item = '$ctx->item';

		// reset item var into template context
		$this->tag->generator->doIf('!isset('.$this->item.')');
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
			list($attribute, $value) = $this->parseSetExpression($exp);
			//echo $attribute."\n";
			if ($attribute == "separator")
			{
				$this->separator = $this->evaluate($value);
			}
        }
    }

    public function end()
    {
		$this->tag->generator->pushCode('echo PHPTAL_Php_Attribute_CHANGE_menuitem::render('.$this->item.', '.var_export($this->tag->attributes, true).', '.self::REPEAT.'->item, trim(ob_get_clean()), '.$this->separator.')');
    }

    public static function render($menuItem, $attributes, $controller, $content, $separator = null)
    {
    	foreach ($attributes as $name => $value)
    	{
    		$attributes[$name] = str_replace(array('%id', '%level', '%index'), array($controller->itemId, $controller->currentLevel, $controller->index), $value);
    	}
    	if ($content)
    	{
    		$menuItem->setLabel($content);
    	}
    	if ($controller->isCurrent)
    	{
    		$html = "<strong class=\"current\">".$menuItem->getLabel()."</strong>";
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
    		$html .= "<span>".$separator."</span>";
    	}
    	
    	return $html;
    }
}
