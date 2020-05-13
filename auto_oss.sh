#!/bin/bash
host=""
bucket=""
Id=""
Key=""
# 参数1，PUT：上传，GET：下载
method="PUT"
# 参数2，上传时为本地源文件路径，下载时为oss源文件路径
source=$1
# 参数3，上传时为OSS目标文件路径，下载时为本地目标文件路径
dest=$2

osshost=$bucket.$host

#校验上传目标路径
if test -z "$dest"
then
    dest=$source
fi

echo "method:"$method
echo "source:"$source
echo "dest:"$dest

#校验参数是否为空
if test -z "$method" || test -z "$source" || test -z "$dest"
then
    echo $0 put localfile objectname
    echo $0 get objectname localfile
    exit -1
fi

if [ "${method}"x = "PUT"x ]
then
    resource="/${bucket}/${dest}"
    contentType=`file -ib ${source} |awk -F ";" '{print $1}'`
    dateValue="`TZ=GMT date +'%a, %d %b %Y %H:%M:%S GMT'`"
    stringToSign="${method}\n\n${contentType}\n${dateValue}\n${resource}"
    signature=`echo -en $stringToSign | openssl sha1 -hmac ${Key} -binary | base64`
    echo $stringToSign
    echo $signature
    url=http://${osshost}/${dest}
    echo "upload ${source} to ${url}"
    curl -i -q -X PUT -T "${source}" \
      -H "Host: ${osshost}" \
      -H "Date: ${dateValue}" \
      -H "Content-Type: ${contentType}" \
      -H "Authorization: OSS ${Id}:${signature}" \
      ${url}
else
    resource="/${bucket}/${source}"
    contentType=""
    dateValue="`TZ=GMT date +'%a, %d %b %Y %H:%M:%S GMT'`"
    stringToSign="${method}\n\n${contentType}\n${dateValue}\n${resource}"
    signature=`echo -en ${stringToSign} | openssl sha1 -hmac ${Key} -binary | base64`
    url=http://${osshost}/${source}
    echo "download ${url} to ${dest}"
    curl --create-dirs \
      -H "Host: ${osshost}" \
      -H "Date: ${dateValue}" \
      -H "Content-Type: ${contentType}" \
      -H "Authorization: OSS ${Id}:${signature}" \
      ${url} -o ${dest}
fi