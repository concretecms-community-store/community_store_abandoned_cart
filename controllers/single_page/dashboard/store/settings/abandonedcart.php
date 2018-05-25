<?php
namespace Concrete\Package\CommunityStoreAbandonedCart\Controller\SinglePage\Dashboard\Store\Settings;
/*
 * abandoned cart settings Controller
 * created by Jos De Berdt
 * github @Jozzeh
 */

use \Concrete\Core\Page\Controller\DashboardPageController;
use Package;
use Core;
use Config;
use Concrete\Core\Http\Request;

class Abandonedcart extends DashboardPageController{
  public function view(){
    $packageObject = Package::getByHandle('community_store_abandoned_cart');
    // get data
    $enabled = $packageObject->getConfig()->get('abandoned_cart.enabled');
    //send reminder mail after X days
    $reminder_days = $packageObject->getConfig()->get('abandoned_cart.reminder_days');
    $send_to = $packageObject->getConfig()->get('abandoned_cart.send_to');

    // from
    $from_mail = $packageObject->getConfig()->get('abandoned_cart.from_mail');
    $from_name = $packageObject->getConfig()->get('abandoned_cart.from_name');

    // bcc
    $bcc_mail = $packageObject->getConfig()->get('abandoned_cart.bcc_mail');
    $bcc_subject = $packageObject->getConfig()->get('abandoned_cart.bcc_subject');

    // header and footer Mails
    $mail_header = $packageObject->getConfig()->get('abandoned_cart.mail_header');
    $mail_footer = $packageObject->getConfig()->get('abandoned_cart.mail_footer');

    // Mail content
    $mail_content = $packageObject->getConfig()->get('abandoned_cart.mail_content');
    $mail_subject = $packageObject->getConfig()->get('abandoned_cart.mail_subject');

    $link_style = $packageObject->getConfig()->get('abandoned_cart.link_style');

    $this->set('enabled',$enabled);
    $this->set('reminder_days',$reminder_days);
    $this->set('mail_header',$mail_header);
    $this->set('mail_footer',$mail_footer);
    $this->set('from_mail',$from_mail);
    $this->set('from_name',$from_name);
    $this->set('bcc_mail',$bcc_mail);
    $this->set('bcc_subject',$bcc_subject);
    $this->set('mail_content',$mail_content);
    $this->set('mail_subject',$mail_subject);
    $this->set('link_style', $link_style);
    $this->set('send_to', $send_to);
  }

  public function save(){
    $postData = $this->post();

    if(!empty($postData)){
      $packageObject = Package::getByHandle('community_store_abandoned_cart');
      // set data
      if(intval($postData['enabled']) != 1){
        $postData['enabled'] = 0;
      }
      $packageObject->getConfig()->save('abandoned_cart.enabled', intval($postData['enabled']));
      //send reminder mail after X days
      if(intval($postData['reminder_days']) < 1){
        $postData['reminder_days'] = 1;
      }
      $packageObject->getConfig()->save('abandoned_cart.reminder_days', intval($postData['reminder_days']));
      $packageObject->getConfig()->save('abandoned_cart.send_to', intval($postData['send_to']));

      $packageObject->getConfig()->save('abandoned_cart.from_mail', $postData['from_mail']);
      $packageObject->getConfig()->save('abandoned_cart.from_name', $postData['from_name']);

      $packageObject->getConfig()->save('abandoned_cart.bcc_mail', $postData['bcc_mail']);
      $packageObject->getConfig()->save('abandoned_cart.bcc_subject', $postData['bcc_subject']);
      // header and footer Mails
      $packageObject->getConfig()->save('abandoned_cart.mail_header', $postData['mail_header']);
      $packageObject->getConfig()->save('abandoned_cart.mail_footer', $postData['mail_footer']);

      // Mail content
      $packageObject->getConfig()->save('abandoned_cart.mail_content', $postData['mail_content']);
      $packageObject->getConfig()->save('abandoned_cart.mail_subject', $postData['mail_subject']);

      $packageObject->getConfig()->save('abandoned_cart.link_style', $postData['link_style']);
    }

    $this->view();
  }
}
?>
