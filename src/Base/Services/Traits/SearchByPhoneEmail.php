<?php
/**
 * amoCRM trait - search entitys by contacts email and phone
 */
namespace Ufee\Amo\Base\Services\Traits;

trait SearchByPhoneEmail
{
  public function searchByPhoneEmail($phone = '', $email = '')
  {

    if (!empty($phone) || !empty($email)) {

      $formatValue = function ($type, $value) {
        if ($type == 'Phone' || $type == 'Телефон') {
          return substr(preg_replace('#[^0-9]+#Uis', '', $value), -10);
        }

        if ($type == 'Email') {
          return mb_strtoupper(trim($value));
        }
      };

      if (!empty($phone)) {
        $field_name                = $this->instance->getAuth('lang') == 'ru' ? 'Телефон' : 'Phone';
        $phone                     = $formatValue($field_name, $phone);
        $searchFields[$field_name] = $phone;
      }

      if (!empty($email)) {
        $email                 = $formatValue('Email', $email);
        $searchFields['Email'] = $email;
      }

      $results = $this->list->where('query', implode(' ', $searchFields))->recursiveCall();

      $results = $results->filter(function ($model) use ($searchFields, $formatValue) {

        if ($this->entity_key === 'leads') {

          if ($model->hasContacts()) {
            $hasNeededContact = false;

            $model->contacts()->recursiveCall()->each(function (&$contact) use ($searchFields, $formatValue, &$hasNeededContact) {

              $hasAllFields = true;

              foreach ($searchFields as $fieldName => $fieldVal) {

                foreach ($contact->cf($fieldName)->getValues() as $value) {
                  if ($fieldVal !== $formatValue($fieldName, $value)) {
                    $hasAllFields = false;
                  }

                }
              }

              if ($hasAllFields) {
                $hasNeededContact = true;
              }

              return false;
            });

            if ($hasNeededContact) {
              return true;
            }
          }

          return false;
        } else {
          $hasAllFields = true;

          foreach ($searchFields as $fieldName => $fieldVal) {
            foreach ($model->cf($fieldName)->getValues() as $value) {
              if ($fieldVal !== $formatValue($fieldName, $value)) {
                return false;
              }
            }
          }

          if ($hasAllFields) {
            return true;
          }
        }

      });

      return $results;
    }

    return [];
  }
};
