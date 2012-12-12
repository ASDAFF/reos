<?php
/**
 * ReOS is a vertical software for real estates.
 * Copyright 2010 IT ELAZOS S.L.
 *
 * This file is part of ReOS v2.x.x.
 *
 * ReOS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * ReOS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with ReOS.  If not, see <http://www.gnu.org/licenses/>.
 **/

/**
 *Class Catalog definition file.
 */
$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/class_catalog.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}
/**
 * Class Gallery.
 * Needs class gallery in delete product to delete product gallery too.
 **/
require_once(_DirINCLUDES."class_gallery.php");

/**
 *Handles product catalog using table prefix_products. Defines product categories tree and product information.
 *@author Josep Marxuach
 *@version 1.0
 *@copyright 2004 by IT eLazos SL
 *@package Catalog
 */
class catalog extends utils {
	var $path_images="imgprods/";
	var $image_size=200; //pixels
	var $txt_error;
	var $cod_lang;

	function catalog(){
		require_once(_DirINCLUDES."class_lovs.php");
		$lovs= new lovs;
		$lovs->getLovs('_LST_IND_ACTIVE',_IDIOMA);
		$lovs->getLovs('_LST_TP_STE_PRC',_IDIOMA);
	}

	/**
	 * Description not available
	 **/
	function products_home( $id_cat=NULL, $price=NULL, $uid=NULL ){

		$join = "".$this->prefix."_products p LEFT JOIN ".$this->prefix."_prod_desc AS d ON p.id_product = d.id_product AND d.cod_lang = '".$this->cod_lang."' LEFT JOIN ".$this->prefix."_ctg_desc AS c ON p.id_category = c.id_category AND c.cod_lang = '".$this->cod_lang."' LEFT JOIN ".$this->prefix."_gallery AS g ON p.id_gal = g.id_gal AND g.cod_lang = '".$this->cod_lang."'";

		if( $id_cat ) $where = "WHERE c.id_category=$id_cat";
		else $where = "";

		if( $price ) {
			$sel = ", precio";
			$join .= " JOIN ".$this->prefix."_prod_price AS pr ON p.id_product = pr.id_product";

			if( $uid ) $join .= " JOIN ".$this->prefix."_accounts AS a ON pr.id_price_lst = a.id_price_lst AND a.id_account = $uid";
		}

		if ($this->query("select name_category, cod_product, name_product, dir_gal, img_front $sel FROM $join $where;"))
		{ if ($this->num_rows()==0) { $this->txt_error=""._NO_PROD.""; return false;} else return true;} else { $this->txt_error=""._ER_SELET_TBL." PROD"; return false;}
	}
	/**
	 * Description not available
	 **/
	function add_product($id_org, $fields){

		if (!is_array($fields)) return false;
		if (!$this->prepare_fields($fields)) return false; // fa les comprobacions

		reset($fields);
		$st_field="";
		$st_value="";
		while (list($key,$value)=each($fields))
		{
			if ($key!="desc_product" && $key!="name_product") {
				if ($st_field!="") {$st_field=$st_field.", "; $st_value=$st_value.", ";}
				$st_field=$st_field.$key;
				$st_value=$st_value.$value;
			}
		}

		if ($this->query("select id_category from ".$this->prefix."_categories where id_category=".$fields["id_category"]." and id_org=$id_org;"))
		{
			if ($this->num_rows()!=0)
			{
				if ($this->query("insert into ".$this->prefix."_products (id_org, $st_field) VALUES($id_org, $st_value);"))
				{
					$this->query("insert into ".$this->prefix."_prod_desc (id_product, cod_lang, name_product, desc_product) VALUES(".$this->last_insert_id().",'".$this->cod_lang."',".$fields["name_product"].",".$fields["desc_product"].");");
					return true;
				} else { $this->txt_error=""._ERROR_INSERT.""; return false;}
			} else { $this->txt_error=""._CTG_NO_EXIST.""; return false;}
		} else { $this->txt_error=""._ER_SELET_TBL." CTG"; return false;}
	}
	/**
	 * Description not available
	 **/
	function delete_product($id){

		if ($this->query("select id_gal from ".$this->prefix."_products where id_product=$id;")){
			$num_rows=$this->num_rows();
			if ($num_rows==1) {
				$this->next_record();
				if (isset($this->Record["id_gal"]) && $this->Record["id_gal"]!="") {
					$dbi= new gallery;
					$dbi->delete_gallery($this->Record["id_gal"]);
				}
			}
		}


		if ($this->query("delete from ".$this->prefix."_products where id_product=$id;")
		&& $this->query("delete from ".$this->prefix."_prod_desc where id_product=$id;")
		&& $this->query("delete from ".$this->prefix."_prod_price where id_product=$id;")) {$this->txt_error=""._DELETED.""; return true;} else { $this->txt_error=""._ERROR_DELETE.""; return false;}
	}
	/**
	 * Description not available
	 **/
	function edit_product($id,$fields){

		if (!is_array($fields)) return false;
		if (!$this->prepare_fields($fields)) return false; // fa les comprobacions

		reset($fields);
		$st_field="";
		$flag_desc=false;
		while (list($key,$value)=each($fields))
		{
			if ($key!="name_product" && $key!="desc_product" ) {
				if ($st_field!="") $st_field=$st_field.", ";
				$st_field=$st_field.$key."=".$value;
			} else $flag_desc=true;
		}

		if ($this->query("update ".$this->prefix."_products set $st_field where id_product=$id;"))
		{
			if ($flag_desc) {
				$this->query("select id_product from ".$this->prefix."_prod_desc where id_product=$id and cod_lang='".$this->cod_lang."'");
				if ($this->num_rows()==0)
				$this->query("insert into ".$this->prefix."_prod_desc (id_product, cod_lang, name_product, desc_product) values ($id,'".$this->cod_lang."',".$fields["name_product"].",".$fields["desc_product"].");");
				else
				$this->query("update ".$this->prefix."_prod_desc set name_product=".$fields["name_product"].", desc_product=".$fields["desc_product"]." where id_product=$id and cod_lang='".$this->cod_lang."';");
			}
			$this->txt_error=""._UPDATED."";return true;
		} else { $this->txt_error=""._ERROR_UPDATE.""; return false;}
	}
	/**
	 * Delete price list
	 **/
	function delete_pricelst($id_price_lst){
		if ($this->query("delete from ".$this->prefix."_price_lst where id_price_lst=$id_price_lst;")
		&& $this->query("delete from ".$this->prefix."_prod_price where id_price_lst=$id_price_lst;")) {$this->txt_error=""._DELETED.""; return true;} else { $this->txt_error=""._ERROR_DELETE.""; return false;}
	}
	/**
	 * Delete product price list of price list
	 **/
	function delete_priceprd($id_price_lst,$id_product){
		if ($this->query("delete from ".$this->prefix."_prod_price where id_price_lst=$id_price_lst and id_product=$id_product;"))
		{$this->txt_error=""._DELETED.""; return true;}
		else { $this->txt_error=""._ERROR_DELETE.""; return false;}
	}
	/**
	 * Update Price list
	 **/
	function update_price_lst($id,$fields){

		if (!is_array($fields)) return false;
		if (!$this->prepare_fields($fields)) return false; // fa les comprobacions

		reset($fields);
		$st_field="";
		while (list($key,$value)=each($fields))
		{
			if ($st_field!="") $st_field=$st_field.", ";
			$st_field=$st_field.$key."=".$value;
		}

		if ($this->query("update ".$this->prefix."_price_lst set $st_field where id_price_lst=$id;"))
		{ $this->txt_error=""._UPDATED."";return true; }
		else { $this->txt_error=""._ERROR_UPDATE.""; return false;}
	}
	/**
	 * Description not available
	 **/
	function product($id){

		$join1="".$this->prefix."_products LEFT JOIN ".$this->prefix."_prod_desc ON ".$this->prefix."_products.id_product = ".$this->prefix."_prod_desc.id_product AND ".$this->prefix."_prod_desc.cod_lang = '".$this->cod_lang."'";
		$join2=" LEFT JOIN ".$this->prefix."_gallery ON ".$this->prefix."_products.id_gal = ".$this->prefix."_gallery.id_gal";

		if ($this->query("select ".$this->prefix."_products.id_product, id_category, cod_product, name_product, desc_product,"
		.$this->prefix."_products.id_gal, tp_vat, img_front, dir_gal from $join1 $join2 where "
		.$this->prefix."_products.id_product=$id;"))
		{ $this->next_record(); return $this->Record;}
		else { $this->txt_error=""._ER_SELET_TBL." PROD"; return false;}
	}
	/**
	 * Description not available
	 **/
	function add_category($id_org, $fields){

		if (!is_array($fields)) return false;
		if (!$this->prepare_fields($fields)) return false; // fa les comprobacions

		reset($fields);
		$st_field="";
		$st_value="";
		while (list($key,$value)=each($fields))
		{
			if ($key!="name_category" && $key!="desc_category") {
				if ($st_field!="") {$st_field=$st_field.", "; $st_value=$st_value.", ";}
				$st_field=$st_field.$key;
				$st_value=$st_value.$value;
			}
		}

		if ($this->query("insert into ".$this->prefix."_categories (id_org, $st_field) VALUES($id_org, $st_value);"))
		{
			$this->query("insert into ".$this->prefix."_ctg_desc (id_category, cod_lang, name_category, desc_category) VALUES(".$this->last_insert_id().",'".$this->cod_lang."',".$fields["name_category"].",".$fields["desc_category"].");");
			return true;
		} else { $this->txt_error=""._ERROR_INSERT.""; return false;}

	}
	/**
	 * Description not available
	 **/
	function delete_category($id){
		if ($this->query("select id_category from ".$this->prefix."_products where id_category=$id;"))
		{
			if ($this->num_rows()==0)
			{
				if ($this->query("select id_category from ".$this->prefix."_categories where id_parent_category=$id;"))
				{
					if ($this->num_rows()==0)
					{
						if ($this->query("delete from ".$this->prefix."_categories where id_category=$id;")
						&& $this->query("delete from ".$this->prefix."_ctg_desc where id_category=$id;"))
						return true; else return false;
					} else { $this->txt_error=""._PARENTCTG_DCHILDREN.""; return false;}
				} else { $this->txt_error=""._ERROR_DELETE.""; return false;}

			} else { $this->txt_error=""._CTG_HAS_PROD.""; return false;}
		} else { $this->txt_error=""._ERROR_DELETE.""; return false;}
	}
	/**
	 * Description not available
	 **/
	function category($id){
		$join="".$this->prefix."_categories LEFT JOIN ".$this->prefix."_ctg_desc ON ".$this->prefix."_categories.id_category = ".$this->prefix."_ctg_desc.id_category AND ".$this->prefix."_ctg_desc.cod_lang = '".$this->cod_lang."'";

		if ($this->query("select name_category, id_parent_category, cod_category, desc_category from $join where ".$this->prefix."_categories.id_category=$id;"))
		{ $this->next_record(); return $this->Record;}
		else { $this->txt_error=""._ER_SELET_TBL." CTG"; return false;}
	}
	/**
	 * Devuelve el nombre de la categoria.
	 */
	function name_category($id){
		$join="".$this->prefix."_categories LEFT JOIN ".$this->prefix."_ctg_desc ON ".$this->prefix."_categories.id_category = ".$this->prefix."_ctg_desc.id_category AND ".$this->prefix."_ctg_desc.cod_lang = '".$this->cod_lang."'";

		if ($this->query("select name_category from $join where ".$this->prefix."_categories.id_category=$id;"))
		{ $this->next_record(); return $this->Record[0];}
		else { $this->txt_error=""._ER_SELET_TBL." CTG"; return false;}
	}
	/**
	 * Devuelve la lista de productos de una categoria.
	 */
	function products_cat($id_cat,$from,$offset, $SelectFields = null){

		if ($SelectFields!=null) {
			$fields = "cod_product, name_product, ELT(isnull(id_gal)+1,"._LST_IND_ACTIVE.") as img";
			$join2= "";
		}
		else {
			$fields = "cod_product, name_product, CONCAT(dir_gal,'/thumbnails/',img_front) img_front ";
			$join2=" LEFT JOIN ".$this->prefix."_gallery ON ".$this->prefix."_products.id_gal = ".$this->prefix."_gallery.id_gal";
		}

		if (isset($from) && isset($offset)) $limit=" limit $from, $offset"; else $limit="";

		$join="".$this->prefix."_products LEFT JOIN ".$this->prefix."_prod_desc ON ".$this->prefix."_products.id_product = ".$this->prefix."_prod_desc.id_product AND ".$this->prefix."_prod_desc.cod_lang = '".$this->cod_lang."'";


		if ($this->query("select SQL_CALC_FOUND_ROWS ".$this->prefix."_products.id_product, $fields from $join $join2 where ".$this->prefix."_products.id_category=$id_cat $limit;"))
		{ if ($this->num_rows()==0) { $this->txt_error=""._NO_PROD.""; return false;} else return true;} else { $this->txt_error=""._ER_SELET_TBL." PROD"; return false;}

	}
	/**
	 * Devuelve un array con todos los padres de la categoria.
	 * Array[][0] = Código
	 * Array[][1] = Nombre categoria
	 * Ordenado en orden ascendente por el arbol de categorias.
	 */
	function ParentCat($id_cat,&$result){
		if (!isset($id_cat)||$id_cat==null) return false;
		if ($this->query("select ctg1.id_parent_category, dsc.name_category "
		."from ".$this->prefix."_categories ctg1, ".$this->prefix."_ctg_desc dsc "
		."where ctg1.id_category=$id_cat and dsc.id_category = ctg1.id_parent_category and dsc.cod_lang='$this->cod_lang';")){
			if ($this->num_rows()==0) {
				$this->txt_error=""._NO_CTG."";
				return false;
			} else {
				$this->next_record();
				$result[][0]=$this->Record[0];
				$result[sizeof($result) - 1][1]=$this->Record[1];
				if ($this->ParentCat($this->Record[0],$result)) return true;
				else return false;
			}
		} else {
			$this->txt_error=""._ER_SELET_TBL." CTG";
			return false;
		}
	}
	/**
	 * Not avalaible
	 */
	function categories($sub_id = NULL, $id_org=NULL, $id=NULL){

		$join="".$this->prefix."_categories LEFT JOIN ".$this->prefix."_ctg_desc ON ".$this->prefix."_categories.id_category = ".$this->prefix."_ctg_desc.id_category AND ".$this->prefix."_ctg_desc.cod_lang = '".$this->cod_lang."'";

		if ($sub_id) $where="where id_parent_category=".$sub_id.""; else $where="where id_parent_category is NULL and id_org=$id_org";
		if ($sub_id=="ALL") $where="where id_org=$id_org";
		if ($id) $where=$where." and ".$this->prefix."_categories.id_category != $id";

		if ($this->query("select ".$this->prefix."_categories.id_category, name_category from $join $where;"))
		{ if ($this->num_rows()==0) { return false;} else return true;} else { $this->txt_error=""._ER_SELET_TBL." CTG"; return false;}
	}
	/**
	 * Add price list
	 **/
	function add_price_lst($id_org, $fields){

		if (!is_array($fields)) return false;
		if (!$this->prepare_fields($fields)) return false; // fa les comprobacions

		reset($fields);
		$st_field="";
		$st_value="";
		while (list($key,$value)=each($fields))
		{
			if ($st_field!="") {$st_field=$st_field.", "; $st_value=$st_value.", ";}
			$st_field=$st_field.$key;
			$st_value=$st_value.$value;
		}

		if ($this->query("insert into ".$this->prefix."_price_lst (id_org, $st_field) VALUES($id_org, $st_value);"))
		{ return true;} else { $this->txt_error=""._ERROR_INSERT.""; return false;}

	}
	/**
	 * Description not available
	 **/
	function update_category($id, $fields){

		if (!is_array($fields)) return false;
		if (!$this->prepare_fields($fields)) return false; // fa les comprobacions

		reset($fields);
		$st_field="";
		while (list($key,$value)=each($fields))
		{
			if ($key!="name_category" && $key!="desc_category") {
				if ($st_field!="") $st_field=$st_field.", ";
				$st_field=$st_field.$key."=".$value;
			}
		}

		if ($this->query("update ".$this->prefix."_categories set $st_field where id_category=$id;"))
		{
			$this->query("select id_category from ".$this->prefix."_ctg_desc where id_category=$id and cod_lang='".$this->cod_lang."'");
			if ($this->num_rows()==0)
			$this->query("insert into ".$this->prefix."_ctg_desc (id_category, cod_lang, name_category, desc_category) values ($id,'".$this->cod_lang."',".$fields["name_category"].",".$fields["desc_category"].");");
			else
			$this->query("update ".$this->prefix."_ctg_desc set name_category=".$fields["name_category"].", desc_category=".$fields["desc_category"]." where id_category=$id and cod_lang='".$this->cod_lang."';");
			$this->txt_error=""._UPDATED."";
			return true;
		} else { $this->txt_error=""._ERROR_UPDATE.""; return false;}
	}
	/**
	 * Crea una array del arbol de categorias categorias
	 * Se pasa por parametro el array, el id de la categoria raiz, i  la org
	 * Si queremos todo el arbol id=NULL
	 * Es recursiva
	 **/
	function ctg_array($id, $id_org,&$result) {
		$consulta=null;
		$ind=0;

		$this->categories($id, $id_org);
		$z=0;
		while ($this->next_record()){
			list($id_category, $name)=($this->Record);
			$consulta[$z][0]=$id_category;
			$consulta[$z][1]=$name;
			$z++;
		}

		$z=0;
		$maxim=count($consulta);
		if ($id==NULL) $id=0;

		while ($z<$maxim){
			$result[$id][$z]["id"]=$consulta[$z][0];
			$id_category=$consulta[$z][0];
			$result[$id][$z]["value"]=$consulta[$z][1];
			$this->ctg_array($id_category,$id_org,$result);
			$z++;
		}
	}
	/**
	 * Description not available
	 **/
	function price_lst_dtl($id){

		$join=$this->prefix."_price_lst";

		if ($this->query("select name_price_lst, tp_state, "
		."DATE_FORMAT(".$this->prefix."_price_lst.dt_create,"._DATE_SQL.") as dt_create, "
		."DATE_FORMAT(".$this->prefix."_price_lst.dt_start,"._DATE_SQL.") as dt_start, "
		."DATE_FORMAT(".$this->prefix."_price_lst.dt_end,"._DATE_SQL.") as dt_end "
		."from $join where ".$this->prefix."_price_lst.id_price_lst=$id;"))
		{ $this->next_record(); return $this->Record;}
		else { $this->txt_error=""._ER_SELET_TBL." PRICE_LST"; return false;}
	}
	/**
	 * Description not available
	 **/
	function price_prd_dtl($id_price_lst, $id_product){

		$join1=$this->prefix."_prod_price LEFT JOIN ".$this->prefix."_prod_desc on ".$this->prefix."_prod_price.id_product=".$this->prefix."_prod_desc.id_product";

		$join2="LEFT JOIN ".$this->prefix."_products on ".$this->prefix."_prod_price.id_product=".$this->prefix."_products.id_product";

		if ($this->query("select cod_product, name_product, precio "
		//."DATE_FORMAT(".$this->prefix."_price_lst.dt_create,"._DATE_SQL.") as dt_create, "
		//."DATE_FORMAT(".$this->prefix."_price_lst.dt_start,"._DATE_SQL.") as dt_start, "
		//."DATE_FORMAT(".$this->prefix."_price_lst.dt_end,"._DATE_SQL.") as dt_end "
		."from $join1 $join2 where ".$this->prefix."_prod_price.id_price_lst=$id_price_lst AND ".$this->prefix."_prod_price.id_product=$id_product;"))
		{ $this->next_record(); return $this->Record;}
		else { $this->txt_error=""._ER_SELET_TBL." PROD_PRICE"; return false;}
	}
	/**
	 * Returns a price list.
	 **/
	function price_list($fields, $id_org,$from,$offset,$order_by){

		if (isset($from) && isset($offset)) $limit=" limit $from, $offset"; else $limit="";
		if ($order_by) $order_by="ORDER BY ".$order_by; else $order_by="";
		$where=" where ".$this->prefix."_price_lst.id_org=$id_org ";

		if (is_array($fields)) {
			if (!$this->prepare_fields($fields)) return false; // fa les comprobacions
			reset($fields);
			$st_field="";
			while (list($key,$value)=each($fields)){
				$operator="=";
				if ($key=="name_pricelst_search") {$key="name_price_lst";$value="'%".addslashes($value)."%'";$operator=" like ";}
				$st_field=$st_field." AND ".$key.$operator.$value;
			}
			if ($st_field!="") $where.=$st_field;
		}

		if ($this->query("select SQL_CALC_FOUND_ROWS ".$this->prefix."_price_lst.id_price_lst, name_price_lst, "
		."ELT(tp_state,"._LST_TP_STE_PRC.") AS tp_state, "
		."DATE_FORMAT(".$this->prefix."_price_lst.dt_start,"._DATE_SQL.") as dt_start, "
		."DATE_FORMAT(".$this->prefix."_price_lst.dt_end,"._DATE_SQL.") as dt_end from "
		.$this->prefix."_price_lst"
		." $where $order_by $limit;"))
		{if ($this->num_rows()==0) {$this->txt_error=""._NO_PRICE_LST."";return false;} else return true;}
		else { $this->txt_error=""._ER_SELET_TBL." PRICE_LST"; return false;}
	}
	/**
	 * Returns a product of a price list.
	 **/
	function pricelst_prods($fields, $id_price_lst,$from,$offset,$order_by){

		$join1="".$this->prefix."_prod_price LEFT JOIN ".$this->prefix."_prod_desc ON ".$this->prefix."_prod_price.id_product = ".$this->prefix."_prod_desc.id_product AND ".$this->prefix."_prod_desc.cod_lang = '".$this->cod_lang."'";

		$join2="LEFT JOIN ".$this->prefix."_products ON ".$this->prefix."_prod_price.id_product = ".$this->prefix."_products.id_product";

		if (isset($from) && isset($offset)) $limit=" limit $from, $offset"; else $limit="";
		if ($order_by) $order_by="ORDER BY ".$order_by; else $order_by="";
		$where=" where ".$this->prefix."_prod_price.id_price_lst=$id_price_lst ";

		if (is_array($fields)) {
			if (!$this->prepare_fields($fields)) return false; // fa les comprobacions
			reset($fields);
			$st_field="";
			while (list($key,$value)=each($fields)){
				$operator="=";
				if ($key=="name_prod_search") {$key="name_product";$value="'%".addslashes($value)."%'";$operator=" like ";}
				$st_field=$st_field." AND ".$key.$operator.$value;
			}
			if ($st_field!="") $where.=$st_field;
		}

		if ($this->query("select SQL_CALC_FOUND_ROWS ".$this->prefix."_prod_price.id_product, cod_product, name_product, precio "
		//."ELT(tp_state,"._LST_TP_STE_PRC.") AS tp_state, "
		//."DATE_FORMAT(".$this->prefix."_price_lst.dt_start,"._DATE_SQL.") as dt_start, "
		//."DATE_FORMAT(".$this->prefix."_price_lst.dt_end,"._DATE_SQL.") as dt_end from "
		." from $join1 $join2"
		." $where $order_by $limit;"))
		{if ($this->num_rows()==0) {$this->txt_error=""._NO_PROD."";return false;} else return true;}
		else { $this->txt_error=""._ER_SELET_TBL." PROD_PRICE"; return false;}
	}
	/**
	 * Update Product Price in a Price List
	 **/
	function update_price_prd($id_price_lst, $id_product,$fields){

		if (!is_array($fields)) return false;
		if (!$this->prepare_fields($fields)) return false; // fa les comprobacions

		reset($fields);
		$st_field="";
		while (list($key,$value)=each($fields))
		{
			if ($st_field!="") $st_field=$st_field.", ";
			$st_field=$st_field.$key."=".$value;
		}

		if ($this->query("update ".$this->prefix."_prod_price set $st_field where id_price_lst=$id_price_lst AND id_product=$id_product;"))
		{ $this->txt_error=""._UPDATED."";return true; }
		else { $this->txt_error=""._ERROR_UPDATE.""; return false;}
	}
	/**
	 * Add product price to price list
	 **/
	function add_price_prd($id_price_lst, $cod_product, $id_org,$fields){

		if ($this->query("select id_product from ".$this->prefix."_products where id_org=$id_org and cod_product=$cod_product;")
		&& ($this->num_rows()>0))
		{
			$this->next_record();
			$id_product=$this->Record["id_product"];

			if (!is_array($fields)) return false;
			if (!$this->prepare_fields($fields)) return false; // fa les comprobacions

			reset($fields);
			$st_field="";
			$st_value="";
			while (list($key,$value)=each($fields))
			{
				if ($st_field!="") {$st_field=$st_field.", "; $st_value=$st_value.", ";}
				$st_field=$st_field.$key;
				$st_value=$st_value.$value;
			}
			if ($this->query("insert into ".$this->prefix."_prod_price (id_price_lst, id_product, $st_field) VALUES($id_price_lst, $id_product, $st_value);"))
			{ return true;} else { $this->txt_error=""._ERROR_INSERT.""; return false;}

		} else { $this->txt_error=""._ERROR_INSERT.""; return false;}
	}
	/**
	 * Description not available
	 **/
	function prepare_fields(&$fields){
		reset($fields);
		while (list($key,$value)=each($fields))
		{
			switch($key)
			{
				case "path_image":
					if (strlen($value)==0) unset($fields[$key]);else $fields[$key]="'$value'";
					break;
				case "name_price_lst":
				case "name_product":
					if (strlen($value)==0) {$this->txt_error=_ERROR_IN." ".constant("_".strtoupper($key));return false;}
					$fields[$key]="'$value'";
					break;
				case "cod_category":
				case "cod_product":
					if (strlen($value)==0) {$this->txt_error=""._NO_COD."";return false;}
					$fields[$key]="'$value'";
					break;
				case "name_category":
					if (strlen($value)==0) {$this->txt_error=""._CTG_NO_NAME."";return false;}
					$fields[$key]="'$value'";
					break;
				case "desc_product":
				case "desc_category":
					if (strlen($value)==0) $fields[$key]="NULL";else $fields[$key]="'$value'";
					break;
				case "dt_create":
				case "dt_start":
				case "dt_end":
					if (!$fields[$key]=$this->date_sql_format($value,""._DATE_FORMAT.""))
					{$this->txt_error=""._ERROR_IN." ".constant("_".strtoupper($key));return false;}
					break;
				case "precio":
					if (strpos($value,".")) $value=str_replace(".","",$value);
					$fields[$key]=str_replace(",",".",$value);
					if($fields[$key]=="") $fields[$key]="null";
					break;
				case "id_gal":
				case "id_category":
				case "id_parent_category":
				case "name_pricelst_search":
				case "name_prod_search":
				case "tp_state":
				case "tp_vat":
					break;
				default:
					unset($fields[$key]);
					break;
			} // end Switch
		} // end while

		if (array_key_exists("dt_start",$fields)) {
			if ($fields["dt_start"]>=$fields["dt_end"]) {$this->txt_error=""._ERROR_IN." "._DT_START." "._DT_END;return false;}
		}
			
		return true;
	}
	/******************************** END CLASS ***********************************************/
}


?>
