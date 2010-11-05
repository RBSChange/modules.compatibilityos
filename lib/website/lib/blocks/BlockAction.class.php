<?php
abstract class block_BlockAction
{
    /**
     * Indicates whether the action is active or not.
     *
     * @var boolean
     */
    protected $active = true;


    /**
     * Lifetime of the action cache.
     *
     * @var integer
     */
    protected $cacheLifeTime = null;


    /**
     * Block handler.
     *
     * @var block_BlockHandler
     */
    private $handler = null;

    /**
     * @var boolean
     */
    private $forwarded = false;

    public final function __construct($handler)
	{
        $this->handler = $handler;
	}

	/**
	 * @return block_BlockHandler
	 */
	protected final function getHandler()
	{
		return $this->handler;
	}

    /**
     * The initialize() method of BlockAction is always called,
     * even if the Action is cached.
     *
     * This is useful for modifying the block's context (page title,
     * stylesheets, etc.) while keeping a cached process and/or content.
     *
     * @param block_BlockContext $context
     * @param block_BlockRequest $request
     * @return void
     */
    public function initialize($context, $request)
	{
		// empty. Sub classes can override it
	}


	/**
	 * Mandatory execute method...
	 *
	 * @param block_BlockContext $context
	 * @param block_BlockRequest $request
	 * @return String the view name
	 */
	abstract function execute($context, $request);


	// ----------------------------------------


	/**
	 * Returns the cache lifetime of the current action.
	 * @deprecated with no replacement
	 * @return integer
	 */
    public final function getCacheLifeTime()
	{
	    return 0;
	}


	/**
	 * Disable cache for the current action.
	 * @deprecated with no replacement
	 */
    public final function disableCache()
	{
	}


    /**
	 * Enable cache for the current action.
	 * @deprecated with no replacement
	 * @param mixed $lifeTime
	 */
    public final function enableCache($lifeTime = null)
	{
		// do nothing
	}

	/**
	 * Activate/deactivate the current action.
	 *
	 * @param boolean $active
	 */
	public final function setActive($active)
	{
	    $this->active = ($active === true);
	}


	/**
	 * Return true if the current action is "active".
	 *
	 * @return boolean
	 */
	public final function isActive()
	{
	    return ($this->active === true);
	}

	/**
	 * Get a new Block instance
	 *
	 * @return block_BlockHandler
	 */
	public final function getNewBlockInstance()
	{
        return block_BlockHandler::getNewInstance(null);
	}

	/**
	 * Do a "forward" to another block and get the resulting content.
	 *
	 * @param block_BlockHandler $destination
	 * @throws BlockException
	 * @return string or array (forwarded view)
	 */
	public final function forward($destination)
	{
	    if ($destination instanceof block_BlockHandler)
	    {
    	    $destination->initialize($this->getController());

    	    $destination->doAction();

    	    $this->handler->setPackageName($destination->getPackageName());
            $this->handler->setType($destination->getType());
    	    $this->handler->setContext($destination->getContext());
    	    $this->handler->setRequest($destination->getRequest());
    	    $this->handler->clearParameters();
    	    $this->handler->setParameters($destination->getParameters());

    	    return $destination->getViewName();
	    }
	    else
	    {
	        throw new BlockException(
               sprintf(
                   'Invalid forward(%s) in %s : first argument must be a valid block_BlockHandler object, use block_BlockAction::getNewBlockInstance() to get it',
                   gettype($destination),
                   get_class($this)
               )
            );
	    }

        return null;
	}


	/**
	 * Returns the BlockController.
	 *
	 * @return block_BlockController
	 */
	protected final function getController()
	{
		return $this->handler->getController();
	}


	/**
	 * Returns the DocumentService instance to use within the block action.
	 *
	 * @return f_persistentdocument_DocumentService
	 */
	protected final function getDocumentService()
	{
		return $this->getController()->getDocumentService();
	}


    /**
	 * Return the current module name of the block.
	 *
	 * @return string
	 */
	public final function getModuleName()
	{
		return $this->handler->getModuleName();
	}


    /**
	 * Return the current package name of the block.
	 *
	 * @return string
	 */
	public final function getPackageName()
	{
		return $this->handler->getPackageName();
	}

	/**
	 * Return the current type of the block.
	 *
	 * @return string
	 */
	public final function getType()
	{
		return $this->handler->getType();
	}


	/**
	 * Return the current lang of the block.
	 *
	 * @return string
	 */
	public final function getLang()
	{
		return $this->handler->getLang();
	}


	/**
	 * Generate a proper URL for the block.
	 *
	 * @param array $parameters Associative array of parameters
	 * @param integer $pageId ID of the targetted page (default = current)
	 * @return string
	 */
	public final function genUrl($parameters = array(), $pageId = null)
	{
		return $this->handler->getContext()->genUrl($parameters, $pageId, $this->getModuleName());
	}


	/**
	 * Clear all parameters associated with this request.
	 *
	 * @return block_BlockHandler
	 */
	public function clearParameters ()
	{
		return $this->handler->clearParameters();
	}


	/**
	 * Retrieve a parameter.
	 *
	 * @param string A parameter name.
	 * @param mixed  A default parameter value.
	 *
	 * @return mixed A parameter value, if the parameter exists, otherwise
	 *               null.
	 */
	public final function & getParameter ($name, $default = null)
	{
		return $this->handler->getParameter($name, $default);
	}



	/**
	 * Retrieve an array of parameter names.
	 *
	 * @return array An indexed array of parameter names.
	 */
	public final function getParameterNames ()
	{
		return $this->handler->getParameterNames();
	}


	/**
	 * Retrieve an array of parameters.
	 *
	 * @return array An associative array of parameters.
	 */
	public final function getParameters ()
	{
		return $this->handler->getParameters();
	}


	/**
	 * Indicates whether or not a parameter exists.
	 *
	 * @param string A parameter name.
	 *
	 * @return bool true, if the parameter exists, otherwise false.
	 */
	public final function hasParameter ($name)
	{
		return $this->handler->hasParameter($name);
	}


	/**
	 * Remove a parameter.
	 *
	 * @param string A parameter name.
	 *
	 * @return string A parameter value, if the parameter was removed,
	 *                otherwise null.
	 */
	public final function & removeParameter ($name)
	{
		return $this->handler->removeParameter($name);
	}


	/**
	 * Set a parameter.
	 *
	 * If a parameter with the name already exists the value will be overridden.
	 *
	 * @param string A parameter name.
	 * @param mixed  A parameter value.
	 *
	 * @return block_BlockHandler
	 */
	public final function setParameter ($name, $value)
	{
		return $this->handler->setParameter($name, $value);
	}


	/**
	 * Set a parameter by reference.
	 *
	 * If a parameter with the name already exists the value will be
	 * overridden.
	 *
	 * @param string A parameter name.
	 * @param mixed  A reference to a parameter value.
	 *
	 * @return block_BlockHandler
	 */
	public final function setParameterByRef ($name, &$value)
	{
		return $this->handler->setParameterByRef($name, $value);
	}


	/**
	 * Set an array of parameters.
	 *
	 * If an existing parameter name matches any of the keys in the supplied
	 * array, the associated value will be overridden.
	 *
	 * @param array An associative array of parameters and their associated
	 *              values.
	 *
	 * @return block_BlockHandler
	 */
	public final function setParameters ($parameters)
	{
		return $this->handler->setParameters($parameters);
	}


	/**
	 * Set an array of parameters by reference.
	 *
	 * If an existing parameter name matches any of the keys in the supplied
	 * array, the associated value will be overridden.
	 *
	 * @param array An associative array of parameters and references to their
	 *              associated values.
	 *
	 * @return block_BlockHandler
	 */
	public final function setParametersByRef (&$parameters)
	{
		return $this->handler->setParametersByRef($parameters);
	}


	/**
	 * Returns the PersistentProvider instance.
	 *
	 * @return f_persistentdocument_PersistentProvider
	 */
	protected final function getPersistentProvider()
	{
		return f_persistentdocument_PersistentProvider::getInstance();
	}

	/**
	 * // really useful ?
	 * @return Integer the document id or null if no <code>K::COMPONENT_ID_ACCESSOR</code> parameter
	 */
	public final function getDocumentIdParameter()
	{
		return $this->handler->getDocumentIdParameter();
	}

	/**
	 * @return f_persistentdocument_PersistentDocument or null if no <code>K::COMPONENT_ID_ACCESSOR</code> parameter
	 */
	public final function getDocumentParameter()
	{
		return $this->handler->getDocumentParameter();
	}

	/**
	 * // really useful ?
	 * @return array<Integer> the document ids or null if no <code>K::COMPONENT_ID_ACCESSOR</code> parameter
	 */
	public final function getDocumentIdsParameter()
	{
		return $this->handler->getDocumentIdsParameter();
	}

	/**
	 * @return array<f_persistentdocument_PersistentDocument> the document id or null if no <code>K::COMPONENT_ID_ACCESSOR</code> parameter
	 */
	public final function getDocumentsParameter()
	{
		return $this->handler->getDocumentsParameter();
	}

	/**
	 * @param array<f_persistentdocument_PersistentDocument> $documents
	 */
	public final function setDocumentsParameter($documents)
	{
		$this->handler->setDocumentsParameter($documents);
	}

	/**
	 * @param f_persistentdocument_PersistentDocument $documents
	 */
	public final function setDocumentParameter($document)
	{
		$this->handler->setDocumentParameter($document);
	}

	/**
	 * @return array<String, String> couples of (parameter name / value) that are used by the block
	 */
	public function getCacheKeyParameters()
	{
		return array_merge($this->handler->getGlobalRequest()->getParameters(), $this->handler->getParameters());
	}

	/**
	 * @see findParameterValues
	 * @param array<String> $parameterNames
	 * @return array<String, String>
	 */
	protected final function buildCacheKeyParameters($parameterNames)
	{
		return $this->findParameterValues($parameterNames);
	}

	/**
	 * Find parameter values in scopes :
	 * <ol>
	 *   <li>handler parameters</li>
	 *   <li>handler request parameters</li>
	 *   <li>handler global request</li>
	 *   <li>session</li>
	 * </ol>
	 * Parameters defined in scope <code>n-1</code> can not be overriden by scope <code>n</code>
	 * @param array<String> $parameterNames
	 * @return array<String, String>
	 */
	protected final function findParameterValues($parameterNames)
	{
		$emptyParameters = array_flip($parameterNames);
		return array_merge(array_intersect_key($_SESSION, $emptyParameters),
		array_intersect_key($this->handler->getGlobalRequest()->getParameters(), $emptyParameters),
		array_intersect_key($this->handler->getRequest()->getParameters(), $emptyParameters),
		array_intersect_key($this->handler->getParameters(), $emptyParameters));
	}

	/**
	 * @param String $parameterName
	 * @return String
	 */
	protected final function findParameterValue($parameterName)
	{
		$value = $this->handler->getRequest()->getParameter($parameterName);
		if (!is_null($value))
		{
			return $value;
		}
		$value = $this->handler->getGlobalRequest()->getParameter($parameterName);
		if (!is_null($value))
		{
			return $value;
		}
		if (isset($_SESSION[$parameterName]))
		{
			return $_SESSION[$parameterName];
		}
		$value = $this->handler->getParameter($parameterName);
		if (!is_null($value))
		{
			return $value;
		}
		return null;
	}

	/**
	 * @return array<String>
	 */
	public function getCacheSpecifications()
	{
		return null;
	}

	/**
	 * @return boolean
	 */
	public function isCacheEnabled()
	{
		return is_array($this->getCacheSpecifications());
	}

	/**
	 * It is pertinent to call this method only is isCacheEnabled() returns true
	 * @return boolean
	 */
	public function isGlobalRequestCacheEnabled()
	{
		return true;
	}

	/**
	 * @return void
	 */
	protected function redirectTo404()
	{
		HttpController::getInstance()->redirect("website", "Error404");
	}
}