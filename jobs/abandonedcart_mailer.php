<?php
namespace Concrete\Package\CommunityStoreAbandonedCart\Job;
use Package;
use Core;
use \Concrete\Core\Job\Job as AbstractJob;
use Concrete\Package\CommunityStoreAbandonedCart\Src\AbandonedCart\Abandoned as StoreAbandonedCart;

class AbandonedcartMailer extends AbstractJob
{

    public function getJobName(){
        return t("Send Abandoned Cart Mails.");
    }

    public function getJobDescription(){
        return t("Send Abandoned Cart Mails (recommended to run every 5-15 minutes).");
    }

    public function run(){
      $packageObject = Package::getByHandle('community_store_abandoned_cart');
      $enabled = $packageObject->getConfig()->get('abandoned_cart.enabled');
      // from
      $from_mail = $packageObject->getConfig()->get('abandoned_cart.from_mail');
      $from_name = $packageObject->getConfig()->get('abandoned_cart.from_name');

      if($enabled == 1 && !empty($from_mail)){
        $mh = Core::make('mail');
        //get all abandoned carts that need to be sent
        $mailsToSend = StoreAbandonedCart::getAbandonedCartMailsToSend();

        //send abandoned cart mails
        foreach($mailsToSend as $mail){
          $mh->to($mail->getAcMail());
          if(!empty($from_name)){
            $mh->from($from_mail, $from_name);
          }else{
            $mh->from($from_mail);
          }
          $mh->setSubject($mail->getAcSubject());
          $mh->setBodyHTML($mail->getAcContent());
          $mh->sendMail();

          //delete after mail send
          $mail->delete();
        }
      }
    }
}
