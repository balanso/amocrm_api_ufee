<?php
/**
 * amoCRM API Service method - add
 */
namespace Ufee\Amo\Methods\IncomingLeads;

class IncomingLeadsAdd extends \Ufee\Amo\Base\Methods\Post
{
  /**
   * @var string
   */
  protected $url = '/api/v2/incoming_leads/form';

  /**
   * Add entitys to CRM
   * @param array $raws
   * @param array $arg
   * @return
   */
  public function add($raws, $arg = [])
  {
    return $this->call(['add' => $raws], $arg);
  }
}
