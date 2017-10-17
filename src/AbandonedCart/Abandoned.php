<?php
namespace Concrete\Package\CommunityStoreAbandonedCart\Src\AbandonedCart;

use Database;
use Events;
use Core;
use Package;
use \Symfony\Component\EventDispatcher\GenericEvent;
use Concrete\Core\Support\Facade\Url as Url;
use Doctrine\Mapping as ORM;

/**
 * @Entity
 * @Table(name="CommunityStoreAbandoned")
 */
class Abandoned
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    protected $acID;

    /**
     * @Column(type="integer")
     */
    protected $uID;

    /**
     * @Column(type="text")
     */
    protected $acData;

    /**
     * @Column(type="text")
     */
    protected $acSubject;

    /**
     * @Column(type="text")
     */
    protected $acContent;

    /**
     * @Column(type="text")
     */
    protected $acMail;

    /**
     * @Column(type="integer")
     */
    protected $acSent;

    /**
     * @Column(type="text")
     */
    protected $uHash;

    /**
     * @Column(type="datetime")
     */
    protected $acDateAdded;

    /**
     * @Column(type="datetime")
     */
    protected $acDateUpdated;

    /**
     * @Column(type="datetime")
     */
    protected $acDateSend;

    protected static $table = "CommunityStoreAbandoned";

    public static function getTableName()
    {
        return self::$table;
    }

    public function setAcData($acData)
    {
        $this->acData = $acData;
    }
    public function setAcSubject($acSubject)
    {
        $this->acSubject = $acSubject;
    }
    public function setAcContent($acContent)
    {
        $this->acContent = $acContent;
    }
    public function setAcMail($acMail)
    {
        $this->acMail = $acMail;
    }
    public function setAcSent($acSent)
    {
        $this->acSent = $acSent;
    }

    public function setUID($uID)
    {
        $this->uID = $uID;
    }
    public function setUHash($uHash)
    {
        $this->uHash = $uHash;
    }

    public function setAcDateAdded($acDateAdded)
    {
        $this->acDateAdded = $acDateAdded;
    }
    public function setAcDateUpdated($acDateUpdated)
    {
        $this->acDateUpdated = $acDateUpdated;
    }
    public function setAcDateSend($acDateSend)
    {
        $this->acDateSend = $acDateSend;
    }

    public function getID()
    {
        return $this->acID;
    }
    public function getAcData()
    {
        return $this->acData;
    }
    public function getAcMail()
    {
        return $this->acMail;
    }
    public function getAcContent()
    {
        return $this->acContent;
    }
    public function getAcSubject()
    {
        return $this->acSubject;
    }
    public function getAcSent()
    {
        return $this->acSent;
    }

    public function getUID()
    {
        return $this->uID;
    }
    public function getUHash()
    {
        return $this->uHash;
    }

    public function getAcDateAdded()
    {
        return $this->acDateAdded;
    }

    public function getAcDateUpdated()
    {
        return $this->acDateUpdated;
    }
    public function getAcDateSend()
    {
        return $this->acDateSend;
    }

    public function save()
    {
        $em = \Database::connection()->getEntityManager();
        $em->persist($this);
        $em->flush();
    }

    public function delete()
    {
        $em = \Database::connection()->getEntityManager();
        $em->remove($this);
        $em->flush();
    }

    public static function add($uID, $acData, $acMail, $mailSubject, $mailContent, $acDateSend)
    {
        $abandonedcart = new self();
        if(empty($uID)){
          $uID = 0;
        }
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $uHash = '';
        for ($i = 0; $i < 14; $i++) {
            $uHash .= $characters[rand(0, strlen($characters))];
        }

        $packageObject = Package::getByHandle('community_store_abandoned_cart');
        $link_style = $packageObject->getConfig()->get('abandoned_cart.link_style');

        $abandonedcart->setUID($uID);
        $abandonedcart->setAcData($acData);
        $abandonedcart->setAcMail($acMail);
        $abandonedcart->setAcContent($mailContent);
        $abandonedcart->setAcSubject($mailSubject);
        $abandonedcart->setUHash($uHash);
        $abandonedcart->setAcSent(0);
        $abandonedcart->setAcDateAdded(new \DateTime());
        $abandonedcart->setAcDateUpdated(new \DateTime());
        $abandonedcart->setAcDateSend($acDateSend);

        $abandonedcart->save();

        //save after determining acID and hash
        $searchArr = array("%cart_start%", "%cart_end%");
        $replaceArr = array("<a style='" . $link_style . "' href='". Url::to('/recovercart') ."?t=".$abandonedcart->getID()."&u=".$abandonedcart->getuHash()."'>", '</a>');
        $maillinkedcontent = str_replace($searchArr, $replaceArr, $abandonedcart->getacContent());
        $abandonedcart->setAcContent($maillinkedcontent);

        $abandonedcart->save();

        //Generic Event launch
        $event = new GenericEvent();
        $event->setArgument('abandonedcart', $abandonedcart);
        \Events::dispatch('on_abandoned_cart_add', $event);

        return $abandonedcart;
    }

    public static function update($acID, $acData, $acDateSend){
        $abandonedcart = self::getByID($acID);

        $abandonedcart->setAcData($acData);
        $abandonedcart->setAcDateUpdated(new \DateTime());
        $abandonedcart->setAcDateSend($acDateSend);

        $abandonedcart->save();

        //Generic Event launch
        $event = new GenericEvent();
        $event->setArgument('abandonedcart', $abandonedcart);
        \Events::dispatch('on_abandoned_cart_update', $event);

        return $abandonedcart;
    }

    public static function getByID($acID)
    {
        $db = \Database::connection();
        $em = $db->getEntityManager();

        return $em->find(get_class(), $acID);
    }

    public static function getByIDandHash($acID, $uHash)
    {
        $db = \Database::connection();
        $row = $db->GetOne("SELECT * FROM " . self::getTableName() . " WHERE acID = ? AND uHash = ?;", array($acID, $uHash));
        if(!empty($row)){
          return self::getByID($row);
        }else{
          return '';
        }
    }

    public static function getAbandonedCartByMail($email){
      $db = \Database::connection();
      $row = $db->GetOne("SELECT * FROM " . self::getTableName() . " WHERE acMail = ? AND acDateSend > DATE(NOW())", array($email));
      if(!empty($row)){
        return self::getByID($row);
      }else{
        return '';
      }
    }

    public static function deleteSentAbandonedCarts($acID){
      //delete all abandoned carts that have been sent
      $db = \Database::connection();
      $rows = $db->GetAll("SELECT * FROM " . self::getTableName() . " WHERE acSent = 1 AND acDateSend < NOW()");
      $amountDeleted = 0;
      foreach($rows as $row){
        $abCart = self::getByID($row['acID']);
        $abCart->delete();
        $amountDeleted++;
      }

      return $amountDeleted;
    }

    public static function getAbandonedCartMailsToSend(){
      $db = \Database::connection();
      $rows = $db->GetAll("SELECT * FROM " . self::getTableName() . " WHERE acSent = 0 AND acDateSend < NOW()");
      $results = array();

      foreach($rows as $row){
        $results[] = self::getByID($row['acID']);
      }

      return $results;
    }
}
