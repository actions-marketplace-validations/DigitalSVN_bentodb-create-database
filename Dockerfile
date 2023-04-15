FROM alpine:3.17

# Install the curl and jq packages
RUN apk --no-cache add curl jq

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]