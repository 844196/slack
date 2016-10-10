<?php

/**
 * @author  Masaya Takeda <844196@gmail.com>
 * @license MIT
 */

namespace App\Domain\Slack;

/**
 * WebAPIクラス
 */
class WebAPI
{
	/**
	 * @var string
	 */
	const ENDPOINT = 'https://slack.com/api';

	/**
	 * @var string
	 */
	private $token;

	/**
	 * @var string
	 */
	private $category;

	/**
	 * @param string $token
	 */
	public function __construct($token)
	{
		$this->token = $token;
	}

	/**
	 * プロパティの呼び出しをフックして、カテゴリを設定する
	 *
	 * @param string $category
	 * @return WebAPI 自身
	 */
	public function __get($category)
	{
		$this->category = $category;
		return $this;
	}

	/**
	 * 渡された配列をJSONエンコードしてPOSTする
	 * 実行後にカテゴリは初期化される
	 *
	 * @param string $method
	 * @param array  $arguments
	 * @throws \RuntimeException
	 * @return stdClass $result
	 */
	public function __call($method, array $arguments)
	{
		$apiUrl = sprintf('%s/%s.%s', self::ENDPOINT, $this->category, $method);
		$client = $this->client($apiUrl);

		$payload = $arguments[0];
		$payload['token'] = $this->token;

		curl_setopt_array($client, [
			CURLOPT_POSTFIELDS => http_build_query($payload),
		]);

		$result = json_decode(curl_exec($client), false);
		$errorMessage = curl_error($client);
		curl_close($client);

		if (!$result->ok) {
			if (false !== $result) {
				$errorMessage = json_encode($result);
			}
			throw new \RuntimeException($errorMessage);
		}

		$this->category = null;
		return $result;
	}

	/**
	 * POST用curlを返す
	 *
	 * @param string $apiUrl
	 * @return resource $curlHandler
	 */
	private function client($apiUrl)
	{
		$curlHandler = curl_init();

		curl_setopt_array($curlHandler, [
			CURLOPT_URL => $apiUrl,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
		]);

		return $curlHandler;
	}
}
