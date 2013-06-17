#!/bin/bash
curl -X POST -H 'Content-type: application/json' 'http://localhost:8000/v1/api/apiaxle' -d '{"endPoint":"localhost:8000"}'
curl -X POST -H 'Content-type: application/json' 'http://localhost:8000/v1/key/apiaxle-travis-ci-key' -d '{"sharedSecret":"apiaxle-travis-ci-secret"}'
curl -X PUT 'http://localhost:8000/v1/api/apiaxle/linkkey/apiaxle-travis-ci-key'
cd config/
ln -s config.travis.php config.local.php
exit 0
