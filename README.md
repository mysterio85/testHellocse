## Test Api Laravel 11

# Installation du projet

`` git clone git@github.com:mysterio85/testHellocse.git ``
``composer install``

### Lancement du serveur 
``php artisan serve``

### Lancement des migrations
``php artisan migrate``

### Chargement des fixture
``php artisan db:seed``

### Compte administrateur de test
``Login: test@example.com  Password: test ``

### Lancement du csfixer
``composer fix``


### Lancement du phpstan
``composer phpstan``


### Lancement des Test unitaires
``php artisan test``


# ROUTES API
| Méthode | Endpoint           | Accès          | Description                                                   |
|---------|--------------------|----------------|---------------------------------------------------------------|
| POST    | /api/login         | Public         | Connexion admin (Sanctum)                                     |
| GET     | /api/profiles      | Public / Admin | Liste des profils actifs (champ statut lorsqu'on est connecté) |
| POST    | /api/profiles      | Admin          | Création d’un profil (avec image)                             |
| PUT     | /api/profiles/{id} | Admin          | Mise à jour d’un profil                                       |
| DELETE  | /api/profiles/{id} | Admin          | Suppression d’un profil                                       |
| POST    | /api/comments      | Admin          | Ajout de commentaire unique par profil                        |
| GET     | /api/me            | Admin          | Détail de l'admin connecté                                    |
| POST    | /api/logout        | Admin          | Déconnexion                                                   |


Collection Postman pour les test :
`` laravel api.postman_collection.json ``

## Login
```
POST /api/login
{
"email": "test@example.com",
"password": "test"
}
```
```
curl --location 'http://localhost:8000/api/login' \
--form 'email="test@example.com"' \
--form 'password="test"'
```

## Création Profil
```
```
```
curl --location 'http://localhost:8000/api/profiles' \
--header 'Accept: application/json' \
--form 'last_name="lastnameDemo"' \
--form 'first_name="test"' \
--form 'image_path=@"/home/essoa/PROJETS/PROJETS path/image/img.png"' \
--form 'status="active"'
```

## Liste des Profils
```
http://localhost:8000/api/profiles
```
```
curl --location 'http://localhost:8000/api/profiles' \
--header 'Accept: multipart/form-data' \
--header 'Authorization: Bearer 1|PcLuJJ0rVkbuC0B1XfYBFknEJytswDlWZZcAwh8J8d528359'
```

## Mise à jour profil
Utilisation de la surchage de méthode 
```
http://localhost:8000/api/profiles/6
```
```
curl --location 'http://localhost:8000/api/profiles/6' \
--header 'Accept: multipart/form-data' \
--header 'Authorization: Bearer 1|PcLuJJ0rVkbuC0B1XfYBFknEJytswDlWZZcAwh8J8d528359' \
--form 'last_name="toto"' \
--form 'first_name="dddd"' \
--form 'image_path=@"/home/essoa/Téléchargements/1.png"' \
--form 'status="pending"' \
--form '_method="PUT"'
```

## suppression de Profil
```
http://localhost:8000/api/profiles/15
```
```
curl --location --request DELETE 'http://localhost:8000/api/profiles/15' \
--header 'Authorization: Bearer 1|PcLuJJ0rVkbuC0B1XfYBFknEJytswDlWZZcAwh8J8d528359'
```

## Mise à jour Commentaire
```
http://localhost:8000/api/comments
```
```
curl --location 'http://localhost:8000/api/comments' \
--header 'Authorization: Bearer 1|PcLuJJ0rVkbuC0B1XfYBFknEJytswDlWZZcAwh8J8d528359' \
--form 'profile_id="3"' \
--form 'text="ceci est un test"'
```


## Logout
```
http://localhost:8000/api/logout
```
```
curl --location --request POST 'http://localhost:8000/api/logout' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer 14|fscEUx4ULTwE7LPHuVfJmRjYS4w7aWMclWlUMRr76e602518'
```

## Me
```
http://localhost:8000/api/me
```
```
curl --location 'http://localhost:8000/api/me' \
--header 'Authorization: Bearer 1|PcLuJJ0rVkbuC0B1XfYBFknEJytswDlWZZcAwh8J8d528359'
```
