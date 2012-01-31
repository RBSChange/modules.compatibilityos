<?php
/**
 * @deprecated
 */
class PHPTAL_Php_Attribute_CHANGE_currentlink extends PHPTAL_Php_Attribute
{
	public function start()
	{
		$tagName = strtolower($this->tag->name);
		$attrName = null;
		if ($tagName == 'form')
		{
			$attrName = 'action';
		}
		else
		{
			$attrName = 'href';
		}
		
		$extraAttributes = array();

		if (!f_util_StringUtils::isEmpty($this->expression))
		{	
			$expressions = $this->tag->generator->splitExpression($this->expression);
			foreach ($expressions as $exp)
			{
				list($parameterName, $value) = $this->parseSetExpression($exp);
				$extraAttributes[] = "'" . $parameterName ."' => " . $this->evaluate($value);
			}
		}

		$this->tag->attributes[$attrName] = $this->getHrefCode("array(".join(", ", $extraAttributes).")");
		$this->tag->attributes['class'] = 'link';
	}

	public function end()
	{
	}

	public function getHrefCode($extraAttributes)
	{
		return '<?php echo LinkHelper::getCurrentUrl('. $extraAttributes .'); ?>';
	}
}