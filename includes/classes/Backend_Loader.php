<?php

namespace DogByteMarketing\User_Notifications;

use DogByteMarketing\User_Notifications\Backend\Admin;

class Backend_Loader {
  
  /**
   * Init
   *
   * @return void
   */
  public function init() {
    $this->load_admin();
  }
  
  /**
   * Load Admin
   *
   * @return void
   */
  public function load_admin() {
    $admin = new Admin();
    $admin->init();
  }

}