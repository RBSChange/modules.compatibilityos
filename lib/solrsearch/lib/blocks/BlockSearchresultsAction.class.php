<?php

class solrsearch_BlockSearchresultsAction extends block_BlockAction
{
	const DEFAULT_ITEM_PER_PAGE = 10;
	const DEFAULT_SORT_MODE = 'score';
	const DATE_FIELD_NAME = 'sortable_date_idx_dt';

	/**
	 * @var Array
	 */
	private $queryParams = null;

	/**
     * @param website_Page $context
     * @param block_BlockRequest $request
     * @return void
     */
	public function initialize($context, $request)
	{
		// Set sorting default
		if (!$this->hasParameter('itemPerPage'))
		{
			if ($request->hasParameter('rows'))
			{
				$this->setParameter('itemPerPage', $request->getParameter('rows'));
				$request->setParameter('rows', null);
			}
			else
			{
				$this->setParameter('itemPerPage',  self::DEFAULT_ITEM_PER_PAGE);
			}
		}
		// Set sorting default
		if (!$this->hasParameter('sortdefault'))
		{
			$this->setParameter('sortdefault', self::DEFAULT_SORT_MODE);
		}
		// Set current page mode
		if ($request->hasParameter('page'))
		{
			$this->setParameter('currentPage', $request->getParameter('page'));
		}
		else if ($request->hasParameter('start'))
		{
			$this->setParameter('currentPage', (int)$request->getParameter('start')/(int)$this->getParameter('itemPerPage')+1);
			$request->setParameter('start', null);
		}
		else
		{
			$this->setParameter('currentPage', 1);
		}
		// Set current sorting mode
		if ($request->hasParameter('sort'))
		{
			$this->setParameter('sort', $request->getParameter('sort'));
		}
		else
		{
			$this->setParameter('sort', $this->getParameter('sortdefault'));
		}

		if ($context->inBackofficeMode())
		{
			$request->setParameter('terms', $context->getPageDocument()->getLabel());
		}
	}

	/**
     * @param block_BlockContext $context
     * @param block_BlockRequest $request
     * @return String view name
     */
	public function execute($context, $request)
	{
		$displaySuggestions = f_util_Convert::fixDataType($this->getParameter('displaysuggestions', true));
		$this->setParameter('displaysuggestions', $displaySuggestions);	
		if( !$request->hasParameter('terms'))
		{
			return $this->handleInvalidQuery();
		}
		$this->setParameter('hasTerms', true);
		$queryString = trim($request->getParameter('terms'));
		// If a term starts with a wildcard * or ?, bail...
		if (preg_match('/^[?*]/', $queryString != 0 ))
		{
			return $this->handleBadQuery();
		}
		$terms = solrsearch_SolrsearchHelper::getValidTermsFromString($queryString);
		if ( count($terms) == 0)
		{
			return $this->handleEmptyQuery();
		}

		$query =  $this->getStandardQuery($context, $request, $terms);
		$searchResults = indexer_IndexService::getInstance()->search($query);
		if ($displaySuggestions)
		{
			$suggestions = solrsearch_SolrsearchHelper::getSuggestionsForTerms($terms, $this->getLang());
		}
		else 
		{
			$suggestions = array();
		}
		$normalizedTerms = join(' ', $terms);
		$this->setParameter('terms', htmlspecialchars($normalizedTerms));
		// We set the parameters
		if(!is_null($searchResults))
		{
			$currentPage = $this->getParameter('currentPage');
			$itemPerPage = $this->getParameter('itemPerPage');
			$sort = $this->getParameter('sort');
			$totalHitsCount = $searchResults->getTotalHitsCount();
			$pageHitsCount = $searchResults->count();

			$this->setParameter('searchResults', $searchResults);
			$this->setParameter('noHits', $pageHitsCount == 0);
			// Suggestions
			if (count($suggestions) > 0)
			{				
				$this->setParameter('suggestionParams', $this->getSuggestionParams($suggestions));
			}

			// Pagination
			$paginator = new paginator_Paginator('solrsearch', $currentPage, array(),  $itemPerPage);
			$paginator->setPageCount((int)ceil($totalHitsCount/$itemPerPage));
			$paginator->setCurrentPageNumber($currentPage);
			$paginator->setExtraParameters(array('terms' => htmlspecialchars($normalizedTerms), 'sort' => $sort));
			$this->setParameter('paginator', $paginator);

			// Sort Paramters
			$this->setParameter('byScoreParams', $this->getSortByScoreParam($normalizedTerms));
			$this->setParameter('byDateParams', $this->getSortByDateParam($normalizedTerms));
			$this->setParameter('byScore', $sort == 'score' );

			$currentOffset = $searchResults->getFirstHitOffset()+1;
			$header =  f_Locale::translate('&modules.solrsearch.frontoffice.Search-results-header;', array('start' => $currentOffset, 'stop' => $currentOffset + $pageHitsCount-1, 'total' => $totalHitsCount));
			$this->setParameter('resultsHeader', $header);
		}
		return block_BlockView::SUCCESS;
	}

	/**
	 * @return String
	 */
	protected function handleInvalidQuery()
	{
		$this->setParameter('noHits', true);
		$this->setParameter('hasTerms', false);
		return block_BlockView::NONE;
	}

	/**
	 * @return String
	 */
	protected function handleBadQuery()
	{
		$this->setParameter('errorMsg', f_Locale::translate("&modules.solrsearch.frontoffice.Search-error-wildcardstart;"));
		return block_BlockView::ERROR;
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
	 * @param block_BlockContext $context
	 * @param block_BlockRequest $request
	 * @param Array<String> $terms
	 * @return indexer_Query
	 */
	protected function getBaseQuery($context, $request, $terms)
	{
		return solrsearch_SolrsearchHelper::standardTextQueryForTerms($terms);
	}
	
	/**
	 * @param block_BlockContext $context
	 * @param block_BlockRequest $request
	 * @return indexer_Query
	 */
	protected function getFilterQuery($context, $request)
	{
		$filter = indexer_QueryHelper::andInstance();
		$website = website_WebsiteModuleService::getInstance()->getCurrentWebsite();
		if (null !== $website)
		{
			$filter->add(indexer_QueryHelper::websiteIdRestrictionInstance($website->getId()));
		}
		
		$docRestriction =  $this->getDocumentRestriction();
		if (!is_null($docRestriction))
		{
			$filter->add($docRestriction);
		}
		return $filter;
	}
	
	private function getDocumentRestriction()
	{
		if (!$this->hasParameter('module') || !$this->hasParameter('document')  )
		{
			return null;
		}
		return new indexer_TermQuery('documentModel', 'modules_'.$this->getParameter('module').'/'.$this->getParameter('document'));
	}

	private function getSuggestionParams($suggestions)
	{
		return array('solrsearchParam' => array('terms' => htmlspecialchars(join(' ', $suggestions))));
	}

	private function getSortByDateParam($terms)
	{
		return array('solrsearchParam' => array('terms' => $terms, 'sort' => 'date') );
	}

	private function getSortByScoreParam($terms)
	{
		return array('solrsearchParam' =>  array('terms' => $terms, 'sort' => 'score') );
	}

	/**
	 * @param Array $terms
	 * @return indexer_Query
	 */
	private function getStandardQuery($context, $request, $terms)
	{
		$masterQuery = $this->getBaseQuery($context, $request, $terms);
		return $this->setQueryParameters($context, $request, $masterQuery);
	}
	
	/**
	 * @param indexer_Query $masterQuery
	 * @return indexer_Query
	 */
	private function setQueryParameters($context, $request, &$masterQuery)
	{
		$masterQuery->setLang($this->getLang());
		
		$filter = $this->getFilterQuery($context, $request);
		
		if ($filter->getSubqueryCount() > 0)
		{
			$masterQuery->setFilterQuery($filter);
		}

		if ($this->getParameter('sort') == 'date')
		{
			$masterQuery->setSortOnField(self::DATE_FIELD_NAME)->setSortOnField('id');
		}
		else
		{
			$masterQuery->setSortOnField('score')->setSortOnField('id');
		}
		$itemPerPage = (int)$this->getParameter('itemPerPage');
		$currentPage = (int)$this->getParameter('currentPage');
		$masterQuery->setHighlighting(true)
		->setFirstHitOffset($itemPerPage*($currentPage-1))
		->setReturnedHitsCount($itemPerPage);
		return $masterQuery;
	}
}