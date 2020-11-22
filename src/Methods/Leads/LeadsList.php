<?php
/**
 * amoCRM API Service method - list
 */
namespace Ufee\Amo\Methods\Leads;

class LeadsList extends \Ufee\Amo\Base\Methods\LimitedList
{
	protected 
		$url = '/api/v2/leads';
	
	public function getLastId() {
		if (!empty($this->args->id)) {
			return $this->args->id;
		}
	}
}
