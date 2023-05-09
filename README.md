# BentoDB - Create Database

A GitHub action to create a database using [BentoDB](https://www.bentodb.com).

### Use cases
* Run tests against a real database
* Create a database for each pull request, and persist the data for the lifetime of the pull request
* Create a database for each branch, and persist the data for the lifetime of the branch

------------------------

### Example usage
1. Create a database using the repository and branch as the name.
2. Output the database name and ID.
3. Use PHP to connect to the database
   1. CREATE a table
   2. DESCRIBE the table
   3. INSERT sample data
   4. SELECT the data with an ORDER BY clause.
4. Delete the database using the ID.
```yaml
on:
  push:
    branches:
      - main
  pull_request:
    branches: '*'

jobs:
  example_job:
    runs-on: ubuntu-latest
    name: Create and then delete a database using BentoDB
    steps:
      - uses: actions/checkout@v3
      - name: Create BentoDB database
        id: create
        uses: DigitalSVN/bentodb-create-database@main
        with:
          api-token: ${{ secrets.BENTODB_API_TOKEN }}
          database-name: "${{ github.repository }}/${{ github.head_ref }}"

      - name: Output the database name
        run: echo "Database name - ${{ steps.create.outputs.database_name }}, ID - ${{ steps.create.outputs.database_id }}"

      - name: Run MySQL test script
        run: |
          php ./.github/workflows/test.php
        env:
          DB_HOST: ${{ steps.create.outputs.database_host }}
          DB_PORT: ${{ steps.create.outputs.database_port }}
          DB_NAME: ${{ steps.create.outputs.database_name }}
          DB_USERNAME: ${{ steps.create.outputs.database_username }}
          DB_PASSWORD: ${{ steps.create.outputs.database_password }}

      - name: Delete BentoDB database
        id: delete
        uses: DigitalSVN/bentodb-delete-database@main
        with:
          api-token: ${{ secrets.BENTODB_API_TOKEN }}
          database-id: ${{ steps.create.outputs.database_id }}

      - name: Confirm deleted database name
        run: echo "Database name - ${{ steps.delete.outputs.database_name }}, ID - ${{ steps.delete.outputs.database_id }}"
```

Example output from the MySQL connection script:
```text
Run php ./.github/workflows/test.php
  php ./.github/workflows/test.php
  shell: /usr/bin/bash -e {0}
  env:
    DB_HOST: xxxxxxxx
    DB_PORT: 3306
    DB_NAME: repo-branch_name
    DB_USERNAME: ***
    DB_PASSWORD: ***
Creating table...
CREATE TABLE IF NOT EXISTS `books` (
    `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
    `title` varchar(255),
    `author` varchar(255),
    PRIMARY KEY (`id`)
);
Table created.
DESCRIBE `books`;
+--------+--------------------+------+-----+---------+----------------+
| Field  | Type               | Null | Key | Default | Extra          |
+--------+--------------------+------+-----+---------+----------------+
| id     | mediumint unsigned | NO   | PRI |         | auto_increment |
| title  | varchar(255)       | YES  |     |         |                |
| author | varchar(255)       | YES  |     |         |                |
+--------+--------------------+------+-----+---------+----------------+
Inserting data...
INSERT INTO `books` (`title`, `author`) VALUES 
      ('The Da Vinci Code', 'Dan Brown'),
      ('Harry Potter and the Chamber of Secrets', 'J. K. Rowling');
Data inserted.
SELECT * FROM `books` ORDER BY title ASC;
+----+-----------------------------------------+---------------+
| id | title                                   | author        |
+----+-----------------------------------------+---------------+
| 2  | Harry Potter and the Chamber of Secrets | J. K. Rowling |
| 1  | The Da Vinci Code                       | Dan Brown     |
+----+-----------------------------------------+---------------+
```

Or see live example here: https://github.com/DigitalSVN/bentodb-create-database/actions

---------------------

### Pre-requisites
You will need a BentoDB API token. These are available for FREE at https://www.bentodb.com

### Configuration
| Key             | Example                | Description                                                                                 | Required |
|-----------------|------------------------|---------------------------------------------------------------------------------------------|----------|
| `api-token`     | `AAaaBBbbCCccDDddEEee` | Your BentoDB API token - this should be stored as a secret in your repo                     | Yes      |
| `database-name` | `db-name`              | Optional name for your database. A name will be randomly generated if you do not supply one | No       |

### Outputs
| Key               | Example                          | Description                  |
|-------------------|----------------------------------|------------------------------|
| database_id       | 12345                            | A unique ID for the database |
| database_name     | my_database_name                 | Name of the database         |
| database_host     | example-mysql-region.bentodb.com | Hostname of the database     |
| database_port     | 3306                             | Port number of the database  |
| database_username | dbu_username                     | Database username            |
| database_password | dbp_password                     | Database password            |
