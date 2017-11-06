#!/bin/bash
openssl genrsa -out apache.key 2048
openssl req -new -x509 -key apache.key -days 365 -sha256 -out apache.crt