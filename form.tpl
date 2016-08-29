<div id="changeshippingbox" class="bootstrap" style="margin: 10px;">
<div id="errors" class="error" style="display: none"></div>
<h4>{l s='Change carrier' mod='changeshipping'}</h4>
{$selector}
<h4>{l s='Delivery date' mod='changeshipping'}</h4>
<input type="text" id="ddate" name="ddate" value="{$ddate}">
<h4>{l s='Change price include tax' mod='changeshipping'}</h4>
<input type="text" id="shippingcostchangei" name="shippingcostchangei" value="{$order_shipping_price_i}">
<h4>{l s='Change price exclude tax' mod='changeshipping'}</h4>
<input type="text" id="shippingcostchangee" name="shippingcostchangee" value="{$order_shipping_price_e}">
<button type="submit" id="submitChangecarrier" name="submitChangecarrier" class="btn btn-primary" style="margin-top: 10px;">{l s='Change carrier' mod='changeshipping'}</button>
<input id="basedir" type="hidden" value="{$dirbase}">
<input id="orderid" type="hidden" value="{$orderid}">
</div>
{literal}
<script type="text/javascript">
$(document).ready(function()
{

  $("#shippingcostchangei").change(function() {
  $("#shippingcostchangee").val($('#shippingcostchangei').val());
  });
  
  $('#submitChangecarrier').click(function(){
   var carrier = $('#carrierslist').val();
   var basedir = $('#basedir').val();
   var orderid = $('#orderid').val();
   var pricee = $('#shippingcostchangee').val();
   var pricei = $('#shippingcostchangei').val();
   var ddate = $('#ddate').val();
   
    $.ajax({
			type: 'POST',
			url: basedir + 'modules/changeshipping/ajax.php',
			async: true,
			cache: false,
			dataType : "json",
			data: 'submitChangecarrier=true' + '&carrier=' + carrier  + '&basedir=' + basedir + '&orderid=' + orderid + '&pricei=' + pricei + '&pricee=' + pricee + '&ddate=' + ddate,
			success: function(jsonData)
			{
				if (jsonData == null)
				{
					$.fancybox.close();
					location.reload();
					return false;
				}

				if (jsonData.hasError)
				{
          var errors = '<b>'+'Ошибки: ' + '</b><ol>';
					for(error in jsonData.errors)
						if(error != 'indexOf')
							errors += '<li>'+jsonData.errors[error]+'</li>';						
						errors += '</ol>';
						$('#errors').html(errors).slideDown('slow');
				}
        else
        {
          location.reload();
        }
			},
		});
    location.reload();
  });
});

</script>
{/literal}