#!/bin/bash
if [ $# -eq 0 ]; then
    echo "Usage: mkpkg directory_name"
else
    DIR="$(dirname "$(readlink -f ${BASH_SOURCE[0]})")"
    TMPDIR=/tmp/tmp.$(($RANDOM * 19318203981230 + 40))
    PLUGIN="$(basename "${DIR}")"
    DIST="${DIR}/dist"
    DESTDIR="${TMPDIR}/usr/local/emhttp/plugins/${PLUGIN}"
    PLG_FILE="${DIR}/plugin/${PLUGIN}.plg"
    VERSION=$(date +"%Y.%m.%d.%H%M")
    ARCH="-x86_64"
    PACKAGE="${DIST}/${PLUGIN}-plugin-${VERSION}${ARCH}.txz"
    MD5="${DIST}/${PLUGIN}-plugin-${VERSION}${ARCH}.md5"
    SHA256="${DIST}/${PLUGIN}-plugin-${VERSION}${ARCH}.sha256"

    mkdir -p "${DIST}"
    mkdir -p "${DESTDIR}/"
    cd "${DIR}/src/"
    cp --parents -r -f $(find .) "${DESTDIR}/"
    cd "${TMPDIR}/"
    makepkg -l y -c y "${PACKAGE}"
    cd "${DIST}/"
    MD5_SUM=$(md5sum "$(basename "${PACKAGE}")")
    echo ${MD5_SUM} > "${MD5}"
    SHA256_SUM=$(sha256sum "$(basename "${PACKAGE}")")
    echo ${SHA256_SUM} > "${SHA256}"
    rm -rf "${TMPDIR}"

    # Verify and install plugin package
    sum1=$(md5sum "${PACKAGE}")
    sum2=$(cat "${MD5}")
    if [ "${sum1:0:32}" != "${sum2:0:32}" ]; then
        echo "MD5 checksum mismatched."
        rm "${MD5}" "${PACKAGE}"
        exit
    else
        echo "MD5 checksum matched."
    fi

    sum1=$(sha256sum "${PACKAGE}")
    sum2=$(cat "${SHA256}")
    if [ "${sum1:0:64}" != "${sum2:0:64}" ]; then
        echo "SHA256 checksum mismatched."
        rm "${SHA256}" "${PACKAGE}"
        exit
    else
        echo "SHA256 checksum matched."
    fi

    sed -i -e "s#\(ENTITY\s*version[^\"]*\).*#\1\"${VERSION}\">#" "${PLG_FILE}"
    sed -i -e "s#\(ENTITY\s*MD5[^\"]*\).*#\1\"${MD5_SUM:0:32}\">#" "${PLG_FILE}"
    sed -i -e "s#\(ENTITY\s*SHA256[^\"]*\).*#\1\"${SHA256_SUM:0:64}\">#" "${PLG_FILE}"
fi