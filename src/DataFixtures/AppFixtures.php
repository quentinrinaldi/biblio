<?php

namespace App\DataFixtures;

use App\Entity\Artist;
use App\Entity\ArtistInvolvement;
use App\Entity\Book;
use App\Entity\Borrowing;
use App\Entity\Category;
use App\Entity\Copy;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Faker;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture implements FixtureGroupInterface
{
    private $faker;
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->faker = Faker\Factory::create('fr_FR');
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadArtists($manager, 10);
        $artistRepo = $manager->getRepository(Artist::class);
        $artists = $artistRepo->findAll();

        $this->loadUsers($manager, 5);
        $users = $manager->getRepository(User::class)->findAll();

        $this->loadCategories($manager);
        $categories = $manager->getRepository(Category::class)->findAll();

        for ($i = 0; $i < 20; ++$i) {
            $book = new Book();

            //Simple book properties
            $book->setIsbn($this->faker->isbn10);
            $book->setTitle($this->faker->sentence(4));
            $book->setPagesCount($this->faker->randomNumber(3));
            $book->setDescription($this->faker->paragraph);
            $book->setPublishedDate($this->faker->dateTime('now'));
            $book->setPublisher($this->faker->company);
            $book->setAvailableSince($this->faker->dateTimeThisYear);
            //$book->setThumbnailUrl("https://via.placeholder.com/200x250?text=+");
            $book->setThumbnailUrl("https://picsum.photos/id/{$this->faker->numberBetween(0, 1084)}/150/250.jpg");
            $book->setStars($this->faker->numberBetween(1, 5));
            https://picsum.photos/200/250
            if ($this->faker->boolean(30)) {
                $book->setIsPinned(true);
            }

            // $artist->setBirthDate(new DateTime($faker->date('Y-m-d', strtotime("01 January 2000"))));

            // BOOK -> CATEGORIES
            for ($j = 0; $j < 3; ++$j) {
                if ($this->faker->boolean(70)) {
                    $category = $this->faker->randomElement($categories);
                    if (false == array_search($category, $book->getCategories()->toArray())) {
                        $book->addCategory($category);
                    }
                }
            }

            //BOOK -> ARTISTS INVOLVED
            $artist = $this->faker->randomElement($artists);
            $artistInvolvment = new ArtistInvolvement();
            $artistInvolvment->setArtist($artist);
            $artistInvolvment->setDocument($book);
            $artistInvolvment->setType($this->faker->randomElement(ArtistInvolvement::TYPE_ARRAY));

            // BOOK -> COPIES

            for ($k = 0; $k < 4; ++$k) {
                if ($this->faker->boolean(50)) {
                    $copy = new Copy();
                    $copy->setPhysicalState(Copy::PHYSICAL_STATE[$this->faker->numberBetween(0, 4)]);
                    $book->addCopy($copy);
                    $book->setAvailability(true);
                    $manager->persist($copy);
                }
            }

            $manager->persist($book);
            $manager->persist($artistInvolvment);
        }

        $manager->flush();

        $this->loadBorrowings($manager, $users, $manager->getRepository(Book::class)->findAll());
    }

    public static function getGroups(): array
    {
        return ['group1'];
    }

    private function loadArtists(ObjectManager $manager, int $nbItems)
    {
        for ($i = 0; $i < $nbItems; ++$i) {
            $artist = new Artist();

            $artist->setFirstName($this->faker->firstName);
            $artist->setLastName($this->faker->lastName);
            $artist->setPresentation($this->faker->paragraph);
            $artist->setBirthDate(new DateTime($this->faker->date('Y-m-d', strtotime('01 January 2000'))));
            if ($this->faker->boolean(50)) {
                $artist->setDeathDate($this->faker->dateTimeBetween($artist->getBirthDate()->format('Y-m-d'), 'now'));
            }
            $manager->persist($artist);
        }

        $manager->flush();
    }

    private function loadCategories(ObjectManager $manager)
    {
        $categories = ['Fantastic', 'SciFi', 'Comedy', 'Horror', 'Manga', 'Thriller', 'Food', 'Lifestyle', 'Computer Science'];
        foreach ($categories as $categoryName) {
            $category = new Category();
            $category->setName($categoryName);
            $category->setDescription($this->faker->paragraph);
            $manager->persist($category);
        }
        $manager->flush();
    }

    private function loadUsers(ObjectManager $manager, int $nbItems)
    {
        for ($i = 0; $i < $nbItems; ++$i) {
            $user = new User();
            if (0 == $i) {
                $user->setUsername('admin');
                $user->setPassword($this->encoder->encodePassword($user, 'admin'));
                $user->setRoles(['ROLE_ADMIN']);
            } else {
                $user->setUsername($this->faker->userName);
                $user->setPassword($this->encoder->encodePassword($user, 'fakepassword'));
            }
            $user->setPhoneNumber($this->faker->e164PhoneNumber);
            $user->setFirstName($this->faker->firstName);
            $user->setLastName($this->faker->lastName);
            $user->setAddress($this->faker->address);
            $user->setEmail($this->faker->email);
            $manager->persist($user);
        }
        $manager->flush();
    }

    private function loadBorrowings(ObjectManager $manager, array $users, array $books)
    {
        foreach ($users as $user) {
            for ($i = 0; $i < 5; ++$i) {
                if ($this->faker->boolean(80)) {
                    $borrowing = new Borrowing();
                    $borrowing->setUser($user);

                    $book = $this->faker->randomElement($books);
                    if ($book->getAvailability()) {
                        try {
                            $copy = $book->getAvailableCopy();

                            $createdAt = $this->faker->dateTimeThisYear();
                            $borrowing->setCreatedAt($createdAt);

                            $expectedReturnedDate = $this->faker->dateTimeBetween($createdAt, 'now');
                            $borrowing->setExpectedReturnDate($expectedReturnedDate);

                            if ($this->faker->boolean(40)) {
                                $copy->setStatus('BORROWED');
                                $borrowing->setUser($user);
                                $borrowing->setCopy($copy);
                                $borrowing->setStatus('READING_IN_PROGRESS');
                                $book->checkAvailability();
                            } else {
                                $borrowing->setUser($user);
                                $borrowing->setCopy($copy);
                                $borrowing->setReturnedAt($this->faker->dateTimeBetween($createdAt, $expectedReturnedDate));
                                if ($this->faker->boolean(50)) {
                                    $borrowing->setStatus('RETURNED_LATE');
                                } else {
                                    $borrowing->setStatus('RETURNED_OK');
                                }
                            }
                            $manager->persist($borrowing);
                        } catch (Exception $exception) {
                            $book->setAvailability(false);
                        }
                    }
                    $manager->flush();
                }
            }
        }
    }
}
