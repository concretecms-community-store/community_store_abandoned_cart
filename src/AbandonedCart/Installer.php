<?php
namespace Concrete\Package\CommunityStoreAbandonedCart\Src\AbandonedCart;

use Package;
use SinglePage;
use Core;
use Job;
use Page;

defined('C5_EXECUTE') or die(_("Access Denied."));

class Installer
{
    public static function setConfigValues($pkg){
      $pkg->getConfig()->save('abandoned_cart.enabled', 0);
      $pkg->getConfig()->save('abandoned_cart.reminder_days', 1);
      $pkg->getConfig()->save('abandoned_cart.send_to', 1);

      // FROM
      $pkg->getConfig()->save('abandoned_cart.from_mail', '');
      $pkg->getConfig()->save('abandoned_cart.from_name', '');

      // BCC
      $pkg->getConfig()->save('abandoned_cart.bcc_mail', '');
      $pkg->getConfig()->save('abandoned_cart.bcc_subject', 'Abandoned cart in your eshop...');

      // header and footer Mails
      $pkg->getConfig()->save('abandoned_cart.mail_header', '<table width="98%" height="auto" border="0" cellpadding="0" cellspacing="0"><tr><td bgcolor="#ffffff" valign="top"><table width="600" border="0" cellpadding="0" cellspacing="0" align="center" style="max-width:600px;"><tr style="width:100%;min-width:100%;"><td style="border: none; width:100%;min-width:100%;" align="left" bgcolor="#ffffff" width="100%" border="0" cellpadding="0" cellspacing="0">');
      $pkg->getConfig()->save('abandoned_cart.mail_footer', '</td></tr></table></td></tr></table>');

      // Mail content
      $pkg->getConfig()->save('abandoned_cart.mail_subject', 'Hey %billing_first_name%, did you forget something?');
      $pkg->getConfig()->save('abandoned_cart.mail_content', '<p>First of all, thank you for shopping on our website.<br/>This is a small reminder that you left your shopping cart with some products in it... Did something happen at the checkout?</p><p>We have saved your shopping cart.<br/>%cart_start%Click here to recover it.%cart_end%</p>');

      $pkg->getConfig()->save('abandoned_cart.link_style', 'text-decoration: none; color: #000000;');
    }

    public static function installJobs($pkg){
      $job = Job::getByHandle('abandonedcart_mailer');
      if(!is_object($job)){
        Job::installByPackage('abandonedcart_mailer', $pkg);
      }
    }

    public static function installSinglePages($pkg) {
        $page = self::installSinglePage('/dashboard/store/settings/abandonedcart', $pkg);
        if (is_object($page)) {
    			   $page->update(array('cName' => t('Abandoned Cart'), 'cDescription'=>t('Abandoned Cart Mail Settings.')));
    		}
    }

    public static function installSinglePage($path, $pkg) {
        $page = Page::getByPath($path);
        if (!is_object($page) || $page->isError()) {
            return SinglePage::add($path, $pkg);
        } else {
            return null;
        }
    }

    public static function upgrade($pkg){
        self::installSinglePages($pkg);
        self::installJobs($pkg);
    }

}
