-- --------------------------------------------
-- @version All
-- @author: Michael Mifsud <http://www.tropotek.com/>
-- --------------------------------------------

CREATE OR REPLACE ALGORITHM=MERGE VIEW v_asset_tick AS
SELECT
    a.id,
    a.user_id,
    a.asset_id,
    a.units,
    a.currency,
    a.bid,
    a.ask,
    (a.units * a.bid) AS bid_total,
    (a.units * a.ask) AS ask_total,
    a.created,
    IFNULL(a2.in_total, 0) AS in_total,           -- Include tick in totals calc
    IFNULL(a2.category_id, 0) AS category_id
FROM asset_tick a
     LEFT JOIN asset a2 ON (a.asset_id = a2.id)
WHERE a2.id IS NOT NULL
;

CREATE OR REPLACE ALGORITHM=MERGE VIEW v_tick_totals AS
SELECT
    a.id,
    a.user_id,
    0 AS asset_id,
    0 AS units,
    a.currency,
    SUM(a.bid_total) AS bid,
    SUM(a.ask_total) AS ask,
    a.created,
    a.in_total,
    a.category_id
FROM v_asset_tick a
WHERE a.in_total = 1
GROUP BY user_id, created
;

