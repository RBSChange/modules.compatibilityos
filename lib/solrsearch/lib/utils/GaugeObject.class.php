<?php
/**
 * @deprecated
 */
class GaugeObject extends solrsearch_GaugeObject
{
	/**
	 * @deprecated
	 * @param float $val
	 * @return solrsearch_GaugeObject
	 */
	public static function getNewInstance($val)
	{
		$instance = new GaugeObject($val);
		$instance->setGauge($val);
		return $instance;
	}
	
	public function increment()
	{
		$this->gauge++;
	}
}

/**
 * @deprecated
 */
class solrsearch_GaugeObject
{
	private $gauge = 0;
	
	private static $imageBaseName;
	private static $imageExtension;
	private static $maxScore;
	
	/**
	 * @deprecated
	 */
	public function __construct($value)
	{
		if (self::$imageBaseName == null)
		{
			$delegateClassName = Framework::getConfigurationValue('modules/solrsearch/gaugeDelegate');
			if ($delegateClassName && f_util_ClassUtils::classExists($delegateClassName))
			{
				self::setDelegate($delegateClassName);
			} 
			
			if (self::$imageBaseName == null)
			{
				self::$imageBaseName = 'solrsearch-results-';
			}
			
			if (self::$imageExtension == null)
			{
				self::$imageExtension = 'png';
			}
			
			if (self::$maxScore == null)
			{
				self::$maxScore = 5;
			}
		}
		$this->gauge = round(self::$maxScore * $value);
	}
	
	/**
	 * @deprecated
	 */
	public function setGauge($value)
	{
		$this->gauge = $value;
	}
	
	/**
	 * @deprecated
	 */
	public static function setDelegate($className)
	{
		$classInstance = new $className();
		$reflectionClass = new ReflectionClass($className);
		
		if ($reflectionClass->hasMethod("getGaugeImageBaseName"))
		{
			self::$imageBaseName = $classInstance->getGaugeImageBaseName();
		}
		
		if ($reflectionClass->hasMethod("getGaugeImageExtension"))
		{
			self::$imageExtension = $classInstance->getGaugeImageExtension();
		}
		
		if ($reflectionClass->hasMethod("getGaugeMaxScore"))
		{
			self::$maxScore = $classInstance->getGaugeMaxScore();
		}
	}
	
	/**
	 * @deprecated
	 */
	public function getImageUrl()
	{
		return htmlentities(MediaHelper::getFrontofficeStaticUrl(self::$imageBaseName . strval($this->gauge)) . '.' . self::$imageExtension);
	}
	
	/**
	 * @deprecated
	 */
	public function getAltText()
	{
		return f_Locale::translate('&modules.solrsearch.frontoffice.Solrsearch-results-' . strval($this->gauge) . ";");
	}
	
	/**
	 * @deprecated
	 */
	public function getClassName()
	{
		return 'solrsearch-results-' . strval($this->gauge);
	}
}