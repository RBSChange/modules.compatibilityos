<?php
/**
 * @deprecated (will be removed in 4.0)
 */
class website_CheckLinksAction extends f_action_BaseAction
{
	/**
	 * @deprecated (will be removed in 4.0)
	 */
	public function _execute($context, $request)
	{
		$lang = RequestContext::getInstance()->getLang();
		
		if ($request->hasParameter('doSend'))
		{
			$message = sprintf('<html><body><h1 style="font-family: Trebuchet, Arial, sans-serif; font-size: 90%%;">Erreurs détectées :</h1><dl style="font-family: Trebuchet, Arial, sans-serif; font-size: 80%%;">%s</dl></body></html>', str_replace('<dt', '<dt style="color: navy; margin-top: 10px;"', $request->getParameter('message')));
			
			$subject = sprintf("Vérification des liens faite le %s - Rapport d'erreurs.", date_DateFormat::format(date_Calendar::now(), 'l d M Y \à H:i:s'));
			
			$sender = f_Locale::translate('&modules.users.mail.Password.Sender;', array('host' => Framework::getUIDefaultHost()));
			
			try
			{
				$document = ModuleService::getInstance()->getPreferencesDocument('website');
				
				$ms = MailService::getInstance();
				foreach ($document->getCheckersrecipientArray() as $recipient)
				{
					$receiver = sprintf('%s <%s>', f_util_StringUtils::strip_accents($recipient->getFullname()), $recipient->getEmail());
					
					$mgs = $ms->getNewMailMessage();
					
					$mgs->setSubject($subject)->setSender($sender)->setReceiver($receiver)->setEncoding('utf-8')->setHtmlAndTextBody($message, f_util_StringUtils::htmlToText($message));
					
					$ms->send($mgs);
				}
			}
			catch (Exception $e)
			{
				Framework::exception($e);
			}
			
			return self::getSuccessView();
		}
		else if ($request->hasParameter('doCheck'))
		{
			$ids = array();
			
			$submittedIds = $request->getParameter(K::COMPONENT_ID_ACCESSOR);
			
			if (! is_array($submittedIds))
			{
				$submittedIds = array($submittedIds);
			}
			
			foreach ($submittedIds as $id)
			{
				try
				{
					$document = $this->getDocumentService()->getDocumentInstance($id);
					if ($document instanceof website_persistentdocument_page)
					{
						$ids[] = $id;
					}
					elseif ($document instanceof website_persistentdocument_pageexternal)
					{
						$ids[] = $id;
					}
				}
				catch (Exception $e)
				{
					Framework::exception($e);
				}
			}
			
			$id = array_shift(array_unique($ids));
			
			$checking = null;
			
			if ($id)
			{
				$document = $this->getDocumentService()->getDocumentInstance($id);
				$request->setAttribute('message', $document->getLabel());
				$checking = $this->checkLinks($document);
			}
			if (empty($checking))
			{
				return self::getSuccessView();
			}
			else
			{
				$request->setAttribute('contents', sprintf('<contents><![CDATA[<dt id="%d">%s :</dt><dd><ol><li>%s</li></ol></dd>]]></contents>', $id, $this->getPagePath($document), implode('</li><li>', $checking)));
			}
		}
		else
		{
			$ids = array();
			
			if (! $request->hasParameter(K::COMPONENT_ID_ACCESSOR))
			{
				$pp = f_persistentdocument_PersistentProvider::getInstance();
				
				$query = $pp->createQuery('modules_website/page')->add(Restrictions::published());
				$pages = $pp->find($query);
				foreach ($pages as $page)
				{
					if ($page->isLangAvailable($lang))
					{
						$ids[] = $page->getId();
					}
				}
				
				$query = $pp->createQuery('modules_website/pageexternal');
				$pages = $pp->find($query);
				foreach ($pages as $page)
				{
					if ($page->isLangAvailable($lang))
					{
						$ids[] = $page->getId();
					}
				}
			}
			else
			{
				$submittedIds = $this->getDocumentIdArrayFromRequest($request);
				foreach ($submittedIds as $id)
				{
					try
					{
						$ids = array_merge($ids, $this->getPages($this->getDocumentService()->getDocumentInstance($id)));
					}
					catch (Exception $e)
					{
						Framework::exception($e);
					}
				}
			}
			
			$ids = array_unique($ids);
			
			$request->setAttribute('ids', $ids);
			
			return View::INPUT;
		}
		
		return self::getErrorView();
	}
	
	/**
	 * @deprecated (will be removed in 4.0)
	 */
	public function checkLinks($document)
	{
		$errors = array();
		
		$rq = RequestContext::getInstance();
		$host = Framework::getBaseUrl();
		
		if ($document instanceof website_persistentdocument_page)
		{
			$string = $document->getContent();
			
			$linkMatches = array();
			preg_match_all('/<a([^>]+)>/i', $string, $linkMatches, PREG_SET_ORDER);
			
			foreach ($linkMatches as $linkMatch)
			{
				$parsedLink = $linkMatch[0];
				$hrefMatch = array();
				if (preg_match('/cmpref="([^"]+)"/i', $parsedLink, $hrefMatch))
				{
					$langMatch = array();
					if (preg_match('/lang="([^"]+)"/i', $parsedLink, $langMatch))
					{
						$language = strtolower(trim($langMatch[1]));
					}
					else
					{
						$language = $rq->getLang();
					}
					
					$subId = intval($hrefMatch[1]);
					
					$rq->beginI18nWork($language);
					
					try
					{
						$subDocument = DocumentHelper::getDocumentInstance($subId);
						if (! $subDocument->isPublished())
						{
							$begin = strpos($string, '>', strpos($string, $parsedLink));
							$end = strpos($string, '</a>', strpos($string, $parsedLink));
							$linkLabel = substr($string, $begin + 1, $end - $begin - 1);
							if (($subDocument instanceof website_persistentdocument_page) || ($subDocument instanceof website_persistentdocument_pageexternal))
							{
								$subLabel = $this->getPagePath($subDocument);
							}
							else
							{
								$subLabel = $subDocument->getLabel();
							}
							$errors[] = sprintf("Le <em>lien</em> '<strong>%s</strong>' pointe vers un <em>document non publié</em> ('%s').", $linkLabel, $subLabel);
						}
					}
					catch (Exception $e)
					{
						Framework::exception($e);
						
						$begin = strpos($string, '>', strpos($string, $parsedLink));
						$end = strpos($string, '</a>', strpos($string, $parsedLink));
						$linkLabel = substr($string, $begin + 1, $end - $begin - 1);
						$errors[] = sprintf("Le <em>lien</em> '<strong>%s</strong>' pointe vers un <em>document</em> inexistant (n°%d).", $linkLabel, $subId);
					}
					
					$rq->endI18nWork();
				}
				else if (preg_match('/href="([^"]+)"/i', $parsedLink, $hrefMatch))
				{
					if (! preg_match('/^(ftp|http|https):\/\//', $hrefMatch[1]))
					{
						$urlToCheck = $host . '/' . $hrefMatch[1];
					}
					else
					{
						$urlToCheck = $hrefMatch[1];
					}
					
					$headers = get_headers($urlToCheck);
					$header = trim($headers[0]);
					
					if (! $header || (stripos($header, '404 not found') !== false))
					{
						$begin = strpos($string, '>', strpos($string, $parsedLink));
						$end = strpos($string, '</a>', strpos($string, $parsedLink));
						$linkLabel = substr($string, $begin + 1, $end - $begin - 1);
						$errors[] = sprintf("Le <em>lien</em> '<strong>%s</strong>' pointe vers une <em>adresse</em> introuvable ('%s').", $linkLabel, $urlToCheck);
					}
				}
			}
		}
		else if ($document instanceof website_persistentdocument_pageexternal)
		{
			$headers = get_headers($document->getUrl());
			$header = trim($headers[0]);
			
			if (! $header || (stripos($header, '404 not found') !== false))
			{
				$errors[] = sprintf("L'adresse de la <em>page externe</em> est introuvable ('%s').", $document->getUrl());
			}
		}
		
		return $errors;
	}
	
	/**
	 * @deprecated (will be removed in 4.0)
	 */
	public function getPagePath($document)
	{
		$path = sprintf('<a href="%s"><strong>%s</strong></a>', LinkHelper::getDocumentUrl($document), $document->getLabel());
		
		$ps = website_PageService::getInstance();
		$ancestors = $ps->getAncestorsOf($document);
		
		if ($document instanceof website_persistentdocument_pageversion)
		{
			//remove Page
			array_pop($ancestors);
		}
		
		$ancestors = array_reverse($ancestors);
		
		//Remove rootFolder
		array_pop($ancestors);
		
		foreach ($ancestors as $ancestor)
		{
			$path = f_Locale::translate($ancestor->getLabel()) . ' / ' . $path;
		}
		
		if ($document instanceof website_persistentdocument_pageexternal)
		{
			$path .= ' <em>(page externe)</em>';
		}
		
		return $path;
	}
	
	/**
	 * @deprecated (will be removed in 4.0)
	 */
	public function getPages($document)
	{
		$lang = RequestContext::getInstance()->getLang();
		
		$ids = array();
		if (($document instanceof website_persistentdocument_page) || ($document instanceof website_persistentdocument_pageexternal))
		{
			if ($document->isLangAvailable($lang))
			{
				if ($document->getPersistentModel()->useCorrection())
				{
					$correctionOfId = intval($document->getCorrectionofid());
					
					if ($correctionOfId != 0)
					{
						$ids[] = $correctionOfId;
					}
					else
					{
						$ids[] = $document->getId();
					}
				}
				else
				{
					$ids[] = $document->getId();
				}
			}
		}
		elseif (($document instanceof website_persistentdocument_website) || ($document instanceof website_persistentdocument_topic) || ($document instanceof website_persistentdocument_menufolder))
		{
			foreach (TreeService::getInstance()->getInstanceByDocumentId($document->getId())->getChildren() as $subDocument)
			{
				$ids = array_merge($ids, $this->getPages($subDocument->getPersistentDocument()));
			}
		}
		elseif (($document instanceof website_persistentdocument_menu))
		{
			foreach ($document->getMenuitemArray() as $subDocument)
			{
				$ids = array_merge($ids, $this->getPages($subDocument));
			}
		}
		return array_unique($ids);
	}
	
	/**
	 * @deprecated (will be removed in 4.0)
	 */
	public function getRequestMethods()
	{
		return Request::POST | Request::GET;
	}
}