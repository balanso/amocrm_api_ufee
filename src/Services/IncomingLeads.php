<?php
/**
 * amoCRM API client Tasks service
 */
namespace Ufee\Amo\Services;

class IncomingLeads extends \Ufee\Amo\Base\Services\LimitedList
{
	protected static
		$_require = [
			'add' => ['incoming_entities'],
			'update' => ['id', 'updated_at']
		];
	protected
		$entity_key = 'incomingleads',
		$entity_model = '\Ufee\Amo\Models\IncomingLead',
		$entity_collection = '\Ufee\Amo\Collections\IncomingLeadCollection',
		$cache_time = false;
}
