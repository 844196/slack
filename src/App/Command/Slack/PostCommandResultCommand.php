<?php

/**
 * @author  Masaya Takeda <844196@gmail.com>
 * @license MIT
 */

namespace App\Command\Slack;

use App\Domain\Slack\WebAPI;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Slackへコマンドの実行結果を投稿するコマンド
 */
class PostCommandResultCommand extends AbstractPostCommand
{
	use PostCommandTrait;

	/**
	 * {@inheritdoc}
	 */
	protected function configure()
	{
		parent::configure();

		$this
			->setName('post:command-result')
			->setDescription('Slackへコマンドの実行結果を投稿する')
			->addArgument('request-command', InputArgument::REQUIRED, '実行するコマンド');
	}

	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$api = new WebAPI(getenv('SLACK_API_TOKEN'));

		$api
			->chat
			->postMessage(array_merge($this->basePayload($input), [
				'mrkdwn' => true,
				'attachments' => json_encode([
					[
						'mrkdwn_in' => ['fields'],
						'text' => sprintf('%sによってコマンドが実行されました', getenv('USER')),
						'color' => 'warning',
						'fields' => [
							[
								'title' => '実行コマンド',
								'value' => sprintf('```%s```', $input->getArgument('request-command')),
								'short' => false,
							],
							[
								'title' => '開始日時',
								'value' => (new \DateTime)->format('Y-m-d H:i:s'),
								'short' => true,
							],
						],
					],
				]),
			]));

		$output = [];
		$exitCode = 0;
		exec(sprintf('%s 2>&1', $input->getArgument('request-command')), $output, $exitCode);

		$api
			->chat
			->postMessage(array_merge($this->basePayload($input), [
				'mrkdwn' => true,
				'attachments' => json_encode([
					[
						'mrkdwn_in' => ['fields'],
						'text' => sprintf('コマンドが%s終了しました', ((0 === $exitCode) ? '正常' : '異常')),
						'color' => ((0 === $exitCode) ? 'good' : 'danger'),
						'fields' => [
							[
								'title' => '実行コマンド',
								'value' => sprintf('```%s```', $input->getArgument('request-command')),
								'short' => false,
							],
							[
								'title' => '実行結果',
								'value' => sprintf('```%s```', $this->removeANSI(implode("\n", $output))),
								'short' => false,
							],
							[
								'title' => '終了日時',
								'value' => (new \DateTime)->format('Y-m-d H:i:s'),
								'short' => true,
							],
						],
					],
				]),
			]));
	}
}
