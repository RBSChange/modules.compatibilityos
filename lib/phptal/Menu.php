<?php
class PHPTAL_Php_Attribute_CHANGE_menu extends PHPTAL_Php_Attribute_TAL_Repeat
{
    protected function initRepeat()
    {
        $g = $this->tag->generator;

        $expressions = $g->splitExpression($this->expression);

        $listTag = 'ul';
        $liClass = '';
        $ulClass = '';
        $liCurrentClass = '';
        $liInPathClass = '';
        $flat = false;
        $masterUlClass = '';
        $masterUlId = '';
        $liEvenClass = '';
        $liOddClass = '';
        $liFirstClass = '';
        $liFirstInLevelClass = '';
        $liLastClass = '';
        $liLastInLevelClass = '';
        $liHasChildrenClass = '';
        $liClassByLevel = array();
        $ulClassByLevel = array();
        $columnBreak = array();
        $lineBreak = 0;
        $maxLevel = 20;
        $anchors = 0;
        $skipMasterUL = false;
        $onlyTopics = false;

        // foreach attribute
        foreach ($expressions as $exp)
        {
            list($attribute, $value) = $this->parseSetExpression($exp);
            $attribute = strtolower($attribute);

            switch ($attribute)
            {
            	case 'menuobject':
            		$code = $g->evaluateExpression($value);
            		break;
            	case 'itemname':
            		$varName = $value;
            		break;
            	case 'liclass':
            		$liClass = $value;
            		break;
            	case 'lioddclass':
            		$liOddClass = $value;
            		break;
            	case 'lievenclass':
            		$liEvenClass = $value;
            		break;
            	case 'lifirstclass':
            		$liFirstClass = $value;
            		break;
            	case 'lilastclass':
            		$liLastClass = $value;
            		break;
            	case 'ulclass':
            		$ulClass = $value;
            		break;
            	case 'maxlevel':
            		$maxLevel = $this->evaluate($value);
            		break;
            	case 'licurrentclass':
            		$liCurrentClass = $value;
            		break;
            	case 'liinpathclass':
            		$liInPathClass = $value;
            		break;
            	case 'masterulclass':
            		$masterUlClass = $value;
            		break;
            	case 'class':
            		$masterUlClass = $this->evaluate($value);
            		break;
            	case 'id':
            		$masterUlId = $this->evaluate($value);
            		break;
            	case 'masterulid':
            		$masterUlId = $value;
            		break;
            	case 'flat':
            		$flat = f_util_Convert::toBoolean($value);
            		break;
            	case 'columnbreak':
            	    $columnBreak = explode('/', $value);
            		break;
            	case 'linebreak':
            		$lineBreak = f_util_Convert::toInteger($value);
            		break;
                case 'anchors':
            		$anchors = f_util_Convert::toInteger($value);
            		break;
            	case 'lihaschildrenclass':
            		$liHasChildrenClass = $value;
            		break;
            	case 'lifirstinlevelclass':
            		$liFirstInLevelClass = $value;
            		break;
            	case 'lilastinlevelclass':
            		$liLastInLevelClass = $value;
            		break;
            	case 'skipmasterul':
            		$skipMasterUL = f_util_Convert::toBoolean($value);
            		break;
            	case 'listtag':
            		$listTag = strcasecmp($value, 'ol') == 0 ? 'ol' : 'ul';
            		break;
            	case 'onlytopics':
            		$onlyTopics = f_util_Convert::toBoolean($value);
            		break;
            	default :
            		$matches = array();
            		if (preg_match('/^liclass\[([\d+])\]$/', $attribute, $matches))
            		{
            			$liClassByLevel[intval($matches[1])] = $value;
            		}
            		else if (preg_match('/^ulclass\[([\d+])\]$/', $attribute, $matches))
            		{
            			$ulClassByLevel[intval($matches[1])] = $value;
            		}
            }
        }

        if ($maxLevel < 0 || $maxLevel > 20)
        {
        	$maxLevel = 20;
        }
        if (empty($maxLevel))
        {
        	$maxLevel = 1;
        }
        if (empty($varName))
        {
        	$varName = 'item';
        }
        if (empty($code))
        {
        	throw new PHPTAL_Exception("Missing \"menuObject\" parameter in \"change:menu\" tag.");
        }

        $this->item       = '$ctx->'.$varName;
        $this->controller = self::REPEAT.'->'.$varName;

        // alias to repeats handler to avoid calling extra getters on each variable access
        $g->doSetVar(self::REPEAT, '$ctx->repeat');

        // reset item var into template context
        $g->doIf('!isset('.$this->item.')');
        $g->doSetVar($this->item, 'false');
        $g->doEnd();

        // instantiate controller using expression
        $g->doSetVar('$tmp', $code);
	    $g->doSetVar($this->controller, 'new PHPTAL_RepeatController($tmp)');
		$g->doSetVar($this->controller.'->currentLevel', '-1');
		$g->doSetVar($this->controller.'->liClass', '"'.$liClass.'"');
		$g->doSetVar($this->controller.'->liOddClass', '"'.$liOddClass.'"');
		$g->doSetVar($this->controller.'->liEvenClass', '"'.$liEvenClass.'"');
		$g->doSetVar($this->controller.'->liFirstClass', '"'.$liFirstClass.'"');
		$g->doSetVar($this->controller.'->liLastClass', '"'.$liLastClass.'"');
		$g->doSetVar($this->controller.'->liFirstInLevelClass', '"'.$liFirstInLevelClass.'"');
		$g->doSetVar($this->controller.'->liLastInLevelClass', '"'.$liLastInLevelClass.'"');
		$g->doSetVar($this->controller.'->ulClass', '"'.$ulClass.'"');
		$g->doSetVar($this->controller.'->liCurrentClass', '"'.$liCurrentClass.'"');
		$g->doSetVar($this->controller.'->liInPathClass', '"'.$liInPathClass.'"');
		$g->doSetVar($this->controller.'->masterUlClass', '"'.$masterUlClass.'"');
		$g->doSetVar($this->controller.'->masterUlId', '"'.$masterUlId.'"');
		$g->doSetVar($this->controller.'->flat', $flat ? "true" : "false");
		$g->doSetVar($this->controller.'->currentPage', 'WebsiteHelper::getCurrentPageAttributeForMenu()');
		$g->doSetVar($this->controller.'->maxLevel', strval($maxLevel));
		$g->doSetVar($this->controller.'->itemOddArray', 'array()');
		$g->doSetVar($this->controller.'->indexArray', 'array()');
		$g->doSetVar($this->controller.'->liClassByLevel', var_export($liClassByLevel, true));
		$g->doSetVar($this->controller.'->ulClassByLevel', var_export($ulClassByLevel, true));
		$g->doSetVar($this->controller.'->columnBreak', var_export($columnBreak, true));
		$g->doSetVar($this->controller.'->lineBreak', strval($lineBreak));
		$g->doSetVar($this->controller.'->anchors', strval($anchors));
		$g->doSetVar($this->controller.'->skipMasterUL', $skipMasterUL ? 'true' : 'false');
		$g->doSetVar($this->controller.'->listTag', '"'.$listTag.'"');
		$g->doSetVar($this->controller.'->onlyTopics', $onlyTopics ? 'true' : 'false');
		$g->doSetVar($this->controller.'->itemIndex', '0');
		$g->doSetVar($this->controller.'->liHasChildrenClass', '"' . $liHasChildrenClass .'"');
        $g->doIf($this->controller.'->length == 0');
        $g->doSetVar($this->controller.'->end', 'true');
        $g->doEnd();

		if ($flat)
		{
	        $g->doIf($this->controller.'->length != 0');
			$g->doEcho('PHPTAL_Php_Attribute_CHANGE_menu::getUL('.$this->controller.', 0, 0)');
	        $g->doEnd();
		}

		$this->tag->headFootDisabled = true;
    }


    protected function updateIterationVars()
    {
        $g = $this->tag->generator;

        $g->doIf($this->controller.'->currentLevel == -1');
        $g->doSetVar($this->controller.'->originalLevel', $this->item.'->getLevel()');
        $g->doEnd();

        $g->doSetVar($this->controller.'->key', '$__key__');
        $g->doSetVar($this->controller.'->index', $this->controller.'->index +1');
        $g->doSetVar($this->controller.'->number', $this->controller.'->number +1');
        $g->doSetVar($this->controller.'->even', $this->controller.'->index % 2 == 0');
        $g->doSetVar($this->controller.'->odd', '!'.$this->controller.'->even');

        // repeat/item/end set to true when last item is reached
        $g->doIf($this->controller.'->number == '.$this->controller.'->length');
        	$g->doSetVar($this->controller.'->end', 'true');
        $g->doEnd();

        // Print entry if:
        //   item->level LESS THAN maxLevel
        // AND
        //   NOT(onlyTopics) OR item->isTopic
		$g->doIf('(('.$this->item.'->getLevel() - '.$this->controller.'->originalLevel) < '.$this->controller.'->maxLevel) && (!'.$this->controller.'->onlyTopics || '.$this->item.'->isTopic())');

		// If in flat mode, set the current level to the item level
		$g->doIf($this->controller.'->flat');
			$g->doSetVar($this->controller.'->currentLevel', $this->item.'->getLevel()');
        $g->doEnd();

		$g->pushCode('if (!isset('.$this->controller.'->indexArray['.$this->item.'->getLevel()])) '.$this->controller.'->indexArray['.$this->item.'->getLevel()] = 0');

		$g->doIf('(' . $this->controller.'->lineBreak > 0) && (' .$this->controller.'->itemIndex > 0) && (' . $this->controller.'->itemIndex % '.$this->controller.'->lineBreak == 0)');
		    $g->pushRawHtml("</li></ul>");
		    $g->doSetVar($this->controller.'->indexArray['.$this->item.'->getLevel()]', '0');
		    $g->doEcho('PHPTAL_Php_Attribute_CHANGE_menu::getUL('.$this->controller.', '.$this->item.'->getLevel(), '.$this->item.'->getId())');
		    $g->doSetVar($this->controller.'->itemIndex', '0');
		    $g->doSetVar($this->controller.'->index', '0');
		// If item level == current level
        $g->doElseIf($this->item.'->getLevel() == '.$this->controller.'->currentLevel');
			$g->doSetVar($this->controller.'->itemOddArray['.$this->item.'->getLevel()]', '! (isset('.$this->controller.'->itemOddArray['.$this->item.'->getLevel()]) && '.$this->controller.'->itemOddArray['.$this->item.'->getLevel()])');
			$g->pushCode($this->controller.'->indexArray['.$this->item.'->getLevel()]++');
			$g->doIf('!'.$this->controller.'->flat || !'.$this->controller.'->start');
				$g->pushRawHtml("</li>");
	        $g->doEnd();
	    // If item level > current level
	    $g->doElseIf($this->item.'->getLevel() > '.$this->controller.'->currentLevel');
			$g->doSetVar($this->controller.'->itemOddArray['.$this->item.'->getLevel()]', 'false');
			$g->doSetVar($this->controller.'->indexArray['.$this->item.'->getLevel()]', '0');
	        $g->doEcho('PHPTAL_Php_Attribute_CHANGE_menu::getUL('.$this->controller.', '.$this->item.'->getLevel(), '.$this->item.'->getId())');
			$g->doSetVar($this->controller.'->itemIndex', '0');
	    // If item level < current level
	    $g->doElseIf($this->item.'->getLevel() < '.$this->controller.'->currentLevel');
	        $g->pushRawHtml("</li>");
			$g->pushCode('echo str_repeat("</ul></li>", '.$this->controller.'->currentLevel - '.$this->item.'->getLevel());');
			$g->doSetVar($this->controller.'->itemOddArray['.$this->item.'->getLevel()]', '! (isset('.$this->controller.'->itemOddArray['.$this->item.'->getLevel()]) && '.$this->controller.'->itemOddArray['.$this->item.'->getLevel()])');
			$g->pushCode($this->controller.'->indexArray['.$this->item.'->getLevel()]++');
        $g->doEnd();

        $g->doSetVar($this->controller.'->itemId', $this->item.'->getId()');

        $g->doIf($this->controller.'->itemId == '.$this->controller.'->currentPage["id"] || (isset('.$this->controller.'->currentPage["indexOf"]) && '.$this->controller.'->itemId == '.$this->controller.'->currentPage["indexOf"])');
        	$g->doSetVar($this->controller.'->isCurrent', 'true');
        $g->doElse();
        	$g->doSetVar($this->controller.'->isCurrent', 'false');
        $g->doEnd();

        $g->doIf('in_array('.$this->controller.'->itemId, '.$this->controller.'->currentPage["ancestors"])');
        // @deprecated 'repeat/item/isDescendent' USE 'repeat/item/isInPath' INSTEAD!
	        $g->doSetVar($this->controller.'->isDescendent', 'true');
	        $g->doSetVar($this->controller.'->isInPath', 'true');
        $g->doElse();
        // @deprecated 'repeat/item/isDescendent' USE 'repeat/item/isInPath' INSTEAD!
	        $g->doSetVar($this->controller.'->isDescendent', 'false');
	        $g->doSetVar($this->controller.'->isInPath', 'false');
        $g->doEnd();


		$g->doSetVar($this->controller.'->currentLevel', $this->item.'->getLevel()');
		$g->doSetVar($this->controller.'->currentPosition', $this->controller.'->indexArray['.$this->item.'->getLevel()]');
		$g->doEcho('PHPTAL_Php_Attribute_CHANGE_menu::getLI('.$this->controller.', '.$this->item.'->getLevel(), '.$this->item.'->getId())');
    }

    public function end()
    {
		$g = $this->tag->generator;

		// End if:
        //   item->level LESS THAN maxLevel
        // AND
        //   NOT(onlyTopics) OR item->isTopic
		// See updateIterationVars() method above.
		$g->doEnd();

        $g->doSetVar($this->controller.'->itemIndex', $this->controller.'->itemIndex + 1');
		$g->doSetVar($this->controller.'->start', 'false');

		// Close nested <ul/> elements.
        $g->doIf($this->controller.'->end && '.$this->controller.'->length != 0');
			$g->doIf('!'.$this->controller.'->flat');
				$g->pushCode('echo str_repeat("</li></" . '.$this->controller.'->listTag . ">", '.$this->controller.'->currentLevel - '.$this->controller.'->originalLevel);');
	        $g->doEnd();
	        $g->pushRawHtml("</li>");

	        $g->doIf('!'.$this->controller.'->skipMasterUL');
		        //$g->pushRawHtml("</ul>");
		        $g->pushCode('echo "</" . '.$this->controller.'->listTag . ">";');
	        $g->doEnd();
        $g->doEnd();

        // End foreach
        $g->doEnd();
    }


    public function getLI($controller, $level, $id)
    {
    	$li = '<li';
    	$class = array();
    	if ($controller->liClass)
    	{
    		$class[] = $controller->liClass;
    	}
		if ($controller->liCurrentClass && $controller->isCurrent)
    	{
    		$class[] = $controller->liCurrentClass;
    	}
		if ($controller->liInPathClass && ($controller->isInPath || $controller->isCurrent))
    	{
    		$class[] = $controller->liInPathClass;
    	}
    	if ($controller->liOddClass && $controller->itemOddArray[$level])
    	{
    		$class[] = $controller->liOddClass;
    	}
    	if ($controller->liEvenClass && !$controller->itemOddArray[$level])
    	{
    		$class[] = $controller->liEvenClass;
    	}
    	if ($controller->liFirstClass && $controller->index == 0)
    	{
    		$class[] = $controller->liFirstClass;
    	}
    	if ($controller->liLastClass && $controller->index == $controller->length - 1)
    	{
    		$class[] = $controller->liLastClass;
    	}
    	if (isset($controller->liClassByLevel[$level]))
    	{
    		$class[] = $controller->liClassByLevel[$level];
    	}
    	if ($controller->liFirstInLevelClass && isset($controller->indexArray[$level]) && intval($controller->indexArray[$level]) === 0)
    	{
    		$class[] = $controller->liFirstInLevelClass;
    	}

    	// Check if element is the last one in the current level
    	if (true)
    	{
	    	$nextIndex = $controller->index + 1;
    		if (!isset($controller->source[$nextIndex]))
    		{
    			$class[] = $controller->liLastInLevelClass;
    		}
    		else
    		{
    			$isLastInLevel = true;
    			$hasChildren = false;
    			// Skip descendent elements (level is greater than current element's level).
    			while (isset($controller->source[$nextIndex]))
    			{
	    			$nextElement = $controller->source[$nextIndex];
	    			// If next element's level is less than current element's level,
	    			// it means that the current element is the last element in the
	    			// current level. So we can add the CSS class ($isLastInLevel's
	    			// value is left to true).
		    		if ($nextElement->getLevel() < $level)
		    		{
		    			break;
		    		}
		    		else if ($nextElement->getLevel() == $level)
		    		{
		    			// If we find an element in the same level, the current
		    			// element is not the last one (set $isLastInLevel to false).
		    			$isLastInLevel = false;
		    			break;
		    		}
		    		else 
		    		{
		    			$hasChildren = true;
		    		}
		    		$nextIndex++;
    			}
    			// Is the current element the last one?
    			if ($isLastInLevel)
    			{
    				$class[] = $controller->liLastInLevelClass;
    			}
    			if ($hasChildren)
    			{
    				$class[] = $controller->liHasChildrenClass;
    			}
    			
    			
    		}
    	}

    	if ($controller->columnBreak
    	&& ($controller->columnBreak[0] == ($level + 1))
    	&& ((($controller->indexArray[$level] + 1) % $controller->columnBreak[1]) == 0))
    	{
    		$class[] = 'column-break-before';
    	}
    	else if ($controller->columnBreak
    	&& ($controller->columnBreak[0] == ($level + 1))
    	&& ($controller->indexArray[$level] > 0)
    	&& ((($controller->indexArray[$level] + 1) % $controller->columnBreak[1]) == 1))
    	{
    		$class[] = 'column-break-after';
    	}

    	if ( ! empty($class) )
    	{
			$class = array_unique($class);
    		foreach ($class as $i => $c)
			{
				$class[$i] = str_replace(array('%id', '%level', '%position', '%index'), array($id, $level, isset($controller->indexArray[$level])? $controller->indexArray[$level] : '0', $controller->index), $c);
			}
    		$li .= ' class="'.trim(join(' ', $class)).'"';
    	}
    	$li .= '>';

    	if (($controller->anchors > 0) && ($level < $controller->anchors))
    	{
    	    $li .= sprintf('<a name="menu_%d" id="menu_%d"></a>', $id, $id);
    	}
    	return $li;
    }

    public function getUL($controller, $level, $id)
    {
    	if ($controller->skipMasterUL == true && $controller->currentLevel == -1)
    	{
    		return '';
    	}
    	$ul = '<' . $controller->listTag;
    	$class = array();
    	if ($controller->currentLevel == -1)
    	{
	    	if ($controller->masterUlClass)
	    	{
	    		$class[] = $controller->masterUlClass;
	    	}
	    	if ($controller->masterUlId)
	    	{
	    		$ul .= ' id="'.$controller->masterUlId.'"';
	    	}
    	}
    	if ($controller->ulClass)
    	{
    		$class[] = $controller->ulClass;
    	}
    	if (isset($controller->ulClassByLevel[$level]))
    	{
    		$class[] = $controller->ulClassByLevel[$level];
    	}

    	if ( ! empty($class) )
    	{
			$class = array_unique($class);
			foreach ($class as $i => $c)
			{
				$class[$i] = str_replace(array('%id', '%level', '%position', '%index'), array($id, $level, isset($controller->indexArray[$level])? $controller->indexArray[$level] : '0', $controller->index), $c);
			}
    		$ul .= ' class="'.trim(join(' ', $class)).'"';
    	}

    	$ul .= '>';
    	return $ul;
    }
}