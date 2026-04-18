#!/bin/bash
# Run this after `lando start` to update the API base URL in .env
PORT=$(lando info --format=json 2>/dev/null | python3 -c "
import json, sys
data = json.load(sys.stdin)
for s in data:
    if s['service'] == 'appserver_nginx':
        for url in s['urls']:
            if url.startswith('http://localhost'):
                print(url.rstrip('/'))
                break
" 2>/dev/null)

if [ -z "$PORT" ]; then
  echo "Error: could not detect Lando port. Is Lando running?"
  exit 1
fi

echo "NUXT_PUBLIC_API_BASE=$PORT" > "$(dirname "$0")/.env"
echo "Updated .env: NUXT_PUBLIC_API_BASE=$PORT"
