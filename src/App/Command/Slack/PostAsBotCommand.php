<?php

/**
 * @author  Masaya Takeda <844196@gmail.com>
 * @license MIT
 */

namespace App\Command\Slack;

use App\Domain\Slack\IncomingWebHookAPI;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * SlackへBotとして投稿するコマンド
 */
class PostAsBotCommand extends Command
{
	/**
	 * {@inheritdoc}
	 */
	protected function configure()
	{
		$this
			->setName('post-as-bot')
			->setDescription('BotとしてSlackへ投稿する')
			->addOption('text', '-t', InputOption::VALUE_REQUIRED, '投稿するテキストを指定する')
			->addOption('username', '-u', InputOption::VALUE_REQUIRED, '投稿者名を指定する')
			->addOption('icon-emoji', '-i', InputOption::VALUE_REQUIRED, '投稿者のアイコンとなる絵文字を指定する')
			->addOption('channel', '-c', InputOption::VALUE_REQUIRED, '投稿するチャンネルを指定する')
			->addOption('pre', '-p', InputOption::VALUE_NONE, 'コードブロックとして投稿する');
	}

	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		if ($input->getOption('text')) {
			$text = $input->getOption('text');
		} else {
			$text = preg_replace('/\e\[[0-9;]*[mK]/', '', file_get_contents('php://stdin'));
		}

		if ($input->getOption('pre')) {
			$text = "```\n" . $text . "```";
		}

		$json = [];
		if ($input->getOption('username')) {
			$json['username'] = $input->getOption('username');
		}
		if ($input->getOption('icon-emoji')) {
			$json['icon_emoji'] = $input->getOption('icon-emoji');
		}
		if ($input->getOption('channel')) {
			$json['channel'] = $input->getOption('channel');
		}

		$result = $this->client()->post(array_merge(['text' => $text], $json));

		if ('ok' !== $result) {
			fputs(STDERR, $result . PHP_EOL);
			exit(1);
		}
	}

	/**
	 * デフォルト値を設定したクライアントを返す
	 *
	 * @throws \RuntimeException
	 * @return IncomingWebHookAPI
	 */
	private function client()
	{
		$apiUrl = getenv('SLACK_INCOMING_WEB_HOOK_API_URL');
		if (false === $apiUrl) {
			throw new \RuntimeException('Incoming WebHook APIのURLが環境変数にセットされていません');
		}

		$initialPayload = [];
		if (false !== getenv('SLACK_USERNAME')) {
			$initialPayload['username'] = getenv('SLACK_USERNAME');
		}
		if (false !== getenv('SLACK_ICON_EMOJI')) {
			$initialPayload['icon_emoji'] = getenv('SLACK_ICON_EMOJI');
		}

		return new IncomingWebHookAPI($apiUrl, $initialPayload);
	}
}
