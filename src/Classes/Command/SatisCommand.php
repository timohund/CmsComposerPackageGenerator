<?php
/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
namespace TYPO3\Composer\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\Composer\Model\SatisJson;
use Guzzle\Http\Client;
use Webmozart\Json\JsonDecoder;

class SatisCommand extends \Symfony\Component\Console\Command\Command
{

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('satis:json:create')
            ->setDescription('Create a satis.json');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $webroot = realpath(__DIR__ . '/../../../');
        $filePath = $webroot . '/satis.json';

        if (!file_exists($filePath)) {
            touch($filePath);
        }

        if (!is_writable($filePath)) {
            throw new \RuntimeException(sprintf('File "%s" is not writable', $filePath), 1438441994);
        }

        $repositories = array(
              array(
                  'type' => 'composer',
                  'url' => 'file://' . $webroot . '/Web/packages-TYPO3Extensions-archive.json'
              ),
              array(
                  'type' => 'composer',
                  'url' => 'file://' . $webroot . '/Web/packages-TYPO3Extensions-quarter.json'
              )
          );

        $satis = new SatisJson('TYPO3 Extension Repository');
        $satis->setHomepage('https://composer.typo3.org');
        $satis->setRepositories($repositories);
        $satis->requireAll();
        $satis->useProviders();

        if (FALSE === file_put_contents($filePath, (string)$satis)) {
            throw new \RuntimeException(sprintf('File "%s" could not be written, reason unknown', $filePath), 1438442238);
        } else {
            $output->writeln(sprintf('Successfully created "%s"', $filePath));
        }
    }
}
