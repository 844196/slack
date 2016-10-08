<?php

/**
 * @author  Masaya Takeda <844196@gmail.com>
 * @license MIT
 */

namespace App\Domain\Slack;

/**
 * IncomingWebHookAPIクラス
 */
class IncomingWebHookAPI
{
	/**
	 * @var string
	 */
	private $apiUrl;

	/**
	 * @var array
	 */
	private $initialPayload = [];

	/**
	 * @param string $apiUrl
	 * @param array  $initialPayload
	 */
	public function __construct($apiUrl, array $initialPayload = [])
	{
		$this->apiUrl = $apiUrl;
		$this->initialPayload = $initialPayload;
	}

	/**
	 * 配列をJSONエンコードして投稿する
	 *
	 * @param array $payload
	 * @return string $result
	 */
	public function post(array $payload)
	{
		$payload = array_merge($this->initialPayload, $payload);
		$curlHandler = $this->client();

		curl_setopt_array($curlHandler, [
			CURLOPT_POSTFIELDS => json_encode($payload),
		]);

		$result = curl_exec($curlHandler);
		curl_close($curlHandler);

		return $result;
	}

	/**
	 * POST用curlを返す
	 *
	 * @return resource $curlHandler
	 */
	private function client()
	{
		$curlHandler = curl_init();

		curl_setopt_array($curlHandler, [
			CURLOPT_URL => $this->apiUrl,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
		]);

		return $curlHandler;
	}
}
