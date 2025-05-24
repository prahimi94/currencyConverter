#!/bin/sh

# wait-for-it.sh
# Usage: wait-for-it.sh host:port [--timeout=seconds]

HOSTPORT=$1
TIMEOUT=30

# if --timeout is provided, set the timeout value
for ARG in "$@"
do
  case $ARG in
    --timeout=*)
      TIMEOUT="${ARG#*=}"
      shift
      ;;
  esac
done

HOST=$(echo $HOSTPORT | cut -d':' -f1)
PORT=$(echo $HOSTPORT | cut -d':' -f2)

echo "Waiting for $HOST:$PORT for up to $TIMEOUT seconds..."

for i in $(seq $TIMEOUT)
do
  nc -z $HOST $PORT && echo "$HOST:$PORT is available" && exit 0
  sleep 1
done

echo "Timeout waiting for $HOST:$PORT"
exit 1
