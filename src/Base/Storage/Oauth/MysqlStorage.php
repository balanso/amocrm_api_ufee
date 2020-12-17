<?php
/**
 * amoCRM API client Oauth handler - MongoDB
 */

namespace Ufee\Amo\Base\Storage\Oauth;

use mysqli;
use Ufee\Amo\Oauthapi;

class MysqlStorage extends AbstractStorage
{
	/**
	 * Constructor
	 * @param array $options
	 * @throws \Exception
	 */
	public function __construct(array $options)
	{
		parent::__construct($options);

		if (empty($this->options['connection']) ||
			!$this->options['connection'] instanceOf mysqli) {
			throw new \Exception('Mysql Storage options[connection] must be instance of \mysqli');
		}


	}

	/**
	 * Init oauth handler
	 * @param Oauthapi $client
	 * @return void
	 * @throws \Exception
	 */
	protected function initClient(Oauthapi $client)
	{
		parent::initClient($client);

		$conn = $this->options['connection'];
		$key = $client->getAuth('domain') . '_' . $client->getAuth('client_id');
		$query = $conn->query("SELECT * FROM oauth WHERE id = '$key'");

		if ($query && $values = $query->fetch_object()) {
			static::$_oauth[$key] = json_decode($values->token, true);

			if ($this->options['check_post_data_duplicate'] && !empty($_POST)) {
				$this->duplicateCheck($values->last_input, $key);
			}
		}
	}

	/**
	 * Set oauth data
	 * @param Oauthapi $client
	 * @param array $oauth
	 * @return bool
	 */
	public function setOauthData(Oauthapi $client, array $oauth)
	{
		parent::setOauthData($client, $oauth);

		$conn = $this->options['connection'];
		$key = $client->getAuth('domain') . '_' . $client->getAuth('client_id');
		$data = json_encode($oauth);

		$statement = "
			INSERT INTO oauth (id, token) VALUES (?, ?)
			ON DUPLICATE KEY UPDATE token=?
			";

		if ($query = $conn->prepare($statement)) {
			$query->bind_param('sss', $key, $data, $data);
			$result = $query->execute();
		} else {
			$result = false;
		}

		return $result;
	}

	protected function duplicateCheck($last_input, $row_key)
	{
		$input_json_data = json_encode($_POST);

		if ($last_input === $input_json_data) {
			exit('Data already recieved');
		}

		$conn = $this->options['connection'];
		$query = $conn->prepare("UPDATE oauth SET last_input=? WHERE id=?");

		if ($query) {
			$query->bind_param('ss', $input_json_data, $row_key);
			$query->execute();
		}
	}
}
