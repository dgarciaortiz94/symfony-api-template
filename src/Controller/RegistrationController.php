<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api', name: 'api_')]
class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'register', methods:["POST"])]
    public function __invoke(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent());

        $user = new User();

        $hashedPassword = $passwordHasher->hashPassword($user, $data->plainPassword);

        $user->setUsername($data->username);
        $user->setName($data->name);
        $user->setFirstSurname($data->firstSurname);
        $user->setLastSurname($data->lastSurname);
        $user->setEmail($data->email);
        $user->setPlainPassword($data->plainPassword);
        $user->setRepeatedPassword($data->repeatedPassword);
        $user->setPassword($hashedPassword);
        $user->setActive(true);

        $userErrors = $this->validateUser($user, $validator);

        if ($userErrors) return $userErrors;

        $userRepository->save($user, true);

        return $this->json(['message' => 'Registered Successfully']);
    }


    private function validateUser(User $user, ValidatorInterface $validator): ?JsonResponse
    { 
        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            return $this->json([
                'error' => $errors[0]->getMessage(),
                'field' => $errors[0]->getPropertyPath(),
            ], Response::HTTP_BAD_REQUEST);
        }

        return null;
    }

}
