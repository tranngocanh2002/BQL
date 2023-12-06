const env = process.env.NODE_ENV;
if (env === "production") {
  console.log("URL_API=http://localhost");
  console.log(
    "URL_UPLOAD_FILE_SERVER=http://localhost/file/upload"
  );
  console.log(
    "URL_DELETE_FILE_SERVER=http://localhost/file/del-file"
  );
} else if (env === "staging") {
  console.log("URL_API=http://localhost");
  console.log(
    "URL_UPLOAD_FILE_SERVER=http://localhost/file/upload"
  );
  console.log(
    "URL_DELETE_FILE_SERVER=http://localhost/file/del-file"
  );
} else if (env === "dev") {
  console.log("URL_API=http://localhost");
  console.log(
    "URL_UPLOAD_FILE_SERVER=http://localhost/file/upload"
  );
  console.log(
    "URL_DELETE_FILE_SERVER=http://localhost/file/del-file"
  );
} else {
  console.log("URL_API=http://localhost");
  console.log(
    "URL_UPLOAD_FILE_SERVER=http://localhost/file/upload"
  );
  console.log(
    "URL_DELETE_FILE_SERVER=http://localhost/file/del-file"
  );
}
