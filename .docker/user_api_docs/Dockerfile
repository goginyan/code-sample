FROM node:14.17.1

ADD package.json /

RUN cd / && mkdir -p /node_modules && npm --global install --unsafe-perm --prefix=/node_modules
RUN mkdir /docs

WORKDIR /docs

ENTRYPOINT /node_modules/.bin/openapi preview-docs

EXPOSE 8080
