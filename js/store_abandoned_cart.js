function AbandonedValidateEmail(mail) {
 if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(mail)){
    return (true);
  }
  return (false);
}

function addAbandonedCart(emailLog){
  $.ajax({
      url: CHECKOUTURL + "/abandoned",
      type: 'post',
      data: {
          email: emailLog,
      },
      success: function (result) {
        var response = JSON.parse(result);
        if(typeof response.retry != 'undefined'){
          if(response.retry == 1){
            addAbandonedCart(emailLog);
          }
        }
      },
      error: function (data) {
        console.log('error with abandoned cart email retrieval...');
        console.log(data);
      }
  });
}

$(document).ready(function(){
  $("#store-checkout-form-group-billing").submit(function (e) {
      var emailLog = $('#store-email').val();
      addAbandonedCart(emailLog);
  });
});
