#!/bin/bash

docker compose up -d user-api-docs
docker compose exec user-api-docs bash -c "cd /docs; /node_modules/.bin/openapi validate"
