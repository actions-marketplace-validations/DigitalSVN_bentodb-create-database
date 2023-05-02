# BentoDB - Create Database

A GitHub action to create a database using [BentoDB](https://bentodb.com).

### Example usage
1. Create a database using the repository and branch as the name.
2. Output the database name and ID.
3. Delete the database using the ID.
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
      - name: Create
        id: create
        uses: DigitalSVN/bentodb-create-database@main
        with:
          api-token: ${{ secrets.BENTODB_API_TOKEN }}
          database-name: "${{ github.repository }}/${{ github.head_ref }}"

      - name: Output the database name
        run: echo "Database name - ${{ steps.create.outputs.database_name }}, ID - ${{ steps.create.outputs.database_id }}"
        
      - name: Delete
        id: delete
        uses: DigitalSVN/bentodb-delete-database@main
        with:
          api-token: ${{ secrets.BENTODB_API_TOKEN }}
          database-id: ${{ steps.create.outputs.database_id }}
          
      - name: Confirm deleted database name
        run: echo "Database name - ${{ steps.delete.outputs.database_name }}, ID - ${{ steps.delete.outputs.database_id }}"

```

### Outputs
| Key               | Example                          | Description                  |
|-------------------|----------------------------------|------------------------------|
| database_id       | 12345                            | A unique ID for the database |
| database_name     | my_database_name                 | Name of the database         |
| database_host     | example-mysql-region.bentodb.com | Hostname of the database     |
| database_port     | 3306                             | Port number of the database  |
| database_username | dbu_username                     | Database username            |
| database_password | dbp_password                     | Database password            |

### Pre-requisites
You will need a BentoDB API token. These are available for FREE at https://www.bentodb.com

### Configuration
| Key             | Example                | Description                                                                                 | Required |
|-----------------|------------------------|---------------------------------------------------------------------------------------------|----------|
| `api-token`     | `AAaaBBbbCCccDDddEEee` | Your BentoDB API token - this should be stored as a secret in your repo                     | Yes      |
| `database-name` | `db-name`              | Optional name for your database. A name will be randomly generated if you do not supply one | No       |