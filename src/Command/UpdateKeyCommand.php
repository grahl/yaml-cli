<?php

namespace Grasmash\YamlCli\Command;

use Dflydev\DotAccessData\Data;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Class CreateProjectCommand
 *
 * @package Grasmash\YamlCli\Command
 */
class UpdateKeyCommand extends CommandBase
{
    /**
     * {inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('update:key')
            ->setDescription('Update a specific key in a YAML file.')
            ->addArgument(
                'filename',
                InputArgument::REQUIRED
            )
            ->addArgument(
                'key',
                InputArgument::REQUIRED
            )
            ->addArgument(
                'new-key',
                InputArgument::REQUIRED
            );
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return bool
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = $input->getArgument('filename');
        $key = $input->getArgument('key');
        $new_key = $input->getArgument('new-key');
        $yaml_parsed = $this->loadYamlFile($filename);
        if (!$yaml_parsed) {
            // Exit with a status of 1.
            return 1;
        }

        $data = new Data($yaml_parsed);
        if (!$this->checkKeyExists($data, $key)) {
            return 1;
        }

        $value = $data->get($key);
        $data->set($new_key, $value);
        $data->remove($key);

        if ($this->writeYamlFile($filename, $data)) {
            $this->output->writeln("<info>The key '$key' was changed to '$new_key' in $filename.</info>");
        }
    }
}