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


  /**
   *
   * @return 
   * @access public
   */
  public function __construct( )
  {
    parent::__construct();


    // remove "tr" so that it also work in translation view
    $this->show(str_replace('tr', '', $_GET['d_val']));
  }


  /**
   *
   * @access private
   */
  private function show( $rev_id )
  {
    // output a preview of the selected content
    $generate = new Generate();
    $generate->preview($rev_id);
  }


} // end of Export_Page
?>
