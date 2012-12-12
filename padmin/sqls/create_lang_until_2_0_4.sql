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
/**
 * Creates new language
 * Must replace table prefix and xx by new language code.
 * Version : Until 2.0.4  
 */
insert into prefix_labels (id_name, cod_lang, txt_label)
     select id_name, 'xx', txt_label from prefix_labels where cod_lang = 'en';

update prefix_labels set txt_label = 'xx' where id_name = '_IDIOMA' and cod_lang = 'xx';

insert into prefix_lists (id_name, txt_code, cod_lang, num_order, txt_value)
     select id_name, txt_code, 'xx', num_order, txt_value from prefix_lists where cod_lang = 'en';

/**
 * Following is optional. Only if we want to identify new labels and lists
 * 
 */
update prefix_labels set txt_label = concat('##',txt_label) 
where (id_name <> '_CHARSET' and id_name <> '_DATETIME_SQL' and id_name <> '_DATE_SQL' and id_name <> '_IDIOMA' and id_name <> '_DATE_FORMAT')
and cod_lang = 'xx';
update prefix_lists set txt_value = concat('##',txt_value) 
where cod_lang = 'xx';


