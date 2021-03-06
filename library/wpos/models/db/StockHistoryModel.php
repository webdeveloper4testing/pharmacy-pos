<?php
/**
 * StockHistoryModel is part of Wallace Point of Sale system (WPOS) API
 *
 * StockHistoryModel extends the DbConfig PDO class to interact with the config DB table
 *
 * WallacePOS is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3.0 of the License, or (at your option) any later version.
 *
 * WallacePOS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details:
 * <https://www.gnu.org/licenses/lgpl.html>
 *
 * @package    wpos
 * @copyright  Copyright (c) 2014 WallaceIT. (https://wallaceit.com.au)

 * @link       https://wallacepos.com
 * @author     Michael B Wallace <micwallace@gmx.com>
 * @since      File available since 24/05/14 4:13 PM
 */
class StockHistoryModel extends DbConfig
{

    /**
     * @var array of available columns
     */
    protected $_columns = ['id', 'storeditemid', 'locationid', 'auxid', 'auxdirection', 'type', 'amount', 'dt'];

    /**
     * Init the DB
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param $stockitemid
     * @param $locationid
     * @param $type
     * @param $amount
     * @param $auxid
     * @param int $direction
     * @return bool|string Returns false on an unexpected failure, returns -1 if a unique constraint in the database fails, or the new rows id if the insert is successful
     */
    public function create($stockitemid, $locationid, $type, $amount, $auxid=-1, $direction=0){
        $sql = "INSERT INTO stock_history (stockitemid, locationid, auxid, auxdir, type, amount) VALUES (:stockitemid, :locationid, :auxid, :auxdir, :type, :amount);";
        $placeholders = [":stockitemid"=>$stockitemid, ":locationid"=>$locationid, ":auxid"=>$auxid, ":auxdir"=>$direction, ":type"=>$type, ":amount"=>$amount];

        return $this->insert($sql, $placeholders);
    }

    /**
     * @param bool $stockitemid
     * @param bool $locationid
     * @return array|bool Returns an array of results on success, false on failure
     */
    public function get($stockitemid = false, $locationid = false){
        $sql = "SELECT h.*, item.name as name, COALESCE(l.name, 'Warehouse') as location FROM stock_history as h LEFT JOIN stock_items as i ON h.stockitemid=i.id LEFT JOIN locations as l ON h.locationid=l.id LEFT JOIN stock_inventory AS inv ON i.stockinventoryid=inv.id LEFT JOIN stored_items AS item ON inv.storeditemid=item.id";
        $placeholders = [];
        if ($stockitemid !== false) {
            if (empty($placeholders)) {
                $sql .= ' WHERE';
            }
            $sql .= ' h.stockitemid = :stockitemid';
            $placeholders[':stockitemid'] = $stockitemid;
        }
        if ($locationid !== false) {
            if (empty($placeholders)) {
                $sql .= ' WHERE';
            } else {
                $sql .= ' AND';
            }
            $sql .= ' h.locationid = :locationid';
            $placeholders[':locationid'] = $locationid;
        }

        return $this->select($sql, $placeholders);
    }

    /**
     * Remove stock history using item ID
     * @param $itemid
     * @return bool|int Returns false on failure or the number of rows affected
     */
    public function removeByItemId($itemid){
        if ($itemid === null) {
            return false;
        }
        $sql = "DELETE FROM stock_history WHERE itemid=:itemid;";
        $placeholders = [":itemid"=>$itemid];

        return $this->delete($sql, $placeholders);
    }

    /**
     * Remove stock history using location ID
     * @param $locationid
     * @return bool|int Returns false on failure or the number of rows affected
     */
    public function removeByLocationId($locationid){
        if ($locationid === null) {
            return false;
        }
        $sql          = "DELETE FROM stock_history WHERE locationid=:locationid;";
        $placeholders = [":locationid"=>$locationid];

        return $this->delete($sql, $placeholders);
    }
}