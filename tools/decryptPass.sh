#!/bin/bash

if [ "$#" -ne 1 ]; then
    echo "usage: $0 <base64 file"
    exit 1
fi

INPUT_FILE=$1
TMP_FILE="temp_binary.bin"
KEY_FILE="private_key.pem"

if [ ! -f "$KEY_FILE" ]; then
    echo "Error: local file not found ($KEY_FILE)!"
    exit 1
fi

base64 -d "$INPUT_FILE" > "$TMP_FILE"

echo "--- Decrypting ---"
openssl pkeyutl -decrypt \
    -in "$TMP_FILE" \
    -inkey "$KEY_FILE" \
    -pkeyopt rsa_padding_mode:oaep \
    -pkeyopt rsa_oaep_md:sha256 \
    -pkeyopt rsa_mgf1_md:sha256

echo -e "\n-------cleaning------------------"

rm "$TMP_FILE"
