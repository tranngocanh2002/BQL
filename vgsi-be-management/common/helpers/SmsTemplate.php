<?php
/**
 * Created by PhpStorm.
 * User: qhuy.duong@gmail.com
 * Date: 14/04/2017
 * Time: 10:38 SA
 */

namespace common\helpers;


class SmsTemplate
{
    /**
     * - OTP
     * - SDT (3 so cuoi)
     * - Service_name
     * - Price
     * - Date
     */
    const SMS_OTP_BOOKING = '(Carento): %s la ma xac thuc sdt ...%s. Dat xe %s - Gia %sk - Ngay %s';

    /**
     * - Service_name
     * - Price
     * - Date
     */
    const SMS_BOOKING_INFO = '(Carento): Chuc mung qui khach dat xe %s thanh cong - Gia %sk - Ngay %s. Chuc anh/chi co chuyen di vui ve.';

    /**
     * - OTP
     * - SDT (3 so cuoi)
     */
    const SMS_OTP_DRIVER_LOGIN = '(Carento): %s la ma xac thuc sdt ...%s. Chao mung anh/chi gia nhap gia dinh Carento Drivers';

    /**
     * - Full name driver
     * - Car name
     * - Bien so
     */
    const SMS_DRIVER_CONFIRM_BOOKING = '(Carento): Tai xe: %s - %s - %s sdt +%s se don quy khach. Hotline +84989668247';
}