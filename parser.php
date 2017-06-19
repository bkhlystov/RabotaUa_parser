<?php

use Common\Utility\Web\CUrlHelper;
use Common\Logger\BLL\SimpleLogger;
use Common\Traits\Model;
use Parse\Engine\BLL\LoadEngineRestore;
use Common\DAL\Sybase\SybaseWebServer;
use PB\Conveyor\BLL\ConveyorManager;

require_once __DIR__ . '/bootstrap.php';

require_once __DIR__ . '/Freelancer.php';


function first_auth()       //первая павторизация для получения данных в куки
{
    $curlHelper = new CUrlHelper();
    $url = 'https://rabota.ua';
    $curlHelper->cookieFileName = __DIR__ .'/store2/cookie.txt';
    $response = $curlHelper->http($url);
    unset($curlHelper);
}

function auth(){
    $curlHelper = new CUrlHelper();
    $page = file_get_contents('./store2/cookie.txt');
    preg_match('@ASP\.NET_SessionId\s*(.*?)$@mis', $page, $ASP);
    preg_match('@rua-usm.date.\s*(.*?)$@mis', $page, $rua_usm_date);
    preg_match('@rua-usm2\sdate=\s*(.*?)$@mis', $page, $rua_usm2);
    preg_match('@user_city\s*(.*?)$@mis', $page, $user_city);
    preg_match('@VisitorDone\s*(.*?)$@mis', $page, $VisitorDone);


    $ASP = trim($ASP[1]);
    $rua_usm_date = trim($rua_usm_date[1]);
    $rua_usm2 = trim($rua_usm2[1]);
    $user_city = trim($user_city[1]);
    $VisitorDone = trim($VisitorDone[1]);

    $authUrl = 'https://rabota.ua/employer/login?redirectUrl=%2femployer%2fnotepad%2fvacancies%3fvacDesignType%3dByStatePublicated%26userId%3d0';
    $params = [
        '__EVENTTARGET'=>'ctl00$content$ZoneLogin$btnLogin',
        '__EVENTARGUMENT'=>'',
        '__VIEWSTATE'=>'K6LLO0euZHsBo/xThG/2HjRP4KwQu57hsLSKi9oa3jgxEGn00peIGhVo/8iQ1b+rxm74U3R7iatpAG2WFYinlgNTO21ms3jtLkx6NYl0YdCgcK0sJBBCaCr9znoeqWLBUj3LluW86OYCzNs7v6ns8FHWO/fF4hvII4eeY9PG5lajbNA04oAWUd6Q33c1b2Rj',
        '__VIEWSTATEGENERATOR'=>'BFE5824D',
        'ctl00$content$ZoneLogin$txLogin'=>'lina.sergeeva@privatbank.ua',
        'ctl00$content$ZoneLogin$txPassword'=>'0509552740',
        'ctl00$content$ZoneLogin$chBoxRemember'=>'on',
        'ctl00$Footer$ddlLanguage'=>'2'
    ];
    $headers = [
        'Accept'=>'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
        'Accept-Encoding'=>'gzip, deflate, br',
        'Accept-Language'=>'ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4',
        'Cache-Control'=>'max-age=0',
        'Connection'=>'keep-alive',
        'Content-Length'=>'512',
        'Content-Type'=>'application/x-www-form-urlencoded',
        'Cookie'=>"b=b; ASP.NET_SessionId={$ASP}; rua-usm=date={$rua_usm_date}; user_city={$user_city}; _ym_uid=1490108059647375242; _ym_isad=1; _ym_visorc_8187883=b; __utmt=1; lld=131346657384251761; vacancy_prev_link=url=https%3a%2f%2frabota.ua%2fpages%2femployer%2fnotepad%2fvacancies.aspx%3fvacDesignType%3dByStatePublicated%26userId%3d0; rua-usm2=date={$rua_usm2}; b=b; VisitorDone={$VisitorDone}; __utma=96869020.1757587373.1490108058.1490173380.1490183142.3; __utmb=96869020.40.9.1490183296510; __utmc=96869020; __utmz=96869020.1490108058.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); __utmv=96869020.|2=have_email=No=1",
        'Host'=>'rabota.ua',
        'Origin'=>'https://rabota.ua',
        'Referer'=>'https://rabota.ua/employer/login?redirectUrl=%2femployer%2fnotepad%2fvacancies%3fvacDesignType%3dByStatePublicated%26userId%3d0',
        'Upgrade-Insecure-Requests'=>'1',
        'User-Agent'=>'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36'
    ];

    $curlHelper->cookieFileName = __DIR__ . '/store2/cookie.txt';
    $response = $curlHelper->http($authUrl, 'POST', $params, $headers);

    return $response;
    unset($curlHelper);
}

function afterauth($pageINp,$arg){
    $curlHelper = new CUrlHelper();

    //считывание страницы переданно после авторизации
    preg_match('@id="__VIEWSTATE"\svalue="(.*?)"@mis', $pageINp, $view_state);
    $view_state = trim($view_state[1]);


    //Считывание куки
    $page = file_get_contents('./store2/cookie.txt');
    preg_match('@ASP\.NET_SessionId\s*(.*?)$@mis', $page, $ASP);
    preg_match('@rua-usm.date.\s*(.*?)$@mis', $page, $rua_usm_date);
    preg_match('@rua-usm2\sdate=\s*(.*?)$@mis', $page, $rua_usm2);
    preg_match('@user_city\s*(.*?)$@mis', $page, $user_city);
    preg_match('@VisitorDone\s*(.*?)$@mis', $page, $VisitorDone);
    preg_match('@RAB2AUTH\s(.*?)$@mis', $page, $RAB2AUTH);
    preg_match('@lld\s*(.*?)$@mis', $page, $lld);
    preg_match('@vacancy_prev_link\surl=\s*(.*?)$@mis', $page, $vacancy_prev_link);


    $ASP = trim($ASP[1]);
    $rua_usm_date = trim($rua_usm_date[1]);
    $rua_usm2 = trim($rua_usm2[1]);
    $user_city = trim($user_city[1]);
    $VisitorDone = trim($VisitorDone[1]);
    $RAB2AUTH = trim($RAB2AUTH[1]);
    $lld = trim($lld[1]);
    $vacancy_prev_link = trim($vacancy_prev_link[1]);

    $authUrl = 'https://rabota.ua/employer/notepad/vacancies?vacDesignType=ByStatePublicated&userId=0';
    $params = [
        '__EVENTTARGET'=>'ctl00$centerZone$employerVacancyList$grVwVacancy$ctl23$linkNext',
        '__VIEWSTATE'=>"{$view_state}",
        '__VIEWSTATEGENERATOR'=>'389788BD',
        'ctl00$centerZone$employerVacancyList$ddlUser'=>'0',
        'ctl00$Footer$ddlLanguage'=>'2'
    ];
    $headers = [
        'Accept'=>'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
        'Accept-Language'=>'ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4',
        'Cache-Control'=>'max-age=0',
        'Connection'=>'keep-alive',
        'Content-Type'=>'application/x-www-form-urlencoded',
        'Cookie'=>"b=b; b=b; ASP.NET_SessionId={$ASP}; rua-usm=date={$rua_usm_date}; user_city={$user_city}; _ym_uid=1490108059647375242; .RAB2AUTH={$RAB2AUTH}; lld={$lld}; _ym_isad=1; mp_6e747ac7e24b5cdc1b4516874be519c4_mixpanel=%7B%22distinct_id%22%3A%20%2215af99e0c620-00e7d5c5ca0f45-5b123112-1fa400-15af99e0c632f0%22%2C%22%24initial_referrer%22%3A%20%22https%3A%2F%2Frabota.ua%2Femployer%2Fnotepad%2Fcvs%3FvacancyId%3D6020247%26fld%3D1%26userId%3D0%22%2C%22%24initial_referring_domain%22%3A%20%22rabota.ua%22%7D; mp_mixpanel__c=0; rua-usm2=date={$rua_usm2}; __utmt=1; _ym_visorc_8187883=b; b=b; vacancy_prev_link=url={$vacancy_prev_link}; VisitorDone={$VisitorDone}; __utma=96869020.1757587373.1490108058.1490336448.1490348856.13; __utmb=96869020.18.10.1490348856; __utmc=96869020; __utmz=96869020.1490108058.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none)",
        'Host'=>'rabota.ua',
        'Origin'=>'https://rabota.ua',
        'Referer'=>'https://rabota.ua/employer/notepad/vacancies?vacDesignType=ByStatePublicated&userId=0',
        'Upgrade-Insecure-Requests'=>'1',
        'User-Agent'=>'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36'
    ];

    $curlHelper->cookieFileName = __DIR__ . '/store2/cookie.txt';
    $response = $curlHelper->http($authUrl, 'POST', $params, $headers);

    preg_match('/ctl00\$centerZone\$employerVacancyList\$grVwVacancy\$ctl23\$linkNext/', $response, $check);
    //Определение последней страницы
    if($check[0]){
        return $response;
    }else{
        return false;
    }
    unset($curlHelper);

}


function getlinks(){
    print('START');
    $put_file = "/opt/www/rabota_ua/result.csv";     //файл для записи
    $count_Slink = 0;                       //Счетчик сколько раз не открыло первую ссылку
    $count_Thlink = 0;                       //Счетчик сколько раз не открыло первую ссылку
    $first = null;

    //Открываем начальную ссылку с опубликованными
    for($i=0;$i<=50;$i++){
        $curlHelper = new CUrlHelper();
        if($i==0){
            $first = auth();
        }
        if($i>0){
            $first = afterauth($first, $i);
        }
        if($first){

            preg_match_all('@class=.gtl_applynew\srua-p-c-red.\sstyle=.margin-left.\s6px.\sdisplay.\sinline.\shref=.(.*?).>@', $first, $link_matches);    //Только новые отклики на вакансии которые светятся

            $link_matches = $link_matches[1];
            //переходим по первой ссылке в разделе опубликованные
            for ($j = 0; $j < count($link_matches); $j++) {
                if ($link_matches[$j]) {

                    $curlHelper->cookieFileName = __DIR__ . '/store2/cookie.txt';
                    $second = $curlHelper->http("https://rabota.ua{$link_matches[$j]}");       //переход на страничку где лежат анкеты людей которые откликнулись на вакансию
                    if ($second) {
                        preg_match_all('/class=.rua-p-t_16 rua-p-c-default ga_cv_view_cv. href=.(.*?).>/', $second, $third_matches);
                        $third_matches = $third_matches[1];
                        $count_Slink = 0;
                        //переходим далее в каждое резюме
                        for ($a = 0; $a < count($third_matches); $a++) {
                            if ($third_matches[$a]) {
                                $curlHelper->cookieFileName = __DIR__ . '/store2/cookie.txt';
                                $third = $curlHelper->http("https://rabota.ua{$third_matches[$a]}");
                                if ($third) {
                                    $count_Thlink = 0;
                                    $freelancer = new Freelancer();
                                    $freelancer->parseHtml($third);
                                    $dateLog = date("Y-m-d");
                                    //file_put_contents("./pages/link{$i}.pageS{$j}.pageTh{$a}.{$freelancer->name}.html", $third, FILE_APPEND);      //сохранение исходников интернет страниц
                                    //логирование записей в файл
                                    file_put_contents("/opt/www/rabota_ua/pages/{$dateLog}.log.txt", "link{$i}.pageS{$j}.pageTh{$a}.{$freelancer->name}"."\r\n", FILE_APPEND);


//                                    $title = "https://rabota.ua{$third_matches[$a]}".' ` '.$freelancer->otclick_job.' ` '.$freelancer->seek_job.' ` '.$freelancer->name.' ` '.$freelancer->date_birth.' ` '.$freelancer->relocate.' ` '.$freelancer->email.' ` '.$freelancer->sity.' ` '.$freelancer->phone.' ` '.$freelancer->skype.' ` '.$freelancer->resume_link;
//                                    file_put_contents($put_file, $title."\r\n", FILE_APPEND);
                                    print("записало link{$i}.pageS{$j}.pageTh{$a}"."\r\n");
//
//==================================Блок записи данных в конвейер========================================================
                                    //Мой тестовый конвеер
//                                    $conveyorId = 120898;
//                                    $loginConvejor = 17724;
//                                    $passwordConvejor = "zqTyHU10glETzB5jvXX5wBtASJYwtOA7D0d5sy6VDocqKSRVeW";

                                    //Рабочий конвеер
//                                    $conveyorId = 131035;
//                                    $loginConvejor = 18864;
//                                    $passwordConvejor = "6rVognmaKTrJMmso5KXfijMvAMHbsWX1ALj63lfw39cbPonMk4";
//
//                                    $data = array(
//                                        'DateInsert'=>date("Y-m-d"),
//                                        'LinkToPage'=>'https://rabota.ua'."{$third_matches[$a]}",
//                                        'OtclickJob'=>"{$freelancer->otclick_job}",
//                                        'SeekingJob'=>"{$freelancer->seek_job}",
//                                        'Name'=>"{$freelancer->name}",
//                                        'DateBirth'=>"{$freelancer->date_birth}",
//                                        'Relocate'=>"{$freelancer->relocate}",
//                                        'Email'=>"{$freelancer->email}",
//                                        'Sity'=>"{$freelancer->sity}",
//                                        'Phone'=>"{$freelancer->phone}",
//                                        'Skype'=>"{$freelancer->skype}",
//                                        'DownloadResumeLink'=>"{$freelancer->resume_link}"
//                                    );
//
//                                    $conveyorManager = new ConveyorManager($loginConvejor, $passwordConvejor);    //параметры конвеера
//                                    $conveyorManager->addSingleTask($conveyorId, $data);
//                                    unset($conveyorManager);

////=====================================Блок записи в Базу Данных  ===================================================================
                                    //запихиваем в БД ASE
                                        $dataManager = new SybaseWebServer(SybaseWebServer::TERMIT);
                                        $dataManager->open();

                                        //вносим клиентов с несколькими телефонами
                                        $arr_phone = $freelancer->phone;
                                        for($f=0;$f<count($arr_phone);$f++){
                                            $chahge = array('\'', '"');     //заменяем строки там где (' или ")

                                            $LinkToPage = str_replace($chahge, "", trim("https://rabota.ua{$third_matches[$a]}"));
                                            $OtclickJob = str_replace($chahge, "", trim("{$freelancer->otclick_job}"));
                                            $SeekingJob = str_replace($chahge, "", trim("{$freelancer->seek_job}"));
                                            $Name = str_replace($chahge, "", trim("{$freelancer->name}"));
                                            $DateBirth = str_replace($chahge, "", trim("{$freelancer->date_birth}"));
                                            $Relocate = str_replace($chahge, "", trim("{$freelancer->relocate}"));
                                            $Email = str_replace($chahge, "", trim("{$freelancer->email}"));
                                            $Sity = str_replace($chahge, "", trim("{$freelancer->sity}"));
                                            $Phone = "{$arr_phone[$f]}";
                                            $Skype = str_replace($chahge, "", trim("{$freelancer->skype}"));
                                            $DownloadResumeLink = str_replace($chahge, "", trim("{$freelancer->resume_link}"));


                                            $sql = "INSERT INTO dbo.RabotaUa_Privat_Response (Date_insert, LinkToPage, OtclickJob, SeekingJob, [Name], DateBirth, Relocate, Email, Sity, Phone, Skype, DownloadResumeLink)
                                                VALUES (convert(date,getdate()),'$LinkToPage','$OtclickJob','$SeekingJob','$Name','$DateBirth','$Relocate','$Email','$Sity','$Phone','$Skype','$DownloadResumeLink')
                                            ";

                                            if( $dataManager->query($sql)) {
                                                print('Вакансия  insert_OK'."\r\n");
                                            }
                                        }
                                        $dataManager->close();
                                        unset($dataManager);
                                     unset($freelancer);
//============================================================================================================================================


                                } else {
                                    if($count_Thlink<3){
                                        print('Не возможно открыть третью ссылку по сайту, пробую повторно открыть'."\r\n");
                                        $count_Thlink = $count_Thlink+1;      //счетчик
                                        //auth();     //повторная авторизация
                                        $a = $a-1;          //пытается повторно открыть предыдущую страницу
                                    }
                                    if($count_Thlink>=3){
                                        $count_Thlink = 0;
                                        print('Не получилось открыть третью страницу 3 раза, отмена открытия и прикращения работы цыкла'."\r\n");
                                        break;
                                    }
                                }
                            }
                        }
                    }else{
                        if($count_Slink<3){
                            print('Не возможно открыть вторую ссылку по сайту, пробую повторно открыть'."\r\n");
                            $count_Slink = $count_Slink+1;      //счетчик
                            //auth();     //повторная авторизация
                            $j = $j-1;          //пытается повторно открыть предыдущую страницу
                        }
                        if($count_Slink>=3){
                            $count_Slink = 0;
                            print('Не получилось открыть Вторую страницу 3 раза, отмена открытия и прикращения работы цыкла'."\r\n");
                            break;
                        }
                    }
                }
            }
        }else{
            break;
            exit('Не получилось открыть Первую страницу 3 раза, отмена открытия и прикращения работы кода');
        }
        unset($curlHelper);
    }
    print('END'."\r\n");
}

first_auth();
auth();
getlinks();


//Создание таблицы на ASE
//drop table dbo.RabotaUa_Privat_Response
//create table dbo.RabotaUa_Privat_Response (
//    Date_insert date,
//LinkToPage varchar(255),
//OtclickJob varchar(255),
//SeekingJob varchar(255),
//[Name] varchar(150),
//DateBirth  varchar(20),
//Relocate varchar(150),
//Email varchar(150),
//Sity  varchar(200),
//Phone varchar(30),
//Skype varchar(100),
//DownloadResumeLink varchar(255)
//)
