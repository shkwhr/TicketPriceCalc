<?php
date_default_timezone_set ('Asia/Tokyo');

/**
 * 人数入力チェック
 * 「大人」(第一引数)は必須入力
 * @param  array $argv コマンド引数
 * @return bool        成否
 */
function checkPersons(array $argv): bool
{
    if (count($argv) <= 1 ) {
        print_r("「大人」人数を入力して下さい。");
        return false;
    } else if (count($argv) > 4) {
        print_r("「大人 子供 シニア」の形式で人数を入力してください。");
        return false;
    }
    return true;
}

/**
 * 引数指定位置の人数を取得(最大50人)
 * @param  array $argv コマンド引数
 * @return int|bool    人数|失敗時bool
 */
function getPersons(array $argv, int $number): int|bool
{
    if (!isset($argv[$number]) || !is_numeric($argv[$number])) {
        return false;
    }
    $person_num = intval($argv[$number]);
    if (is_int($person_num)) {
        if ($person_num > 50) {
            print_r("受付可能人数は最大50人です。");
            return false;
        }
        return $person_num;
    }

    return false;
}

/**
 * 団体割引対象チェック
 * 10人以上(子供0.5人換算)を対象とする
 * @param  int $adult  大人人数
 * @param  int $child  子供人数
 * @param  int $senior シニア人数
 * @return bool        対象可否
 */
function isGroupDiscount(int $adult, int $child, int $senior): bool
{
    $group_persons = $adult + ($child * 0.5) + $senior;
    return ($group_persons >= 10) ? true : false;

}

/**
 * 料金計算
 * 大人:1000円、子供500円、シニア800円
 * @param  int $adult    大人人数
 * @param  int $child    子供人数
 * @param  int $senior   シニア人数
 * @param  int $discount 割引料金
 * @return int           合計金額
 */
function calcPrice(int $adult, int $child, int $senior, int $discount = 0): int
{
    return ($adult * (1000 - $discount)) + ($child * (500 - $discount)) + ($senior * (800 - $discount)); 
}

/**
 * 曜日、時間帯より料金計算を行う
 * 土日: 1.5%割増、平日17時以降は300円引
 * 10名以上団体割引(1割引)
 * 小数点以下切り上げ
 * @param  int $adult    大人人数
 * @param  int $child    子供人数
 * @param  int $senior   シニア人数
 * @param  int $now      strtoint変換現在時刻
 * @return int           料金
 */
function calcWeekdaytimePrice(int $adult, int $child, int $senior, int $now): int
{
    switch (date('w')) {
        case 0:     // 日
        case 6:     // 土
            $price  = calcPrice($adult, $child, $senior);
            $price *= 1.15;
            break;
        default:    // 平日
            // 17時以降は300円割引
            $discount = ($now >= strtotime('17:00:00')) ? 300 : 0;
            $price    = calcPrice($adult, $child, $senior, $discount);
            break;
    }
    
    // 団体割引
    if (isGroupDiscount($adult, $child, $senior)) {
        $price *= 0.9;
    }

    return ceil($price);
}



$adult  = 0;
$child  = 0;
$senior = 0;

// 入力値チェック
if (!checkPersons($argv)) {
    exit(0);
}
if (($adult = getPersons($argv, 1)) === false) {
    print_r("「大人」人数の入力内容が正しくありません。");
    exit(0);
}
if (isset($argv[2])) {
    if (($child = getPersons($argv, 2)) === false) {
        print_r("「子供」人数の入力内容が正しくありません。");
        exit(0);
    }
}
if (isset($argv[3])) {
    if (($senior = getPersons($argv, 3)) === false) {
        print_r("「シニア」人数の入力内容が正しくありません。");
        exit(0);
    }
}

// 受付時間
$now = strtotime(date('H:i:s'));
if ($now < strtotime('9:00:00') || $now >= strtotime('19:00:00')) {
    print_r("受付時間外のため、受付できません。");
    exit(0);
}

// 料金計算
$price = calcWeekdaytimePrice($adult, $child, $senior, $now);

// 表示
print_r("大人 {$adult}人、子供 {$child}人、シニア {$senior}人 の\n合計料金は {$price} 円です。");
exit(1);
