DROP TABLE dbo.dalance

CREATE TABLE dbo.dalance (
  id                     INT IDENTITY PRIMARY KEY,
  date_insert            DATETIME DEFAULT getdate() NULL,

  source_url             VARCHAR(255)               NULL,
  nickname               VARCHAR(50)                NULL,
  fullname               VARCHAR(100)               NULL,
  surname                VARCHAR(50)                NULL,
  name                   VARCHAR(50)                NULL,
  patronymic             VARCHAR(50)                NULL,
  avatar_url             VARCHAR(255)               NULL,
  ratio                  FLOAT                      NULL,
  ratio_visit            FLOAT                      NULL,
  average_user_ratio_min FLOAT                      NULL,
  average_user_ratio_max FLOAT                      NULL,
  date_register          DATETIME                   NULL,
  date_register_str      VARCHAR(50)                NULL,
  date_last_visit        DATETIME                   NULL,
  date_last_visit_str    VARCHAR(50)                NULL,
  position               VARCHAR(200)               NULL,
  phone                  VARCHAR(100)               NULL,
  skype                  VARCHAR(50)                NULL,
  location               VARCHAR(100)               NULL,
  site                   VARCHAR(255)               NULL,
  icq                    VARCHAR(50)                NULL,
  description            VARCHAR(900)               NULL,
  work_experience        VARCHAR(200)               NULL
)