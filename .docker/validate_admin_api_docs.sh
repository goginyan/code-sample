#!/bin/bash

docker compose up -d admin-api-docs
docker compose exec admin-api-docs bash -c "cd /docs; /node_modules/.bin/openapi validate"

