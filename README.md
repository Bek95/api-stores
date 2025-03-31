# Documentation de l'API - Configuration avec Docker

## Prérequis
Avant de commencer, assurez-vous d'avoir Docker et Docker Compose installés sur votre machine et que le dæmon Docker est bien lancé.

- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/install/)

---

## Démarrer l'application avec Docker

### Étapes

1. **Décompresser le projet** :

   Assurez-vous que tous les fichiers sont bien extraits.

2. **Configurer le fichier `.env`** :

   Le fichier `.env` doit contenir les informations de connexion à la base de données. Voici un exemple de contenu :

   ```env
   DB_NAME=api_database
   DB_USER=api_user
   DB_PASSWORD=your_password
   ```

3. **Lancer les conteneurs** :

   Exécutez la commande suivante à la racine du projet :

   ```sh
   docker-compose up -d
   ```

   Cela démarrera les services suivants en arrière-plan :
    - **PHP-FPM (`php`)** : Exécution du code PHP de l'API.
    - **Nginx (`nginx`)** : Serveur web pour gérer les requêtes HTTP.
    - **MariaDB (`db`)** : Base de données pour stocker les informations des magasins.

---

## Importation des données

Exécutez le fichier `importSQL.SQL` situé à la racine du projet pour initialiser la base de données.

---

4. **Vérifier le bon fonctionnement** :

   Une fois les conteneurs lancés, accédez à :

   ```
   http://localhost:8080/swagger/index.html ou via Postman
   ```
   
   Swagger propose une documentation interactive permettant de tester l'API.

 Pour accéder aux routes protégées, vous devez :
    1. Créer un utilisateur via `/user/create` (email et mot de passe requis).
    2. Vous connecter via `/user/login`.
    3. Récupérer le token d'authentification et l'utiliser pour accéder aux routes protégées.

5. **Arrêter les conteneurs** :

   Pour arrêter l'application, exécutez :

   ```sh
   docker-compose down
   ```

---

## Structure des fichiers Docker

Le fichier `docker-compose.yml` définit trois services principaux :

- **PHP (`php`)** :
    - Exécute l'application PHP via PHP-FPM.
    - Dépend du service de base de données (`db`).
    - Monte les fichiers du projet dans le conteneur.

- **Nginx (`nginx`)** :
    - Sert de proxy et redirige les requêtes HTTP vers PHP-FPM.
    - Expose le port `8080` pour accéder à l'API.

- **MariaDB (`db`)** :
    - Stocke les données de l'application.
    - Utilise les variables d'environnement du fichier `.env`.

### Volumes et Réseau

- **Volume de la base de données** :
    - Les données sont stockées de manière persistante dans un volume Docker (`db_data`).

- **Réseau** :
    - Les services communiquent entre eux via un réseau Docker (`app-network`).

---

## Accéder aux logs

Vous pouvez consulter les logs des services en utilisant les commandes suivantes :

- **PHP-FPM** :
  ```sh
  docker-compose logs -f php
  ```
- **Nginx** :
  ```sh
  docker-compose logs -f nginx
  ```
- **MariaDB** :
  ```sh
  docker-compose logs -f db
  ```


## Exemples d'utilisation de l'API

L'API est accessible à l'adresse :
```
http://localhost:8080
```

### Authentification

- **Création d'un utilisateur** :
  ```http
  POST /user/create
  ```

- **Connexion et obtention d'un token** :
  ```http
  POST /user/login
  ```

### Gestion des magasins

- **Récupérer la liste des magasins** :
  ```http
  GET /stores
  ```

- **Récupérer un magasin par ID** :
  ```http
  GET /stores/{id}
  ```

### Routes protégées (requièrent un token JWT)

- **Créer un magasin** :
  ```http
  POST /stores/create
  ```

- **Modifier un magasin existant** :
  ```http
  PUT /stores/edit/{id}
  ```

- **Supprimer un magasin** :
  ```http
  DELETE /stores/delete/{id}
  ```

---

### Remarque
Si vous rencontrez des problèmes, vérifiez :
- Que Docker et Docker Compose sont bien installés et exécutés.
- Que le fichier `.env` est correctement configuré.
- Que les conteneurs sont bien lancés (`docker ps`).

Bonne utilisation de l'API !

