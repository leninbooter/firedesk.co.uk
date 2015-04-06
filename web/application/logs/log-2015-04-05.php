<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

DEBUG - 2015-04-05 00:10:39 --> Config Class Initialized
DEBUG - 2015-04-05 00:10:39 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:10:39 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:10:39 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:10:39 --> URI Class Initialized
DEBUG - 2015-04-05 00:10:39 --> Router Class Initialized
DEBUG - 2015-04-05 00:10:39 --> No URI present. Default controller set.
DEBUG - 2015-04-05 00:10:39 --> Output Class Initialized
DEBUG - 2015-04-05 00:10:39 --> Security Class Initialized
DEBUG - 2015-04-05 00:10:39 --> Input Class Initialized
DEBUG - 2015-04-05 00:10:39 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:10:39 --> Language Class Initialized
DEBUG - 2015-04-05 00:10:39 --> Loader Class Initialized
DEBUG - 2015-04-05 00:10:39 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:10:39 --> Controller Class Initialized
DEBUG - 2015-04-05 00:10:39 --> File loaded: ../application/views/login.php
DEBUG - 2015-04-05 00:10:39 --> Final output sent to browser
DEBUG - 2015-04-05 00:10:39 --> Total execution time: 0.0330
DEBUG - 2015-04-05 00:10:48 --> Config Class Initialized
DEBUG - 2015-04-05 00:10:48 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:10:48 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:10:48 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:10:48 --> URI Class Initialized
DEBUG - 2015-04-05 00:10:48 --> Router Class Initialized
DEBUG - 2015-04-05 00:10:48 --> Output Class Initialized
DEBUG - 2015-04-05 00:10:48 --> Security Class Initialized
DEBUG - 2015-04-05 00:10:48 --> Input Class Initialized
DEBUG - 2015-04-05 00:10:48 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:10:48 --> Language Class Initialized
DEBUG - 2015-04-05 00:10:48 --> Loader Class Initialized
DEBUG - 2015-04-05 00:10:48 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:10:48 --> Controller Class Initialized
DEBUG - 2015-04-05 00:10:48 --> XSS Filtering completed
DEBUG - 2015-04-05 00:10:48 --> XSS Filtering completed
DEBUG - 2015-04-05 00:10:48 --> Model Class Initialized
DEBUG - 2015-04-05 00:10:48 --> Model Class Initialized
DEBUG - 2015-04-05 00:10:48 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:10:48 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:10:49 --> select 
				cr.pk_id, 
				cr.username,
				cr.password,
				p.pk_id as profile_id,
				p.name as profile_name,
				co.pk_id as company_id,
				co.name as company_name,
				co.db,
				co.db_user,
				co.db_pwd,
				co.db_host
				from credentials as cr
				inner join profiles as p on p.pk_id = cr.fk_profile_id
				left join companies as co on co.pk_id = cr.fk_company_id
				where cr.username =  'alvin@alvin.com'
DEBUG - 2015-04-05 00:10:49 --> Nativesession class already loaded. Second attempt ignored.
DEBUG - 2015-04-05 00:10:49 --> Model Class Initialized
DEBUG - 2015-04-05 00:10:49 --> hostname=localhost
DEBUG - 2015-04-05 00:10:49 --> username=root
DEBUG - 2015-04-05 00:10:49 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:10:49 --> database=cl51-democompa
DEBUG - 2015-04-05 00:10:49 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:10:49 --> dbprefix=
DEBUG - 2015-04-05 00:10:49 --> pconnect=1
DEBUG - 2015-04-05 00:10:49 --> db_debug=1
DEBUG - 2015-04-05 00:10:49 --> cache_on=
DEBUG - 2015-04-05 00:10:49 --> cachedir=
DEBUG - 2015-04-05 00:10:49 --> char_set=utf8
DEBUG - 2015-04-05 00:10:49 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:10:49 --> swap_pre=
DEBUG - 2015-04-05 00:10:49 --> autoinit=1
DEBUG - 2015-04-05 00:10:49 --> stricton=
DEBUG - 2015-04-05 00:10:49 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:10:50 --> Final output sent to browser
DEBUG - 2015-04-05 00:10:50 --> Total execution time: 2.3151
DEBUG - 2015-04-05 00:10:50 --> Config Class Initialized
DEBUG - 2015-04-05 00:10:50 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:10:50 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:10:50 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:10:50 --> URI Class Initialized
DEBUG - 2015-04-05 00:10:50 --> Router Class Initialized
DEBUG - 2015-04-05 00:10:50 --> Output Class Initialized
DEBUG - 2015-04-05 00:10:50 --> Security Class Initialized
DEBUG - 2015-04-05 00:10:50 --> Input Class Initialized
DEBUG - 2015-04-05 00:10:50 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:10:51 --> Language Class Initialized
DEBUG - 2015-04-05 00:10:51 --> Loader Class Initialized
DEBUG - 2015-04-05 00:10:51 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:10:51 --> Controller Class Initialized
DEBUG - 2015-04-05 00:10:51 --> File loaded: ../application/views/header_nav.php
DEBUG - 2015-04-05 00:10:51 --> File loaded: ../application/views/desk.php
DEBUG - 2015-04-05 00:10:51 --> File loaded: ../application/views/footer_copyright.php
DEBUG - 2015-04-05 00:10:51 --> File loaded: ../application/views/footer_common.php
DEBUG - 2015-04-05 00:10:51 --> File loaded: ../application/views/footer.php
DEBUG - 2015-04-05 00:10:51 --> Final output sent to browser
DEBUG - 2015-04-05 00:10:51 --> Total execution time: 0.0370
DEBUG - 2015-04-05 00:10:52 --> Config Class Initialized
DEBUG - 2015-04-05 00:10:52 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:10:52 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:10:52 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:10:52 --> Config Class Initialized
DEBUG - 2015-04-05 00:10:52 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:10:52 --> URI Class Initialized
DEBUG - 2015-04-05 00:10:52 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:10:52 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:10:52 --> URI Class Initialized
DEBUG - 2015-04-05 00:10:52 --> Router Class Initialized
DEBUG - 2015-04-05 00:10:52 --> Output Class Initialized
DEBUG - 2015-04-05 00:10:52 --> Security Class Initialized
DEBUG - 2015-04-05 00:10:52 --> Input Class Initialized
DEBUG - 2015-04-05 00:10:52 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:10:52 --> Language Class Initialized
DEBUG - 2015-04-05 00:10:52 --> Loader Class Initialized
DEBUG - 2015-04-05 00:10:52 --> Router Class Initialized
DEBUG - 2015-04-05 00:10:52 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:10:52 --> Output Class Initialized
DEBUG - 2015-04-05 00:10:52 --> Security Class Initialized
DEBUG - 2015-04-05 00:10:52 --> Controller Class Initialized
DEBUG - 2015-04-05 00:10:52 --> Input Class Initialized
DEBUG - 2015-04-05 00:10:52 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:10:52 --> Language Class Initialized
DEBUG - 2015-04-05 00:10:52 --> Config Class Initialized
DEBUG - 2015-04-05 00:10:52 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:10:52 --> Loader Class Initialized
DEBUG - 2015-04-05 00:10:52 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:10:52 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:10:52 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:10:52 --> URI Class Initialized
DEBUG - 2015-04-05 00:10:52 --> Router Class Initialized
DEBUG - 2015-04-05 00:10:52 --> Output Class Initialized
DEBUG - 2015-04-05 00:10:52 --> Security Class Initialized
DEBUG - 2015-04-05 00:10:52 --> Input Class Initialized
DEBUG - 2015-04-05 00:10:52 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:10:52 --> Language Class Initialized
DEBUG - 2015-04-05 00:10:52 --> Loader Class Initialized
DEBUG - 2015-04-05 00:10:52 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:10:52 --> Model Class Initialized
DEBUG - 2015-04-05 00:10:52 --> Model Class Initialized
DEBUG - 2015-04-05 00:10:52 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:10:52 --> hostname=localhost
DEBUG - 2015-04-05 00:10:52 --> username=root
DEBUG - 2015-04-05 00:10:52 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:10:52 --> database=cl51-democompa
DEBUG - 2015-04-05 00:10:52 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:10:52 --> dbprefix=
DEBUG - 2015-04-05 00:10:52 --> pconnect=1
DEBUG - 2015-04-05 00:10:52 --> db_debug=1
DEBUG - 2015-04-05 00:10:52 --> cache_on=
DEBUG - 2015-04-05 00:10:52 --> cachedir=
DEBUG - 2015-04-05 00:10:52 --> char_set=utf8
DEBUG - 2015-04-05 00:10:52 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:10:52 --> swap_pre=
DEBUG - 2015-04-05 00:10:52 --> autoinit=1
DEBUG - 2015-04-05 00:10:52 --> stricton=
DEBUG - 2015-04-05 00:10:52 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:10:53 --> select mm.pk_id from messenger_messages as mm
					inner join messenger_users_messages as mum on mum.fk_message_id = mm.pk_id 
					and mum.`read` = 0 
					and mm.fk_destination_user = 1
					limit 1;
DEBUG - 2015-04-05 00:10:53 --> Final output sent to browser
DEBUG - 2015-04-05 00:10:53 --> Total execution time: 1.1151
DEBUG - 2015-04-05 00:10:53 --> Controller Class Initialized
DEBUG - 2015-04-05 00:10:53 --> Model Class Initialized
DEBUG - 2015-04-05 00:10:53 --> Model Class Initialized
DEBUG - 2015-04-05 00:10:53 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:10:53 --> hostname=localhost
DEBUG - 2015-04-05 00:10:53 --> username=root
DEBUG - 2015-04-05 00:10:53 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:10:53 --> database=cl51-democompa
DEBUG - 2015-04-05 00:10:53 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:10:53 --> dbprefix=
DEBUG - 2015-04-05 00:10:53 --> pconnect=1
DEBUG - 2015-04-05 00:10:53 --> db_debug=1
DEBUG - 2015-04-05 00:10:53 --> cache_on=
DEBUG - 2015-04-05 00:10:53 --> cachedir=
DEBUG - 2015-04-05 00:10:53 --> char_set=utf8
DEBUG - 2015-04-05 00:10:53 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:10:53 --> swap_pre=
DEBUG - 2015-04-05 00:10:53 --> autoinit=1
DEBUG - 2015-04-05 00:10:53 --> stricton=
DEBUG - 2015-04-05 00:10:53 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:10:54 --> Final output sent to browser
DEBUG - 2015-04-05 00:10:54 --> Total execution time: 2.1791
DEBUG - 2015-04-05 00:10:54 --> Controller Class Initialized
DEBUG - 2015-04-05 00:10:54 --> Helper loaded: custom_validations_helper
DEBUG - 2015-04-05 00:10:54 --> Model Class Initialized
DEBUG - 2015-04-05 00:10:54 --> Model Class Initialized
DEBUG - 2015-04-05 00:10:54 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:10:54 --> hostname=localhost
DEBUG - 2015-04-05 00:10:54 --> username=root
DEBUG - 2015-04-05 00:10:54 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:10:54 --> database=cl51-democompa
DEBUG - 2015-04-05 00:10:54 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:10:54 --> dbprefix=
DEBUG - 2015-04-05 00:10:54 --> pconnect=1
DEBUG - 2015-04-05 00:10:54 --> db_debug=1
DEBUG - 2015-04-05 00:10:54 --> cache_on=
DEBUG - 2015-04-05 00:10:54 --> cachedir=
DEBUG - 2015-04-05 00:10:54 --> char_set=utf8
DEBUG - 2015-04-05 00:10:54 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:10:54 --> swap_pre=
DEBUG - 2015-04-05 00:10:54 --> autoinit=1
DEBUG - 2015-04-05 00:10:54 --> stricton=
DEBUG - 2015-04-05 00:10:54 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:10:55 --> XSS Filtering completed
DEBUG - 2015-04-05 00:10:55 --> Final output sent to browser
DEBUG - 2015-04-05 00:10:55 --> Total execution time: 3.1762
DEBUG - 2015-04-05 00:12:57 --> Config Class Initialized
DEBUG - 2015-04-05 00:12:57 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:12:57 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:12:57 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:12:57 --> URI Class Initialized
DEBUG - 2015-04-05 00:12:57 --> Router Class Initialized
DEBUG - 2015-04-05 00:12:57 --> Output Class Initialized
DEBUG - 2015-04-05 00:12:57 --> Security Class Initialized
DEBUG - 2015-04-05 00:12:57 --> Input Class Initialized
DEBUG - 2015-04-05 00:12:57 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:12:57 --> Language Class Initialized
DEBUG - 2015-04-05 00:12:57 --> Loader Class Initialized
DEBUG - 2015-04-05 00:12:57 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:12:57 --> Controller Class Initialized
DEBUG - 2015-04-05 00:12:57 --> File loaded: ../application/views/header_nav.php
DEBUG - 2015-04-05 00:12:57 --> File loaded: ../application/views/hire_stock_fleet_records.php
DEBUG - 2015-04-05 00:12:57 --> File loaded: ../application/views/footer_common.php
DEBUG - 2015-04-05 00:12:57 --> File loaded: ../application/views/footer_copyright.php
DEBUG - 2015-04-05 00:12:57 --> File loaded: ../application/views/footer.php
DEBUG - 2015-04-05 00:12:57 --> Final output sent to browser
DEBUG - 2015-04-05 00:12:57 --> Total execution time: 0.0760
DEBUG - 2015-04-05 00:12:58 --> Config Class Initialized
DEBUG - 2015-04-05 00:12:58 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:12:58 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:12:58 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:12:58 --> URI Class Initialized
DEBUG - 2015-04-05 00:12:58 --> Router Class Initialized
DEBUG - 2015-04-05 00:12:58 --> Config Class Initialized
DEBUG - 2015-04-05 00:12:58 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:12:58 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:12:58 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:12:58 --> Output Class Initialized
DEBUG - 2015-04-05 00:12:58 --> URI Class Initialized
DEBUG - 2015-04-05 00:12:58 --> Security Class Initialized
DEBUG - 2015-04-05 00:12:58 --> Router Class Initialized
DEBUG - 2015-04-05 00:12:58 --> Input Class Initialized
DEBUG - 2015-04-05 00:12:58 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:12:58 --> Output Class Initialized
DEBUG - 2015-04-05 00:12:58 --> Security Class Initialized
DEBUG - 2015-04-05 00:12:58 --> Input Class Initialized
DEBUG - 2015-04-05 00:12:58 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:12:58 --> Language Class Initialized
DEBUG - 2015-04-05 00:12:58 --> Language Class Initialized
DEBUG - 2015-04-05 00:12:58 --> Loader Class Initialized
DEBUG - 2015-04-05 00:12:58 --> Loader Class Initialized
DEBUG - 2015-04-05 00:12:58 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:12:58 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:12:58 --> Controller Class Initialized
DEBUG - 2015-04-05 00:12:58 --> Model Class Initialized
DEBUG - 2015-04-05 00:12:58 --> Model Class Initialized
DEBUG - 2015-04-05 00:12:58 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:12:58 --> hostname=localhost
DEBUG - 2015-04-05 00:12:58 --> username=root
DEBUG - 2015-04-05 00:12:58 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:12:58 --> database=cl51-democompa
DEBUG - 2015-04-05 00:12:58 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:12:58 --> dbprefix=
DEBUG - 2015-04-05 00:12:58 --> pconnect=1
DEBUG - 2015-04-05 00:12:58 --> db_debug=1
DEBUG - 2015-04-05 00:12:58 --> cache_on=
DEBUG - 2015-04-05 00:12:58 --> cachedir=
DEBUG - 2015-04-05 00:12:58 --> char_set=utf8
DEBUG - 2015-04-05 00:12:58 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:12:58 --> swap_pre=
DEBUG - 2015-04-05 00:12:58 --> autoinit=1
DEBUG - 2015-04-05 00:12:58 --> stricton=
DEBUG - 2015-04-05 00:12:58 --> Config Class Initialized
DEBUG - 2015-04-05 00:12:58 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:12:58 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:12:58 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:12:58 --> URI Class Initialized
DEBUG - 2015-04-05 00:12:58 --> Router Class Initialized
DEBUG - 2015-04-05 00:12:58 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:12:58 --> Output Class Initialized
DEBUG - 2015-04-05 00:12:58 --> Security Class Initialized
DEBUG - 2015-04-05 00:12:58 --> Input Class Initialized
DEBUG - 2015-04-05 00:12:58 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:12:58 --> Language Class Initialized
DEBUG - 2015-04-05 00:12:58 --> Config Class Initialized
DEBUG - 2015-04-05 00:12:58 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:12:58 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:12:58 --> Loader Class Initialized
DEBUG - 2015-04-05 00:12:58 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:12:58 --> URI Class Initialized
DEBUG - 2015-04-05 00:12:58 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:12:58 --> Router Class Initialized
DEBUG - 2015-04-05 00:12:58 --> Output Class Initialized
DEBUG - 2015-04-05 00:12:58 --> Security Class Initialized
DEBUG - 2015-04-05 00:12:58 --> Input Class Initialized
DEBUG - 2015-04-05 00:12:58 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:12:58 --> Language Class Initialized
DEBUG - 2015-04-05 00:12:58 --> Loader Class Initialized
DEBUG - 2015-04-05 00:12:58 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:12:59 --> Final output sent to browser
DEBUG - 2015-04-05 00:12:59 --> Total execution time: 1.0871
DEBUG - 2015-04-05 00:12:59 --> Controller Class Initialized
DEBUG - 2015-04-05 00:12:59 --> Model Class Initialized
DEBUG - 2015-04-05 00:12:59 --> Model Class Initialized
DEBUG - 2015-04-05 00:12:59 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:12:59 --> hostname=localhost
DEBUG - 2015-04-05 00:12:59 --> username=root
DEBUG - 2015-04-05 00:12:59 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:12:59 --> database=cl51-democompa
DEBUG - 2015-04-05 00:12:59 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:12:59 --> dbprefix=
DEBUG - 2015-04-05 00:12:59 --> pconnect=1
DEBUG - 2015-04-05 00:12:59 --> db_debug=1
DEBUG - 2015-04-05 00:12:59 --> cache_on=
DEBUG - 2015-04-05 00:12:59 --> cachedir=
DEBUG - 2015-04-05 00:12:59 --> char_set=utf8
DEBUG - 2015-04-05 00:12:59 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:12:59 --> swap_pre=
DEBUG - 2015-04-05 00:12:59 --> autoinit=1
DEBUG - 2015-04-05 00:12:59 --> stricton=
DEBUG - 2015-04-05 00:12:59 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:13:00 --> select mm.pk_id from messenger_messages as mm
					inner join messenger_users_messages as mum on mum.fk_message_id = mm.pk_id 
					and mum.`read` = 0 
					and mm.fk_destination_user = 1
					limit 1;
DEBUG - 2015-04-05 00:13:00 --> Final output sent to browser
DEBUG - 2015-04-05 00:13:00 --> Total execution time: 2.1421
DEBUG - 2015-04-05 00:13:00 --> Controller Class Initialized
DEBUG - 2015-04-05 00:13:00 --> Model Class Initialized
DEBUG - 2015-04-05 00:13:00 --> Model Class Initialized
DEBUG - 2015-04-05 00:13:00 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:13:00 --> hostname=localhost
DEBUG - 2015-04-05 00:13:00 --> username=root
DEBUG - 2015-04-05 00:13:00 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:13:00 --> database=cl51-democompa
DEBUG - 2015-04-05 00:13:00 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:13:00 --> dbprefix=
DEBUG - 2015-04-05 00:13:00 --> pconnect=1
DEBUG - 2015-04-05 00:13:00 --> db_debug=1
DEBUG - 2015-04-05 00:13:00 --> cache_on=
DEBUG - 2015-04-05 00:13:00 --> cachedir=
DEBUG - 2015-04-05 00:13:00 --> char_set=utf8
DEBUG - 2015-04-05 00:13:00 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:13:00 --> swap_pre=
DEBUG - 2015-04-05 00:13:00 --> autoinit=1
DEBUG - 2015-04-05 00:13:00 --> stricton=
DEBUG - 2015-04-05 00:13:00 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:13:01 --> Final output sent to browser
DEBUG - 2015-04-05 00:13:01 --> Total execution time: 3.2982
DEBUG - 2015-04-05 00:13:01 --> Controller Class Initialized
DEBUG - 2015-04-05 00:13:01 --> Helper loaded: custom_validations_helper
DEBUG - 2015-04-05 00:13:01 --> Model Class Initialized
DEBUG - 2015-04-05 00:13:01 --> Model Class Initialized
DEBUG - 2015-04-05 00:13:01 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:13:01 --> hostname=localhost
DEBUG - 2015-04-05 00:13:01 --> username=root
DEBUG - 2015-04-05 00:13:01 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:13:01 --> database=cl51-democompa
DEBUG - 2015-04-05 00:13:01 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:13:01 --> dbprefix=
DEBUG - 2015-04-05 00:13:01 --> pconnect=1
DEBUG - 2015-04-05 00:13:01 --> db_debug=1
DEBUG - 2015-04-05 00:13:01 --> cache_on=
DEBUG - 2015-04-05 00:13:01 --> cachedir=
DEBUG - 2015-04-05 00:13:01 --> char_set=utf8
DEBUG - 2015-04-05 00:13:01 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:13:01 --> swap_pre=
DEBUG - 2015-04-05 00:13:01 --> autoinit=1
DEBUG - 2015-04-05 00:13:01 --> stricton=
DEBUG - 2015-04-05 00:13:01 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:13:02 --> XSS Filtering completed
DEBUG - 2015-04-05 00:13:02 --> Final output sent to browser
DEBUG - 2015-04-05 00:13:02 --> Total execution time: 4.3242
DEBUG - 2015-04-05 00:13:18 --> Config Class Initialized
DEBUG - 2015-04-05 00:13:18 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:13:18 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:13:18 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:13:18 --> URI Class Initialized
DEBUG - 2015-04-05 00:13:18 --> Router Class Initialized
DEBUG - 2015-04-05 00:13:18 --> Output Class Initialized
DEBUG - 2015-04-05 00:13:18 --> Security Class Initialized
DEBUG - 2015-04-05 00:13:18 --> Input Class Initialized
DEBUG - 2015-04-05 00:13:18 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:13:18 --> Language Class Initialized
DEBUG - 2015-04-05 00:13:18 --> Loader Class Initialized
DEBUG - 2015-04-05 00:13:18 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:13:18 --> Controller Class Initialized
DEBUG - 2015-04-05 00:13:18 --> Model Class Initialized
DEBUG - 2015-04-05 00:13:18 --> Model Class Initialized
DEBUG - 2015-04-05 00:13:18 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:13:18 --> hostname=localhost
DEBUG - 2015-04-05 00:13:18 --> username=root
DEBUG - 2015-04-05 00:13:18 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:13:18 --> database=cl51-democompa
DEBUG - 2015-04-05 00:13:18 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:13:18 --> dbprefix=
DEBUG - 2015-04-05 00:13:18 --> pconnect=1
DEBUG - 2015-04-05 00:13:18 --> db_debug=1
DEBUG - 2015-04-05 00:13:18 --> cache_on=
DEBUG - 2015-04-05 00:13:18 --> cachedir=
DEBUG - 2015-04-05 00:13:18 --> char_set=utf8
DEBUG - 2015-04-05 00:13:18 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:13:18 --> swap_pre=
DEBUG - 2015-04-05 00:13:18 --> autoinit=1
DEBUG - 2015-04-05 00:13:18 --> stricton=
DEBUG - 2015-04-05 00:13:18 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:13:19 --> SELECT 
				a.pk_id,
				a.message,
				a.creation_date,
				b.`read` as isread,
				c.name
				FROM messenger_messages as a
				inner join messenger_users_messages as b on a.pk_id = b.fk_message_id
				inner join users as c on c.pk_id = a.fk_user_id
				where a.fk_destination_user = 1 order by 3 desc
DEBUG - 2015-04-05 00:13:19 --> File loaded: ../application/views/messenger_inbox_view.php
DEBUG - 2015-04-05 00:13:19 --> Final output sent to browser
DEBUG - 2015-04-05 00:13:19 --> Total execution time: 1.0691
DEBUG - 2015-04-05 00:13:22 --> Config Class Initialized
DEBUG - 2015-04-05 00:13:22 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:13:22 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:13:22 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:13:22 --> URI Class Initialized
DEBUG - 2015-04-05 00:13:22 --> Router Class Initialized
DEBUG - 2015-04-05 00:13:22 --> Output Class Initialized
DEBUG - 2015-04-05 00:13:22 --> Security Class Initialized
DEBUG - 2015-04-05 00:13:22 --> Input Class Initialized
DEBUG - 2015-04-05 00:13:22 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:13:22 --> Language Class Initialized
DEBUG - 2015-04-05 00:13:22 --> Loader Class Initialized
DEBUG - 2015-04-05 00:13:22 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:13:22 --> Controller Class Initialized
DEBUG - 2015-04-05 00:13:22 --> Model Class Initialized
DEBUG - 2015-04-05 00:13:22 --> Model Class Initialized
DEBUG - 2015-04-05 00:13:22 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:13:22 --> hostname=localhost
DEBUG - 2015-04-05 00:13:22 --> username=root
DEBUG - 2015-04-05 00:13:22 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:13:22 --> database=cl51-democompa
DEBUG - 2015-04-05 00:13:22 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:13:22 --> dbprefix=
DEBUG - 2015-04-05 00:13:22 --> pconnect=1
DEBUG - 2015-04-05 00:13:22 --> db_debug=1
DEBUG - 2015-04-05 00:13:22 --> cache_on=
DEBUG - 2015-04-05 00:13:22 --> cachedir=
DEBUG - 2015-04-05 00:13:22 --> char_set=utf8
DEBUG - 2015-04-05 00:13:22 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:13:22 --> swap_pre=
DEBUG - 2015-04-05 00:13:22 --> autoinit=1
DEBUG - 2015-04-05 00:13:22 --> stricton=
DEBUG - 2015-04-05 00:13:22 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:13:23 --> XSS Filtering completed
DEBUG - 2015-04-05 00:13:23 --> Final output sent to browser
DEBUG - 2015-04-05 00:13:23 --> Total execution time: 1.2111
DEBUG - 2015-04-05 00:13:23 --> Config Class Initialized
DEBUG - 2015-04-05 00:13:23 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:13:23 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:13:23 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:13:23 --> URI Class Initialized
DEBUG - 2015-04-05 00:13:23 --> Config Class Initialized
DEBUG - 2015-04-05 00:13:23 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:13:23 --> Router Class Initialized
DEBUG - 2015-04-05 00:13:23 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:13:23 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:13:23 --> URI Class Initialized
DEBUG - 2015-04-05 00:13:23 --> Output Class Initialized
DEBUG - 2015-04-05 00:13:23 --> Router Class Initialized
DEBUG - 2015-04-05 00:13:23 --> Security Class Initialized
DEBUG - 2015-04-05 00:13:23 --> Input Class Initialized
DEBUG - 2015-04-05 00:13:23 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:13:23 --> Output Class Initialized
DEBUG - 2015-04-05 00:13:23 --> Language Class Initialized
DEBUG - 2015-04-05 00:13:23 --> Security Class Initialized
DEBUG - 2015-04-05 00:13:23 --> Loader Class Initialized
DEBUG - 2015-04-05 00:13:23 --> Input Class Initialized
DEBUG - 2015-04-05 00:13:23 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:13:23 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:13:23 --> Controller Class Initialized
DEBUG - 2015-04-05 00:13:23 --> Model Class Initialized
DEBUG - 2015-04-05 00:13:23 --> Model Class Initialized
DEBUG - 2015-04-05 00:13:23 --> Language Class Initialized
DEBUG - 2015-04-05 00:13:23 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:13:23 --> hostname=localhost
DEBUG - 2015-04-05 00:13:23 --> username=root
DEBUG - 2015-04-05 00:13:23 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:13:23 --> database=cl51-democompa
DEBUG - 2015-04-05 00:13:23 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:13:23 --> dbprefix=
DEBUG - 2015-04-05 00:13:23 --> pconnect=1
DEBUG - 2015-04-05 00:13:23 --> db_debug=1
DEBUG - 2015-04-05 00:13:23 --> cache_on=
DEBUG - 2015-04-05 00:13:23 --> cachedir=
DEBUG - 2015-04-05 00:13:23 --> char_set=utf8
DEBUG - 2015-04-05 00:13:23 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:13:23 --> swap_pre=
DEBUG - 2015-04-05 00:13:23 --> autoinit=1
DEBUG - 2015-04-05 00:13:23 --> stricton=
DEBUG - 2015-04-05 00:13:23 --> Loader Class Initialized
DEBUG - 2015-04-05 00:13:23 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:13:23 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:13:24 --> select mm.pk_id from messenger_messages as mm
					inner join messenger_users_messages as mum on mum.fk_message_id = mm.pk_id 
					and mum.`read` = 0 
					and mm.fk_destination_user = 1
					limit 1;
DEBUG - 2015-04-05 00:13:24 --> Final output sent to browser
DEBUG - 2015-04-05 00:13:24 --> Total execution time: 1.0761
DEBUG - 2015-04-05 00:13:24 --> Controller Class Initialized
DEBUG - 2015-04-05 00:13:24 --> Helper loaded: custom_validations_helper
DEBUG - 2015-04-05 00:13:24 --> Model Class Initialized
DEBUG - 2015-04-05 00:13:24 --> Model Class Initialized
DEBUG - 2015-04-05 00:13:24 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:13:24 --> hostname=localhost
DEBUG - 2015-04-05 00:13:24 --> username=root
DEBUG - 2015-04-05 00:13:24 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:13:24 --> database=cl51-democompa
DEBUG - 2015-04-05 00:13:24 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:13:24 --> dbprefix=
DEBUG - 2015-04-05 00:13:24 --> pconnect=1
DEBUG - 2015-04-05 00:13:24 --> db_debug=1
DEBUG - 2015-04-05 00:13:24 --> cache_on=
DEBUG - 2015-04-05 00:13:24 --> cachedir=
DEBUG - 2015-04-05 00:13:24 --> char_set=utf8
DEBUG - 2015-04-05 00:13:24 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:13:24 --> swap_pre=
DEBUG - 2015-04-05 00:13:24 --> autoinit=1
DEBUG - 2015-04-05 00:13:24 --> stricton=
DEBUG - 2015-04-05 00:13:24 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:13:25 --> XSS Filtering completed
DEBUG - 2015-04-05 00:13:25 --> Final output sent to browser
DEBUG - 2015-04-05 00:13:25 --> Total execution time: 2.1381
DEBUG - 2015-04-05 00:13:26 --> Config Class Initialized
DEBUG - 2015-04-05 00:13:26 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:13:26 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:13:26 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:13:26 --> URI Class Initialized
DEBUG - 2015-04-05 00:13:26 --> Router Class Initialized
DEBUG - 2015-04-05 00:13:26 --> Output Class Initialized
DEBUG - 2015-04-05 00:13:26 --> Security Class Initialized
DEBUG - 2015-04-05 00:13:26 --> Input Class Initialized
DEBUG - 2015-04-05 00:13:26 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:13:26 --> Language Class Initialized
DEBUG - 2015-04-05 00:13:26 --> Loader Class Initialized
DEBUG - 2015-04-05 00:13:26 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:13:26 --> Controller Class Initialized
DEBUG - 2015-04-05 00:13:26 --> Model Class Initialized
DEBUG - 2015-04-05 00:13:26 --> Model Class Initialized
DEBUG - 2015-04-05 00:13:26 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:13:26 --> hostname=localhost
DEBUG - 2015-04-05 00:13:26 --> username=root
DEBUG - 2015-04-05 00:13:26 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:13:26 --> database=cl51-democompa
DEBUG - 2015-04-05 00:13:26 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:13:26 --> dbprefix=
DEBUG - 2015-04-05 00:13:26 --> pconnect=1
DEBUG - 2015-04-05 00:13:26 --> db_debug=1
DEBUG - 2015-04-05 00:13:26 --> cache_on=
DEBUG - 2015-04-05 00:13:26 --> cachedir=
DEBUG - 2015-04-05 00:13:26 --> char_set=utf8
DEBUG - 2015-04-05 00:13:26 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:13:26 --> swap_pre=
DEBUG - 2015-04-05 00:13:26 --> autoinit=1
DEBUG - 2015-04-05 00:13:26 --> stricton=
DEBUG - 2015-04-05 00:13:26 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:13:27 --> SELECT 
				a.pk_id,
				a.message,
				a.creation_date,
				b.`read` as isread,
				c.name
				FROM messenger_messages as a
				inner join messenger_users_messages as b on a.pk_id = b.fk_message_id
				inner join users as c on c.pk_id = a.fk_user_id
				where a.fk_destination_user = 1 order by 3 desc
DEBUG - 2015-04-05 00:13:27 --> File loaded: ../application/views/messenger_inbox_view.php
DEBUG - 2015-04-05 00:13:27 --> Final output sent to browser
DEBUG - 2015-04-05 00:13:27 --> Total execution time: 1.0701
DEBUG - 2015-04-05 00:22:09 --> Config Class Initialized
DEBUG - 2015-04-05 00:22:09 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:22:09 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:22:09 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:22:09 --> URI Class Initialized
DEBUG - 2015-04-05 00:22:09 --> Router Class Initialized
DEBUG - 2015-04-05 00:22:09 --> Output Class Initialized
DEBUG - 2015-04-05 00:22:09 --> Security Class Initialized
DEBUG - 2015-04-05 00:22:09 --> Input Class Initialized
DEBUG - 2015-04-05 00:22:09 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:22:09 --> Language Class Initialized
DEBUG - 2015-04-05 00:22:09 --> Loader Class Initialized
DEBUG - 2015-04-05 00:22:09 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:22:09 --> Controller Class Initialized
DEBUG - 2015-04-05 00:22:09 --> Model Class Initialized
DEBUG - 2015-04-05 00:22:09 --> Model Class Initialized
DEBUG - 2015-04-05 00:22:09 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:22:09 --> hostname=localhost
DEBUG - 2015-04-05 00:22:09 --> username=root
DEBUG - 2015-04-05 00:22:09 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:22:09 --> database=cl51-democompa
DEBUG - 2015-04-05 00:22:09 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:22:09 --> dbprefix=
DEBUG - 2015-04-05 00:22:09 --> pconnect=1
DEBUG - 2015-04-05 00:22:09 --> db_debug=1
DEBUG - 2015-04-05 00:22:09 --> cache_on=
DEBUG - 2015-04-05 00:22:09 --> cachedir=
DEBUG - 2015-04-05 00:22:09 --> char_set=utf8
DEBUG - 2015-04-05 00:22:09 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:22:09 --> swap_pre=
DEBUG - 2015-04-05 00:22:09 --> autoinit=1
DEBUG - 2015-04-05 00:22:09 --> stricton=
DEBUG - 2015-04-05 00:22:09 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:22:10 --> Model Class Initialized
DEBUG - 2015-04-05 00:22:10 --> hostname=localhost
DEBUG - 2015-04-05 00:22:10 --> username=root
DEBUG - 2015-04-05 00:22:10 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:22:10 --> database=cl51-democompa
DEBUG - 2015-04-05 00:22:10 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:22:10 --> dbprefix=
DEBUG - 2015-04-05 00:22:10 --> pconnect=1
DEBUG - 2015-04-05 00:22:10 --> db_debug=1
DEBUG - 2015-04-05 00:22:10 --> cache_on=
DEBUG - 2015-04-05 00:22:10 --> cachedir=
DEBUG - 2015-04-05 00:22:10 --> char_set=utf8
DEBUG - 2015-04-05 00:22:10 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:22:10 --> swap_pre=
DEBUG - 2015-04-05 00:22:10 --> autoinit=1
DEBUG - 2015-04-05 00:22:10 --> stricton=
DEBUG - 2015-04-05 00:22:10 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:22:11 --> File loaded: ../application/views/header_nav.php
DEBUG - 2015-04-05 00:22:11 --> File loaded: ../application/views/hire_stock_groups.php
DEBUG - 2015-04-05 00:22:11 --> File loaded: ../application/views/footer_common.php
DEBUG - 2015-04-05 00:22:11 --> File loaded: ../application/views/footer_copyright.php
DEBUG - 2015-04-05 00:22:11 --> File loaded: ../application/views/footer.php
DEBUG - 2015-04-05 00:22:11 --> Final output sent to browser
DEBUG - 2015-04-05 00:22:11 --> Total execution time: 2.2201
DEBUG - 2015-04-05 00:22:11 --> Config Class Initialized
DEBUG - 2015-04-05 00:22:11 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:22:11 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:22:11 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:22:11 --> URI Class Initialized
DEBUG - 2015-04-05 00:22:11 --> Router Class Initialized
DEBUG - 2015-04-05 00:22:11 --> Output Class Initialized
DEBUG - 2015-04-05 00:22:11 --> Security Class Initialized
DEBUG - 2015-04-05 00:22:11 --> Input Class Initialized
DEBUG - 2015-04-05 00:22:11 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:22:11 --> Language Class Initialized
DEBUG - 2015-04-05 00:22:12 --> Loader Class Initialized
DEBUG - 2015-04-05 00:22:12 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:22:12 --> Controller Class Initialized
DEBUG - 2015-04-05 00:22:12 --> Config Class Initialized
DEBUG - 2015-04-05 00:22:12 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:22:12 --> Model Class Initialized
DEBUG - 2015-04-05 00:22:12 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:22:12 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:22:12 --> Model Class Initialized
DEBUG - 2015-04-05 00:22:12 --> URI Class Initialized
DEBUG - 2015-04-05 00:22:12 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:22:12 --> Router Class Initialized
DEBUG - 2015-04-05 00:22:12 --> Output Class Initialized
DEBUG - 2015-04-05 00:22:12 --> Security Class Initialized
DEBUG - 2015-04-05 00:22:12 --> Input Class Initialized
DEBUG - 2015-04-05 00:22:12 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:22:12 --> hostname=localhost
DEBUG - 2015-04-05 00:22:12 --> username=root
DEBUG - 2015-04-05 00:22:12 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:22:12 --> database=cl51-democompa
DEBUG - 2015-04-05 00:22:12 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:22:12 --> dbprefix=
DEBUG - 2015-04-05 00:22:12 --> pconnect=1
DEBUG - 2015-04-05 00:22:12 --> db_debug=1
DEBUG - 2015-04-05 00:22:12 --> cache_on=
DEBUG - 2015-04-05 00:22:12 --> cachedir=
DEBUG - 2015-04-05 00:22:12 --> char_set=utf8
DEBUG - 2015-04-05 00:22:12 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:22:12 --> swap_pre=
DEBUG - 2015-04-05 00:22:12 --> autoinit=1
DEBUG - 2015-04-05 00:22:12 --> stricton=
DEBUG - 2015-04-05 00:22:12 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:22:12 --> Language Class Initialized
DEBUG - 2015-04-05 00:22:12 --> Loader Class Initialized
DEBUG - 2015-04-05 00:22:12 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:22:12 --> Config Class Initialized
DEBUG - 2015-04-05 00:22:12 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:22:12 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:22:12 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:22:12 --> URI Class Initialized
DEBUG - 2015-04-05 00:22:12 --> Router Class Initialized
DEBUG - 2015-04-05 00:22:12 --> Config Class Initialized
DEBUG - 2015-04-05 00:22:12 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:22:12 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:22:12 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:22:12 --> Output Class Initialized
DEBUG - 2015-04-05 00:22:12 --> URI Class Initialized
DEBUG - 2015-04-05 00:22:12 --> Security Class Initialized
DEBUG - 2015-04-05 00:22:12 --> Router Class Initialized
DEBUG - 2015-04-05 00:22:12 --> Input Class Initialized
DEBUG - 2015-04-05 00:22:12 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:22:12 --> Language Class Initialized
DEBUG - 2015-04-05 00:22:12 --> Output Class Initialized
DEBUG - 2015-04-05 00:22:12 --> Security Class Initialized
DEBUG - 2015-04-05 00:22:12 --> Loader Class Initialized
DEBUG - 2015-04-05 00:22:12 --> Input Class Initialized
DEBUG - 2015-04-05 00:22:12 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:22:12 --> Language Class Initialized
DEBUG - 2015-04-05 00:22:12 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:22:12 --> Loader Class Initialized
DEBUG - 2015-04-05 00:22:12 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:22:12 --> Config Class Initialized
DEBUG - 2015-04-05 00:22:12 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:22:12 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:22:12 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:22:12 --> URI Class Initialized
DEBUG - 2015-04-05 00:22:12 --> Router Class Initialized
DEBUG - 2015-04-05 00:22:12 --> Output Class Initialized
DEBUG - 2015-04-05 00:22:12 --> Security Class Initialized
DEBUG - 2015-04-05 00:22:12 --> Input Class Initialized
DEBUG - 2015-04-05 00:22:12 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:22:12 --> Language Class Initialized
DEBUG - 2015-04-05 00:22:12 --> Loader Class Initialized
DEBUG - 2015-04-05 00:22:12 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:22:13 --> Final output sent to browser
DEBUG - 2015-04-05 00:22:13 --> Total execution time: 1.0781
DEBUG - 2015-04-05 00:22:13 --> Controller Class Initialized
DEBUG - 2015-04-05 00:22:13 --> Model Class Initialized
DEBUG - 2015-04-05 00:22:13 --> Model Class Initialized
DEBUG - 2015-04-05 00:22:13 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:22:13 --> hostname=localhost
DEBUG - 2015-04-05 00:22:13 --> username=root
DEBUG - 2015-04-05 00:22:13 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:22:13 --> database=cl51-democompa
DEBUG - 2015-04-05 00:22:13 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:22:13 --> dbprefix=
DEBUG - 2015-04-05 00:22:13 --> pconnect=1
DEBUG - 2015-04-05 00:22:13 --> db_debug=1
DEBUG - 2015-04-05 00:22:13 --> cache_on=
DEBUG - 2015-04-05 00:22:13 --> cachedir=
DEBUG - 2015-04-05 00:22:13 --> char_set=utf8
DEBUG - 2015-04-05 00:22:13 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:22:13 --> swap_pre=
DEBUG - 2015-04-05 00:22:13 --> autoinit=1
DEBUG - 2015-04-05 00:22:13 --> stricton=
DEBUG - 2015-04-05 00:22:13 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:22:14 --> Final output sent to browser
DEBUG - 2015-04-05 00:22:14 --> Total execution time: 2.1491
DEBUG - 2015-04-05 00:22:14 --> Controller Class Initialized
DEBUG - 2015-04-05 00:22:14 --> Model Class Initialized
DEBUG - 2015-04-05 00:22:14 --> Model Class Initialized
DEBUG - 2015-04-05 00:22:14 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:22:14 --> hostname=localhost
DEBUG - 2015-04-05 00:22:14 --> username=root
DEBUG - 2015-04-05 00:22:14 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:22:14 --> database=cl51-democompa
DEBUG - 2015-04-05 00:22:14 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:22:14 --> dbprefix=
DEBUG - 2015-04-05 00:22:14 --> pconnect=1
DEBUG - 2015-04-05 00:22:14 --> db_debug=1
DEBUG - 2015-04-05 00:22:14 --> cache_on=
DEBUG - 2015-04-05 00:22:14 --> cachedir=
DEBUG - 2015-04-05 00:22:14 --> char_set=utf8
DEBUG - 2015-04-05 00:22:14 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:22:14 --> swap_pre=
DEBUG - 2015-04-05 00:22:14 --> autoinit=1
DEBUG - 2015-04-05 00:22:14 --> stricton=
DEBUG - 2015-04-05 00:22:14 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:22:15 --> Final output sent to browser
DEBUG - 2015-04-05 00:22:15 --> Total execution time: 3.0932
DEBUG - 2015-04-05 00:22:15 --> Controller Class Initialized
DEBUG - 2015-04-05 00:22:15 --> Model Class Initialized
DEBUG - 2015-04-05 00:22:15 --> Model Class Initialized
DEBUG - 2015-04-05 00:22:15 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:22:15 --> hostname=localhost
DEBUG - 2015-04-05 00:22:15 --> username=root
DEBUG - 2015-04-05 00:22:15 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:22:15 --> database=cl51-democompa
DEBUG - 2015-04-05 00:22:15 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:22:15 --> dbprefix=
DEBUG - 2015-04-05 00:22:15 --> pconnect=1
DEBUG - 2015-04-05 00:22:15 --> db_debug=1
DEBUG - 2015-04-05 00:22:15 --> cache_on=
DEBUG - 2015-04-05 00:22:15 --> cachedir=
DEBUG - 2015-04-05 00:22:15 --> char_set=utf8
DEBUG - 2015-04-05 00:22:15 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:22:15 --> swap_pre=
DEBUG - 2015-04-05 00:22:15 --> autoinit=1
DEBUG - 2015-04-05 00:22:15 --> stricton=
DEBUG - 2015-04-05 00:22:15 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:22:16 --> select mm.pk_id from messenger_messages as mm
					inner join messenger_users_messages as mum on mum.fk_message_id = mm.pk_id 
					and mum.`read` = 0 
					and mm.fk_destination_user = 1
					limit 1;
DEBUG - 2015-04-05 00:22:16 --> Final output sent to browser
DEBUG - 2015-04-05 00:22:16 --> Total execution time: 4.1242
DEBUG - 2015-04-05 00:22:16 --> Controller Class Initialized
DEBUG - 2015-04-05 00:22:16 --> Helper loaded: custom_validations_helper
DEBUG - 2015-04-05 00:22:16 --> Model Class Initialized
DEBUG - 2015-04-05 00:22:16 --> Model Class Initialized
DEBUG - 2015-04-05 00:22:16 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:22:16 --> hostname=localhost
DEBUG - 2015-04-05 00:22:16 --> username=root
DEBUG - 2015-04-05 00:22:16 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:22:16 --> database=cl51-democompa
DEBUG - 2015-04-05 00:22:16 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:22:16 --> dbprefix=
DEBUG - 2015-04-05 00:22:16 --> pconnect=1
DEBUG - 2015-04-05 00:22:16 --> db_debug=1
DEBUG - 2015-04-05 00:22:16 --> cache_on=
DEBUG - 2015-04-05 00:22:16 --> cachedir=
DEBUG - 2015-04-05 00:22:16 --> char_set=utf8
DEBUG - 2015-04-05 00:22:16 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:22:16 --> swap_pre=
DEBUG - 2015-04-05 00:22:16 --> autoinit=1
DEBUG - 2015-04-05 00:22:16 --> stricton=
DEBUG - 2015-04-05 00:22:16 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:22:17 --> XSS Filtering completed
DEBUG - 2015-04-05 00:22:17 --> Final output sent to browser
DEBUG - 2015-04-05 00:22:17 --> Total execution time: 5.1433
DEBUG - 2015-04-05 00:22:28 --> Config Class Initialized
DEBUG - 2015-04-05 00:22:28 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:22:28 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:22:28 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:22:28 --> URI Class Initialized
DEBUG - 2015-04-05 00:22:28 --> Router Class Initialized
DEBUG - 2015-04-05 00:22:28 --> Output Class Initialized
DEBUG - 2015-04-05 00:22:28 --> Security Class Initialized
DEBUG - 2015-04-05 00:22:28 --> Input Class Initialized
DEBUG - 2015-04-05 00:22:28 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:22:28 --> Language Class Initialized
DEBUG - 2015-04-05 00:22:28 --> Loader Class Initialized
DEBUG - 2015-04-05 00:22:28 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:22:28 --> Controller Class Initialized
DEBUG - 2015-04-05 00:22:28 --> File loaded: ../application/views/header_nav.php
DEBUG - 2015-04-05 00:22:28 --> File loaded: ../application/views/hire_stock_fleet_records.php
DEBUG - 2015-04-05 00:22:28 --> File loaded: ../application/views/footer_common.php
DEBUG - 2015-04-05 00:22:28 --> File loaded: ../application/views/footer_copyright.php
DEBUG - 2015-04-05 00:22:28 --> File loaded: ../application/views/footer.php
DEBUG - 2015-04-05 00:22:28 --> Final output sent to browser
DEBUG - 2015-04-05 00:22:28 --> Total execution time: 0.0440
DEBUG - 2015-04-05 00:22:29 --> Config Class Initialized
DEBUG - 2015-04-05 00:22:29 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:22:29 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:22:29 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:22:29 --> Config Class Initialized
DEBUG - 2015-04-05 00:22:29 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:22:29 --> URI Class Initialized
DEBUG - 2015-04-05 00:22:29 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:22:29 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:22:29 --> URI Class Initialized
DEBUG - 2015-04-05 00:22:29 --> Router Class Initialized
DEBUG - 2015-04-05 00:22:29 --> Config Class Initialized
DEBUG - 2015-04-05 00:22:29 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:22:29 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:22:29 --> Output Class Initialized
DEBUG - 2015-04-05 00:22:29 --> Security Class Initialized
DEBUG - 2015-04-05 00:22:29 --> Input Class Initialized
DEBUG - 2015-04-05 00:22:29 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:22:29 --> Language Class Initialized
DEBUG - 2015-04-05 00:22:29 --> Router Class Initialized
DEBUG - 2015-04-05 00:22:29 --> Loader Class Initialized
DEBUG - 2015-04-05 00:22:29 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:22:29 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:22:29 --> Output Class Initialized
DEBUG - 2015-04-05 00:22:29 --> URI Class Initialized
DEBUG - 2015-04-05 00:22:29 --> Security Class Initialized
DEBUG - 2015-04-05 00:22:29 --> Router Class Initialized
DEBUG - 2015-04-05 00:22:29 --> Input Class Initialized
DEBUG - 2015-04-05 00:22:29 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:22:29 --> Language Class Initialized
DEBUG - 2015-04-05 00:22:29 --> Output Class Initialized
DEBUG - 2015-04-05 00:22:29 --> Security Class Initialized
DEBUG - 2015-04-05 00:22:29 --> Input Class Initialized
DEBUG - 2015-04-05 00:22:29 --> Controller Class Initialized
DEBUG - 2015-04-05 00:22:29 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:22:29 --> Helper loaded: custom_validations_helper
DEBUG - 2015-04-05 00:22:29 --> Model Class Initialized
DEBUG - 2015-04-05 00:22:29 --> Model Class Initialized
DEBUG - 2015-04-05 00:22:29 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:22:29 --> hostname=localhost
DEBUG - 2015-04-05 00:22:29 --> username=root
DEBUG - 2015-04-05 00:22:29 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:22:29 --> database=cl51-democompa
DEBUG - 2015-04-05 00:22:29 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:22:29 --> dbprefix=
DEBUG - 2015-04-05 00:22:29 --> pconnect=1
DEBUG - 2015-04-05 00:22:29 --> db_debug=1
DEBUG - 2015-04-05 00:22:29 --> cache_on=
DEBUG - 2015-04-05 00:22:29 --> cachedir=
DEBUG - 2015-04-05 00:22:29 --> char_set=utf8
DEBUG - 2015-04-05 00:22:29 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:22:29 --> swap_pre=
DEBUG - 2015-04-05 00:22:29 --> autoinit=1
DEBUG - 2015-04-05 00:22:29 --> stricton=
DEBUG - 2015-04-05 00:22:29 --> Loader Class Initialized
DEBUG - 2015-04-05 00:22:29 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:22:29 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:22:29 --> Language Class Initialized
DEBUG - 2015-04-05 00:22:29 --> Loader Class Initialized
DEBUG - 2015-04-05 00:22:29 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:22:29 --> Config Class Initialized
DEBUG - 2015-04-05 00:22:29 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:22:29 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:22:29 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:22:29 --> URI Class Initialized
DEBUG - 2015-04-05 00:22:29 --> Router Class Initialized
DEBUG - 2015-04-05 00:22:29 --> Output Class Initialized
DEBUG - 2015-04-05 00:22:29 --> Security Class Initialized
DEBUG - 2015-04-05 00:22:29 --> Input Class Initialized
DEBUG - 2015-04-05 00:22:29 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:22:29 --> Language Class Initialized
DEBUG - 2015-04-05 00:22:29 --> Loader Class Initialized
DEBUG - 2015-04-05 00:22:29 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:22:30 --> XSS Filtering completed
DEBUG - 2015-04-05 00:22:30 --> Final output sent to browser
DEBUG - 2015-04-05 00:22:30 --> Total execution time: 1.0991
DEBUG - 2015-04-05 00:22:30 --> Controller Class Initialized
DEBUG - 2015-04-05 00:22:30 --> Model Class Initialized
DEBUG - 2015-04-05 00:22:30 --> Model Class Initialized
DEBUG - 2015-04-05 00:22:30 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:22:30 --> hostname=localhost
DEBUG - 2015-04-05 00:22:30 --> username=root
DEBUG - 2015-04-05 00:22:30 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:22:30 --> database=cl51-democompa
DEBUG - 2015-04-05 00:22:30 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:22:30 --> dbprefix=
DEBUG - 2015-04-05 00:22:30 --> pconnect=1
DEBUG - 2015-04-05 00:22:30 --> db_debug=1
DEBUG - 2015-04-05 00:22:30 --> cache_on=
DEBUG - 2015-04-05 00:22:30 --> cachedir=
DEBUG - 2015-04-05 00:22:30 --> char_set=utf8
DEBUG - 2015-04-05 00:22:30 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:22:30 --> swap_pre=
DEBUG - 2015-04-05 00:22:30 --> autoinit=1
DEBUG - 2015-04-05 00:22:30 --> stricton=
DEBUG - 2015-04-05 00:22:30 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:22:31 --> select mm.pk_id from messenger_messages as mm
					inner join messenger_users_messages as mum on mum.fk_message_id = mm.pk_id 
					and mum.`read` = 0 
					and mm.fk_destination_user = 1
					limit 1;
DEBUG - 2015-04-05 00:22:31 --> Final output sent to browser
DEBUG - 2015-04-05 00:22:31 --> Total execution time: 2.1411
DEBUG - 2015-04-05 00:22:31 --> Controller Class Initialized
DEBUG - 2015-04-05 00:22:31 --> Model Class Initialized
DEBUG - 2015-04-05 00:22:31 --> Model Class Initialized
DEBUG - 2015-04-05 00:22:31 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:22:31 --> hostname=localhost
DEBUG - 2015-04-05 00:22:31 --> username=root
DEBUG - 2015-04-05 00:22:31 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:22:31 --> database=cl51-democompa
DEBUG - 2015-04-05 00:22:31 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:22:31 --> dbprefix=
DEBUG - 2015-04-05 00:22:31 --> pconnect=1
DEBUG - 2015-04-05 00:22:31 --> db_debug=1
DEBUG - 2015-04-05 00:22:31 --> cache_on=
DEBUG - 2015-04-05 00:22:31 --> cachedir=
DEBUG - 2015-04-05 00:22:31 --> char_set=utf8
DEBUG - 2015-04-05 00:22:31 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:22:31 --> swap_pre=
DEBUG - 2015-04-05 00:22:31 --> autoinit=1
DEBUG - 2015-04-05 00:22:31 --> stricton=
DEBUG - 2015-04-05 00:22:31 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:22:32 --> Final output sent to browser
DEBUG - 2015-04-05 00:22:32 --> Total execution time: 3.1902
DEBUG - 2015-04-05 00:22:32 --> Controller Class Initialized
DEBUG - 2015-04-05 00:22:32 --> Model Class Initialized
DEBUG - 2015-04-05 00:22:32 --> Model Class Initialized
DEBUG - 2015-04-05 00:22:32 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:22:32 --> hostname=localhost
DEBUG - 2015-04-05 00:22:32 --> username=root
DEBUG - 2015-04-05 00:22:32 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:22:32 --> database=cl51-democompa
DEBUG - 2015-04-05 00:22:32 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:22:32 --> dbprefix=
DEBUG - 2015-04-05 00:22:32 --> pconnect=1
DEBUG - 2015-04-05 00:22:32 --> db_debug=1
DEBUG - 2015-04-05 00:22:32 --> cache_on=
DEBUG - 2015-04-05 00:22:32 --> cachedir=
DEBUG - 2015-04-05 00:22:32 --> char_set=utf8
DEBUG - 2015-04-05 00:22:32 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:22:32 --> swap_pre=
DEBUG - 2015-04-05 00:22:32 --> autoinit=1
DEBUG - 2015-04-05 00:22:32 --> stricton=
DEBUG - 2015-04-05 00:22:32 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:22:33 --> Final output sent to browser
DEBUG - 2015-04-05 00:22:33 --> Total execution time: 4.1732
DEBUG - 2015-04-05 00:33:19 --> Config Class Initialized
DEBUG - 2015-04-05 00:33:19 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:33:19 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:33:19 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:33:19 --> URI Class Initialized
DEBUG - 2015-04-05 00:33:19 --> Router Class Initialized
DEBUG - 2015-04-05 00:33:19 --> Output Class Initialized
DEBUG - 2015-04-05 00:33:19 --> Security Class Initialized
DEBUG - 2015-04-05 00:33:19 --> Input Class Initialized
DEBUG - 2015-04-05 00:33:19 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:33:19 --> Language Class Initialized
DEBUG - 2015-04-05 00:33:19 --> Loader Class Initialized
DEBUG - 2015-04-05 00:33:19 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:33:19 --> Controller Class Initialized
DEBUG - 2015-04-05 00:33:19 --> Model Class Initialized
DEBUG - 2015-04-05 00:33:19 --> Model Class Initialized
DEBUG - 2015-04-05 00:33:19 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:33:19 --> hostname=localhost
DEBUG - 2015-04-05 00:33:19 --> username=root
DEBUG - 2015-04-05 00:33:19 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:33:19 --> database=cl51-democompa
DEBUG - 2015-04-05 00:33:19 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:33:19 --> dbprefix=
DEBUG - 2015-04-05 00:33:19 --> pconnect=1
DEBUG - 2015-04-05 00:33:19 --> db_debug=1
DEBUG - 2015-04-05 00:33:19 --> cache_on=
DEBUG - 2015-04-05 00:33:19 --> cachedir=
DEBUG - 2015-04-05 00:33:19 --> char_set=utf8
DEBUG - 2015-04-05 00:33:19 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:33:19 --> swap_pre=
DEBUG - 2015-04-05 00:33:19 --> autoinit=1
DEBUG - 2015-04-05 00:33:19 --> stricton=
DEBUG - 2015-04-05 00:33:19 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:33:20 --> File loaded: ../application/views/header_nav.php
DEBUG - 2015-04-05 00:33:20 --> File loaded: ../application/views/hire_stock_new_item.php
DEBUG - 2015-04-05 00:33:20 --> File loaded: ../application/views/footer_common.php
DEBUG - 2015-04-05 00:33:20 --> File loaded: ../application/views/footer_copyright.php
DEBUG - 2015-04-05 00:33:20 --> File loaded: ../application/views/footer.php
DEBUG - 2015-04-05 00:33:20 --> Final output sent to browser
DEBUG - 2015-04-05 00:33:20 --> Total execution time: 1.1141
DEBUG - 2015-04-05 00:33:21 --> Config Class Initialized
DEBUG - 2015-04-05 00:33:21 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:33:21 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:33:21 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:33:21 --> URI Class Initialized
DEBUG - 2015-04-05 00:33:21 --> Router Class Initialized
DEBUG - 2015-04-05 00:33:21 --> Config Class Initialized
DEBUG - 2015-04-05 00:33:21 --> Output Class Initialized
DEBUG - 2015-04-05 00:33:21 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:33:21 --> Security Class Initialized
DEBUG - 2015-04-05 00:33:21 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:33:21 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:33:21 --> Input Class Initialized
DEBUG - 2015-04-05 00:33:21 --> URI Class Initialized
DEBUG - 2015-04-05 00:33:21 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:33:21 --> Language Class Initialized
DEBUG - 2015-04-05 00:33:21 --> Loader Class Initialized
DEBUG - 2015-04-05 00:33:21 --> Router Class Initialized
DEBUG - 2015-04-05 00:33:21 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:33:21 --> Output Class Initialized
DEBUG - 2015-04-05 00:33:21 --> Security Class Initialized
DEBUG - 2015-04-05 00:33:21 --> Input Class Initialized
DEBUG - 2015-04-05 00:33:21 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:33:21 --> Controller Class Initialized
DEBUG - 2015-04-05 00:33:21 --> Model Class Initialized
DEBUG - 2015-04-05 00:33:21 --> Language Class Initialized
DEBUG - 2015-04-05 00:33:21 --> Model Class Initialized
DEBUG - 2015-04-05 00:33:21 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:33:21 --> hostname=localhost
DEBUG - 2015-04-05 00:33:21 --> username=root
DEBUG - 2015-04-05 00:33:21 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:33:21 --> database=cl51-democompa
DEBUG - 2015-04-05 00:33:21 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:33:21 --> Loader Class Initialized
DEBUG - 2015-04-05 00:33:21 --> dbprefix=
DEBUG - 2015-04-05 00:33:21 --> pconnect=1
DEBUG - 2015-04-05 00:33:21 --> db_debug=1
DEBUG - 2015-04-05 00:33:21 --> cache_on=
DEBUG - 2015-04-05 00:33:21 --> cachedir=
DEBUG - 2015-04-05 00:33:21 --> char_set=utf8
DEBUG - 2015-04-05 00:33:21 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:33:21 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:33:21 --> swap_pre=
DEBUG - 2015-04-05 00:33:21 --> autoinit=1
DEBUG - 2015-04-05 00:33:21 --> stricton=
DEBUG - 2015-04-05 00:33:21 --> Config Class Initialized
DEBUG - 2015-04-05 00:33:21 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:33:21 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:33:21 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:33:21 --> URI Class Initialized
DEBUG - 2015-04-05 00:33:21 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:33:21 --> Router Class Initialized
DEBUG - 2015-04-05 00:33:21 --> Output Class Initialized
DEBUG - 2015-04-05 00:33:21 --> Security Class Initialized
DEBUG - 2015-04-05 00:33:21 --> Input Class Initialized
DEBUG - 2015-04-05 00:33:21 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:33:21 --> Language Class Initialized
DEBUG - 2015-04-05 00:33:21 --> Loader Class Initialized
DEBUG - 2015-04-05 00:33:21 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:33:22 --> Final output sent to browser
DEBUG - 2015-04-05 00:33:22 --> Total execution time: 1.0871
DEBUG - 2015-04-05 00:33:22 --> Controller Class Initialized
DEBUG - 2015-04-05 00:33:22 --> Helper loaded: custom_validations_helper
DEBUG - 2015-04-05 00:33:22 --> Model Class Initialized
DEBUG - 2015-04-05 00:33:22 --> Model Class Initialized
DEBUG - 2015-04-05 00:33:22 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:33:22 --> hostname=localhost
DEBUG - 2015-04-05 00:33:22 --> username=root
DEBUG - 2015-04-05 00:33:22 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:33:22 --> database=cl51-democompa
DEBUG - 2015-04-05 00:33:22 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:33:22 --> dbprefix=
DEBUG - 2015-04-05 00:33:22 --> pconnect=1
DEBUG - 2015-04-05 00:33:22 --> db_debug=1
DEBUG - 2015-04-05 00:33:22 --> cache_on=
DEBUG - 2015-04-05 00:33:22 --> cachedir=
DEBUG - 2015-04-05 00:33:22 --> char_set=utf8
DEBUG - 2015-04-05 00:33:22 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:33:22 --> swap_pre=
DEBUG - 2015-04-05 00:33:22 --> autoinit=1
DEBUG - 2015-04-05 00:33:22 --> stricton=
DEBUG - 2015-04-05 00:33:22 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:33:23 --> XSS Filtering completed
DEBUG - 2015-04-05 00:33:23 --> Final output sent to browser
DEBUG - 2015-04-05 00:33:23 --> Total execution time: 2.1021
DEBUG - 2015-04-05 00:33:23 --> Controller Class Initialized
DEBUG - 2015-04-05 00:33:23 --> Model Class Initialized
DEBUG - 2015-04-05 00:33:23 --> Model Class Initialized
DEBUG - 2015-04-05 00:33:23 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:33:23 --> hostname=localhost
DEBUG - 2015-04-05 00:33:23 --> username=root
DEBUG - 2015-04-05 00:33:23 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:33:23 --> database=cl51-democompa
DEBUG - 2015-04-05 00:33:23 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:33:23 --> dbprefix=
DEBUG - 2015-04-05 00:33:23 --> pconnect=1
DEBUG - 2015-04-05 00:33:23 --> db_debug=1
DEBUG - 2015-04-05 00:33:23 --> cache_on=
DEBUG - 2015-04-05 00:33:23 --> cachedir=
DEBUG - 2015-04-05 00:33:23 --> char_set=utf8
DEBUG - 2015-04-05 00:33:23 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:33:23 --> swap_pre=
DEBUG - 2015-04-05 00:33:23 --> autoinit=1
DEBUG - 2015-04-05 00:33:23 --> stricton=
DEBUG - 2015-04-05 00:33:23 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:33:24 --> select mm.pk_id from messenger_messages as mm
					inner join messenger_users_messages as mum on mum.fk_message_id = mm.pk_id 
					and mum.`read` = 0 
					and mm.fk_destination_user = 1
					limit 1;
DEBUG - 2015-04-05 00:33:24 --> Final output sent to browser
DEBUG - 2015-04-05 00:33:24 --> Total execution time: 3.1592
DEBUG - 2015-04-05 00:33:42 --> Config Class Initialized
DEBUG - 2015-04-05 00:33:42 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:33:42 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:33:42 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:33:42 --> URI Class Initialized
DEBUG - 2015-04-05 00:33:42 --> Router Class Initialized
DEBUG - 2015-04-05 00:33:42 --> Output Class Initialized
DEBUG - 2015-04-05 00:33:42 --> Security Class Initialized
DEBUG - 2015-04-05 00:33:42 --> Input Class Initialized
DEBUG - 2015-04-05 00:33:42 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:33:42 --> Language Class Initialized
DEBUG - 2015-04-05 00:33:42 --> Loader Class Initialized
DEBUG - 2015-04-05 00:33:42 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:33:42 --> Controller Class Initialized
DEBUG - 2015-04-05 00:33:42 --> Session Class Initialized
DEBUG - 2015-04-05 00:33:42 --> Helper loaded: string_helper
DEBUG - 2015-04-05 00:33:42 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:33:43 --> A session cookie was not found.
DEBUG - 2015-04-05 00:33:43 --> Session routines successfully run
DEBUG - 2015-04-05 00:33:43 --> Helper loaded: form_helper
DEBUG - 2015-04-05 00:33:43 --> XSS Filtering completed
DEBUG - 2015-04-05 00:33:43 --> XSS Filtering completed
DEBUG - 2015-04-05 00:33:43 --> XSS Filtering completed
DEBUG - 2015-04-05 00:33:43 --> XSS Filtering completed
DEBUG - 2015-04-05 00:33:43 --> XSS Filtering completed
DEBUG - 2015-04-05 00:33:43 --> XSS Filtering completed
DEBUG - 2015-04-05 00:33:43 --> XSS Filtering completed
DEBUG - 2015-04-05 00:33:43 --> XSS Filtering completed
DEBUG - 2015-04-05 00:33:43 --> XSS Filtering completed
DEBUG - 2015-04-05 00:33:43 --> XSS Filtering completed
DEBUG - 2015-04-05 00:33:43 --> XSS Filtering completed
DEBUG - 2015-04-05 00:33:43 --> XSS Filtering completed
DEBUG - 2015-04-05 00:33:43 --> XSS Filtering completed
DEBUG - 2015-04-05 00:33:43 --> XSS Filtering completed
DEBUG - 2015-04-05 00:33:43 --> XSS Filtering completed
DEBUG - 2015-04-05 00:33:43 --> XSS Filtering completed
DEBUG - 2015-04-05 00:33:43 --> XSS Filtering completed
DEBUG - 2015-04-05 00:33:43 --> Model Class Initialized
DEBUG - 2015-04-05 00:33:43 --> Model Class Initialized
DEBUG - 2015-04-05 00:33:43 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:33:43 --> hostname=localhost
DEBUG - 2015-04-05 00:33:43 --> username=root
DEBUG - 2015-04-05 00:33:43 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:33:43 --> database=cl51-democompa
DEBUG - 2015-04-05 00:33:43 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:33:43 --> dbprefix=
DEBUG - 2015-04-05 00:33:43 --> pconnect=1
DEBUG - 2015-04-05 00:33:43 --> db_debug=1
DEBUG - 2015-04-05 00:33:43 --> cache_on=
DEBUG - 2015-04-05 00:33:43 --> cachedir=
DEBUG - 2015-04-05 00:33:43 --> char_set=utf8
DEBUG - 2015-04-05 00:33:43 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:33:43 --> swap_pre=
DEBUG - 2015-04-05 00:33:43 --> autoinit=1
DEBUG - 2015-04-05 00:33:43 --> stricton=
DEBUG - 2015-04-05 00:33:43 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:33:44 --> Final output sent to browser
DEBUG - 2015-04-05 00:33:44 --> Total execution time: 2.5651
DEBUG - 2015-04-05 00:33:44 --> Config Class Initialized
DEBUG - 2015-04-05 00:33:44 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:33:44 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:33:44 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:33:45 --> URI Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Router Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Output Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Security Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Input Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:33:45 --> Language Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Loader Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:33:45 --> Controller Class Initialized
DEBUG - 2015-04-05 00:33:45 --> File loaded: ../application/views/header_nav.php
DEBUG - 2015-04-05 00:33:45 --> File loaded: ../application/views/hire_stock_fleet_records.php
DEBUG - 2015-04-05 00:33:45 --> File loaded: ../application/views/footer_common.php
DEBUG - 2015-04-05 00:33:45 --> File loaded: ../application/views/footer_copyright.php
DEBUG - 2015-04-05 00:33:45 --> File loaded: ../application/views/footer.php
DEBUG - 2015-04-05 00:33:45 --> Final output sent to browser
DEBUG - 2015-04-05 00:33:45 --> Total execution time: 0.0420
DEBUG - 2015-04-05 00:33:45 --> Config Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:33:45 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:33:45 --> URI Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Router Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Output Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Security Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Input Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:33:45 --> Language Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Loader Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:33:45 --> Config Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:33:45 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:33:45 --> URI Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Config Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Controller Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Model Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:33:45 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:33:45 --> Model Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:33:45 --> URI Class Initialized
DEBUG - 2015-04-05 00:33:45 --> hostname=localhost
DEBUG - 2015-04-05 00:33:45 --> Router Class Initialized
DEBUG - 2015-04-05 00:33:45 --> username=root
DEBUG - 2015-04-05 00:33:45 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:33:45 --> database=cl51-democompa
DEBUG - 2015-04-05 00:33:45 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:33:45 --> dbprefix=
DEBUG - 2015-04-05 00:33:45 --> pconnect=1
DEBUG - 2015-04-05 00:33:45 --> db_debug=1
DEBUG - 2015-04-05 00:33:45 --> cache_on=
DEBUG - 2015-04-05 00:33:45 --> cachedir=
DEBUG - 2015-04-05 00:33:45 --> char_set=utf8
DEBUG - 2015-04-05 00:33:45 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:33:45 --> swap_pre=
DEBUG - 2015-04-05 00:33:45 --> autoinit=1
DEBUG - 2015-04-05 00:33:45 --> stricton=
DEBUG - 2015-04-05 00:33:45 --> Output Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Router Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Security Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Input Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:33:45 --> Language Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Loader Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:33:45 --> Config Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:33:45 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:33:45 --> URI Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Router Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Output Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Output Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Security Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Security Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Input Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Input Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:33:45 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:33:45 --> Language Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Language Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Loader Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Loader Class Initialized
DEBUG - 2015-04-05 00:33:45 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:33:45 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:33:46 --> select mm.pk_id from messenger_messages as mm
					inner join messenger_users_messages as mum on mum.fk_message_id = mm.pk_id 
					and mum.`read` = 0 
					and mm.fk_destination_user = 1
					limit 1;
DEBUG - 2015-04-05 00:33:46 --> Final output sent to browser
DEBUG - 2015-04-05 00:33:46 --> Total execution time: 1.0961
DEBUG - 2015-04-05 00:33:46 --> Controller Class Initialized
DEBUG - 2015-04-05 00:33:46 --> Model Class Initialized
DEBUG - 2015-04-05 00:33:46 --> Model Class Initialized
DEBUG - 2015-04-05 00:33:46 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:33:46 --> hostname=localhost
DEBUG - 2015-04-05 00:33:46 --> username=root
DEBUG - 2015-04-05 00:33:46 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:33:46 --> database=cl51-democompa
DEBUG - 2015-04-05 00:33:46 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:33:46 --> dbprefix=
DEBUG - 2015-04-05 00:33:46 --> pconnect=1
DEBUG - 2015-04-05 00:33:46 --> db_debug=1
DEBUG - 2015-04-05 00:33:46 --> cache_on=
DEBUG - 2015-04-05 00:33:46 --> cachedir=
DEBUG - 2015-04-05 00:33:46 --> char_set=utf8
DEBUG - 2015-04-05 00:33:46 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:33:46 --> swap_pre=
DEBUG - 2015-04-05 00:33:46 --> autoinit=1
DEBUG - 2015-04-05 00:33:46 --> stricton=
DEBUG - 2015-04-05 00:33:46 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:33:47 --> Final output sent to browser
DEBUG - 2015-04-05 00:33:47 --> Total execution time: 2.1301
DEBUG - 2015-04-05 00:33:47 --> Controller Class Initialized
DEBUG - 2015-04-05 00:33:47 --> Helper loaded: custom_validations_helper
DEBUG - 2015-04-05 00:33:47 --> Model Class Initialized
DEBUG - 2015-04-05 00:33:47 --> Model Class Initialized
DEBUG - 2015-04-05 00:33:47 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:33:47 --> hostname=localhost
DEBUG - 2015-04-05 00:33:47 --> username=root
DEBUG - 2015-04-05 00:33:47 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:33:47 --> database=cl51-democompa
DEBUG - 2015-04-05 00:33:47 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:33:47 --> dbprefix=
DEBUG - 2015-04-05 00:33:47 --> pconnect=1
DEBUG - 2015-04-05 00:33:47 --> db_debug=1
DEBUG - 2015-04-05 00:33:47 --> cache_on=
DEBUG - 2015-04-05 00:33:47 --> cachedir=
DEBUG - 2015-04-05 00:33:47 --> char_set=utf8
DEBUG - 2015-04-05 00:33:47 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:33:47 --> swap_pre=
DEBUG - 2015-04-05 00:33:47 --> autoinit=1
DEBUG - 2015-04-05 00:33:47 --> stricton=
DEBUG - 2015-04-05 00:33:47 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:33:48 --> XSS Filtering completed
DEBUG - 2015-04-05 00:33:48 --> Final output sent to browser
DEBUG - 2015-04-05 00:33:48 --> Total execution time: 3.1662
DEBUG - 2015-04-05 00:33:48 --> Controller Class Initialized
DEBUG - 2015-04-05 00:33:48 --> Model Class Initialized
DEBUG - 2015-04-05 00:33:48 --> Model Class Initialized
DEBUG - 2015-04-05 00:33:48 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:33:48 --> hostname=localhost
DEBUG - 2015-04-05 00:33:48 --> username=root
DEBUG - 2015-04-05 00:33:48 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:33:48 --> database=cl51-democompa
DEBUG - 2015-04-05 00:33:48 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:33:48 --> dbprefix=
DEBUG - 2015-04-05 00:33:48 --> pconnect=1
DEBUG - 2015-04-05 00:33:48 --> db_debug=1
DEBUG - 2015-04-05 00:33:48 --> cache_on=
DEBUG - 2015-04-05 00:33:48 --> cachedir=
DEBUG - 2015-04-05 00:33:48 --> char_set=utf8
DEBUG - 2015-04-05 00:33:48 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:33:48 --> swap_pre=
DEBUG - 2015-04-05 00:33:48 --> autoinit=1
DEBUG - 2015-04-05 00:33:48 --> stricton=
DEBUG - 2015-04-05 00:33:48 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:33:49 --> Final output sent to browser
DEBUG - 2015-04-05 00:33:49 --> Total execution time: 4.1592
DEBUG - 2015-04-05 00:34:11 --> Config Class Initialized
DEBUG - 2015-04-05 00:34:11 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:34:11 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:34:11 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:34:11 --> URI Class Initialized
DEBUG - 2015-04-05 00:34:11 --> Router Class Initialized
DEBUG - 2015-04-05 00:34:11 --> Output Class Initialized
DEBUG - 2015-04-05 00:34:11 --> Security Class Initialized
DEBUG - 2015-04-05 00:34:11 --> Input Class Initialized
DEBUG - 2015-04-05 00:34:11 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:34:11 --> Language Class Initialized
DEBUG - 2015-04-05 00:34:11 --> Loader Class Initialized
DEBUG - 2015-04-05 00:34:11 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:34:11 --> Controller Class Initialized
DEBUG - 2015-04-05 00:34:11 --> Model Class Initialized
DEBUG - 2015-04-05 00:34:11 --> Model Class Initialized
DEBUG - 2015-04-05 00:34:11 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:34:11 --> hostname=localhost
DEBUG - 2015-04-05 00:34:11 --> username=root
DEBUG - 2015-04-05 00:34:11 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:34:11 --> database=cl51-democompa
DEBUG - 2015-04-05 00:34:11 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:34:11 --> dbprefix=
DEBUG - 2015-04-05 00:34:11 --> pconnect=1
DEBUG - 2015-04-05 00:34:11 --> db_debug=1
DEBUG - 2015-04-05 00:34:11 --> cache_on=
DEBUG - 2015-04-05 00:34:11 --> cachedir=
DEBUG - 2015-04-05 00:34:11 --> char_set=utf8
DEBUG - 2015-04-05 00:34:11 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:34:11 --> swap_pre=
DEBUG - 2015-04-05 00:34:11 --> autoinit=1
DEBUG - 2015-04-05 00:34:11 --> stricton=
DEBUG - 2015-04-05 00:34:11 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:34:12 --> File loaded: ../application/views/header_nav.php
DEBUG - 2015-04-05 00:34:12 --> File loaded: ../application/views/hire_stock_new_item.php
DEBUG - 2015-04-05 00:34:12 --> File loaded: ../application/views/footer_common.php
DEBUG - 2015-04-05 00:34:12 --> File loaded: ../application/views/footer_copyright.php
DEBUG - 2015-04-05 00:34:12 --> File loaded: ../application/views/footer.php
DEBUG - 2015-04-05 00:34:12 --> Final output sent to browser
DEBUG - 2015-04-05 00:34:12 --> Total execution time: 1.0661
DEBUG - 2015-04-05 00:34:12 --> Config Class Initialized
DEBUG - 2015-04-05 00:34:12 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:34:12 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:34:12 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:34:12 --> URI Class Initialized
DEBUG - 2015-04-05 00:34:12 --> Router Class Initialized
DEBUG - 2015-04-05 00:34:12 --> Output Class Initialized
DEBUG - 2015-04-05 00:34:12 --> Security Class Initialized
DEBUG - 2015-04-05 00:34:12 --> Input Class Initialized
DEBUG - 2015-04-05 00:34:12 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:34:12 --> Language Class Initialized
DEBUG - 2015-04-05 00:34:12 --> Config Class Initialized
DEBUG - 2015-04-05 00:34:12 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:34:12 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:34:12 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:34:12 --> Loader Class Initialized
DEBUG - 2015-04-05 00:34:12 --> URI Class Initialized
DEBUG - 2015-04-05 00:34:12 --> Router Class Initialized
DEBUG - 2015-04-05 00:34:12 --> Output Class Initialized
DEBUG - 2015-04-05 00:34:12 --> Config Class Initialized
DEBUG - 2015-04-05 00:34:12 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:34:12 --> Security Class Initialized
DEBUG - 2015-04-05 00:34:12 --> Controller Class Initialized
DEBUG - 2015-04-05 00:34:12 --> Model Class Initialized
DEBUG - 2015-04-05 00:34:12 --> Model Class Initialized
DEBUG - 2015-04-05 00:34:12 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:34:12 --> hostname=localhost
DEBUG - 2015-04-05 00:34:12 --> username=root
DEBUG - 2015-04-05 00:34:12 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:34:12 --> database=cl51-democompa
DEBUG - 2015-04-05 00:34:12 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:34:12 --> dbprefix=
DEBUG - 2015-04-05 00:34:12 --> pconnect=1
DEBUG - 2015-04-05 00:34:12 --> db_debug=1
DEBUG - 2015-04-05 00:34:12 --> cache_on=
DEBUG - 2015-04-05 00:34:12 --> cachedir=
DEBUG - 2015-04-05 00:34:12 --> char_set=utf8
DEBUG - 2015-04-05 00:34:12 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:34:12 --> swap_pre=
DEBUG - 2015-04-05 00:34:12 --> autoinit=1
DEBUG - 2015-04-05 00:34:12 --> Input Class Initialized
DEBUG - 2015-04-05 00:34:12 --> stricton=
DEBUG - 2015-04-05 00:34:12 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:34:12 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:34:12 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:34:12 --> URI Class Initialized
DEBUG - 2015-04-05 00:34:12 --> Router Class Initialized
DEBUG - 2015-04-05 00:34:12 --> Output Class Initialized
DEBUG - 2015-04-05 00:34:12 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:34:12 --> Language Class Initialized
DEBUG - 2015-04-05 00:34:12 --> Loader Class Initialized
DEBUG - 2015-04-05 00:34:12 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:34:12 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:34:12 --> Security Class Initialized
DEBUG - 2015-04-05 00:34:12 --> Input Class Initialized
DEBUG - 2015-04-05 00:34:12 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:34:12 --> Language Class Initialized
DEBUG - 2015-04-05 00:34:12 --> Loader Class Initialized
DEBUG - 2015-04-05 00:34:12 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:34:13 --> Final output sent to browser
DEBUG - 2015-04-05 00:34:13 --> Total execution time: 1.0701
DEBUG - 2015-04-05 00:34:13 --> Controller Class Initialized
DEBUG - 2015-04-05 00:34:13 --> Model Class Initialized
DEBUG - 2015-04-05 00:34:13 --> Model Class Initialized
DEBUG - 2015-04-05 00:34:13 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:34:13 --> hostname=localhost
DEBUG - 2015-04-05 00:34:13 --> username=root
DEBUG - 2015-04-05 00:34:13 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:34:13 --> database=cl51-democompa
DEBUG - 2015-04-05 00:34:13 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:34:13 --> dbprefix=
DEBUG - 2015-04-05 00:34:13 --> pconnect=1
DEBUG - 2015-04-05 00:34:13 --> db_debug=1
DEBUG - 2015-04-05 00:34:13 --> cache_on=
DEBUG - 2015-04-05 00:34:13 --> cachedir=
DEBUG - 2015-04-05 00:34:13 --> char_set=utf8
DEBUG - 2015-04-05 00:34:13 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:34:13 --> swap_pre=
DEBUG - 2015-04-05 00:34:13 --> autoinit=1
DEBUG - 2015-04-05 00:34:13 --> stricton=
DEBUG - 2015-04-05 00:34:13 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:34:14 --> select mm.pk_id from messenger_messages as mm
					inner join messenger_users_messages as mum on mum.fk_message_id = mm.pk_id 
					and mum.`read` = 0 
					and mm.fk_destination_user = 1
					limit 1;
DEBUG - 2015-04-05 00:34:14 --> Final output sent to browser
DEBUG - 2015-04-05 00:34:14 --> Total execution time: 2.1091
DEBUG - 2015-04-05 00:34:14 --> Controller Class Initialized
DEBUG - 2015-04-05 00:34:14 --> Helper loaded: custom_validations_helper
DEBUG - 2015-04-05 00:34:14 --> Model Class Initialized
DEBUG - 2015-04-05 00:34:14 --> Model Class Initialized
DEBUG - 2015-04-05 00:34:14 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:34:14 --> hostname=localhost
DEBUG - 2015-04-05 00:34:14 --> username=root
DEBUG - 2015-04-05 00:34:14 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:34:14 --> database=cl51-democompa
DEBUG - 2015-04-05 00:34:14 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:34:14 --> dbprefix=
DEBUG - 2015-04-05 00:34:14 --> pconnect=1
DEBUG - 2015-04-05 00:34:14 --> db_debug=1
DEBUG - 2015-04-05 00:34:14 --> cache_on=
DEBUG - 2015-04-05 00:34:14 --> cachedir=
DEBUG - 2015-04-05 00:34:14 --> char_set=utf8
DEBUG - 2015-04-05 00:34:14 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:34:14 --> swap_pre=
DEBUG - 2015-04-05 00:34:14 --> autoinit=1
DEBUG - 2015-04-05 00:34:14 --> stricton=
DEBUG - 2015-04-05 00:34:14 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:34:15 --> XSS Filtering completed
DEBUG - 2015-04-05 00:34:15 --> Final output sent to browser
DEBUG - 2015-04-05 00:34:15 --> Total execution time: 3.1412
DEBUG - 2015-04-05 00:49:08 --> Config Class Initialized
DEBUG - 2015-04-05 00:49:08 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:49:08 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:49:08 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:49:08 --> URI Class Initialized
DEBUG - 2015-04-05 00:49:08 --> Router Class Initialized
DEBUG - 2015-04-05 00:49:08 --> Output Class Initialized
DEBUG - 2015-04-05 00:49:08 --> Security Class Initialized
DEBUG - 2015-04-05 00:49:08 --> Input Class Initialized
DEBUG - 2015-04-05 00:49:08 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:49:08 --> Language Class Initialized
DEBUG - 2015-04-05 00:49:08 --> Loader Class Initialized
DEBUG - 2015-04-05 00:49:08 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:49:08 --> Controller Class Initialized
DEBUG - 2015-04-05 00:49:08 --> Model Class Initialized
DEBUG - 2015-04-05 00:49:08 --> Model Class Initialized
DEBUG - 2015-04-05 00:49:08 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:49:08 --> hostname=localhost
DEBUG - 2015-04-05 00:49:08 --> username=root
DEBUG - 2015-04-05 00:49:08 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:49:08 --> database=cl51-democompa
DEBUG - 2015-04-05 00:49:08 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:49:08 --> dbprefix=
DEBUG - 2015-04-05 00:49:08 --> pconnect=1
DEBUG - 2015-04-05 00:49:08 --> db_debug=1
DEBUG - 2015-04-05 00:49:08 --> cache_on=
DEBUG - 2015-04-05 00:49:08 --> cachedir=
DEBUG - 2015-04-05 00:49:08 --> char_set=utf8
DEBUG - 2015-04-05 00:49:08 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:49:08 --> swap_pre=
DEBUG - 2015-04-05 00:49:08 --> autoinit=1
DEBUG - 2015-04-05 00:49:08 --> stricton=
DEBUG - 2015-04-05 00:49:08 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:49:09 --> File loaded: ../application/views/header_nav.php
DEBUG - 2015-04-05 00:49:09 --> File loaded: ../application/views/hire_stock_new_item.php
DEBUG - 2015-04-05 00:49:09 --> File loaded: ../application/views/footer_common.php
DEBUG - 2015-04-05 00:49:09 --> File loaded: ../application/views/footer_copyright.php
DEBUG - 2015-04-05 00:49:09 --> File loaded: ../application/views/footer.php
DEBUG - 2015-04-05 00:49:09 --> Final output sent to browser
DEBUG - 2015-04-05 00:49:09 --> Total execution time: 1.0661
DEBUG - 2015-04-05 00:49:09 --> Config Class Initialized
DEBUG - 2015-04-05 00:49:09 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:49:09 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:49:09 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:49:09 --> URI Class Initialized
DEBUG - 2015-04-05 00:49:09 --> Router Class Initialized
DEBUG - 2015-04-05 00:49:09 --> Config Class Initialized
DEBUG - 2015-04-05 00:49:09 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:49:09 --> Output Class Initialized
DEBUG - 2015-04-05 00:49:09 --> Security Class Initialized
DEBUG - 2015-04-05 00:49:09 --> Input Class Initialized
DEBUG - 2015-04-05 00:49:09 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:49:09 --> Config Class Initialized
DEBUG - 2015-04-05 00:49:09 --> Language Class Initialized
DEBUG - 2015-04-05 00:49:09 --> Loader Class Initialized
DEBUG - 2015-04-05 00:49:09 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:49:09 --> Controller Class Initialized
DEBUG - 2015-04-05 00:49:09 --> Model Class Initialized
DEBUG - 2015-04-05 00:49:09 --> Model Class Initialized
DEBUG - 2015-04-05 00:49:09 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:49:09 --> hostname=localhost
DEBUG - 2015-04-05 00:49:09 --> username=root
DEBUG - 2015-04-05 00:49:09 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:49:09 --> database=cl51-democompa
DEBUG - 2015-04-05 00:49:09 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:49:09 --> dbprefix=
DEBUG - 2015-04-05 00:49:09 --> pconnect=1
DEBUG - 2015-04-05 00:49:09 --> db_debug=1
DEBUG - 2015-04-05 00:49:09 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:49:09 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:49:09 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:49:09 --> URI Class Initialized
DEBUG - 2015-04-05 00:49:09 --> Router Class Initialized
DEBUG - 2015-04-05 00:49:09 --> Output Class Initialized
DEBUG - 2015-04-05 00:49:09 --> Security Class Initialized
DEBUG - 2015-04-05 00:49:09 --> Input Class Initialized
DEBUG - 2015-04-05 00:49:09 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:49:09 --> Language Class Initialized
DEBUG - 2015-04-05 00:49:09 --> Loader Class Initialized
DEBUG - 2015-04-05 00:49:09 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:49:09 --> cache_on=
DEBUG - 2015-04-05 00:49:09 --> cachedir=
DEBUG - 2015-04-05 00:49:09 --> char_set=utf8
DEBUG - 2015-04-05 00:49:09 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:49:09 --> swap_pre=
DEBUG - 2015-04-05 00:49:09 --> autoinit=1
DEBUG - 2015-04-05 00:49:09 --> stricton=
DEBUG - 2015-04-05 00:49:09 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:49:09 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:49:09 --> URI Class Initialized
DEBUG - 2015-04-05 00:49:09 --> Router Class Initialized
DEBUG - 2015-04-05 00:49:09 --> Output Class Initialized
DEBUG - 2015-04-05 00:49:09 --> Security Class Initialized
DEBUG - 2015-04-05 00:49:09 --> Input Class Initialized
DEBUG - 2015-04-05 00:49:09 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:49:09 --> Language Class Initialized
DEBUG - 2015-04-05 00:49:09 --> Loader Class Initialized
DEBUG - 2015-04-05 00:49:09 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:49:09 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:49:10 --> Final output sent to browser
DEBUG - 2015-04-05 00:49:10 --> Total execution time: 1.1041
DEBUG - 2015-04-05 00:49:10 --> Controller Class Initialized
DEBUG - 2015-04-05 00:49:10 --> Helper loaded: custom_validations_helper
DEBUG - 2015-04-05 00:49:10 --> Model Class Initialized
DEBUG - 2015-04-05 00:49:10 --> Model Class Initialized
DEBUG - 2015-04-05 00:49:10 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:49:10 --> hostname=localhost
DEBUG - 2015-04-05 00:49:10 --> username=root
DEBUG - 2015-04-05 00:49:10 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:49:10 --> database=cl51-democompa
DEBUG - 2015-04-05 00:49:10 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:49:10 --> dbprefix=
DEBUG - 2015-04-05 00:49:10 --> pconnect=1
DEBUG - 2015-04-05 00:49:10 --> db_debug=1
DEBUG - 2015-04-05 00:49:10 --> cache_on=
DEBUG - 2015-04-05 00:49:10 --> cachedir=
DEBUG - 2015-04-05 00:49:10 --> char_set=utf8
DEBUG - 2015-04-05 00:49:10 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:49:10 --> swap_pre=
DEBUG - 2015-04-05 00:49:10 --> autoinit=1
DEBUG - 2015-04-05 00:49:10 --> stricton=
DEBUG - 2015-04-05 00:49:10 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:49:11 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:11 --> Final output sent to browser
DEBUG - 2015-04-05 00:49:11 --> Total execution time: 2.1171
DEBUG - 2015-04-05 00:49:11 --> Controller Class Initialized
DEBUG - 2015-04-05 00:49:11 --> Model Class Initialized
DEBUG - 2015-04-05 00:49:11 --> Model Class Initialized
DEBUG - 2015-04-05 00:49:12 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:49:12 --> hostname=localhost
DEBUG - 2015-04-05 00:49:12 --> username=root
DEBUG - 2015-04-05 00:49:12 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:49:12 --> database=cl51-democompa
DEBUG - 2015-04-05 00:49:12 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:49:12 --> dbprefix=
DEBUG - 2015-04-05 00:49:12 --> pconnect=1
DEBUG - 2015-04-05 00:49:12 --> db_debug=1
DEBUG - 2015-04-05 00:49:12 --> cache_on=
DEBUG - 2015-04-05 00:49:12 --> cachedir=
DEBUG - 2015-04-05 00:49:12 --> char_set=utf8
DEBUG - 2015-04-05 00:49:12 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:49:12 --> swap_pre=
DEBUG - 2015-04-05 00:49:12 --> autoinit=1
DEBUG - 2015-04-05 00:49:12 --> stricton=
DEBUG - 2015-04-05 00:49:12 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:49:13 --> select mm.pk_id from messenger_messages as mm
					inner join messenger_users_messages as mum on mum.fk_message_id = mm.pk_id 
					and mum.`read` = 0 
					and mm.fk_destination_user = 1
					limit 1;
DEBUG - 2015-04-05 00:49:13 --> Final output sent to browser
DEBUG - 2015-04-05 00:49:13 --> Total execution time: 3.1552
DEBUG - 2015-04-05 00:49:36 --> Config Class Initialized
DEBUG - 2015-04-05 00:49:36 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:49:36 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:49:36 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:49:36 --> URI Class Initialized
DEBUG - 2015-04-05 00:49:36 --> Router Class Initialized
DEBUG - 2015-04-05 00:49:36 --> Output Class Initialized
DEBUG - 2015-04-05 00:49:36 --> Security Class Initialized
DEBUG - 2015-04-05 00:49:36 --> Input Class Initialized
DEBUG - 2015-04-05 00:49:36 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:49:36 --> Language Class Initialized
DEBUG - 2015-04-05 00:49:36 --> Loader Class Initialized
DEBUG - 2015-04-05 00:49:36 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:49:36 --> Controller Class Initialized
DEBUG - 2015-04-05 00:49:36 --> Session Class Initialized
DEBUG - 2015-04-05 00:49:36 --> Helper loaded: string_helper
DEBUG - 2015-04-05 00:49:36 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:49:38 --> Session routines successfully run
DEBUG - 2015-04-05 00:49:38 --> Helper loaded: form_helper
DEBUG - 2015-04-05 00:49:38 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:38 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:38 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:38 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:38 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:38 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:38 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:38 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:38 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:38 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:38 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:38 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:38 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:38 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:38 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:38 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:38 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:38 --> Model Class Initialized
DEBUG - 2015-04-05 00:49:38 --> Model Class Initialized
DEBUG - 2015-04-05 00:49:38 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:49:38 --> hostname=localhost
DEBUG - 2015-04-05 00:49:38 --> username=root
DEBUG - 2015-04-05 00:49:38 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:49:38 --> database=cl51-democompa
DEBUG - 2015-04-05 00:49:38 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:49:38 --> dbprefix=
DEBUG - 2015-04-05 00:49:38 --> pconnect=1
DEBUG - 2015-04-05 00:49:38 --> db_debug=1
DEBUG - 2015-04-05 00:49:38 --> cache_on=
DEBUG - 2015-04-05 00:49:38 --> cachedir=
DEBUG - 2015-04-05 00:49:38 --> char_set=utf8
DEBUG - 2015-04-05 00:49:38 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:49:38 --> swap_pre=
DEBUG - 2015-04-05 00:49:38 --> autoinit=1
DEBUG - 2015-04-05 00:49:38 --> stricton=
DEBUG - 2015-04-05 00:49:38 --> Database Driver Class Initialized
ERROR - 2015-04-05 00:49:39 --> Severity: Notice  --> Undefined index: cost_prite C:\inetpub\firedesk.co.uk\web\application\models\hire_stock_m.php 143
DEBUG - 2015-04-05 00:49:39 --> DB Transaction Failure
ERROR - 2015-04-05 00:49:39 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near '
					NULL,
					NULL,
					NULL,
					NULL,
					NULL,
					NULL,
					N' at line 27
DEBUG - 2015-04-05 00:49:39 --> Language file loaded: language/english/db_lang.php
ERROR - 2015-04-05 00:49:39 --> Severity: Warning  --> Cannot modify header information - headers already sent by (output started at C:\inetpub\firedesk.co.uk\web\system\core\Exceptions.php:185) C:\inetpub\firedesk.co.uk\web\system\core\Common.php 440
DEBUG - 2015-04-05 00:49:48 --> Config Class Initialized
DEBUG - 2015-04-05 00:49:48 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:49:48 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:49:48 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:49:48 --> URI Class Initialized
DEBUG - 2015-04-05 00:49:48 --> Router Class Initialized
DEBUG - 2015-04-05 00:49:48 --> Output Class Initialized
DEBUG - 2015-04-05 00:49:48 --> Security Class Initialized
DEBUG - 2015-04-05 00:49:48 --> Input Class Initialized
DEBUG - 2015-04-05 00:49:48 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:49:48 --> Language Class Initialized
DEBUG - 2015-04-05 00:49:48 --> Loader Class Initialized
DEBUG - 2015-04-05 00:49:48 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:49:48 --> Controller Class Initialized
DEBUG - 2015-04-05 00:49:48 --> Session Class Initialized
DEBUG - 2015-04-05 00:49:48 --> Helper loaded: string_helper
DEBUG - 2015-04-05 00:49:48 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:49:48 --> Config Class Initialized
DEBUG - 2015-04-05 00:49:48 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:49:48 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:49:48 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:49:48 --> URI Class Initialized
DEBUG - 2015-04-05 00:49:48 --> Router Class Initialized
DEBUG - 2015-04-05 00:49:48 --> Output Class Initialized
DEBUG - 2015-04-05 00:49:48 --> Security Class Initialized
DEBUG - 2015-04-05 00:49:48 --> Input Class Initialized
DEBUG - 2015-04-05 00:49:48 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:49:48 --> Language Class Initialized
DEBUG - 2015-04-05 00:49:48 --> Loader Class Initialized
DEBUG - 2015-04-05 00:49:48 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:49:49 --> Config Class Initialized
DEBUG - 2015-04-05 00:49:49 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:49:49 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:49:49 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:49:49 --> URI Class Initialized
DEBUG - 2015-04-05 00:49:49 --> Router Class Initialized
DEBUG - 2015-04-05 00:49:49 --> Output Class Initialized
DEBUG - 2015-04-05 00:49:49 --> Security Class Initialized
DEBUG - 2015-04-05 00:49:49 --> Input Class Initialized
DEBUG - 2015-04-05 00:49:49 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:49:49 --> Language Class Initialized
DEBUG - 2015-04-05 00:49:49 --> Loader Class Initialized
DEBUG - 2015-04-05 00:49:49 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:49:49 --> Session routines successfully run
DEBUG - 2015-04-05 00:49:49 --> Helper loaded: form_helper
DEBUG - 2015-04-05 00:49:49 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:49 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:49 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:49 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:49 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:49 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:49 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:49 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:49 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:49 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:49 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:49 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:49 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:49 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:49 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:49 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:49 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:49 --> Model Class Initialized
DEBUG - 2015-04-05 00:49:49 --> Model Class Initialized
DEBUG - 2015-04-05 00:49:49 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:49:49 --> hostname=localhost
DEBUG - 2015-04-05 00:49:49 --> username=root
DEBUG - 2015-04-05 00:49:49 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:49:49 --> database=cl51-democompa
DEBUG - 2015-04-05 00:49:49 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:49:49 --> dbprefix=
DEBUG - 2015-04-05 00:49:49 --> pconnect=1
DEBUG - 2015-04-05 00:49:49 --> db_debug=1
DEBUG - 2015-04-05 00:49:49 --> cache_on=
DEBUG - 2015-04-05 00:49:49 --> cachedir=
DEBUG - 2015-04-05 00:49:49 --> char_set=utf8
DEBUG - 2015-04-05 00:49:49 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:49:49 --> swap_pre=
DEBUG - 2015-04-05 00:49:49 --> autoinit=1
DEBUG - 2015-04-05 00:49:49 --> stricton=
DEBUG - 2015-04-05 00:49:49 --> Database Driver Class Initialized
ERROR - 2015-04-05 00:49:50 --> Severity: Notice  --> Undefined index: cost_prite C:\inetpub\firedesk.co.uk\web\application\models\hire_stock_m.php 143
DEBUG - 2015-04-05 00:49:50 --> DB Transaction Failure
ERROR - 2015-04-05 00:49:50 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near '
					NULL,
					NULL,
					NULL,
					NULL,
					NULL,
					NULL,
					N' at line 27
DEBUG - 2015-04-05 00:49:50 --> Language file loaded: language/english/db_lang.php
ERROR - 2015-04-05 00:49:50 --> Severity: Warning  --> Cannot modify header information - headers already sent by (output started at C:\inetpub\firedesk.co.uk\web\system\core\Exceptions.php:185) C:\inetpub\firedesk.co.uk\web\system\core\Common.php 440
DEBUG - 2015-04-05 00:49:50 --> Controller Class Initialized
DEBUG - 2015-04-05 00:49:51 --> Session Class Initialized
DEBUG - 2015-04-05 00:49:51 --> Helper loaded: string_helper
DEBUG - 2015-04-05 00:49:51 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:49:52 --> Session routines successfully run
DEBUG - 2015-04-05 00:49:52 --> Helper loaded: form_helper
DEBUG - 2015-04-05 00:49:52 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:52 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:52 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:52 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:52 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:52 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:52 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:52 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:52 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:52 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:52 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:52 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:52 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:52 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:52 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:52 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:52 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:52 --> Model Class Initialized
DEBUG - 2015-04-05 00:49:52 --> Model Class Initialized
DEBUG - 2015-04-05 00:49:52 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:49:52 --> hostname=localhost
DEBUG - 2015-04-05 00:49:52 --> username=root
DEBUG - 2015-04-05 00:49:52 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:49:52 --> database=cl51-democompa
DEBUG - 2015-04-05 00:49:52 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:49:52 --> dbprefix=
DEBUG - 2015-04-05 00:49:52 --> pconnect=1
DEBUG - 2015-04-05 00:49:52 --> db_debug=1
DEBUG - 2015-04-05 00:49:52 --> cache_on=
DEBUG - 2015-04-05 00:49:52 --> cachedir=
DEBUG - 2015-04-05 00:49:52 --> char_set=utf8
DEBUG - 2015-04-05 00:49:52 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:49:52 --> swap_pre=
DEBUG - 2015-04-05 00:49:52 --> autoinit=1
DEBUG - 2015-04-05 00:49:52 --> stricton=
DEBUG - 2015-04-05 00:49:52 --> Database Driver Class Initialized
ERROR - 2015-04-05 00:49:53 --> Severity: Notice  --> Undefined index: cost_prite C:\inetpub\firedesk.co.uk\web\application\models\hire_stock_m.php 143
DEBUG - 2015-04-05 00:49:53 --> DB Transaction Failure
ERROR - 2015-04-05 00:49:53 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near '
					NULL,
					NULL,
					NULL,
					NULL,
					NULL,
					NULL,
					N' at line 27
DEBUG - 2015-04-05 00:49:53 --> Language file loaded: language/english/db_lang.php
ERROR - 2015-04-05 00:49:53 --> Severity: Warning  --> Cannot modify header information - headers already sent by (output started at C:\inetpub\firedesk.co.uk\web\system\core\Exceptions.php:185) C:\inetpub\firedesk.co.uk\web\system\core\Common.php 440
DEBUG - 2015-04-05 00:49:53 --> Controller Class Initialized
DEBUG - 2015-04-05 00:49:53 --> Session Class Initialized
DEBUG - 2015-04-05 00:49:53 --> Helper loaded: string_helper
DEBUG - 2015-04-05 00:49:53 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:49:54 --> Session routines successfully run
DEBUG - 2015-04-05 00:49:54 --> Helper loaded: form_helper
DEBUG - 2015-04-05 00:49:54 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:54 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:54 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:54 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:54 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:54 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:54 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:54 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:54 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:54 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:54 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:54 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:54 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:54 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:54 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:54 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:54 --> XSS Filtering completed
DEBUG - 2015-04-05 00:49:54 --> Model Class Initialized
DEBUG - 2015-04-05 00:49:54 --> Model Class Initialized
DEBUG - 2015-04-05 00:49:54 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:49:54 --> hostname=localhost
DEBUG - 2015-04-05 00:49:54 --> username=root
DEBUG - 2015-04-05 00:49:54 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:49:54 --> database=cl51-democompa
DEBUG - 2015-04-05 00:49:54 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:49:54 --> dbprefix=
DEBUG - 2015-04-05 00:49:54 --> pconnect=1
DEBUG - 2015-04-05 00:49:54 --> db_debug=1
DEBUG - 2015-04-05 00:49:54 --> cache_on=
DEBUG - 2015-04-05 00:49:54 --> cachedir=
DEBUG - 2015-04-05 00:49:54 --> char_set=utf8
DEBUG - 2015-04-05 00:49:54 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:49:54 --> swap_pre=
DEBUG - 2015-04-05 00:49:54 --> autoinit=1
DEBUG - 2015-04-05 00:49:54 --> stricton=
DEBUG - 2015-04-05 00:49:54 --> Database Driver Class Initialized
ERROR - 2015-04-05 00:49:55 --> Severity: Notice  --> Undefined index: cost_prite C:\inetpub\firedesk.co.uk\web\application\models\hire_stock_m.php 143
DEBUG - 2015-04-05 00:49:55 --> DB Transaction Failure
ERROR - 2015-04-05 00:49:55 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near '
					NULL,
					NULL,
					NULL,
					NULL,
					NULL,
					NULL,
					N' at line 27
DEBUG - 2015-04-05 00:49:55 --> Language file loaded: language/english/db_lang.php
ERROR - 2015-04-05 00:49:55 --> Severity: Warning  --> Cannot modify header information - headers already sent by (output started at C:\inetpub\firedesk.co.uk\web\system\core\Exceptions.php:185) C:\inetpub\firedesk.co.uk\web\system\core\Common.php 440
DEBUG - 2015-04-05 00:51:25 --> Config Class Initialized
DEBUG - 2015-04-05 00:51:25 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:51:25 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:51:25 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:51:25 --> URI Class Initialized
DEBUG - 2015-04-05 00:51:25 --> Router Class Initialized
DEBUG - 2015-04-05 00:51:25 --> Output Class Initialized
DEBUG - 2015-04-05 00:51:25 --> Security Class Initialized
DEBUG - 2015-04-05 00:51:25 --> Input Class Initialized
DEBUG - 2015-04-05 00:51:25 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:51:25 --> Language Class Initialized
DEBUG - 2015-04-05 00:51:25 --> Loader Class Initialized
DEBUG - 2015-04-05 00:51:25 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:51:25 --> Controller Class Initialized
DEBUG - 2015-04-05 00:51:25 --> Session Class Initialized
DEBUG - 2015-04-05 00:51:25 --> Helper loaded: string_helper
DEBUG - 2015-04-05 00:51:25 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:51:26 --> Session routines successfully run
DEBUG - 2015-04-05 00:51:26 --> Helper loaded: form_helper
DEBUG - 2015-04-05 00:51:26 --> XSS Filtering completed
DEBUG - 2015-04-05 00:51:26 --> XSS Filtering completed
DEBUG - 2015-04-05 00:51:26 --> XSS Filtering completed
DEBUG - 2015-04-05 00:51:26 --> XSS Filtering completed
DEBUG - 2015-04-05 00:51:26 --> XSS Filtering completed
DEBUG - 2015-04-05 00:51:26 --> XSS Filtering completed
DEBUG - 2015-04-05 00:51:26 --> XSS Filtering completed
DEBUG - 2015-04-05 00:51:26 --> XSS Filtering completed
DEBUG - 2015-04-05 00:51:26 --> XSS Filtering completed
DEBUG - 2015-04-05 00:51:26 --> XSS Filtering completed
DEBUG - 2015-04-05 00:51:26 --> XSS Filtering completed
DEBUG - 2015-04-05 00:51:26 --> XSS Filtering completed
DEBUG - 2015-04-05 00:51:26 --> XSS Filtering completed
DEBUG - 2015-04-05 00:51:26 --> XSS Filtering completed
DEBUG - 2015-04-05 00:51:26 --> XSS Filtering completed
DEBUG - 2015-04-05 00:51:26 --> XSS Filtering completed
DEBUG - 2015-04-05 00:51:26 --> XSS Filtering completed
DEBUG - 2015-04-05 00:51:26 --> Model Class Initialized
DEBUG - 2015-04-05 00:51:26 --> Model Class Initialized
DEBUG - 2015-04-05 00:51:26 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:51:26 --> hostname=localhost
DEBUG - 2015-04-05 00:51:26 --> username=root
DEBUG - 2015-04-05 00:51:26 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:51:26 --> database=cl51-democompa
DEBUG - 2015-04-05 00:51:26 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:51:26 --> dbprefix=
DEBUG - 2015-04-05 00:51:26 --> pconnect=1
DEBUG - 2015-04-05 00:51:26 --> db_debug=1
DEBUG - 2015-04-05 00:51:26 --> cache_on=
DEBUG - 2015-04-05 00:51:26 --> cachedir=
DEBUG - 2015-04-05 00:51:26 --> char_set=utf8
DEBUG - 2015-04-05 00:51:26 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:51:26 --> swap_pre=
DEBUG - 2015-04-05 00:51:26 --> autoinit=1
DEBUG - 2015-04-05 00:51:26 --> stricton=
DEBUG - 2015-04-05 00:51:26 --> Database Driver Class Initialized
ERROR - 2015-04-05 00:51:27 --> Severity: Notice  --> Undefined index: cost_prite C:\inetpub\firedesk.co.uk\web\application\models\hire_stock_m.php 143
DEBUG - 2015-04-05 00:51:27 --> DB Transaction Failure
ERROR - 2015-04-05 00:51:27 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near '
					NULL,
					NULL,
					NULL,
					NULL,
					NULL,
					NULL,
					N' at line 27
DEBUG - 2015-04-05 00:51:27 --> Language file loaded: language/english/db_lang.php
ERROR - 2015-04-05 00:51:27 --> Severity: Warning  --> Cannot modify header information - headers already sent by (output started at C:\inetpub\firedesk.co.uk\web\system\core\Exceptions.php:185) C:\inetpub\firedesk.co.uk\web\system\core\Common.php 440
DEBUG - 2015-04-05 00:51:38 --> Config Class Initialized
DEBUG - 2015-04-05 00:51:38 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:51:38 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:51:38 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:51:38 --> URI Class Initialized
DEBUG - 2015-04-05 00:51:38 --> Router Class Initialized
DEBUG - 2015-04-05 00:51:38 --> Output Class Initialized
DEBUG - 2015-04-05 00:51:38 --> Security Class Initialized
DEBUG - 2015-04-05 00:51:38 --> Input Class Initialized
DEBUG - 2015-04-05 00:51:38 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:51:38 --> Language Class Initialized
DEBUG - 2015-04-05 00:51:38 --> Loader Class Initialized
DEBUG - 2015-04-05 00:51:38 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:51:38 --> Controller Class Initialized
DEBUG - 2015-04-05 00:51:38 --> Session Class Initialized
DEBUG - 2015-04-05 00:51:38 --> Helper loaded: string_helper
DEBUG - 2015-04-05 00:51:38 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:51:39 --> Session garbage collection performed.
DEBUG - 2015-04-05 00:51:39 --> Session routines successfully run
DEBUG - 2015-04-05 00:51:39 --> Helper loaded: form_helper
DEBUG - 2015-04-05 00:51:39 --> XSS Filtering completed
DEBUG - 2015-04-05 00:51:39 --> XSS Filtering completed
DEBUG - 2015-04-05 00:51:39 --> XSS Filtering completed
DEBUG - 2015-04-05 00:51:39 --> XSS Filtering completed
DEBUG - 2015-04-05 00:51:39 --> XSS Filtering completed
DEBUG - 2015-04-05 00:51:39 --> XSS Filtering completed
DEBUG - 2015-04-05 00:51:39 --> XSS Filtering completed
DEBUG - 2015-04-05 00:51:39 --> XSS Filtering completed
DEBUG - 2015-04-05 00:51:39 --> XSS Filtering completed
DEBUG - 2015-04-05 00:51:39 --> XSS Filtering completed
DEBUG - 2015-04-05 00:51:39 --> XSS Filtering completed
DEBUG - 2015-04-05 00:51:39 --> XSS Filtering completed
DEBUG - 2015-04-05 00:51:39 --> XSS Filtering completed
DEBUG - 2015-04-05 00:51:39 --> XSS Filtering completed
DEBUG - 2015-04-05 00:51:39 --> XSS Filtering completed
DEBUG - 2015-04-05 00:51:39 --> XSS Filtering completed
DEBUG - 2015-04-05 00:51:39 --> XSS Filtering completed
DEBUG - 2015-04-05 00:51:39 --> Model Class Initialized
DEBUG - 2015-04-05 00:51:39 --> Model Class Initialized
DEBUG - 2015-04-05 00:51:39 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:51:39 --> hostname=localhost
DEBUG - 2015-04-05 00:51:39 --> username=root
DEBUG - 2015-04-05 00:51:39 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:51:39 --> database=cl51-democompa
DEBUG - 2015-04-05 00:51:39 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:51:39 --> dbprefix=
DEBUG - 2015-04-05 00:51:39 --> pconnect=1
DEBUG - 2015-04-05 00:51:39 --> db_debug=1
DEBUG - 2015-04-05 00:51:39 --> cache_on=
DEBUG - 2015-04-05 00:51:39 --> cachedir=
DEBUG - 2015-04-05 00:51:39 --> char_set=utf8
DEBUG - 2015-04-05 00:51:39 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:51:39 --> swap_pre=
DEBUG - 2015-04-05 00:51:39 --> autoinit=1
DEBUG - 2015-04-05 00:51:39 --> stricton=
DEBUG - 2015-04-05 00:51:39 --> Database Driver Class Initialized
ERROR - 2015-04-05 00:51:40 --> Severity: Notice  --> Undefined index: cost_prite C:\inetpub\firedesk.co.uk\web\application\models\hire_stock_m.php 143
DEBUG - 2015-04-05 00:51:40 --> DB Transaction Failure
ERROR - 2015-04-05 00:51:40 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near '
					NULL,
					NULL,
					NULL,
					NULL,
					NULL,
					NULL,
					N' at line 27
DEBUG - 2015-04-05 00:51:40 --> Language file loaded: language/english/db_lang.php
ERROR - 2015-04-05 00:51:40 --> Severity: Warning  --> Cannot modify header information - headers already sent by (output started at C:\inetpub\firedesk.co.uk\web\system\core\Exceptions.php:185) C:\inetpub\firedesk.co.uk\web\system\core\Common.php 440
DEBUG - 2015-04-05 00:52:00 --> Config Class Initialized
DEBUG - 2015-04-05 00:52:00 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:52:00 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:52:00 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:52:00 --> URI Class Initialized
DEBUG - 2015-04-05 00:52:00 --> Router Class Initialized
DEBUG - 2015-04-05 00:52:00 --> Output Class Initialized
DEBUG - 2015-04-05 00:52:00 --> Security Class Initialized
DEBUG - 2015-04-05 00:52:00 --> Input Class Initialized
DEBUG - 2015-04-05 00:52:00 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:52:00 --> Language Class Initialized
DEBUG - 2015-04-05 00:52:00 --> Loader Class Initialized
DEBUG - 2015-04-05 00:52:00 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:52:00 --> Controller Class Initialized
DEBUG - 2015-04-05 00:52:00 --> Session Class Initialized
DEBUG - 2015-04-05 00:52:00 --> Helper loaded: string_helper
DEBUG - 2015-04-05 00:52:00 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:52:01 --> Session routines successfully run
DEBUG - 2015-04-05 00:52:01 --> Helper loaded: form_helper
DEBUG - 2015-04-05 00:52:01 --> XSS Filtering completed
DEBUG - 2015-04-05 00:52:01 --> XSS Filtering completed
DEBUG - 2015-04-05 00:52:01 --> XSS Filtering completed
DEBUG - 2015-04-05 00:52:01 --> XSS Filtering completed
DEBUG - 2015-04-05 00:52:01 --> XSS Filtering completed
DEBUG - 2015-04-05 00:52:01 --> XSS Filtering completed
DEBUG - 2015-04-05 00:52:01 --> XSS Filtering completed
DEBUG - 2015-04-05 00:52:01 --> XSS Filtering completed
DEBUG - 2015-04-05 00:52:01 --> XSS Filtering completed
DEBUG - 2015-04-05 00:52:01 --> XSS Filtering completed
DEBUG - 2015-04-05 00:52:01 --> XSS Filtering completed
DEBUG - 2015-04-05 00:52:01 --> XSS Filtering completed
DEBUG - 2015-04-05 00:52:01 --> XSS Filtering completed
DEBUG - 2015-04-05 00:52:01 --> XSS Filtering completed
DEBUG - 2015-04-05 00:52:01 --> XSS Filtering completed
DEBUG - 2015-04-05 00:52:01 --> XSS Filtering completed
DEBUG - 2015-04-05 00:52:01 --> XSS Filtering completed
DEBUG - 2015-04-05 00:52:01 --> Model Class Initialized
DEBUG - 2015-04-05 00:52:01 --> Model Class Initialized
DEBUG - 2015-04-05 00:52:01 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:52:01 --> hostname=localhost
DEBUG - 2015-04-05 00:52:01 --> username=root
DEBUG - 2015-04-05 00:52:01 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:52:01 --> database=cl51-democompa
DEBUG - 2015-04-05 00:52:01 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:52:01 --> dbprefix=
DEBUG - 2015-04-05 00:52:01 --> pconnect=1
DEBUG - 2015-04-05 00:52:01 --> db_debug=1
DEBUG - 2015-04-05 00:52:01 --> cache_on=
DEBUG - 2015-04-05 00:52:01 --> cachedir=
DEBUG - 2015-04-05 00:52:01 --> char_set=utf8
DEBUG - 2015-04-05 00:52:01 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:52:01 --> swap_pre=
DEBUG - 2015-04-05 00:52:01 --> autoinit=1
DEBUG - 2015-04-05 00:52:01 --> stricton=
DEBUG - 2015-04-05 00:52:01 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:52:02 --> Final output sent to browser
DEBUG - 2015-04-05 00:52:02 --> Total execution time: 2.2341
DEBUG - 2015-04-05 00:52:03 --> Config Class Initialized
DEBUG - 2015-04-05 00:52:03 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:52:03 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:52:03 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:52:03 --> URI Class Initialized
DEBUG - 2015-04-05 00:52:03 --> Router Class Initialized
DEBUG - 2015-04-05 00:52:03 --> Output Class Initialized
DEBUG - 2015-04-05 00:52:03 --> Security Class Initialized
DEBUG - 2015-04-05 00:52:03 --> Input Class Initialized
DEBUG - 2015-04-05 00:52:03 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:52:03 --> Language Class Initialized
DEBUG - 2015-04-05 00:52:03 --> Loader Class Initialized
DEBUG - 2015-04-05 00:52:03 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:52:03 --> Controller Class Initialized
DEBUG - 2015-04-05 00:52:03 --> File loaded: ../application/views/header_nav.php
DEBUG - 2015-04-05 00:52:03 --> File loaded: ../application/views/hire_stock_fleet_records.php
DEBUG - 2015-04-05 00:52:03 --> File loaded: ../application/views/footer_common.php
DEBUG - 2015-04-05 00:52:03 --> File loaded: ../application/views/footer_copyright.php
DEBUG - 2015-04-05 00:52:03 --> File loaded: ../application/views/footer.php
DEBUG - 2015-04-05 00:52:03 --> Final output sent to browser
DEBUG - 2015-04-05 00:52:03 --> Total execution time: 0.0360
DEBUG - 2015-04-05 00:52:04 --> Config Class Initialized
DEBUG - 2015-04-05 00:52:04 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:52:04 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:52:04 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:52:04 --> URI Class Initialized
DEBUG - 2015-04-05 00:52:04 --> Router Class Initialized
DEBUG - 2015-04-05 00:52:04 --> Output Class Initialized
DEBUG - 2015-04-05 00:52:04 --> Security Class Initialized
DEBUG - 2015-04-05 00:52:04 --> Input Class Initialized
DEBUG - 2015-04-05 00:52:04 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:52:04 --> Language Class Initialized
DEBUG - 2015-04-05 00:52:04 --> Config Class Initialized
DEBUG - 2015-04-05 00:52:04 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:52:04 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:52:04 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:52:04 --> URI Class Initialized
DEBUG - 2015-04-05 00:52:04 --> Router Class Initialized
DEBUG - 2015-04-05 00:52:04 --> Output Class Initialized
DEBUG - 2015-04-05 00:52:04 --> Security Class Initialized
DEBUG - 2015-04-05 00:52:04 --> Input Class Initialized
DEBUG - 2015-04-05 00:52:04 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:52:04 --> Language Class Initialized
DEBUG - 2015-04-05 00:52:04 --> Loader Class Initialized
DEBUG - 2015-04-05 00:52:04 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:52:04 --> Controller Class Initialized
DEBUG - 2015-04-05 00:52:04 --> Model Class Initialized
DEBUG - 2015-04-05 00:52:04 --> Model Class Initialized
DEBUG - 2015-04-05 00:52:04 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:52:04 --> hostname=localhost
DEBUG - 2015-04-05 00:52:04 --> username=root
DEBUG - 2015-04-05 00:52:04 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:52:04 --> database=cl51-democompa
DEBUG - 2015-04-05 00:52:04 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:52:04 --> dbprefix=
DEBUG - 2015-04-05 00:52:04 --> pconnect=1
DEBUG - 2015-04-05 00:52:04 --> db_debug=1
DEBUG - 2015-04-05 00:52:04 --> cache_on=
DEBUG - 2015-04-05 00:52:04 --> cachedir=
DEBUG - 2015-04-05 00:52:04 --> char_set=utf8
DEBUG - 2015-04-05 00:52:04 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:52:04 --> swap_pre=
DEBUG - 2015-04-05 00:52:04 --> autoinit=1
DEBUG - 2015-04-05 00:52:04 --> stricton=
DEBUG - 2015-04-05 00:52:04 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:52:04 --> Config Class Initialized
DEBUG - 2015-04-05 00:52:04 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:52:04 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:52:04 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:52:04 --> URI Class Initialized
DEBUG - 2015-04-05 00:52:04 --> Router Class Initialized
DEBUG - 2015-04-05 00:52:04 --> Output Class Initialized
DEBUG - 2015-04-05 00:52:04 --> Security Class Initialized
DEBUG - 2015-04-05 00:52:04 --> Input Class Initialized
DEBUG - 2015-04-05 00:52:04 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:52:04 --> Language Class Initialized
DEBUG - 2015-04-05 00:52:04 --> Loader Class Initialized
DEBUG - 2015-04-05 00:52:04 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:52:04 --> Loader Class Initialized
DEBUG - 2015-04-05 00:52:04 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:52:04 --> Config Class Initialized
DEBUG - 2015-04-05 00:52:04 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:52:04 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:52:04 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:52:04 --> URI Class Initialized
DEBUG - 2015-04-05 00:52:04 --> Router Class Initialized
DEBUG - 2015-04-05 00:52:04 --> Output Class Initialized
DEBUG - 2015-04-05 00:52:04 --> Security Class Initialized
DEBUG - 2015-04-05 00:52:04 --> Input Class Initialized
DEBUG - 2015-04-05 00:52:04 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:52:04 --> Language Class Initialized
DEBUG - 2015-04-05 00:52:04 --> Loader Class Initialized
DEBUG - 2015-04-05 00:52:04 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:52:05 --> select mm.pk_id from messenger_messages as mm
					inner join messenger_users_messages as mum on mum.fk_message_id = mm.pk_id 
					and mum.`read` = 0 
					and mm.fk_destination_user = 1
					limit 1;
DEBUG - 2015-04-05 00:52:05 --> Final output sent to browser
DEBUG - 2015-04-05 00:52:05 --> Total execution time: 1.1451
DEBUG - 2015-04-05 00:52:05 --> Controller Class Initialized
DEBUG - 2015-04-05 00:52:05 --> Helper loaded: custom_validations_helper
DEBUG - 2015-04-05 00:52:05 --> Model Class Initialized
DEBUG - 2015-04-05 00:52:05 --> Model Class Initialized
DEBUG - 2015-04-05 00:52:05 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:52:05 --> hostname=localhost
DEBUG - 2015-04-05 00:52:05 --> username=root
DEBUG - 2015-04-05 00:52:05 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:52:05 --> database=cl51-democompa
DEBUG - 2015-04-05 00:52:05 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:52:05 --> dbprefix=
DEBUG - 2015-04-05 00:52:05 --> pconnect=1
DEBUG - 2015-04-05 00:52:05 --> db_debug=1
DEBUG - 2015-04-05 00:52:05 --> cache_on=
DEBUG - 2015-04-05 00:52:05 --> cachedir=
DEBUG - 2015-04-05 00:52:05 --> char_set=utf8
DEBUG - 2015-04-05 00:52:05 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:52:05 --> swap_pre=
DEBUG - 2015-04-05 00:52:05 --> autoinit=1
DEBUG - 2015-04-05 00:52:05 --> stricton=
DEBUG - 2015-04-05 00:52:05 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:52:06 --> XSS Filtering completed
DEBUG - 2015-04-05 00:52:06 --> Final output sent to browser
DEBUG - 2015-04-05 00:52:06 --> Total execution time: 2.1301
DEBUG - 2015-04-05 00:52:06 --> Controller Class Initialized
DEBUG - 2015-04-05 00:52:06 --> Model Class Initialized
DEBUG - 2015-04-05 00:52:06 --> Model Class Initialized
DEBUG - 2015-04-05 00:52:06 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:52:06 --> hostname=localhost
DEBUG - 2015-04-05 00:52:06 --> username=root
DEBUG - 2015-04-05 00:52:06 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:52:06 --> database=cl51-democompa
DEBUG - 2015-04-05 00:52:06 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:52:06 --> dbprefix=
DEBUG - 2015-04-05 00:52:06 --> pconnect=1
DEBUG - 2015-04-05 00:52:06 --> db_debug=1
DEBUG - 2015-04-05 00:52:06 --> cache_on=
DEBUG - 2015-04-05 00:52:06 --> cachedir=
DEBUG - 2015-04-05 00:52:06 --> char_set=utf8
DEBUG - 2015-04-05 00:52:06 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:52:06 --> swap_pre=
DEBUG - 2015-04-05 00:52:06 --> autoinit=1
DEBUG - 2015-04-05 00:52:06 --> stricton=
DEBUG - 2015-04-05 00:52:06 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:52:07 --> Final output sent to browser
DEBUG - 2015-04-05 00:52:07 --> Total execution time: 3.2602
DEBUG - 2015-04-05 00:52:07 --> Controller Class Initialized
DEBUG - 2015-04-05 00:52:07 --> Model Class Initialized
DEBUG - 2015-04-05 00:52:07 --> Model Class Initialized
DEBUG - 2015-04-05 00:52:07 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:52:07 --> hostname=localhost
DEBUG - 2015-04-05 00:52:07 --> username=root
DEBUG - 2015-04-05 00:52:07 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:52:07 --> database=cl51-democompa
DEBUG - 2015-04-05 00:52:07 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:52:07 --> dbprefix=
DEBUG - 2015-04-05 00:52:07 --> pconnect=1
DEBUG - 2015-04-05 00:52:07 --> db_debug=1
DEBUG - 2015-04-05 00:52:07 --> cache_on=
DEBUG - 2015-04-05 00:52:07 --> cachedir=
DEBUG - 2015-04-05 00:52:07 --> char_set=utf8
DEBUG - 2015-04-05 00:52:07 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:52:07 --> swap_pre=
DEBUG - 2015-04-05 00:52:07 --> autoinit=1
DEBUG - 2015-04-05 00:52:07 --> stricton=
DEBUG - 2015-04-05 00:52:07 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:52:08 --> Final output sent to browser
DEBUG - 2015-04-05 00:52:08 --> Total execution time: 4.1772
DEBUG - 2015-04-05 00:52:58 --> Config Class Initialized
DEBUG - 2015-04-05 00:52:58 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:52:58 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:52:58 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:52:58 --> URI Class Initialized
DEBUG - 2015-04-05 00:52:58 --> Router Class Initialized
DEBUG - 2015-04-05 00:52:58 --> Output Class Initialized
DEBUG - 2015-04-05 00:52:58 --> Security Class Initialized
DEBUG - 2015-04-05 00:52:58 --> Input Class Initialized
DEBUG - 2015-04-05 00:52:58 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:52:58 --> Language Class Initialized
DEBUG - 2015-04-05 00:52:58 --> Loader Class Initialized
DEBUG - 2015-04-05 00:52:58 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:52:58 --> Controller Class Initialized
DEBUG - 2015-04-05 00:52:58 --> Model Class Initialized
DEBUG - 2015-04-05 00:52:58 --> Model Class Initialized
DEBUG - 2015-04-05 00:52:58 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:52:58 --> hostname=localhost
DEBUG - 2015-04-05 00:52:58 --> username=root
DEBUG - 2015-04-05 00:52:58 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:52:58 --> database=cl51-democompa
DEBUG - 2015-04-05 00:52:58 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:52:58 --> dbprefix=
DEBUG - 2015-04-05 00:52:58 --> pconnect=1
DEBUG - 2015-04-05 00:52:58 --> db_debug=1
DEBUG - 2015-04-05 00:52:58 --> cache_on=
DEBUG - 2015-04-05 00:52:58 --> cachedir=
DEBUG - 2015-04-05 00:52:58 --> char_set=utf8
DEBUG - 2015-04-05 00:52:58 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:52:58 --> swap_pre=
DEBUG - 2015-04-05 00:52:58 --> autoinit=1
DEBUG - 2015-04-05 00:52:58 --> stricton=
DEBUG - 2015-04-05 00:52:58 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:52:59 --> File loaded: ../application/views/header_nav.php
DEBUG - 2015-04-05 00:52:59 --> File loaded: ../application/views/hire_stock_new_item.php
DEBUG - 2015-04-05 00:52:59 --> File loaded: ../application/views/footer_common.php
DEBUG - 2015-04-05 00:52:59 --> File loaded: ../application/views/footer_copyright.php
DEBUG - 2015-04-05 00:52:59 --> File loaded: ../application/views/footer.php
DEBUG - 2015-04-05 00:52:59 --> Final output sent to browser
DEBUG - 2015-04-05 00:52:59 --> Total execution time: 1.0671
DEBUG - 2015-04-05 00:53:00 --> Config Class Initialized
DEBUG - 2015-04-05 00:53:00 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:53:00 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:53:00 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:53:00 --> URI Class Initialized
DEBUG - 2015-04-05 00:53:00 --> Router Class Initialized
DEBUG - 2015-04-05 00:53:00 --> Output Class Initialized
DEBUG - 2015-04-05 00:53:00 --> Security Class Initialized
DEBUG - 2015-04-05 00:53:00 --> Input Class Initialized
DEBUG - 2015-04-05 00:53:00 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:53:00 --> Language Class Initialized
DEBUG - 2015-04-05 00:53:00 --> Loader Class Initialized
DEBUG - 2015-04-05 00:53:00 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:53:00 --> Controller Class Initialized
DEBUG - 2015-04-05 00:53:00 --> Model Class Initialized
DEBUG - 2015-04-05 00:53:00 --> Model Class Initialized
DEBUG - 2015-04-05 00:53:00 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:53:00 --> hostname=localhost
DEBUG - 2015-04-05 00:53:00 --> username=root
DEBUG - 2015-04-05 00:53:00 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:53:00 --> database=cl51-democompa
DEBUG - 2015-04-05 00:53:00 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:53:00 --> dbprefix=
DEBUG - 2015-04-05 00:53:00 --> pconnect=1
DEBUG - 2015-04-05 00:53:00 --> db_debug=1
DEBUG - 2015-04-05 00:53:00 --> cache_on=
DEBUG - 2015-04-05 00:53:00 --> cachedir=
DEBUG - 2015-04-05 00:53:00 --> char_set=utf8
DEBUG - 2015-04-05 00:53:00 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:53:00 --> swap_pre=
DEBUG - 2015-04-05 00:53:00 --> autoinit=1
DEBUG - 2015-04-05 00:53:00 --> stricton=
DEBUG - 2015-04-05 00:53:00 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:53:00 --> Config Class Initialized
DEBUG - 2015-04-05 00:53:00 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:53:00 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:53:00 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:53:00 --> URI Class Initialized
DEBUG - 2015-04-05 00:53:00 --> Router Class Initialized
DEBUG - 2015-04-05 00:53:00 --> Output Class Initialized
DEBUG - 2015-04-05 00:53:00 --> Security Class Initialized
DEBUG - 2015-04-05 00:53:00 --> Input Class Initialized
DEBUG - 2015-04-05 00:53:00 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:53:00 --> Language Class Initialized
DEBUG - 2015-04-05 00:53:00 --> Loader Class Initialized
DEBUG - 2015-04-05 00:53:00 --> Config Class Initialized
DEBUG - 2015-04-05 00:53:00 --> Hooks Class Initialized
DEBUG - 2015-04-05 00:53:00 --> Utf8 Class Initialized
DEBUG - 2015-04-05 00:53:00 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 00:53:00 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:53:00 --> URI Class Initialized
DEBUG - 2015-04-05 00:53:00 --> Router Class Initialized
DEBUG - 2015-04-05 00:53:00 --> Output Class Initialized
DEBUG - 2015-04-05 00:53:00 --> Security Class Initialized
DEBUG - 2015-04-05 00:53:00 --> Input Class Initialized
DEBUG - 2015-04-05 00:53:00 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 00:53:00 --> Language Class Initialized
DEBUG - 2015-04-05 00:53:00 --> Loader Class Initialized
DEBUG - 2015-04-05 00:53:00 --> Helper loaded: url_helper
DEBUG - 2015-04-05 00:53:01 --> Final output sent to browser
DEBUG - 2015-04-05 00:53:01 --> Total execution time: 1.1011
DEBUG - 2015-04-05 00:53:01 --> Controller Class Initialized
DEBUG - 2015-04-05 00:53:01 --> Model Class Initialized
DEBUG - 2015-04-05 00:53:01 --> Model Class Initialized
DEBUG - 2015-04-05 00:53:01 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:53:01 --> hostname=localhost
DEBUG - 2015-04-05 00:53:01 --> username=root
DEBUG - 2015-04-05 00:53:01 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:53:01 --> database=cl51-democompa
DEBUG - 2015-04-05 00:53:01 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:53:01 --> dbprefix=
DEBUG - 2015-04-05 00:53:01 --> pconnect=1
DEBUG - 2015-04-05 00:53:01 --> db_debug=1
DEBUG - 2015-04-05 00:53:01 --> cache_on=
DEBUG - 2015-04-05 00:53:01 --> cachedir=
DEBUG - 2015-04-05 00:53:01 --> char_set=utf8
DEBUG - 2015-04-05 00:53:01 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:53:01 --> swap_pre=
DEBUG - 2015-04-05 00:53:01 --> autoinit=1
DEBUG - 2015-04-05 00:53:01 --> stricton=
DEBUG - 2015-04-05 00:53:01 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:53:02 --> select mm.pk_id from messenger_messages as mm
					inner join messenger_users_messages as mum on mum.fk_message_id = mm.pk_id 
					and mum.`read` = 0 
					and mm.fk_destination_user = 1
					limit 1;
DEBUG - 2015-04-05 00:53:02 --> Final output sent to browser
DEBUG - 2015-04-05 00:53:02 --> Total execution time: 2.1121
DEBUG - 2015-04-05 00:53:02 --> Controller Class Initialized
DEBUG - 2015-04-05 00:53:02 --> Helper loaded: custom_validations_helper
DEBUG - 2015-04-05 00:53:02 --> Model Class Initialized
DEBUG - 2015-04-05 00:53:02 --> Model Class Initialized
DEBUG - 2015-04-05 00:53:02 --> Helper loaded: models_helper
DEBUG - 2015-04-05 00:53:02 --> hostname=localhost
DEBUG - 2015-04-05 00:53:02 --> username=root
DEBUG - 2015-04-05 00:53:02 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 00:53:02 --> database=cl51-democompa
DEBUG - 2015-04-05 00:53:02 --> dbdriver=mysqli
DEBUG - 2015-04-05 00:53:02 --> dbprefix=
DEBUG - 2015-04-05 00:53:02 --> pconnect=1
DEBUG - 2015-04-05 00:53:02 --> db_debug=1
DEBUG - 2015-04-05 00:53:02 --> cache_on=
DEBUG - 2015-04-05 00:53:02 --> cachedir=
DEBUG - 2015-04-05 00:53:02 --> char_set=utf8
DEBUG - 2015-04-05 00:53:02 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 00:53:02 --> swap_pre=
DEBUG - 2015-04-05 00:53:02 --> autoinit=1
DEBUG - 2015-04-05 00:53:02 --> stricton=
DEBUG - 2015-04-05 00:53:02 --> Database Driver Class Initialized
DEBUG - 2015-04-05 00:53:03 --> XSS Filtering completed
DEBUG - 2015-04-05 00:53:03 --> Final output sent to browser
DEBUG - 2015-04-05 00:53:03 --> Total execution time: 3.1212
DEBUG - 2015-04-05 02:40:34 --> Config Class Initialized
DEBUG - 2015-04-05 02:40:34 --> Hooks Class Initialized
DEBUG - 2015-04-05 02:40:34 --> Utf8 Class Initialized
DEBUG - 2015-04-05 02:40:34 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 02:40:34 --> URI Class Initialized
DEBUG - 2015-04-05 02:40:34 --> Router Class Initialized
DEBUG - 2015-04-05 02:40:34 --> Output Class Initialized
DEBUG - 2015-04-05 02:40:34 --> Security Class Initialized
DEBUG - 2015-04-05 02:40:34 --> Input Class Initialized
DEBUG - 2015-04-05 02:40:34 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 02:40:34 --> Language Class Initialized
DEBUG - 2015-04-05 02:40:34 --> Loader Class Initialized
DEBUG - 2015-04-05 02:40:34 --> Helper loaded: url_helper
DEBUG - 2015-04-05 02:40:34 --> Controller Class Initialized
DEBUG - 2015-04-05 02:40:34 --> Session Class Initialized
DEBUG - 2015-04-05 02:40:34 --> Helper loaded: string_helper
DEBUG - 2015-04-05 02:40:34 --> Database Driver Class Initialized
DEBUG - 2015-04-05 02:40:35 --> Session routines successfully run
DEBUG - 2015-04-05 02:40:35 --> Helper loaded: form_helper
DEBUG - 2015-04-05 02:40:35 --> XSS Filtering completed
DEBUG - 2015-04-05 02:40:35 --> XSS Filtering completed
DEBUG - 2015-04-05 02:40:35 --> XSS Filtering completed
DEBUG - 2015-04-05 02:40:35 --> XSS Filtering completed
DEBUG - 2015-04-05 02:40:35 --> XSS Filtering completed
DEBUG - 2015-04-05 02:40:35 --> XSS Filtering completed
DEBUG - 2015-04-05 02:40:35 --> XSS Filtering completed
DEBUG - 2015-04-05 02:40:35 --> XSS Filtering completed
DEBUG - 2015-04-05 02:40:35 --> XSS Filtering completed
DEBUG - 2015-04-05 02:40:35 --> XSS Filtering completed
DEBUG - 2015-04-05 02:40:35 --> XSS Filtering completed
DEBUG - 2015-04-05 02:40:35 --> XSS Filtering completed
DEBUG - 2015-04-05 02:40:35 --> XSS Filtering completed
DEBUG - 2015-04-05 02:40:35 --> XSS Filtering completed
DEBUG - 2015-04-05 02:40:35 --> XSS Filtering completed
DEBUG - 2015-04-05 02:40:35 --> XSS Filtering completed
DEBUG - 2015-04-05 02:40:35 --> XSS Filtering completed
DEBUG - 2015-04-05 02:40:35 --> Model Class Initialized
DEBUG - 2015-04-05 02:40:35 --> Model Class Initialized
DEBUG - 2015-04-05 02:40:35 --> Helper loaded: models_helper
DEBUG - 2015-04-05 02:40:35 --> hostname=localhost
DEBUG - 2015-04-05 02:40:35 --> username=root
DEBUG - 2015-04-05 02:40:35 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 02:40:35 --> database=cl51-democompa
DEBUG - 2015-04-05 02:40:35 --> dbdriver=mysqli
DEBUG - 2015-04-05 02:40:35 --> dbprefix=
DEBUG - 2015-04-05 02:40:35 --> pconnect=1
DEBUG - 2015-04-05 02:40:35 --> db_debug=1
DEBUG - 2015-04-05 02:40:35 --> cache_on=
DEBUG - 2015-04-05 02:40:35 --> cachedir=
DEBUG - 2015-04-05 02:40:35 --> char_set=utf8
DEBUG - 2015-04-05 02:40:35 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 02:40:35 --> swap_pre=
DEBUG - 2015-04-05 02:40:35 --> autoinit=1
DEBUG - 2015-04-05 02:40:35 --> stricton=
DEBUG - 2015-04-05 02:40:35 --> Database Driver Class Initialized
DEBUG - 2015-04-05 02:40:36 --> Final output sent to browser
DEBUG - 2015-04-05 02:40:36 --> Total execution time: 2.4321
DEBUG - 2015-04-05 02:40:36 --> Config Class Initialized
DEBUG - 2015-04-05 02:40:36 --> Hooks Class Initialized
DEBUG - 2015-04-05 02:40:36 --> Utf8 Class Initialized
DEBUG - 2015-04-05 02:40:36 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 02:40:36 --> URI Class Initialized
DEBUG - 2015-04-05 02:40:36 --> Router Class Initialized
DEBUG - 2015-04-05 02:40:36 --> Output Class Initialized
DEBUG - 2015-04-05 02:40:36 --> Security Class Initialized
DEBUG - 2015-04-05 02:40:36 --> Input Class Initialized
DEBUG - 2015-04-05 02:40:36 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 02:40:36 --> Language Class Initialized
DEBUG - 2015-04-05 02:40:36 --> Loader Class Initialized
DEBUG - 2015-04-05 02:40:36 --> Helper loaded: url_helper
DEBUG - 2015-04-05 02:40:36 --> Controller Class Initialized
DEBUG - 2015-04-05 02:40:36 --> Model Class Initialized
DEBUG - 2015-04-05 02:40:36 --> Model Class Initialized
DEBUG - 2015-04-05 02:40:36 --> Helper loaded: models_helper
DEBUG - 2015-04-05 02:40:36 --> hostname=localhost
DEBUG - 2015-04-05 02:40:36 --> username=root
DEBUG - 2015-04-05 02:40:36 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 02:40:36 --> database=cl51-democompa
DEBUG - 2015-04-05 02:40:36 --> dbdriver=mysqli
DEBUG - 2015-04-05 02:40:36 --> dbprefix=
DEBUG - 2015-04-05 02:40:36 --> pconnect=1
DEBUG - 2015-04-05 02:40:36 --> db_debug=1
DEBUG - 2015-04-05 02:40:36 --> cache_on=
DEBUG - 2015-04-05 02:40:36 --> cachedir=
DEBUG - 2015-04-05 02:40:36 --> char_set=utf8
DEBUG - 2015-04-05 02:40:36 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 02:40:36 --> swap_pre=
DEBUG - 2015-04-05 02:40:36 --> autoinit=1
DEBUG - 2015-04-05 02:40:36 --> stricton=
DEBUG - 2015-04-05 02:40:36 --> Database Driver Class Initialized
DEBUG - 2015-04-05 02:40:38 --> Final output sent to browser
DEBUG - 2015-04-05 02:40:38 --> Total execution time: 1.1091
DEBUG - 2015-04-05 02:40:44 --> Config Class Initialized
DEBUG - 2015-04-05 02:40:44 --> Hooks Class Initialized
DEBUG - 2015-04-05 02:40:44 --> Utf8 Class Initialized
DEBUG - 2015-04-05 02:40:44 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 02:40:44 --> URI Class Initialized
DEBUG - 2015-04-05 02:40:44 --> Router Class Initialized
DEBUG - 2015-04-05 02:40:44 --> Output Class Initialized
DEBUG - 2015-04-05 02:40:44 --> Security Class Initialized
DEBUG - 2015-04-05 02:40:44 --> Input Class Initialized
DEBUG - 2015-04-05 02:40:44 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 02:40:44 --> Language Class Initialized
DEBUG - 2015-04-05 02:40:44 --> Loader Class Initialized
DEBUG - 2015-04-05 02:40:44 --> Helper loaded: url_helper
DEBUG - 2015-04-05 02:40:44 --> Controller Class Initialized
DEBUG - 2015-04-05 02:40:44 --> Session Class Initialized
DEBUG - 2015-04-05 02:40:44 --> Helper loaded: string_helper
DEBUG - 2015-04-05 02:40:44 --> Database Driver Class Initialized
DEBUG - 2015-04-05 02:40:45 --> Session routines successfully run
DEBUG - 2015-04-05 02:40:45 --> Model Class Initialized
DEBUG - 2015-04-05 02:40:45 --> Model Class Initialized
DEBUG - 2015-04-05 02:40:45 --> Helper loaded: models_helper
DEBUG - 2015-04-05 02:40:45 --> hostname=localhost
DEBUG - 2015-04-05 02:40:45 --> username=root
DEBUG - 2015-04-05 02:40:45 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 02:40:45 --> database=cl51-democompa
DEBUG - 2015-04-05 02:40:45 --> dbdriver=mysqli
DEBUG - 2015-04-05 02:40:45 --> dbprefix=
DEBUG - 2015-04-05 02:40:45 --> pconnect=1
DEBUG - 2015-04-05 02:40:45 --> db_debug=1
DEBUG - 2015-04-05 02:40:45 --> cache_on=
DEBUG - 2015-04-05 02:40:45 --> cachedir=
DEBUG - 2015-04-05 02:40:45 --> char_set=utf8
DEBUG - 2015-04-05 02:40:45 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 02:40:45 --> swap_pre=
DEBUG - 2015-04-05 02:40:45 --> autoinit=1
DEBUG - 2015-04-05 02:40:45 --> stricton=
DEBUG - 2015-04-05 02:40:45 --> Database Driver Class Initialized
DEBUG - 2015-04-05 02:40:47 --> XSS Filtering completed
DEBUG - 2015-04-05 02:40:47 --> XSS Filtering completed
DEBUG - 2015-04-05 02:40:47 --> Final output sent to browser
DEBUG - 2015-04-05 02:40:47 --> Total execution time: 2.4411
DEBUG - 2015-04-05 02:40:47 --> Config Class Initialized
DEBUG - 2015-04-05 02:40:47 --> Hooks Class Initialized
DEBUG - 2015-04-05 02:40:47 --> Utf8 Class Initialized
DEBUG - 2015-04-05 02:40:47 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 02:40:47 --> URI Class Initialized
DEBUG - 2015-04-05 02:40:47 --> Router Class Initialized
DEBUG - 2015-04-05 02:40:47 --> Output Class Initialized
DEBUG - 2015-04-05 02:40:47 --> Security Class Initialized
DEBUG - 2015-04-05 02:40:47 --> Input Class Initialized
DEBUG - 2015-04-05 02:40:47 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 02:40:47 --> Language Class Initialized
DEBUG - 2015-04-05 02:40:47 --> Loader Class Initialized
DEBUG - 2015-04-05 02:40:47 --> Helper loaded: url_helper
DEBUG - 2015-04-05 02:40:47 --> Controller Class Initialized
DEBUG - 2015-04-05 02:40:47 --> File loaded: ../application/views/header_nav.php
DEBUG - 2015-04-05 02:40:47 --> File loaded: ../application/views/hire_stock_fleet_records.php
DEBUG - 2015-04-05 02:40:47 --> File loaded: ../application/views/footer_common.php
DEBUG - 2015-04-05 02:40:47 --> File loaded: ../application/views/footer_copyright.php
DEBUG - 2015-04-05 02:40:47 --> File loaded: ../application/views/footer.php
DEBUG - 2015-04-05 02:40:47 --> Final output sent to browser
DEBUG - 2015-04-05 02:40:47 --> Total execution time: 0.0450
DEBUG - 2015-04-05 02:40:48 --> Config Class Initialized
DEBUG - 2015-04-05 02:40:48 --> Hooks Class Initialized
DEBUG - 2015-04-05 02:40:48 --> Utf8 Class Initialized
DEBUG - 2015-04-05 02:40:48 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 02:40:48 --> URI Class Initialized
DEBUG - 2015-04-05 02:40:48 --> Router Class Initialized
DEBUG - 2015-04-05 02:40:48 --> Config Class Initialized
DEBUG - 2015-04-05 02:40:48 --> Config Class Initialized
DEBUG - 2015-04-05 02:40:48 --> Hooks Class Initialized
DEBUG - 2015-04-05 02:40:48 --> Utf8 Class Initialized
DEBUG - 2015-04-05 02:40:48 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 02:40:48 --> Output Class Initialized
DEBUG - 2015-04-05 02:40:48 --> URI Class Initialized
DEBUG - 2015-04-05 02:40:48 --> Security Class Initialized
DEBUG - 2015-04-05 02:40:48 --> Router Class Initialized
DEBUG - 2015-04-05 02:40:48 --> Input Class Initialized
DEBUG - 2015-04-05 02:40:48 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 02:40:48 --> Language Class Initialized
DEBUG - 2015-04-05 02:40:48 --> Output Class Initialized
DEBUG - 2015-04-05 02:40:48 --> Hooks Class Initialized
DEBUG - 2015-04-05 02:40:48 --> Utf8 Class Initialized
DEBUG - 2015-04-05 02:40:48 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 02:40:48 --> Loader Class Initialized
DEBUG - 2015-04-05 02:40:48 --> URI Class Initialized
DEBUG - 2015-04-05 02:40:48 --> Router Class Initialized
DEBUG - 2015-04-05 02:40:48 --> Security Class Initialized
DEBUG - 2015-04-05 02:40:48 --> Output Class Initialized
DEBUG - 2015-04-05 02:40:48 --> Input Class Initialized
DEBUG - 2015-04-05 02:40:48 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 02:40:48 --> Security Class Initialized
DEBUG - 2015-04-05 02:40:48 --> Helper loaded: url_helper
DEBUG - 2015-04-05 02:40:48 --> Language Class Initialized
DEBUG - 2015-04-05 02:40:48 --> Controller Class Initialized
DEBUG - 2015-04-05 02:40:48 --> Loader Class Initialized
DEBUG - 2015-04-05 02:40:48 --> Input Class Initialized
DEBUG - 2015-04-05 02:40:48 --> Helper loaded: url_helper
DEBUG - 2015-04-05 02:40:48 --> Model Class Initialized
DEBUG - 2015-04-05 02:40:48 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 02:40:48 --> Model Class Initialized
DEBUG - 2015-04-05 02:40:48 --> Language Class Initialized
DEBUG - 2015-04-05 02:40:48 --> Helper loaded: models_helper
DEBUG - 2015-04-05 02:40:48 --> hostname=localhost
DEBUG - 2015-04-05 02:40:48 --> username=root
DEBUG - 2015-04-05 02:40:48 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 02:40:48 --> database=cl51-democompa
DEBUG - 2015-04-05 02:40:48 --> dbdriver=mysqli
DEBUG - 2015-04-05 02:40:48 --> dbprefix=
DEBUG - 2015-04-05 02:40:48 --> pconnect=1
DEBUG - 2015-04-05 02:40:48 --> db_debug=1
DEBUG - 2015-04-05 02:40:48 --> cache_on=
DEBUG - 2015-04-05 02:40:48 --> cachedir=
DEBUG - 2015-04-05 02:40:48 --> Loader Class Initialized
DEBUG - 2015-04-05 02:40:48 --> char_set=utf8
DEBUG - 2015-04-05 02:40:48 --> Helper loaded: url_helper
DEBUG - 2015-04-05 02:40:48 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 02:40:48 --> swap_pre=
DEBUG - 2015-04-05 02:40:48 --> autoinit=1
DEBUG - 2015-04-05 02:40:48 --> stricton=
DEBUG - 2015-04-05 02:40:48 --> Database Driver Class Initialized
DEBUG - 2015-04-05 02:40:48 --> Config Class Initialized
DEBUG - 2015-04-05 02:40:48 --> Hooks Class Initialized
DEBUG - 2015-04-05 02:40:48 --> Utf8 Class Initialized
DEBUG - 2015-04-05 02:40:48 --> UTF-8 Support Enabled
DEBUG - 2015-04-05 02:40:48 --> URI Class Initialized
DEBUG - 2015-04-05 02:40:48 --> Router Class Initialized
DEBUG - 2015-04-05 02:40:48 --> Output Class Initialized
DEBUG - 2015-04-05 02:40:48 --> Security Class Initialized
DEBUG - 2015-04-05 02:40:48 --> Input Class Initialized
DEBUG - 2015-04-05 02:40:48 --> Global POST and COOKIE data sanitized
DEBUG - 2015-04-05 02:40:48 --> Language Class Initialized
DEBUG - 2015-04-05 02:40:48 --> Loader Class Initialized
DEBUG - 2015-04-05 02:40:48 --> Helper loaded: url_helper
DEBUG - 2015-04-05 02:40:49 --> Final output sent to browser
DEBUG - 2015-04-05 02:40:49 --> Total execution time: 1.1551
DEBUG - 2015-04-05 02:40:49 --> Controller Class Initialized
DEBUG - 2015-04-05 02:40:49 --> Model Class Initialized
DEBUG - 2015-04-05 02:40:49 --> Model Class Initialized
DEBUG - 2015-04-05 02:40:49 --> Helper loaded: models_helper
DEBUG - 2015-04-05 02:40:49 --> hostname=localhost
DEBUG - 2015-04-05 02:40:49 --> username=root
DEBUG - 2015-04-05 02:40:49 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 02:40:49 --> database=cl51-democompa
DEBUG - 2015-04-05 02:40:49 --> dbdriver=mysqli
DEBUG - 2015-04-05 02:40:49 --> dbprefix=
DEBUG - 2015-04-05 02:40:49 --> pconnect=1
DEBUG - 2015-04-05 02:40:49 --> db_debug=1
DEBUG - 2015-04-05 02:40:49 --> cache_on=
DEBUG - 2015-04-05 02:40:49 --> cachedir=
DEBUG - 2015-04-05 02:40:49 --> char_set=utf8
DEBUG - 2015-04-05 02:40:49 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 02:40:49 --> swap_pre=
DEBUG - 2015-04-05 02:40:49 --> autoinit=1
DEBUG - 2015-04-05 02:40:49 --> stricton=
DEBUG - 2015-04-05 02:40:49 --> Database Driver Class Initialized
DEBUG - 2015-04-05 02:40:50 --> select mm.pk_id from messenger_messages as mm
					inner join messenger_users_messages as mum on mum.fk_message_id = mm.pk_id 
					and mum.`read` = 0 
					and mm.fk_destination_user = 1
					limit 1;
DEBUG - 2015-04-05 02:40:50 --> Final output sent to browser
DEBUG - 2015-04-05 02:40:50 --> Total execution time: 2.2671
DEBUG - 2015-04-05 02:40:50 --> Controller Class Initialized
DEBUG - 2015-04-05 02:40:50 --> Helper loaded: custom_validations_helper
DEBUG - 2015-04-05 02:40:50 --> Model Class Initialized
DEBUG - 2015-04-05 02:40:50 --> Model Class Initialized
DEBUG - 2015-04-05 02:40:50 --> Helper loaded: models_helper
DEBUG - 2015-04-05 02:40:50 --> hostname=localhost
DEBUG - 2015-04-05 02:40:50 --> username=root
DEBUG - 2015-04-05 02:40:50 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 02:40:50 --> database=cl51-democompa
DEBUG - 2015-04-05 02:40:50 --> dbdriver=mysqli
DEBUG - 2015-04-05 02:40:50 --> dbprefix=
DEBUG - 2015-04-05 02:40:50 --> pconnect=1
DEBUG - 2015-04-05 02:40:50 --> db_debug=1
DEBUG - 2015-04-05 02:40:50 --> cache_on=
DEBUG - 2015-04-05 02:40:50 --> cachedir=
DEBUG - 2015-04-05 02:40:50 --> char_set=utf8
DEBUG - 2015-04-05 02:40:50 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 02:40:50 --> swap_pre=
DEBUG - 2015-04-05 02:40:50 --> autoinit=1
DEBUG - 2015-04-05 02:40:50 --> stricton=
DEBUG - 2015-04-05 02:40:50 --> Database Driver Class Initialized
DEBUG - 2015-04-05 02:40:51 --> XSS Filtering completed
DEBUG - 2015-04-05 02:40:51 --> Final output sent to browser
DEBUG - 2015-04-05 02:40:51 --> Total execution time: 3.3142
DEBUG - 2015-04-05 02:40:51 --> Controller Class Initialized
DEBUG - 2015-04-05 02:40:51 --> Model Class Initialized
DEBUG - 2015-04-05 02:40:51 --> Model Class Initialized
DEBUG - 2015-04-05 02:40:51 --> Helper loaded: models_helper
DEBUG - 2015-04-05 02:40:51 --> hostname=localhost
DEBUG - 2015-04-05 02:40:51 --> username=root
DEBUG - 2015-04-05 02:40:51 --> password=7H3nMvBaWcAHDr8K
DEBUG - 2015-04-05 02:40:51 --> database=cl51-democompa
DEBUG - 2015-04-05 02:40:51 --> dbdriver=mysqli
DEBUG - 2015-04-05 02:40:51 --> dbprefix=
DEBUG - 2015-04-05 02:40:51 --> pconnect=1
DEBUG - 2015-04-05 02:40:51 --> db_debug=1
DEBUG - 2015-04-05 02:40:51 --> cache_on=
DEBUG - 2015-04-05 02:40:51 --> cachedir=
DEBUG - 2015-04-05 02:40:51 --> char_set=utf8
DEBUG - 2015-04-05 02:40:51 --> dbcollat=utf8_general_ci
DEBUG - 2015-04-05 02:40:51 --> swap_pre=
DEBUG - 2015-04-05 02:40:51 --> autoinit=1
DEBUG - 2015-04-05 02:40:51 --> stricton=
DEBUG - 2015-04-05 02:40:51 --> Database Driver Class Initialized
DEBUG - 2015-04-05 02:40:52 --> Final output sent to browser
DEBUG - 2015-04-05 02:40:52 --> Total execution time: 4.2892
