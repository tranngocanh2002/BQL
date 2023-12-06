<?php
/**
 * Created by PhpStorm.
 * User: qhuy.duong@gmail.com
 * Date: 14/04/2017
 * Time: 10:38 SA
 */

namespace common\helpers;


class NotificationTemplate
{
    /**
     * - Building Cluster Name
     * - Campaign Title
     */
    const NOTIFICATION_NEW_TO_RESIDENT = 'Ban quản lý gửi thông báo %s';
    const NOTIFICATION_NEW_TO_RESIDENT_EN = 'The Management Board has sent a notification %s';

    /**
     * - Apartment Name
     * - Request Title
     */
    const APARTMENT_NEW_REQUEST_TO_MANAGEMENT = '%s (%s) đã gửi phản ánh mới: %s';
    const APARTMENT_NEW_REQUEST_TO_MANAGEMENT_EN = '%s (%s) has sent a new feedback: %s';

    const APARTMENT_NEW_BOOKING_TO_MANAGEMENT = '%s (%s) đã đăng ký tiện ích %s';
    const APARTMENT_NEW_BOOKING_TO_MANAGEMENT_EN = '%s (%s) has registered %s';

    /**
     * - Apartment Name
     * - Request Title
     */
    const APARTMENT_UPDATE_REQUEST_TO_MANAGEMENT = '%s vừa bình luận trong phản ánh %s';
    const APARTMENT_UPDATE_REQUEST_TO_MANAGEMENT_EN = '%s just commented in the feedback %s';

    /**
     * - Apartment Name
     * - Request Title
     */
    const MANAGEMENT_CHANGE_STATUS_REQUEST = 'Ban quản lý chuyển phản ánh %s của bạn sang %s';
    const MANAGEMENT_CHANGE_STATUS_REQUEST_EN = 'The Board Management has changed the status of your feedback %s to %s';

    const MANAGEMENT_CHANGE_STATUS_CLOSE_REQUEST = 'Ban quản lý chuyển phản ánh %s của bạn sang %s. Vui lòng đánh giá chất lượng xử lý phản ánh.';
    const MANAGEMENT_CHANGE_STATUS_CLOSE_REQUEST_EN = 'The Board Management has changed the status of your feedback %s to %s. Please rate the feedback process quality.';

    const MANAGEMENT_CREATE_REQUEST_COMMENT = 'Ban quản lý đã gửi một bình luận trong phản ánh %s của bạn';
    const MANAGEMENT_CREATE_REQUEST_COMMENT_EN = 'The Management Board has sent a comment in your feedback %s';

    const RESIDENT_CREATE_REQUEST_COMMENT = '%s đã gửi một bình luận trong phản ánh %s';
    const RESIDENT_CREATE_REQUEST_COMMENT_EN = '%s has sent a comment in the %s feedback';

    const MANAGEMENT_CHANGE_STATUS_REQUEST_COMMENT = '%s (%s) đã gửi một bình luận trong phản ánh %s';
    const MANAGEMENT_CHANGE_STATUS_REQUEST_COMMENT_EN = '%s (%s) has sent a comment in the %s feedback';

    const MANAGEMENT_CHANGE_STATUS_REQUEST_CANCEL = '%s (%s) đã hủy phản ánh %s';
    const MANAGEMENT_CHANGE_STATUS_REQUEST_CANCEL_EN = '%s (%s) has canceled the feedback %s';

    const MANAGEMENT_CHANGE_STATUS_REQUEST_REOPENT = '%s (%s) đã mở lại phản ánh %s';
    const MANAGEMENT_CHANGE_STATUS_REQUEST_REOPENT_EN = '%s (%s) has reopened the feedback %s';

    const MANAGEMENT_CHANGE_STATUS_BOOKING = '%s (%s) đã hủy đăng ký tiện ích %s';
    const MANAGEMENT_CHANGE_STATUS_BOOKING_EN = '%s (%s) canceled %s registration';

    const MANAGEMENT_CHANGE_STATUS_BOOKING_APPROVED = 'Ban quản lý đã phê duyệt yêu cầu đặt tiện ích %s của bạn';
    const MANAGEMENT_CHANGE_STATUS_BOOKING_APPROVED_EN = 'Management Board has approved your booking %s request';

    const MANAGEMENT_CHANGE_STATUS_AUTO = 'Bạn có phí %s cần thanh toán';
    const MANAGEMENT_CHANGE_STATUS_AUTO_EN = 'You have a %s fee that needs to be paid.';

    const MANAGEMENT_CHANGE_STATUS_BOOKING_FORM_APPROVED = 'Ban quản lý đã phê duyệt yêu cầu %s của bạn';
    const MANAGEMENT_CHANGE_STATUS_BOOKING_FORM_APPROVED_EN = 'Management Board has approved your %s request';

    const MANAGEMENT_CHANGE_STATUS_BOOKING_CANCEL = 'Ban quản lý đã từ chối yêu cầu đặt tiện ích %s của bạn';
    const MANAGEMENT_CHANGE_STATUS_BOOKING_CANCEL_EN = 'Management Board rejected your booking %s request';

    const SYSTEMS_CHANGE_STATUS_BOOKING_CANCEL = 'Hệ thống đã hủy yêu cầu đặt tiện ích %s của bạn';
    const SYSTEMS_CHANGE_STATUS_BOOKING_CANCEL_EN = 'The system has canceled your booking %s request';

    const MANAGEMENT_CHANGE_STATUS_BOOKING_FORM_CANCEL = 'Ban quản lý từ chối yêu cầu %s của bạn';
    const MANAGEMENT_CHANGE_STATUS_BOOKING_FORM_CANCEL_EN = 'Management Board rejected your %s request ';

    const MANAGEMENT_BOOKING_SEND_NOTIFY = 'Bạn có lịch sử dụng tiện ích %s vào %s, ngày %s.';
    const MANAGEMENT_BOOKING_SEND_NOTIFY_EN = 'You have scheduled to use %s at %s on %s';

    const MANAGEMENT_GEN_CODE_REJECT = 'Ban quản lý từ chối yêu cầu thanh toán của bạn';
    const MANAGEMENT_GEN_CODE_REJECT_EN = 'The Board Management has rejected your payment request.';

    /**
     * - Building Cluster Name
     * - Request Title
     */
    const MANAGEMENT_UPDATE_REQUEST_TO_MANAGEMENT = '%s vừa bình luận trong phản ánh %s';
    const MANAGEMENT_UPDATE_REQUEST_TO_MANAGEMENT_EN = '%s just commented in the feedback %s';

    /**
     * - Management Name/Auth Group Name
     * - Request Title
     */
    const MANAGEMENT_UPDATE_REQUEST_TO_APARTMENT = '%s %s';

    const MANAGEMENT_UPDATE_BOOKING_TO_APARTMENT = '%s %s';

    const MANAGEMENT_CREATE_FEE_BOOKING_TO_APARTMENT = 'Ban quản lý vừa tạo phí cho tiện ích %s của bạn.';

    const MANAGEMENT_CREATE_FEE_BOOKING_TO_APARTMENT_EN = 'The Board Management has created a fee for your utility %s.';

    const INVESTORS_SEND_NEWS = 'Chủ đầu tư gửi tin tức %s.';

    const INVESTORS_SEND_NEWS_EN = 'Investor has send a news %s.';


    const APARTMENT_CREATE_BILL_TO_MANAGEMENT = '[Thanh toán] Có thanh toán mới %s - %s';

    const MANAGEMENT_CREATE_BILL_TO_APARTMENT = 'Bạn có phí %s cần thanh toán.';
    const MANAGEMENT_CREATE_BILL_TO_APARTMENT_EN = 'You have a %s fee that needs to be paid.';

    const SERVICE_PAYMENT_FEE = '[Duyệt phí] Có phí mới cần duyệt của %s';

    const SERVICE_PAYMENT_GEN_CODE = '%s (%s) đã gửi 1 yêu cầu thanh toán';
    const SERVICE_PAYMENT_GEN_CODE_EN = '%s (%s) has sent 1 payment request';

    const MANAGEMENT_PERFORMER_CREATE_JOB = '%s vừa giao cho bạn 1 công việc mới %s.';
    const MANAGEMENT_PERFORMER_CREATE_JOB_EN = '%s has assigned you a new task: %s.';
    const MANAGEMENT_PEOPLE_INVOLVED_CREATE_JOB = 'Bạn vừa được gán liên quan đến công việc %s.';
    const MANAGEMENT_PEOPLE_INVOLVED_CREATE_JOB_EN = 'You have just been assigned to the task %s.';
    const MANAGEMENT_CANCEL_JOB = '%s vừa hủy công việc %s.';
    const MANAGEMENT_CANCEL_JOB_EN = '%s has canceled the task %s.';
    const MANAGEMENT_DELETE_JOB = '%s vừa xóa công việc %s.';
    const MANAGEMENT_DELETE_JOB_EN = '%s has deleted the task %s.';

    const MANAGEMENT_REMIND_WORK_JOB = 'Bạn có công việc sắp đến hạn: %s.';
    const MANAGEMENT_REMIND_WORK_JOB_EN = 'You have a task that is deadline: %s.';

    const MANAGEMENT_JUST_CHANGED_THE_TASK_STATUS = '%s vừa chuyển trạng thái công việc %s sang %s';
    const MANAGEMENT_JUST_CHANGED_THE_TASK_STATUS_EN = '%s just changed the task status %s to %s';

    const APARTMENT_NEW_FORM_TO_MANAGEMENT = '%s (%s) đã %s';
    const APARTMENT_NEW_FORM_TO_MANAGEMENT_EN = '%s (%s) has registered %s';

    const APARTMENT_CANCEL_FORM_TO_MANAGEMENT = '%s (%s) đã hủy %s';
    const APARTMENT_CANCEL_FORM_TO_MANAGEMENT_EN = '%s (%s) has canceled the %s registration';


    const VNPAY_PAYMENT = '%s (%s) %s đã thanh toán phí thành công';
    const VNPAY_PAYMENT_EN = '%s (%s) %s has successfully paid the fee';

    const CHANGE_PHONE_NUMBER_FROM    = '%s (%s) đã thay đổi số điện thoại từ %s sang SĐT %s';
    const CHANGE_PHONE_NUMBER_FROM_EN = '%s (%s) changed phone number from %s to SĐT %s';

    public static function vsprintf($format, $arrayParam){
        return vsprintf($format, $arrayParam);
    }
}
