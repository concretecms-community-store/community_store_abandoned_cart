<?php
namespace Concrete\Package\CommunityStoreAbandonedCart\Src\AbandonedCart;

use Concrete\Core\Controller\Controller as RouteController;
use View;
use Session;
use Package;
use Database;
use Concrete\Core\Support\Facade\Url as Url;
use Concrete\Package\CommunityStore\Src\CommunityStore\Cart\Cart as StoreCart;
use Concrete\Package\CommunityStore\Src\CommunityStore\Product\Product as StoreProduct;
use Concrete\Package\CommunityStore\Src\CommunityStore\Customer\Customer as StoreCustomer;
use Concrete\Package\CommunityStoreAbandonedCart\Src\AbandonedCart\Abandoned as StoreAbandonedCart;

class CartInterceptor extends RouteController
{

  public static function recoverCart(){
    if(!empty($_GET['t']) && !empty($_GET['u'])){
      $abandonedCart = StoreAbandonedCart::getByIDandHash($_GET['t'], $_GET['u']);
      if(is_object($abandonedCart)){
        //can be recovered
        $cartIntermediate = json_decode($abandonedCart->getAcData(), true);
        if(!empty($cartIntermediate)){
          for($i = 0; $i < count($cartIntermediate); $i++){
            $cartIntermediate[$i]['product']['object'] = StoreProduct::getByID($cartIntermediate[$i]['product']['pID']);
          }
        }
        Session::set('communitystore.cart', $cartIntermediate);
      }
    }
    header('Location: ' . Url::to('/cart'));
    die;
  }

  public static function addNewAbandonedCart(){
    $packageObject = Package::getByHandle('community_store_abandoned_cart');
    $enabled = $packageObject->getConfig()->get('abandoned_cart.enabled');
    $reminder_days = $packageObject->getConfig()->get('abandoned_cart.reminder_days');
    $send_to = $packageObject->getConfig()->get('abandoned_cart.send_to');
    $mail_header = $packageObject->getConfig()->get('abandoned_cart.mail_header');
    $mail_footer = $packageObject->getConfig()->get('abandoned_cart.mail_footer');
    $mail_content = $packageObject->getConfig()->get('abandoned_cart.mail_content');
    $mail_subject = $packageObject->getConfig()->get('abandoned_cart.mail_subject');

    if($enabled == 1){
      //add a new abandoned cart entry
      $customer = new StoreCustomer();
      $customeremail = $customer->getValue('email');

      $canSend = 0;
      if($send_to == 0){
        $canSend = 1;
      }else if($send_to == 1 && !$customer->isGuest()){
        $canSend = 1;
      }

      if(!empty($customeremail) && $canSend == 1){
        //if duplicate, then the client is back within the allocated time to send the email...
        $isDuplicate = StoreAbandonedCart::getAbandonedCartByMail($customeremail);

        $abcart = new StoreAbandonedCart();
        $abandonedUID = $customer->getUserID();

        $abandonedCart = StoreCart::getCart();
        for($i = 0; $i > count($abandonedCart); $i++){
          unset($abandonedCart[$i]['product']['object']);
        }

        $abandonedAcSend = new \DateTime();
        $abandonedAcSend->modify('+' . $reminder_days . ' days');

        //search array
        $searchArr = array("%billing_first_name%", "%billing_last_name%", "%email%");
        //replace array
        $replaceArr = array($customer->getValue('billing_first_name'), $customer->getValue('billing_last_name'), $customeremail);
        $mailcontent = $mail_header. str_replace($searchArr, $replaceArr, $mail_content) . $mail_footer;
        $mailsubject = str_replace($searchArr, $replaceArr, $mail_subject);

        if(is_object($isDuplicate)){
          $returned = $isDuplicate->update($isDuplicate->getID(), json_encode($abandonedCart), $abandonedAcSend);
        }else{
          $returned = $abcart->add($abandonedUID, json_encode($abandonedCart), $customeremail, $mailsubject, $mailcontent, $abandonedAcSend);
        }
        echo json_encode(array('success' => 1));
      }else{
        if($canSend == 1){
          echo json_encode(array('retry' => 1));
        }
      }
    }
    exit;
  }
}
