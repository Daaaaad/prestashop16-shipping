<?php
/**
 * This file is part of the Prestashop Shipping module of DPD Nederland B.V.
 *
 * Copyright (C) 2017  DPD Nederland B.V.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

require_once (_PS_MODULE_DIR_ . 'dpdconnect' . DIRECTORY_SEPARATOR . 'dpdconnect.php');

class AdminCarrierWizardController extends AdminCarrierWizardControllerCore
{
    public $dpdCarrier;

    public function __construct()
    {
        $dpdconnect = new dpdconnect();
        $this->dpdCarrier = $dpdconnect->dpdCarrier();
        parent::__construct();
    }

    public function getStepOneFieldsValues($carrier)
    {
        return [
            'id_carrier' => $this->getFieldValue($carrier, 'id_carrier'),
            'name' => $this->getFieldValue($carrier, 'name'),
            'delay' => $this->getFieldValue($carrier, 'delay'),
            'grade' => $this->getFieldValue($carrier, 'grade'),
            'url' => $this->getFieldValue($carrier, 'url'),
            //own code
            'showfromtime' => Configuration::get('dpdconnect_saturday_showfromtime'),
            'showfromday' => Configuration::get('dpdconnect_saturday_showfromday'),
            'showtillday' => Configuration::get('dpdconnect_saturday_showtillday'),
            'showtilltime' => Configuration::get('dpdconnect_saturday_showtilltime'),
        ];
    }


    public function renderStepOne($carrier)
    {
        $this->fields_form = [
            'form' => [
                'id_form' => 'step_carrier_general',
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('Carrier name'),
                        'name' => 'name',
                        'required' => true,
                        'hint' => [
                            sprintf($this->l('Allowed characters: letters, spaces and "%s".'), '().-'),
                            $this->l('The carrier\'s name will be displayed during checkout.'),
                            $this->l('For in-store pickup, enter 0 to replace the carrier name with your shop name.')
                        ]
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Transit time'),
                        'name' => 'delay',
                        'lang' => true,
                        'required' => true,
                        'maxlength' => 512,
                        'hint' => $this->l('The estimated delivery time will be displayed during checkout.')
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Speed grade'),
                        'name' => 'grade',
                        'required' => false,
                        'size' => 1,
                        'hint' => $this->l('Enter "0" for a longest shipping delay, or "9" for the shortest shipping delay.')
                    ],
                    [
                        'type' => 'logo',
                        'label' => $this->l('Logo'),
                        'name' => 'logo'
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Tracking URL'),
                        'name' => 'url',
                        'hint' => $this->l('Delivery tracking URL: Type \'@\' where the tracking number should appear. It will be automatically replaced by the tracking number.'),
                        'desc' => $this->l('For example: \'http://example.com/track.php?num=@\' with \'@\' where the tracking number should appear.')
                    ],

                ]
            ]
        ];
        //own code
        if ($carrier->id == $this->dpdCarrier->getLatestCarrierByReferenceId(Configuration::get('dpdconnect_saturday'))
            || $carrier->id ==  $this->dpdCarrier->getLatestCarrierByReferenceId(Configuration::get('dpdconnect_classic_saturday')) ) {
            $this->fields_form['form']['input'][] = [
                'required' => true,
                'type' => 'select',
                'label' => $this->l('Show from day'),
                'name' => 'showfromday',
                'options' => [
                    'query' => [
                        ['key' => '', 'name' => $this->l('Select a Day')],
                        ['key' => 'monday', 'name' => $this->l('Monday')],
                        ['key' => 'tuesday', 'name' => $this->l('Tuesday')],
                        ['key' => 'wednesday', 'name' => $this->l('Wednesday')],
                        ['key' => 'thursday', 'name' => $this->l('Thursday')],
                        ['key' => 'friday', 'name' => $this->l('Friday')],
                        ['key' => 'saturday', 'name' => $this->l('Saturday')],
                        ['key' => 'sunday', 'name' => $this->l('Sunday')],
                    ],
                    'id' => 'key',
                    'name' => 'name'
                ]
            ];

            $this->fields_form['form']['input'][] = [
                'required' => true,
                'type' => 'text',
                'label' => $this->l('Show from time'),
                'name' => 'showfromtime',
                'hint' => $this->l('Time in 24h format'),
                'desc' => $this->l('For example: 18:00')
            ];
            $this->fields_form['form']['input'][] = [
                'required' => true,
                'type' => 'select',
                'label' => $this->l('Show till day'),
                'name' => 'showtillday',
                'options' => [
                    'query' => [
                        ['key' => '', 'name' => $this->l('Select a Day')],
                        ['key' => 'monday', 'name' => $this->l('Monday')],
                        ['key' => 'tuesday', 'name' => $this->l('Tuesday')],
                        ['key' => 'wednesday', 'name' => $this->l('Wednesday')],
                        ['key' => 'thursday', 'name' => $this->l('Thursday')],
                        ['key' => 'friday', 'name' => $this->l('Friday')],
                        ['key' => 'saturday', 'name' => $this->l('Saturday')],
                        ['key' => 'sunday', 'name' => $this->l('Sunday')],
                    ],
                    'id' => 'key',
                    'name' => 'name'
                ]
            ];

            $this->fields_form['form']['input'][] = [
                'required' => true,
                'type' => 'text',
                'label' => $this->l('Show till time'),
                'name' => 'showtilltime',
                'hint' => $this->l('Time in 24h format'),
                'desc' => $this->l('For example: 18:00')
            ];
        }
        // prestashops code
        $tpl_vars = ['max_image_size' => (int)Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE') / 1024 / 1024];
        $fields_value = $this->getStepOneFieldsValues($carrier);
        return parent::renderGenericForm(['form' => $this->fields_form], $fields_value, $tpl_vars);
    }


    /**
     * for getting al values for the fields
     */
    public function getTplRangesVarsAndValues($carrier, &$tpl_vars, &$fields_value)
    {
        if ($this->dpdCarrier->checkIfCarrierIsDpd($carrier->id_reference) && Shop::isFeatureActive()) {
            // own code
            list($shopId, $groupId) = $this->getShopOrGroupId($carrier);

            $tpl_vars['zones'] = Zone::getZones(false);
            $carrier_zones = $carrier->getZones();
            $carrier_zones_ids = [];
            if (is_array($carrier_zones)) {
                foreach ($carrier_zones as $carrier_zone) {
                    $carrier_zones_ids[] = $carrier_zone['id_zone'];
                }
            }

            $range_table = $carrier->getRangeTable();
            $shipping_method = $carrier->getShippingMethod();

            $zones = Zone::getZones(false);
            foreach ($zones as $zone) {
                $fields_value['zones'][$zone['id_zone']] = Tools::getValue(
                    'zone_'.$zone['id_zone'],
                    (in_array($zone['id_zone'], $carrier_zones_ids))
                );
            }

            if ($shipping_method == Carrier::SHIPPING_METHOD_FREE) {
                $range_obj = $carrier->getRangeObject($carrier->shipping_method);
                $price_by_range = [];
            } else {
                $range_obj = $carrier->getRangeObject();
                $price_by_range = $this->getPricesPerShop($range_table, (int)$carrier->id, $shopId, $groupId);
            }
            foreach ($price_by_range as $price) {
                $tpl_vars['price_by_range'][$price['id_'.$range_table]][$price['id_zone']] = $price['price'];
            }

            $tmp_range = $range_obj->getRanges((int)$carrier->id);
            $tpl_vars['ranges'] = [];
            if ($shipping_method != Carrier::SHIPPING_METHOD_FREE) {
                foreach ($tmp_range as $id => $range) {
                    $tpl_vars['ranges'][$range['id_'.$range_table]] = $range;
                    $tpl_vars['ranges'][$range['id_'.$range_table]]['id_range'] = $range['id_'.$range_table];
                }
            }
            // init blank range
            if (!count($tpl_vars['ranges'])) {
                $tpl_vars['ranges'][] = ['id_range' => 0, 'delimiter1' => 0, 'delimiter2' => 0];
            }
        } else {
            parent::getTplRangesVarsAndValues($carrier, $tpl_vars, $fields_value); // TODO: Change the autogenerated stub
        }
    }

    /**
     * get prices per shop
     */
    public function getPricesPerShop($range_table, $id_carrier, $id_shop = null, $id_shop_group = null)
    {
        if ($id_shop !== null) {
            $where = ' AND d.id_shop = ' . $id_shop;
        } elseif ($id_shop_group) {
            $where = ' AND d.id_shop_group = ' . $id_shop_group;
        } else {
            $where = ' AND d.id_shop_group IS NULL AND d.id_shop IS NULL ';
        }

        $sql = 'SELECT d.`id_'.bqSQL($range_table).'`, d.id_carrier, d.id_zone, d.price
				FROM '._DB_PREFIX_.'delivery d
				LEFT JOIN `'._DB_PREFIX_.bqSQL($range_table).'` r ON r.`id_'.bqSQL($range_table).'` = d.`id_'.bqSQL($range_table).'`
				WHERE d.id_carrier = '.(int)$id_carrier.'
					AND d.`id_'.bqSQL($range_table).'` IS NOT NULL
					AND d.`id_'.bqSQL($range_table).'` != 0'
            . $where .
            ' ORDER BY r.delimiter1';
        return Db::getInstance()->executeS($sql);
    }



    public function setMedia()
    {
        parent::setMedia();
        $this->addJs(_PS_MODULE_DIR_ . 'dpdconnect' . DS . 'views' . DS . 'js' . DS . 'dpd_carrier_wizard.js');
    }

    public function renderView()
    {

        $carrier = new Carrier(Tools::getValue('id_carrier'));
        if ($this->dpdCarrier->checkIfCarrierIsDpd($carrier->id_reference) && Shop::isFeatureActive()) {
            $range_table = $carrier->getRangeTable();
            if (empty(Carrier::getDeliveryPriceByRanges($range_table, (int)$carrier->id) || empty(Context::getContext()->cookie->shopContext))) {
                $error = '<div class="alert alert-danger">Er is geen standaard kosten ingesteld, vul deze in als u dit voor alle shops in wilt stellen.</div>';
            } else {
                $error = '';
            }
        }
        return  $error . parent::renderView(); // TODO: Change the autogenerated stub
    }

    public function renderStepThree($carrier)
    {
        list($shopId, $groupId) = $this->getShopOrGroupId($carrier);


        $output = parent::renderStepThree($carrier);
        if ($this->dpdCarrier->checkIfCarrierIsDpd($carrier->id_reference) && Shop::isFeatureActive()) {
            if (empty($this->getPricesPerShop($carrier->getRangeTable(), $carrier->id, $shopId, $groupId))) {
                $checked = 'checked';
                $display = 'style="display:none"';
            }
            if (!empty($shopId) || !empty($groupId)) {
                $checkHTML = '<form type="post"> <input value="1" class="standard-value" name="standard-value" ' . $checked . ' type="checkbox" /> Standaard waarde <br /> </form>';
                $output = $checkHTML  . '<div class="step-3-form" '. $display .'>' . $output . '</div>';
            }
        }
        return $output ;
    }

    public function processRanges($id_carrier, $shopId = null, $groupId = null, $free = false)
    {
        $carrier = new Carrier($id_carrier);
        if ($this->dpdCarrier->checkIfCarrierIsDpd($carrier->id_reference) && Shop::isFeatureActive()) {
            if (!$this->tabAccess['edit'] || !$this->tabAccess['add']) {
                $this->errors[] = Tools::displayError('You do not have permission to use this wizard.');
                return;
            }

            if (!Validate::isLoadedObject($carrier)) {
                return false;
            }
            $range_inf = Tools::getValue('range_inf');
            $range_sup = Tools::getValue('range_sup');
            $range_type = Tools::getValue('shipping_method');

            $fees = Tools::getValue('fees');
            $carrier->deleteDeliveryPrice($carrier->getRangeTable());
            if ($range_type != Carrier::SHIPPING_METHOD_FREE) {
                foreach ($range_inf as $key => $delimiter1) {
                    if (!isset($range_sup[$key])) {
                        continue;
                    }
                    $add_range = true;
                    if ($range_type == Carrier::SHIPPING_METHOD_WEIGHT) {
                        if (!RangeWeight::rangeExist(null, (float)$delimiter1, (float)$range_sup[$key], $carrier->id_reference)) {
                            $range = new RangeWeight();
                        } else {
                            $range = new RangeWeight((int)$key);
                            $range->id_carrier = (int)$carrier->id;
                            $range->save();
                            $add_range = false;
                        }
                    }

                    if ($range_type == Carrier::SHIPPING_METHOD_PRICE) {
                        if (!RangePrice::rangeExist(null, (float)$delimiter1, (float)$range_sup[$key], $carrier->id_reference)) {
                            $range = new RangePrice();
                        } else {
                            $range = new RangePrice((int)$key);
                            $range->id_carrier = (int)$carrier->id;
                            $range->save();
                            $add_range = false;
                        }
                    }
                    if ($add_range) {
                        $range->id_carrier = (int)$carrier->id;
                        $range->delimiter1 = (float)$delimiter1;
                        $range->delimiter2 = (float)$range_sup[$key];
                        $range->save();
                    }

                    if (!Validate::isLoadedObject($range)) {
                        return false;
                    }

                    $price_list = [];
                    if (is_array($fees) && count($fees)) {
                        foreach ($fees as $id_zone => $fee) {
                            if ($free) {
                                unset($fee[$key]);
                            }
                            $price_list[] = [
                                'id_range_price' => ($range_type == Carrier::SHIPPING_METHOD_PRICE ? (int)$range->id : null),
                                'id_range_weight' => ($range_type == Carrier::SHIPPING_METHOD_WEIGHT ? (int)$range->id : null),
                                'id_carrier' => (int)$carrier->id,
                                'id_zone' => (int)$id_zone,
                                'price' => isset($fee[$key]) ? (float)str_replace(',', '.', $fee[$key]) : 0,
                                'id_shop' => $shopId,
                                'id_shop_group' => $groupId,
                            ];
                        }
                    }

                    if (count($price_list) && !$carrier->addDeliveryPrice($price_list, true)) {
                        return false;
                    }
                }
            }
            return true;
        } else {
            return parent::processRanges($id_carrier);
        }
    }

    public function ajaxProcessFinishStep()
    {
        $carrier = new Carrier((int)Tools::getValue('id_carrier'));
        // own code
        list($shopId, $groupId) = $this->getShopOrGroupId($carrier);

        unset($this->errors[0]);
        $return = ['has_error' => false];
        if ($this->dpdCarrier->ifHasSameReferenceId(Tools::getValue('id_carrier'), Configuration::get('dpdconnect_saturday'))
            || $this->dpdCarrier->ifHasSameReferenceId(Tools::getValue('id_carrier'), Configuration::get('dpdconnect_classic_saturday'))  ) {
            if (Tools::getValue('showfromday') === '') {
                $return['has_error'] = true;
                $return['errors'][] = $this->l('Show from day is not set!');
            } elseif (Tools::getValue('showfromtime') == '') {
                $return['has_error'] = true;
                $return['errors'][] = $this->l('Show from time is not set!');
            } elseif (Tools::getValue('showtillday') === '') {
                $return['has_error'] = true;
                $return['errors'][] = $this->l('Show till day is not set!');
            } elseif (Tools::getValue('showtilltime') == '') {
                $return['has_error'] = true;
                $return['errors'][] = $this->l('Show till time is not set!');
            } else {
                Configuration::updateValue('dpdconnect_saturday_showfromday', Tools::getValue('showfromday'));
                Configuration::updateValue('dpdconnect_saturday_showfromtime', Tools::getValue('showfromtime'));
                Configuration::updateValue('dpdconnect_saturday_showtillday', Tools::getValue('showtillday'));
                Configuration::updateValue('dpdconnect_saturday_showtilltime', Tools::getValue('showtilltime'));
            }
        }

        if (!$this->tabAccess['edit']) {
            $return = [
                'has_error' =>  true,
                $return['errors'][] = Tools::displayError('You do not have permission to use this wizard.')
            ];
        } else {
            if ((!isset($shopId) || !isset($groupId) ) && !(boolean)Tools::getValue('standard-value')) {
                $this->validateForm(false);
                if ($id_carrier = Tools::getValue('id_carrier')) {
                    $current_carrier = new Carrier((int)$id_carrier);
                    $new_carrier = $current_carrier->duplicateObject();
                    if (Validate::isLoadedObject($new_carrier)) {
                        $current_carrier->deleted = true;
                        $current_carrier->update();
                        $this->copyFromPost($new_carrier, $this->table);
                        $new_carrier->position = $current_carrier->position;
                        $new_carrier->update();
                        $this->updateAssoShop((int)$new_carrier->id);
                        $this->duplicateLogo((int)$new_carrier->id, (int)$current_carrier->id);
                        $this->changeGroups((int)$new_carrier->id);
                        if (Configuration::get('PS_CARRIER_DEFAULT') == $current_carrier->id) {
                            Configuration::updateValue('PS_CARRIER_DEFAULT', (int)$new_carrier->id);
                        }
                        Hook::exec('actionCarrierUpdate', [
                            'id_carrier' => (int)$current_carrier->id,
                            'carrier' => $new_carrier
                        ]);
                        $this->postImage($new_carrier->id);
                        $this->changeZones($new_carrier->id);
                        $new_carrier->setTaxRulesGroup((int)Tools::getValue('id_tax_rules_group'));
                        $carrier = $new_carrier;
                    }
                } else {
                    $carrier = new Carrier();
                    $this->copyFromPost($carrier, $this->table);
                    if (!$carrier->add()) {
                        $return['has_error'] = true;
                        $return['errors'][] = $this->l('An error occurred while saving this carrier.');
                    }
                }
                if ($carrier->is_free) {
                    if ($this->dpdCarrier->checkIfCarrierIsDpd($carrier->id_reference) && Shop::isFeatureActive()) {
                        $this->processRanges((int)$carrier->id, $shopId, $groupId, true);
                    } else {
                        $carrier->deleteDeliveryPrice('range_weight');
                        $carrier->deleteDeliveryPrice('range_price');
                    }
                }
                if (Validate::isLoadedObject($carrier)) {
                    if (!$this->changeGroups((int)$carrier->id)) {
                        $return['has_error'] = true;
                        $return['errors'][] = $this->l('An error occurred while saving carrier groups.');
                    }
                    if (!$this->changeZones((int)$carrier->id)) {
                        $return['has_error'] = true;
                        $return['errors'][] = $this->l('An error occurred while saving carrier zones.');
                    }
                    if (!$carrier->is_free) {
                        if (!$this->processRanges((int)$carrier->id, $shopId, $groupId)) {
                            $return['has_error'] = true;
                            $return['errors'][] = $this->l('An error occurred while saving carrier ranges.');
                        }
                    }
                    if (Shop::isFeatureActive() && !$this->updateAssoShop((int)$carrier->id)) {
                        $return['has_error'] = true;
                        $return['errors'][] = $this->l('An error occurred while saving associations of shops.');
                    }
                    if (!$carrier->setTaxRulesGroup((int)Tools::getValue('id_tax_rules_group'))) {
                        $return['has_error'] = true;
                        $return['errors'][] = $this->l('An error occurred while saving the tax rules group.');
                    }
                    if (Tools::getValue('logo')) {
                        if (Tools::getValue('logo') == 'null' && file_exists(_PS_SHIP_IMG_DIR_ . $carrier->id . '.jpg')) {
                            unlink(_PS_SHIP_IMG_DIR_ . $carrier->id . '.jpg');
                        } else {
                            $logo = basename(Tools::getValue('logo'));
                            if (!file_exists(_PS_TMP_IMG_DIR_ . $logo) || !copy(_PS_TMP_IMG_DIR_ . $logo, _PS_SHIP_IMG_DIR_ . $carrier->id . '.jpg')) {
                                $return['has_error'] = true;
                                $return['errors'][] = $this->l('An error occurred while saving carrier logo.');
                            }
                        }
                    }
                    $return['id_carrier'] = $carrier->id;
                }
            } elseif (Tools::getValue('standard-value')) {
                if ($shopId !== null) {
                    $where = ' AND id_shop =' . $shopId;
                } elseif ($groupId !== null) {
                    $where = ' AND id_shop_group = '. $groupId;
                }
                DB::getInstance()->delete('delivery', 'id_carrier =' . $carrier->id . $where);
            }
            if ($this->dpdCarrier->checkIfCarrierIsDpd($carrier->id_reference) && Shop::isFeatureActive()) {
                $this->updateOtherDelivery($current_carrier->id, $new_carrier->id, $shopId, $groupId);
            }
        }
        die(Tools::jsonEncode($return));
    }

    public function updateOtherDelivery($current_carrier_id, $new_carrier_id, $id_shop = null, $id_shop_group = null)
    {
        if ($id_shop !== null) {
            $where = '(id_shop != ' .$id_shop . ' OR id_shop IS NULL)';
        } elseif ($id_shop_group !== null) {
            $where = ' (id_shop_group != ' . $id_shop_group . ' OR id_shop_group IS NULL)';
        } elseif ($id_shop_group == null && $id_shop == null) {
            $where = '(id_shop_group IS NOT NULL OR id_shop  IS NOT NULL )';
        }
        Db::getInstance()->update('delivery', ['id_carrier' => pSQL($new_carrier_id)], ' id_carrier = ' .  pSQL($current_carrier_id)  . ' AND' . $where);
    }

    public function getShopOrGroupId($carrier)
    {
        if ($this->dpdCarrier->checkIfCarrierIsDpd($carrier->id_reference) && Shop::isFeatureActive()) {
            $shopOrGroupid = Context::getContext()->cookie->shopContext;
            $shopContext = explode('-', $shopOrGroupid);
            if ($shopContext[0] == 'g') {
                $groupId = $shopContext[1];
            } elseif ($shopContext[0] == 's') {
                $shopId = $shopContext[1];
            }
        }

        return [$shopId, $groupId];
    }
}
