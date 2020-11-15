<?php

namespace App\Command;

use App\DataFixtures\AdvancedAppFixtures;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * This command allows you to load a book from a isbn. If the isbn is valid (= reachable by the used apis), the book is loaded into the database,
 * and automatically added to the file DataFixtures/booksCollection.json.
 *
 * Note: The command accept isbn format with and without dash
 */
class LoadBookCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:load-book';
    protected $appFixtures;

    public function __construct(AdvancedAppFixtures $appFixtures)
    {
        $this->appFixtures = $appFixtures;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addArgument('isbn', InputArgument::REQUIRED, 'book isbn to load')
            ->setDescription('Load a book from a valid isbn.')
            ->setHelp('This command allows you to load a book into the database from a valid isbn. Be')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Book Creator from ISBN',
            '============',
            '',
        ]);

        //Load the book as a data fixture
        $isbn = $input->getArgument('isbn');
        $cleanedIsbn = preg_replace('/-/u', '', $isbn);
        $this->appFixtures->loadBook($cleanedIsbn);

        // Load the booksCollection file
        $path = __DIR__.'/../DataFixtures/booksCollection.json';
        $booksCollection = json_decode(file_get_contents($path));
        // Insert the new book in databooks
        array_push($booksCollection, $cleanedIsbn);

        // Encode and replace the book collection file
        $data = json_encode($booksCollection, JSON_PRETTY_PRINT);
        file_put_contents($path, $data);

        $output->writeln([
            'Book Loaded',
            '============',
            '',
        ]);

        return Command::SUCCESS;
    }
}
