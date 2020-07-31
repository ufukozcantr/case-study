<?php

namespace App\Command;

use App\Repository\ToDoRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class Provider1CreateToDoCommand
 * @package App\Command
 */
class Provider1CreateToDoCommand extends Command
{
    /**
     * @var HttpClientInterface
     */
    private $client;
    /**
     * @var ToDoRepository
     */
    private $toDoRepository;

    // the name of the command (the part after "bin/console")
    /**
     * @var string
     */
    protected static $defaultName = 'provider1:create-todo';

    /**
     * Provider1CreateToDoCommand constructor.
     * @param HttpClientInterface $client
     * @param ToDoRepository $toDoRepository
     */
    public function __construct(HttpClientInterface $client, ToDoRepository $toDoRepository)
    {
        $this->client = $client;
        $this->toDoRepository = $toDoRepository;

        // you *must* call the parent constructor
        parent::__construct();
    }

    /**
     *
     */
    protected function configure()
    {
        $this->setDescription('Create To Do List For Provider 1!');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool|int
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $response = $this->client->request(
            'GET',
            'http://www.mocky.io/v2/5d47f235330000623fa3ebf7'
        );
        $content = $response->toArray();
        foreach ($content as $item) {
            $data = [];
            foreach ($item as $key => $item2) {
                $data['level'] = $item2['level'];
                $data['estimatedDuration'] = $item2['estimated_duration'];
                $data['title'] = $key;

                $this->toDoRepository->toDoCreate($data);
            }
        }
        $output->writeln('ToDo Create Success');

        return true;
    }
}