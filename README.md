# CSV2OAI : Serveur OAI-PMH pour fichier CSV

Ce projet implémente un serveur OAI-PMH (_Open Archives Initiative Protocol for Metadata Harvesting_) simple, en **PHP**, dont les données proviennent d’un fichier **CSV** contenant des métadonnées au format _Dublin Core Element Set_.

---

## ✅ Prérequis

- **PHP ≥ 7.2** (aucune extension spéciale requise)
- Serveur HTTP (Apache, Nginx, ou intégré via `php -S`)
- Fichier CSV structuré selon le format _Dublin Core Element Set_
- Accès au serveur via URL (localhost ou en ligne)

---

## 📦 Fichiers du projet

| Fichier          | Description |
|------------------|-------------|
| `oai-pmh.php`    | Point d’entrée principal du serveur OAI-PMH |
| `utils.php`      | Fonctions PHP auxiliaires pour charger et accéder aux données |
| `data.csv`       | Base de données CSV avec les enregistrements Dublin Core |
| `index.html`     | Interface de test OAI-PMH (facultative) |

---

## 📁 Installation

1. Clone ou copie les fichiers dans ton serveur web local :

   ```bash
   git clone https://example.com/oai-php.git
   cd oai-php/
   ```

2. Lance un serveur local PHP (si besoin) :

   ```bash
   php -S localhost:8000
   ```

3. Accède à :

   ```
   http://localhost:8000/oai-pmh.php?verb=Identify
   ```

---

## 🧩 Fonctionnement

Le script supporte les verbes suivants du protocole OAI-PMH :

- `Identify`
- `ListMetadataFormats`
- `ListIdentifiers`
- `ListRecords`
- `GetRecord`

Le verbe est passé par URL via `?verb=...`.

---

## 📌 Format du CSV attendu

Le fichier `data.csv` doit contenir une première ligne avec les champs suivants (en anglais, sans accents) :

```
identifier;title;creator;subject;description;publisher;date;type;format;language;coverage;rights
```

---

## 📚 Détail des fichiers et fonctions

### `oai-pmh.php`

Ce fichier reçoit les requêtes OAI-PMH et génère une réponse XML conforme au protocole.

#### Variables importantes :

- `$verb` — Verbe OAI demandé (`Identify`, `ListRecords`, etc.)
- `$resumptionToken` — Index de pagination pour les listes
- `$batchSize` — Nombre d’enregistrements par réponse (modifiable, ex. `10`)

#### Logique principale :

```php
switch ($verb) {
  case 'Identify':
    // Retourne les métadonnées du dépôt
  case 'ListIdentifiers':
    // Liste uniquement les identifiants et datestamps
  case 'ListRecords':
    // Retourne les enregistrements Dublin Core complets
  case 'GetRecord':
    // Retourne un enregistrement à partir de son identifiant
}
```

#### XML généré :

- Conforme à OAI-PMH 2.0
- Utilise le schéma Dublin Core (`oai_dc`)

---

### `utils.php`

Contient les fonctions de traitement du fichier CSV.

#### `load_records($filename = 'data.csv')`

- Charge les enregistrements du fichier CSV
- Nettoie les entêtes
- Assure un identifiant et une date valide pour chaque ligne

#### `get_record_by_id($identifier, $records)`

- Recherche un enregistrement dans le tableau par identifiant OAI

#### `validate_date($date)`

- Vérifie si une date est au format `YYYY`, `YYYY-MM`, ou `YYYY-MM-DD`

---

## 🧪 Exemples d'URL de test

| Verbe            | Exemple d’URL |
|------------------|-----------------------------|
| Identify         | `?verb=Identify` |
| ListIdentifiers  | `?verb=ListIdentifiers&metadataPrefix=oai_dc` |
| ListRecords      | `?verb=ListRecords&metadataPrefix=oai_dc` |
| GetRecord        | `?verb=GetRecord&identifier=oai:example:1&metadataPrefix=oai_dc` |
| Pagination       | `?verb=ListRecords&metadataPrefix=oai_dc&resumptionToken=10` |

---

## ❓ FAQ

**Q : Le script ne retourne qu’un seul enregistrement. Pourquoi ?**  
A : Vérifiez que le paramètre `$batchSize` dans `oai-pmh.php` est bien défini à 10 (ou le nombre voulu).

**Q : Le XML est vide ou ne contient pas de données ?**  
A : Assurez-vous que le fichier `data.csv` est encodé en UTF-8 sans BOM, avec `;` comme séparateur, et que les entêtes sont exacts.

---

## 📃 Licence et citation

Ce projet est open-source, voir le fichier LICENSE pour plus d'information.

Citation : POUYLLAU, S. (CNRS), _CSV2OAI : Serveur OAI-PMH pour fichier CSV_, juillet 2025.

---

## 📬 Contact

Créé par Stéphane Pouyllau, ingénieur de recherche CNRS. 
Date : juillet 2025.