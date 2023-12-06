'use strick'

let URL_UPLOAD_FILE_SERVER = process.env.URL_UPLOAD_FILE_SERVER;
let URL_DELETE_FILE_SERVER = process.env.URL_DELETE_FILE_SERVER;

const KEY_API_APP = '98CPB8ITIRGHVO3OJ5QT';


export const getHeadersUpload = () => {
  return {
    action: URL_UPLOAD_FILE_SERVER,
    headers: window.connection.getHeader()
  }
};

export function UPLOAD(files) {
  return new Promise((resolve, reject) => {

    if (!files) {
      resolve({
        "success": true,
        "statusCode": 200,
        "message": "Upload thành công",
        "data": {
          "files": [

          ]
        }
      })
      return
    }

    let formData = new FormData();
    if (files instanceof Array) {
      if (files.length == 0) {
        resolve({
          "success": true,
          "statusCode": 200,
          "message": "Upload thành công",
          "data": {
            "files": [

            ]
          }
        })
        return
      }
      for (let i = 0; i < files.length; i++) {

        let name = (files[i].name || files[i].filename)
        let uri = (files[i].url || files[i].path)

        if (!!!name) {
          let urrrr = uri.split('/')
          name = urrrr[urrrr.length - 1]
        }

        formData.append('UploadForm[files][]', { uri, name: (name || '').replace(/ /gi, '_'), type: 'multipart/form-data' });

      }
    } else {

      let name = (files.name || files.filename)
      let uri = (files.url || files.path)

      if (!!!name) {
        let urrrr = uri.split('/')
        name = urrrr[urrrr.length - 1]
      }
      console.log(`files`, files)
      formData.append('UploadForm[files][]', files, (name || '').replace(/ /gi, '_'));
    }

    window.connection.POST('/file/upload', formData)
      .then(json => {
        resolve(json)
      })
      .catch(e => {
        reject(e)
      })
  })
}

export function DELETE_FILES(files) {
  console.log('DELETE_FILES ', files)
  return new Promise((resolve, reject) => {
    fetch(URL_DELETE_FILE_SERVER,
      {
        // mode: 'no-cors',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-Luci-Api-Key': KEY_API_APP,
        },
        method: 'POST',
        body: JSON.stringify({ files })
      })
      .then((res) => res.json())
      .then(json => {
        console.log('DELETE_FILES res::', json)
        resolve(json)
      })
      .catch(e => {
        console.log('/site/del-file', e)
        reject(e)
      })
  })
}
