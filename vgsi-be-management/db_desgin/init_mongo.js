/**
 * Created by MyPC on 05/10/2017.
 */

db = connect("localhost:27017/admin",'siteRootAdmin','luci1109');
printjson(db.adminCommand('listDatabases'));
dbonline = db.getSiblingDB('ibuilding_staging');
dbonline.createUser(
    {
        user: "ibuilding_staging",
        pwd: "ibuilding123",
        roles: [ "readWrite", "dbAdmin" ]
    }
);