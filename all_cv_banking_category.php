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
    $curlHelper->cookieFileName = __DIR__ .'/store2/cookie2.txt';
    $response = $curlHelper->http($url);
}

function auth(){
    $curlHelper = new CUrlHelper();
    $page = file_get_contents('./store2/cookie2.txt');
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

    $curlHelper->cookieFileName = __DIR__ . '/store2/cookie2.txt';
    $response = $curlHelper->http($authUrl, 'POST', $params, $headers);
    return $response;

}

function arrayStartLinks($arg){
    $curlHelper = new CUrlHelper();


    //Считывание куки
    $page = file_get_contents('./store2/cookie2.txt');
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

    if($arg==1){
        $authUrl = 'https://rabota.ua/employer/find/cv_list?parentid=18&period=1&sort=date';

        $params = [
            'parentid'=>'18',
            'period'=>'1',
            'sort'=>'date'
        ];

        $headers = [
        'Accept'=>'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
        'Accept-Encoding'=>'gzip, deflate, sdch, br',
        'Accept-Language'=>'ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4',
        'Connection'=>'keep-alive',
        'Cookie'=>"b=b; ASP.NET_SessionId={$ASP}; rua-usm=date={$rua_usm_date}; .RAB2AUTH=$RAB2AUTH; lld={$lld}; vacancy_list_state=; b=b; rua-usm2=date={$rua_usm_date}; __utmt=1; mp_6e747ac7e24b5cdc1b4516874be519c4_mixpanel=%7B%22distinct_id%22%3A%20%2215c8baafafb90-041951136870af-323f5c0f-1fa400-15c8baafafd1cb%22%2C%22%24initial_referrer%22%3A%20%22https%3A%2F%2Frabota.ua%2Fcv%2F10310138%22%2C%22%24initial_referring_domain%22%3A%20%22rabota.ua%22%7D; mp_mixpanel__c=0; VisitorDone=1; _ga=GA1.2.371033033.1496991857; _gid=GA1.2.1471511302.1496991857; __utma=96869020.371033033.1496991857.1496991857.1496990525.1; __utmb=96869020.31.8.1496994489570; __utmc=96869020; __utmz=96869020.1496991857.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none)",
        'Host'=>'rabota.ua',
        'Upgrade-Insecure-Requests'=>'1',
        'User-Agent'=>'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36'
        ];
    }

    if($arg>1){
        $authUrl = "https://rabota.ua/employer/find/cv_list?parentid=18&period=1&sort=date&pg={$arg}";

        $params = [
            'parentid'=>'18',
            'period'=>'1',
            'sort'=>'date'
        ];

        $headers = [
            'Accept'=>'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Encoding'=>'gzip, deflate, sdch, br',
            'Accept-Language'=>'ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4',
            'Connection'=>'keep-alive',
            'Cookie'=>"b=b; ASP.NET_SessionId={$ASP}; rua-usm=date={$rua_usm_date}; .RAB2AUTH=$RAB2AUTH; lld={$lld}; vacancy_list_state=; b=b; rua-usm2=date={$rua_usm_date}; __utmt=1; mp_6e747ac7e24b5cdc1b4516874be519c4_mixpanel=%7B%22distinct_id%22%3A%20%2215c8baafafb90-041951136870af-323f5c0f-1fa400-15c8baafafd1cb%22%2C%22%24initial_referrer%22%3A%20%22https%3A%2F%2Frabota.ua%2Fcv%2F10310138%22%2C%22%24initial_referring_domain%22%3A%20%22rabota.ua%22%7D; mp_mixpanel__c=0; VisitorDone=1; _ga=GA1.2.371033033.1496991857; _gid=GA1.2.1471511302.1496991857; __utma=96869020.371033033.1496991857.1496991857.1496990525.1; __utmb=96869020.31.8.1496994489570; __utmc=96869020; __utmz=96869020.1496991857.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none)",
            'Host'=>'rabota.ua',
            'Upgrade-Insecure-Requests'=>'1',
            'User-Agent'=>'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36'
        ];
    }


    $curlHelper->cookieFileName = __DIR__ . '/store2/cookie2.txt';
    $response = $curlHelper->http($authUrl, 'POST', $params, $headers);

    preg_match_all('/itemprop=[\"|\']jobTitle[\"|\']><a.itemprop=.url..href=[\"|\'](.*?)[\"|\']>/', $response, $check);

    //Определение последней страницы
    if(count($check[1])>1){
        $check = $check[1];
        return $check;      //возвращаем массив ссылок
    }else{
        return false;
    }
    unset($curlHelper);

}

function openClientContacts($response, $link){
    $curlHelper = new CUrlHelper();

    //Считываем страницу
    preg_match('@id=[\"|\']__VIEWSTATE"\svalue=[\"|\'](.*?)[\"|\']@mis', $response, $view_state);
    preg_match('@id=[\"|\']__VIEWSTATEGENERATOR[\"|\']\svalue=[\"|\'](.*?)[\"|\']@mis', $response, $view_state_generator);
    preg_match('/id=.centerZone_BriefResume1_CvView1_cvHeader_hdnPrev.\svalue=[\"|\'](.*?)[\"|\']/', $response, $centerZone_BriefResume1_CvView1_cvHeader_hdnPrev);
    preg_match('/ctl00\$Footer\$ddlLanguage.+selected=.selected..value=[\"|\'](.*?)[\"|\']/', $response, $Footer_ddlLanguage);


    $view_state = trim($view_state[1]);
    $view_state_generator = trim($view_state_generator[1]);
    $centerZone_BriefResume1_CvView1_cvHeader_hdnPrev = trim($centerZone_BriefResume1_CvView1_cvHeader_hdnPrev[1]);
    $Footer_ddlLanguage = trim($Footer_ddlLanguage[1]);


    //Считывание куки
    $page = file_get_contents('./store2/cookie2.txt');
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



    $authUrl = "{$link}";

    $headers = [
    'Accept'=>'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
    //'Accept-Encoding'=>'gzip, deflate, br',
    'Accept-Language'=>'ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4',
    'Cache-Control'=>'max-age=0',
    'Connection'=>'keep-alive',
    'Content-Length'=>'2231',
    'Content-Type'=>'application/x-www-form-urlencoded',
    'Cookie'=>"b=b; ASP.NET_SessionId={$ASP}; rua-usm=date={$rua_usm_date}; .RAB2AUTH={$RAB2AUTH}; lld={$lld}; vacancy_list_state=; b=b; rua-usm2=date={$rua_usm2}; VisitorDone={$VisitorDone}; _ga=GA1.2.371033033.1496991857; _gid=GA1.2.1471511302.1496991857; __utmt=1; mp_6e747ac7e24b5cdc1b4516874be519c4_mixpanel=%7B%22distinct_id%22%3A%20%2215c53dd88150-0919dcc4de6a7c-323f5c0f-1fa400-15c53dd881642b%22%2C%22%24initial_referrer%22%3A%20%22https%3A%2F%2Frabota.ua%2F%22%2C%22%24initial_referring_domain%22%3A%20%22rabota.ua%22%2C%22utm_source%22%3A%20%22cvdb%22%2C%22utm_medium%22%3A%20%22cv%22%2C%22utm_campaign%22%3A%20%22rezume%22%7D; mp_mixpanel__c=0; __utma=96869020.371033033.1496991857.1496990525.1497002090.2; __utmb=96869020.4.9.1497002334002; __utmc=96869020; __utmz=96869020.1496991857.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none)",
    'Host'=>'rabota.ua',
    'Origin'=>'https://rabota.ua',
    'Referer'=>"{$link}",
    'Upgrade-Insecure-Requests'=>'1',
    'User-Agent'=>'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36'
    ];


    $params = [
    '__EVENTTARGET'=>'ctl00$centerZone$BriefResume1$CvView1$cvHeader$lnkOpenContact',
    '__EVENTARGUMENT'=>'',
    '__VIEWSTATE'=>"{$view_state}",
    '__VIEWSTATEGENERATOR'=>"{$view_state_generator}",
    'ctl00$centerZone$BriefResume1$CvView1$cvHeader$AjaxLogin1$txtLogin'=>'',
    'ctl00$centerZone$BriefResume1$CvView1$cvHeader$AjaxLogin1$txtPassword'=>'',
    'ctl00$centerZone$BriefResume1$CvView1$cvHeader$hdnPrev'=>"{$centerZone_BriefResume1_CvView1_cvHeader_hdnPrev}",
    'ctl00$centerZone$BriefResume1$CvView1$cvHeader$Refusal1$hdnCvId'=>'',
    'ctl00$centerZone$BriefResume1$CvView1$cvHeader$Refusal1$hdnArcType'=>'',
    'ctl00$centerZone$BriefResume1$CvView1$cvHeader$Refusal1$hdnId'=>'',
    'ctl00$centerZone$BriefResume1$CvView1$cvHeader$Refusal1$hdnUserName'=>'',
    'ctl00$centerZone$BriefResume1$CvView1$cvHeader$Refusal1$hdnMultyCvId'=>'',
    'ctl00$centerZone$BriefResume1$CvView1$cvHeader$Refusal1$hdnParentId'=>'',
    'ctl00$centerZone$BriefResume1$CvView1$cvHeader$hdnReAssignCV'=>'',
    'ctl00$centerZone$BriefResume1$CvView1$hdnPDrefresh'=>'',
    'ctl00$Footer$ddlLanguage'=>"{$Footer_ddlLanguage}",
    'ctl00$centerZone$BriefResume1$CvView1$cvHeader$Refusal1$txtRefuse'=>'',
    'ctl00$centerZone$BriefResume1$CvView1$cvHeader$txtNote'=>''
    ];

    $curlHelper->cookieFileName = __DIR__ . '/store2/cookie2.txt';
    $response = $curlHelper->http($authUrl, 'POST', $params, $headers);

    return $response;
}


function getlinks(){
    print_r('START'."\r\n");
    $count_Thlink = 0;                       //Счетчик сколько раз не открыло первую ссылку
    $first = null;
    $auth = null;

    //Открываем начальную ссылку с опубликованными
    for($i=1;$i<=50;$i++){
        $curlHelper = new CUrlHelper();
        $array_first_links = arrayStartLinks($i);

        if($array_first_links[0]){

            //переходим по первой ссылке в разделе Банки - Инвестиции - Лизинг в раздел первого CV
            for ($j = 0; $j < count($array_first_links); $j++) {
                if ($array_first_links[$j]) {

                    $curlHelper->cookieFileName = __DIR__ . '/store2/cookie2.txt';
                    $search_link = 'https://rabota.ua'."{$array_first_links[$j]}";
                    $second = $curlHelper->http($search_link);       //переход на страничку где лежат анкеты людей которые откликнулись на вакансию с кнопкой

                    $ClientContacts = openClientContacts($second, $search_link);

                    if ($ClientContacts) {

                            $count_Thlink = 0;
                            $freelancer = new Freelancer();
                            $freelancer->parseHtml($ClientContacts);
                            $dateLog = date("Y-m-d");
                            //file_put_contents("./pages/link{$i}.pageS{$j}.{$freelancer->name}.html", $third, FILE_APPEND);      //сохранение исходников интернет страниц
                            //логирование записей в файл
                            file_put_contents("/opt/www/rabota_ua/pages2/{$dateLog}.log.txt", "link{$i}.pageS{$j}.{$freelancer->name}"."\r\n", FILE_APPEND);

//                            $put_file = "/opt/www/rabota_ua/{$dateLog}result2.txt";     //файл для записи
//                            $title = "https://rabota.ua{$array_first_links[$j]}".' ` '.$freelancer->otclick_job.' ` '.$freelancer->seek_job.' ` '.$freelancer->name.' ` '.$freelancer->date_birth.' ` '.$freelancer->relocate.' ` '.$freelancer->email.' ` '.$freelancer->sity.' ` '.$freelancer->phone.' ` '.$freelancer->skype.' ` '.$freelancer->resume_link;
//                            file_put_contents($put_file, $title."\r\n", FILE_APPEND);
                            print("записало link{$i}.pageS{$j}"."\r\n");
//
//==================================Блок записи данных в конвейер========================================================
                            //Мой тестовый конвеер
//                                    $conveyorId = 120898;
//                                    $loginConvejor = 17724;
//                                    $passwordConvejor = "zqTyHU10glETzB5jvXX5wBtASJYwtOA7D0d5sy6VDocqKSRVeW";

                            //Рабочий конвеер
//                        $conveyorId = 131035;
//                        $loginConvejor = 18864;
//                        $passwordConvejor = "6rVognmaKTrJMmso5KXfijMvAMHbsWX1ALj63lfw39cbPonMk4";
//
//                        $data = array(
//                            'DateInsert'=>date("Y-m-d"),
//                            'LinkToPage'=>'https://rabota.ua'."{$array_first_links[$j]}",
//                            'OtclickJob'=>"{$freelancer->otclick_job}",
//                            'SeekingJob'=>"{$freelancer->seek_job}",
//                            'Name'=>"{$freelancer->name}",
//                            'DateBirth'=>"{$freelancer->date_birth}",
//                            'Relocate'=>"{$freelancer->relocate}",
//                            'Email'=>"{$freelancer->email}",
//                            'Sity'=>"{$freelancer->sity}",
//                            'Phone'=>"{$freelancer->phone}",
//                            'Skype'=>"{$freelancer->skype}",
//                            'DownloadResumeLink'=>"{$freelancer->resume_link}"
//                        );
//
//                        $conveyorManager = new ConveyorManager($loginConvejor, $passwordConvejor);    //параметры конвеера
//                        $conveyorManager->addSingleTask($conveyorId, $data);
//                        unset($conveyorManager);

////=====================================Блок записи в Базу Данных  ===================================================================
                            //запихиваем в БД Termit ASE
                                $dataManager = new SybaseWebServer(SybaseWebServer::TERMIT);
                                $dataManager->open();

                                //вносим клиентов с несколькими телефонами
                                $arr_phone = $freelancer->phone;
                                for($f=0;$f<count($arr_phone);$f++){
                                    $chahge = array('\'', '"');     //заменяем строки там где (' или ")

                                    $LinkToPage = str_replace($chahge, "", trim("https://rabota.ua{$array_first_links[$j]}"));
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


                                    $sql = "INSERT INTO dbo.RabotaUa_ALL_Banling_Kategory (Date_insert, LinkToPage, OtclickJob, SeekingJob, [Name], DateBirth, Relocate, Email, Sity, Phone, Skype, DownloadResumeLink)
                                                        VALUES (convert(date,getdate()),'$LinkToPage','$OtclickJob','$SeekingJob','$Name','$DateBirth','$Relocate','$Email','$Sity','$Phone','$Skype','$DownloadResumeLink')
                                                    ";

                                    if( $dataManager->query($sql)) {
                                        print("Вакансия {$LinkToPage} insert_OK \r\n");
                                    }
                                }

                                $dataManager->close();
                                unset($dataManager);

                            unset($freelancer);
//============================================================================================================================================


                    } else {
                        if($count_Thlink<3){
                            print('Не возможно открыть вторую ссылку по сайту, пробую повторно открыть'."\r\n");
                            $count_Thlink = $count_Thlink+1;      //счетчик
                            $j = $j-1;          //пытается повторно открыть предыдущую страницу
                        }
                        if($count_Thlink>=3){
                            $count_Thlink = 0;
                            print('Не получилось открыть вторую страницу 3 раза, отмена открытия и прикращения работы цыкла'."\r\n");
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