<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'../../../init.php');
include(dirname(__FILE__).'/changeshipping.php');

$changeshipping = new Changeshipping();
global $smarty, $cookie;
ini_set('display_errors', 'on');

if (Tools::isSubmit('submitChangecarrier'))
{
	$errors = array();
    
    $order_id = Tools::getValue('orderid');
		$carrier = Tools::getValue('carrier');
		$price_e = Tools::getValue('pricee');
		$price_i = Tools::getValue('pricei');
    $ddate = Tools::getValue('ddate');
		

		if (!Validate::isPrice($price_e)) $errors[] = $changeshipping->l('Price  exclude tax must be a number with point as separator');
		if (!Validate::isPrice($price_i)) $errors[] = $changeshipping->l('Price  include tax must be a number with point as separator');
   // if (!Validate::isBirthDate($ddate)) $errors[] = $changeshipping->l('Error date format');
		
/*function varDumpToString ($var)
{
    ob_start();
    var_dump($var);
    $result = ob_get_clean();
    return $result;
}

$result = varDumpToString($orderinv);
		

		$errors[] = $result;*/


	if (sizeof($errors))
	{
		$return = array(
			'hasError' => !empty($errors), 
			'errors' => $errors
		);

		die(Tools::jsonEncode($return));
	}
	else
	{
		$order = new Order($order_id);
		$order_shipping = $order->getShipping();
		$order->id_carrier = $carrier;
		$id_order_carrier = $order_shipping[0]['id_order_carrier'];
		$id_order_invoice = $order_shipping[0]['id_order_invoice'];
		$ddate = date( 'Y-m-d H:i:s', strtotime( $ddate ) );
    
		$orderinv = new OrderInvoice($id_order_invoice);
		
		$sql = 'UPDATE  `'._DB_PREFIX_.'order_carrier` SET  `id_carrier` = '.$carrier.' WHERE `id_order_carrier` = '.$id_order_carrier;
		$sql_e = 'UPDATE  `'._DB_PREFIX_.'order_carrier` SET  `shipping_cost_tax_excl` = '.$price_e.' WHERE `id_order_carrier` = '.$id_order_carrier;
		$sql_i = 'UPDATE  `'._DB_PREFIX_.'order_carrier` SET  `shipping_cost_tax_incl` = '.$price_i.' WHERE `id_order_carrier` = '.$id_order_carrier;
    $sql_d = 'UPDATE  `'._DB_PREFIX_.'order_carrier` SET  `date_add` = "'.$ddate.'" WHERE `id_order_carrier` = '.$id_order_carrier;
		Db::getInstance()->execute ($sql);
		Db::getInstance()->execute ($sql_e);
		Db::getInstance()->execute ($sql_i);
    Db::getInstance()->execute ($sql_d);
    
    $order->updateShippingCost($price_i);
    $difference_i = $price_i - $order->total_shipping_tax_incl;
    $difference_e = $price_e - $order->total_shipping_tax_excl;
    if ($difference_i == 0)
    {
    }
    else
    {
    $order->total_shipping_tax_incl = $price_i;
    $order->total_paid_tax_incl += $difference_i;
    $orderinv->total_shipping_tax_incl = $order->total_shipping_tax_incl;
    $orderinv->total_paid_tax_incl = $order->total_paid_tax_incl;
    }
    if ($difference_e == 0)
    {
    }
    else
    {
    $order->total_shipping_tax_excl = $price_e;
    $order->total_paid_tax_excl += $difference_e;
    $orderinv->total_shipping_tax_excl = $order->total_shipping_tax_excl;
    $orderinv->total_paid_tax_excl = $order->total_paid_tax_excl;
    }

    $order->update();
    $orderinv->update();
    $return = true;
    die(Tools::jsonEncode($return));
	}
}
else
{
		$id_lang = $cookie->id_lang;;
    
    $dirbase = Tools::getValue('dirbase');
    $orderid = Tools::getValue('orderid');
    
    $carriers_list = Carrier::getCarriers($id_lang,true);
    
    $order = new Order($orderid);
		$order_shipping = $order->getShipping();

		$id_order_carrier = (int)$order_shipping[0]['id_carrier'];
		$order_shipping_price_e = number_format($order_shipping[0]['shipping_cost_tax_excl'],2, '.', '');
		$order_shipping_price_i = number_format($order_shipping[0]['shipping_cost_tax_incl'],2, '.', '');
    $ddate = date( 'd.m.Y', strtotime( $order_shipping[0]['date_add'] ) );

    
    $selector = '<select id="carrierslist">';
    foreach ($carriers_list as $carrier)
    {
      if ((int)$carrier['id_carrier'] == $id_order_carrier)
      $selector.='<option value="'.$carrier['id_carrier'].'" selected="selected">'.$carrier['name'].'</option>';
      else
      $selector.='<option value="'.$carrier['id_carrier'].'">'.$carrier['name'].'</option>';
    }
    $selector.='</select>';
    
    $smarty->assign(
        array(
            'carriers_list' => $carriers_list,
            'selector' => $selector,
            'order_shipping_price_e' => $order_shipping_price_e,
            'order_shipping_price_i' => $order_shipping_price_i,
            'dirbase' => $dirbase,
            'orderid' => $orderid,
            'ddate' => $ddate,
        )
    );
    
	$smarty->display(dirname(__FILE__).'/form.tpl');
}