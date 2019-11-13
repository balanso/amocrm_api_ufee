<?php
/**
 * amoCRM UnsortedLead model
 */
namespace Ufee\Amo\Models;

use Ufee\Amo\Base\Models\Traits;

class IncomingLead extends \Ufee\Amo\Base\Models\ApiModel
{
  use Traits\LinkedParents, Traits\EntityDetector;

  /**
   * @var string
   */
  protected static $_type = 'incominglead';
  /**
   * @var array
   */
  protected $hidden = [
    'query_hash',
    'service',
  ],
  $writable = [
    'incoming_entities',
    'created_at',
    'incoming_lead_info',
    'pipeline_id',
    'source_uid',
    'source_name',
    'result',
  ];

  /**
   * Model on load
   * @param array $data
   * @return void
   */
  protected function _boot($data = [])
  {
    parent::_boot($data);
    if (isset($data->result)) {
      $this->attributes['result']->entity = null;
    }
    unset(
      $this->attributes['result']->_links
    );

    if (!isset($this->source_name)) {
      $this->source_name = 'source_name';
    }

    if (!isset($this->source_uid)) {
      $this->source_uid = 'source_uid';
    }

    if (!isset($this->source_uid)) {
      $this->source_uid = 'source_uid';
    }

    if (!isset($this->created_at)) {
      $this->created_at = time();
    }

    if (!isset($this->incoming_lead_info)) {
      $this->incoming_lead_info = [
        'form_id'      => 'form_id',
        'form_page'    => 'form_page',
        'ip'           => '127.0.0.1',
        'service_code' => 'service_code',
      ];
    }

  }
  /**
   * Add lead to incoming
   * @return Lead
   */
  public function addLead($lead)
  {
    if (!$lead instanceof Lead) {
      throw new \Exception('Lead must be instance of Ufee\Amo\Models\Lead');
    }

    $data = $lead->getChangedRawApiData();
    unset($data['updated_at']);

    $incoming_entities = $this->incoming_entities;
    $incoming_entities['leads'] = [$data];

    $this->incoming_entities = $incoming_entities;
    return $this;
  }

  /**
   * Add lead to incoming
   * @return Lead
   */
  public function addLeadNote($note)
  {
    if (!$note instanceof Note) {
      throw new \Exception('Note must be instance of Ufee\Amo\Models\Note');
    }

    $data = $note->getChangedRawApiData();
    unset($data['updated_at']);

    if (isset($this->incoming_entities['leads'][0])) {
    	$incoming_entities = $this->incoming_entities;
    	$incoming_entities['leads'][0]['notes'] = [$data];
    }

    $this->incoming_entities = $incoming_entities;
    return $this;
  }

  /**
   * Add contact to incoming
   * @return Lead
   */
  public function addContact($contact)
  {
    if (!$contact instanceof Contact) {
      throw new \Exception('Contact must be instance of Ufee\Amo\Models\Contact');
    }

    $data = $contact->getChangedRawApiData();
    unset($data['updated_at']);

    $incoming_entities = $this->incoming_entities;
    $incoming_entities['contacts'] = [$data];

    $this->incoming_entities = $incoming_entities;
    return $this;
  }

  /**
   * @param $page
   * @return mixed
   */
  public function setFormPage($page)
  {
  	$incoming_lead_info = $this->incoming_lead_info;
  	$incoming_lead_info['form_page'] = $page;
    $this->incoming_lead_info = $incoming_lead_info;
    return $this;
  }

  /**
   * Convert Model to array
   * @return array
   */
  public function toArray()
  {
    $fields                = parent::toArray();
    $fields['result_text'] = '';
    if (isset($this->attributes['result']->id)) {
      $fields['result_text'] = $this->attributes['result']->text;
    }
    unset($fields['result']);
    return $fields;
  }
}
