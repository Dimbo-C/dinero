<?php
/**
 * Created by IntelliJ IDEA.
 * User: root
 * Date: 26.10.17
 * Time: 10:48
 */

namespace App\Helpers;


class QiwiSecurityHelper {
    public static function emailBinding($login, $mail) {
        $qiwiControl = QiwiGeneralHelper::getQiwiControlObject($login);
        $response = $qiwiControl->bindEmail($mail);

        $success = false;
        if ($response) {
            $success = (intval(json_decode($response)->code->value) == 0);
        }

        $options[] = $qiwiControl->getLastError();
        $options[] = $qiwiControl->getResponseData();

        return [
                'success' => $success,
                'options' => $options
        ];
    }

    public static function emailFetchToken($login) {
        $qiwiControl = QiwiGeneralHelper::getQiwiControlObject($login);
        $response = $qiwiControl->emailUnbindingToken();
        //        dd($response);
        $response = json_decode($response);

        $success = $response->code->_name == "CONFIRM";
        $token = $success ? $response->identifier : "null";

        $args[] = $qiwiControl->getLastError();
        $args[] = $qiwiControl->getResponseData();

        return [
                'success' => $success,
                'token' => $token,
                'args' => $args
        ];
    }

    public static function emailUnbinding($login, $code, $token) {
        $qiwiControl = QiwiGeneralHelper::getQiwiControlObject($login);
        $response = $qiwiControl->unbindEmail($code, $token);
        if ($response) {
            $success = (intval(json_decode($response)->code->value) == 0);
        } else {
            $success = false;
        }

        $args[] = $qiwiControl->getLastError();
        $args[] = $qiwiControl->getResponseData();

        return [
                'success' => $success,
                'token' => $token,
                'args' => $args
        ];
    }

    public static function setSecurityAttribute($login, $attribute, $enabled) {
        $qiwiControl = QiwiGeneralHelper::getQiwiControlObject($login);
        $success = $qiwiControl->setQIWISecuritySetting($attribute, $enabled);
        $token = $qiwiControl->getResponseData();

        return [
                'success' => $success,
                'token' => $token
        ];
    }

    /**
     * @param $login
     * @param $enabled bool change setting to this state
     * @return array result
     *
     */
    public static function smsConfirmation($login, $enabled) {
        $qiwiControl = QiwiGeneralHelper::getQiwiControlObject($login);
        $success = $qiwiControl->setQIWISecuritySetting("SMS_CONFIRMATION", $enabled);
        $token = $qiwiControl->$success['data'];

        return [
                'success' => $success,
                'token' => $token
        ];
    }

    public static function userConfirmBySMS($login, $token, $code) {
        $qiwiControl = QiwiGeneralHelper::getQiwiControlObject($login);
        $success = $qiwiControl->userConfirmBySMS("SMS_CONFIRMATION", $token, $code);

        return ['success' => $success];
    }

    public static function getSecuritySettings($login) {
        $qiwiControl = QiwiGeneralHelper::getQiwiControlObject($login);
        $settings = $qiwiControl->getQIWISecuritySettings();
        $qiwiControl->removeCookies();

        return $settings;
    }

    public static function callConfirmFetchToken($login) {
        $qiwiControl = QiwiGeneralHelper::getQiwiControlObject($login);

        return $qiwiControl->setQIWISecuritySetting("CALL_CONFIRMATION", false);

        //        if (!$token) {
        //            return ["success" => false,];
        //        } else {
        //            return [
        //                    "success" => false,
        //                    "token" => $token
        //            ];
        //        }
    }
}