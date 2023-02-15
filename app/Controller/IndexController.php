<?php

namespace App\Controller;

use App\Model\DB;
use App\Services\Bitrix;

class IndexController
{

    public function __construct()
    {
        $this->db = new DB();
    }

    public function index($request) : bool|array
    {

        $auth =  [
            'access_token' => $request['AUTH_ID'],
            'expires_in' => $request['AUTH_EXPIRES'],
            'application_token' => $request['APP_SID'],
            'refresh_token' => $request['REFRESH_ID'],
            'domain' => $request['DOMAIN'],
            'client_endpoint' => 'https://' . $request['DOMAIN'] . '/rest/',
        ];

        $user = $this->db->getUserByDomain($request['DOMAIN']);
        $userList = Bitrix::getUsersList($auth);
        $data['user_list'] = $userList;
        $data['option_list'] = $this->prepareUserList($userList);

        if(empty($user)){

            $app = Bitrix::getAppInfo($auth);
            $data['app_info'] = $app;
            $data['auth'] = $auth;
 
        } else {
            
            $data['user'] = $user;
            $settings = $this->db->getSettings($user['id']);
            $data['settings'] = $settings;
            $data['payed'] = $this->checkPayData($user['payed']);
            
        }

        return $data;

    }

    private function checkPayData($date): array
    {

        $rest = round((strtotime($date) - strtotime(date('Y-m-d H:i:s'))) / (60 * 60 * 24)) + 1;

        if(strtotime($date) < strtotime(date('Y-m-d H:i:s'))){
            $data['title'] = 'Необхідна оплата для подальшого використання програми!!!';
            $data['text'] = 'Тестовий період скінчився!';
            $data['style'] = 'text-danger';
        } elseif ($rest < 7){
            $data['title'] = `Закінчується строк використання програми!!! (кіль. днів - <b>`.$rest.`</b>)`;
            $data['text'] = 'Активно до: '.$date;
            $data['style'] = 'text-secondary';
        } else {
            $data['title'] = '';
            $data['text'] = 'Активно до: '.$date;
            $data['style'] = 'text-success';
        }

        return $data;

    }

    private function prepareUserList($list): string
    {
        $userOutput = '';
        foreach ($list as $item) {
            $fullName = $item["NAME"].'&nbsp;'.$item["LAST_NAME"];
            if ($fullName === '&nbsp;') {
                $fullName = $item["EMAIL"];
            }
            $userOutputID = $item["ID"];
            $userOutput .= '<option value="'.$userOutputID.'">'. $fullName .'</option>';
        }

        return $userOutput;
    }

}