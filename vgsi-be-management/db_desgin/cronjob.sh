#web
#push notify
* * * * * cd /var/www/be-web/; php yii announcement/push 2 0 >> /var/logs/web/announcement_push_0.log 2>&1
* * * * * cd /var/www/be-web/; php yii announcement/push 2 1 >> /var/logs/web/announcement_push_1.log 2>&1
* * * * * cd /var/www/be-web/; php yii announcement/push-is-event 2 0  >> /var/logs/web/announcement_push_event_0.log 2>&1
* * * * * cd /var/www/be-web/; php yii announcement/push-is-event 2 1 >> /var/logs/web/announcement_push_evant_1.log 2>&1

#Service
30 * * * * cd /var/www/be-web/; php yii service/bill-delete-draft
#tao phi toa nha
* * * * * cd /var/www/be-web/; php yii service/building-config-fee
#tao phi toa nha hàng tháng
0,15,30,45 * * * * cd /var/www/be-web/; php yii service/building-fee 2 0
1,16,31,46 * * * * cd /var/www/be-web/; php yii service/building-fee 2 1
#tạo phi gui xe
5,20,35,50 * * * * cd /var/www/be-web/; php yii service/parking-fee 2 0
6,21,36,51 * * * * cd /var/www/be-web/; php yii service/parking-fee 2 1
#công nơ
5 2 1 * * cd /var/www/be-web/; php yii service/debt 2 0
10 2 1 * * cd /var/www/be-web/; php yii service/debt 2 1
#công nợ tháng hiện tại
0,30 * * * * cd /var/www/be-web/; php yii service/debt-current-month 2 0
5,35 * * * * cd /var/www/be-web/; php yii service/debt-current-month 2 1
#xóa access token hết hạn
0 0 1 * * cd /var/www/be-web/; php yii access-token/delete-expired

#report by date
5 1 * * * cd /var/www/be-web/; php yii report/request-date
10 3,9,15,21 * * * cd /var/www/be-web/; php yii report/service-fee-date

#report tổng hợp số liệu booking
30 2 * * * cd /var/www/be-web/; php yii report/service-booking-week
#30phut/1 lần để chạy test
0,30 * * * * cd /var/www/be-web/; php yii report/service-booking-week 1

#Tự động hủy booking
#Hủy yêu cầu booking quá thời gian chờ tạo yêu cầu thanh toán
* * * * * cd /var/www/be-web/; php yii service-booking/cancel-delay-pay-request 2 0
* * * * * cd /var/www/be-web/; php yii service-booking/cancel-delay-pay-request 2 1
#Hủy yêu cầu booking hết thời gian sử dụng mà chưa được duyệt
50 23 * * * cd /var/www/be-web/; php yii service-booking/cancel-end-day 2 0
50 23 * * * cd /var/www/be-web/; php yii service-booking/cancel-end-day 2 1
#Delete user báo xóa sau 15 ngày
#50 23 * * * cd /var/www/be-web/; php yii resident-user/delete-end-day >> /var/logs/web/delete_resident_user.log 2>&1
* * * * * cd /var/www/be-web/; php yii resident-user/delete-end-day >> /var/logs/web/delete_resident_user.log 2>&1
#Đếm số cư dân theo độ tuổi
0 7 * * * cd /var/www/be-web/; php yii report/count-resident-by-age 
#check job
0 7 * * * cd /var/www/be-web/; php yii job/check 2 0
0 7 * * * cd /var/www/be-web/; php yii job/check 2 1
#Kiểm tra công việc
0 7 * * * cd /var/www/be-web/; php yii service-booking/before-notify 2 0
0 7 * * * cd /var/www/be-web/; php yii service-booking/before-notify 2 1
#Thông báo trước 7 ngày tới thời gian bảo trì thiết bị
0 7 * * * cd /var/www/be-web/; php yii maintenance-device/check 2 0
0 7 * * * cd /var/www/be-web/; php yii maintenance-device/check 2 1