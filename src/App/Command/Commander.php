<?php

/**
 * @author  Masaya Takeda <844196@gmail.com>
 * @license MIT
 */

namespace App\Command;

use Symfony\Component\Console\Command\Command;

/**
 * サブコマンドを集約するためのクラス
 */
class Commander
{
	public function all(callable $block)
	{
		$all = [];

		foreach ($this->getClassNames() as $className) {
			$all[] = $block($className);
		}

		return $all;
	}

	private function getClassNames()
	{
		$files = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator(
				dirname(__FILE__),
				\FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::SKIP_DOTS
			)
		);

		$classNames = [];
		foreach ($files as $filePath => $fileInfo) {
			if (!$fileInfo->isFile()) {
				continue;
			}

			$className = preg_replace(['/^.*\/src\/(.+)\.php$/', '/\//'], ['\1', '\\\\'], $filePath);

			if (__CLASS__ === $className) {
				continue;
			}
			if ((new \ReflectionClass($className))->isAbstract()) {
				continue;
			}
			if (!(new \ReflectionClass($className))->isSubclassOf(Command::class)) {
				continue;
			}

			$classNames[] = $className;
		}

		return $classNames;
	}
}
