# Ярус 4: GLM-5.3-Flash (pi, thinking=low, 22.5с, 37,442 ток.)

- **Ось A (судья яруса 4): 3.5**
  - цитата: `Округление ровно одно: итоговая стоимость события округляется до 6 знаков... HALF_UP`
  - цитата: `return $s <= $e ? $t >= $s && $t < $e : $t >= $s || $t < $e;`
  - цитата: `ON CONFLICT (provider, provider_request_id) DO NOTHING`
  - цитата: `if ($candidate->getValidFrom() <= $event->getTs()) ... $candidate->getValidFrom() > $row->getValidFrom()`
- заметка: Сильное решение: чистая доменная модель (Provider/Model/PriceRow/UsageEvent + CostCalculator), вся арифметика на bcmath со scale 16, единственное округление HALF_UP на уровне события, отчёт суммирует уже округлённые стоимости. Оф-пик через полночь, границы окна и valid_from покрыты кодом и тестами, дедупликация через уникальный индекс + ON CONFLICT, есть бонус reprice с привязкой события к price_row. Смущают мелочи: смешение Doctrine и сырого SQL в приёме событий (прагматично, но граница слоёв условная), строковое сравнение времени в off-peak через 'HH:MM'→int (хрупко, хотя валидация формата есть), поля money как VARCHAR(32) без NUMERIC, а .phpunit.result.cache с записями о падениях попал в дифф.

<details><summary>сырой ответ</summary>

```
{"axis_a": 3.5, "quotes": ["Округление ровно одно: итоговая стоимость события округляется до 6 знаков... HALF_UP", "return $s <= $e ? $t >= $s && $t < $e : $t >= $s || $t < $e;", "ON CONFLICT (provider, provider_request_id) DO NOTHING", "if ($candidate->getValidFrom() <= $event->getTs()) ... $candidate->getValidFrom() > $row->getValidFrom()"], "note": "Сильное решение: чистая доменная модель (Provider/Model/PriceRow/UsageEvent + CostCalculator), вся арифметика на bcmath со scale 16, единственное округление HALF_UP на уровне события, отчёт суммирует уже округлённые стоимости. Оф-пик через полночь, границы окна и valid_from покрыты кодом и тестами, дедупликация через уникальный индекс + ON CONFLICT, есть бонус reprice с привязкой события к price_row. Смущают мелочи: смешение Doctrine и сырого SQL в приёме событий (прагматично, но граница слоёв условная), строковое сравнение времени в off-peak через 'HH:MM'→int (хрупко, хотя валидация формата есть), поля money как VARCHAR(32) без NUMERIC, а .phpunit.result.cache с записями о падениях попал в дифф."}
```

</details>
