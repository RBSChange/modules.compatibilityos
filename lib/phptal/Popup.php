<?php
/**
 * @deprecated (will be removed in 4.0) use change:link="...; pupup" if you realy need a popup. 
 */
class PHPTAL_Php_Attribute_CHANGE_popup extends PHPTAL_Php_Attribute
{
	private $pageId;
	private $lang;
	private $documentId;

	/**
	 * @deprecated
	 */
	public function start()
	{
		$popupParameters = self::parsePopupArg($this->expression);

		if ( ! empty($popupParameters) )
		{
			$this->tag->attributes['onclick'] = self::getOnClick($popupParameters);
		}
	}

	/**
	 * @deprecated
	 */
	public function end()
	{
	}

	/**
	 * @deprecated
	 */
	public static function parsePopupArg($value)
	{
		$popupParameters = array();
		foreach (explode(',', $value) as $param)
		{
			list($pName, $pValue) = explode(':', $param);
			$popupParameters[strtolower(trim($pName))] = trim($pValue);
		}
		return $popupParameters;
	}

	/**
	 * @deprecated
	 */
	public static function getOnClick($popupParameters)
	{
		$onClick = 'return accessiblePopup(this';
		if (isset($popupParameters['width']) && is_numeric($popupParameters['width']))
		{
			$onClick .= ', '.$popupParameters['width'];
		}
		if (isset($popupParameters['height']) && is_numeric($popupParameters['height']))
		{
			$onClick .= ', '.$popupParameters['height'];
		}
		$onClick .= ');';
		return $onClick;
	}
}