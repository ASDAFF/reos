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
/* Database name:  ReOS 2.0.1                                   */
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     02/07/2009 12:03:29                          */
/*==============================================================*/

drop table if exists tmp_arpa_bookings;

rename table arpa_bookings to tmp_arpa_bookings;

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
character set = utf8;

#warning: the following insert order will fail because it cannot give value to mandatory columns
insert into arpa_bookings (id_bookings, dt_create, id_immo, id_account, tp_state, dt_start, dt_end)
select id_bookings, ?, id_immo, id_account, ?, dt_start, dt_end
from tmp_arpa_bookings;

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

alter table arpa_immos
   add tp_currency varchar(3);

alter table arpa_sdays
   add tp_currency varchar(3);

alter table arpa_bookings add constraint fk_ref_10835 foreign key (id_immo)
      references arpa_immos (id_immo) on delete restrict on update restrict;

alter table arpa_bookings add constraint fk_ref_9122 foreign key (id_account)
      references arpa_accounts (id_account) on delete restrict on update restrict;

