<?php
/**
 * 국가 목록 - 모든 국가의 정보 (이름, 전화코드, 국기 이모지)
 *
 * TypeScript에서 PHP로 변환된 국가 데이터베이스
 * 약 250개 국가/지역 포함
 *
 * @package Sonub
 * @subpackage etc/country
 */

// 모든 국가 목록
const COUNTRIES = [
    ['countryNameEn' => 'Andorra', 'countryNameLocal' => 'Andorra', 'countryCallingCode' => '376', 'flag' => '🇦🇩', 'region' => 'Europe'],
    ['countryNameEn' => 'Afghanistan', 'countryNameLocal' => 'د افغانستان اسلامي دولتدولت اسلامی افغانستان, جمهوری اسلامی افغانستان', 'countryCallingCode' => '93', 'flag' => '🇦🇫', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Antigua and Barbuda', 'countryNameLocal' => 'Antigua and Barbuda', 'countryCallingCode' => '1268', 'flag' => '🇦🇬', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'Anguilla', 'countryNameLocal' => 'Anguilla', 'countryCallingCode' => '1264', 'flag' => '🇦🇮', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'Albania', 'countryNameLocal' => 'Shqipëria', 'countryCallingCode' => '355', 'flag' => '🇦🇱', 'region' => 'Europe'],
    ['countryNameEn' => 'Armenia', 'countryNameLocal' => 'Հայաստան', 'countryCallingCode' => '374', 'flag' => '🇦🇲', 'region' => 'Europe'],
    ['countryNameEn' => 'Angola', 'countryNameLocal' => 'Angola', 'countryCallingCode' => '244', 'flag' => '🇦🇴', 'region' => 'Africa'],
    ['countryNameEn' => 'Antarctica', 'countryNameLocal' => 'Antarctica, Antártico, Antarctique, Антарктике', 'countryCallingCode' => '672', 'flag' => '🇦🇶', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Argentina', 'countryNameLocal' => 'Argentina', 'countryCallingCode' => '54', 'flag' => '🇦🇷', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'American Samoa', 'countryNameLocal' => 'American Samoa', 'countryCallingCode' => '1684', 'flag' => '🇦🇸', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Austria', 'countryNameLocal' => 'Österreich', 'countryCallingCode' => '43', 'flag' => '🇦🇹', 'region' => 'Europe'],
    ['countryNameEn' => 'Australia', 'countryNameLocal' => 'Australia', 'countryCallingCode' => '61', 'flag' => '🇦🇺', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Aruba', 'countryNameLocal' => 'Aruba', 'countryCallingCode' => '297', 'flag' => '🇦🇼', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'Åland Islands', 'countryNameLocal' => 'Åland', 'countryCallingCode' => '358', 'flag' => '🇦🇽', 'region' => 'Europe'],
    ['countryNameEn' => 'Azerbaijan', 'countryNameLocal' => 'Azərbaycan', 'countryCallingCode' => '994', 'flag' => '🇦🇿', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Bosnia and Herzegovina', 'countryNameLocal' => 'Bosna i Hercegovina', 'countryCallingCode' => '387', 'flag' => '🇧🇦', 'region' => 'Europe'],
    ['countryNameEn' => 'Barbados', 'countryNameLocal' => 'Barbados', 'countryCallingCode' => '1246', 'flag' => '🇧🇧', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'Bangladesh', 'countryNameLocal' => 'গণপ্রজাতন্ত্রী বাংলাদেশ', 'countryCallingCode' => '880', 'flag' => '🇧🇩', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Belgium', 'countryNameLocal' => 'België, Belgique, Belgien', 'countryCallingCode' => '32', 'flag' => '🇧🇪', 'region' => 'Europe'],
    ['countryNameEn' => 'Burkina Faso', 'countryNameLocal' => 'Burkina Faso', 'countryCallingCode' => '226', 'flag' => '🇧🇫', 'region' => 'Africa'],
    ['countryNameEn' => 'Bulgaria', 'countryNameLocal' => 'България', 'countryCallingCode' => '359', 'flag' => '🇧🇬', 'region' => 'Europe'],
    ['countryNameEn' => 'Bahrain', 'countryNameLocal' => 'البحرين', 'countryCallingCode' => '973', 'flag' => '🇧🇭', 'region' => 'Arab States'],
    ['countryNameEn' => 'Burundi', 'countryNameLocal' => 'Burundi', 'countryCallingCode' => '257', 'flag' => '🇧🇮', 'region' => 'Africa'],
    ['countryNameEn' => 'Benin', 'countryNameLocal' => 'Bénin', 'countryCallingCode' => '229', 'flag' => '🇧🇯', 'region' => 'Africa'],
    ['countryNameEn' => 'Saint Barthélemy', 'countryNameLocal' => 'Saint-Barthélemy', 'countryCallingCode' => '590', 'flag' => '🇧🇱', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'Bermuda', 'countryNameLocal' => 'Bermuda', 'countryCallingCode' => '1441', 'flag' => '🇧🇲', 'region' => 'North America'],
    ['countryNameEn' => 'Brunei Darussalam', 'countryNameLocal' => 'Brunei Darussalam', 'countryCallingCode' => '673', 'flag' => '🇧🇳', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Bolivia (Plurinational State of)', 'countryNameLocal' => 'Bolivia, Bulibiya, Volívia, Wuliwya', 'countryCallingCode' => '591', 'flag' => '🇧🇴', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'Bonaire, Sint Eustatius and Saba', 'countryNameLocal' => 'Caribisch Nederland', 'countryCallingCode' => '5997', 'flag' => '🇧🇶', 'region' => 'Unknown'],
    ['countryNameEn' => 'Brazil', 'countryNameLocal' => 'Brasil', 'countryCallingCode' => '55', 'flag' => '🇧🇷', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'Bhutan', 'countryNameLocal' => 'འབྲུག་ཡུལ', 'countryCallingCode' => '975', 'flag' => '🇧🇹', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Bouvet Island', 'countryNameLocal' => 'Bouvetøya', 'countryCallingCode' => '47', 'flag' => '🇧🇻', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'Botswana', 'countryNameLocal' => 'Botswana', 'countryCallingCode' => '267', 'flag' => '🇧🇼', 'region' => 'Africa'],
    ['countryNameEn' => 'Belarus', 'countryNameLocal' => 'Беларусь', 'countryCallingCode' => '375', 'flag' => '🇧🇾', 'region' => 'Europe'],
    ['countryNameEn' => 'Belize', 'countryNameLocal' => 'Belize', 'countryCallingCode' => '501', 'flag' => '🇧🇿', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'Canada', 'countryNameLocal' => 'Canada', 'countryCallingCode' => '1', 'flag' => '🇨🇦', 'region' => 'North America'],
    ['countryNameEn' => 'Switzerland', 'countryNameLocal' => 'Schweiz, Suisse, Svizzera, Svizra', 'countryCallingCode' => '41', 'flag' => '🇨🇭', 'region' => 'Europe'],
    ['countryNameEn' => "Côte d'Ivoire", 'countryNameLocal' => "Côte d'Ivoire", 'countryCallingCode' => '225', 'flag' => '🇨🇮', 'region' => 'Africa'],
    ['countryNameEn' => 'Chile', 'countryNameLocal' => 'Chile', 'countryCallingCode' => '56', 'flag' => '🇨🇱', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'Cameroon', 'countryNameLocal' => 'Cameroun, Cameroon', 'countryCallingCode' => '237', 'flag' => '🇨🇲', 'region' => 'Africa'],
    ['countryNameEn' => 'China', 'countryNameLocal' => '中国', 'countryCallingCode' => '86', 'flag' => '🇨🇳', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Colombia', 'countryNameLocal' => 'Colombia', 'countryCallingCode' => '57', 'flag' => '🇨🇴', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'Costa Rica', 'countryNameLocal' => 'Costa Rica', 'countryCallingCode' => '506', 'flag' => '🇨🇷', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'Cuba', 'countryNameLocal' => 'Cuba', 'countryCallingCode' => '53', 'flag' => '🇨🇺', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'Cabo Verde', 'countryNameLocal' => 'Cabo Verde', 'countryCallingCode' => '238', 'flag' => '🇨🇻', 'region' => 'Africa'],
    ['countryNameEn' => 'Curaçao', 'countryNameLocal' => 'Curaçao', 'countryCallingCode' => '599', 'flag' => '🇨🇼', 'region' => 'Unknown'],
    ['countryNameEn' => 'Christmas Island', 'countryNameLocal' => 'Christmas Island', 'countryCallingCode' => '61', 'flag' => '🇨🇽', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Cyprus', 'countryNameLocal' => 'Κύπρος, Kibris', 'countryCallingCode' => '357', 'flag' => '🇨🇾', 'region' => 'Europe'],
    ['countryNameEn' => 'Germany', 'countryNameLocal' => 'Deutschland', 'countryCallingCode' => '49', 'flag' => '🇩🇪', 'region' => 'Europe'],
    ['countryNameEn' => 'Djibouti', 'countryNameLocal' => 'Djibouti, جيبوتي, Jabuuti, Gabuutih', 'countryCallingCode' => '253', 'flag' => '🇩🇯', 'region' => 'Arab States'],
    ['countryNameEn' => 'Denmark', 'countryNameLocal' => 'Danmark', 'countryCallingCode' => '45', 'flag' => '🇩🇰', 'region' => 'Europe'],
    ['countryNameEn' => 'Dominica', 'countryNameLocal' => 'Dominica', 'countryCallingCode' => '767', 'flag' => '🇩🇲', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'Algeria', 'countryNameLocal' => 'الجزائر', 'countryCallingCode' => '213', 'flag' => '🇩🇿', 'region' => 'Arab States'],
    ['countryNameEn' => 'Ecuador', 'countryNameLocal' => 'Ecuador', 'countryCallingCode' => '593', 'flag' => '🇪🇨', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'Estonia', 'countryNameLocal' => 'Eesti', 'countryCallingCode' => '372', 'flag' => '🇪🇪', 'region' => 'Europe'],
    ['countryNameEn' => 'Egypt', 'countryNameLocal' => 'مصر', 'countryCallingCode' => '20', 'flag' => '🇪🇬', 'region' => 'Arab States'],
    ['countryNameEn' => 'Western Sahara', 'countryNameLocal' => 'Sahara Occidental', 'countryCallingCode' => '212', 'flag' => '🇪🇭', 'region' => 'Africa'],
    ['countryNameEn' => 'Eritrea', 'countryNameLocal' => 'ኤርትራ, إرتريا, Eritrea', 'countryCallingCode' => '291', 'flag' => '🇪🇷', 'region' => 'Africa'],
    ['countryNameEn' => 'Spain', 'countryNameLocal' => 'España', 'countryCallingCode' => '34', 'flag' => '🇪🇸', 'region' => 'Europe'],
    ['countryNameEn' => 'Ethiopia', 'countryNameLocal' => 'ኢትዮጵያ, Itoophiyaa', 'countryCallingCode' => '251', 'flag' => '🇪🇹', 'region' => 'Africa'],
    ['countryNameEn' => 'Finland', 'countryNameLocal' => 'Suomi', 'countryCallingCode' => '358', 'flag' => '🇫🇮', 'region' => 'Europe'],
    ['countryNameEn' => 'Fiji', 'countryNameLocal' => 'Fiji', 'countryCallingCode' => '679', 'flag' => '🇫🇯', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Micronesia (Federated States of)', 'countryNameLocal' => 'Micronesia', 'countryCallingCode' => '691', 'flag' => '🇫🇲', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'France', 'countryNameLocal' => 'France', 'countryCallingCode' => '33', 'flag' => '🇫🇷', 'region' => 'Europe'],
    ['countryNameEn' => 'Gabon', 'countryNameLocal' => 'Gabon', 'countryCallingCode' => '241', 'flag' => '🇬🇦', 'region' => 'Africa'],
    ['countryNameEn' => 'Grenada', 'countryNameLocal' => 'Grenada', 'countryCallingCode' => '1473', 'flag' => '🇬🇩', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'Georgia', 'countryNameLocal' => 'საქართველო', 'countryCallingCode' => '995', 'flag' => '🇬🇪', 'region' => 'Europe'],
    ['countryNameEn' => 'French Guiana', 'countryNameLocal' => 'Guyane française', 'countryCallingCode' => '594', 'flag' => '🇬🇫', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'Guernsey', 'countryNameLocal' => 'Guernsey', 'countryCallingCode' => '44', 'flag' => '🇬🇬', 'region' => 'Europe'],
    ['countryNameEn' => 'Ghana', 'countryNameLocal' => 'Ghana', 'countryCallingCode' => '233', 'flag' => '🇬🇭', 'region' => 'Africa'],
    ['countryNameEn' => 'Gibraltar', 'countryNameLocal' => 'Gibraltar', 'countryCallingCode' => '350', 'flag' => '🇬🇮', 'region' => 'Europe'],
    ['countryNameEn' => 'Greenland', 'countryNameLocal' => 'Kalaallit Nunaat, Grønland', 'countryCallingCode' => '299', 'flag' => '🇬🇱', 'region' => 'Europe'],
    ['countryNameEn' => 'Guinea', 'countryNameLocal' => 'Guinée', 'countryCallingCode' => '224', 'flag' => '🇬🇳', 'region' => 'Africa'],
    ['countryNameEn' => 'Guadeloupe', 'countryNameLocal' => 'Guadeloupe', 'countryCallingCode' => '590', 'flag' => '🇬🇵', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'Equatorial Guinea', 'countryNameLocal' => 'Guiena ecuatorial, Guinée équatoriale, Guiné Equatorial', 'countryCallingCode' => '240', 'flag' => '🇬🇶', 'region' => 'Africa'],
    ['countryNameEn' => 'Greece', 'countryNameLocal' => 'Ελλάδα', 'countryCallingCode' => '30', 'flag' => '🇬🇷', 'region' => 'Europe'],
    ['countryNameEn' => 'South Georgia and the South Sandwich Islands', 'countryNameLocal' => 'South Georgia and the South Sandwich Islands', 'countryCallingCode' => '500', 'flag' => '🇬🇸', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'Guatemala', 'countryNameLocal' => 'Guatemala', 'countryCallingCode' => '502', 'flag' => '🇬🇹', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'Guam', 'countryNameLocal' => 'Guam, Guåhån', 'countryCallingCode' => '1', 'flag' => '🇬🇺', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Guinea-Bissau', 'countryNameLocal' => 'Guiné-Bissau', 'countryCallingCode' => '245', 'flag' => '🇬🇼', 'region' => 'Africa'],
    ['countryNameEn' => 'Guyana', 'countryNameLocal' => 'Guyana', 'countryCallingCode' => '592', 'flag' => '🇬🇾', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'Hong Kong', 'countryNameLocal' => '香港, Hong Kong', 'countryCallingCode' => '852', 'flag' => '🇭🇰', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Honduras', 'countryNameLocal' => 'Honduras', 'countryCallingCode' => '504', 'flag' => '🇭🇳', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'Croatia', 'countryNameLocal' => 'Hrvatska', 'countryCallingCode' => '385', 'flag' => '🇭🇷', 'region' => 'Europe'],
    ['countryNameEn' => 'Haiti', 'countryNameLocal' => 'Haïti, Ayiti', 'countryCallingCode' => '509', 'flag' => '🇭🇹', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'Hungary', 'countryNameLocal' => 'Magyarország', 'countryCallingCode' => '36', 'flag' => '🇭🇺', 'region' => 'Europe'],
    ['countryNameEn' => 'Indonesia', 'countryNameLocal' => 'Indonesia', 'countryCallingCode' => '62', 'flag' => '🇮🇩', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Ireland', 'countryNameLocal' => 'Ireland, Éire', 'countryCallingCode' => '353', 'flag' => '🇮🇪', 'region' => 'Europe'],
    ['countryNameEn' => 'Israel', 'countryNameLocal' => 'ישראל', 'countryCallingCode' => '972', 'flag' => '🇮🇱', 'region' => 'Europe'],
    ['countryNameEn' => 'Isle of Man', 'countryNameLocal' => 'Isle of Man', 'countryCallingCode' => '44', 'flag' => '🇮🇲', 'region' => 'Europe'],
    ['countryNameEn' => 'India', 'countryNameLocal' => 'भारत, India', 'countryCallingCode' => '91', 'flag' => '🇮🇳', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'British Indian Ocean Territories', 'countryNameLocal' => 'British Indian Ocean Territories', 'countryCallingCode' => '246', 'flag' => '🇮🇴', 'region' => 'Indian Ocean'],
    ['countryNameEn' => 'Iraq', 'countryNameLocal' => 'العراق, Iraq', 'countryCallingCode' => '964', 'flag' => '🇮🇶', 'region' => 'Arab States'],
    ['countryNameEn' => 'Iran (Islamic Republic of)', 'countryNameLocal' => 'ایران', 'countryCallingCode' => '98', 'flag' => '🇮🇷', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Iceland', 'countryNameLocal' => 'Ísland', 'countryCallingCode' => '354', 'flag' => '🇮🇸', 'region' => 'Europe'],
    ['countryNameEn' => 'Italy', 'countryNameLocal' => 'Italia', 'countryCallingCode' => '39', 'flag' => '🇮🇹', 'region' => 'Europe'],
    ['countryNameEn' => 'Jersey', 'countryNameLocal' => 'Jersey', 'countryCallingCode' => '44', 'flag' => '🇯🇪', 'region' => 'Europe'],
    ['countryNameEn' => 'Jamaica', 'countryNameLocal' => 'Jamaica', 'countryCallingCode' => '876', 'flag' => '🇯🇲', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'Jordan', 'countryNameLocal' => 'الأُرْدُن', 'countryCallingCode' => '962', 'flag' => '🇯🇴', 'region' => 'Arab States'],
    ['countryNameEn' => 'Japan', 'countryNameLocal' => '日本', 'countryCallingCode' => '81', 'flag' => '🇯🇵', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Kenya', 'countryNameLocal' => 'Kenya', 'countryCallingCode' => '254', 'flag' => '🇰🇪', 'region' => 'Africa'],
    ['countryNameEn' => 'Kyrgyzstan', 'countryNameLocal' => 'Кыргызстан, Киргизия', 'countryCallingCode' => '996', 'flag' => '🇰🇬', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Cambodia', 'countryNameLocal' => 'កម្ពុជា', 'countryCallingCode' => '855', 'flag' => '🇰🇭', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'South Korea', 'countryNameLocal' => '대한민국', 'countryCallingCode' => '82', 'flag' => '🇰🇷', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Kiribati', 'countryNameLocal' => 'Kiribati', 'countryCallingCode' => '686', 'flag' => '🇰🇮', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Saint Kitts and Nevis', 'countryNameLocal' => 'Saint Kitts and Nevis', 'countryCallingCode' => '1869', 'flag' => '🇰🇳', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'Kuwait', 'countryNameLocal' => 'الكويت', 'countryCallingCode' => '965', 'flag' => '🇰🇼', 'region' => 'Arab States'],
    ['countryNameEn' => 'Kazakhstan', 'countryNameLocal' => 'Қазақстан, Казахстан', 'countryCallingCode' => '7', 'flag' => '🇰🇿', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Lebanon', 'countryNameLocal' => 'لبنان, Liban', 'countryCallingCode' => '961', 'flag' => '🇱🇧', 'region' => 'Arab States'],
    ['countryNameEn' => 'Saint Lucia', 'countryNameLocal' => 'Saint Lucia', 'countryCallingCode' => '1758', 'flag' => '🇱🇨', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'Liechtenstein', 'countryNameLocal' => 'Liechtenstein', 'countryCallingCode' => '423', 'flag' => '🇱🇮', 'region' => 'Europe'],
    ['countryNameEn' => 'Sri Lanka', 'countryNameLocal' => 'ශ්‍රී ලංකා, இலங்கை', 'countryCallingCode' => '94', 'flag' => '🇱🇰', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Liberia', 'countryNameLocal' => 'Liberia', 'countryCallingCode' => '231', 'flag' => '🇱🇷', 'region' => 'Africa'],
    ['countryNameEn' => 'Lesotho', 'countryNameLocal' => 'Lesotho', 'countryCallingCode' => '266', 'flag' => '🇱🇸', 'region' => 'Africa'],
    ['countryNameEn' => 'Lithuania', 'countryNameLocal' => 'Lietuva', 'countryCallingCode' => '370', 'flag' => '🇱🇹', 'region' => 'Europe'],
    ['countryNameEn' => 'Luxembourg', 'countryNameLocal' => 'Lëtzebuerg, Luxembourg, Luxemburg', 'countryCallingCode' => '352', 'flag' => '🇱🇺', 'region' => 'Europe'],
    ['countryNameEn' => 'Latvia', 'countryNameLocal' => 'Latvija', 'countryCallingCode' => '371', 'flag' => '🇱🇻', 'region' => 'Europe'],
    ['countryNameEn' => 'Libya', 'countryNameLocal' => 'ليبيا', 'countryCallingCode' => '218', 'flag' => '🇱🇾', 'region' => 'Arab States'],
    ['countryNameEn' => 'Morocco', 'countryNameLocal' => 'Maroc, ⵍⵎⵖⵔⵉⴱ, المغرب', 'countryCallingCode' => '212', 'flag' => '🇲🇦', 'region' => 'Arab States'],
    ['countryNameEn' => 'Monaco', 'countryNameLocal' => 'Monaco', 'countryCallingCode' => '377', 'flag' => '🇲🇨', 'region' => 'Europe'],
    ['countryNameEn' => 'Montenegro', 'countryNameLocal' => 'Crna Gora, Црна Гора', 'countryCallingCode' => '382', 'flag' => '🇲🇪', 'region' => 'Europe'],
    ['countryNameEn' => 'Saint Martin (French part)', 'countryNameLocal' => 'Saint-Martin', 'countryCallingCode' => '590', 'flag' => '🇲🇫', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'Madagascar', 'countryNameLocal' => 'Madagasikara, Madagascar', 'countryCallingCode' => '261', 'flag' => '🇲🇬', 'region' => 'Africa'],
    ['countryNameEn' => 'Mali', 'countryNameLocal' => 'Mali', 'countryCallingCode' => '223', 'flag' => '🇲🇱', 'region' => 'Africa'],
    ['countryNameEn' => 'Myanmar', 'countryNameLocal' => 'မြန်မာ', 'countryCallingCode' => '95', 'flag' => '🇲🇲', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Mongolia', 'countryNameLocal' => 'Монгол Улс', 'countryCallingCode' => '976', 'flag' => '🇲🇳', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Macao', 'countryNameLocal' => '澳門, Macau', 'countryCallingCode' => '853', 'flag' => '🇲🇴', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Martinique', 'countryNameLocal' => 'Martinique', 'countryCallingCode' => '596', 'flag' => '🇲🇶', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'Mauritania', 'countryNameLocal' => 'موريتانيا, Mauritanie', 'countryCallingCode' => '222', 'flag' => '🇲🇷', 'region' => 'Arab States'],
    ['countryNameEn' => 'Montserrat', 'countryNameLocal' => 'Montserrat', 'countryCallingCode' => '1664', 'flag' => '🇲🇸', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'Malta', 'countryNameLocal' => 'Malta', 'countryCallingCode' => '356', 'flag' => '🇲🇹', 'region' => 'Europe'],
    ['countryNameEn' => 'Mauritius', 'countryNameLocal' => 'Maurice, Mauritius', 'countryCallingCode' => '230', 'flag' => '🇲🇺', 'region' => 'Africa'],
    ['countryNameEn' => 'Maldives', 'countryNameLocal' => 'ދިވެހި', 'countryCallingCode' => '960', 'flag' => '🇲🇻', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Malawi', 'countryNameLocal' => 'Malawi', 'countryCallingCode' => '265', 'flag' => '🇲🇼', 'region' => 'Africa'],
    ['countryNameEn' => 'Mexico', 'countryNameLocal' => 'México', 'countryCallingCode' => '52', 'flag' => '🇲🇽', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'Malaysia', 'countryNameLocal' => 'Bahasa Melayu, بهاس ملايو‎', 'countryCallingCode' => '60', 'flag' => '🇲🇾', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Mozambique', 'countryNameLocal' => 'Mozambique', 'countryCallingCode' => '258', 'flag' => '🇲🇿', 'region' => 'Africa'],
    ['countryNameEn' => 'Namibia', 'countryNameLocal' => 'Namibia', 'countryCallingCode' => '264', 'flag' => '🇳🇦', 'region' => 'Africa'],
    ['countryNameEn' => 'New Caledonia', 'countryNameLocal' => 'Nouvelle-Calédonie', 'countryCallingCode' => '687', 'flag' => '🇳🇨', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Norfolk Island', 'countryNameLocal' => 'Norfolk Island', 'countryCallingCode' => '672', 'flag' => '🇳🇫', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Nigeria', 'countryNameLocal' => 'Nigeria', 'countryCallingCode' => '234', 'flag' => '🇳🇬', 'region' => 'Africa'],
    ['countryNameEn' => 'Nicaragua', 'countryNameLocal' => 'Nicaragua', 'countryCallingCode' => '505', 'flag' => '🇳🇮', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'Norway', 'countryNameLocal' => 'Norge, Noreg', 'countryCallingCode' => '47', 'flag' => '🇳🇴', 'region' => 'Europe'],
    ['countryNameEn' => 'Nepal', 'countryNameLocal' => 'नेपाली', 'countryCallingCode' => '977', 'flag' => '🇳🇵', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Nauru', 'countryNameLocal' => 'Nauru', 'countryCallingCode' => '674', 'flag' => '🇳🇷', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Niue', 'countryNameLocal' => 'Niue', 'countryCallingCode' => '683', 'flag' => '🇳🇺', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'New Zealand', 'countryNameLocal' => 'New Zealand', 'countryCallingCode' => '64', 'flag' => '🇳🇿', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Oman', 'countryNameLocal' => 'سلطنة عُمان', 'countryCallingCode' => '968', 'flag' => '🇴🇲', 'region' => 'Arab States'],
    ['countryNameEn' => 'Panama', 'countryNameLocal' => 'Panama', 'countryCallingCode' => '507', 'flag' => '🇵🇦', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'Peru', 'countryNameLocal' => 'Perú', 'countryCallingCode' => '51', 'flag' => '🇵🇪', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'French Polynesia', 'countryNameLocal' => 'Polynésie française', 'countryCallingCode' => '689', 'flag' => '🇵🇫', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Papua New Guinea', 'countryNameLocal' => 'Papua New Guinea', 'countryCallingCode' => '675', 'flag' => '🇵🇬', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Pakistan', 'countryNameLocal' => 'پاکستان', 'countryCallingCode' => '92', 'flag' => '🇵🇰', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Poland', 'countryNameLocal' => 'Polska', 'countryCallingCode' => '48', 'flag' => '🇵🇱', 'region' => 'Europe'],
    ['countryNameEn' => 'Saint Pierre and Miquelon', 'countryNameLocal' => 'Saint-Pierre-et-Miquelon', 'countryCallingCode' => '508', 'flag' => '🇵🇲', 'region' => 'North America'],
    ['countryNameEn' => 'Pitcairn', 'countryNameLocal' => 'Pitcairn', 'countryCallingCode' => '64', 'flag' => '🇵🇳', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Puerto Rico', 'countryNameLocal' => 'Puerto Rico', 'countryCallingCode' => '1', 'flag' => '🇵🇷', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'Palestine, State of', 'countryNameLocal' => 'Palestinian Territory', 'countryCallingCode' => '970', 'flag' => '🇵🇸', 'region' => 'Arab States'],
    ['countryNameEn' => 'Portugal', 'countryNameLocal' => 'Portugal', 'countryCallingCode' => '351', 'flag' => '🇵🇹', 'region' => 'Europe'],
    ['countryNameEn' => 'Palau', 'countryNameLocal' => 'Palau', 'countryCallingCode' => '680', 'flag' => '🇵🇼', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Paraguay', 'countryNameLocal' => 'Paraguay', 'countryCallingCode' => '595', 'flag' => '🇵🇾', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'Qatar', 'countryNameLocal' => 'قطر', 'countryCallingCode' => '974', 'flag' => '🇶🇦', 'region' => 'Arab States'],
    ['countryNameEn' => 'Réunion', 'countryNameLocal' => 'La Réunion', 'countryCallingCode' => '262', 'flag' => '🇷🇪', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Romania', 'countryNameLocal' => 'România', 'countryCallingCode' => '40', 'flag' => '🇷🇴', 'region' => 'Europe'],
    ['countryNameEn' => 'Serbia', 'countryNameLocal' => 'Србија', 'countryCallingCode' => '381', 'flag' => '🇷🇸', 'region' => 'Europe'],
    ['countryNameEn' => 'Russia', 'countryNameLocal' => 'Россия', 'countryCallingCode' => '7', 'flag' => '🇷🇺', 'region' => 'Europe'],
    ['countryNameEn' => 'Rwanda', 'countryNameLocal' => 'Rwanda', 'countryCallingCode' => '250', 'flag' => '🇷🇼', 'region' => 'Africa'],
    ['countryNameEn' => 'Saudi Arabia', 'countryNameLocal' => 'السعودية', 'countryCallingCode' => '966', 'flag' => '🇸🇦', 'region' => 'Arab States'],
    ['countryNameEn' => 'Solomon Islands', 'countryNameLocal' => 'Solomon Islands', 'countryCallingCode' => '677', 'flag' => '🇸🇧', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Seychelles', 'countryNameLocal' => 'Seychelles', 'countryCallingCode' => '248', 'flag' => '🇸🇨', 'region' => 'Africa'],
    ['countryNameEn' => 'Sweden', 'countryNameLocal' => 'Sverige', 'countryCallingCode' => '46', 'flag' => '🇸🇪', 'region' => 'Europe'],
    ['countryNameEn' => 'Singapore', 'countryNameLocal' => 'Singapore', 'countryCallingCode' => '65', 'flag' => '🇸🇬', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Saint Helena, Ascension and Tristan da Cunha', 'countryNameLocal' => 'Saint Helena', 'countryCallingCode' => '290', 'flag' => '🇸🇭', 'region' => 'Africa'],
    ['countryNameEn' => 'Slovenia', 'countryNameLocal' => 'Slovenija', 'countryCallingCode' => '386', 'flag' => '🇸🇮', 'region' => 'Europe'],
    ['countryNameEn' => 'Svalbard and Jan Mayen', 'countryNameLocal' => 'Svalbard and Jan Mayen', 'countryCallingCode' => '4779', 'flag' => '🇸🇯', 'region' => 'Europe'],
    ['countryNameEn' => 'Slovakia', 'countryNameLocal' => 'Slovensko', 'countryCallingCode' => '421', 'flag' => '🇸🇰', 'region' => 'Europe'],
    ['countryNameEn' => 'Sierra Leone', 'countryNameLocal' => 'Sierra Leone', 'countryCallingCode' => '232', 'flag' => '🇸🇱', 'region' => 'Africa'],
    ['countryNameEn' => 'Republic of San Marino', 'countryNameLocal' => 'Repubblica di San Marino', 'countryCallingCode' => '378', 'flag' => '🇸🇲', 'region' => 'Europe'],
    ['countryNameEn' => 'Senegal', 'countryNameLocal' => 'Sénégal', 'countryCallingCode' => '221', 'flag' => '🇸🇳', 'region' => 'Africa'],
    ['countryNameEn' => 'Somalia', 'countryNameLocal' => 'Somalia, الصومال', 'countryCallingCode' => '252', 'flag' => '🇸🇴', 'region' => 'Arab States'],
    ['countryNameEn' => 'Suriname', 'countryNameLocal' => 'Suriname', 'countryCallingCode' => '597', 'flag' => '🇸🇷', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'South Sudan', 'countryNameLocal' => 'South Sudan', 'countryCallingCode' => '211', 'flag' => '🇸🇸', 'region' => 'Africa'],
    ['countryNameEn' => 'Sao Tome and Principe', 'countryNameLocal' => 'São Tomé e Príncipe', 'countryCallingCode' => '239', 'flag' => '🇸🇹', 'region' => 'Africa'],
    ['countryNameEn' => 'El Salvador', 'countryNameLocal' => 'El Salvador', 'countryCallingCode' => '503', 'flag' => '🇸🇻', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'Sint Maarten (Dutch part)', 'countryNameLocal' => 'Sint Maarten', 'countryCallingCode' => '1721', 'flag' => '🇸🇽', 'region' => 'Unknown'],
    ['countryNameEn' => 'Syrian Arab Republic', 'countryNameLocal' => 'سوريا, Sūriyya', 'countryCallingCode' => '963', 'flag' => '🇸🇾', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Chad', 'countryNameLocal' => 'Tchad, تشاد', 'countryCallingCode' => '235', 'flag' => '🇹🇩', 'region' => 'Africa'],
    ['countryNameEn' => 'Togo', 'countryNameLocal' => 'Togo', 'countryCallingCode' => '228', 'flag' => '🇹🇬', 'region' => 'Africa'],
    ['countryNameEn' => 'Thailand', 'countryNameLocal' => 'ประเทศไทย', 'countryCallingCode' => '66', 'flag' => '🇹🇭', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Tajikistan', 'countryNameLocal' => ',', 'countryCallingCode' => '992', 'flag' => '🇹🇯', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Tokelau', 'countryNameLocal' => 'Tokelau', 'countryCallingCode' => '690', 'flag' => '🇹🇰', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Timor-Leste', 'countryNameLocal' => "Timor-Leste, Timor Lorosa'e", 'countryCallingCode' => '670', 'flag' => '🇹🇱', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Turkmenistan', 'countryNameLocal' => 'Türkmenistan', 'countryCallingCode' => '993', 'flag' => '🇹🇲', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Tunisia', 'countryNameLocal' => 'تونس, Tunisie', 'countryCallingCode' => '216', 'flag' => '🇹🇳', 'region' => 'Arab States'],
    ['countryNameEn' => 'Tonga', 'countryNameLocal' => 'Tonga', 'countryCallingCode' => '676', 'flag' => '🇹🇴', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Turkey', 'countryNameLocal' => 'Türkiye', 'countryCallingCode' => '90', 'flag' => '🇹🇷', 'region' => 'Europe'],
    ['countryNameEn' => 'Trinidad and Tobago', 'countryNameLocal' => 'Trinidad and Tobago', 'countryCallingCode' => '868', 'flag' => '🇹🇹', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'Tuvalu', 'countryNameLocal' => 'Tuvalu', 'countryCallingCode' => '688', 'flag' => '🇹🇻', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'United Republic of Tanzania', 'countryNameLocal' => 'Tanzania', 'countryCallingCode' => '255', 'flag' => '🇹🇿', 'region' => 'Africa'],
    ['countryNameEn' => 'Ukraine', 'countryNameLocal' => 'Україна', 'countryCallingCode' => '380', 'flag' => '🇺🇦', 'region' => 'Europe'],
    ['countryNameEn' => 'Uganda', 'countryNameLocal' => 'Uganda', 'countryCallingCode' => '256', 'flag' => '🇺🇬', 'region' => 'Africa'],
    ['countryNameEn' => 'United States of America', 'countryNameLocal' => 'United States of America', 'countryCallingCode' => '1', 'flag' => '🇺🇸', 'region' => 'North America'],
    ['countryNameEn' => 'Uruguay', 'countryNameLocal' => 'Uruguay', 'countryCallingCode' => '598', 'flag' => '🇺🇾', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'Uzbekistan', 'countryNameLocal' => 'Oʻzbek, Ўзбек, أۇزبېك‎', 'countryCallingCode' => '998', 'flag' => '🇺🇿', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Saint Vincent and the Grenadines', 'countryNameLocal' => 'Saint Vincent and the Grenadines', 'countryCallingCode' => '1784', 'flag' => '🇻🇨', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'Venezuela (Bolivarian Republic of)', 'countryNameLocal' => 'Venezuela', 'countryCallingCode' => '58', 'flag' => '🇻🇪', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'Virgin Islands (British)', 'countryNameLocal' => 'British Virgin Islands', 'countryCallingCode' => '1284', 'flag' => '🇻🇬', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'Virgin Islands (U.S.)', 'countryNameLocal' => 'United States Virgin Islands', 'countryCallingCode' => '1340', 'flag' => '🇻🇮', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'Vietnam', 'countryNameLocal' => 'Việt Nam', 'countryCallingCode' => '84', 'flag' => '🇻🇳', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Vanuatu', 'countryNameLocal' => 'Vanuatu', 'countryCallingCode' => '678', 'flag' => '🇻🇺', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Wallis and Futuna', 'countryNameLocal' => 'Wallis-et-Futuna', 'countryCallingCode' => '681', 'flag' => '🇼🇫', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Samoa', 'countryNameLocal' => 'Samoa', 'countryCallingCode' => '685', 'flag' => '🇼🇸', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Yemen', 'countryNameLocal' => 'اليَمَن', 'countryCallingCode' => '967', 'flag' => '🇾🇪', 'region' => 'Arab States'],
    ['countryNameEn' => 'Mayotte', 'countryNameLocal' => 'Mayotte', 'countryCallingCode' => '262', 'flag' => '🇾🇹', 'region' => 'Africa'],
    ['countryNameEn' => 'South Africa', 'countryNameLocal' => 'South Africa', 'countryCallingCode' => '27', 'flag' => '🇿🇦', 'region' => 'Africa'],
    ['countryNameEn' => 'Zambia', 'countryNameLocal' => 'Zambia', 'countryCallingCode' => '260', 'flag' => '🇿🇲', 'region' => 'Africa'],
    ['countryNameEn' => 'Zimbabwe', 'countryNameLocal' => 'Zimbabwe', 'countryCallingCode' => '263', 'flag' => '🇿🇼', 'region' => 'Africa'],
    ['countryNameEn' => 'Eswatini', 'countryNameLocal' => 'Swaziland', 'countryCallingCode' => '268', 'flag' => '🇸🇿', 'region' => 'Africa'],
    ['countryNameEn' => 'North Macedonia', 'countryNameLocal' => 'Македонија', 'countryCallingCode' => '389', 'flag' => '🇲🇰', 'region' => 'Europe'],
    ['countryNameEn' => 'Philippines', 'countryNameLocal' => 'Philippines', 'countryCallingCode' => '63', 'flag' => '🇵🇭', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Netherlands', 'countryNameLocal' => 'Nederland', 'countryCallingCode' => '31', 'flag' => '🇳🇱', 'region' => 'Europe'],
    ['countryNameEn' => 'United Arab Emirates', 'countryNameLocal' => 'دولة الإمارات العربيّة المتّحدة', 'countryCallingCode' => '971', 'flag' => '🇦🇪', 'region' => 'Arab States'],
    ['countryNameEn' => 'Republic of Moldova', 'countryNameLocal' => 'Moldova, Молдавия', 'countryCallingCode' => '373', 'flag' => '🇲🇩', 'region' => 'Europe'],
    ['countryNameEn' => 'Gambia', 'countryNameLocal' => 'The Gambia', 'countryCallingCode' => '220', 'flag' => '🇬🇲', 'region' => 'Africa'],
    ['countryNameEn' => 'Dominican Republic', 'countryNameLocal' => 'República Dominicana', 'countryCallingCode' => '1', 'flag' => '🇩🇴', 'region' => 'South/Latin America'],
    ['countryNameEn' => 'Sudan', 'countryNameLocal' => 'السودان', 'countryCallingCode' => '249', 'flag' => '🇸🇩', 'region' => 'Arab States'],
    ['countryNameEn' => "Lao People's Democratic Republic", 'countryNameLocal' => 'ປະຊາຊົນລາວ', 'countryCallingCode' => '856', 'flag' => '🇱🇦', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Taiwan, Province of China', 'countryNameLocal' => 'Taiwan', 'countryCallingCode' => '886', 'flag' => '🇹🇼', 'region' => 'Asia & Pacific'],
    ['countryNameEn' => 'Republic of the Congo', 'countryNameLocal' => 'République du Congo', 'countryCallingCode' => '242', 'flag' => '🇨🇬', 'region' => 'Africa'],
    ['countryNameEn' => 'Czechia', 'countryNameLocal' => 'Česká republika', 'countryCallingCode' => '420', 'flag' => '🇨🇿', 'region' => 'Europe'],
    ['countryNameEn' => 'United Kingdom', 'countryNameLocal' => 'Great Britain', 'countryCallingCode' => '44', 'flag' => '🇬🇧', 'region' => 'Europe'],
    ['countryNameEn' => 'Niger', 'countryNameLocal' => 'Niger', 'countryCallingCode' => '227', 'flag' => '🇳🇪', 'region' => 'Africa'],
    ['countryNameEn' => 'Democratic Republic of the Congo', 'countryNameLocal' => 'Democratic Republic of the Congo', 'countryCallingCode' => '243', 'flag' => '🇨🇩', 'region' => 'Africa'],
    ['countryNameEn' => 'Commonwealth of The Bahamas', 'countryNameLocal' => 'Commonwealth of The Bahamas', 'countryCallingCode' => '1 242', 'flag' => '🇧🇸', 'region' => 'Caribbean'],
    ['countryNameEn' => 'Cocos (Keeling) Islands', 'countryNameLocal' => 'Pulu Kokos (Keeling)', 'countryCallingCode' => '61 891', 'flag' => '🇨🇨', 'region' => 'Australia'],
    ['countryNameEn' => 'Central African Republic', 'countryNameLocal' => 'République centrafricaine', 'countryCallingCode' => '236', 'flag' => '🇨🇫', 'region' => 'Africa'],
    ['countryNameEn' => 'Cook Islands', 'countryNameLocal' => "Kūki 'Āirani", 'countryCallingCode' => '682', 'flag' => '🇨🇰', 'region' => 'South Pacific Ocean'],
    ['countryNameEn' => 'Falkland Islands', 'countryNameLocal' => 'Falkland Islands', 'countryCallingCode' => '500', 'flag' => '🇫🇰', 'region' => 'South Atlantic Ocean'],
    ['countryNameEn' => 'Faroe Islands', 'countryNameLocal' => 'Færøerne', 'countryCallingCode' => '298', 'flag' => '🇫🇴', 'region' => 'Europe'],
    ['countryNameEn' => 'Territory of Heard Island and McDonald Islands', 'countryNameLocal' => 'Territory of Heard Island and McDonald Islands', 'countryCallingCode' => '672', 'flag' => '🇭🇲', 'region' => 'Indian Ocean'],
    ['countryNameEn' => 'British Indian Ocean Territory', 'countryNameLocal' => 'British Indian Ocean Territory', 'countryCallingCode' => '246', 'flag' => '🇮🇴', 'region' => 'Indian Ocean'],
    ['countryNameEn' => 'Comoros', 'countryNameLocal' => 'Umoja wa Komori', 'countryCallingCode' => '269', 'flag' => '🇰🇲', 'region' => 'Indian Ocean'],
    ['countryNameEn' => 'Cayman Islands', 'countryNameLocal' => 'Cayman Islands', 'countryCallingCode' => '1 345', 'flag' => '🇰🇾', 'region' => 'Caribbean Sea'],
    ['countryNameEn' => 'Republic of the Marshall Islands', 'countryNameLocal' => 'Aolepān Aorōkin Ṃajeḷ', 'countryCallingCode' => '692', 'flag' => '🇲🇭', 'region' => 'Pacific Ocean'],
    ['countryNameEn' => 'Commonwealth of the Northern Mariana Islands', 'countryNameLocal' => 'Sankattan Siha Na Islas Mariånas', 'countryCallingCode' => '1 670', 'flag' => '🇲🇵', 'region' => 'Pacific Ocean'],
    ['countryNameEn' => 'Turks and Caicos Islands', 'countryNameLocal' => 'Turks and Caicos Islands', 'countryCallingCode' => '1 649', 'flag' => '🇹🇨', 'region' => 'Atlantic Ocean'],
    ['countryNameEn' => 'French Southern and Antarctic Lands', 'countryNameLocal' => 'Terres australes et antarctiques françaises', 'countryCallingCode' => '672', 'flag' => '🇹🇫', 'region' => 'Indian Ocean'],
    ['countryNameEn' => 'United States Minor Outlying Islands', 'countryNameLocal' => 'United States Minor Outlying Islands', 'countryCallingCode' => '1', 'flag' => '🇺🇲', 'region' => 'Pacific Ocean'],
    ['countryNameEn' => 'Holy See', 'countryNameLocal' => 'Sancta Sedes', 'countryCallingCode' => '39', 'flag' => '🇻🇦', 'region' => 'Europe'],
    ['countryNameEn' => 'Republic of Kosovo', 'countryNameLocal' => 'Republika e Kosovës', 'countryCallingCode' => '383', 'flag' => '🇽🇰', 'region' => 'Europe'],
];

/**
 * 국가 코드로 국가 정보 검색
 *
 * @param string $code 국가 호출 코드 (예: '63', '82')
 * @return array|null 국가 정보 배열 또는 null
 *
 * @example
 * $country = get_country_by_code('63'); // 필리핀
 * echo $country['countryNameEn']; // Philippines
 */
function get_country_by_code($code) {
    // 한글 주석
    foreach (COUNTRIES as $country) {
        if ($country['countryCallingCode'] === $code) {
            return $country;
        }
    }
    return null;
}

/**
 * 국가명으로 국가 정보 검색
 *
 * @param string $name 국가 로컬명 (예: 'Philippines', '대한민국')
 * @return array|null 국가 정보 배열 또는 null
 *
 * @example
 * $country = get_country_by_local_name('대한민국');
 * echo $country['flag']; // 🇰🇷
 */
function get_country_by_local_name($name) {
    foreach (COUNTRIES as $country) {
        if ($country['countryNameLocal'] === $name) {
            return $country;
        }
    }
    return null;
}

/**
 * 지역별로 국가 목록 그룹화
 *
 * @return array 지역별 국가 배열 [['region' => '지역명', 'countries' => [...]]]
 *
 * @example
 * $regions = get_countries_by_region();
 * foreach ($regions as $region) {
 *     echo $region['region'];
 *     foreach ($region['countries'] as $country) {
 *         echo $country['countryNameEn'];
 *     }
 * }
 */
function get_countries_by_region() {
    $regions = [];

    // 지역 목록 추출
    foreach (COUNTRIES as $country) {
        $region = $country['region'];
        if (!isset($regions[$region])) {
            $regions[$region] = [];
        }
        $regions[$region][] = $country;
    }

    // 결과 포맷팅
    $result = [];
    foreach ($regions as $region => $countries) {
        $result[] = [
            'region' => $region,
            'countries' => $countries
        ];
    }

    return $result;
}

/**
 * 알파벳 순서로 국가 목록 그룹화
 *
 * @return array 알파벳별 국가 배열 [['letter' => 'A', 'countries' => [...]]]
 *
 * @example
 * $alphabets = get_countries_by_alphabet();
 * foreach ($alphabets as $group) {
 *     if (!empty($group['countries'])) {
 *         echo $group['letter'];
 *         foreach ($group['countries'] as $country) {
 *             echo $country['countryNameEn'];
 *         }
 *     }
 * }
 */
function get_countries_by_alphabet() {
    $result = [];
    $letters = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ');

    foreach ($letters as $letter) {
        $countries = [];

        // 해당 문자로 시작하는 국가 찾기
        foreach (COUNTRIES as $country) {
            if (strtoupper(substr($country['countryNameEn'], 0, 1)) === $letter) {
                $countries[] = $country;
            }
        }

        // 알파벳순으로 정렬
        usort($countries, function($a, $b) {
            return strcmp($a['countryNameEn'], $b['countryNameEn']);
        });

        $result[] = [
            'letter' => $letter,
            'countries' => $countries
        ];
    }

    return $result;
}

/**
 * 모든 국가 목록 반환
 *
 * @return array 모든 국가 정보 배열
 */
function get_all_countries() {
    return COUNTRIES;
}

/**
 * 국가 개수 반환
 *
 * @return int 등록된 국가 총 개수
 */
function get_countries_count() {
    return count(COUNTRIES);
}
