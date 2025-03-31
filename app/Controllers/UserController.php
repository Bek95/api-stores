<?php

namespace App\Controllers;

use App\Core\JsonResponse;
use App\Models\User;
use App\Utils\JWT;

/**
 *
 *  @OA\Tag(
 *       name="User",
 *       description="Creation et Login d'un utilisateur"
 *   )
 *
 * @OA\Server(
 *     url="http://localhost:8080",
 *     description="Serveur local"
 * )
 */
class UserController
{
    /**
     * @OA\Post(
     *     path="/user/create",
     *     summary="Créer un utilisateur",
     *     description="Permet de créer un utilisateur.",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", description="Email de l'utilisateur"),
     *             @OA\Property(property="password", type="string", description="Mot de passe de l'utilisateur")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Utilisateur créé avec succès"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Données invalides"
     *     )
     * )
     */
    public function register()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        // vérification des champs requis
        if (!isset($data['email']) || !isset($data['password'])) {
            return JsonResponse::error('Veuillez remplir tous les champs !', 400);
        }

        $newUser = new User();
        // vérification que l'email est bien unique
        $existingUser = $newUser->findByEmail($data['email']);
        if (!empty($existingUser)) {
            return JsonResponse::error('Cet email est déja utilisé', 400);
        }

        //Hashage du password
        $password = password_hash($data['password'], PASSWORD_DEFAULT);

        // création du User
        try {
            $newUser->create($data['email'], $password);

            return JsonResponse::success(['message' => 'Utilisateur a été créé avec succès'], 200);

        } catch (\Exception $e) {
            return JsonResponse::error('Erreur lors de la création de l\'utilisateur', 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/user/login",
     *     summary="permet de se connecter",
     *     description="Se connecter et récupérer un token.",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Utilisateur connecté avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Champs manquants ou mauvais identifiants"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur lors de la connexion de l'utilisateur"
     *     )
     * )
     */

    public function login()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $email = $data['email'];
        $password = $data['password'];
        // vérification des champs requis
        if (!isset($email) || !isset($password)) {
            return JsonResponse::error('Email et mot de passe sont requis', 400);
        }

        $user = new User();
        $user = $user->findByEmail($email);
        if (!$user) {
            return JsonResponse::error('Utilisateur non trouvé', 400);
        }

        // Vérification du mot de passe
        if (!password_verify($password, $user['password'])) {
            return JsonResponse::error('Mot de passe incorrect', 401);
        }

        // Générer le token JWT
        $token = JWT::generateToken($user);

        return JsonResponse::success([
            'message' => 'Connexion réussie',
            'token' => $token,
        ]);

    }
}