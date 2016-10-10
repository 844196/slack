<?php

/**
 * @author  Masaya Takeda <844196@gmail.com>
 * @license MIT
 */

namespace App\Command\Slack;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * Slack投稿系コマンドの抽象クラス
 */
abstract class AbstractPostCommand extends Command
{
	/**
	 * {@inheritdoc}
	 */
	protected function configure()
	{
		$this
			->addOption(
				'username',
				'-u',
				InputOption::VALUE_REQUIRED,
				'投稿者名を指定する',
				getenv('SLACK_DEFAULT_USERNAME') ?: ''
			)
			->addOption(
				'channel',
				'-c',
				InputOption::VALUE_REQUIRED,
				'投稿するチャンネルを指定する',
				getenv('SLACK_DEFAULT_CHANNEL') ?: ''
			)
			->addOption(
				'icon-emoji',
				'-i',
				InputOption::VALUE_REQUIRED,
				'投稿者のアイコンとなる絵文字を指定する',
				getenv('SLACK_DEFAULT_ICON_EMOJI') ?: ''
			);
	}
}
