# PostgreSQL Optimization Environment Variables

Add these to your `.env` file:

```env
# === POSTGRESQL TIER 1 OPTIMIZATIONS ===

# Cache Driver (redis recommended)
CACHE_DRIVER=redis

# Redis Configuration
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DB=0
REDIS_CACHE_DB=1

# Database Performance
DB_PERSISTENT=false          # Set true in production
DB_TIMEOUT=10

# Query Logging
LOG_SLOW_QUERIES=true
SLOW_QUERY_THRESHOLD=1000    # milliseconds
LOG_ALL_QUERIES=false

# Cache Prefix
CACHE_PREFIX=migrationmanager_cache
```

See `TIER1_OPTIMIZATIONS_GUIDE.md` for complete documentation.
