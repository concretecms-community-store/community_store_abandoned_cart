# Abandoned Cart

Abandoned Cart package for Concrete5 Community Store

__What is it?__  
A gentle reminder mail to persuade people to buy from your store.  
This mail usually has a link to retrieve the products that were in the cart before abandoning.  
Because these mails have high conversion rates... It is a must for every e-commerce.   
_-- Always test before using on a live website. --_  

So how does it work :  
A person starts with the checkout procedure but decide not to order (Shipping cost too high, just browsing, order later, ...).  
When the person has filled in the email address field at checkout and has pressed the 'next' button... Then a abandoned-cart-mail has been created (but not send).  
If the person places an order within a given period, then the mail will be deleted.  
If the person did not place an order within a given period, the mail is send __with a link to recover the cart__.

_Important to keep in mind with this package is what happens when choosing to mail 'everyone'._  
Example :  
I go to a community store as a guest. I go to the checkout and I hit next , next , next, but for some reason I do not complete the order. Even though I do not have an account, I will receive an e-mail to recover my cart.  

__Settings__

* enable / disable
* Send reminder after X days 
* Send reminder to : Everyone / Only Registered Users
* From email and from name
* Subject and content (with replacement tags)
* Header and footer (html)

__Cron Jobs__

A cron job runs to send the abandoned cart mails.  
Depending on the amount of visitors, set the cron job to run every 15 minutes. This should be more than enough.  
If the store has a huge amount of visitors and a lot of abandoned carts, run the cron job every 5 minutes. 

__Events__

At this time, there are 2 event hooks :  
* on_abandoned_cart_add
* on_abandoned_cart_update
