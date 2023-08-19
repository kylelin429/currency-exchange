# test

# Intro

一個簡單的貨幣轉換 API

# API

1. 匯率轉換

Request

- Request URL: https://xxx/api/currency/convert
- Method: GET
- URI Parameters
    
    
    | NO. | 名稱 | 型態 | 說明 | 必填 |
    | --- | --- | --- | --- | --- |
    | 1 | source | string | 原始貨幣，支援的類型：TWD, JPY, USD  | Y |
    | 2 | target | string | 目標貨幣，支援的類型：TWD, JPY, USD  | Y |
    | 3 | amount | string | 原始貨幣金額 (可以用 $ 開頭) | Y |
- Request Example

`https://xxx/api/currency/convert?source=USD&target=TWD&amount=$1,556.66`

Response

| NO. | 名稱 | 型態 | 說明 | 必填 |
| --- | --- | --- | --- | --- |
| 1 | msg | string | 執行結果 (成功:success, 失敗: failed) | Y |
| 2 | amount | string | 轉換後的目標貨幣金額 | Y |
- Response Example

成功

```json
{
    "msg": "success",
    "amount": "$170496.53"
}
```

參數驗證錯誤

```json
{
    "msg": "failed",
    "amount": ""
}
```