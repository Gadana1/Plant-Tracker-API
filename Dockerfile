FROM webdevops/php-nginx:8.0-alpine

# Start as root
USER root

# Updated and Add packages
RUN set -xe; \
    apk update && \
    apk add --no-cache supervisor && \
    apk add --no-cache unzip && \
    apk add --no-cache mysql mysql-client


###########################################################################
# Add non-root user - 'Laravel':
###########################################################################

# Add a non-root user to prevent files being created with root permissions on host machine.
ARG PGID=1010
ENV PGID ${PGID}
ARG PUID=1010
ENV PUID ${PUID}

# Add user and group
RUN addgroup -g ${PGID} -S laravel && adduser -u ${PUID} -S laravel  -G laravel


# Copy instalation files from your file system to container file system
COPY composer.json ./
COPY composer.lock ./

# Clean up
RUN rm -rf /tmp/* /var/tmp/* && \
    rm -rf /var/log/lastlog /var/log/faillog
    
# Switch to non-root user -prevent starting docker as root
# USER laravel
 
# Start up script
CMD ./scripts/startup.sh