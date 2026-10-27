Implement LLM token cost accounting.

- Added Entities: Provider, Model, Price, Usage.
- Implemented app:prices:import for loading provider, model, and price data.
- Implemented app:usage:import for loading usage events with deduplication.
- Implemented app:billing:rate for calculating usage costs using bcmath for precision and HALF_UP rounding.
- Implemented app:billing:report for producing the cost report in CSV format.
- Handled off-peak pricing based on UTC time windows.
- Handled time-based price versioning.
