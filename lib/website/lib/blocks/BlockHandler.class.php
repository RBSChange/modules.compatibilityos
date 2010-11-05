<?php
class block_BlockHandler
{
    /**
     * Block id.
     *
     * @var mixed
     */
    private $id = null;


    /**
     * Block lang.
     *
     * @var mixed
     */
    private $lang = null;


    /**
     * Block package name.
     *
     * @var string
     */
	private $packageName = null;


	/**
	 * Block type.
	 *
	 * @var string
	 */
	private $type = null;


	/**
	 * Block controller.
	 *
	 * @var block_BlockController
	 */
	private $controller = null;


	/**
	 * Block request.
	 *
	 * @var block_BlockRequest
	 */
	private $request = null;


	/**
	 * Block action.
	 *
	 * @var block_BlockAction
	 */
	private $blockAction = null;


	/**
	 * Name of the required view.
	 *
	 * @var string
	 */
	private $viewClassName = null;


	/**
	 * Block view.
	 *
	 * @var block_BlockView
	 */
	private $blockView = null;


	/**
	 * Block module name.
	 *
	 * @var string
	 */
	private $moduleName = null;


	/**
	 * Block parameters.
	 *
	 * @var array
	 */
	protected $parameters = array();

	/**
	 * @var array<f_persistentdocument_PersistentDocument>
	 */
	private $documentsParameter;
	/**
	 * @var f_persistentdocument_PersistentDocument
	 */
	private $documentParameter;
	
	/**
	 * @var String
	 */
	private $viewModuleName;

	/**
	 * @param mixed $id
	 */
	private function __construct($id)
	{
		$this->id = $id;
	}

	/**
	 * Retrieve a new block_BlockHandler instance
	 *
	 * @param mixed $id
	 * @return block_BlockHandler
	 */
	public static function getNewInstance($id)
	{
		return new block_BlockHandler($id);
	}

	/**
	 * Load the current block Action.
	 *
	 * @throws BlockException if the block the class was not found
	 */
	private function loadAction()
	{
	    if (!$this->blockAction)
	    {    
    	    try
    	    {
    	        $cmpIdParam = $this->getParameter(K::COMPONENT_ID_ACCESSOR);
    	    	if (is_array($cmpIdParam) && (count($cmpIdParam) > 1))
    	    	{
    	    	    $oldType = $this->getType();
    	    		$this->setType($oldType . 'List');
   	    		
    	    		$classListeName  = $this->getBlockClassName();
    	    		if (!f_util_ClassUtils::classExists($classListeName))
    	    		{
    	    		    $this->setType($oldType);
    	    		}
    	    	}
    	    	
    	    	$className = $this->getBlockClassName();
    	    	ClassLoader::getInstance()->load($className);
    	    	$this->blockAction = new $className($this);

    	    }
    	    catch (ClassNotFoundException $e)
    	    {
    	  		Framework::warn(__METHOD__ . ':' . $_SERVER["REQUEST_URI"]);
    	    	Framework::exception($e);
    	    	throw new BlockException(sprintf('Cannot load Block Action %s_%s : related class %s not found',
    	    	$this->getPackageName(),
    	    	$this->getType(),
    	    	$className));
    	    }
	    }
	}
	
	public function setSpecificationsArray($blockSpecs)
	{
		if (isset($blockSpecs['package']))
		{
			$this->setPackageName($blockSpecs['package']);
		}

		if (isset($blockSpecs['name']))
		{
			$this->setType($blockSpecs['name']);
		}
		else if (isset($blockSpecs['type']))
		{
			$this->setType($blockSpecs['type']);
		}

		if (isset($blockSpecs['lang']))
		{
			$this->setLang($blockSpecs['lang']);
		}

		if (isset($blockSpecs['parameters']))
		{
			foreach ($blockSpecs['parameters'] as $name => $value)
			{
				$this->setParameter($name, $value);
			}
		}
	}
	
	/**
	 * @return String
	 */
	private function getBlockClassName()
	{
		return $this->getModuleName().'_Block'.ucfirst($this->getType()).'Action';
	}
	
	/**
	 * @return block_BlockAction
	 */
	public function getBlockAction()
	{
		$this->loadAction();
		return $this->blockAction;
	}

	/**
	 * @return block_BlockView
	 */
	public function getBlockView()
	{
		if (!$this->hasViewName())
		{
			return null;
		}
		if (!$this->blockView)
	    {
    	    $className = $this->getViewName();
    	    if (ClassLoader::getInstance()->exists($className))
    	    {
    	    	$this->blockView = new $className($this);
    	    }
    	    else
    	    {
    	    	Framework::info("$className block view does not exists, using block_SimpleBlockView");
    	    	$this->blockView = new block_SimpleBlockView($this);
    	    	$this->blockView->setViewClassName($className);
    	    }
	    }
		return $this->blockView;
	}

	/**
	 * Initialize the block with the given controller.
	 *
	 * @param block_BlockController $controller
	 */
	public function initialize($controller)
	{
	    $this->controller = $controller;
        $this->request = block_BlockRequest::getNewInstance();
        $this->request->initialize($this);
        $this->loadAction();

        if ($this->blockAction)
	    {
	        $requestContext = RequestContext::getInstance();
	        try
	        {
	        	$requestContext->beginI18nWork($this->getLang());
	        	$this->blockAction->initialize($this->getContext(), $this->getRequest());
	        	$requestContext->endI18nWork();
	        }
	        catch (Exception $e)
	        {
	        	$requestContext->endI18nWork($e);
	        }
	    }
	}

	/**
	 * Execute the block Action and set the resulting view.
	 */
	public function doAction()
	{
	    if ($this->blockAction && $this->isActive())
	    {  
	        $requestContext = RequestContext::getInstance();
	        try
	        {
	        	$requestContext->beginI18nWork($this->getLang());
	        	if ($this->getContext()->inBackofficeMode() && f_util_ClassUtils::methodExists($this->blockAction, 'executeBackOffice'))
	        	{
	        	    $this->setViewName($this->blockAction->executeBackOffice($this->getContext(), $this->getRequest()));
	        	}
	        	else
	        	{
	        	   	$this->setViewName($this->blockAction->execute($this->getContext(), $this->getRequest()));
	        	}
    	    	$requestContext->endI18nWork();
	        }
	        catch (Exception $e)
	        {
	        	$requestContext->endI18nWork($e);
	        }
	    }
	}

	/**
	 * Execute the block View and return its content.
	 *
	 * @return string (null if no view)
	 */
	public function doView()
	{
		if ($this->hasViewName())
		{
			$blockView = $this->getBlockView();

			if ($blockView)
			{
				$requestContext = RequestContext::getInstance();

				try
				{
					$requestContext->beginI18nWork($this->getLang());
					$blockView->initialize($this->getContext(), $this->getRequest());

					if ($this->isVisible())
					{
						$blockView->execute($this->getContext(), $this->getRequest());
						$viewData = $blockView->render();

						$viewData = preg_replace("/&(\w+)=/i", '&amp;$1=', $viewData);
						
						$requestContext->endI18nWork();
						return $viewData;
					}
					$requestContext->endI18nWork();
				}
				catch (Exception $e)
				{
					$requestContext->endI18nWork($e);
				}
			}
		}

		return null;
	}

	/**
	 * Retrieve the block ID.
	 *
	 * @return mixed
	 */
	public function getId()
	{
	    return $this->id;
	}

	/**
	 * Set the block lang.
	 */
	public function setLang($lang)
	{
	    $this->lang = $lang;
	}

	/**
	 * Retrieve the block lang.
	 *
	 * @return mixed
	 */
	public function getLang()
	{
	    if (is_null($this->lang))
	    {
	        $this->lang = RequestContext::getInstance()->getLang();
	    }

	    return $this->lang;
	}

	/**
	 * Retrieve the current block controller.
	 *
	 * @return block_BlockController
	 */
	public function getController()
	{
	    return $this->controller;
	}

	/**
	 * Retrieve the current block context.
	 *
	 * @return block_BlockContext
	 */
	public function getContext()
	{
	    return $this->controller->getContext();
	}

	/**
	 * Set the current block context.
	 *
	 * @param block_BlockContext $context
	 * @return block_BlockController
	 */
	public function setContext($context)
	{
	    return $this->controller->setContext($context);
	}

	/**
	 * Retrieve the GLOBAL context.
	 *
	 * @return Context
	 */
	public function getGlobalContext()
	{
	    return $this->controller->getGlobalContext();
	}

	/**
	 * Retrieve the current block request.
	 *
	 * @return block_BlockRequest
	 */
	public function getRequest()
	{
	    return $this->request;
	}

	/**
	 * Retrieve the current block request for CACHING PURPOSE.
	 *
	 * @return array
	 */
	public function getRequestForCache()
	{
	    return $this->request->getParameters();
	}

	/**
	 * Set the current block request.
	 *
	 * @param block_BlockRequest $request
	 * @return block_BlockHandler
	 */
	public function setRequest($request)
	{
	    $this->request = $request;

	    return $this;
	}

	/**
	 * Set the current block request from CACHED DATA.
	 *
	 * @param array $request
	 * @return block_BlockRequest
	 */
	public function setRequestFromCache($request)
	{
	    return $this->request->setParameters($request);
	}

	/**
	 * Retrieve the GLOBAL request.
	 *
	 * @return Request
	 */
	public function getGlobalRequest()
	{
	    return $this->controller->getGlobalRequest();
	}

	/**
	 * Indicates whether or not the block is active.
	 *
	 * @return bool true, if the block is active, otherwise false.
	 */
	public function isActive()
	{
	    return $this->blockAction->isActive();
	}

	/**
	 * Indicates whether or not the block is visible.
	 *
	 * @return bool true, if the block is visible, otherwise false.
	 */
	public function isVisible()
	{
	    return $this->blockView->isVisible();
	}

	/**
	 * Indicates whether or not the block Action could be cached.
	 *
	 * @return bool true, if the block Action could be cached, otherwise false.
	 */
	public function isActionCacheEnabled()
	{
		return $this->blockAction->isCacheEnabled();
	}

	/**
	 * Indicates whether or not the block View could be cached.
	 *
	 * @return bool true, if the block View could be cached, otherwise false.
	 */
	public function isViewCacheEnabled()
	{
		return $this->blockView->isCacheEnabled();
	}

	/**
	 * Set the full package name of the block
	 *
	 * @param string $packageName Package name (framework, modules_generic, libs_agavi, ... )
	 * @return block_BlockHandler
	 */
	public function setPackageName($packageName)
	{
		$this->packageName = $packageName;

		$this->setModuleName(substr($packageName, 1 + strrpos($packageName, '_')));

		return $this;
	}

	/**
	 * get the full name of the block (packageName_type)
	 *
	 * @return String
	 */
	public function getName()
	{
		return $this->getPackageName().'_'.$this->getType();
	}

	/**
	 * Get the full package name of the block
	 *
	 * @return string
	 */
	public function getPackageName()
	{
		return $this->packageName;
	}
	
	public function getViewPackageName()
	{
		return "modules_".$this->viewModuleName;
	}

	/**
	 * Set the module name of the block
	 *
	 * @param string $moduleName
	 * @return block_BlockHandler
	 */
	public function setModuleName($moduleName)
	{
		$this->moduleName = $moduleName;
		$this->viewModuleName = $moduleName;

		return $this;
	}

	/**
	 * Get the module name of the block
	 *
	 * @return string
	 */
	public function getModuleName()
	{
		return $this->moduleName;
	}

	/**
	 * @return String
	 */
	public final function getViewModuleName()
	{
		return $this->viewModuleName;
	}
	
	/**
	 * Set the type of the block (example : "menu", "folder", "detail", etc.)
	 *
	 * @param string $type
	 * @return block_BlockHandler
	 */
	public function setType($type)
	{
		$this->type = $type;

		return $this;
	}

	/**
	 * Get the type of the block
	 *
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * Set the view name of the block
	 * @param String $viewName or array(0 => ModuleName, 1 => BlocTypeName, 2 => ViewName)
	 * @return block_BlockHandler
	 */
	public function setViewName($viewName)
	{
	    if (!is_null($viewName))
	    {
    		if (is_array($viewName))
    		{
    			$className = sprintf('%s_Block%s%sView', $viewName[0], ucfirst($viewName[1]), ucfirst($viewName[2]));
    			
    			$this->viewModuleName = $viewName[0];
    		}
    		else if (strpos($viewName, '_Block') === false)
    		{
    			$className = sprintf('%s_Block%s%sView', $this->getModuleName(), ucfirst($this->getType()), $viewName);
    		}
    		else
    		{
    		    $className = $viewName;
    		}

    		$this->viewClassName = $className;
	    }
	    else
	    {
	        $this->viewClassName = null;
	    }

		return $this;
	}
	
	/**
	 * Get the view name of the block
	 *
	 * @return string
	 */
	public function getViewName()
	{
		return $this->viewClassName;
	}

	/**
	 * Indicates whether or not the block has a defined View name.
	 *
	 * @return bool true, if the block has a defined View name, otherwise false.
	 */
	public function hasViewName()
	{
		return (!is_null($this->viewClassName));
	}

	/**
	 * Clear all parameters associated with this request.
	 *
	 * @return block_BlockHandler
	 */
	public function clearParameters ()
	{
		$this->parameters = null;
		$this->parameters = array();

		return $this;
	}

	/**
	 * Retrieve a parameter.
	 *
	 * @param string A parameter name.
	 * @param mixed  A default parameter value.
	 *
	 * @return mixed A parameter value, if the parameter exists, otherwise
	 */
	public function & getParameter ($name, $default = null)
	{
		if (isset($this->parameters[$name]))
		{
			return $this->parameters[$name];
		}
		return $default;
	}

	/**
	 * Retrieve an array of parameter names.
	 *
	 * @return array An indexed array of parameter names.
	 */
	public function getParameterNames ()
	{
		return array_keys($this->parameters);
	}

	/**
	 * Retrieve an array of parameters.
	 *
	 * @return array An associative array of parameters.
	 */
	public function getParameters ()
	{
		return $this->parameters;
	}

	/**
	 * Indicates whether or not a parameter exists.
	 *
	 * @param string A parameter name.
	 *
	 * @return bool true, if the parameter exists, otherwise false.
	 */
	public function hasParameter ($name)
	{
		return isset($this->parameters[$name]);
	}

	/**
	 * Remove a parameter.
	 *
	 * @param string A parameter name.
	 *
	 * @return string A parameter value, if the parameter was removed,
	 *                otherwise null.
	 */
	public function & removeParameter ($name)
	{
		$retval = null;

		if (isset($this->parameters[$name]))
		{
			$retval =& $this->parameters[$name];
			unset($this->parameters[$name]);
		}

		return $retval;
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
	public function setParameter ($name, $value)
	{
		$this->parameters[$name] = $value;

		return $this;
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
	public function setParameterByRef ($name, &$value)
	{
		$this->parameters[$name] =& $value;

		return $this;
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
	public function setParameters ($parameters)
	{
		$this->parameters = array_merge($this->parameters, $parameters);

		return $this;
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
	public function setParametersByRef (&$parameters)
	{
		foreach ($parameters as $key => &$value)
		{
			$this->parameters[$key] =& $value;
		}

		return $this;
	}

	/**
	 * // really useful ?
	 * @return Integer the document id or null if no <code>K::COMPONENT_ID_ACCESSOR</code> parameter
	 */
	public final function getDocumentIdParameter()
	{
		$ids = $this->getDocumentIdsParameter();
		if (!empty($ids))
		{
			return $ids[0];
		}
		return null;
	}

	/**
	 * @return f_persistentdocument_PersistentDocument or null if no <code>K::COMPONENT_ID_ACCESSOR</code> parameter
	 */
	public final function getDocumentParameter()
	{
		if (!is_null($this->documentParameter))
		{
			return $this->documentParameter;
		}
		$docId = intval($this->getDocumentIdParameter());
		if ($docId <= 0)
		{
			return null;
		}
		try 
		{
			$this->documentParameter = f_persistentdocument_DocumentService::getInstance()->getDocumentInstance($docId);
			return $this->documentParameter;
		}
		catch (Exception $e)
		{
			Framework::exception($e);
			return null;
		}		
	}

	/**
	 * Search for document ids parameter as blockHandler parameter.
	 * Fall back to block_BlockRequest if not present
	 * @return array<Integer> the document ids or null if no <code>K::COMPONENT_ID_ACCESSOR</code> parameter
	 */
	public final function getDocumentIdsParameter()
	{
		$ids = $this->getParameter(K::COMPONENT_ID_ACCESSOR);
		if (empty($ids))
		{
			$ids = $this->getRequest()->getParameter(K::COMPONENT_ID_ACCESSOR);
			if (empty($ids)) {return array();}
		}
		return is_array($ids) ? $ids : explode(',', $ids);
	}

	/**
	 * @return array<f_persistentdocument_PersistentDocument> the document id or null if no <code>K::COMPONENT_ID_ACCESSOR</code> parameter
	 */
	public final function getDocumentsParameter()
	{
		if (!is_null($this->documentsParameter))
		{
			return $this->documentsParameter;
		}
		$ids = $this->getDocumentIdsParameter();
		if (empty($ids))
		{
			return null;
		}
		$docs = array();
		$ds = f_persistentdocument_DocumentService::getInstance();
		foreach ($ids as $id)
		{
			$docs[] = $ds->getDocumentInstance($id);
		}
		$this->documentsParameter = $docs;
		return $this->documentsParameter;
	}

	/**
	 * @param array<f_persistentdocument_PersistentDocument> $documents
	 */
	public final function setDocumentsParameter($documents)
	{
		$this->documentsParameter = $documents;
		if (!empty($documents))
		{
			$this->documentParameter = $documents[0];
		}
		else
		{
			$this->documentParameter = null;
		}
		return $this;
	}

	/**
	 * @param f_persistentdocument_PersistentDocument $documents
	 */
	public final function setDocumentParameter($document)
	{
		$this->documentParameter = $document;
		if (!is_null($document))
		{
			$this->documentsParameter = array($document);
		}
		else
		{
			$this->documentsParameter = null;
		}
		return $this;
	}
	
	/**
	 * @return Integer
	 */
	final function getOrder()
	{
		$blockInfo = block_BlockService::getInstance()->getBlockInfo($this->packageName.'_'. $this->type);
		if ($blockInfo == null)
		{
			return 0;
		}
		if ("true" == $blockInfo->getAttribute("afterAll"))
		{
			return -1;
		}
		if ("true" == $blockInfo->getAttribute("beforeAll"))
		{
			return 1;
		}
		return 0;
	}
}