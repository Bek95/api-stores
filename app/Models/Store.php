<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Store
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllStores($filters, $orderBy, $sortBy = null)
    {
        // Initialisation de la requête SQL
        $sql = "SELECT * FROM stores";
        $params = [];

        // Appliquer les filtres si présents
        if (!empty($filters)) {
            $whereClauses = [];
            foreach ($filters as $column => $value) {
                $whereClauses[] = "$column LIKE :$column";
                $params[$column] = "%" . $value . "%"; // Recherche partielle
            }
            $sql .= " WHERE " . implode(" AND ", $whereClauses);
        }

        // Gestion du tri
        if (!empty($orderBy)) {
            $sql .= " ORDER BY $orderBy";
        }

        if (!empty($sortBy)) {
            $sql .= " $sortBy";
        }

        // Préparer et exécuter la requête
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        // Retourner les résultats
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStoreById(int $id)
    {
        try {
            $sql = "SELECT * FROM stores WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id]);

            $store = $stmt->fetch(PDO::FETCH_ASSOC);

            return $store ?: null;
        } catch (\Exception $e) {
            error_log("Erreur SQL: " . $e->getMessage());
            return false;
        }
    }

    public function createStore(array $data)
    {
        $sql = "INSERT INTO stores (name, description, adress, zipcode, city, country, phone_number) VALUES (:name, :description, :adress, :zipcode, :city, :country, :phone_number)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'],
            'adress' => $data['adress'],
            'zipcode' => $data['zipcode'],
            'city' => $data['city'],
            'country' => $data['country'],
            'phone_number' => $data['phone_number']
        ]);

        return $this->db->lastInsertId();
    }

    public function updateStore(int $id, array $data)
    {
        $sql = "UPDATE stores SET ";
        $fields = [];
        $fields[] = "updated_at = now()";
        $params = ['id' => htmlspecialchars($id),];
        // construire dynamiquement la requête en fonction des champs fournis
        foreach ($data as $key => $value) {
            if(in_array($key, ['name', 'description', 'adress', 'zipcode', 'city', ]) ) {
                $fields[] = "$key = :$key";
                $params[$key] = htmlspecialchars($value);
            }
        }

        if (empty($fields)) {
            return false;
        }

        $sql .= implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute($params);
    }

    public function deleteStore(int $id)
    {
        $sql = "DELETE FROM stores WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}