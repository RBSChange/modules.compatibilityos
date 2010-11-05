<?php
abstract class solrsearch_BlockSearchresultsBaseAction extends block_BlockAction
{
	const DEFAULT_ITEM_PER_PAGE = 10;
	const DEFAULT_SORT_MODE = 'score';
	const DISPLAY_OTHER = 'others';
	const DISPLAY_ALL = 'all';

	/**
	 * @var String
	 */
	private $sortMode;
	
	/**
	 * @param String $sortMode
	 */
	protected function setSortMode($sortMode)
	{
		$this->sortMode = $sortMode;
	}
	
	/**
	 * @return String
	 */
	protected function getSortMode()
	{
		return $this->sortMode;
	}
	
	/**
	 * @var Integer
	 */
	private $itemPerPage;
	
	/**
	 * @return Integer
	 */
	protected function getItemPerPage()
	{
		return $this->itemPerPage;
	}
	
	/**
	 * @param Mixed $itemPerPage
	 */
	protected function setItemPerPage($itemPerPage)
	{
		$this->itemPerPage = intval($itemPerPage);
	}
	
	/**
	 * @var Integer
	 */
	private $currentPage;
	
	/**
	 * @return Integer
	 */
	protected function getCurrentPage()
	{
		return $this->currentPage;
	}
	
	/**
	 * @param Integer $currentPage
	 */
	protected function setCurrentPage($currentPage)
	{
		$this->currentPage = intval($currentPage);
	}
	
	/**
	 * @param String displaymode
	 */
	private $displaymode;
	
	/**
	 * @return unknown
	 */
	protected function getDisplaymode()
	{
		return $this->displaymode;
	}
	
	/**
	 * @param unknown_type $displaymode
	 */
	protected function setDisplaymode($displaymode)
	{
		$this->setParameter('displayMode', $displaymode);
		$this->displaymode = $displaymode;
	}
	
	/**
	 * Returns the array of values indexed as documentFamily that are handled by this block.
	 * 
	 * @see modules_catalog/product final class getIndexedDocument()
	 * @return array<String>
	 */
	abstract protected function getHandledFamilies();

	/**
     * @param block_BlockContext $context
     * @param block_BlockRequest $request
     * @return void
     */
	public function initialize($context, $request)
	{
		// Set current sorting mode.
		$this->setSortMode($request->getParameter('sort', $this->getParameter('sortdefault', self::DEFAULT_SORT_MODE)));
		
		// Set items per page.
		$this->setItemPerPage($this->getParameter('itemperpage', self::DEFAULT_ITEM_PER_PAGE));
		
		// Set current page mode
		$this->setCurrentPage($request->getParameter('page', 1));
		
		// Set display mode.
		$this->setDisplaymode($request->getParameter('displaymode', self::DISPLAY_ALL));
	}

	/**
	 * @param String $terms
	 * @param block_BlockRequest $request
	 * @return indexer_SearchResults
	 */
	protected function getOtherResults($terms, $request)
	{
		$paginate = $this->getParameter('displayMode') == self::DISPLAY_OTHER;
		$query = solrsearch_SolrsearchHelper::standardTextQueryForTerms($terms);
		$families = $this->getHandledFamilies();
		if (f_util_ArrayUtils::isNotEmpty($families))
		{
			$filter = indexer_QueryHelper::andInstance();
			foreach ($families as $family)
			{
				//TODO: introduce indexer_QueryHelper::notEquals
				$filter->add(indexer_QueryHelper::notInstance(new indexer_TermQuery('documentModel', 'm*'), indexer_QueryHelper::stringFieldInstance('documentFamily', $family)));
			}
			$query->setFilterQuery($filter);
		}		
		$query->setHighlighting(true);
		$this->setQueryParameters($query, $paginate);
		return indexer_IndexService::getInstance()->search($query);
	}

	/**
	 * @return String
	 */
	protected function handleEmptyQuery()
	{
		$this->setParameter('errorMsg', f_Locale::translate("&modules.solrsearch.frontoffice.Search-error-emptyquery;"));
		return block_BlockView::ERROR;
	}

	/**
	 * @param block_BlockRequest $request
	 * @return unknown
	 */
	protected function getShowOthersParams($terms)
	{
		$params = $this->getParameters();
		$params['terms'] = $terms;
		$params['displaymode'] = self::DISPLAY_OTHER;
		return array($this->getModuleName() .'Param' => $params);
	}
	
	/**
	 * @param indexer_Query $masterQuery
	 * @return indexer_Query
	 */
	protected function setQueryParameters(&$masterQuery, $paginate)
	{
		$masterQuery->setLang($this->getLang());
		$masterQuery->setHighlighting(true);
		if ($paginate)
		{
			$itemPerPage = $this->getItemPerPage();
			$currentPage = $this->getCurrentPage();			
			$masterQuery->setFirstHitOffset($itemPerPage*($currentPage-1))->setReturnedHitsCount($itemPerPage);
		}
		return $masterQuery;
	}
}