# Immobilier - Location

Gestion locative et quittances pour Dolibarr ERP.

## Numéro de module
`700003`

## Dépendances
immocore, immobien, immoclient, societe

## Installation

1. Copier le dossier dans `dolibarr/htdocs/custom/immolocatif/`
2. Activer le module depuis **Configuration > Modules/Applications**
3. Les tables seront créées automatiquement

## Tests

```bash
php test/phpunit/ImmoBailTest.php
```

## Structure

```
immolocatif/
├── core/modules/       → Fichier d'activation module
├── class/              → Classes métier
├── sql/                → Schémas de base de données
├── langs/              → Fichiers de traduction
├── test/phpunit/       → Tests automatisés
├── css/                → Feuilles de style
└── js/                 → Scripts JavaScript
```

## License

GPLv3
