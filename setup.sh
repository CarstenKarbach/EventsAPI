#!/bin/bash

if [[ $(/usr/bin/id -u) -ne 0 ]]; then
    echo "This script requires root privileges." >&2
    exit 1
fi

#Adjust the certificate to use, if a special certificate was injected
SPECIALCERT=/etc/ssl/certs/ssl-cert-eventsapi.pem
KEYNAME=ssl-cert-eventsapi.key
SPECIALCERTKEY="/etc/ssl/certs/"$KEYNAME
SSLCONFFILE=/etc/apache2/sites-available/default-ssl.conf
if test -e "$SPECIALCERT";then
	chmod 644 $SPECIALCERT
	sed -i -e "\|^[ \t]\+SSLCertificateFile|s|SSLCertificateFile[ \t]\+.*|SSLCertificateFile "$SPECIALCERT"|" $SSLCONFFILE
fi

if test -e "$SPECIALCERTKEY";then
	mv $SPECIALCERTKEY /etc/ssl/private
	chmod 640 "/etc/ssl/private/"$KEYNAME
	chown root:ssl-cert "/etc/ssl/private/"$KEYNAME
	sed -i -e "\|^[ \t]\+SSLCertificateKeyFile|s|SSLCertificateKeyFile[ \t]\+.*|SSLCertificateKeyFile /etc/ssl/private/"$KEYNAME"|" $SSLCONFFILE
fi

