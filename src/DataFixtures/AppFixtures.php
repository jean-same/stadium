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

        $levelArray = [
            "Beginner",
            "Normal",
            "Pro"
        ];

        $assocArray = [];
        $adherentArray = [];
        $accountAdherent = [];
        $accountAssoc = [];

        $nb = 0;
        //$accountsList = [];
        for ($acc = 0; $acc <= 60; $acc++) {
            $account = new Account();
            $hash = $this->passwordHasher->hashPassword($account, "password");

            $account->setEmail($faker->email())
                ->setPassword($hash)
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
            }

        }

        foreach ($accountAdherent as $currentAdherentAccount) {
            $nb++;
            //Creer des profils
            $profil = new Profil;

            $profil->setFirstName($faker->firstName())
                ->setLastName($faker->lastName())
                ->setPicture("pic" . $nb . ".jpeg")
                ->setAccount($currentAdherentAccount)
                //->setAssociation($faker->randomElements($assocArray));
            ;


            $manager->persist($profil);
            $adherentArray[] = $profil;

            $file = new File;

            $file->setPhoneNumber("05646789" . $nb)
                ->setDateOfBirth($faker->dateTimeBetween())
                ->setAddress($faker->region())
                ->setEmergencyContactName($faker->name())
                ->setEmergencyContactPhoneNumber("02457469" . $nb)
                ->setMedicalCertificate("medical" . $nb . ".pdf")
                ->setRulesOfProcedure("rules" . $nb . ".pdf")
                ->setIsPaid(0)
                ->setIsValid(0)
                ->setIsComplete(0)
                ->setProfil($profil);

            $manager->persist($file);
        }

        //dd($adherentArray);

        foreach ($accountAssoc as $key => $currentAssocAccount) {
            $nb++;
            $association = new Association;
            $association->setName($faker->team)
                ->setPresidentFirstName($faker->firstName())
                ->setPresidentLastName($faker->lastName())
                ->setAddress($faker->region())
                ->setPhoneNumber("09648469" . $nb)
                ->setAccount($currentAssocAccount);

            $nbProfilForAssoc = mt_rand(0, count($adherentArray));
            $profilToAddInAsso = $faker->randomElements($adherentArray, $nbProfilForAssoc);

            foreach ($profilToAddInAsso as $currentProfilForAssoc) {
                if (!$currentProfilForAssoc->getAssociation()) {
                    $association->addProfil($currentProfilForAssoc);
                }
            }

            $manager->persist($association);
            $assocArray[] = $association;

            //Creer des events
            $event = new Event;
            $event->setName($faker->title())
                ->setPlace($faker->region())
                ->setStartDate($faker->dateTimeBetween('-1 week', '+1 week'))
                ->setEndDate($faker->dateTimeBetween('-1 week', '+1 week'))
                ->setSchedule($faker->dateTimeBetween())
                ->setMaxParticipants(mt_rand(5, 100))
                ->setAssociation($association);

            $nbProfilForEvent = mt_rand(0, count($adherentArray));
            $profilToAddInEvent = $faker->randomElements($adherentArray, $nbProfilForEvent);

            foreach ($profilToAddInEvent as $currentProfilForEvent) {
                if ($currentProfilForEvent->getAssociation() == $event->getAssociation()) {
                    $event->addProfile($currentProfilForEvent);
                }
            }

            $manager->persist($event);


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
                $testTwo = $event->getMaxParticipants() >= $nbProfilForActivity;
                $testThree = $event->getMaxParticipants() >= count($event->getProfiles()) + $nbProfilForActivity;

                if ($testOne && $testTwo && $testThree) {
                    $activity->addProfile($currentProfilForActivity);
                }
            }

            $manager->persist($activity);

            //Creer des lessons
            $lesson = new Lesson;

            $lesson->setLevel($levelArray[mt_rand(0, count($levelArray) - 1)])
                ->setStartTime($faker->dateTimeBetween('-1 day', '+1 day'))
                ->setEndTime($faker->dateTimeBetween('-1 day', '+1 day'))
                ->setDay(mt_rand(1, 7))
                ->setPlace($faker->region())
                ->setActivity($activity);

            $manager->persist($lesson);
        }

        $manager->flush();
    }
}
