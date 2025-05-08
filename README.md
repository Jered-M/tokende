# Tokende - Site de Transport

![GitHub repo size](https://img.shields.io/github/repo-size/Jered-M/tokende)
![GitHub stars](https://img.shields.io/github/stars/Jered-M/tokende?style=social)
![GitHub license](https://img.shields.io/github/license/Jered-M/tokende)

Tokende est une application web de transport inspirée des plateformes comme Uber. Ce projet est encore en phase de développement initial et vise à fournir une solution de transport rapide et fiable.

## Fonctionnalités actuelles

- **Inscription des utilisateurs** : Les utilisateurs peuvent créer un compte avec un nom d'utilisateur et un mot de passe.
- **Connexion des utilisateurs** : Les utilisateurs peuvent se connecter à leur compte.
- **Interface utilisateur** : Une interface simple et intuitive pour l'inscription et la connexion.
- **Design moderne** : Une mise en page responsive avec un design inspiré des applications modernes.

## Fonctionnalités prévues

- **Gestion des trajets** : Les utilisateurs pourront demander des trajets en spécifiant leur point de départ et leur destination.
- **Interface pour les chauffeurs** : Les chauffeurs pourront accepter ou refuser les demandes de trajet.
- **Suivi en temps réel** : Intégration d'une carte pour suivre les trajets en direct.
- **Paiement en ligne** : Intégration d'un système de paiement sécurisé.
- **Évaluations et commentaires** : Les clients et les chauffeurs pourront s'évaluer mutuellement.

## Modifications récentes
- Ajout de la gestion des publications dans le fichier `profil.php`.
- Amélioration de l'affichage des publications avec des cartes Bootstrap.
- Ajout de la gestion des erreurs pour le téléchargement des photos.
- Ajout de la suppression de l'image de profil par défaut.
- **Ajout d'une carte interactive pour le suivi des taxis** :
  - Intégration de Leaflet pour afficher une carte centrée sur Lubumbashi.
  - Mise à jour dynamique de la position d'un taxi via une API.
  - Chargement des lieux publics (hôpitaux, écoles, pharmacies, restaurants, magasins) à partir de l'API Overpass.
  - Utilisation d'icônes personnalisées pour différents types de lieux.
  - Améliorations visuelles avec des styles CSS.

## Installation

1. Clonez le dépôt GitHub :
   ```bash
   git clone https://github.com/votre-utilisateur/tokende.git
   ```

2. Accédez au répertoire du projet :
   ```bash
   cd tokende
   ```

3. Configurez la base de données :
   - Créez une base de données MySQL nommée `site_likes`.
   - Importez les tables nécessaires en utilisant un fichier SQL (à venir).

4. Configurez le fichier `config/config.php` :
   - Ajoutez vos informations de connexion à la base de données.

5. Lancez un serveur local (par exemple, avec PHP) :
   ```bash
   php -S localhost:8000 -t public
   ```

6. Accédez au site dans votre navigateur :
   ```
   http://localhost:8000
   ```

## Structure du projet

```
/config          # Fichiers de configuration (ex. connexion à la base de données)
/public          # Fichiers accessibles publiquement (ex. pages HTML, CSS, JS)
/src             # Classes PHP (ex. gestion des utilisateurs)
/README.md       # Documentation du projet
```

## Technologies utilisées

- **Backend** : PHP avec PDO pour la gestion de la base de données.
- **Frontend** : HTML, CSS (design responsive).
- **Base de données** : MySQL.

## Contribuer

Les contributions sont les bienvenues ! Si vous souhaitez contribuer, veuillez ouvrir une issue ou soumettre une pull request.

## Licence

Ce projet est sous licence MIT. Vous êtes libre de l'utiliser, de le modifier et de le distribuer.

---
**Note** : Ce projet est encore en phase de développement initial. Certaines fonctionnalités peuvent ne pas être entièrement implémentées.

# Suivi de commande - Lubumbashi

## Description
Cette application permet de suivre la position d'un taxi en temps réel sur une carte interactive et d'afficher des lieux publics tels que des hôpitaux, écoles, pharmacies, restaurants, et magasins dans la région de Lubumbashi.

## Fonctionnalités
- **Carte interactive** : Utilisation de Leaflet pour afficher une carte centrée sur Lubumbashi.
- **Position dynamique du taxi** : Mise à jour en temps réel de la position d'un taxi via une API.
- **Affichage des lieux publics** : Chargement des lieux publics à partir de l'API Overpass.
- **Icônes personnalisées** : Utilisation d'icônes spécifiques pour différents types de lieux (hôpital, école, pharmacie, etc.).

## Mises à jour du jour
1. **Ajout d'icônes personnalisées** :
   - Icônes pour les taxis, hôpitaux, écoles, pharmacies, restaurants, magasins, et un icône par défaut.
2. **Mise à jour dynamique de la position du taxi** :
   - Intégration d'une API pour récupérer la position actuelle du taxi toutes les 3 secondes.
3. **Chargement des lieux publics** :
   - Utilisation de l'API Overpass pour afficher les lieux publics dans une zone géographique spécifique.
4. **Améliorations visuelles** :
   - Ajout de styles CSS pour une meilleure expérience utilisateur.

## Technologies utilisées
- **PHP** : Gestion des sessions et des requêtes API.
- **JavaScript** : Intégration de Leaflet et gestion des mises à jour en temps réel.
- **Leaflet** : Bibliothèque pour la carte interactive.
- **Overpass API** : Chargement des données des lieux publics.
- **HTML/CSS** : Structure et style de la page.

## Instructions
1. Assurez-vous que les dépendances nécessaires (PHP, serveur web) sont installées.
2. Configurez les fichiers `session.php` et `database.php` pour gérer les sessions et la base de données.
3. Lancez le projet sur un serveur local et accédez à la page `localisation.php`.

## Auteur
Jered Minono