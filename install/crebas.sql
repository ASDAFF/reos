/* ReOS is a vertical software for real estates.                               */
/* Copyright 2010 IT ELAZOS S.L.                                               */

/* This file is part of ReOS v2.x.x.                                           */

/* ReOS is free software: you can redistribute it and/or modify                */
/* it under the terms of the GNU Affero General Public License as published by */
/* the Free Software Foundation, either version 3 of the License, or           */
/* (at your option) any later version.                                         */

/* ReOS is distributed in the hope that it will be useful,                     */
/* but WITHOUT ANY WARRANTY, without even the implied warranty of              */
/* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the               */
/* GNU General Public License for more details.                                */

/* You should have received a copy of the GNU Affero General Public License    */
/* along with ReOS.  If not, see <http://www.gnu.org/licenses/>.               */
/*==============================================================*/
/* Database name:  ReOS 2.0.5                                   */
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     01/03/2010 18:16:47                          */
/*==============================================================*/


/*==============================================================*/
/* Table: arpa_acc_perfil                                       */
/*==============================================================*/
create table arpa_acc_perfil
(
   id_account           integer not null,
   dt_create            date,
   ind_active           tinyint,
   tp_servicio          set('1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20'),
   tp_propiedad         set('1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20'),
   set_properties       set('1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20'),
   ind_piscina          tinyint,
   num_parking          smallint,
   num_wc               smallint,
   num_dormitorios      smallint,
   int_superficie_const smallint,
   int_superficie       smallint,
   precio_alquiler      decimal(10,0),
   precio_compra        decimal(10,0),
   txt_comment          varchar(255),
   primary key (id_account)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Table: arpa_accounts                                         */
/*==============================================================*/
create table arpa_accounts
(
   id_account           integer not null auto_increment,
   dt_create            date,
   tp_state             tinyint,
   id_org               integer,
   id_position          integer,
   name_account         varchar(80),
   id_acisa             integer,
   id_price_lst         integer,
   txt_cif              char(9),
   txt_address1         varchar(80),
   txt_poblacion        varchar(50),
   txt_cp               varchar(30),
   txt_telf1            varchar(30),
   txt_telf2            varchar(30),
   txt_fax              varchar(30),
   txt_email1           varchar(50),
   txt_email2           varchar(50),
   txt_web              varchar(50),
   username             varchar(60) not null,
   password             varchar(32),
   txt_comment          varchar(255),
   ind_mailing          smallint,
   cod_lang             char(5),
   primary key (id_account)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Index: arpa_username                                         */
/*==============================================================*/
create unique index arpa_username on arpa_accounts
(
   username
);

/*==============================================================*/
/* Index: arpa_accounts_fk1                                     */
/*==============================================================*/
create index arpa_accounts_fk1 on arpa_accounts
(
   id_org
);

/*==============================================================*/
/* Index: arpa_accounts_fk2                                     */
/*==============================================================*/
create index arpa_accounts_fk2 on arpa_accounts
(
   id_position
);

/*==============================================================*/
/* Index: arpa_accounts_fk3                                     */
/*==============================================================*/
create index arpa_accounts_fk3 on arpa_accounts
(
   id_price_lst
);

/*==============================================================*/
/* Table: arpa_auth_user                                        */
/*==============================================================*/
create table arpa_auth_user
(
   user_id              varchar(32) not null,
   id_org               integer,
   username             varchar(32) not null,
   password             varchar(32) not null,
   name_user            varchar(50),
   txt_email            varchar(50),
   user_type            smallint not null,
   txt_telf1            varchar(20),
   txt_telf2            varchar(20),
   primary key (user_id)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Index: k_username                                            */
/*==============================================================*/
create unique index k_username on arpa_auth_user
(
   username
);

/*==============================================================*/
/* Index: arpa_auth_user_fk1                                    */
/*==============================================================*/
create index arpa_auth_user_fk1 on arpa_auth_user
(
   id_org
);

/*==============================================================*/
/* Table: arpa_bookings                                         */
/*==============================================================*/
create table arpa_bookings
(
   id_bookings          integer not null auto_increment,
   dt_create            date not null,
   id_immo              integer not null,
   id_account           integer not null,
   tp_state             integer not null,
   dt_start             date not null,
   dt_end               date not null,
   int_pers             integer,
   txt_comment          varchar(255),
   primary key (id_bookings)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Index: arpa_bookings_fk1                                     */
/*==============================================================*/
create index arpa_bookings_fk1 on arpa_bookings
(
   id_account
);

/*==============================================================*/
/* Index: arpa_bookings_fk2                                     */
/*==============================================================*/
create index arpa_bookings_fk2 on arpa_bookings
(
   id_immo
);

/*==============================================================*/
/* Table: arpa_categories                                       */
/*==============================================================*/
create table arpa_categories
(
   id_category          integer not null auto_increment,
   id_org               integer not null,
   id_parent_category   integer,
   cod_category         varchar(32) not null,
   primary key (id_category)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Table: arpa_comunidad                                        */
/*==============================================================*/
create table arpa_comunidad
(
   id_comunidad         smallint not null auto_increment,
   id_country           smallint,
   comdad_name          varchar(64) not null,
   primary key (id_comunidad)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Index: arpa_comunidad_fk1                                    */
/*==============================================================*/
create index arpa_comunidad_fk1 on arpa_comunidad
(
   id_country
);

/*==============================================================*/
/* Table: arpa_contacts                                         */
/*==============================================================*/
create table arpa_contacts
(
   id_contact           integer not null auto_increment,
   id_account           integer,
   dt_create            date,
   nm_contact           varchar(80) not null,
   txt_email            varchar(50),
   username             varchar(32),
   password             varchar(32),
   txt_comment          varchar(255),
   primary key (id_contact)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Table: arpa_country                                          */
/*==============================================================*/
create table arpa_country
(
   id_country           smallint not null auto_increment,
   country_name         varchar(64) not null,
   country_iso_code_2   char(2),
   country_iso_code_3   char(3),
   address_format_id    smallint,
   primary key (id_country)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Table: arpa_ctg_desc                                         */
/*==============================================================*/
create table arpa_ctg_desc
(
   id_category          integer not null,
   cod_lang             char(5) not null,
   name_category        varchar(32),
   desc_category        varchar(255),
   primary key (id_category, cod_lang)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Table: arpa_gallery                                          */
/*==============================================================*/
create table arpa_gallery
(
   id_gal               integer not null auto_increment,
   id_org               integer,
   name_gal             char(50),
   dir_gal              char(50),
   txt_desc             char(255),
   cod_lang             char(5),
   tp_gal               smallint,
   img_front            varchar(50),
   primary key (id_gal)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Index: arpa_gallery_fk1                                      */
/*==============================================================*/
create index arpa_gallery_fk1 on arpa_gallery
(
   id_org
);

/*==============================================================*/
/* Table: arpa_immos                                            */
/*==============================================================*/
create table arpa_immos
(
   id_immo              integer not null auto_increment,
   id_account           integer,
   id_org               integer,
   id_position          integer,
   id_gal               integer,
   ref_immo             varchar(10),
   tp_state             tinyint,
   ind_oferta           tinyint,
   dt_create            date,
   dt_valid             date,
   tp_propiedad         smallint,
   tp_servicio          smallint,
   txt_address1         varchar(80),
   txt_zona             varchar(50),
   txt_cp               varchar(30),
   txt_poblacion        varchar(50),
   txt_provincia        varchar(50),
   ind_piscina          tinyint,
   num_parking          smallint,
   num_wc               smallint,
   num_dormitorios      smallint,
   int_superficie_const integer,
   int_superficie       integer,
   int_terrace          integer,
   precio               decimal(10,2),
   tp_price             tinyint,
   set_intro            set('1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31','32','33','34','35','36','37','38','39','40','41','42','43','44','45','46','47','48','49','50'),
   set_properties       set('1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31','32','33','34','35','36','37','38','39','40','41','42','43','44','45','46','47','48','49','50'),
   set_equip            set('1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31','32','33','34','35','36','37','38','39','40','41','42','43','44','45','46','47','48','49','50'),
   set_activities       set('1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31','32','33','34','35','36','37','38','39','40','41','42','43','44','45','46','47','48','49','50'),
   set_services         set('1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31','32','33','34','35','36','37','38','39','40','41','42','43','44','45','46','47','48','49','50'),
   set_observ           set('1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31','32','33','34','35','36','37','38','39','40','41','42','43','44','45','46','47','48','49','50'),
   id_seasons           integer,
   int_year             smallint,
   int_capacity         smallint,
   txt_distances        varchar(255),
   txt_comment          varchar(255),
   primary key (id_immo)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Index: arpa_immos_fk1                                        */
/*==============================================================*/
create index arpa_immos_fk1 on arpa_immos
(
   id_org
);

/*==============================================================*/
/* Index: arpa_immos_fk2                                        */
/*==============================================================*/
create index arpa_immos_fk2 on arpa_immos
(
   id_position
);

/*==============================================================*/
/* Index: arpa_immos_fk3                                        */
/*==============================================================*/
create index arpa_immos_fk3 on arpa_immos
(
   id_account
);

/*==============================================================*/
/* Index: arpa_immos_fk4                                        */
/*==============================================================*/
create index arpa_immos_fk4 on arpa_immos
(
   id_gal
);

/*==============================================================*/
/* Index: arpa_immos_fk5                                        */
/*==============================================================*/
create index arpa_immos_fk5 on arpa_immos
(
   id_seasons
);

/*==============================================================*/
/* Table: arpa_labels                                           */
/*==============================================================*/
create table arpa_labels
(
   id_name              varchar(40) not null,
   cod_lang             char(5) not null,
   txt_label            varchar(1024) character set utf8 not null,
   primary key (id_name, cod_lang)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Table: arpa_lists                                            */
/*==============================================================*/
create table arpa_lists
(
   id_name              varchar(20) not null,
   txt_code             varchar(10) character set utf8 not null,
   cod_lang             char(5) not null,
   num_order            integer not null,
   txt_value            varchar(255) character set utf8 not null,
   primary key (id_name, txt_code, cod_lang)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Table: arpa_mailing                                          */
/*==============================================================*/
create table arpa_mailing
(
   id_mailing           integer not null auto_increment,
   id_org               integer not null,
   id_news              integer,
   tp_state             tinyint,
   name_mailing         varchar(255),
   dt_create            date,
   dt_sent              date,
   tp_send_to           tinyint,
   tp_send              tinyint,
   txt_subject          varchar(255),
   txt_content          text,
   url_pg               varchar(255),
   txt_idioma           char(2),
   num_sent             int,
   primary key (id_mailing)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Index: arpa_mailing_fk1                                      */
/*==============================================================*/
create index arpa_mailing_fk1 on arpa_mailing
(
   id_org
);

/*==============================================================*/
/* Index: arpa_mailing_fk2                                      */
/*==============================================================*/
create index arpa_mailing_fk2 on arpa_mailing
(
   id_news
);

/*==============================================================*/
/* Table: arpa_news                                             */
/*==============================================================*/
create table arpa_news
(
   id_news              integer not null auto_increment,
   id_org               integer,
   id_position          integer,
   dt_news              datetime not null,
   tp_news              varchar(25) default null,
   txt_title            varchar(100) not null,
   txt_resum            text not null,
   txt_content          text,
   txt_email            varchar(60),
   int_score            smallint default 0,
   txt_url              varchar(100),
   txt_url_title        varchar(50),
   int_hits             char(10),
   txt_idioma           char(2) not null,
   flag_home            char(2) not null,
   txt_imatge           varchar(60),
   cod_posimg           char(1),
   txt_imatge1          varchar(60),
   txt_imatge2          varchar(60),
   txt_imatge3          varchar(60),
   primary key (id_news)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Index: arpa_news_fk1                                         */
/*==============================================================*/
create index arpa_news_fk1 on arpa_news
(
   id_org
);

/*==============================================================*/
/* Index: arpa_news_fk2                                         */
/*==============================================================*/
create index arpa_news_fk2 on arpa_news
(
   id_position
);

/*==============================================================*/
/* Table: arpa_org                                              */
/*==============================================================*/
create table arpa_org
(
   id_org               integer not null auto_increment,
   parent_id_org        integer,
   id_perm              integer,
   root_id_org          integer,
   name_org             varchar(80),
   id_acisa             integer,
   txt_cif              char(9),
   txt_address1         varchar(80),
   txt_provincia        varchar(50),
   txt_poblacion        varchar(50),
   txt_zona             varchar(50),
   txt_cp               varchar(30),
   txt_telf1            varchar(30),
   txt_telf2            varchar(30),
   txt_fax              varchar(30),
   txt_activity         text,
   amount_export        float,
   txt_sector           varchar(40),
   cod_cnae             integer,
   amount_sales         float,
   num_employees        integer,
   amount_capital       float,
   tp_export            varchar(40),
   txt_email1           varchar(50),
   txt_email2           varchar(50),
   txt_web              varchar(50),
   primary key (id_org)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Index: arpa_org_fk1                                          */
/*==============================================================*/
create index arpa_org_fk1 on arpa_org
(
   parent_id_org
);

/*==============================================================*/
/* Table: arpa_perm_scrs                                        */
/*==============================================================*/
create table arpa_perm_scrs
(
   id_perm              integer not null,
   id_screen            integer not null,
   numorder             integer default 0 comment 'Orden de aparici�n',
   primary key (id_perm, id_screen)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Table: arpa_perm_view                                        */
/*==============================================================*/
create table arpa_perm_view
(
   id_perm              integer not null,
   id_view              integer not null,
   tp_perm              char not null,
   primary key (id_perm, id_view)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Table: arpa_perms                                            */
/*==============================================================*/
create table arpa_perms
(
   id_perm              integer not null auto_increment,
   nm_profile           varchar(30) not null,
   txt_obsrv            varchar(255),
   primary key (id_perm)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Table: arpa_pob_acc                                          */
/*==============================================================*/
create table arpa_pob_acc
(
   id_account           integer not null,
   id_poblacion         integer not null,
   primary key (id_account, id_poblacion)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Table: arpa_pob_org                                          */
/*==============================================================*/
create table arpa_pob_org
(
   id_org               integer not null,
   id_poblacion         integer not null,
   primary key (id_org, id_poblacion)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Table: arpa_poblacion                                        */
/*==============================================================*/
create table arpa_poblacion
(
   id_poblacion         integer not null auto_increment,
   id_prov              smallint,
   name_pob             varchar(64) not null,
   ind_active           tinyint,
   primary key (id_poblacion)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Index: arpa_poblacion_fk1                                    */
/*==============================================================*/
create index arpa_poblacion_fk1 on arpa_poblacion
(
   id_prov
);

/*==============================================================*/
/* Table: arpa_point                                            */
/*==============================================================*/
create table arpa_point
(
   id_immo              integer not null,
   txt_x                char(20),
   txt_y                char(20),
   primary key (id_immo)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Index: index_points                                          */
/*==============================================================*/
create index index_points on arpa_point
(
   txt_x,
   txt_y
);

/*==============================================================*/
/* Table: arpa_position                                         */
/*==============================================================*/
create table arpa_position
(
   id_position          integer not null auto_increment,
   id_org               integer not null,
   user_id              varchar(32) not null,
   name_position        varchar(50),
   primary key (id_position)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Index: arpa_position_fk1                                     */
/*==============================================================*/
create index arpa_position_fk1 on arpa_position
(
   id_org
);

/*==============================================================*/
/* Index: arpa_position_fk2                                     */
/*==============================================================*/
create index arpa_position_fk2 on arpa_position
(
   user_id
);

/*==============================================================*/
/* Table: arpa_prefs                                            */
/*==============================================================*/
create table arpa_prefs
(
   id_pref              varchar(20) not null,
   vl_pref              varchar(255),
   onload               tinyint default 0,
   primary key (id_pref)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Table: arpa_price_lst                                        */
/*==============================================================*/
create table arpa_price_lst
(
   id_price_lst         integer not null auto_increment,
   id_org               integer,
   name_price_lst       varchar(50) not null,
   tp_state             tinyint not null,
   dt_create            date,
   dt_start             date,
   dt_end               date,
   primary key (id_price_lst)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Index: arpa_price_lst_fk1                                    */
/*==============================================================*/
create index arpa_price_lst_fk1 on arpa_price_lst
(
   id_org
);

/*==============================================================*/
/* Table: arpa_prod_desc                                        */
/*==============================================================*/
create table arpa_prod_desc
(
   id_product           integer not null,
   cod_lang             char(5) not null,
   name_product         varchar(32),
   desc_product         varchar(255),
   primary key (id_product, cod_lang)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Table: arpa_prod_price                                       */
/*==============================================================*/
create table arpa_prod_price
(
   id_product           integer not null,
   id_price_lst         integer not null,
   precio               decimal(10,2),
   primary key (id_product, id_price_lst)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Table: arpa_products                                         */
/*==============================================================*/
create table arpa_products
(
   id_product           integer not null auto_increment,
   id_org               integer not null,
   id_category          integer,
   id_gal               integer,
   cod_product          varchar(32) not null,
   tp_vat               tinyint not null,
   primary key (id_product)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Index: arpa_products_fk1                                     */
/*==============================================================*/
create index arpa_products_fk1 on arpa_products
(
   id_category
);

/*==============================================================*/
/* Table: arpa_provincia                                        */
/*==============================================================*/
create table arpa_provincia
(
   id_prov              smallint not null auto_increment,
   id_comunidad         smallint,
   prov_name            varchar(64) not null,
   ind_active           tinyint,
   primary key (id_prov)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Index: arpa_provincia_fk1                                    */
/*==============================================================*/
create index arpa_provincia_fk1 on arpa_provincia
(
   id_comunidad
);

/*==============================================================*/
/* Table: arpa_scr_views                                        */
/*==============================================================*/
create table arpa_scr_views
(
   id_screen            integer not null,
   id_view              integer not null,
   numorder             integer comment 'Orden de aparici�n',
   primary key (id_screen, id_view)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Table: arpa_screens                                          */
/*==============================================================*/
create table arpa_screens
(
   id_screen            integer not null auto_increment,
   nm_screen            varchar(30) not null,
   txt_obsrv            varchar(255),
   app_file             varchar(30) not null,
   primary key (id_screen)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Table: arpa_sdays                                            */
/*==============================================================*/
create table arpa_sdays
(
   id_sdays             int not null auto_increment,
   id_seasons           integer,
   tp_sdays             tinyint,
   dt_start             date,
   dt_end               date,
   precio               decimal(10,2),
   primary key (id_sdays)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Index: arpa_sdays_fk1                                        */
/*==============================================================*/
create index arpa_sdays_fk1 on arpa_sdays
(
   id_seasons
);

/*==============================================================*/
/* Table: arpa_seasons                                          */
/*==============================================================*/
create table arpa_seasons
(
   id_seasons           integer not null auto_increment,
   id_org               integer,
   name_seasons         varchar(255),
   tp_price             tinyint,
   primary key (id_seasons)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Table: arpa_stats_access                                     */
/*==============================================================*/
create table arpa_stats_access
(
   id_day_stats         date not null,
   num_visits           integer,
   num_robots           integer,
   num_hits             integer,
   primary key (id_day_stats)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Table: arpa_stats_referers                                   */
/*==============================================================*/
create table arpa_stats_referers
(
   int_month            tinyint not null,
   txt_url              varchar(255) not null,
   total                integer,
   primary key (int_month, txt_url)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Table: arpa_stats_strings                                    */
/*==============================================================*/
create table arpa_stats_strings
(
   int_month            tinyint not null,
   txt_search           varchar(255) not null,
   total                integer,
   primary key (int_month, txt_search)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Table: arpa_ticket                                           */
/*==============================================================*/
create table arpa_ticket
(
   id_ticket            integer not null auto_increment,
   id_tk_ctg            integer,
   id_position          integer,
   id_account           integer,
   id_org               integer,
   ref_ticket           varchar(10) not null,
   dt_create            datetime not null,
   txt_subject          varchar(255) not null,
   nm_account           varchar(255) default null,
   txt_email            varchar(255),
   txt_phone            varchar(20),
   tp_status            tinyint(1) not null,
   tp_priority          tinyint(1) not null,
   txt_ip               varchar(255),
   txt_trans_msg        varchar(255),
   cod_lang             char(5) not null default 'en',
   primary key (id_ticket)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Table: arpa_tk_att                                           */
/*==============================================================*/
create table arpa_tk_att
(
   id_tk_att            integer not null auto_increment,
   id_ticket            integer,
   id_tk_msg            integer,
   filename             varchar(100) not null,
   type                 varchar(15) not null,
   primary key (id_tk_att)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Table: arpa_tk_ctg                                           */
/*==============================================================*/
create table arpa_tk_ctg
(
   id_tk_ctg            integer not null auto_increment,
   nm_tk_ctg            varchar(100) not null,
   pophost              varchar(200) not null,
   popuser              varchar(200) not null,
   poppass              varchar(200) not null,
   txt_email            varchar(200) not null,
   txt_sign             text not null,
   fg_hidden            tinyint(1) not null default 0,
   primary key (id_tk_ctg)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Table: arpa_tk_ctg_org                                       */
/*==============================================================*/
create table arpa_tk_ctg_org
(
   id_tk_ctg            integer not null,
   id_org               integer not null,
   primary key (id_tk_ctg, id_org)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Table: arpa_tk_msg                                           */
/*==============================================================*/
create table arpa_tk_msg
(
   id_tk_msg            integer not null auto_increment,
   id_ticket            integer,
   dt_create            datetime not null,
   user_id              varchar(32),
   id_contact           integer,
   txt_msg              text not null,
   txt_headers          text,
   fg_private           tinyint(1) default 0,
   primary key (id_tk_msg)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Table: arpa_tk_msgtext                                       */
/*==============================================================*/
create table arpa_tk_msgtext
(
   id_msgtext           char(10) not null,
   cod_lang             char(5) not null,
   txt_subject          varchar(255),
   txt_email            text,
   primary key (cod_lang, id_msgtext)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Table: arpa_views                                            */
/*==============================================================*/
create table arpa_views
(
   id_view              integer not null auto_increment,
   nm_view              varchar(30) not null,
   app_file             varchar(30) not null,
   txt_obsrv            varchar(255),
   params               varchar(255) comment 'Parametros pasados a la vista.
            Se escriben como los parametros de una URL.
            Ej: max=1&ver=true',
   primary key (id_view)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Index: i_app_file                                            */
/*==============================================================*/
create index i_app_file on arpa_views
(
   app_file
);

/*==============================================================*/
/* Table: arpa_zona                                             */
/*==============================================================*/
create table arpa_zona
(
   id_zona              integer not null auto_increment,
   id_poblacion         integer,
   name_zona            varchar(64) not null,
   ind_active           tinyint,
   primary key (id_zona)
)
type = innodb
character set = utf8;

/*==============================================================*/
/* Index: arpa_zona_fk1                                         */
/*==============================================================*/
create index arpa_zona_fk1 on arpa_zona
(
   id_poblacion
);

alter table arpa_acc_perfil add constraint fk_ref_5121 foreign key (id_account)
      references arpa_accounts (id_account) on delete restrict on update restrict;

alter table arpa_accounts add constraint fk_ref_1036 foreign key (id_org)
      references arpa_org (id_org) on delete restrict on update restrict;

alter table arpa_accounts add constraint fk_ref_15787 foreign key (id_price_lst)
      references arpa_price_lst (id_price_lst) on delete restrict on update restrict;

alter table arpa_accounts add constraint fk_ref_1908 foreign key (id_position)
      references arpa_position (id_position) on delete restrict on update restrict;

alter table arpa_auth_user add constraint fk_ref_397 foreign key (id_org)
      references arpa_org (id_org) on delete restrict on update restrict;

alter table arpa_bookings add constraint fk_ref_10835 foreign key (id_immo)
      references arpa_immos (id_immo) on delete restrict on update restrict;

alter table arpa_bookings add constraint fk_ref_9122 foreign key (id_account)
      references arpa_accounts (id_account) on delete restrict on update restrict;

alter table arpa_categories add constraint fk_ref_270 foreign key (id_parent_category)
      references arpa_categories (id_category) on delete restrict on update restrict;

alter table arpa_categories add constraint fk_ref_389 foreign key (id_org)
      references arpa_org (id_org) on delete restrict on update restrict;

alter table arpa_comunidad add constraint fk_ref_2615 foreign key (id_country)
      references arpa_country (id_country) on delete restrict on update restrict;

alter table arpa_contacts add constraint fk_ref_57 foreign key (id_account)
      references arpa_accounts (id_account) on delete restrict on update restrict;

alter table arpa_ctg_desc add constraint fk_ref_15001 foreign key (id_category)
      references arpa_categories (id_category) on delete restrict on update restrict;

alter table arpa_gallery add constraint fk_ref_1203 foreign key (id_org)
      references arpa_org (id_org) on delete restrict on update restrict;

alter table arpa_immos add constraint fk_ref_10970 foreign key (id_seasons)
      references arpa_seasons (id_seasons) on delete restrict on update restrict;

alter table arpa_immos add constraint fk_ref_2373 foreign key (id_org)
      references arpa_org (id_org) on delete restrict on update restrict;

alter table arpa_immos add constraint fk_ref_2377 foreign key (id_position)
      references arpa_position (id_position) on delete restrict on update restrict;

alter table arpa_immos add constraint fk_ref_3621 foreign key (id_account)
      references arpa_accounts (id_account) on delete restrict on update restrict;

alter table arpa_immos add constraint fk_ref_4317 foreign key (id_gal)
      references arpa_gallery (id_gal) on delete restrict on update restrict;

alter table arpa_mailing add constraint fk_ref_8250 foreign key (id_org)
      references arpa_org (id_org) on delete restrict on update restrict;

alter table arpa_mailing add constraint fk_ref_8661 foreign key (id_news)
      references arpa_news (id_news) on delete restrict on update restrict;

alter table arpa_news add constraint fk_ref_2025 foreign key (id_position)
      references arpa_position (id_position) on delete restrict on update restrict;

alter table arpa_news add constraint fk_ref_393 foreign key (id_org)
      references arpa_org (id_org) on delete restrict on update restrict;

alter table arpa_org add constraint fk_ref_1040 foreign key (parent_id_org)
      references arpa_org (id_org) on delete restrict on update restrict;

alter table arpa_org add constraint fk_ref_63 foreign key (id_perm)
      references arpa_perms (id_perm) on delete restrict on update restrict;

alter table arpa_perm_scrs add constraint fk_ref_27 foreign key (id_perm)
      references arpa_perms (id_perm) on delete restrict on update restrict;

alter table arpa_perm_scrs add constraint fk_ref_28 foreign key (id_screen)
      references arpa_screens (id_screen) on delete restrict on update restrict;

alter table arpa_perm_view add constraint fk_ref_29 foreign key (id_perm)
      references arpa_perms (id_perm) on delete restrict on update restrict;

alter table arpa_perm_view add constraint fk_ref_30 foreign key (id_view)
      references arpa_views (id_view) on delete restrict on update restrict;

alter table arpa_pob_acc add constraint fk_ref_4633 foreign key (id_account)
      references arpa_accounts (id_account) on delete restrict on update restrict;

alter table arpa_pob_acc add constraint fk_ref_4637 foreign key (id_poblacion)
      references arpa_poblacion (id_poblacion) on delete restrict on update restrict;

alter table arpa_pob_org add constraint fk_ref_3200 foreign key (id_org)
      references arpa_org (id_org) on delete restrict on update restrict;

alter table arpa_pob_org add constraint fk_ref_3208 foreign key (id_poblacion)
      references arpa_poblacion (id_poblacion) on delete restrict on update restrict;

alter table arpa_poblacion add constraint fk_ref_2629 foreign key (id_prov)
      references arpa_provincia (id_prov) on delete restrict on update restrict;

alter table arpa_point add constraint fk_reference_59 foreign key (id_immo)
      references arpa_immos (id_immo) on delete restrict on update restrict;

alter table arpa_position add constraint fk_ref_1469 foreign key (id_org)
      references arpa_org (id_org) on delete restrict on update restrict;

alter table arpa_position add constraint fk_ref_1473 foreign key (user_id)
      references arpa_auth_user (user_id) on delete restrict on update restrict;

alter table arpa_price_lst add constraint fk_ref_15779 foreign key (id_org)
      references arpa_org (id_org) on delete restrict on update restrict;

alter table arpa_prod_desc add constraint fk_ref_14988 foreign key (id_product)
      references arpa_products (id_product) on delete restrict on update restrict;

alter table arpa_prod_price add constraint fk_ref_15763 foreign key (id_product)
      references arpa_products (id_product) on delete restrict on update restrict;

alter table arpa_prod_price add constraint fk_ref_15771 foreign key (id_price_lst)
      references arpa_price_lst (id_price_lst) on delete restrict on update restrict;

alter table arpa_products add constraint fk_ref_16624 foreign key (id_gal)
      references arpa_gallery (id_gal) on delete restrict on update restrict;

alter table arpa_products add constraint fk_ref_266 foreign key (id_category)
      references arpa_categories (id_category) on delete restrict on update restrict;

alter table arpa_products add constraint fk_ref_381 foreign key (id_org)
      references arpa_org (id_org) on delete restrict on update restrict;

alter table arpa_provincia add constraint fk_ref_2622 foreign key (id_comunidad)
      references arpa_comunidad (id_comunidad) on delete restrict on update restrict;

alter table arpa_scr_views add constraint fk_ref_25 foreign key (id_screen)
      references arpa_screens (id_screen) on delete restrict on update restrict;

alter table arpa_scr_views add constraint fk_ref_26 foreign key (id_view)
      references arpa_views (id_view) on delete restrict on update restrict;

alter table arpa_sdays add constraint fk_ref_9092 foreign key (id_seasons)
      references arpa_seasons (id_seasons) on delete restrict on update restrict;

alter table arpa_ticket add constraint fk_ref_46 foreign key (id_tk_ctg)
      references arpa_tk_ctg (id_tk_ctg) on delete restrict on update restrict;

alter table arpa_ticket add constraint fk_ref_50 foreign key (id_position)
      references arpa_position (id_position) on delete restrict on update restrict;

alter table arpa_ticket add constraint fk_ref_51 foreign key (id_account)
      references arpa_accounts (id_account) on delete restrict on update restrict;

alter table arpa_ticket add constraint fk_ref_60 foreign key (id_org)
      references arpa_org (id_org) on delete restrict on update restrict;

alter table arpa_tk_att add constraint fk_ref_41 foreign key (id_ticket)
      references arpa_ticket (id_ticket) on delete restrict on update restrict;

alter table arpa_tk_att add constraint fk_ref_48 foreign key (id_tk_msg)
      references arpa_tk_msg (id_tk_msg) on delete restrict on update restrict;

alter table arpa_tk_ctg_org add constraint fk_ref_61 foreign key (id_tk_ctg)
      references arpa_tk_ctg (id_tk_ctg) on delete restrict on update restrict;

alter table arpa_tk_ctg_org add constraint fk_ref_62 foreign key (id_org)
      references arpa_org (id_org) on delete restrict on update restrict;

alter table arpa_tk_msg add constraint fk_ref_42 foreign key (id_ticket)
      references arpa_ticket (id_ticket) on delete restrict on update restrict;

alter table arpa_tk_msg add constraint fk_ref_56 foreign key (user_id)
      references arpa_auth_user (user_id) on delete restrict on update restrict;

alter table arpa_tk_msg add constraint fk_ref_59 foreign key (id_contact)
      references arpa_contacts (id_contact) on delete restrict on update restrict;

alter table arpa_zona add constraint fk_ref_3212 foreign key (id_poblacion)
      references arpa_poblacion (id_poblacion) on delete restrict on update restrict;

