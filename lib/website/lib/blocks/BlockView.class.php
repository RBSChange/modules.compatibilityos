<?php
abstract class block_BlockView
{


    /**
     * Default name for "alert" views.
     *
     * Common usage : when a block cannot be displayed (in Backoffice mode, for example),
     * or other kind of internal errors.
     *
     */
    const ALERT = 'Alert';


    /**
     * Default name for "input" views.
     *
     * Common usage : display form blocks in "input" state.
     *
     */
    const INPUT = 'Input';


    /**
     * Default name for "success" views.
     *
     */
    const SUCCESS = 'Success';


    /**
     * Default name for "error" views.
     *
     */
    const ERROR = 'Error';


    /**
     * Default name for "item" views.
     *
     * Common usage : display a document in "full details".
     *
     */
    const ITEM = 'Item';


    /**
     * Default name for "short item" views.
     *
     * Common usage : display a document in "short details".
     *
     */
    const SHORTITEM = 'ShortItem';


    /**
     * Default name for "short item" views.
     *
     * Common usage : display a document within a list.
     *
     */
    const LISTITEM = 'ListItem';


    /**
     * Default name for "menu" views.
     *
     * Common usage : display a navigation menu related to the document or module.
     *
     */
    const MENU = 'Menu';


    /**
     * Default name for "dummy" views.
     *
     * Common usage : display dummy content, especially for backoffice purpose.
     *
     */
    const DUMMY = 'Dummy';


    /**
     * Default name for "mail" views.
     *
     * Common usage : Newsletter or other mailed content.
     *
     */
    const MAIL = 'Mail';

    /**
     * Name for "unavailable" view.
     * Commun usage: unpublished documents
     * @var String
     */
    const UNAVAILABLE = "Unavailable";

    /**
     * Default name for "empty" views.
     *
     * Common usage : hidden blocks (indexing mode, etc.).
     *
     */
    const NONE = null;


    /**
     * Indicates whether the block is visible or not.
     *
     * @var boolean
     */
    protected $visible = true;


    /**
     * Block handler.
     *
     * @var block_BlockHandler
     */
    private $handler = null;


    /**
     * Block template.
     *
     * @var TemplateObject
     */
    protected $template = null;


    /**
     * Block template attributes.
     *
     * @var array
     */
    private $attributes = array();


    /**
     * @param block_BlankHandler $handler
     */
    public final function __construct($handler)
	{
        $this->handler = $handler;
	}


    /**
     * The initialize() method of BlockView is always called,
     * even if the View is cached.
     *
     * This is useful for modifying the block's context (page title,
     * stylesheets, etc.) while keeping a cached process and/or content.
     *
     * @param block_BlockContext $context
     * @param block_BlockRequest $request
     */
    public function initialize($context, $request)
	{
		// empty
	}


	/**
	 * Mandatory execute method...
	 *
	 * @param block_BlockContext $context
	 * @param block_BlockRequest $request
	 */
	abstract function execute($context, $request);


	// ----------------------------------------


    /**
	 * Returns the cache lifetime of the current view.
	 * @deprecated with no replacement
	 * @return integer
	 */
    public final function getCacheLifeTime()
	{
	    return 0;
	}


	/**
	 * Disable cache for the current view.
	 * @deprecated with no replacement
	 */
    public final function disableCache()
	{
		// nothing
	}


	 /**
	 * Enable cache for the current view.
	 *
	 * @param mixed $lifeTime
	 * @deprecated with no replacement
	 */
    public final function enableCache($lifeTime = null)
	{
	    // nothing
	}


	/**
	 * Return true if the cache is enabled for the current view.
	 *
	 * @return boolean
	 */
	public function isCacheEnabled()
	{
	    return false;
	}
	
	private $cacheSpecs = null;
	private $cacheKeys = null;
	
	public final function getCacheKeyParameters()
	{
		if (!$this->cacheKeys)
		{
			$keyParameters = array();
			$cacheSpecs = array();
			$this->buildCacheKeyParameters($this->getUsedParameters(), $keyParameters, $cacheSpecs);
			$this->cacheSpecs = array_unique($cacheSpecs);
			$this->cacheKeys = $keyParameters;
		}
		return $this->cacheKeys;
	}
	
	protected function getUsedParameters()
	{
		return $this->getParameters();
	}
	
	public final function getCacheSpecifications()
	{
		if (!$this->cacheSpecs)
		{
			// to build
			$this->getCacheKeyParameters();
		}
		return $this->cacheSpecs;
	}
	
	private function buildCacheKeyParameters($source, &$dest, &$cacheSpecs)
	{
		foreach ($source as $key => $value)
		{
			if ($value instanceof f_persistentdocument_PersistentDocument)
			{
				$dest[$key] = $value->getId();
				$cacheSpecs[] = $value->getDocumentModelName();
			}
			elseif (is_array($value))
			{
				$newValue = array();
				$this->buildCacheKeyParameters($value, $newValue, $cacheSpecs);
				$dest[$key] = $newValue;
			}
			else
			{
				$dest[$key] = $value;
			}
		}
	}

	/**
	 * Active/disactive the current view.
	 *
	 * @param boolean $active
	 */
	public final function setVisible($visible)
	{
	    $this->visible = ($visible === true);
	}


	/**
	 * Return true if the current view is "visible".
	 *
	 * @return boolean
	 */
	public final function isVisible()
	{
	    return ($this->visible === true);
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
	 * @return string (null if empty block)
	 */
	public final function forward($destination)
	{
	    if ($destination instanceof block_BlockHandler)
	    {
	    	$destination->initialize($this->getController());
	    	$blockCache = new block_BlockCache($destination);
    	    $blockCache->doAction();

            return $blockCache->doView();
	    }
	    else
	    {
	        throw new BlockException(
               sprintf(
                   'Invalid forward(%s) in %s : first argument must be a valid block_BlockHandler object, use block_BlockView::getNewBlockInstance() to get it',
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
	 * Returns the DocumentService instance to use within the block.
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
		return $this->handler->getViewModuleName();
	}

	/**
	 * Return the current package name of the block.
	 *
	 * @return string
	 */
	public final function getPackageName()
	{
		return $this->handler->getViewPackageName();
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
	 * Set the template name for the current view
	 *
	 * @param string $templateName
	 * @param string $mimeType
	 * @throws BlockException
	 */
	public final function setTemplateName($templateName, $mimeType = K::HTML)
	{
	    $templateLoader = TemplateLoader::getInstance()->setMimeContentType($mimeType);

		$templateLoader->setDirectory('templates');

		try
		{
    		try
    		{
    			$this->template = $templateLoader->setPackageName($this->getPackageName())->load($templateName);
    		}
    		catch (TemplateNotFoundException $e)
    		{
    		    Framework::exception($e);

    			$this->template = $templateLoader->setPackageName('modules_' . K::GENERIC_MODULE_NAME)->load($templateName);
    		}
		}
		catch (TemplateNotFoundException $e)
		{
		    Framework::exception($e);

			throw new BlockException(
               sprintf(
                   'Cannot render block %s_%s : template "%s" not found in %s',
                   $this->getPackageName(),
                   $this->getType(),
                   $templateName,
                   get_class($this)
               )
            );
		}
		
	}
	
	/**
	 * Indicates whether or not the block has a template.
	 *
	 * @return bool true, if the block has a template, otherwise false.
	 */
	public final function hasTemplate()
	{
		return !is_null($this->template);
	}


	/**
	 * Get the template object of the current view.
	 *
	 * @return TemplateObject
	 */
	public final function & getTemplate()
	{
		return $this->template;
	}


	/**
	 * Render the current view.
	 *
	 * @throws BlockException
	 * @return string
	 */
	public final function render()
	{
        if ($this->hasTemplate())
        {
        	// import all the parameters
        	$this->setAttributes($this->getParameters());
        	$attr = $this->getAttributes();
        	$attr['context'] = $this->getController()->getContext();
        	$attr['website_page'] = $this->getController()->getContext()->getAttribute("website_page");
            $this->getTemplate()->importAttributes($attr);
    		return $this->getTemplate()->execute();
        }
        else
        {
            throw new BlockException(
               sprintf(
                   'Cannot render Block %s_%s : no template set in %s',
                   $this->getPackageName(),
                   $this->getType(),
                   get_class($this)
               )
            );
        }
	}


	/**
	 * Check template availability.
	 *
	 * @throws BlockException
	 */
	public final function checkTemplateAvailability()
	{
	    if (!$this->hasTemplate())
        {
            throw new BlockException(
               sprintf(
                   'Block %s_%s tries to set/get attributes of an undefined template in %s',
                   $this->getPackageName(),
                   $this->getType(),
                   get_class($this)
               )
            );
        }
	}


	/**
	 * Clear all attributes associated with this view.
	 *
	 * @return block_BlockView
	 */
	public final function clearAttributes ()
	{
	    $this->checkTemplateAvailability();

		$this->attributes = null;
		$this->attributes = array();

		return $this;
	}


	/**
	 * Retrieve an attribute.
	 *
	 * @param string An attribute name.
	 *
	 * @return mixed An attribute value, if the attribute exists, otherwise
	 *               null.
	 */
	public final function & getAttribute ($name, $default = null)
	{
		$this->checkTemplateAvailability();

		$retval =& $default;
		if (isset($this->attributes[$name]))
		{
			$retval =& $this->attributes[$name];
		}

		return $retval;
	}


	/**
	 * Retrieve an array of attribute names.
	 *
	 * @return array An indexed array of attribute names.
	 */
	public final function getAttributeNames ()
	{
		$this->checkTemplateAvailability();

		return array_keys($this->attributes);
	}


	/**
	 * Retrieve an array of attributes.
	 *
	 * @return array An array of attributes.
	 */
	public final function getAttributes ()
	{
		$this->checkTemplateAvailability();

		return $this->attributes;
	}


	/**
	 * Indicates whether or not the block has the given attribute.
	 *
	 * @param string $name
	 *
	 * @return bool true, if the block has the given attribute, otherwise false.
	 */
	public final function hasAttribute ($name)
	{
		$this->checkTemplateAvailability();

		return isset($this->attributes[$name]);
	}


	/**
	 * Remove an attribute.
	 *
	 * @param string An attribute name.
	 *
	 * @return mixed An attribute value, if the attribute was removed,
	 *               otherwise null.
	 */
	public final function & removeAttribute ($name)
	{
		$this->checkTemplateAvailability();

		$retval = null;

		if (isset($this->attributes[$name]))
		{
			$retval =& $this->attributes[$name];
			unset($this->attributes[$name]);
		}

		return $retval;
	}


	/**
	 * Set an attribute.
	 *
	 * If an attribute with the name already exists the value will be
	 * overridden.
	 *
	 * @param string An attribute name.
	 * @param mixed  An attribute value.
	 *
	 * @return block_BlockView
	 */
	public final function setAttribute ($name, $value)
	{
		$this->checkTemplateAvailability();

		$this->attributes[$name] = $value;

		return $this;
	}


	/**
	 * Set an attribute by reference.
	 *
	 * If an attribute with the name already exists the value will be
	 * overridden.
	 *
	 * @param string An attribute name.
	 * @param mixed  A reference to an attribute value.
	 *
	 * @return block_BlockView
	 */
	public final function setAttributeByRef ($name, &$value)
	{
		$this->checkTemplateAvailability();

		$this->attributes[$name] =& $value;

		return $this;
	}


	/**
	 * Set an array of attributes.
	 *
	 * If an existing attribute name matches any of the keys in the supplied
	 * array, the associated value will be overridden.
	 *
	 * @param array An associative array of attributes and their associated
	 *              values.
	 *
	 * @return block_BlockView
	 */
	public final function setAttributes ($attributes)
	{
		$this->checkTemplateAvailability();

		$this->attributes = array_merge($this->attributes, $attributes);

		return $this;
	}


	/**
	 * Set an array of attributes by reference.
	 *
	 * If an existing attribute name matches any of the keys in the supplied
	 * array, the associated value will be overridden.
	 *
	 * @param array An associative array of attributes and references to their
	 *              associated values.
	 *
	 * @return block_BlockView
	 */
	public final function setAttributesByRef (&$attributes)
	{
		$this->checkTemplateAvailability();

		foreach ($attributes as $key => &$value)
		{
			$this->attributes[$key] =& $value;
		}

		return $this;
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
}