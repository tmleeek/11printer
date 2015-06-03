<?php

class TM_FireCheckout_Block_Checkout_Shipping extends Mage_Checkout_Block_Onepage_Shipping
{
    /**
     * Added to get shipping address from quote for guests too.
     *
     * Return Sales Quote Address model (shipping address)
     *
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getAddress()
    {
        if (is_null($this->_address)) {
            $this->_address = $this->getQuote()->getShippingAddress();
        }

        return $this->_address;
    }

//    public function getAddressesHtmlSelect($type)
//    {
//        if ($this->isCustomerLoggedIn()) {
//            $options = array();
//            foreach ($this->getCustomer()->getAddresses() as $address) {
//                $options[] = array(
//                    'value'=>$address->getId(),
//                    'label'=>$address->format('oneline')
//                );
//            }

//            if ($type=='billing') {
//                $address = $this->getCustomer()->getPrimaryBillingAddress();
//            } else {
//                $address = $this->getCustomer()->getPrimaryShippingAddress();
//            }
//            if ($address) {
//                $addressId = $address->getId();
//            } else {
//                $addressId = $this->getAddress()->getId();
//            }

//            $select = $this->getLayout()->createBlock('core/html_select')
//                ->setName($type.'_address_id')
//                ->setId($type.'-address-select')
//                ->setClass('address-select')
//                ->setExtraParams('onchange="'.$type.'.newAddress(!this.value)"')
//                ->setValue($addressId)
//                ->setOptions($options);

//            $select->addOption('', Mage::helper('checkout')->__('New Address'));

//            return $select->getHtml();
//        }
//        return '';
//    }

    public function getCountryHtmlSelect($type)
    {
        $countryId = $this->getAddress()->getCountryId();
        if (is_null($countryId)) {
            $countryId = Mage::helper('core')->getDefaultCountry();
        }
        $select = $this->getLayout()->createBlock('core/html_select')
            ->setName($type.'[country_id]')
            ->setId($type.':country_id')
            ->setTitle(Mage::helper('checkout')->__('Country'))
            ->setClass('validate-select')
            ->setValue($countryId)
            ->setOptions($this->getCountryOptions());
//        if ($type === 'shipping') {
//            $select->setExtraParams('onchange="shipping.setSameAsBilling(false);"');
//        }

        return $select->getHtml();
    }
}
