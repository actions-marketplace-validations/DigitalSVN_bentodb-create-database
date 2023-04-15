#!/bin/sh -l

# Exit immediately if a command exits with a non-zero status
set -e

API_TOKEN=$1
DATABASE_NAME=$2

if [ -z "$API_TOKEN" ]; then
  echo "API_TOKEN is required - Get your FREE API token at https://www.bentodb.com"
  exit 1
fi

echo "Creating database..."

http_response_code=$(curl --silent --write-out "%{http_code}" --output response.txt \
  -X POST \
  --url https://www.bentodb.com/api/databases/create \
  -H "Accept: application/json" \
  -H 'Content-Type: application/json' \
  -H "Authorization: Bearer $API_TOKEN" \
  --data '{
    "name": "'"$DATABASE_NAME"'",
    "convert-unsupported-characters": true
  }')

response_content=$(cat response.txt)

content_message=$(echo $response_content | jq -r '.message')
content_error=$(echo $response_content | jq -r '.error')

content_database_id=$(echo $response_content | jq -r '.data.id')
content_database_name=$(echo $response_content | jq -r '.data.name')

content_database_host=$(echo $response_content | jq -r '.data.host')
content_database_port=$(echo $response_content | jq -r '.data.port')
content_database_username=$(echo $response_content | jq -r '.data.username')
content_database_password=$(echo $response_content | jq -r '.data.password')

rm response.txt

if [[ "$http_response_code" != "200" && "$http_response_code" != "201" ]]; then
  printf "Code:$http_response_code\nMessage:$content_message\nError:$content_error"
  exit 1
fi

# Return these values to the action
echo "database_id=$content_database_id" >> $GITHUB_OUTPUT
echo "database_name=$content_database_name" >> $GITHUB_OUTPUT

echo "database_host=$content_database_host" >> $GITHUB_OUTPUT
echo "database_port=$content_database_port" >> $GITHUB_OUTPUT
echo "database_username=$content_database_username" >> $GITHUB_OUTPUT
echo "database_password=$content_database_password" >> $GITHUB_OUTPUT

# Output the message
printf "Code:$http_response_code\nMessage:$content_message\nError:$content_error"
