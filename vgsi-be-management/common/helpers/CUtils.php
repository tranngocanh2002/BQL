<?php
namespace common\helpers;

use common\models\BookingRequest;
use common\models\Customer;
use common\models\Driver;
use common\models\OtpConfirm;
use common\models\VerifyCode;
use DateTime;
use frontend\models\BookingRequestForm;
use PHPUnit\Framework\Error\Deprecated;
use Yii;
use yii\base\Exception;
use yii\helpers\Json;

/**
 * Description of CUtils
 *
 */
class CUtils
{
    /*
     * Chuyển tiền từ số thành chữ
     */
    public static function convert_number_to_words($number) {

        $hyphen      = ' ';
        $conjunction = '  ';
        $separator   = ' ';
        $negative    = 'âm ';
        $decimal     = ' phẩy ';
        $dictionary  = array(
            0                   => 'Không',
            1                   => 'Một',
            2                   => 'Hai',
            3                   => 'Ba',
            4                   => 'Bốn',
            5                   => 'Năm',
            6                   => 'Sáu',
            7                   => 'Bảy',
            8                   => 'Tám',
            9                   => 'Chín',
            10                  => 'Mười',
            11                  => 'Mười một',
            12                  => 'Mười hai',
            13                  => 'Mười ba',
            14                  => 'Mười bốn',
            15                  => 'Mười năm',
            16                  => 'Mười sáu',
            17                  => 'Mười bảy',
            18                  => 'Mười tám',
            19                  => 'Mười chín',
            20                  => 'Hai mươi',
            30                  => 'Ba mươi',
            40                  => 'Bốn mươi',
            50                  => 'Năm mươi',
            60                  => 'Sáu mươi',
            70                  => 'Bảy mươi',
            80                  => 'Tám mươi',
            90                  => 'Chín mươi',
            100                 => 'trăm',
            1000                => 'nghìn',
            1000000             => 'triệu',
            1000000000          => 'tỷ',
            1000000000000       => 'nghìn tỷ',
            1000000000000000    => 'ngàn triệu triệu',
            1000000000000000000 => 'tỷ tỷ'
        );

        if (!is_numeric($number)) {
            return false;
        }

        if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
// overflow
            trigger_error(
                'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
                E_USER_WARNING
            );
            return false;
        }

        if ($number < 0) {
            return $negative . self::convert_number_to_words(abs($number));
        }

        $string = $fraction = null;

        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens   = ((int) ($number / 10)) * 10;
                $units  = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds  = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . self::convert_number_to_words($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = self::convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= self::convert_number_to_words($remainder);
                }
                break;
        }

        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal;
            $words = array();
            foreach (str_split((string) $fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }

        return $string;
    }

    //unset những giá trị null
    public static function arrLoad($arrLoad){
        foreach ($arrLoad as $key => $val) {
            if ($val === null && !is_array($val)) {
                unset($arrLoad[$key]);
            }else if(in_array($key,['medias', 'attach']) && !is_array($val)){
                unset($arrLoad[$key]);
            }else if(in_array($key,['service_payment_fee_id', 'service_bill_item_id_delete', 'billing_info']) && empty($val)){
                unset($arrLoad[$key]);
            };
        }
        return (array)$arrLoad;
    }
    //ép kiểu param
    public static function modifyParams($params){
        $arrInt = [
            'id',
            'order',
            'active_app',
            'auth_group_id',
            'management_user_id',
            'resident_user_id',
            'request_category_id',
            'request_id',
            'apartment_id',
//            'status',
            'type',
            'gender',
            'birthday',
            'parent_id',
            'building_cluster_id',
            'building_area_id',
            'status_verify_phone',
            'status_verify_email',
            'is_deleted',
            'is_send_push',
            'is_send_email',
            'is_send_notify',
            'is_send_sms',
            'send_at',
            'announcement_category_id',
            'is_send',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
            'from_month',
            'to_month',
            'from_day',
            'to_day',
            'service_map_management_id',
            'is_hidden',
            'type_send',
            'start_time',
            'end_time',
        ];
        foreach ($params as $key=>$val){
            if(in_array($key,$arrInt)){
                if(is_numeric($val)){
                    $params[$key] = (int)$val;
                }else{
                    $params[$key] = 0;
                }
            }
        }
        return $params;
    }
    public static function startsWith($haystack, $needle)
    {
        return $needle === "" || strpos($haystack, $needle) === 0;
    }

    public static function endsWith($haystack, $needle)
    {
        return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
    }

    //put your code here

    /**
     * Log a msg as custom level "CUtils::Debug"
     * You need to add this level ("CUtils::Debug") to log component in config/main.php :
     * <code>
     * 'log'=>array(
     *        'class'=>'CLogRouter',
     *        'routes'=>array(
     *            array(
     *                'class'=>'CFileLogRoute',
     *                'levels'=>'error, warning, <b>CUtils::log</b>',
     *            ),
     *            array('class'=>'CWebLogRoute',),
     *        ),
     * </code>
     * @param string $msg
     */
    public static function log($msg, $category = "-=Carento=-")
    {
        \Yii::info($msg, 'CUtils::log', $category);
    }

    /**
     * Check if $params in $arr invalid,
     * @param $arr
     * @return bool
     */
    public static function checkRequiredParams($arr)
    {
        foreach ($arr as $param) {
            if (!isset($param) || empty($param)) {
                return false;
            }
        }
        return true;
    }

    public static function Encrypt($data, $secret = null)
    {
        if ($secret == null) {
            $secret = 'luci2018xyz';
        }
        //Generate a key from a hash
        $key = md5(utf8_encode($secret), true);

        //Take first 8 bytes of $key and append them to the end of $key.
        $key .= substr($key, 0, 8);

        //Pad for PKCS7
        $blockSize = mcrypt_get_block_size('tripledes', 'ecb');
        $len = strlen($data);
        $pad = $blockSize - ($len % $blockSize);
        $data .= str_repeat(chr($pad), $pad);

        //Encrypt data
        $encData = mcrypt_encrypt('tripledes', $key, $data, 'ecb');
        return base64_encode($encData);
    }

    public static function Decrypt($data, $secret = null)
    {
        if ($secret == null) {
            $secret = 'luci2018xyz';
        }
        //Generate a key from a hash
        $key = md5(utf8_encode($secret), true);

        //Take first 8 bytes of $key and append them to the end of $key.
        $key .= substr($key, 0, 8);

        $data = base64_decode($data);

        $data = mcrypt_decrypt('tripledes', $key, $data, 'ecb');

        ///$block = mcrypt_get_block_size('tripledes', 'ecb');
        $len = strlen($data);
        $pad = ord($data[$len - 1]);

        return substr($data, 0, strlen($data) - $pad);
    }

    public static function formatDateTime($time)
    {
        if (!$time || !is_integer($time)) {
            throw new Exception(501, 'Invalid data');
        }

        return date('Y-m-d H:i:s', $time);
    }

    /**
     * @param float $number
     * @return string (1.000)
     */
    public static function numberFormat($number)
    {
        return number_format($number, 0, '', '.');
    }

    /**
     * @param double $price
     * @return String
     */
    public static function formatPrice($price)
    {
        if (!isset($price) || empty($price)) {
            return "0";
        }
        return "" . number_format($price, 0, ',', '.');
    }

    public static function randomString($length = 32, $chars = "abcdefghijklmnopqrstuvwxyz0123456789")
    {
        $max_ind = strlen($chars) - 1;
        $res = "";
        for ($i = 0; $i < $length; $i++) {
            $res .= $chars[rand(0, $max_ind)];
        }

        return $res;
    }


    public static function checksum($str)
    {
        return md5($str);
    }

    public static function timeElapsedString($ptime, $toward = false)
    {
        if ($toward) {
            $etime = $ptime - time();
        } else {
            $etime = time() - $ptime;
        }

        if ($etime < 1) {
            return '0 giây';
        }

        $a = array(12 * 30 * 24 * 60 * 60 => 'năm',
            30 * 24 * 60 * 60 => 'tháng',
            24 * 60 * 60 => 'ngày',
            60 * 60 => 'giờ',
            60 => 'phút',
            1 => 'giây'
        );

        foreach ($a as $secs => $str) {
            $d = $etime / $secs;
            if ($d >= 1) {
                $r = round($d);
                return $r . ' ' . $str;
            }
        }
    }

    public static function convertMysqlToTimestamp($dateString)
    {
        $format = '@^(?P<year>\d{4})-(?P<month>\d{2})-(?P<day>\d{2}) (?P<hour>\d{2}):(?P<minute>\d{2}):(?P<second>\d{2})$@';
        preg_match($format, $dateString, $dateInfo);
        $unixTimestamp = mktime(
            $dateInfo['hour'], $dateInfo['minute'], $dateInfo['second'],
            $dateInfo['month'], $dateInfo['day'], $dateInfo['year']
        );
        return $unixTimestamp;
    }

    public static function timeElapsedStringFromMysql($dateString)
    {
        $ptime = CUtils::convertMysqlToTimestamp($dateString);
        return CUtils::timeElapsedString($ptime);
    }

    public static function cidrMatch($ip, $range)
    {
        list ($subnet, $bits) = explode('/', $range);
        $ip = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask = -1 << (32 - $bits);
        $subnet &= $mask; # nb: in case the supplied subnet wasn't correctly aligned
        return ($ip & $mask) == $subnet;
    }

    /**
     * @param $file_path : path of css, image
     * @return string
     */
    public static function getAssetUrl($file_path)
    {
        return \Yii::$app->urlManagerPublicApi->getBaseUrl() . '/' . $file_path;
    }

    /**
     * @param array $params : string action, parameters ex: ['site/index','a'=>1]
     * @return mixed
     */
    public static function createAbsoluteUrl($params = array())
    {
        return \Yii::$app->urlManagerPublicApi->createAbsoluteUrl($params);
    }

    public static function getMoneyString($money)
    {
        $str = '';
        if (strlen($money) <= 3) {
            return $money;
        } else {
            $length = strlen($money);
            for ($i = $length; $i > 0; $i -= 3) {
                if (strlen($money) < 3) {
                    $str = '.' . $money . $str;
                } else {
                    $str = '.' . substr($money, strlen($money) - 3, 3) . $str;
                    $money = substr($money, 0, strlen($money) - 3);
                }

            }
        }

        return substr($str, 1, strlen($str) - 1);
    }

    public static function generateRandomString($length = 6)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function generateRandomNumber($length = 6)
    {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function clientIP()
    {
        if (!empty($_SERVER['HTTP_CLIENTIP'])) {
            return $_SERVER['HTTP_CLIENTIP'];
        }

        if (!empty($_SERVER['X_REAL_ADDR'])) {
            return $_SERVER['X_REAL_ADDR'];
        }

        if (isset($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }
        return gethostbyname(gethostname()); // tra ve ip local khi chay CLI
    }


    public static function strToHex($string)
    {

        $hex = '';

        for ($i = 0; $i < strlen($string); $i++) {

            $ord = ord($string[$i]);

            $hexCode = dechex($ord);

            $hex .= substr('0' . $hexCode, -2);

        }

        return strToUpper($hex);

    }

    public static function hexToStr($hex)
    {

        $string = '';

        for ($i = 0; $i < strlen($hex) - 1; $i += 2) {

            $string .= chr(hexdec($hex[$i] . $hex[$i + 1]));

        }

        return $string;

    }


    /**
     * @param $str
     * @return array|int
     */
    public static function parseTokenStream($str)
    {
        $result = array();
        if (empty($str) || strlen($str) <= 32) {
            return $result;
        }

        $result['token'] = substr($str, 0, 32);
        $result['profileid'] = substr($str, 32);
        return $result;
    }


    public static function getStartDate($startDate)
    {
        $date = new DateTime($startDate);
        $date->setTime(00, 00, 00);
        return $date->format('Y-m-d H:i:s');
    }

    public static function getEndDate($endDate)
    {
        $date = new DateTime($endDate);
        $date->setTime(23, 59, 59);
        return $date->format('Y-m-d H:i:s');
    }

    public static function isToday($startDate, $endDate)
    {
        $today = date("Y-m-d H:i:s", time());
        $startDate = CUtils::getStartDate($startDate);
        $endDate = CUtils::getEndDate($endDate);
        if ($today >= $startDate && $today <= $endDate) {
            return true;
        } else return false;
    }

    public static function getDateViaFormat($datetoformat, $format = 'd/m/Y')
    {
        $date = new \DateTime($datetoformat);
        //$date->setTime(00, 00, 00);
        return $date->format($format);
    }

    public static function format_time_to_hour($t, $f = ':') // t = seconds, f = separator
    {
        return sprintf("%02d%s%02d%s%02d", floor($t / 3600), $f, ($t / 60) % 60, $f, $t % 60);
    }


    /**
     * Lay thoi gian cuoi cua ngay hien tai
     * @return int
     */
    public static function getLastTimeDate()
    {
        $currentDate = \DateTime::createFromFormat("d-m-Y H:i:s", date("d-m-Y") . " 23:59:59");
        return $currentDate->getTimestamp();
    }

    /**
     * Lay thoi gian dau cua ngay hien tai
     * @return int
     */
    public static function getFirstTimeDate()
    {
        $currentDate = \DateTime::createFromFormat("d-m-Y H:i:s", date("d-m-Y") . " 00:00:00");
        return $currentDate->getTimestamp();
    }

    public static function convertStringToTimeStamp($dateString, $inputFormat)
    {

        $dateFormat = \DateTime::createFromFormat($inputFormat, $dateString);
        if ($dateFormat && $dateFormat->format($inputFormat) === $dateString) {
            return ($dateFormat) ? $dateFormat->getTimestamp() : null;
        }else{
            return null;
        }
//        Yii::info("Timestamp: " . $dateFormat->getTimestamp());
        //echo $dateFormat->format($output);
//        return ($dateFormat) ? $dateFormat->getTimestamp() : null;
    }

    public static function getTelcoName($mobileNumber)
    {

        $mobileNumber = str_replace('+84', '84', $mobileNumber);

        if (preg_match('/^(84|0|)(89|90|93|120|121|122|126|128|70|76|77|78|79)\d{7}$/', $mobileNumber, $matches)) {
            return "Mobifone";
        }
        if (preg_match('/^(84|0|)(88|91|94|123|124|125|127|129|81|82|83|84|85)\d{7}$/', $mobileNumber, $matches)) {
            return "Vinaphone";
        }
        if (preg_match('/^(84|0|)(86|96|97|98|162|163|164|165|166|167|168|169|32|33|34|35|36|37|38|39)\d{7}$/', $mobileNumber, $matches)) {
            return "Viettel";
        }
        if (preg_match('/^(84|0|)(92|56|58)\d{7}$/', $mobileNumber, $matches)) {
            return "Vietnamobile";
        }
        if (preg_match('/^(84|0|)(99|199|59)\d{7}$/', $mobileNumber, $matches)) {
            return "Gmobile";
        }

        return "Other";
    }

    /**
     *Validate dau so viettel
     * @param string $mobileNumber
     * @param int $typeFormat : 0: format 84xxx, 1: format 0xxxx, 2: format xxxx
     * @return String valid mobile
     */
    public static function validateMsisdn($mobileNumber, $typeFormat = 0)
    {

        $mobileNumber = trim($mobileNumber);
        $valid_number = '';
        $mobileNumber = str_replace('+84', '84', $mobileNumber);
        if(self::getTelcoName($mobileNumber) == 'Other'){
            return '';
        }
        $format_match = '/^(84|0|)(\d{9,10})$/';

        if (preg_match($format_match, $mobileNumber, $matches)) {

//            Yii::info($matches);
            /**
             * $typeFormat == 0: 8491xxxxxx
             * $typeFormat == 1: 091xxxxxx
             * $typeFormat == 2: 91xxxxxx
             */
            $country = '84';
            if ($typeFormat == 0) {
                if ($matches[1] == '0' || $matches[1] == '') {
                    $valid_number = preg_replace('/^(0||)/', $country, $mobileNumber);
                } else {
                    $valid_number = $mobileNumber;
                }
            } else if ($typeFormat == 1) {
                if ($matches[1] == '84' || $matches[1] == '') {
                    $valid_number = preg_replace('/^(84||)/', '0', $mobileNumber);
                } else {
                    $valid_number = $mobileNumber;
                }
            } else if ($typeFormat == 2) {
                if ($matches[1] == '84' || $matches[1] == '0') {
                    $valid_number = preg_replace('/^(84|0)/', '', $mobileNumber);
                } else {
                    $valid_number = $mobileNumber;
                }
            }
        }
        $valid_number = preg_replace("/^8484/", '84',$valid_number);
        if((strlen($valid_number) <= 10 || strlen($valid_number) >= 12) && $typeFormat == 0){
            return '';
        }
        return $valid_number;
    }

    public static function errorForEditable($errors, $index)
    {
        $editable = array();
        foreach ($errors as $attribute => $error) {
            list($model, $attribute_id) = explode('-', $attribute);
            $newID = $model . '-' . $index . '-' . $attribute_id;
            $editable[$newID] = $error;
        }
        return $editable;
    }

    /**
     * Merge subarray objects to array objects by key merge data.
     * array_need_merge = [['A' => 1, 'name'=>'n1'], ['B' => 2, 'name'=>'n2'], ['C' => 3, 'name'=>'n3']]
     * array_merge = [['D' => 1, 'name'=>'n1'], ['C' => 2, 'name'=>'f2'], ['E' => 4, 'name'=>'n4']]
     * expect = [['A' => 1, 'name'=>'n1'], ['B' => 2, 'name'=>'f2'], ['C' => 2, 'name'=>'n3'], ['D' => 4, 'name'=>'n4'],['E' => 4, 'name'=>'n4']]
     *
     * @param $array_need_merge
     * @param $array_merge
     * @return array
     */
    static function mergeArrayObjectsSameExistsOrNot($array_need_merge, $array_merge = [])
    {
        if (empty($array_need_merge)) {
            return $array_merge;
        }
        if (!is_array($array_need_merge))
            $array_need_merge = [];

        // update new data if duplicate key
        foreach ($array_need_merge as $key => &$item) {
            if (isset($array_merge[$key])) {
                // Add gia tri tu mang array_merge sang array_need_merge
                if (is_array($item))
                    $item = array_merge($item, $array_merge[$key]);
                else
                    $item = $array_merge[$key];
                unset($array_merge[$key]);
            }
        }
        foreach ($array_merge as $k => $v) {
            $array_need_merge[$k] = $v;
        }
        return $array_need_merge;
    }

    /**
     * Merge subarray objects to array objects by key merge data.
     * array_need_merge = [['floorid' => 1, 'name'=>'n1'], ['floorid' => 2, 'name'=>'n2'], ['floorid' => 3, 'name'=>'n3']]
     * array_merge = [['floorid' => 1, 'name'=>'n1'], ['floorid' => 2, 'name'=>'f2'], ['floorid' => 4, 'name'=>'n4']]
     * key_merge = 'floorid'
     * expect = [['floorid' => 1, 'name'=>'n1'], ['floorid' => 2, 'name'=>'f2'], ['floorid' => 3, 'name'=>'n3'], ['floorid' => 4, 'name'=>'n4']]
     *
     * @param $key_merge
     * @param $array_need_merge
     * @param $array_merge
     * @return array
     */
    public static function mergeArrayObjectsSameKeyOrNot($key_merge, $array_need_merge, $array_merge, $arr_static = [])
    {
        /**
         * Lay mang gia tri cua key_merge tren array_merge
         * expect = [[1=>0],[2 => 1], [4 => 2]]
         */
        $arr_id = [];
        foreach ($array_merge as $key => $item) {
            if (isset($item[$key_merge])) {
                $id = $item[$key_merge];
                $arr_id[$id] = $key;
            } else {
                return $array_need_merge;
            }
        }

        /**
         *Thay doi nhung phan tu co gia tri giong nhau cua 2 bang?
         * expect: array_need_merge = [['floorid' => 1, 'name'=>'n1'], ['floorid' => 2, 'name'=>'f2'], ['floorid' => 3, 'name'=>'n3']]
         */
        if (!empty($array_need_merge)) {
            // update new data if duplicate key
            foreach ($array_need_merge as $key => $item) {
                if (isset($item[$key_merge])) {
                    $id = $item[$key_merge];
                    if (isset($arr_id[$id])) {
                        $item_old = $array_need_merge[$key];
                        $item_new = $array_merge[$arr_id[$id]];
                        if (!empty($arr_static)) {
                            foreach ($arr_static as $value_static) {
                                if (isset($item_old[$value_static])) {
                                    $item_new[$value_static] = $item_old[$value_static];
                                }
                            }
                        }

                        // Add gia tri tu mang array_merge sang array_need_merge
                        $array_need_merge[$key] = $item_new;
                        // Loai ra khoi mang
                        unset($arr_id[$id]);
                    }
                } else {
                    return $array_need_merge;
                }

            }
        }

        /**
         * Add them phan tu moi vao mang
         * expect: array_need_merge = [['floorid' => 1, 'name'=>'n1'], ['floorid' => 2, 'name'=>'f2'], ['floorid' => 3, 'name'=>'n3'], ['floorid' => 4, 'name'=>'n4']]
         */
        foreach ($arr_id as $key => $value) {
            $array_need_merge[] = $array_merge[$value];
        }

        return $array_need_merge;
    }

    /**
     * Delete element in array by keys in sub-array and re-index after that
     *
     * @param $key_delete
     * @param $array_need_delete
     * @param $array_key_delete
     * @return array
     */
    public static function deleteElementOfArrayObjectsByArrayKeys($key_delete, $array_need_delete, $array_key_delete)
    {
        foreach ($array_need_delete as $key => $value) {
            if (in_array($value[$key_delete], $array_key_delete)) {
                unset($array_need_delete[$key]);
            }
        }

        return array_values($array_need_delete);
    }

    /**
     * Find element in array by keys in sub-array and re-index after that
     *
     * @param $key_find
     * @param $array_need_find
     * @param $array_key_res
     * @return array
     */
    public static function findElementOfArrayObjectsByArrayKeys($key_find, $array_need_find, $array_key_find)
    {
        $array_key_res = [];
        foreach ($array_need_find as $key => $value) {
            if (in_array($value[$key_find], $array_key_find)) {
                $array_key_res[] = $array_need_find[$key];
            }
        }
        return array_values($array_key_res);
    }

    /**
     * Tạo mã ngẫu nhiên hỗ trợ chức năng xác nhận thài khoản
     * @param int $chars
     * @return $this
     */
    public static function setVerifyCode($chars = 4)
    {
        $letters = '1234567890';
        return substr(str_shuffle($letters), 0, $chars);
    }

    public static function slugify($str)
    {
        $str = preg_replace('/[^A-Za-z0-9\-_ ]/', '', $str);
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
        $str = preg_replace("/(đ)/", 'd', $str);
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
        $str = preg_replace("/(Đ|Ð)/", 'D', $str);
        $str = preg_replace('/\s+/', '-', mb_strtolower(trim(strip_tags($str)), 'UTF-8'));
        $str = preg_replace('/[^A-Za-z0-9\-_ ]/', '', $str);
        $str = trim($str,'-');
        return $str;
    }


    /*
    * xử lý lại key array
    */
    public static function arrayCombine($array, $type = 0)
    {
        $array_new = $array;
        foreach ($array as $k => $v) {
            if (gettype($v) == 'array') {
                $array_new[$k] = self::arrayCombine($v, $type);
            }
        }
        $keys = array_keys($array_new);
        if ($type == 0) {
            $keys = array_map(function ($key) {
                return str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
            }, $keys);
        } else {
            $keys = array_map(function ($key) {
                $A = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
                $a = ['_a', '_b', '_c', '_d', '_e', '_f', '_g', '_h', '_i', '_j', '_k', '_l', '_m', '_n', '_o', '_p', '_q', '_r', '_s', '_t', '_u', '_v', '_w', '_x', '_y', '_z'];
                return trim(str_replace($A, $a, $key), '_');
            }, $keys);
        }
        $array_new = array_combine($keys, $array_new);
        return $array_new;
    }

    public static function startTimeWeek($time){
        $start = (date('w', $time) == 0) ? $time : strtotime('last sunday', $time);
        $start_time_week = date('Y-m-d 00:00:00', $start); // đầu tuần
        return strtotime($start_time_week);
    }

    public static function startTimeNextWeek($time){
        $start = (date('w', $time) == 0) ? $time : strtotime('last sunday', $time);
        $next_start_time_week = date('Y-m-d 00:00:00', strtotime('next day', strtotime('next saturday', $start))); // đầu tuần tiếp theo
        return strtotime($next_start_time_week);
    }

    /*
     * input: integer
     * output: string
     * vd: input 5
     * output: (1) + (2) + (3) + (4) + (5)
     */
    public static function subtotalShowText($index){
        $t = '';
        for($i = 1;$i < $index;$i++){
            $t .= '('.$i.') + ';
        }
        return trim($t,' + ');
    }

    public static function convertMessageError($errorMessage, $r = false){
        $message = Yii::t('resident', "System busy");
        if($r == true){
            $message = $errorMessage;
        }
        if(strpos($errorMessage,"Incorrect string value") !== false){
            $message = Yii::t('resident','Nội dung không được chứa ký tự đặc biệt!');
        }else if(strpos($errorMessage,"Numeric value out of range") !== false){
            $message = Yii::t('resident','Giá trị số quá lớn!');
        }else if(strpos($errorMessage,"Database Exception") !== false){
            $message = Yii::t('resident','Dữ liệu không hợp lệ!');
        }else if(strpos($errorMessage,"Exception: SQLSTATE") !== false){
            $message = Yii::t('resident','Dữ liệu không hợp lệ!');
        }else if(strpos($errorMessage, "SQLSTATE[HY000]") !== false){
            $message = Yii::t('resident','Dữ liệu không hợp lệ!');
        }else if(strpos($errorMessage,"Unknown JSON") !== false){
            $message = Yii::t('resident','Dữ liệu không hợp lệ!');
        }else if(strpos($errorMessage,"to a member function getTimestamp") !== false){
            $message = Yii::t('resident','Định dạng ngày tháng không đúng!');
        }
        Yii::warning($message);
        return $message;
    }
}


?>
