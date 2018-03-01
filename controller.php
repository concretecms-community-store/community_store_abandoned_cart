<?php
namespace Concrete\Package\CommunityStoreAbandonedCart;

/**
 * Community Store Abandoned Cart
 *
 * @author Jos De Berdt <www.josdeberdt.be>
 * @version 0.0.9
 * @package community_store_abandoned_cart
 * @github jozzeh
 */

 use Package;
 use Page;
 use SinglePage;
 use Route;
 use Core;
 use View;
 use AssetList;
 use Asset;
 use Concrete\Package\CommunityStoreAbandonedCart\Src\AbandonedCart\Installer as Installer;
 use Concrete\Package\CommunityStoreAbandonedCart\Src\AbandonedCart\Abandoned as StoreAbandonedCart;
 use Concrete\Package\CommunityStore\Src\CommunityStore\Order\OrderEvent as StoreOrderEvent;
 use Concrete\Core\Support\Facade\Events as Events;
 use Whoops\Exception\ErrorException;

class Controller extends Package{

  protected $pkgHandle = 'community_store_abandoned_cart';
  protected $appVersionRequired = '5.7.5.8';
  protected $pkgVersion = '0.0.9';

  public function getPackageDescription(){
      return t("Abandoned Cart Mail Package for Community Store.");
  }

  public function getPackageName(){
      return t("Abandoned Cart");
  }

  public function installAbandonedCart()
  {
      $pkg = Package::getByHandle('community_store_abandoned_cart');

      Installer::installSinglePages($pkg);
      Installer::installJobs($pkg);
      Installer::setConfigValues($pkg);
  }

  public function install()
  {
      $installed = Package::getInstalledHandles();
      if(!(is_array($installed) && in_array('community_store',$installed)) ) {
          throw new ErrorException(t('This package requires that Community Store be installed'));
      } else {
          parent::install();
          $this->installAbandonedCart();
      }
  }

  public function upgrade()
  {
      $pkg = Package::getByHandle('community_store_abandoned_cart');
      Installer::upgrade($pkg);
      parent::upgrade();
  }

  public function registerRoutes()
  {
      Route::register('/checkout/abandoned', '\Concrete\Package\CommunityStoreAbandonedCart\Src\AbandonedCart\CartInterceptor::addNewAbandonedCart');
      Route::register('/recovercart', '\Concrete\Package\CommunityStoreAbandonedCart\Src\AbandonedCart\CartInterceptor::recoverCart');
  }
  public function on_start()
  {
      $this->registerRoutes();

      $al = AssetList::getInstance();
      $al->register('javascript', 'community-store-abandoned-cart', 'js/store_abandoned_cart.js', array('version' => '1', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => false, 'combine' => false), $this);
      $al->registerGroup('community-store-abandoned-cart',
          array(
              array('javascript', 'community-store-abandoned-cart'),
          )
      );
   
      $requestURI = $_SERVER['REQUEST_URI'];
      $requestArray = explode("/", $requestURI);
      $lastEl = array_values(array_slice($requestArray, -1))[0];
      if($lastEl == "checkout"){
         $view = View::getInstance();
         $view->requireAsset('community-store-abandoned-cart');
      }

      Events::addListener('on_community_store_order', function($event){
          //when an order has been placed, we need to crosscheck the abandoned cart Table
          //if we find an abandoned cart with the same email -> delete it.
          //Because the order was made and cart was not abandoned...
          $order = $event->getOrder();
          $email = $order->getAttribute('email');

          $abandonedcart = StoreAbandonedCart::getAbandonedCartByMail($email);
          if(is_object($abandonedcart)){
            //abandonedcart with this email has been found -> delete
            $abandonedcart->delete();
          }
      });
  }

}

?>
