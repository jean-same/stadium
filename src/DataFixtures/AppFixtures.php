<?php

namespace App\DataFixtures;

use DateTime;
use Faker\Factory;
use App\Entity\File;
use App\Entity\Event;
use App\Entity\Lesson;
use App\Entity\Profil;
use DateTimeImmutable;
use App\Entity\Account;
use App\Entity\Activity;
use App\Entity\Association;
use Doctrine\Persistence\ObjectManager;
use App\Repository\AssociationRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{

    protected $slugger;
    protected $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher, SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = Factory::create('en_US');
        $faker->addProvider(new \Bezhanov\Faker\Provider\Team($faker));
        $faker->addProvider(new \Faker\Provider\fr_FR\Address($faker));

        $roleArray = [
            "ROLE_SUPER_ADMIN",
            "ROLE_ASSOC",
            "ROLE_ADHERENT",
            "ROLE_ADMIN"
        ];

        $activitiesArray = [
            "Musculation",
            "Volley-ball",
            "Zumba",
            "Tennis",
            "Ping-pong",
            "Pilates",
            "Plongée",
            "Natation",
            "Football",
            "Running",
            "Badminton",
            "Basket-ball",
            "Boxing",
            "Fitness",
        ];

        $eventsArray = [
            "Snooker",
            "Judo - Grand Slam",
            "Tennis - ATP 250",
            "Moto - Moto GP",
            "Tennis de table",
            "Harlem Week",
            "Tournoi u-15",
            "Absolutely Hilarious",
            "Run Experience",
            "Grand Prix d'Amérique",
            "Body Fitness",
            "Opеn Swіm Stаrs",
            "Schneider Electric Marathon",
            "Sport Forum",
            "Sport for a New Generation",
            "Sport & Climate",
            "Future Sports Leaders",
            "Women In Sport",
            "Sport for Development",
            "Mental Health Training",
            "Physical Activity in Teenagers",
            "When Law and Sports Collide",
            "Club Support Workshop",
            "Sports, Nutrition, Health and Wellness",
            "The Right to Play: Women's Football 1921 -2021",
            "Involving Young Volunteers in Sport",
            "Esports Research Network Conference",
            "Football Transfer Forum",
            "Performance Nutrition Workshop",
            "Rider In Balance",
            "Get Fit In Strength Training",
            "Science of Running",
            "Getting in the Game",
            "Tabata-Style HIIT Workout",
            "Les promesses de l’IA en nutrition",
            "Horse and Rider Mindset",
            "Gendered Sporting Bodies",
            "Body Positivity Webinar",
            "Mindful Yoga FREE CLASS",
            "Introducing the Sporting Heritage Toolkit",
            "Team Strong Silvers",
            "Quick HIIT - Fit Tabata",
            "Badminton Horse Trials"
        ];

        $adresses = [
            "adr1" => [
                'address' => '15 Rue de Rungis, Paris 13e Arrondissement, Île-de-France, France',
                'lat' => 48.8219,
                'lng' => 2.3448,
                'codePost' => 75013,
                'city' => 'Paris'
            ],
            "adr2" => [
                'address' => '105 Rue Anatole France, Levallois-Perret, Île-de-France, France',
                'lat' => 48.8928,
                'lng' => 2.28432,
                'codePost' => 92300,
                'city' => 'Levallois-Perret'
            ],
            "adr3" => [
                'address' => '11 Rue Charles Delescluze, Paris 11e Arrondissement, Île-de-France, France',
                'lat' => 48.8525,
                'lng' => 2.38023,
                'codePost' => 75011,
                'city' => 'Paris'
            ],
            "adr4" => [
                'address' => '11 Rue de Paris, Charenton-le-Pont, Île-de-France, France',
                'lat' => 48.8236,
                'lng' => 2.4103,
                'codePost' => 94220,
                'city' => 'Charenton-le-Pont'
            ],
            "adr5" => [
                'address' => '63 Rue de Gra Vur, Bégard, Bretagne, France',
                'lat' => 48.6281,
                'lng' => -3.30514,
                'codePost' => 22140,
                'city' => 'Bégard'
            ],
            "adr6" => [
                'address' => '202 v Rue Abel Villard, Quimper, Bretagne, France',
                'lat' => 48.0016,
                'lng' => -4.1098,
                'codePost' => 29000,
                'city' => 'Quimper'
            ],
            "adr7" => [
                'address' => '85 Rue Jean Moulin, Lille, Hauts-de-France, France',
                'lat' => 50.6423,
                'lng' => 3.05792,
                'codePost' => 59000,
                'city' => 'Lille'
            ],
            "adr8" => [
                'address' => 'aeger, Alfortville, Île-de-France, France',
                'lat' => 48.7898,
                'lng' => 2.4278,
                'codePost' => 94140,
                'city' => 'Alfortville'
            ],
            "adr9" => [
                'address' => '11 Allée Jean Bécot, Vitry-sur-Seine, Île-de-France, France',
                'lat' => 48.8015,
                'lng' => 2.3822,
                'codePost' => 94400,
                'city' => 'Vitry-sur-Seine'
            ],
            "adr10" => [
                'address' => '58 Avenue Charles V, Nogent-sur-Marne, Île-de-France, France',
                'lat' => 48.8317,
                'lng' => 2.4723,
                'codePost' => 94130,
                'city' => 'Nogent-sur-Marne'
            ],
            "adr11" => [
                'address' => '75 Rue Jacques Hébert, Marseille 10e Arrondissement, Provence-Alpes-Côte d\'Azur, France',
                'lat' => 43.2839,
                'lng' => 5.39469,
                'codePost' => 13010,
                'city' => 'Marseille'
            ],
            "adr12" => [
                'address' => '6 Rue Antoine Barbier, Lyon 6e Arrondissement, Auvergne-Rhône-Alpes, France',
                'lat' => 45.7715,
                'lng' => 4.86057,
                'codePost' => 69006,
                'city' => 'Lyon'
            ],
            "adr13" => [
                'address' => '5 Rue Amyot, Paris 5e Arrondissement, Île-de-France, France',
                'lat' => 48.8435,
                'lng' => 2.34728,
                'codePost' => 75005,
                'city' => 'Paris'
            ],
            "adr14" => [
                'address' => '25 Rue du 25 Août 1944, Boulogne-Billancourt, Île-de-France, France',
                'lat' => 48.836,
                'lng' => 2.2333,
                'codePost' => 92100,
                'city' => 'Hauts-de-Seine'
            ],
            "adr15" => [
                'address' => '13 Place du Trocadéro et du 11 Novembre 1918, 75016 Paris, France',
                'lat' => 48.862725,
                'lng' => 2.287592,
                'codePost' => 75016,
                'city' => 'Paris'
            ],
            "adr16" => [
                'address' => '9 Rue des Violettes, 91270 Vigneux-sur-Seine, France',
                'lat' => 48.70111,
                'lng' => 2.424921,
                'codePost' => 91270,
                'city' => 'Vigneux-sur-Seine, '
            ],
            "adr17" => [
                'address' => '17 Boulevard de l\'Espérance, 93220 Gagny, France',
                'lat' => 48.87379,
                'lng' => 2.545427,
                'codePost' => 93220,
                'city' => 'Gagny'
            ],
            "adr18" => [
                'address' => 'Sentier des Fauvains, 60140 Rosoy, France',
                'lat' => 49.33429,
                'lng' => 2.498735,
                'codePost' => 60140 ,
                'city' => 'Rosoy'
            ],
            "adr19" => [
                'address' => 'Le Plan Dore, 60510 Velennes, France',
                'lat' => 49.46954,
                'lng' => 2.194894,
                'codePost' => 60510 ,
                'city' => 'Velennes'
            ],
            "adr20" => [
                'address' => '115 Quai de Valmy, 75010 Paris, France',
                'lat' => 48.87505,
                'lng' => 2.363466,
                'codePost' => 75010 ,
                'city' => 'Paris'
            ],
            "adr21" => [
                'address' => '20 Boulevard Chastenet de Géry, 94270 Le Kremlin-Bicêtre, France',
                'lat' => 48.80473,
                'lng' => 2.357973,
                'codePost' => 94270  ,
                'city' => 'Le Kremlin-Bicêtre'
            ],
            "adr22" => [
                'address' => '44 Rue de l\'Union, 94350 Villiers-sur-Marne, France',
                'lat' => 48.81952,
                'lng' => 2.552637,
                'codePost' => 94350   ,
                'city' => 'Villiers-sur-Marne'
            ],
            "adr23" => [
                'address' => '139 Rue de la Convention, 75015 Paris, France',
                'lat' => 48.83979,
                'lng' => 2.290936,
                'codePost' => 75015 ,
                'city' => 'Paris'
            ],
            "adr24" => [
                'address' => 'La Coulée Verte, 92220 Bagneux, France',
                'lat' => 48.79644,
                'lng' => 2.295496,
                'codePost' => 92220 ,
                'city' => 'Bagneux'
            ]
        ];

        $levelArray = [
            "Beginner",
            "Normal",
            "Pro"
        ];

        $assocArray = [];
        $adherentArray = [];
        $accountAdherent = [];
        $accountAssoc = [];
        $accountAdmin = [];
        $eventCreatedArray = [];
        $lessonsCreatedArray = [];

        $nb = 0;
        //$accountsList = [];
        for ($acc = 0; $acc <= 60; $acc++) {
            $account = new Account();

            $year = 2021;
            $month = mt_rand(1, 12);
            $day = mt_rand(1, 28);

            $datetimeImmutable  = new DateTimeImmutable();
            $joinedAt = $datetimeImmutable->setDate($year, $month, $day);

            $account->setEmail($faker->email())
                ->setPassword("password")
                ->setRoles($faker->randomElements($roleArray))
                ->setIsVerified(0);

            //$accountsList [] = $account;
            $manager->persist($account);

            foreach ($account->getRoles() as $role) {
                if ($role == "ROLE_ASSOC") {
                    $account->setJoinedUsAt($joinedAt);
                    $accountAssoc[] = $account;
                }
                if ($role == "ROLE_ADHERENT") {
                    $accountAdherent[] = $account;
                }
                if ($role == "ROLE_ADMIN") {
                    $accountAdmin[] = $account;
                }
                if ($role == "ROLE_SUPER_ADMIN") {
                    $accountSuperAdmin[] = $account;
                }
            }
        }

        foreach ($accountAdherent as $currentAdherentAccount) {
            $nb++;
            //Creer des profils
            $profil = new Profil;

            $profil->setFirstName($faker->firstName())
                ->setLastName($faker->lastName())
                ->setPicture("pic" . $nb . ".jpg")
                ->setAccount($currentAdherentAccount)
                ->setSlug($this->slugger->slug(strtolower($profil->getLastName()) . '-' . strtolower($profil->getFirstName())))
                //->setAssociation($faker->randomElements($assocArray));
            ;


            $manager->persist($profil);
            $adherentArray[] = $profil;

            $file = new File;

            $file->setPhoneNumber("0" . mt_rand(6, 7) . mt_rand(10, 99) . mt_rand(100, 500) . mt_rand(600, 999))
                ->setDateOfBirth($faker->dateTimeBetween())
                ->setAddress($faker->address())
                ->setEmergencyContactName($faker->name())
                ->setEmergencyContactPhoneNumber("0" . mt_rand(6, 7) . mt_rand(10, 99) . mt_rand(100, 500) . mt_rand(600, 999))
                ->setMedicalCertificate("medical" . $nb . ".pdf")
                ->setRulesOfProcedure("rules" . $nb . ".pdf")
                ->setIsPaid(0)
                ->setIsValid(0)
                ->setIsComplete(0)
                ->setProfil($profil);

            $manager->persist($file);
        }

        foreach ($accountSuperAdmin as $currentSuperAdminAccount) {
            $nb++;
            //Creer des profils
            $profil = new Profil;

            $profil->setFirstName($faker->firstName())
                ->setLastName($faker->lastName())
                ->setPicture("random.jpg")
                ->setAccount($currentSuperAdminAccount)
                ->setSlug($this->slugger->slug(strtolower($profil->getLastName()) . '-' . strtolower($profil->getFirstName())))
                //->setAssociation($faker->randomElements($assocArray));
            ;

            $manager->persist($profil);
        }

        foreach ($accountAdmin as $currentAdminAccount) {
            $nb++;
            //Creer des profils
            $profil = new Profil;

            $profil->setFirstName($faker->firstName())
                ->setLastName($faker->lastName())
                ->setPicture("pic" . $nb . ".jpg")
                ->setAccount($currentAdminAccount)
                ->setSlug($this->slugger->slug(strtolower($profil->getLastName()) . '-' . strtolower($profil->getFirstName())))
                //->setAssociation($faker->randomElements($assocArray));
            ;

            $manager->persist($profil);
            $adminArray[] = $profil;
        }


        //dd($adherentArray);
        $digit = 0;
        foreach ($accountAssoc as $key => $currentAssocAccount) {
            $nb++;
            $digit++;
            $association = new Association;
            $association->setName($faker->team)
                ->setPresidentFirstName($faker->firstName())
                ->setPresidentLastName($faker->lastName())
                ->setAddress($faker->address())
                ->setPhoneNumber("01" . mt_rand(10, 99) . mt_rand(100, 500) . mt_rand(600, 999))
                ->setAccount($currentAssocAccount)
                ->setSlug($this->slugger->slug(strtolower($association->getName())))
                ->setDescription($faker->text(200))
                ->setImage("asso_" . $digit . ".png");
            
            $addressToAdd = $faker->randomElements($adresses);
            $association->setAddress($addressToAdd[0]["address"])
                        ->setLat($addressToAdd[0]["lat"])
                        ->setLng($addressToAdd[0]["lng"])
                        ->setPostCode($addressToAdd[0]["codePost"])
                        ->setCity($addressToAdd[0]["city"])
            ;

            $nbProfilForAssoc = mt_rand(0, count($adherentArray));
            $nbAdminForAssoc = mt_rand(0, count($adminArray));
            $profilToAddInAsso = $faker->randomElements($adherentArray, $nbProfilForAssoc);
            $adminToAddInAsso = $faker->randomElements($adminArray, $nbAdminForAssoc);

            foreach ($profilToAddInAsso as $currentProfilForAssoc) {

                $year = 2022;
                $month = mt_rand(1, 12);
                $day = mt_rand(1, 28);

                $datetimeImmutable  = new DateTimeImmutable();
                $joinedAt = $datetimeImmutable->setDate($year, $month, $day);

                if (!$currentProfilForAssoc->getAssociation()) {
                    $association->addProfil($currentProfilForAssoc);
                    $currentProfilForAssoc->setJoinedAssocAt($joinedAt);
                    $manager->persist($currentProfilForAssoc);
                    $manager->persist($association);
                }
            }

            foreach ($adminToAddInAsso as $currentAdminForAssoc) {

                $year = 2022;
                $month = mt_rand(1, 12);
                $day = mt_rand(1, 28);

                $datetimeImmutable  = new DateTimeImmutable();
                $joinedAt = $datetimeImmutable->setDate($year, $month, $day);

                if (!$currentAdminForAssoc->getAssociation()) {
                    $currentAdminForAssoc->setJoinedAssocAt($joinedAt);
                    $association->addProfil($currentAdminForAssoc);
                    $manager->persist($association);
                }
            }

            $manager->persist($association);
            $assocArray[] = $association;

            $eventNameToAdd = $faker->randomElement($eventsArray);

            //Creer des events
            $event = new Event;
            $event->setName($eventNameToAdd)
                ->setPlace($faker->address())
                ->setStartDate($faker->dateTimeThisMonth())
                ->setEndDate($faker->dateTimeThisMonth('+10 days'))
                ->setSchedule($faker->dateTimeBetween())
                ->setMaxParticipants(mt_rand(5, 100))
                ->setPicture("random.jpg")
                ->setAssociation($association);

            $nbProfilForEvent = mt_rand(0, count($adherentArray));
            $profilToAddInEvent = $faker->randomElements($adherentArray, $nbProfilForEvent);

            $testMaxParticipantOne = $event->getMaxParticipants() >= $nbProfilForEvent;
            $testMaxParticipantTwo = $event->getMaxParticipants() >= count($event->getProfiles()) + $nbProfilForEvent;

            foreach ($profilToAddInEvent as $currentProfilForEvent) {
                if ($currentProfilForEvent->getAssociation() == $event->getAssociation() && $testMaxParticipantOne && $testMaxParticipantTwo) {
                    $event->addProfile($currentProfilForEvent);
                }
            }

            $eventCreatedArray[] = $event;

            $manager->persist($event);

            //Creer des activités
            $activity = new Activity;

            $currentActivity = $activitiesArray[mt_rand(0, count($activitiesArray) - 1)];

            $activity->setName($currentActivity)
                ->setPicture("activity.png")
                ->setAssociation($association);

            $nbProfilForActivity = mt_rand(0, count($adherentArray));
            $profilToAddInActivity = $faker->randomElements($adherentArray, $nbProfilForActivity);

            foreach ($profilToAddInActivity as $currentProfilForActivity) {
                $testOne = ($currentProfilForActivity->getAssociation() == $activity->getAssociation());

                if ($testOne) {
                    $activity->addProfile($currentProfilForActivity);
                }
            }

            $manager->persist($activity);

            //Creer des lessons
            $lesson = new Lesson;

            $startTime = mt_rand(6, 20);
            $endTime = $startTime + 2;

            $lesson->setLevel($levelArray[mt_rand(0, count($levelArray) - 1)])
                ->setStartTime(new DateTime($startTime . ":00"))
                ->setEndTime(new DateTime($endTime . ":00"))
                ->setDay(mt_rand(1, 7))
                ->setPlace($faker->address())
                ->setActivity($activity);


            $lessonsCreatedArray[] = $lesson;
            $manager->persist($lesson);


            $nbProfilForLesson = mt_rand(0, count($adherentArray));
            $profilToAddInLesson = $faker->randomElements($adherentArray, $nbProfilForLesson);

            foreach ($profilToAddInLesson as $currentProfilForLesson) {

                foreach ($lessonsCreatedArray as $currentLesson) {
                    $testOne = ($currentProfilForLesson->getAssociation() == $currentLesson->getActivity()->getAssociation());
                    if ($testOne) {
                        if ($currentLesson != $currentProfilForLesson->getLesson()) {
                            $currentLesson->addProfile($currentProfilForLesson);
                            $manager->persist($currentLesson);
                        }
                    }
                }
            }
        }

        $associationForEvents = $faker->randomElement($assocArray);
        $eventsForAsso = $faker->randomElements($eventCreatedArray, mt_rand(2, 4));

        foreach ($eventsForAsso as $currentEvent) {
            $associationForEvents->addEvent($currentEvent);
            $manager->persist($association);
        }



        $manager->persist($lesson);

        $manager->flush();
    }
}
