<?php

namespace App\DataFixtures;

use App\Entity\ShoppingItem;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    public function __construct(
        private readonly UserPasswordHasherInterface $hasher
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadUsers($manager);
        $this->loadShoppingItems($manager);
    }

    private function loadUsers(ObjectManager $manager) :void
    {
        $data =["julien@mail.dev", "benoit@mail.dev"];

        $admin = new User();
        $admin->setEmail("admin@mail.dev");
        $admin->setRoles(["ROLE_ADMIN"]);
        $admin->setPassword($this->hasher->hashPassword($admin,"password"));
        $manager->persist($admin);

        foreach($data as $email){
            $user = new User();
            $user->setEmail($email);
            $user->setPassword($this->hasher->hashPassword($user,"password"));
            $manager->persist($user);
        }
        $manager->flush();
    }

    private function loadShoppingItems(ObjectManager $manager) : void
    {
        $data = ["Pommes", "Coca","Couches bébé", "Poulet", "Yahourt"];

        $users = $manager->getRepository(User::class)->findAll();

        $users = array_filter($users,function($user){
            if(!in_array("ROLE_ADMIN", $user->getRoles())){
                return $user;
            }
        });


        foreach($data as $label){

            $userIndex = array_rand($users,1);
            $item = new ShoppingItem();
            $item->setLabel($label);
            $item->setIsCheck(mt_rand(0,1));
            $item->setUser($users[$userIndex]);

            $manager->persist($item);
        }
        $manager->flush();
    }
}
