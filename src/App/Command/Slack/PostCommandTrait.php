<?php

/**
 * @author  Masaya Takeda <844196@gmail.com>
 * @license MIT
 */

namespace App\Command\Slack;

use Symfony\Component\Console\Input\InputInterface;

/**
 * Slack投稿系コマンドの共通処理をまとめたトレイト
 */
trait PostCommandTrait
{
	/**
	 * InputInterfaceから投稿のためのベースペイロードを返す
	 *
	 * @param InputInterface $input
	 * @return array $basePayload
	 */
	private function basePayload(InputInterface $input)
	{
		$basePayload = [];

		if ($input->getOption('username')) {
			$basePayload['username'] = $input->getOption('username');
		} else {
			$basePayload['as_user'] = true;
		}
		if ($input->getOption('channel')) {
			$basePayload['channel'] = $input->getOption('channel');
		}
		if ($input->getOption('icon-emoji')) {
			$basePayload['icon_emoji'] = $input->getOption('icon-emoji');
		}

		return $basePayload;
	}

	/**
	 * ANSIエスケープシーケンスを取り除く
	 *
	 * @param string $string
	 * @return string
	 */
	private function removeANSI($string)
	{
		return preg_replace('/\e\[[0-9;]*[mK]/', '', $string);
	}
}
