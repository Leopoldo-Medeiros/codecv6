#ADD horizon.conf /etc/supervisor/conf.d

# web-build/Dockerfile

ARG BASE_IMAGE
FROM $BASE_IMAGE

RUN apt-get update && apt-get install -y \
    jpegoptim \
    optipng \
    pngquant \
    gifsicle \
    webp \
    libavif-bin \
    && npm install -g svgo \
    && apt-get clean && rm -rf /var/lib/apt/lists/*
