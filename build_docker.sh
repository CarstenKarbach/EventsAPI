#!/bin/bash

APACHE_SSL_CERT=./configs/certificates/ssl-cert-eventsapi.pem
APACHE_SSL_CERT_KEY=./configs/certificates/ssl-cert-eventsapi.key

# Create local relative folder for certificates
mkdir ./servercerts
if test -e "$APACHE_SSL_CERT";then
	cp $APACHE_SSL_CERT ./servercerts/ssl-cert-eventsapi.pem
fi
if test -e "$APACHE_SSL_CERT_KEY";then
	cp $APACHE_SSL_CERT_KEY ./servercerts/ssl-cert-eventsapi.key
fi

docker build -t karbach/eventsapi:v1 .

#Clear automatically created folders
rm -rf ./servercerts
