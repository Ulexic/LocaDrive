<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DropReservationsCommand extends Command
{
    protected static $defaultName = 'app:drop-reservations';
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Drops all rows from the reservation table.')
            ->setHelp('This command allows you to delete all rows from the reservation table.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $connection = $this->entityManager->getConnection();
        $sql = 'DELETE FROM "reservation"';

        try {
            $connection->executeStatement($sql);
            $output->writeln('<info>All rows from the reservation table have been deleted.</info>');
        } catch (\Exception $e) {
            $output->writeln('<error>Failed to delete rows: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}