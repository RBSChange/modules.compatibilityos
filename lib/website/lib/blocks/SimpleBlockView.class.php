<?php
class block_SimpleBlockView extends block_BlockView
{
	/**
	 * @var String
	 */
	private $viewClassName;
    
    public function setViewClassName($viewClassName)
    {
    	$this->viewClassName = $viewClassName;
    }

	/**
	 * Just set template name using ViewClassName provided :
	 * modulename_BlockMydocumentSuccessView => Modulename-BlockMydocumentSuccess
	 * @param block_BlockContext $context
	 * @param block_BlockRequest $request
	 */
    public function execute($context, $request)
    {	
    	preg_match('/'.$this->getModuleName().'_Block(\w+)View/', $this->viewClassName, $result);
		preg_match_all('/[A-Z]+/', $result[1], $result2, PREG_OFFSET_CAPTURE);
				
		$last = array_pop(array_pop($result2));
		$suffix = substr($result[1], $last[1]);		
		$prefix = substr($result[1], 0, $last[1]);
    	
    	$this->setTemplateName(ucfirst($this->getModuleName()) . '-Block-' . $prefix . '-' . $suffix);
    }
}