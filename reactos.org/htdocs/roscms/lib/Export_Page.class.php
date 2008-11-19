<?php
    /*
    RosCMS - ReactOS Content Management System
    Copyright (C) 2007  Klemens Friedl <frik85@reactos.org>

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
    */

/**
 * class Export_Page
 * 
 */
class Export_Page extends Export
{

  /** Aggregations: */

  /** Compositions: */

   /*** Attributes: ***/


  /**
   *
   * @return 
   * @access public
   */
  public function __construct( )
  {
    parent::__construct();
    $this->page();
  }


  /**
   *
   * @return 
   * @access public
   */
  public function page( )
  {
    global $RosCMS_GET_d_value, $RosCMS_GET_d_value2,$RosCMS_GET_d_value3;
    global $RosCMS_GET_d_r_lang;
    global $roscms_standard_language;

    $dynamic_num = $RosCMS_GET_d_value3;

    switch (@$_GET['d_u']) {
      case 'output':
        // @TODO
        break;

      case 'show':
      default:
        if (empty($_GET['d_r_id']) || strpos($_GET['d_r_id'], 'tr') >= 0) {
          // translation mode (contains "tr")
          $RosCMS_GET_d_value2 = $RosCMS_GET_d_r_lang;
        }

        // remove "tr" so that it also work in translation view
        $RosCMS_GET_d_value = str_replace('tr', '', $RosCMS_GET_d_value);

        if ( is_numeric($RosCMS_GET_d_value) ) {
          $stmt=DBConnection::getInstance()->prepare("SELECT d.data_name, r.rev_id, d.data_id, r.rev_language FROM data_ d, data_revision r WHERE r.data_id = d.data_id  AND r.rev_id = :rev_id  ORDER BY r.rev_version DESC LIMIT 1");
          $stmt->bindParam('rev_id',$RosCMS_GET_d_value,PDO::PARAM_INT);
        }
        else {
          $stmt=DBConnection::getInstance()->prepare("SELECT d.data_name, r.rev_id, d.data_id, r.rev_language FROM data_ d JOIN data_revision r ON r.data_id = d.data_id WHERE d.data_name = :data_name AND (r.rev_language = lang_one OR r.rev_language = :lang_two) ORDER BY r.rev_version DESC LIMIT 1");
          $stmt->bindParam('data_name',$RosCMS_GET_d_value,PDO::PARAM_STR);
          $stmt->bindParam('lang_one',$RosCMS_GET_d_value2,PDO::PARAM_STR);
          $stmt->bindParam('lang_two',$roscms_standard_language,PDO::PARAM_STR);
        }

        $stmt->execute();
        $revision = $stmt->fetchOnce();
        if ($dynamic_num == '') {
          $dynamic_num = Tag::getValueByUser($revision['data_id'], $revision['rev_id'], 'number', -1);
        }

        Log::writeGenerateLow('preview page: generate_page('.$revision['data_name'].', '.$revision['rev_language'].', '.$dynamic_num.', '.$_GET['d_u'].')');

        $export_html = new Export_HTML();
        echo $export_html->processText($revision['rev_id'], $_GET['d_u']);
        break;
    }
  }


} // end of Export_Page
?>
