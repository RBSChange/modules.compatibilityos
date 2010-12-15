<?php
/**
 * @package compatibilityos
 */
class compatibilityos_DocumentHelper
{
    /**
     * Key accessor for allowed component names.
     *
     */
    const ALLOWED_ACCESSOR = 'allowed';


    /**
     * Key accessor for disabled component names.
     *
     */
    const DISABLED_ACCESSOR = 'disabled';


    /**
     * Key accessor for suggested component names and values.
     *
     */
    const SUGGESTED_ACCESSOR = 'suggested';


    /**
     * Key accessor for translation option.
     *
     */
    const TRANSLATION_ACCESSOR = 'translation';

	
	/**
	 * @deprecated
	 */
	public static function toXml($document, $allowedComponentNames = null, $disabledComponentNames = null, $suggestedComponents = null)
	{
	    $model = $document->getPersistentModel();

	    if ($model->isLocalized() && ($document->getLang() != RequestContext::getInstance()->getLang()))
	    {
	        if (is_null($disabledComponentNames))
	        {
	            $disabledComponentNames = array();
	        }

	        $componentNames = $model->getPropertiesNames();

    	    foreach ($componentNames as $componentName)
    		{
    			$property = $model->getProperty($componentName);

    			if (!$property->isLocalized())
    			{
    			    $disabledComponentNames[] = $componentName;
    			}
    		}

        	$disabledComponentNames = array_unique($disabledComponentNames);
	    }

	    $options = array(
	        self::ALLOWED_ACCESSOR => $allowedComponentNames,
	        self::DISABLED_ACCESSOR => $disabledComponentNames,
	        self::SUGGESTED_ACCESSOR => $suggestedComponents,
	        self::TRANSLATION_ACCESSOR => false
	    );

	    return self::_toXml($document, 1, null, null, $options);
	}


	/**
	 * @deprecated
	 */
	public static function toXmlForTranslation($document, $localizedOnly = false, $allowedComponentNames = null, $disabledComponentNames = null, $suggestedComponents = null)
	{
	    $model = $document->getPersistentModel();

   	    $componentNames = $model->getPropertiesNames();

	    if ($localizedOnly)
	    {
	        if (is_null($allowedComponentNames))
    	    {
    	        $allowedComponentNames = array();
    	    }

    	    foreach ($componentNames as $componentName)
    		{
    			$property = $model->getProperty($componentName);

    			if ($property->isLocalized())
    			{
    			    $allowedComponentNames[] = $componentName;
    			}
    		}

    		$allowedComponentNames = array_unique($allowedComponentNames);
	    }

	    if (is_null($disabledComponentNames))
	    {
	        $disabledComponentNames = array();
	    }

	    foreach ($componentNames as $componentName)
		{
			$property = $model->getProperty($componentName);

			if (!$property->isLocalized())
			{
			    $disabledComponentNames[] = $componentName;
			}
		}

		$disabledComponentNames = array_unique($disabledComponentNames);

	    $options = array(
	        self::ALLOWED_ACCESSOR => $allowedComponentNames,
	        self::DISABLED_ACCESSOR => $disabledComponentNames,
	        self::SUGGESTED_ACCESSOR => $suggestedComponents,
	        self::TRANSLATION_ACCESSOR => true
	    );

	    return self::_toXml($document, 1, null, null, $options);
	}


	/**
	 * @deprecated
	 */
	private static function _toXml($document, $level = 1, $lang = null, $forceId = null, $options)
	{
	    if ($level < 0)
		{
			return '';
		}

		if (isset($options[self::ALLOWED_ACCESSOR]) && !is_null($options[self::ALLOWED_ACCESSOR]))
		{
		    $allowedComponentNames = $options[self::ALLOWED_ACCESSOR];
		}
		else
		{
		    $allowedComponentNames = null;
		}

		if (isset($options[self::DISABLED_ACCESSOR]) && !is_null($options[self::DISABLED_ACCESSOR]))
		{
		    $disabledComponentNames = $options[self::DISABLED_ACCESSOR];
		}
		else
		{
		    $disabledComponentNames = null;
		}

		if (isset($options[self::SUGGESTED_ACCESSOR]) && !is_null($options[self::SUGGESTED_ACCESSOR]))
		{
		    $suggestedComponents = $options[self::SUGGESTED_ACCESSOR];
		}
		else
		{
		    $suggestedComponents = null;
		}

		if (isset($options[self::TRANSLATION_ACCESSOR]) && $options[self::TRANSLATION_ACCESSOR])
		{
		    $supportedLanguages = array(RequestContext::getInstance()->getLang());

		    // $supportedLanguages = RequestContext::getInstance()->getSupportedLanguages();

		    $forTranslation = true;
		}
		else
		{
		    $forTranslation = false;
		}

		$model = $document->getPersistentModel();

		$componentNames = $model->getPropertiesNames();

		$xml = array('<document>');

		if (!is_numeric($forceId) )
		{
			$forceId = $document->getId();
		}

		if (!$forTranslation && (is_null($allowedComponentNames) || in_array('id', $allowedComponentNames)))
		{
		    $xml[] = '<component name="id">' . $forceId . '</component>';
		}

		if (!$forTranslation && (is_null($allowedComponentNames) || in_array('lang', $allowedComponentNames)))
		{
		    $xml[] = '<component name="lang">' . $document->getLang() . '</component>';
		}

		 $xml[] = '<component name="documentversion">' . $document->getDocumentversion() . '</component>';


		if ( is_null($lang) )
		{
			$lang = $document->getLang();
		}

		foreach ($componentNames as $componentName)
		{
			$formProperty = $model->getFormProperty($componentName);
			$property = $model->getProperty($componentName);

			// TODO intbonjf 2007-04-12:
			// Label is always visible, since it may be displayed in an element picker.
			// THIS IS A WORKAROUND: may be the 'hidden' information is not suffisant...
			if ((!$formProperty->isHidden() || $componentName == 'label')
			&& (is_null($allowedComponentNames) || in_array($componentName, $allowedComponentNames)
			|| (!is_null($suggestedComponents) && isset($suggestedComponents[$componentName])) ))
			{
			    if ($forTranslation && $property->isLocalized()
			    && (is_null($disabledComponentNames) || !in_array($componentName, $disabledComponentNames)))
			    {
			        foreach ($supportedLanguages as $language)
			        {
			            if ($document->isLangAvailable($language))
                        {
                            $value = $document->{'get'.ucfirst($componentName).'ForLang'}($language);

                            if ($value)
                            {
                                $xml[] = '<component name="' . $componentName . '" suggested="true" label="' . f_Locale::translateUI('&modules.uixul.bo.languages.' . ucfirst($language) . 'Label;') . '">';
                                $xml[] = '<![CDATA[' . $document->{'get'.ucfirst($componentName).'ForLang'}($language). ']]>';
                    		    $xml[] = '</component>';
                            }
                        }
			        }
			    }
			    else if (is_null($allowedComponentNames) || in_array($componentName, $allowedComponentNames))
			    {
			        if (!is_null($disabledComponentNames) && in_array($componentName, $disabledComponentNames))
            		{
            		    $xml[] = '<component name="' . $componentName . '" disabled="true">';
            		}
            		else
            		{
            		    $xml[] = '<component name="' . $componentName . '">';
            		}

            		if ($property->isDocument())
    				{
    					if ($property->isArray())
    					{
    						$components = $document->{'get'.ucfirst($componentName).'Array'}();

    						foreach ($components as $component)
    						{
    							$xml[] = self::_toXml($component, $level-1, $lang, null, $options);
    						}
    					}
    					else
    					{
    						$component = $document->{'get'.ucfirst($componentName)}();

    						if ($component instanceof f_persistentdocument_PersistentDocument)
    						{
    							$xml[] = self::_toXml($component, $level-1, $lang, null, $options);
    						}
    					}
    				}
    				else
    				{
    					$value = $document->{"get".ucfirst($componentName)}();
    					if (is_bool($value))
    					{
    						$value = f_util_Convert::toString($value);
    					}
    					$xml[] = '<![CDATA['.$value.']]>';
    				}

    				$xml[] = '</component>';
			    }

			    if (!is_null($suggestedComponents) && isset($suggestedComponents[$componentName]))
    		    {
    		        $first = true;

    		        if (!is_array($suggestedComponents[$componentName]))
    		        {
    		            $suggestedComponents[$componentName] = array($suggestedComponents[$componentName]);
    		        }

        		    foreach ($suggestedComponents[$componentName] as $suggestion)
        		    {
        		        if ($first)
        		        {
        		            $first = false;
        		            $xml[] = '<component name="' . $componentName . '" suggested="true" label="' . f_Locale::translateUI('&modules.uixul.bo.general.suggestion.OtherLabel;') .'">';
        		        }
        		        else
        		        {
        		            $xml[] = '<component name="' . $componentName . '" suggested="true">';
        		        }
        		        $xml[] = '<![CDATA[' . $suggestion . ']]>';
        		        $xml[] = '</component>';
        		    }
    		    }
			}
		}

		$xml[] = '</document>';

		return join(K::CRLF, $xml);
	}
	
	/**
	 * @deprecated
	 */
	public static function toXmlForm($document)
	{
	    $model = $document->getPersistentModel();
	    $allowedProperties = array('id', 'lang', 'documentversion');
	    foreach ($model->getFormPropertiesInfos() as $formProperty) 
	    {
	    	if (!$formProperty->isHidden() && !in_array($formProperty->getName(), $allowedProperties))
	    	{
	    	    $allowedProperties[] = $formProperty->getName();
	    	}
	    }
	    
	    if ($model->isLocalized() && $document->getLang() != RequestContext::getInstance()->getLang())
	    {
	       $disabledProperties = self::getNoneLocalizedProperties($model);
	    }
	    else
	    {
	       $disabledProperties = array(); 
	    }
	    
	    $suggestedPropertiesValue = array();
	   
	    $xmlDocument  = new DOMDocument('1.0', 'utf-8');
	    self::xmlExport($xmlDocument, null, $document, $allowedProperties, $disabledProperties, $suggestedPropertiesValue);
	    return $xmlDocument->saveXML($xmlDocument->documentElement);
	}
	
	/**
	 * @deprecated
	 */
	public static function toXmlFormForTranslation($document)
	{
	    $model = $document->getPersistentModel();
	    $allowedProperties = array('documentversion');
	    foreach ($model->getFormPropertiesInfos() as $formProperty) 
	    {
	    	if (!$formProperty->isHidden() && !in_array($formProperty->getName(), $allowedProperties))
	    	{
	    	    $allowedProperties[] = $formProperty->getName();
	    	}
	    }
	    
	    if ($model->isLocalized())
	    {
	       $disabledProperties = self::getNoneLocalizedProperties($model);
	    }
	    else
	    {
	       $disabledProperties = array(); 
	    }
	    
	    $suggestedPropertiesValue = array();	    
	    if ($document->isContextLangAvailable())
	    {
	        $label = f_Locale::translateUI('&modules.uixul.bo.languages.' .RequestContext::getInstance()->getLang() . 'Label;');
	        foreach ($model->getPropertiesInfos() as $name => $infos)
		    {
			    if ($infos->isLocalized())
			    {
			        if ($infos->getType() == f_persistentdocument_PersistentDocument::PROPERTYTYPE_BOOLEAN)
			        {
			            $value = $document->{'get'.ucfirst($name)}() ? 'true' : 'false';
			        } else if ($infos->getType() == f_persistentdocument_PersistentDocument::PROPERTYTYPE_DATETIME)
                    {
                        $value = $document->{"getUI".ucfirst($name)}();	
                    } else 
                    {
                        $value = $document->{'get'.ucfirst($name)}();		        
                    }
                    
			        if ($value)
			        {
			            $suggestedPropertiesValue[$name] = array($label, $value);
			        }
			    }
		    }	        
	    }
 
	    $xmlDocument  = new DOMDocument('1.0', 'utf-8');
	    self::xmlExport($xmlDocument, null, $document, $allowedProperties, $disabledProperties, $suggestedPropertiesValue);
	    return $xmlDocument->saveXML($xmlDocument->documentElement);
	}
	
	
	/**
	 * @deprecated
	 */
	public static function toXmlCustomForm($document, $allowedProperties = array(), $disabledProperties = array(), $suggestedPropertiesValue = array())
	{
	    if (!in_array('documentversion', $allowedProperties))
        {
            $allowedProperties[] = 'documentversion';
        }
        
        foreach ($disabledProperties as $disabledProperty) 
        {
        	if (!in_array($disabledProperty, $allowedProperties))
        	{
        	    $allowedProperties[] = $disabledProperty;
        	}
        }
    	foreach ($suggestedPropertiesValue as $suggestedProperty => $data) 
        {
        	if (!in_array($suggestedProperty, $allowedProperties))
        	{
        	    $allowedProperties[] = $suggestedProperty;
        	}
        }	          
        
	    $xmlDocument  = new DOMDocument('1.0', 'utf-8');
	    self::xmlExport($xmlDocument, null, $document, $allowedProperties, $disabledProperties, $suggestedPropertiesValue);
	    return $xmlDocument->saveXML($xmlDocument->documentElement);
	}

	/**
	 * @deprecated
	 */
	private static function xmlExport($xmlDocument, $parentElement, $document, $allowedProperties = array(), $disabledProperties  = array(), $suggestedPropertiesValue = array())
	{
	    $xmlElement = $xmlDocument->createElement('document');
	    if ($parentElement === null)
	    {
	        $xmlDocument->appendChild($xmlElement);
	    } else 
	    {
	        $parentElement->appendChild($xmlElement);
	    }	    
	    foreach ($document->getPersistentModel()->getPropertiesInfos() as $name => $infos) 
	    {
	    	if (!in_array($name, $allowedProperties))
	    	{
	    	    continue;
	    	}
	    	self::xmlExportProperty($xmlDocument, $xmlElement, $document, $infos, 
	    	    in_array($name, $disabledProperties), 
	    	    isset($suggestedPropertiesValue[$name]) ? $suggestedPropertiesValue[$name] : null, $parentElement !== null);
	    }	    
	}
	
	/**
	 * @deprecated
	 */
	private static function xmlExportProperty($xmlDocument, $parentElement, $document, $propertyInfo, $disabled, $suggeste = null, $isSubProperty = false)
	{
	    $xmlElement = $xmlDocument->createElement('component');
	    $parentElement->appendChild($xmlElement);
	    $xmlElement->setAttribute('name', $propertyInfo->getName());
	    if ($disabled)
	    {
	        $xmlElement->setAttribute('disabled', 'true');
	    }
	    if ($suggeste !== null)
	    {
	        $xmlElement->setAttribute('suggested', 'true');
            if (is_array($suggeste))
            {
    	        $xmlElement->setAttribute('label', $suggeste[0]);
    	        $xmlElement->appendChild($xmlDocument->createCDATASection($suggeste[1])); 
            }
	        else
	        {
    	        $xmlElement->setAttribute('label', f_Locale::translateUI('&modules.uixul.bo.general.suggestion.OtherLabel;'));
    	        $xmlElement->appendChild($xmlDocument->createCDATASection($suggeste)); 
	        } 	        
	    }
	    else
	    {
            if ($propertyInfo->isDocument())
            {
                if ($propertyInfo->isArray())
                {
                    $documents = $document->{'get'.ucfirst($propertyInfo->getName()).'Array'}();
                    foreach ($documents as $subdoc)
					{
						self::xmlExport($xmlDocument, $xmlElement, $subdoc, array('id', 'label', 'lang', 'documentversion'));
					}                   
                }
                else
                {
                    $subdoc = $document->{'get'.ucfirst($propertyInfo->getName())}();
                    if ($subdoc instanceof f_persistentdocument_PersistentDocument)
					{
						self::xmlExport($xmlDocument, $xmlElement, $subdoc, array('id', 'label'));
					}
                }
            }
            else if ($propertyInfo->getType() == f_persistentdocument_PersistentDocument::PROPERTYTYPE_BOOLEAN)
            {
                $value = $document->{"get".ucfirst($propertyInfo->getName())}();
            	$xmlElement->appendChild($xmlDocument->createTextNode($value ? 'true' : 'false'));
            } 
            else if ($propertyInfo->getType() == f_persistentdocument_PersistentDocument::PROPERTYTYPE_DATETIME)
            {
                $value = $document->{"getUI".ucfirst($propertyInfo->getName())}();
            	$xmlElement->appendChild($xmlDocument->createTextNode($value));                
            }
	        else if ($propertyInfo->getType() == f_persistentdocument_PersistentDocument::PROPERTYTYPE_DOUBLE)
            {
                $value = $document->{"get".ucfirst($propertyInfo->getName())}();
                $txtValue = f_util_Convert::toUIDouble($value);
            	$xmlElement->appendChild($xmlDocument->createTextNode($txtValue));                
            }
            else
            {
            	if ($isSubProperty && "label" == $propertyInfo->getName())
            	{
            		$value = $document->getTreeNodeLabel();
            	}
            	else
            	{
                	$value = $document->{"get".ucfirst($propertyInfo->getName())}();
            	}
            	$xmlElement->appendChild($xmlDocument->createCDATASection($value));
            }  
	    }
	}
}
