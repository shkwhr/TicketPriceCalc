# TicketPriceCalc

## Overview
曜日、時間、人数に応じた料金計算を行うサンプルプログラム

## Requirement
- PHP 8.*

## Usage
* 大人:1
```bash
php ticket_price_calc.php 1
```
* 大人:1 子供:1
```bash
php ticket_price_calc.php 1 1
```
* 大人:2 子供:0 シニア:1
```bash
php ticket_price_calc.php 2 0 1
```

## Features
* 料金
  * 大人一人 1000円 子供一人500円 シニア一人 800円
  * 大人、子供、シニアそれぞれ最大50人まで計算可能
* 処理可能時間
  * 9:00 - 19:00
* 平日夕方割引
  * 平日夕方17時以降は300円引
* 土日料金
  * 土日は15%割増
* 団体割引
  * 10人以上10%割引 (子供は0.5人換算)
