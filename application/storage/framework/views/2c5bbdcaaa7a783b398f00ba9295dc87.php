<div class="row">
    <div class="col-lg-12">

        <?php if(config('visibility.contacts_modal_client_fields')): ?>
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label required"><?php echo e(cleanLang(__('lang.company_name'))); ?>*</label>
            <div class="col-sm-12 col-lg-9">
                <!--select2 basic search-->
                <select name="clientid" id="clientid"
                    class="form-control form-control-sm js-select2-basic-search-modal select2-hidden-accessible"
                    data-ajax--url="<?php echo e(url('/')); ?>/feed/company_names"></select>
                <!--select2 basic search-->
            </div>
        </div>
        <?php endif; ?>

        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label required"><?php echo e(cleanLang(__('lang.first_name'))); ?>*</label>
            <div class="col-sm-12 col-lg-9">
                <input type="text" class="form-control form-control-sm" id="first_name" name="first_name"
                    value="<?php echo e($user->first_name ?? ''); ?>">
            </div>
        </div>
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label required"><?php echo e(cleanLang(__('lang.last_name'))); ?>*</label>
            <div class="col-sm-12 col-lg-9">
                <input type="text" class="form-control form-control-sm" id="last_name" name="last_name"
                    value="<?php echo e($user->last_name ?? ''); ?>">
            </div>
        </div>
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label required"><?php echo e(cleanLang(__('lang.email_address'))); ?>*</label>
            <div class="col-sm-12 col-lg-9">
                <input type="text" class="form-control form-control-sm" id="email" name="email"
                    value="<?php echo e($user->email ?? ''); ?>">
            </div>
        </div>
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label"><?php echo e(cleanLang(__('lang.telephone'))); ?></label>
            <div class="col-sm-12 col-lg-9">
                <input type="text" class="form-control form-control-sm" id="phone" name="phone"
                    value="<?php echo e($user->phone ?? ''); ?>">
            </div>
        </div>
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label"><?php echo e(cleanLang(__('lang.position'))); ?></label>
            <div class="col-sm-12 col-lg-9">
                <input type="text" class="form-control form-control-sm" id="position" name="position"
                    value="<?php echo e($user->position ?? ''); ?>">
            </div>
        </div>

        <!--timezone-->
        <div class="form-group row">
            <label class="col-sm-12 col-lg-3 text-left control-label col-form-label"><?php echo e(cleanLang(__('lang.timezone'))); ?></label>
            <div class="col-sm-12 col-lg-9">
                <select class="select2-basic-with-search form-control form-control-sm select2-preselected"
                    data-allow-clear="false" data-preselected="<?php echo e($user->timezone ?? config('system.settings_system_timezone')); ?>"
                    id="timezone" name="timezone">
                    <optgroup label="UTC">
                        <option value="UTC">UTC</option>
                    </optgroup>
                    <optgroup label="EUROPE">
                        <option value="Europe/Amsterdam">Europe/Amsterdam</option>
                        <option value="Europe/Andorra">Europe/Andorra</option>
                        <option value="Europe/Astrakhan">Europe/Astrakhan</option>
                        <option value="Europe/Athens">Europe/Athens</option>
                        <option value="Europe/Belgrade">Europe/Belgrade</option>
                        <option value="Europe/Berlin">Europe/Berlin</option>
                        <option value="Europe/Bratislava">Europe/Bratislava</option>
                        <option value="Europe/Brussels">Europe/Brussels</option>
                        <option value="Europe/Bucharest">Europe/Bucharest</option>
                        <option value="Europe/Budapest">Europe/Budapest</option>
                        <option value="Europe/Busingen">Europe/Busingen</option>
                        <option value="Europe/Chisinau">Europe/Chisinau</option>
                        <option value="Europe/Copenhagen">Europe/Copenhagen</option>
                        <option value="Europe/Dublin">Europe/Dublin</option>
                        <option value="Europe/Gibraltar">Europe/Gibraltar</option>
                        <option value="Europe/Guernsey">Europe/Guernsey</option>
                        <option value="Europe/Helsinki">Europe/Helsinki</option>
                        <option value="Europe/Isle_of_Man">Europe/Isle_of_Man</option>
                        <option value="Europe/Istanbul">Europe/Istanbul</option>
                        <option value="Europe/Jersey">Europe/Jersey</option>
                        <option value="Europe/Kaliningrad">Europe/Kaliningrad</option>
                        <option value="Europe/Kiev">Europe/Kiev</option>
                        <option value="Europe/Kirov">Europe/Kirov</option>
                        <option value="Europe/Lisbon">Europe/Lisbon</option>
                        <option value="Europe/Ljubljana">Europe/Ljubljana</option>
                        <option value="Europe/London">Europe/London</option>
                        <option value="Europe/Luxembourg">Europe/Luxembourg</option>
                        <option value="Europe/Madrid">Europe/Madrid</option>
                        <option value="Europe/Malta">Europe/Malta</option>
                        <option value="Europe/Mariehamn">Europe/Mariehamn</option>
                        <option value="Europe/Minsk">Europe/Minsk</option>
                        <option value="Europe/Monaco">Europe/Monaco</option>
                        <option value="Europe/Moscow">Europe/Moscow</option>
                        <option value="Europe/Oslo">Europe/Oslo</option>
                        <option value="Europe/Paris">Europe/Paris</option>
                        <option value="Europe/Podgorica">Europe/Podgorica</option>
                        <option value="Europe/Prague">Europe/Prague</option>
                        <option value="Europe/Riga">Europe/Riga</option>
                        <option value="Europe/Rome">Europe/Rome</option>
                        <option value="Europe/Samara">Europe/Samara</option>
                        <option value="Europe/San_Marino">Europe/San_Marino</option>
                        <option value="Europe/Sarajevo">Europe/Sarajevo</option>
                        <option value="Europe/Saratov">Europe/Saratov</option>
                        <option value="Europe/Simferopol">Europe/Simferopol</option>
                        <option value="Europe/Skopje">Europe/Skopje</option>
                        <option value="Europe/Sofia">Europe/Sofia</option>
                        <option value="Europe/Stockholm">Europe/Stockholm</option>
                        <option value="Europe/Tallinn">Europe/Tallinn</option>
                        <option value="Europe/Tirane">Europe/Tirane</option>
                        <option value="Europe/Ulyanovsk">Europe/Ulyanovsk</option>
                        <option value="Europe/Uzhgorod">Europe/Uzhgorod</option>
                        <option value="Europe/Vaduz">Europe/Vaduz</option>
                        <option value="Europe/Vatican">Europe/Vatican</option>
                        <option value="Europe/Vienna">Europe/Vienna</option>
                        <option value="Europe/Vilnius">Europe/Vilnius</option>
                        <option value="Europe/Volgograd">Europe/Volgograd</option>
                        <option value="Europe/Warsaw">Europe/Warsaw</option>
                        <option value="Europe/Zagreb">Europe/Zagreb</option>
                        <option value="Europe/Zaporozhye">Europe/Zaporozhye</option>
                        <option value="Europe/Zurich">Europe/Zurich</option>
                    </optgroup>
                    <optgroup label="AMERICA">
                        <option value="America/Adak">America/Adak</option>
                        <option value="America/Anchorage">America/Anchorage</option>
                        <option value="America/Anguilla">America/Anguilla</option>
                        <option value="America/Antigua">America/Antigua</option>
                        <option value="America/Araguaina">America/Araguaina</option>
                        <option value="America/Argentina/Buenos_Aires">America/Argentina/Buenos_Aires</option>
                        <option value="America/Argentina/Catamarca">America/Argentina/Catamarca</option>
                        <option value="America/Argentina/Cordoba">America/Argentina/Cordoba</option>
                        <option value="America/Argentina/Jujuy">America/Argentina/Jujuy</option>
                        <option value="America/Argentina/La_Rioja">America/Argentina/La_Rioja</option>
                        <option value="America/Argentina/Mendoza">America/Argentina/Mendoza</option>
                        <option value="America/Argentina/Rio_Gallegos">America/Argentina/Rio_Gallegos</option>
                        <option value="America/Argentina/Salta">America/Argentina/Salta</option>
                        <option value="America/Argentina/San_Juan">America/Argentina/San_Juan</option>
                        <option value="America/Argentina/San_Luis">America/Argentina/San_Luis</option>
                        <option value="America/Argentina/Tucuman">America/Argentina/Tucuman</option>
                        <option value="America/Argentina/Ushuaia">America/Argentina/Ushuaia</option>
                        <option value="America/Aruba">America/Aruba</option>
                        <option value="America/Asuncion">America/Asuncion</option>
                        <option value="America/Atikokan">America/Atikokan</option>
                        <option value="America/Bahia">America/Bahia</option>
                        <option value="America/Bahia_Banderas">America/Bahia_Banderas</option>
                        <option value="America/Barbados">America/Barbados</option>
                        <option value="America/Belem">America/Belem</option>
                        <option value="America/Belize">America/Belize</option>
                        <option value="America/Blanc-Sablon">America/Blanc-Sablon</option>
                        <option value="America/Boa_Vista">America/Boa_Vista</option>
                        <option value="America/Bogota">America/Bogota</option>
                        <option value="America/Boise">America/Boise</option>
                        <option value="America/Cambridge_Bay">America/Cambridge_Bay</option>
                        <option value="America/Campo_Grande">America/Campo_Grande</option>
                        <option value="America/Cancun">America/Cancun</option>
                        <option value="America/Caracas">America/Caracas</option>
                        <option value="America/Cayenne">America/Cayenne</option>
                        <option value="America/Cayman">America/Cayman</option>
                        <option value="America/Chicago">America/Chicago</option>
                        <option value="America/Chihuahua">America/Chihuahua</option>
                        <option value="America/Costa_Rica">America/Costa_Rica</option>
                        <option value="America/Creston">America/Creston</option>
                        <option value="America/Cuiaba">America/Cuiaba</option>
                        <option value="America/Curacao">America/Curacao</option>
                        <option value="America/Danmarkshavn">America/Danmarkshavn</option>
                        <option value="America/Dawson">America/Dawson</option>
                        <option value="America/Dawson_Creek">America/Dawson_Creek</option>
                        <option value="America/Denver">America/Denver</option>
                        <option value="America/Detroit">America/Detroit</option>
                        <option value="America/Dominica">America/Dominica</option>
                        <option value="America/Edmonton">America/Edmonton</option>
                        <option value="America/Eirunepe">America/Eirunepe</option>
                        <option value="America/El_Salvador">America/El_Salvador</option>
                        <option value="America/Fort_Nelson">America/Fort_Nelson</option>
                        <option value="America/Fortaleza">America/Fortaleza</option>
                        <option value="America/Glace_Bay">America/Glace_Bay</option>
                        <option value="America/Godthab">America/Godthab</option>
                        <option value="America/Goose_Bay">America/Goose_Bay</option>
                        <option value="America/Grand_Turk">America/Grand_Turk</option>
                        <option value="America/Grenada">America/Grenada</option>
                        <option value="America/Guadeloupe">America/Guadeloupe</option>
                        <option value="America/Guatemala">America/Guatemala</option>
                        <option value="America/Guayaquil">America/Guayaquil</option>
                        <option value="America/Guyana">America/Guyana</option>
                        <option value="America/Halifax">America/Halifax</option>
                        <option value="America/Havana">America/Havana</option>
                        <option value="America/Hermosillo">America/Hermosillo</option>
                        <option value="America/Indiana/Indianapolis">America/Indiana/Indianapolis</option>
                        <option value="America/Indiana/Knox">America/Indiana/Knox</option>
                        <option value="America/Indiana/Marengo">America/Indiana/Marengo</option>
                        <option value="America/Indiana/Petersburg">America/Indiana/Petersburg</option>
                        <option value="America/Indiana/Tell_City">America/Indiana/Tell_City</option>
                        <option value="America/Indiana/Vevay">America/Indiana/Vevay</option>
                        <option value="America/Indiana/Vincennes">America/Indiana/Vincennes</option>
                        <option value="America/Indiana/Winamac">America/Indiana/Winamac</option>
                        <option value="America/Inuvik">America/Inuvik</option>
                        <option value="America/Iqaluit">America/Iqaluit</option>
                        <option value="America/Jamaica">America/Jamaica</option>
                        <option value="America/Juneau">America/Juneau</option>
                        <option value="America/Kentucky/Louisville">America/Kentucky/Louisville</option>
                        <option value="America/Kentucky/Monticello">America/Kentucky/Monticello</option>
                        <option value="America/Kralendijk">America/Kralendijk</option>
                        <option value="America/La_Paz">America/La_Paz</option>
                        <option value="America/Lima">America/Lima</option>
                        <option value="America/Los_Angeles">America/Los_Angeles</option>
                        <option value="America/Lower_Princes">America/Lower_Princes</option>
                        <option value="America/Maceio">America/Maceio</option>
                        <option value="America/Managua">America/Managua</option>
                        <option value="America/Manaus">America/Manaus</option>
                        <option value="America/Marigot">America/Marigot</option>
                        <option value="America/Martinique">America/Martinique</option>
                        <option value="America/Matamoros">America/Matamoros</option>
                        <option value="America/Mazatlan">America/Mazatlan</option>
                        <option value="America/Menominee">America/Menominee</option>
                        <option value="America/Merida">America/Merida</option>
                        <option value="America/Metlakatla">America/Metlakatla</option>
                        <option value="America/Mexico_City">America/Mexico_City</option>
                        <option value="America/Miquelon">America/Miquelon</option>
                        <option value="America/Moncton">America/Moncton</option>
                        <option value="America/Monterrey">America/Monterrey</option>
                        <option value="America/Montevideo">America/Montevideo</option>
                        <option value="America/Montserrat">America/Montserrat</option>
                        <option value="America/Nassau">America/Nassau</option>
                        <option value="America/New_York">America/New_York</option>
                        <option value="America/Nipigon">America/Nipigon</option>
                        <option value="America/Nome">America/Nome</option>
                        <option value="America/Noronha">America/Noronha</option>
                        <option value="America/North_Dakota/Beulah">America/North_Dakota/Beulah</option>
                        <option value="America/North_Dakota/Center">America/North_Dakota/Center</option>
                        <option value="America/North_Dakota/New_Salem">America/North_Dakota/New_Salem</option>
                        <option value="America/Ojinaga">America/Ojinaga</option>
                        <option value="America/Panama">America/Panama</option>
                        <option value="America/Pangnirtung">America/Pangnirtung</option>
                        <option value="America/Paramaribo">America/Paramaribo</option>
                        <option value="America/Phoenix">America/Phoenix</option>
                        <option value="America/Port-au-Prince">America/Port-au-Prince</option>
                        <option value="America/Port_of_Spain">America/Port_of_Spain</option>
                        <option value="America/Porto_Velho">America/Porto_Velho</option>
                        <option value="America/Puerto_Rico">America/Puerto_Rico</option>
                        <option value="America/Punta_Arenas">America/Punta_Arenas</option>
                        <option value="America/Rainy_River">America/Rainy_River</option>
                        <option value="America/Rankin_Inlet">America/Rankin_Inlet</option>
                        <option value="America/Recife">America/Recife</option>
                        <option value="America/Regina">America/Regina</option>
                        <option value="America/Resolute">America/Resolute</option>
                        <option value="America/Rio_Branco">America/Rio_Branco</option>
                        <option value="America/Santarem">America/Santarem</option>
                        <option value="America/Santiago">America/Santiago</option>
                        <option value="America/Santo_Domingo">America/Santo_Domingo</option>
                        <option value="America/Sao_Paulo">America/Sao_Paulo</option>
                        <option value="America/Scoresbysund">America/Scoresbysund</option>
                        <option value="America/Sitka">America/Sitka</option>
                        <option value="America/St_Barthelemy">America/St_Barthelemy</option>
                        <option value="America/St_Johns">America/St_Johns</option>
                        <option value="America/St_Kitts">America/St_Kitts</option>
                        <option value="America/St_Lucia">America/St_Lucia</option>
                        <option value="America/St_Thomas">America/St_Thomas</option>
                        <option value="America/St_Vincent">America/St_Vincent</option>
                        <option value="America/Swift_Current">America/Swift_Current</option>
                        <option value="America/Tegucigalpa">America/Tegucigalpa</option>
                        <option value="America/Thule">America/Thule</option>
                        <option value="America/Thunder_Bay">America/Thunder_Bay</option>
                        <option value="America/Tijuana">America/Tijuana</option>
                        <option value="America/Toronto">America/Toronto</option>
                        <option value="America/Tortola">America/Tortola</option>
                        <option value="America/Vancouver">America/Vancouver</option>
                        <option value="America/Whitehorse">America/Whitehorse</option>
                        <option value="America/Winnipeg">America/Winnipeg</option>
                        <option value="America/Yakutat">America/Yakutat</option>
                        <option value="America/Yellowknife">America/Yellowknife</option>
                    </optgroup>
                    <optgroup label="INDIAN">
                        <option value="Indian/Antananarivo">Indian/Antananarivo</option>
                        <option value="Indian/Chagos">Indian/Chagos</option>
                        <option value="Indian/Christmas">Indian/Christmas</option>
                        <option value="Indian/Cocos">Indian/Cocos</option>
                        <option value="Indian/Comoro">Indian/Comoro</option>
                        <option value="Indian/Kerguelen">Indian/Kerguelen</option>
                        <option value="Indian/Mahe">Indian/Mahe</option>
                        <option value="Indian/Maldives">Indian/Maldives</option>
                        <option value="Indian/Mauritius">Indian/Mauritius</option>
                        <option value="Indian/Mayotte">Indian/Mayotte</option>
                        <option value="Indian/Reunion">Indian/Reunion</option>
                    </optgroup>
                    <optgroup label="AUSTRALIA">
                        <option value="Australia/Adelaide">Australia/Adelaide</option>
                        <option value="Australia/Brisbane">Australia/Brisbane</option>
                        <option value="Australia/Broken_Hill">Australia/Broken_Hill</option>
                        <option value="Australia/Currie">Australia/Currie</option>
                        <option value="Australia/Darwin">Australia/Darwin</option>
                        <option value="Australia/Eucla">Australia/Eucla</option>
                        <option value="Australia/Hobart">Australia/Hobart</option>
                        <option value="Australia/Lindeman">Australia/Lindeman</option>
                        <option value="Australia/Lord_Howe">Australia/Lord_Howe</option>
                        <option value="Australia/Melbourne">Australia/Melbourne</option>
                        <option value="Australia/Perth">Australia/Perth</option>
                        <option value="Australia/Sydney">Australia/Sydney</option>
                    </optgroup>
                    <optgroup label="ASIA">
                        <option value="Asia/Aden">Asia/Aden</option>
                        <option value="Asia/Almaty">Asia/Almaty</option>
                        <option value="Asia/Amman">Asia/Amman</option>
                        <option value="Asia/Anadyr">Asia/Anadyr</option>
                        <option value="Asia/Aqtau">Asia/Aqtau</option>
                        <option value="Asia/Aqtobe">Asia/Aqtobe</option>
                        <option value="Asia/Ashgabat">Asia/Ashgabat</option>
                        <option value="Asia/Atyrau">Asia/Atyrau</option>
                        <option value="Asia/Baghdad">Asia/Baghdad</option>
                        <option value="Asia/Bahrain">Asia/Bahrain</option>
                        <option value="Asia/Baku">Asia/Baku</option>
                        <option value="Asia/Bangkok">Asia/Bangkok</option>
                        <option value="Asia/Barnaul">Asia/Barnaul</option>
                        <option value="Asia/Beirut">Asia/Beirut</option>
                        <option value="Asia/Bishkek">Asia/Bishkek</option>
                        <option value="Asia/Brunei">Asia/Brunei</option>
                        <option value="Asia/Chita">Asia/Chita</option>
                        <option value="Asia/Choibalsan">Asia/Choibalsan</option>
                        <option value="Asia/Colombo">Asia/Colombo</option>
                        <option value="Asia/Damascus">Asia/Damascus</option>
                        <option value="Asia/Dhaka">Asia/Dhaka</option>
                        <option value="Asia/Dili">Asia/Dili</option>
                        <option value="Asia/Dubai">Asia/Dubai</option>
                        <option value="Asia/Dushanbe">Asia/Dushanbe</option>
                        <option value="Asia/Famagusta">Asia/Famagusta</option>
                        <option value="Asia/Gaza">Asia/Gaza</option>
                        <option value="Asia/Hebron">Asia/Hebron</option>
                        <option value="Asia/Ho_Chi_Minh">Asia/Ho_Chi_Minh</option>
                        <option value="Asia/Hong_Kong">Asia/Hong_Kong</option>
                        <option value="Asia/Hovd">Asia/Hovd</option>
                        <option value="Asia/Irkutsk">Asia/Irkutsk</option>
                        <option value="Asia/Jakarta">Asia/Jakarta</option>
                        <option value="Asia/Jayapura">Asia/Jayapura</option>
                        <option value="Asia/Jerusalem">Asia/Jerusalem</option>
                        <option value="Asia/Kabul">Asia/Kabul</option>
                        <option value="Asia/Kamchatka">Asia/Kamchatka</option>
                        <option value="Asia/Karachi">Asia/Karachi</option>
                        <option value="Asia/Kathmandu">Asia/Kathmandu</option>
                        <option value="Asia/Khandyga">Asia/Khandyga</option>
                        <option value="Asia/Kolkata">Asia/Kolkata</option>
                        <option value="Asia/Krasnoyarsk">Asia/Krasnoyarsk</option>
                        <option value="Asia/Kuala_Lumpur">Asia/Kuala_Lumpur</option>
                        <option value="Asia/Kuching">Asia/Kuching</option>
                        <option value="Asia/Kuwait">Asia/Kuwait</option>
                        <option value="Asia/Macau">Asia/Macau</option>
                        <option value="Asia/Magadan">Asia/Magadan</option>
                        <option value="Asia/Makassar">Asia/Makassar</option>
                        <option value="Asia/Manila">Asia/Manila</option>
                        <option value="Asia/Muscat">Asia/Muscat</option>
                        <option value="Asia/Nicosia">Asia/Nicosia</option>
                        <option value="Asia/Novokuznetsk">Asia/Novokuznetsk</option>
                        <option value="Asia/Novosibirsk">Asia/Novosibirsk</option>
                        <option value="Asia/Omsk">Asia/Omsk</option>
                        <option value="Asia/Oral">Asia/Oral</option>
                        <option value="Asia/Phnom_Penh">Asia/Phnom_Penh</option>
                        <option value="Asia/Pontianak">Asia/Pontianak</option>
                        <option value="Asia/Pyongyang">Asia/Pyongyang</option>
                        <option value="Asia/Qatar">Asia/Qatar</option>
                        <option value="Asia/Qostanay">Asia/Qostanay</option>
                        <option value="Asia/Qyzylorda">Asia/Qyzylorda</option>
                        <option value="Asia/Riyadh">Asia/Riyadh</option>
                        <option value="Asia/Sakhalin">Asia/Sakhalin</option>
                        <option value="Asia/Samarkand">Asia/Samarkand</option>
                        <option value="Asia/Seoul">Asia/Seoul</option>
                        <option value="Asia/Shanghai">Asia/Shanghai</option>
                        <option value="Asia/Singapore">Asia/Singapore</option>
                        <option value="Asia/Srednekolymsk">Asia/Srednekolymsk</option>
                        <option value="Asia/Taipei">Asia/Taipei</option>
                        <option value="Asia/Tashkent">Asia/Tashkent</option>
                        <option value="Asia/Tbilisi">Asia/Tbilisi</option>
                        <option value="Asia/Tehran">Asia/Tehran</option>
                        <option value="Asia/Thimphu">Asia/Thimphu</option>
                        <option value="Asia/Tokyo">Asia/Tokyo</option>
                        <option value="Asia/Tomsk">Asia/Tomsk</option>
                        <option value="Asia/Ulaanbaatar">Asia/Ulaanbaatar</option>
                        <option value="Asia/Urumqi">Asia/Urumqi</option>
                        <option value="Asia/Ust-Nera">Asia/Ust-Nera</option>
                        <option value="Asia/Vientiane">Asia/Vientiane</option>
                        <option value="Asia/Vladivostok">Asia/Vladivostok</option>
                        <option value="Asia/Yakutsk">Asia/Yakutsk</option>
                        <option value="Asia/Yangon">Asia/Yangon</option>
                        <option value="Asia/Yekaterinburg">Asia/Yekaterinburg</option>
                        <option value="Asia/Yerevan">Asia/Yerevan</option>
                    </optgroup>
                    <optgroup label="AFRICA">
                        <option value="Africa/Abidjan">Africa/Abidjan</option>
                        <option value="Africa/Accra">Africa/Accra</option>
                        <option value="Africa/Addis_Ababa">Africa/Addis_Ababa</option>
                        <option value="Africa/Algiers">Africa/Algiers</option>
                        <option value="Africa/Asmara">Africa/Asmara</option>
                        <option value="Africa/Bamako">Africa/Bamako</option>
                        <option value="Africa/Bangui">Africa/Bangui</option>
                        <option value="Africa/Banjul">Africa/Banjul</option>
                        <option value="Africa/Bissau">Africa/Bissau</option>
                        <option value="Africa/Blantyre">Africa/Blantyre</option>
                        <option value="Africa/Brazzaville">Africa/Brazzaville</option>
                        <option value="Africa/Bujumbura">Africa/Bujumbura</option>
                        <option value="Africa/Cairo">Africa/Cairo</option>
                        <option value="Africa/Casablanca">Africa/Casablanca</option>
                        <option value="Africa/Ceuta">Africa/Ceuta</option>
                        <option value="Africa/Conakry">Africa/Conakry</option>
                        <option value="Africa/Dakar">Africa/Dakar</option>
                        <option value="Africa/Dar_es_Salaam">Africa/Dar_es_Salaam</option>
                        <option value="Africa/Djibouti">Africa/Djibouti</option>
                        <option value="Africa/Douala">Africa/Douala</option>
                        <option value="Africa/El_Aaiun">Africa/El_Aaiun</option>
                        <option value="Africa/Freetown">Africa/Freetown</option>
                        <option value="Africa/Gaborone">Africa/Gaborone</option>
                        <option value="Africa/Harare">Africa/Harare</option>
                        <option value="Africa/Johannesburg">Africa/Johannesburg</option>
                        <option value="Africa/Juba">Africa/Juba</option>
                        <option value="Africa/Kampala">Africa/Kampala</option>
                        <option value="Africa/Khartoum">Africa/Khartoum</option>
                        <option value="Africa/Kigali">Africa/Kigali</option>
                        <option value="Africa/Kinshasa">Africa/Kinshasa</option>
                        <option value="Africa/Lagos">Africa/Lagos</option>
                        <option value="Africa/Libreville">Africa/Libreville</option>
                        <option value="Africa/Lome">Africa/Lome</option>
                        <option value="Africa/Luanda">Africa/Luanda</option>
                        <option value="Africa/Lubumbashi">Africa/Lubumbashi</option>
                        <option value="Africa/Lusaka">Africa/Lusaka</option>
                        <option value="Africa/Malabo">Africa/Malabo</option>
                        <option value="Africa/Maputo">Africa/Maputo</option>
                        <option value="Africa/Maseru">Africa/Maseru</option>
                        <option value="Africa/Mbabane">Africa/Mbabane</option>
                        <option value="Africa/Mogadishu">Africa/Mogadishu</option>
                        <option value="Africa/Monrovia">Africa/Monrovia</option>
                        <option value="Africa/Nairobi">Africa/Nairobi</option>
                        <option value="Africa/Ndjamena">Africa/Ndjamena</option>
                        <option value="Africa/Niamey">Africa/Niamey</option>
                        <option value="Africa/Nouakchott">Africa/Nouakchott</option>
                        <option value="Africa/Ouagadougou">Africa/Ouagadougou</option>
                        <option value="Africa/Porto-Novo">Africa/Porto-Novo</option>
                        <option value="Africa/Sao_Tome">Africa/Sao_Tome</option>
                        <option value="Africa/Tripoli">Africa/Tripoli</option>
                        <option value="Africa/Tunis">Africa/Tunis</option>
                        <option value="Africa/Windhoek">Africa/Windhoek</option>
                    </optgroup>
                    <optgroup label="ANTARCTICA">
                        <option value="Antarctica/Casey">Antarctica/Casey</option>
                        <option value="Antarctica/Davis">Antarctica/Davis</option>
                        <option value="Antarctica/DumontDUrville">Antarctica/DumontDUrville</option>
                        <option value="Antarctica/Macquarie">Antarctica/Macquarie</option>
                        <option value="Antarctica/Mawson">Antarctica/Mawson</option>
                        <option value="Antarctica/McMurdo">Antarctica/McMurdo</option>
                        <option value="Antarctica/Palmer">Antarctica/Palmer</option>
                        <option value="Antarctica/Rothera">Antarctica/Rothera</option>
                        <option value="Antarctica/Syowa">Antarctica/Syowa</option>
                        <option value="Antarctica/Troll">Antarctica/Troll</option>
                        <option value="Antarctica/Vostok">Antarctica/Vostok</option>
                    </optgroup>
                    <optgroup label="ARCTIC">
                        <option value="Arctic/Longyearbyen">Arctic/Longyearbyen</option>
                    </optgroup>
                    <optgroup label="ATLANTIC">
                        <option value="Atlantic/Azores">Atlantic/Azores</option>
                        <option value="Atlantic/Bermuda">Atlantic/Bermuda</option>
                        <option value="Atlantic/Canary">Atlantic/Canary</option>
                        <option value="Atlantic/Cape_Verde">Atlantic/Cape_Verde</option>
                        <option value="Atlantic/Faroe">Atlantic/Faroe</option>
                        <option value="Atlantic/Madeira">Atlantic/Madeira</option>
                        <option value="Atlantic/Reykjavik">Atlantic/Reykjavik</option>
                        <option value="Atlantic/South_Georgia">Atlantic/South_Georgia</option>
                        <option value="Atlantic/St_Helena">Atlantic/St_Helena</option>
                        <option value="Atlantic/Stanley">Atlantic/Stanley</option>
                    </optgroup>
                    <optgroup label="PACIFIC">
                        <option value="Pacific/Apia">Pacific/Apia</option>
                        <option value="Pacific/Auckland">Pacific/Auckland</option>
                        <option value="Pacific/Bougainville">Pacific/Bougainville</option>
                        <option value="Pacific/Chatham">Pacific/Chatham</option>
                        <option value="Pacific/Chuuk">Pacific/Chuuk</option>
                        <option value="Pacific/Easter">Pacific/Easter</option>
                        <option value="Pacific/Efate">Pacific/Efate</option>
                        <option value="Pacific/Enderbury">Pacific/Enderbury</option>
                        <option value="Pacific/Fakaofo">Pacific/Fakaofo</option>
                        <option value="Pacific/Fiji">Pacific/Fiji</option>
                        <option value="Pacific/Funafuti">Pacific/Funafuti</option>
                        <option value="Pacific/Galapagos">Pacific/Galapagos</option>
                        <option value="Pacific/Gambier">Pacific/Gambier</option>
                        <option value="Pacific/Guadalcanal">Pacific/Guadalcanal</option>
                        <option value="Pacific/Guam">Pacific/Guam</option>
                        <option value="Pacific/Honolulu">Pacific/Honolulu</option>
                        <option value="Pacific/Kiritimati">Pacific/Kiritimati</option>
                        <option value="Pacific/Kosrae">Pacific/Kosrae</option>
                        <option value="Pacific/Kwajalein">Pacific/Kwajalein</option>
                        <option value="Pacific/Majuro">Pacific/Majuro</option>
                        <option value="Pacific/Marquesas">Pacific/Marquesas</option>
                        <option value="Pacific/Midway">Pacific/Midway</option>
                        <option value="Pacific/Nauru">Pacific/Nauru</option>
                        <option value="Pacific/Niue">Pacific/Niue</option>
                        <option value="Pacific/Norfolk">Pacific/Norfolk</option>
                        <option value="Pacific/Noumea">Pacific/Noumea</option>
                        <option value="Pacific/Pago_Pago">Pacific/Pago_Pago</option>
                        <option value="Pacific/Palau">Pacific/Palau</option>
                        <option value="Pacific/Pitcairn">Pacific/Pitcairn</option>
                        <option value="Pacific/Pohnpei">Pacific/Pohnpei</option>
                        <option value="Pacific/Port_Moresby">Pacific/Port_Moresby</option>
                        <option value="Pacific/Rarotonga">Pacific/Rarotonga</option>
                        <option value="Pacific/Saipan">Pacific/Saipan</option>
                        <option value="Pacific/Tahiti">Pacific/Tahiti</option>
                        <option value="Pacific/Tarawa">Pacific/Tarawa</option>
                        <option value="Pacific/Tongatapu">Pacific/Tongatapu</option>
                        <option value="Pacific/Wake">Pacific/Wake</option>
                        <option value="Pacific/Wallis">Pacific/Wallis</option>
                    </optgroup>
                </select>
            </div>
        </div>


        <!--[UPCOMING] change account owner-->
        <?php if(config('visibility.contacts_modal_account_owner')): ?>
        <div class="form-group form-group-checkbox row hidden">
            <label
                class="col-sm-12 col-lg-3 col-form-label text-left"><?php echo e(cleanLang(__('lang.account_owner'))); ?>?</label>
            <div class="col-6 text-left p-t-5">
                <input type="checkbox" id="account_owner" name="account_owner" class="filled-in chk-col-light-blue"
                    <?php echo e(runtimeAccountOwnerDisabled($user['account_owner'] ?? '')); ?>

                    <?php echo e(runtimeAccountOwnerCheckbox($user['account_owner'] ?? '')); ?>>
                <label for="account_owner"></label>
            </div>
        </div>
        <?php endif; ?>

        <?php if(isset($page['section']) && $page['section'] == 'edit'): ?>
        <!--social profile-->
        <div class="spacer row">
            <div class="col-sm-12 col-lg-8">
                <?php echo e(cleanLang(__('lang.social_profile'))); ?>

            </div>
            <div class="col-sm-12 col-lg-4">
                <div class="switch  text-right">
                    <label>
                        <input type="checkbox" name="toggle_social_profile" id="toggle_social_profile"
                            class="js-switch-toggle-hidden-content" data-target="social_profile_section">
                        <span class="lever switch-col-light-blue"></span>
                    </label>
                </div>
            </div>
        </div>
        <div class="hidden" id="social_profile_section">
            <!--twitter-->
            <div class="form-group row">
                <label class="col-sm-12 col-lg-3 text-left control-label col-form-label">Twitter</label>
                <div class="col-sm-12 col-lg-9">
                    <input type="text" class="form-control form-control-sm" id="social_twitter" name="social_twitter"
                        value="<?php echo e($user->social_twitter ?? ''); ?>"
                        placeholder="<?php echo e(cleanLang(__('lang.social_profile_username'))); ?>">
                </div>
            </div>
            <!--facebook-->
            <div class="form-group row">
                <label class="col-sm-12 col-lg-3 text-left control-label col-form-label">Facebook</label>
                <div class="col-sm-12 col-lg-9">
                    <input type="text" class="form-control form-control-sm" id="social_facebook" name="social_facebook"
                        value="<?php echo e($user->social_facebook ?? ''); ?>"
                        placeholder="<?php echo e(cleanLang(__('lang.social_profile_username'))); ?>">
                </div>
            </div>
            <!--linkedin-->
            <div class="form-group row">
                <label class="col-sm-12 col-lg-3 text-left control-label col-form-label">LinkedIn</label>
                <div class="col-sm-12 col-lg-9">
                    <input type="text" class="form-control form-control-sm" id="social_linkedin" name="social_linkedin"
                        value="<?php echo e($user->social_linkedin ?? ''); ?>"
                        placeholder="<?php echo e(cleanLang(__('lang.social_profile_username'))); ?>">
                </div>
            </div>
            <!--github-->
            <div class="form-group row">
                <label class="col-sm-12 col-lg-3 text-left control-label col-form-label">Github</label>
                <div class="col-sm-12 col-lg-9">
                    <input type="text" class="form-control form-control-sm" id="social_github" name="social_github"
                        value="<?php echo e($user->social_github ?? ''); ?>"
                        placeholder="<?php echo e(cleanLang(__('lang.social_profile_username'))); ?>">
                </div>
            </div>
            <!--dribble-->
            <div class="form-group row">
                <label class="col-sm-12 col-lg-3 text-left control-label col-form-label">Dribble</label>
                <div class="col-sm-12 col-lg-9">
                    <input type="text" class="form-control form-control-sm" id="social_dribble" name="social_dribble"
                        value="<?php echo e($user->social_dribble ?? ''); ?>"
                        placeholder="<?php echo e(cleanLang(__('lang.social_profile_username'))); ?>">
                </div>
            </div>
        </div>
        <!--social profile-->
        <?php endif; ?>

        <!--pass source-->
        <input type="hidden" name="source" value="<?php echo e(request('source')); ?>">

        <!--notes-->
        <div class="row">
            <div class="col-12">
                <div><small><strong>* <?php echo e(cleanLang(__('lang.required'))); ?></strong></small></div>
            </div>
        </div>
    </div>
</div><?php /**PATH D:\my_apache\application\resources\views/pages/contacts/components/modals/add-edit-inc.blade.php ENDPATH**/ ?>