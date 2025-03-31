<?php

namespace App\Controllers;

use App\Core\JsonResponse;
use App\Models\Store;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(title=" stores API", version="0.1")
 *
 * @OA\Tag(
 *      name="Stores",
 *      description="Gestion des Magasins"
 *  )
 *
 * @OA\Server(
 *      url="http://localhost:8080",
 *      description="Serveur local"
 *  )
 *
 * @OA\SecurityScheme(
 *       securityScheme="BearerToken",
 *       type="http",
 *       scheme="bearer",
 *       bearerFormat="JWT"
 *  )
 */
class StoreController
{

    /**
     * @OA\Get(
     *     path="/stores",
     *     summary="Récupère tous les magasins",
     *     description="Récupère la liste des magasins avec des filtres et options de tri.",
     *     tags={"Stores"},
     *     @OA\Parameter(
     *         name="filter_by",
     *         in="query",
     *         description="Nom de la colonne sur laquelle appliquer le filtre (name, adress, zipcode, city, country, phone_number).",
     *         required=false,
     *         @OA\Schema(type="string", enum={"name", "adress", "zipcode", "city", "country", "phone_number"})
     *     ),
     *     @OA\Parameter(
     *         name="filter_value",
     *         in="query",
     *         description="Valeur à rechercher dans la colonne spécifiée par filter_by.",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="order_by",
     *         in="query",
     *         description="Nom du champ pour trier les magasins.",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Ordre de tri (ASC ou DESC).",
     *         required=false,
     *         @OA\Schema(type="string", enum={"ASC", "DESC"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des magasins récupérée avec succès",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="adress", type="string"),
     *                 @OA\Property(property="zipcode", type="string"),
     *                 @OA\Property(property="city", type="string"),
     *                 @OA\Property(property="country", type="string"),
     *                 @OA\Property(property="phone_number", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur lors de la récupération des magasins"
     *     )
     * )
     */
    public function getStores(): JsonResponse
    {
        $storeModel = new Store();

        $excludedKeys = ['order_by', 'sort_by', 'filter_by', 'filter_value'];
        $filters = [];

        // Gestion des filtres
        $filterBy = $_GET['filter_by'] ?? null;
        $filterValue = $_GET['filter_value'] ?? null;

        // Si filter_by et filter_value sont présents, ajouter un filtre spécifique
        if ($filterBy && $filterValue) {
            $validFilters = ['name', 'adress', 'zipcode', 'city', 'country', 'phone_number'];
            if (!in_array($filterBy, $validFilters)) {
                return JsonResponse::error("Colonne de filtre invalide", 400);
            }
            $filters[$filterBy] = filter_var($filterValue, FILTER_SANITIZE_STRING);
        }

        // Gestion du tri avec validation
        $validColumns = ['id', 'name', 'zipcode', 'city', 'country']; // Colonnes autorisées pour le tri
        $orderBy = $_GET['order_by'] ?? 'id';
        if (!in_array($orderBy, $validColumns)) {
            return JsonResponse::error("Colonne de tri invalide", 400);
        }

        $sortBy = strtoupper($_GET['sort_by'] ?? 'ASC');
        if (!in_array($sortBy, ['ASC', 'DESC'])) {
            return JsonResponse::error("Ordre de tri invalide", 400);
        }

        try {
            // Passer les filtres à la méthode du modèle
            $stores = $storeModel->getAllStores($filters, $orderBy, $sortBy);
            return JsonResponse::success($stores);
        } catch (\Exception $e) {
            return JsonResponse::error("Une erreur est survenue lors de la récupération des magasins : " . $e->getMessage(), 500);
        }
    }



    /**
     * @OA\Get(
     *     path="/stores/{id}",
     *     summary="Récupère un magasin spécifique",
     *     description="Récupère un magasin par son ID.",
     *     tags={"Stores"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du magasin",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Magasin récupéré avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="adress", type="string"),
     *             @OA\Property(property="zipcode", type="string"),
     *             @OA\Property(property="city", type="string"),
     *             @OA\Property(property="country", type="string"),
     *             @OA\Property(property="phone_number", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Magasin non trouvé"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur lors de la récupération du magasin"
     *     )
     * )
     */
    public function getStore(string $id): JsonResponse
    {
        try {
            // Validation de l'ID
            if (empty($id) || !ctype_digit($id)) {
                return JsonResponse::error("ID invalide", 400);
            }

            $storeModel = new Store();
            $id = (int) $id; // Assurer que c'est un entier
            $store = $storeModel->getStoreById($id);

            if (!$store) {
                return JsonResponse::error("Magasin non trouvé", 404);
            }

            return JsonResponse::success($store);
        } catch (\Exception $e) {
            return JsonResponse::error("Une erreur est survenue lors de la récupération du magasin", 500);
        }
    }



    /**
     * @OA\Put(
     *     path="/stores/edit/{id}",
     *     summary="Met à jour un magasin",
     *     description="Met à jour un magasin existant par son ID.",
     *     security={{"BearerToken":{}}},
     *     tags={"Stores"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du magasin",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name", "description", "adress", "zipcode", "city", "country", "phone_number"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="adress", type="string"),
     *             @OA\Property(property="zipcode", type="string"),
     *             @OA\Property(property="city", type="string"),
     *             @OA\Property(property="country", type="string"),
     *             @OA\Property(property="phone_number", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Magasin mis à jour avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Données invalides ou aucune modification"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Magasin non trouvé"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur lors de la mise à jour du magasin"
     *     )
     * )
     */
    public function updateStore(string $id): JsonResponse
    {
        try {
            // Validation de l'ID
            if (empty($id) || !ctype_digit($id)) {
                return JsonResponse::error("ID invalide", 400);
            }

            $data = json_decode(file_get_contents('php://input'), true);
            if (empty($data)) {
                return JsonResponse::error("Données invalides", 400);
            }

            // Vérification si le magasin existe
            $storeModel = new Store();
            $existingStore = $storeModel->getStoreById((int) $id);
            if (!$existingStore) {
                return JsonResponse::error("Magasin non trouvé", 404);
            }

            // Mettre à jour le magasin
            $updated = $storeModel->updateStore((int) $id, $data);
            if ($updated) {
                return JsonResponse::success(["message" => "Magasin mis à jour avec succès"]);
            } else {
                return JsonResponse::error("Aucune modification", 400);
            }
        } catch (\Exception $e) {
            return JsonResponse::error("Une erreur est survenue", 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/stores/create",
     *     summary="Crée un nouveau magasin",
     *     description="Crée un magasin avec les données fournies.",
     *     security={{"BearerToken":{}}},
     *     tags={"Stores"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name", "description", "adress", "zipcode", "city", "country", "phone_number"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="adress", type="string"),
     *             @OA\Property(property="zipcode", type="string"),
     *             @OA\Property(property="city", type="string"),
     *             @OA\Property(property="country", type="string"),
     *             @OA\Property(property="phone_number", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Magasin créé avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Champs requis manquants ou données invalides"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur lors de la création du magasin"
     *     )
     * )
     */
    public function createStore(): JsonResponse
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            if (empty($data)) {
                return JsonResponse::error("Données invalides", 400);
            }

            // Vérification des champs requis
            $requiredFields = ['name', 'description', 'adress', 'zipcode', 'city', 'country', 'phone_number'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    return JsonResponse::error("Champ '$field' requis", 400);
                }
                // Sécuriser les champs avant envoi en base de données
                $data[$field] = filter_var($data[$field], FILTER_SANITIZE_STRING);
            }

            // Création du magasin
            $storeModel = new Store();
            $storeId = $storeModel->createStore($data);

            return JsonResponse::success(["message" => "Magasin créé avec succès", "id" => $storeId], 201);
        } catch (\Exception $e) {
            return JsonResponse::error("Erreur lors de la création du magasin : " . $e->getMessage(), 500);
        }
    }


    /**
     * @OA\Delete(
     *     path="/stores/delete/{id}",
     *     summary="Supprime un magasin",
     *     description="Supprime un magasin par son ID.",
     *     security={{"BearerToken":{}}},
     *     tags={"Stores"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du magasin",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Magasin supprimé avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="ID invalide"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Magasin non trouvé"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur lors de la suppression du magasin"
     *     )
     * )
     */
    public function deleteStore(string $id): JsonResponse
    {
        try {
            // Validation de l'ID
            if (empty($id) || !ctype_digit($id)) {
                return JsonResponse::error("ID invalide", 400);
            }

            $storeModel = new Store();
            // Vérification de l'existence du magasin
            $store = $storeModel->getStoreById((int) $id);
            if (!$store) {
                return JsonResponse::error("Magasin non trouvé", 404);
            }

            // Suppression du magasin
            $deleted = $storeModel->deleteStore((int) $id);
            if ($deleted) {
                return JsonResponse::success(['message' => 'Magasin supprimé avec succès']);
            } else {
                return JsonResponse::error("Erreur lors de la suppression du magasin", 500);
            }
        } catch (\Exception $e) {
            return JsonResponse::error("Une erreur est survenue lors de la suppression du magasin", 500);
        }
    }

}
