<?php

namespace App\DataFixtures;

use App\Entity\Account;
use App\Entity\Activity;
use App\Entity\Association;
use App\Entity\Event;
use App\Entity\File;
use App\Entity\Lesson;
use App\Entity\Profil;
use App\Repository\AssociationRepository;
use DateTime;
use Faker\Factory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    protected $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
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
            "Aquagym",
            "Musculation",
            "Volley-ball",
            "Zumba",
            "Tennis",
            "Tennis de table",
            "Théâtre",
            "Pilates",
            "Plongée",
            "Natation",
            "Cardio-training",
            "Course à pied",
            "Chorale",
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
            $hash = $this->passwordHasher->hashPassword($account, "password");

            $account->setEmail($faker->email())
                ->setPassword("password")
                ->setRoles($faker->randomElements($roleArray));

            //$accountsList [] = $account;
            $manager->persist($account);

            foreach ($account->getRoles() as $role) {
                if ($role == "ROLE_ASSOC") {
                    $accountAssoc[] = $account;
                }
                if ($role == "ROLE_ADHERENT") {
                    $accountAdherent[] = $account;
                }
                if ($role == "ROLE_ADMIN") {
                    $accountAdmin[] = $account;
                }
            }
        }

        foreach ($accountAdherent as $currentAdherentAccount) {
            $nb++;
            //Creer des profils
            $profil = new Profil;

            $profil->setFirstName($faker->firstName())
                ->setLastName($faker->lastName())
                ->setPicture("profil" . $nb . ".jpeg")
                ->setAccount($currentAdherentAccount)
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

        foreach ($accountAdmin as $currentAdminAccount) {
            $nb++;
            //Creer des profils
            $profil = new Profil;

            $profil->setFirstName($faker->firstName())
                ->setLastName($faker->lastName())
                ->setPicture("profil" . $nb . ".jpeg")
                ->setAccount($currentAdminAccount)
                //->setAssociation($faker->randomElements($assocArray));
            ;


            $manager->persist($profil);
            $adminArray[] = $profil;

        }

        //dd($adherentArray);

        foreach ($accountAssoc as $key => $currentAssocAccount) {
            $nb++;
            $association = new Association;
            $association->setName($faker->team)
                ->setPresidentFirstName($faker->firstName())
                ->setPresidentLastName($faker->lastName())
                ->setAddress($faker->address())
                ->setPhoneNumber("01" . mt_rand(10, 99) . mt_rand(100, 500) . mt_rand(600, 999))
                ->setAccount($currentAssocAccount);

            $nbProfilForAssoc = mt_rand(0, count($adherentArray));
            $nbAdminForAssoc = mt_rand(0, count($adminArray));
            $profilToAddInAsso = $faker->randomElements($adherentArray, $nbProfilForAssoc);
            $adminToAddInAsso = $faker->randomElements($adminArray, $nbAdminForAssoc);

            foreach ($profilToAddInAsso as $currentProfilForAssoc) {
                if (!$currentProfilForAssoc->getAssociation()) {
                    $association->addProfil($currentProfilForAssoc);
                    $manager->persist($association);
                }
            }

            foreach ($adminToAddInAsso as $currentAdminForAssoc) {
                if (!$currentAdminForAssoc->getAssociation()) {
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

            /* $associationForEvents = $faker->randomElement($assocArray);
            $eventsForAsso = $faker->randomElements($eventCreatedArray, mt_rand(2 , 4 ));

            foreach($eventsForAsso as $currentEvent){
                $associationForEvents->addEvent($currentEvent);
                $manager->persist($association);
            } */

            //Creer des activités
            $activity = new Activity;

            $currentActivity = $activitiesArray[mt_rand(0, count($activitiesArray) - 1)];

            $activity->setName($currentActivity)
                ->setPicture("picActivity" . $nb . ".jpeg")
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

            $startTime = mt_rand(6 , 20);
            $endTime = $startTime + 2;

            $lesson->setLevel($levelArray[mt_rand(0, count($levelArray) - 1)])
                ->setStartTime(new DateTime($startTime . ":00"))
                ->setEndTime(new DateTime($endTime . ":00"))
                ->setDay(mt_rand(1, 7))
                ->setPlace($faker->address())
                ->setActivity($activity);

            /*$nbProfilForLesson = mt_rand(0, count($adherentArray));
            $profilToAddInLesson = $faker->randomElements($adherentArray, $nbProfilForLesson);

            foreach ($profilToAddInLesson as $currentProfilForLesson) {
                $testOne = ($currentProfilForLesson->getAssociation() == $lesson->getActivity()->getAssociation() );

                if ($testOne) {
                    foreach($lesson->getProfiles() as $currentProfil ){
                        if($currentProfil != $currentProfilForLesson ){
                            $lesson->addProfile($currentProfilForLesson);
                        }
                    }

                }
            }*/

            $lessonsCreatedArray [] = $lesson;
            $manager->persist($lesson);

            
            $nbProfilForLesson = mt_rand(0, count($adherentArray));
            $profilToAddInLesson = $faker->randomElements($adherentArray, $nbProfilForLesson);
    
            foreach ($profilToAddInLesson as $currentProfilForLesson) {
    
                    foreach($lessonsCreatedArray as $currentLesson ){
                        $testOne = ($currentProfilForLesson->getAssociation() == $currentLesson->getActivity()->getAssociation() );
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
