FROM webdevops/php-nginx:8.0-alpine

# Start as root
USER root

# Updated and Add packages
RUN set -xe; \
    apk update && \
    apk add --no-cache supervisor && \
    apk add --no-cache unzip && \
    apk add --no-cache mysql mysql-client && \
    apk add --no-cache rsync

# Copy instalation files from your file system to container file system
ADD composer.json ./
ADD composer.lock ./

# Clean up
RUN rm -rf /tmp/* /var/tmp/* && \
    rm -rf /var/log/lastlog /var/log/faillog

# Switch to non-root user - prevent starting docker as root (APPLICATION_USER env is supplied by image)
USER ${APPLICATION_USER}

# Start up script
CMD [ "./scripts/startup.sh" ]
