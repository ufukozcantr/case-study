<?php

namespace App\Command;

use App\Repository\DeveloperRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class CreateDeveloperCommand
 * @package App\Command
 */
class CreateDeveloperCommand extends Command
{
    /**
     * @var HttpClientInterface
     */
    private $client;
    /**
     * @var DeveloperRepository
     */
    private $developerRepository;

    // the name of the command (the part after "bin/console")
    /**
     * @var string
     */
    protected static $defaultName = 'developer:create';

    /**
     * CreateDeveloperCommand constructor.
     * @param HttpClientInterface $client
     * @param DeveloperRepository $developerRepository
     */
    public function __construct(HttpClientInterface $client, DeveloperRepository $developerRepository)
    {
        $this->client = $client;
        $this->developerRepository = $developerRepository;

        // you *must* call the parent constructor
        parent::__construct();
    }

    /**
     *
     */
    protected function configure()
    {
        $this->setDescription('Create Developer');
        $this->addArgument('name', InputArgument::REQUIRED, 'Developer Name');
        $this->addArgument('estimatedDuration', InputArgument::REQUIRED, 'Developer Estimated Duration');
        $this->addArgument('level', InputArgument::REQUIRED, 'Developer Level');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool|int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $data['name'] = $input->getArgument('name');
        $data['estimatedDuration'] = $input->getArgument('estimatedDuration');
        $data['level'] = $input->getArgument('level');

        $this->developerRepository->developerCreate($data);

        $output->writeln('Developer Created');

        return true;
    }
}