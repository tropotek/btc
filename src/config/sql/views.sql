-- --------------------------------------------
-- @version All
-- @author: Michael Mifsud <http://www.tropotek.com/>
--
-- This file should be execute on all upgrades/updates, etc
--
-- --------------------------------------------

CREATE OR REPLACE ALGORITHM=MERGE VIEW v_asset_tick AS
SELECT
    a.id,
    a.user_id,
    a.asset_id,
    IFNULL(a2.category_id, 0) AS 'category_id',
    a.units,
    a.currency,
    a.bid,
    a.ask,
    (a.units * a.bid) AS 'bid_total',
    (a.units * a.ask) AS 'ask_total',
    IFNULL(a2.in_total, 0) AS 'in_total',           -- Include tick in totals calc
    a.created
FROM `asset_tick` a LEFT JOIN asset a2 ON (a.asset_id = a2.id)
;





# SELECT a.*
# FROM v_asset_tick a
# WHERE asset_id
# ORDER BY a.`created` DESC
# ;

SELECT a.user_id, currency, SUM(a.bid_total) as 'bid_total', SUM(a.ask_total) as 'ask_total', created
FROM v_asset_tick a
WHERE a.asset_id
    AND a.user_id = '2'
    AND a.in_total
GROUP BY created, currency
ORDER BY a.`created` DESC
;


