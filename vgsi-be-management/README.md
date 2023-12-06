API Management
===============================

Install composer
------------------
Linux
```
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
```
Windown: Download https://getcomposer.org/Composer-Setup.exe

Intall plugin composer
---------------------
```
composer global require "fxp/composer-asset-plugin:^1.3.1"
```
Init Config
-------------------
```
php init
```

Init Database
-------------------
```
Tạo db mysql, sau đó cấu hình trong file 
common/config/main-local.php
```

Init Application
-------------------
```
composer install
```

Khởi tạo bảng language
=========================
```
php yii migrate/up --migrationPath=@vendor/lajax/yii2-translate-manager/migrations
php yii migrate
```

Create permission
------------------
Link to swagger: http://api.building.local:8080/swagger/doc#!/Rbac/rbac_permissions


API Docs (Swagger API Docs)
===========================
Link reference: https://swagger.io/docs/specification/about/
Module Yii2: https://github.com/lichunqiang/yii2-swagger </br>
Define API_HOST in file frontend/web/index
```
define("API_HOST", "api.management.dev");
```

Khởi tạo permission
=========================
```
php yii rbac/import
```

Khởi tạo cronjob
=========================
```
#LUCNN => IBUILDING
#push notify
* * * * * cd /var/www/be-management/; php yii announcement/push 2 0 >> /var/logs/ibuilding/announcement_push_0.log 2>&1
* * * * * cd /var/www/be-management/; php yii announcement/push 2 1 >> /var/logs/ibuilding/announcement_push_1.log 2>&1
* * * * * cd /var/www/be-management/; php yii announcement/push-is-event 2 0  >> /var/logs/ibuilding/announcement_push_event_0.log 2>&1
* * * * * cd /var/www/be-management/; php yii announcement/push-is-event 2 1 >> /var/logs/ibuilding/announcement_push_evant_1.log 2>&1

#Service
30 * * * * cd /var/www/be-management/; php yii service/bill-delete-draft
#tao phi toa nha
#* * * * * cd /var/www/be-management/; php yii service/building-config-fee
#tao phi toa nha hàng tháng
0,15,30,45 * * * * cd /var/www/be-management/; php yii service/building-fee 2 0
1,16,31,46 * * * * cd /var/www/be-management/; php yii service/building-fee 2 1
#tạo phi gui xe
5,20,35,50 * * * * cd /var/www/be-management/; php yii service/parking-fee 2 0
6,21,36,51 * * * * cd /var/www/be-management/; php yii service/parking-fee 2 1
#công nơ
5 2 1 * * cd /var/www/be-management/; php yii service/debt 2 0
10 2 1 * * cd /var/www/be-management/; php yii service/debt 2 1
#công nợ tháng hiện tại
0,30 * * * * cd /var/www/be-management/; php yii service/debt-current-month 2 0
5,35 * * * * cd /var/www/be-management/; php yii service/debt-current-month 2 1
#xóa access token hết hạn
0 0 1 * * cd /var/www/be-management/; php yii access-token/delete-expired

#report by date
5 1 * * * cd /var/www/be-management/; php yii report/request-date
10 3,9,15,21 * * * cd /var/www/be-backend/; php yii report/service-fee-date

#report tổng hợp số liệu booking
30 2 * * * cd /var/www/be-management/; php yii report/service-booking-week
#30phut/1 lần để chạy test
0,30 * * * * cd /var/www/be-management/; php yii report/service-booking-week 1

#Tự động hủy booking
#Hủy yêu cầu booking quá thời gian chờ tạo yêu cầu thanh toán
* * * * * cd /var/www/be-management/; php yii service-booking/cancel-delay-pay-request 2 0
* * * * * cd /var/www/be-management/; php yii service-booking/cancel-delay-pay-request 2 1
#Hủy yêu cầu booking hết thời gian sử dụng mà chưa được duyệt
50 23 * * * cd /var/www/be-management/; php yii service-booking/cancel-end-day 2 0
50 23 * * * cd /var/www/be-management/; php yii service-booking/cancel-end-day 2 1

#Delete user báo xóa sau 15 ngày
50 23 * * * cd /var/www/be-management/; php yii resident-user/delete-end-day

#Đếm số cư dân theo độ tuổi
0 7 * * * cd /var/www/be-management/; php yii report/count-resident-by-age 

#Kiểm tra công việc
0 7 * * * cd /var/www/be-management/; php yii job/check 2 0
0 7 * * * cd /var/www/be-management/; php yii job/check 2 1 

#Thông báo trước 60p tới thời gian sử dụng tiện ích
0 7 * * * cd /var/www/be-management/; php yii service-booking/before-notify 2 0
0 7 * * * cd /var/www/be-management/; php yii service-booking/before-notify 2 1

#Thông báo trước 7 ngày tới thời gian bảo trì thiết bị
0 7 * * * cd /var/www/be-management/; php yii maintenance-device/check 2 0
0 7 * * * cd /var/www/be-management/; php yii maintenance-device/check 2 1
```

Các bước khai báo thêm Building Cluster
=========================
```
1, Khai báo thông tin cụm tòa nhà và thông tin Management User trong trang quản trị - Admin Backend
   + hoặc tạo default: php yii import/create-cluster-default
2, Khởi tạo các teamplate mẫu thông báo và mẫu file pdf phí - Admin Backend
3, Login web ban quản lý : Khởi tạo thông tin các các phân khu của cụm tòa nhà
4, Cấu hình các nhóm quyền cho tài khoản quản lý
5, Thêm tài khoản quản lý và gán guyền tương ứng
6, Import thông tin căn hộ, Import thông tin cư dân
7, Lựa chọn các dịnh vụ cung cấp cho cư dân trong cụm tòa nhà
8, Cấu hình thông tin các loại phí dịch vụ
9, Import thông tin sử dụng dịch vụ ban đầu của mỗi căn hộ  
```