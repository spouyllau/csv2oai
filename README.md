
# CSV2OAI: OAI-PMH Server for CSV Files

This project implements a simple OAI-PMH (_Open Archives Initiative Protocol for Metadata Harvesting_) server in **PHP**, where the data comes from a **CSV** file containing metadata in the _Dublin Core Element Set_ format.

> Note: code written with the help of LLM [Mistral-7B-Instruct-v0.3](https://huggingface.co/mistralai/Mistral-7B-Instruct-v0.3) using a personal prompt.

[TOC]
---

## Requirements

- **PHP ≥ 7.2** (no special extensions required, the goal is to be as simple and dependency-free as possible for the host server)
- HTTP Server (Apache, Nginx, or built-in using `php -S`)
- CSV file structured according to the _Dublin Core Element Set_ format
- Server access via URL (localhost or online)

---

## Project Files

| File             | Description |
|------------------|-------------|
| `oai-pmh.php`    | Main entry point for the OAI-PMH server |
| `utils.php`      | Auxiliary PHP functions to load and access data |
| `data.csv`       | CSV database with Dublin Core records |
| `index.html`     | OAI-PMH quering and testing interface (optional) |

---

## Installation

1. Clone or copy the files to your local web server:

   ```bash
   git clone https://example.com/oai-php.git
   cd oai-php/
   ```

2. Start a local PHP server (if needed):

   ```bash
   php -S localhost:8000
   ```

3. Access:

   ```
   http://localhost:8000/oai-pmh.php?verb=Identify
   ```

---

## How It Works

The script supports the following verbs from the OAI-PMH protocol:

- `Identify`
- `ListMetadataFormats`
- `ListSets`
- `ListIdentifiers`
- `ListRecords`
- `GetRecord`

The verb is passed via URL using `?verb=...`.

---

## Expected CSV Format

The `data.csv` file must contain a first line with the following fields (in English, no accents):

```
set;identifier_oai;identifier;title;creator;subject;description;publisher;date;type;format;language;coverage;rights;relation
```

- set: this is the marker for the OAI-PMH Set and is used in the `ListSets` verb.
- The other fields correspond to those in the _Dublin Core Element Set_.

---

## File and Function Details

### `oai-pmh.php`

This file receives OAI-PMH requests and generates an XML response compliant with the protocol.

#### Important variables:

- `$verb` — Requested OAI verb (`Identify`, `ListRecords`, etc.)
- `$resumptionToken` — Pagination index for listings
- `$batchSize` — Number of records per response (modifiable, e.g. `10`)

#### Main logic:

```php
switch ($verb) {
  case 'Identify':
    // Returns repository metadata
  case 'ListIdentifiers':
    // Lists only identifiers and datestamps
  case 'ListSets':
    // Lists only the sets
  case 'ListRecords':
    // Returns full Dublin Core records
  case 'GetRecord':
    // Returns a single record by identifier
}
```

#### Generated XML:

- Compliant with OAI-PMH 2.0
- Uses the Dublin Core schema (`oai_dc`)

---

### `utils.php`

Contains functions to process the CSV file.

#### `load_records($filename = 'data.csv')`

- Loads records from the CSV file
- Cleans the headers
- Ensures a valid identifier and date for each row

#### `get_record_by_id($identifier, $records)`

- Searches a record in the array by OAI identifier

#### `validate_date($date)`

- Checks if a date is in the format `YYYY`, `YYYY-MM`, or `YYYY-MM-DD`

---

## Test URL Examples

| Verb             | Example URL |
|------------------|-----------------------------|
| Identify         | `?verb=Identify` |
| ListIdentifiers  | `?verb=ListIdentifiers&metadataPrefix=oai_dc` |
| ListSets         | `?verb=ListSets` |
| ListRecords      | `?verb=ListRecords&metadataPrefix=oai_dc` |
| GetRecord        | `?verb=GetRecord&identifier=oai:example:1&metadataPrefix=oai_dc` |
| Pagination       | `?verb=ListRecords&metadataPrefix=oai_dc&resumptionToken=10` |

---

## Notes and Limitations

- Data is entirely extracted from `data.csv`.
- Pagination is done using `resumptionToken`.
- The script does not implement `deleted`, `from`, or `until` functionalities of OAI, to keep it lightweight for users unfamiliar with OAI. Those seeking a full-featured OAI-PMH implementation should consider tools like Dataverse, Omeka Classic or Omeka S.
- This server is not suited for large CSV files; other tools are better for handling large data volumes (e.g. Dataverse).

---

## Demo

A demo is avalaible on <a href="https://www.stephanepouyllau.org/oai-pmh/">https://www.stephanepouyllau.org/oai-pmh/</a>.

---

## FAQ

**Q: The script returns only one record. Why?**  
A: Check that the `$batchSize` parameter in `oai-pmh.php` is set to 10 (or your desired number).

**Q: The XML is empty or contains no data?**  
A: Ensure that the `data.csv` file is encoded in UTF-8 without BOM, uses `;` as separator, and that the headers are correct.

---

## License and Citation

This project is open-source; see the LICENSE file for more information.

Citation: POUYLLAU, S. (CNRS) with Mistral 7b, _CSV2OAI: OAI-PMH Server for CSV Files_, July 2025.

---

## Contact

Created by Stéphane Pouyllau, CNRS Research Engineer.  
Date: July 2025.
