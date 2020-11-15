<?php

namespace App\DataFixtures;

use App\Entity\Artist;
use App\Entity\ArtistInvolvement;
use App\Entity\Book;
use App\Entity\Category;
use App\Entity\Copy;
use App\Entity\Document;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AdvancedAppFixtures extends Fixture implements FixtureGroupInterface
{
    private $faker;
    private $encoder;
    private $client;

    public function __construct(UserPasswordEncoderInterface $encoder, HttpClientInterface $client, EntityManagerInterface $manager)
    {
        $this->faker = Faker\Factory::create('fr_FR');
        $this->encoder = $encoder;
        $this->client = $client;
        $this->manager = $manager;
    }

    public function load(ObjectManager $manager)
    {
        $path = __DIR__.'/booksCollection.json';
        $booksCollection = json_decode(file_get_contents($path));
        $bookCpt = 0;
        foreach ($booksCollection as $isbn) {
            try {
                $this->loadBook($isbn);
                ++$bookCpt;
            } catch (\Exception $e) {
                echo "this book can't be loaded \n";
                echo "message : {$e->getMessage()} \n";

                continue;
            }
        }
        echo "{$bookCpt} books has been loaded\n";
    }

    public static function getGroups(): array
    {
        return ['group2'];
    }

    /**
     * URL example : https://www.googleapis.com/books/v1/volumes?q=isbn:9781781101032.
     */
    public function loadBook(string $isbn)
    {
        $response = $this->client->request(
            'GET',
            "https://www.googleapis.com/books/v1/volumes?q=isbn:{$isbn}"
        );
        $statusCode = $response->getStatusCode();
        $dataResponse = $response->toArray();
        if (200 == $statusCode && 1 == $dataResponse['totalItems']) {
            $data = $response->toArray()['items'][0]['volumeInfo'];
            $book = new Book();
            $this->hydrateBook($isbn, $book, $data);
        } else {
            throw new \Exception("Bad response from url query | totalitems: {$dataResponse['totalItems']} | isbn: {$isbn}");
        }
    }

    private function hydrateBook(string $isbn, Book $book, $bookData)
    {
        $title = array_key_exists('subtitle', $bookData) ? "{$bookData['title']} - {$bookData['subtitle']}" : "{$bookData['title']}";
        $book
            ->setIsbn($isbn)
            ->setPagesCount($bookData['pageCount'])
            ->setPublisher($bookData['publisher'])
            ->setPublishedDate(new DateTime($bookData['publishedDate']))
            ->setDescription($bookData['description'])
            ->setThumbnailUrl($bookData['imageLinks']['smallThumbnail'])
            ->setAvailableSince($this->faker->dateTimeThisYear)
            ->setTitle($title)
        ;

        foreach ($bookData['categories'] as $categoryName) {
            $category = $this->getCategory($categoryName);
            $book->addCategory($category);
        }

        try {
            $artists = $this->getArtistsFromOpenLibrary($isbn);
        } catch (\Exception $e) {
            $artistRepository = $this->manager->getRepository(Artist::class);
            // no artist found - Trying to find it in the database from google api
            $name = $this->parseName($bookData['authors'][0]);
            $firstName = $name['firstName'];
            $lastName = $name['lastName'];
            $artist = $artistRepository->findOneBy(['firstName' => $firstName, 'lastName' => $lastName]);
            if (empty($artist)) {
                $artist = new Artist();
                $artist->setFirstName($firstName)->setLastName($lastName);
                $this->manager->persist($artist);
            }
            $artists = [$artist];
        }
        $this->manager->persist($book);
        $this->loadCopies($book);

        foreach ($artists as $artist) {
            $this->setArtistInvolvement($book, $artist, ArtistInvolvement::TYPE_AUTHOR);
        }
        $this->manager->flush();
    }

    private function setArtistInvolvement(Document $document, Artist $artist, string $role)
    {
        $inv = new ArtistInvolvement();
        $inv->setArtist($artist);
        $inv->setDocument($document);
        $inv->setType($role);

        $this->manager->persist($inv);
        // $this->manager->flush();
    }

    // Return the category if the category already exist, otherwise create it, persist it into the database, and return it
    private function getCategory($categoryName): Category
    {
        $categoryRepository = $this->manager->getRepository(Category::class);
        $category = $categoryRepository->findOneBy(['name' => ucfirst(strtolower($categoryName))]);
        if (empty($category)) {
            $category = new Category();
            $category->setName($categoryName);
            $this->manager->persist($category);
            //   $this->manager->flush();
        }

        return $category;
    }

    /**
     * To retrieve advanced artists data: https://openlibrary.org/isbn/9782253061205.json
     * Exemple of artist url : https://openlibrary.org/authors/OL107569A.json.
     */
    private function getArtistsFromOpenLibrary(string $isbn): array
    {
        $artistRepository = $this->manager->getRepository(Artist::class);

        $bookOpenLibraryQuery = $this->client->request(
            'GET',
            "https://openlibrary.org/isbn/{$isbn}.json"
        );
        $bookData = $bookOpenLibraryQuery->toArray();
        $artistsArray = [];
        foreach ($bookData['authors'] as $authorUrl) {
            try {
                $artistOpenLibraryQuery = $this->client->request(
                    'GET',
                    "https://openlibrary.org{$authorUrl['key']}.json"
                );
                $artistData = $artistOpenLibraryQuery->toArray();

                $name = $this->parseName($artistData['name']);
                $firstName = $name['firstName'];
                $lastName = $name['lastName'];

                $artist = $artistRepository->findOneBy(['firstName' => $firstName, 'lastName' => $lastName]);
                if (empty($artist)) {
                    $artist = new Artist();
                    $artist
                        ->setFirstName($firstName)
                        ->setLastName($lastName)
                        ->setBirthDate(array_key_exists('birth_date', $artistData) ? $this->parseDate($artistData['birth_date']) : null)
                        ->setDeathDate(array_key_exists('death_date', $artistData) ? $this->parseDate($artistData['death_date']) : null)
                        ->setPresentation(array_key_exists('bio', $artistData) ? $artistData['bio']['value'] : null)
                    ;
                    $this->manager->persist($artist);
                    //  $this->manager->flush();
                }
                $artistsArray[] = $artist;
            } catch (\Exception $e) {
                echo "Bad artists data : {$e->getMessage()}\n";

                continue;
            }
        }

        return $artistsArray;
    }

    private function loadCopies(Document $document)
    {
        for ($k = 0; $k < 4; ++$k) {
            if ($this->faker->boolean(50)) {
                $copy = new Copy();
                $copy->setPhysicalState(Copy::PHYSICAL_STATE[$this->faker->numberBetween(0, 4)]);
                $document->addCopy($copy);
                $document->setAvailability(true);
                $this->manager->persist($copy);
            }
        }
    }

    private function parseName($name): array
    {
        $explodedName = explode(' ', $name);
        $firstName = '';
        $lastName = '';

        switch (count($explodedName)) {
            case 1:
                $firstName = $explodedName[0];
                $lastName = $explodedName[0];

                break;
            case 2:
                $firstName = $explodedName[0];
                $lastName = $explodedName[1];

                break;
            default:
                $lastName = array_pop($explodedName);
                $firstName = join(' ', $explodedName);

                break;
        }

        return ['firstName' => $firstName, 'lastName' => $lastName];
    }

    private function parseDate(string $dateString)
    {
        if (count(explode(' ', $dateString)) > 1) {
            return new DateTime($dateString);
        }

        return DateTime::createFromFormat('Y', $dateString);
    }
}
