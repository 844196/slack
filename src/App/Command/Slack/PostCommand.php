<?php

/**
 * @author  Masaya Takeda <844196@gmail.com>
 * @license MIT
 */

namespace App\Command\Slack;

use App\Domain\Slack\WebAPI;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Slackへ投稿するコマンド
 */
class PostCommand extends AbstractPostCommand
{
	use PostCommandTrait;

	/**
	 * {@inheritdoc}
	 */
	protected function configure()
	{
		parent::configure();

		$this
			->setName('post')
			->setDescription('Slackへ投稿する')
			->addArgument('text', InputArgument::OPTIONAL, '投稿するテキスト')
			->addOption('pre', '-p', InputOption::VALUE_NONE, 'コードブロックとして投稿する');
	}

	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$payload = $this->basePayload($input);

		if ($input->getArgument('text')) {
			$payload['text'] = $input->getArgument('text');
		} else {
			$payload['text'] = $this->removeANSI(file_get_contents('php://stdin'));
		}
		if ($input->getOption('pre')) {
			$payload['text'] = "```\n" . $payload['text'] . "```";
		}

		$api = new WebAPI(getenv('SLACK_API_TOKEN'));
		$result = $api
			->chat
			->postMessage($payload);

		if ($input->getOption('verbose')) {
			$output->writeln(json_encode($result, JSON_PRETTY_PRINT));
		}
	}
}
