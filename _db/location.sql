INSERT INTO ADV.dalance(
date_insert, source_url, nickname, fullname, surname, [name], patronymic, avatar_url,
ratio, ratio_visit, average_user_ratio_min, average_user_ratio_max,
date_register, date_register_str, date_last_visit, date_last_visit_str, [position],
phone, skype, location, site, icq, description, work_experience)

location 'vmcpiase.RISK'
{
SELECT date_insert, source_url, nickname, fullname, surname, [name], patronymic, avatar_url,
ratio, ratio_visit, average_user_ratio_min, average_user_ratio_max,
date_register, date_register_str, date_last_visit, date_last_visit_str, [position],
phone, skype, location, site, icq, description, work_experience
    FROM dbo.dalance
}