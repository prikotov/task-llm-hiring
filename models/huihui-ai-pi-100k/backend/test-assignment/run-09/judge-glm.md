# Ярус 4: GLM-5.3-Flash (pi, thinking=low, 41.3с, 37,393 ток.)

- **Ось A (судья яруса 4): 3.5**
  - цитата: `Деньги считаются только bcmath (шкалы 16–20): без промежуточного округления, ровно одно округление HALF_UP до 6 знаков на стоимость события`
  - цитата: `if ($offPeak !== null && $offPeak->covers($ts)) { $cost = bcmul($cost, $offPeak->multiplier(), Decimal::SCALE); }`
  - цитата: `// Окно переходит через полночь.
return $minute >= $this->startMinute || $minute < $this->endMinute;`
  - цитата: `$events = $this->em->createQuery('SELECT e FROM App\Entity\UsageEvent e WHERE e.priceRowId = :id AND e.status = :status ...')`
- заметка: Сильное решение: чистый домен (Decimal/OffPeakPolicy/Rater) отделён от приёма и отчёта, bcmath везде, дедуп по (provider, provider_request_id) и на уровне приложения, и в БД, unrated с ретраями, оф-пик через полночь покрыт тестами, бонус reprice реализован через сохранённую ссылку на строку цены. Слабости: halfUp6 сделан добавлением 0.0000005 с ltrim('-') — коряво и фактически half-up только для неотрицательных; мультипликатор парсится регэкспом, допускающим 0; промежуточный масштаб 16–18 местами впритык; хранение времени datetime_immutable при TIMESTAMPTZ в миграции. Это косметика, на уверенность не влияет.

<details><summary>сырой ответ</summary>

```
{"axis_a": 3.5, "quotes": ["Деньги считаются только bcmath (шкалы 16–20): без промежуточного округления, ровно одно округление HALF_UP до 6 знаков на стоимость события", "if ($offPeak !== null && $offPeak->covers($ts)) { $cost = bcmul($cost, $offPeak->multiplier(), Decimal::SCALE); }", "// Окно переходит через полночь.\nreturn $minute >= $this->startMinute || $minute < $this->endMinute;", "$events = $this->em->createQuery('SELECT e FROM App\\Entity\\UsageEvent e WHERE e.priceRowId = :id AND e.status = :status ...')"], "note": "Сильное решение: чистый домен (Decimal/OffPeakPolicy/Rater) отделён от приёма и отчёта, bcmath везде, дедуп по (provider, provider_request_id) и на уровне приложения, и в БД, unrated с ретраями, оф-пик через полночь покрыт тестами, бонус reprice реализован через сохранённую ссылку на строку цены. Слабости: halfUp6 сделан добавлением 0.0000005 с ltrim('-') — коряво и фактически half-up только для неотрицательных; мультипликатор парсится регэкспом, допускающим 0; промежуточный масштаб 16–18 местами впритык; хранение времени datetime_immutable при TIMESTAMPTZ в миграции. Это косметика, на уверенность не влияет."}
```

</details>
