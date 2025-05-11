# Tokende - Plateforme de Transport et de Messagerie

![GitHub repo size](https://img.shields.io/github/repo-size/Jered-M/tokende)
![GitHub stars](https://img.shields.io/github/stars/Jered-M/tokende?style=social)
![GitHub license](https://img.shields.io/github/license/Jered-M/tokende)

Tokende est une application web de transport inspirée des plateformes comme Uber. Elle permet aux utilisateurs de réserver des chauffeurs, d'envoyer des messages, de consulter des publications et d'accéder à des fonctionnalités de localisation.

---

## Fonctionnalités actuelles

### 1. **Gestion des utilisateurs**
- Inscription et connexion sécurisées.
- Gestion des sessions pour les utilisateurs connectés.
- Affichage de la photo de profil avec une image par défaut si aucune n'est définie.

### 2. **Réservation**
- Réservation de chauffeurs avec des informations détaillées (nom, marque de voiture, plaque d'immatriculation, couleur, cote moyenne).
- Affichage des chauffeurs triés par leur cote moyenne.
- Intégration d'un bouton "Réserver" pour une interaction rapide.

### 3. **Messagerie**
- Envoi et réception de messages.
- Affichage des conversations avec les derniers messages.
- Badge de notification pour les messages non lus.
- Intégration de liens vers des cartes interactives pour afficher la localisation des clients.

### 4. **Localisation**
- Affichage de la localisation du client et de l'utilisateur sur une carte interactive.
- Intégration avec Leaflet.js pour une expérience cartographique fluide.
- Chargement des lieux publics (hôpitaux, écoles, pharmacies, restaurants, magasins) via l'API Overpass.

### 5. **Publications**
- Affichage des publications récentes avec photos et descriptions.
- Fonctionnalités d'interaction : "J'aime", commenter et partager.
- Chargement des commentaires via AJAX.

### 6. **Interface utilisateur**
- Design moderne et responsive.
- Utilisation de Bootstrap 5 et MDB UI Kit pour un rendu visuel attrayant.
- Navigation intuitive avec une barre latérale et une barre de navigation fixe.

---

## Modifications récentes
- Amélioration de l'affichage des chauffeurs avec des cartes modernes et des badges pour les cotes moyennes.
- Ajout d'une carte interactive pour afficher la localisation des clients et des utilisateurs.
- Intégration d'un badge de notification pour les messages non lus.
- Ajout de la gestion des publications avec des fonctionnalités de "J'aime" et de commentaires.
- Chargement dynamique des lieux publics sur la carte via l'API Overpass.

---

## Installation

1. Clonez le dépôt GitHub :
   ```bash
   git clone https://github.com/Jered-M/tokende.git
   ```

2. Accédez au répertoire du projet :
   ```bash
   cd tokende
   ```

3. Configurez la base de données :
   - Créez une base de données MySQL nommée `site_likes`.
   - Importez les tables nécessaires en utilisant un fichier SQL fourni.

4. Configurez le fichier `config/database.php` :
   - Ajoutez vos informations de connexion à la base de données.

5. Lancez un serveur local :
   ```bash
   php -S localhost:8000 -t public
   ```

6. Accédez au site dans votre navigateur :
   ```
   http://localhost:8000
   ```

---

## Structure du projet

```
/config          # Fichiers de configuration (ex. connexion à la base de données)
/public          # Fichiers accessibles publiquement (ex. pages HTML, CSS, JS)
/uploads         # Dossiers pour les photos de profil et les publications
/src             # Classes PHP (ex. gestion des utilisateurs)
/README.md       # Documentation du projet
```

---

## Technologies utilisées

- **Backend** : PHP avec PDO pour la gestion de la base de données.
- **Frontend** : HTML5, CSS3, JavaScript (AJAX), Bootstrap 5, MDB UI Kit.
- **Base de données** : MySQL.
- **Cartographie** : Leaflet.js et Overpass API.

---

## Contribuer

Les contributions sont les bienvenues ! Si vous souhaitez contribuer :
1. Forkez le dépôt.
2. Créez une branche pour vos modifications :
   ```bash
   git checkout -b feature/nom-de-la-fonctionnalite
   ```
3. Soumettez une pull request.

---

## Licence

Ce projet est sous licence MIT. Consultez le fichier `LICENSE` pour plus d'informations.

---

**Note** : Ce projet est en cours de développement. Certaines fonctionnalités peuvent ne pas être entièrement implémentées.