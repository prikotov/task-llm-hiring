# Ярус 4: GLM-5.3-Flash (pi, thinking=low, 17.2с, 35,170 ток.)

- **Ось A (судья яруса 4): 2.5**
  - цитата: `if ($tokIn + $tokOut + $tokCR + $tokCW === 0) { $errors[] = "$label: all-zero token counts"; continue; }`
  - цитата: `final class BillingEngine { ... No floating point anywhere in this path.`
  - цитата: `return $minutesOfDay >= $this->startMinutes || $minutesOfDay < $this->endMinutes; // crosses midnight`
  - цитата: `Simple heuristic: re-rate if e.tsUtc >= validFrom AND the event's old cost doesn't already match`
- заметка: Есть аккуратное чистое доменное ядро: DecimalMoney на bcmath без float с единственным округлением HALF_UP до 6 знаков, OffPeakWindow с корректным переходом через полночь, BillingEngine с выбором строки по valid_from <= ts. Но команды фактически его не используют: ApplicationServices дублирует exactCost, выбор цены и оф-пик своим кодом — граница слоёв условная, два параллельных движка. Смущают отклонения от ТЗ: событие со всеми нулевыми токенами отбрасывается как ошибка (в ТЗ это валидное событие), сортировка отчёта strcasecmp (регистронезависимо, а нужна лексикографическая), частичный успех импорта с exit 1. Reprice — с самопризнанным «эвристическим» пересчётом, N+1 запросы в rate, тестов в диффе нет.

<details><summary>сырой ответ</summary>

```
{"axis_a": 2.5, "quotes": ["if ($tokIn + $tokOut + $tokCR + $tokCW === 0) { $errors[] = \"$label: all-zero token counts\"; continue; }", "final class BillingEngine { ... No floating point anywhere in this path.", "return $minutesOfDay >= $this->startMinutes || $minutesOfDay < $this->endMinutes; // crosses midnight", "Simple heuristic: re-rate if e.tsUtc >= validFrom AND the event's old cost doesn't already match"], "note": "Есть аккуратное чистое доменное ядро: DecimalMoney на bcmath без float с единственным округлением HALF_UP до 6 знаков, OffPeakWindow с корректным переходом через полночь, BillingEngine с выбором строки по valid_from <= ts. Но команды фактически его не используют: ApplicationServices дублирует exactCost, выбор цены и оф-пик своим кодом — граница слоёв условная, два параллельных движка. Смущают отклонения от ТЗ: событие со всеми нулевыми токенами отбрасывается как ошибка (в ТЗ это валидное событие), сортировка отчёта strcasecmp (регистронезависимо, а нужна лексикографическая), частичный успех импорта с exit 1. Reprice — с самопризнанным «эвристическим» пересчётом, N+1 запросы в rate, тестов в диффе нет."}
```

</details>
