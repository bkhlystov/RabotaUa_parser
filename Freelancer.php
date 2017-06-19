<?php
/**
 * Created by PhpStorm.
 * User: dn020191kbl1
 * Date: 09.06.17
 * Time: 9:00
 */


use Common\Traits\Model;
use Common\Utility\GeneralPurpose\NormalizeHelper;

class Freelancer
{
    use Model;

    public $otclick_job;
    public $seek_job;
    public $name;
    public $date_birth;
    public $relocate;
    public $email;
    public $sity;
    public $phone;
    public $skype;
    public $resume_link;

    protected function norm_str($str){
        $str = str_replace('quot;', '', $str);
        $str = str_replace('#39;', '', $str);
        $str = str_replace('lt;', '', $str);
        $str = str_replace('gt;', '', $str);
        $str = str_replace('amp;', '', $str);
        $str = str_replace('nbsp;', '', $str);
        $str = str_replace('&', '', $str);
        $str = str_replace('Отклик на вакансию: ', '', $str);
        $res = str_replace('Отклик на вакансию:', '', $str);

        return $res;
    }

    public function parseHtml($html)
    {
        //Отклик на вакарсию
        if (preg_match('/<span id=.centerZone_BriefResume1_CvView1_cvHeader_txtJobName. class=.muted.>(.*?)<\/span>/', $html, $matchOtclick)) {
            $matchOtclick = str_replace("\r\n", ' ', (trim($matchOtclick[1])));
            $matchOtclick = $this->norm_str($matchOtclick);
            $this->otclick_job = strip_tags($matchOtclick);
            unset($matchOtclick);
        }else{
            $this->otclick_job='';
        }

        if(preg_match('/id=.centerZone_BriefResume1_CvView1_cvHeader_ddlVacancies. class=.ddlVacancies input-block-level.><option value=..+?>(.*?)</', $html, $matchOtclick)){
            $matchOtclick = str_replace("\r\n", ' ', (trim($matchOtclick[1])));
            $matchOtclick = $this->norm_str($matchOtclick);
            $this->otclick_job = strip_tags($matchOtclick);
            unset($matchOtclick);
        }

        if(preg_match('/<p class=.rua-p-t_20.\s.+>(.*)/', $html, $matchOtclick)){
            $matchOtclick = str_replace("\r\n", ' ', (trim($matchOtclick[1])));
            $matchOtclick = $this->norm_str($matchOtclick);
            $this->otclick_job = strip_tags($matchOtclick);
            unset($matchOtclick);
        }

        //Ищу работу
        if (preg_match('/<span id=.centerZone_BriefResume1_CvView1_cvHeader_txtJobName. class=.muted.>(.*?)<\/span>/', $html, $matchSeek_job)) {
            $matchSeek_job = str_replace("\r\n", ' ', $matchSeek_job[1]);
            $matchSeek_job = $this->norm_str($matchSeek_job);
            $this->seek_job = strip_tags($matchSeek_job);
            unset($matchSeek_job);
        }else{
            $this->seek_job='';
        }

        //Имя соискателя
        if (preg_match('/<span id=.centerZone_BriefResume1_ViewAttachedCV1_cvHeader_lblName. class=.rua-p-t_20.>(.*?)<\/span>/', $html, $matchName)) {
            $matchName = str_replace("\r\n", ' ', $matchName[1]);
            $matchName = $this->norm_str($matchName);
            $this->name = strip_tags($matchName);
            unset($matchName);
        }else{
            $this->name='';
        }
        if (preg_match('/<span id=.centerZone_BriefResume1_CvView1_cvHeader_lblName. class=.rua-p-t_20.>(.*?)<\/span>/', $html, $matchName)) {
            $matchName = str_replace("\r\n", ' ', $matchName[1]);
            $matchName = $this->norm_str($matchName);
            $this->name = strip_tags($matchName);
            unset($matchName);
        }

        //Дата рождения
        if (preg_match('/<span id=.centerZone_BriefResume1_CvView1_cvHeader_lblBirthDateValue. class=.rua-p-t_13.><span class=..>(.*?)\(/', $html, $matchDate_birth)) {
            $matchDate_birth = str_replace("\r\n", ' ', $matchDate_birth[1]);
            $matchDate_birth = strip_tags($matchDate_birth);
            $this->date_birth = NormalizeHelper::Date2(trim($matchDate_birth));
            unset($matchDate_birth);
        }else{
            $this->date_birth='';
        }

        //Готов к переезду
        if (preg_match('/<span id=.centerZone_BriefResume1_CvView1_cvHeader_lnlMoveValue. class=.rua-p-t_13.>(.*?)<\//', $html, $match_relocate)) {
            $match_relocate = str_replace("\r\n", ' ', $match_relocate[1]);
            $match_relocate = $this->norm_str($match_relocate);
            $this->relocate = strip_tags($match_relocate);
            unset($match_relocate);
        }else{
            $this->relocate='';
        }

        //Имейл
        if (preg_match('/<span id=.centerZone_BriefResume1_CvView1_cvHeader_lblEmailValue. class=.rua-p-t_13.>(.*?)<\/span>/', $html, $matchEmail)) {
            $matchEmail = str_replace("\r\n", ' ', $matchEmail[1]);
            $this->email = strip_tags($matchEmail);
            unset($matchEmail);
        }else{
            $this->email='';
        }
        
        if (preg_match('/<span id=.centerZone_BriefResume1_ViewAttachedCV1_cvHeader_lblEmailValue. class=.rua-p-t_13.>(.*?)<\/span>/', $html, $matchEmail)) {
            $matchEmail = str_replace("\r\n", ' ', $matchEmail[1]);
            $this->email = strip_tags($matchEmail);
            unset($matchEmail);
        }

        //Город
        if (preg_match('/<span id=.centerZone_BriefResume1_CvView1_cvHeader_lblRegionValue. class=.rua-p-t_13.>(.*?)<\/span>/', $html, $matchSity)) {
            $matchSity = str_replace("\r\n", ' ', $matchSity[1]);
            $matchSity = $this->norm_str($matchSity);
            $this->sity = strip_tags($matchSity);
            unset($matchSity);
        }else{
            $this->sity='';
        }

        //Телефон нормализатор
        if (preg_match('/<span id=.centerZone_BriefResume1_CvView1_cvHeader_lblPhoneValue. class=.rua-p-t_13.>(.*?)<\/span>/', $html, $matchPhone)) {
            $matchPhone = str_replace("\r\n", ' ', $matchPhone[1]);
            $matchPhone = NormalizeHelper::Phone(trim($matchPhone));
            $not_chenge = $matchPhone;
            $matchPhone = str_replace('-', '', trim($matchPhone));
            $matchPhone = str_replace(';', ' ', trim($matchPhone));
            $matchPhone = str_replace('/', ' ', trim($matchPhone));
            $matchPhone = str_replace('(', '', trim($matchPhone));
            $matchPhone = str_replace(') ', '', trim($matchPhone));
            $matchPhone = str_replace(')', '', trim($matchPhone));
            $matchPhone = str_replace('+38', '', trim($matchPhone));
            $matchPhone = str_replace('+7', '', trim($matchPhone));
            $matchPhone = str_replace(', ', ' ', trim($matchPhone));
            $matchPhone = str_replace(',', ' ', trim($matchPhone));
            $matchPhone = preg_replace("/[а-я]|[a-z]/", '', $matchPhone);

            $matchPhone = explode(" ", $matchPhone);
            $tmp = array();
            for($i=0;$i<count($matchPhone);$i++){
                if(strlen($matchPhone[$i]) >3){
                    if(substr($matchPhone[$i], 0, 2) == '+38'){
                        $matchPhone[$i] = str_replace('+38', '', trim($matchPhone[$i]));
                    }
                    if(trim($matchPhone[$i]) == ''){
                        $part = $matchPhone[$i];
                    }else{
                        $part = '+38'.$matchPhone[$i];
                    }
                }
                if(strlen(trim($matchPhone[$i])) == 3){
                    $part = '+38'.$matchPhone[$i].$matchPhone[$i+1];
                    unset($matchPhone[$i+1]);
                }

                if(strlen($part) == 12){
                    array_push($tmp, $part);
                }
                if(strlen($part)>12 || strlen($part)<12){
                    array_push($tmp, $not_chenge);
                    break;
                }

                array_push($tmp, $part);
            }

            $this->phone = $tmp;
            unset($matchPhone);
        }else{
            $this->phone='';
        }
        //Скайп
        if (preg_match('/<span id=.centerZone_BriefResume1_CvView1_cvHeader_lblSkypeValue. class=.rua-p-t_13 rua-g-force-wrap.>(.*?)<\/span>/', $html, $matchSkype)) {
            $matchSkype = str_replace("\r\n", ' ', $matchSkype[1]);
            $this->skype = strip_tags($matchSkype);
            unset($matchSkype);
        }else{
            $this->skype='';
        }
        //Ссылка на резюме текстовой версии
        if (preg_match('/<a href=.(.*?). id=.centerZone_BriefResume1_ViewAttachedCV1_lnkGetFile./', $html, $matchResume)) {
            $matchResume = str_replace("\r\n", ' ', $matchResume[1]);
            $this->resume_link = 'https://rabota.ua'.strip_tags($matchResume);
            unset($matchResume);
        }else{
            $this->resume_link='';
        }

        if (preg_match('/<a.class=.rua-p-t_16. href=.(.*?).\s/', $html, $matchResume)) {
            $matchResume = str_replace("\r\n", ' ', $matchResume[1]);
            $this->resume_link = 'https://rabota.ua'.strip_tags($matchResume);
            unset($matchResume);
        }

    }

}