# Tokende - Site de Transport

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