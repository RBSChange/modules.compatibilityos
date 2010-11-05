<?php
class contactcard_BlockDetailItemView extends block_BlockView
{
	/**
	 * @param block_BlockContext $context
	 * @param block_BlockRequest $request
     * @return void
	 */
	public function execute($context, $request)
	{
		$this->setTemplateName('contact');
		$contact = $this->getParameter('contact');

		if(!is_null($contact->getPhoto()))
		{
			$this->setAttribute('photo',MediaHelper::getUrl($contact->getPhoto()));
		}
		
		if(is_null($contact->getAddress1()) && is_null($contact->getAddress2()))
		{
		    $this->setAttribute('adr', false);
		}
		else
		{
		    if (is_null($contact->getAddress1()))
		    {
		        $address = $contact->getAddress2();
		    } elseif (is_null($contact->getAddress2()))
		    {
		       $address = $contact->getAddress1() ;
		    }
		    else
		    {
		        $address = $contact->getAddress1() . "\n" . $contact->getAddress2();
		    }
			$this->setAttribute('address', f_util_HtmlUtils::nlTobr($address));
			$this->setAttribute('adr',true);			
		}
		
	    if ($contact->getTitle())
	    {
	    	$this->setAttribute('title', $contact->getTitle()->getLabel());
	    }
	    else
	    {
	    	$this->setAttribute('title', false);
	    }
		
		$this->setAttribute('contactform',$this->getParameter('contactform'));
		$this->setAttribute('contactNotPublished',$this->getParameter('contactNotPublished'));
		$this->setAttribute('contactformPageNotPublished',$this->getParameter('contactformPageNotPublished'));

		$this->setAttribute('contact',$contact);
	}
}