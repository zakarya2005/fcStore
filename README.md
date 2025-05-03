# Cahier de charges

## 1. Présentation du projet
**Nom du projet** : Football Club Store
**Brève description** :  
Football Club Store est une boutique en ligne officielle du club de football, spécialisée dans la vente de produits dérivés : maillots, accessoires, tenues d'entraînement et objets collector. La plateforme vise à offrir une expérience utilisateur simple, rapide et mobile-friendly.
**Objectif principal** :  
Fournir aux supporters un moyen pratique d’acheter des articles officiels du club avec une interface moderne et un processus de commande optimisé basé sur le paiement à la livraison.

## 2. Problèmes à résoudre / besoins exprimés
**Principales difficultés résolues** :
- Manque d'une boutique centralisée pour les articles du club
- Mise à jour peu fréquente des stocks (disponibilité des tailles/couleurs)
- Manque de simplicité dans les systèmes de commande des produits officiels
- Mauvaise expérience sur mobile dans les anciennes plateformes
**Attentes des utilisateurs** :
- Interface visuellement attractive valorisant les produits du club
- Navigation fluide avec des filtres efficaces (taille, nouveauté, collections)
- Notifications en temps réel (promotions, nouveautés)
- Politique de retours claire et facile
- Paiement simple sans carte bancaire (Cash On Delivery)

## 3. Utilisateurs cibles
- **Supporters / Clients** : Fans du club souhaitant acheter des produits dérivés, de tout âge, avec une aisance technologique variable.

## 4. Contraintes techniques
- **Frontend** : React.js
- **Backend API** : Laravel (PHP)
- **DB**: MySQL
- AWS & Docker
**Responsiveness / Mobile** :
- Design mobile-first
- Interface réactive sur smartphones, tablettes et ordinateurs
- Images optimisées et chargement rapide

---
# Types d'Utilisateurs

| **Type d'Utilisateur** | **Description** | **Actions Autorisées** | **Routes Protégées (React)** | **Permissions Backend (Laravel)** |
|------------------------|-----------------|-------------------------|-------------------------------|------------------------------------|
| **Client** | Supporters du club achetant des produits dérivés en ligne | • Parcourir et rechercher des produits<br>• Ajouter au panier<br>• Passer commande (paiement à la livraison)<br>• Noter et commenter les produits<br>• Ajouter des articles à la wishlist<br>• Consulter l'historique de commandes | `/checkout`<br>`/account/orders`<br>`/wishlist` | • `OrderPolicy@view` pour restreindre l’accès aux données<br>• Limitation d'accès par ID utilisateur |
| **Administrateur** | Équipe interne du club gérant la boutique | • CRUD sur le catalogue produits<br>• Créer et gérer promotions et remises<br>• Suivi des commandes et retours<br>• Génération de rapports de ventes et inventaire<br>• Gestion des utilisateurs | `/admin/*`<br>`/analytics` | • Accès complet via `Gate::define('isAdmin')`<br>• Middleware admin pour protéger les routes sensibles |

---
# Liste des Fonctionnalités

## Liste des Fonctionnalités par Type d’Utilisateur
### Pour les Clients :
- Créer un compte / se connecter (ou acheter en tant qu’invité)
- Rechercher des produits du club et appliquer des filtres (taille, type, nouveauté)
- Ajouter des produits au panier
- Commander avec **paiement à la livraison (COD)**
- Enregistrer des produits dans une wishlist
- Accéder à l’historique des commandes
- Laisser des avis et notes sur les produits
- Voir des suggestions de produits similaires
- Recevoir des notifications (promotions, confirmation de commande)
### Pour les Administrateurs :
- Accès à un **tableau de bord analytique**
- Gestion du catalogue produits (ajout, modification, suppression)
- Suivi et mise à jour des **stocks en temps réel**
- Création de **promotions et codes promo**
- Suivi des commandes (préparation, livraison, retours)
- Gestion des utilisateurs (clients, revendeurs)
- Personnalisation de l’interface de la boutique
- Génération de rapports de ventes et d’inventaire
### Pour les Partenaires / Revendeurs (optionnel - Marketplace) :
- Gestion d’un espace personnel
- Ajout, modification et suppression de leurs propres produits
- Suivi des ventes et des revenus
- Gestion de leur inventaire indépendant

## User Stories
- En tant que **client**, je veux filtrer les produits par taille et type afin de trouver rapidement ce que je cherche.
- En tant que **administrateur**, je veux visualiser les statistiques de vente pour prendre des décisions éclairées.
- En tant que **client**, je veux recevoir une notification lorsque ma commande est livrée afin de pouvoir la suivre.
- En tant que **client**, je veux ajouter des produits à mes favoris pour les retrouver facilement plus tard.
- En tant que **partenaire (optionnel)**, je veux gérer mon propre inventaire pour vendre mes produits via la plateforme.

## Priorisation des Fonctionnalités
### Essentielles (MVP) :
- Authentification client (avec ou sans compte)
- Catalogue produits avec recherche et filtres
- Panier d’achat avec **paiement à la livraison (COD)**
- Gestion des stocks et commandes côté administrateur
- Tableau de bord administrateur de base
- API RESTful (Laravel backend + React frontend)
- Design responsive orienté mobile
### Secondaires :
- Wishlist (produits favoris)
- Historique des commandes
- Avis et notes sur les produits
- Notifications (email ou interface)
### Bonus :
- Suggestions de produits similaires
- Espace revendeur (si marketplace activée)
- Interface administrateur personnalisable

---


