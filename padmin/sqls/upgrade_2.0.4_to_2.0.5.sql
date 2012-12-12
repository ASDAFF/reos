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
/* Created on:     01/03/2010 18:42:23                          */
/*==============================================================*/


drop index arpa_username on arpa_accounts;

alter table arpa_accounts
   modify column txt_cp varchar(30);

alter table arpa_accounts
   modify column txt_telf1 varchar(30);

alter table arpa_accounts
   modify column txt_telf2 varchar(30);

alter table arpa_accounts
   modify column txt_fax varchar(30);

alter table arpa_accounts
   modify column username varchar(60) not null;

alter table arpa_accounts
   modify column cod_lang char(5);

/*==============================================================*/
/* Index: arpa_username                                         */
/*==============================================================*/
create unique index arpa_username on arpa_accounts
(
   username
);

drop table if exists tmp_arpa_ctg_desc;

rename table arpa_ctg_desc to tmp_arpa_ctg_desc;

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

insert into arpa_ctg_desc (id_category, cod_lang, name_category, desc_category)
select id_category, cod_lang, name_category, desc_category
from tmp_arpa_ctg_desc;

alter table arpa_gallery
   modify column cod_lang char(5);

alter table arpa_immos
   modify column txt_cp varchar(30);

alter table arpa_immos
   modify column int_superficie_const integer;

alter table arpa_immos
   modify column int_superficie integer;

alter table arpa_immos
   modify column int_terrace integer;

alter table arpa_org
   modify column txt_cp varchar(30);

alter table arpa_org
   modify column txt_telf1 varchar(30);

alter table arpa_org
   modify column txt_telf2 varchar(30);

alter table arpa_org
   modify column txt_fax varchar(30);

drop table if exists tmp_arpa_prod_desc;

rename table arpa_prod_desc to tmp_arpa_prod_desc;

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

insert into arpa_prod_desc (id_product, cod_lang, name_product, desc_product)
select id_product, cod_lang, name_product, desc_product
from tmp_arpa_prod_desc;

alter table arpa_ticket
   modify column cod_lang char(5) not null default 'en';

drop table if exists tmp_arpa_tk_msgtext;

rename table arpa_tk_msgtext to tmp_arpa_tk_msgtext;

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

insert into arpa_tk_msgtext (id_msgtext, cod_lang, txt_subject, txt_email)
select id_msgtext, cod_lang, txt_subject, txt_email
from tmp_arpa_tk_msgtext;

alter table arpa_ctg_desc add constraint fk_ref_15001 foreign key (id_category)
      references arpa_categories (id_category) on delete restrict on update restrict;

alter table arpa_prod_desc add constraint fk_ref_14988 foreign key (id_product)
      references arpa_products (id_product) on delete restrict on update restrict;

