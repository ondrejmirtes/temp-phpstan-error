1. stop all users cron on LMS
2. Export tables from emerald, mysqldump -h10.116.40.39 -ulms2backup@azeus2telehealthmysql02 -pBaKm3Ap! emerald_wh dim_user dim_user_hr_snapshot > user_20220328.sql
3. Restore locally
4. in emerald_wh add missing fields to dim_user
ALTER TABLE emerald_wh.`dim_user` ADD `active_dim_user_hr_snapshot_id` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `rbac_user_id`;

5. clean LOCAL lms tables
truncate lms4_reports_staging.dim_user;
truncate lms4_reports_staging.dim_user_hr_snapshot;

5.A temporary add fields pmid,sam_account_id AFTER medstar_employee_id

6. temporary disable trigger locally
7. Copy data into lms4 from emerald

insert into lms4_reports_staging.`dim_user`
SELECT * FROM emerald_wh.`dim_user` order by rbac_user_id ASC;

insert into lms4_reports_staging.`dim_user_hr_snapshot`
SELECT * FROM emerald_wh.`dim_user_hr_snapshot` order by id ASC;

8. Map active snapshot in dim_user
UPDATE lms4_reports_staging.dim_user A JOIN lms4_reports_staging.dim_user_hr_snapshot B ON A.rbac_user_id = B.rbac_user_id
SET A.active_dim_user_hr_snapshot_id = B.id
WHERE B.is_active_snapshot = 1  

9. DROP fields pmid,sam_account_id

9a. UPDATE lms4_reports_staging.`dim_user` SET `is_update` = '1'

9b. export JUST data of these two tables and copy to sambarel
scp dim_user* itay@release.samba42.club:.

10. in production truncate 
truncate lms4_reports_staging.dim_user;
truncate lms4_reports_staging.dim_user_hr_snapshot;
truncate lms4_reports_staging.log_lms3users_user_snapshot_changed

11. Find the min hr event to process
select * from user_snapshot_changed where status='enabled' order by id asc limit 1\G
and put it in the app table
update lms4_reports_staging.`app_user_hr_snapshot` set `last_id_user_snapshot_changed`= ?????

12. Restore snapshot table first 
13. restore dim_user
14. activate crons
