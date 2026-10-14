# Visa Estimate API

Base path: `/api/visa-estimate`  
Authentication: `Authorization: Bearer {token}` (Sanctum, client portal)

---

## 1. Get Visa List

**Request**

```
GET /api/visa-estimate/visa-list
```

| Query parameter | Type    | Required | Rules              | Default |
|-----------------|---------|----------|--------------------|---------|
| `q`             | string  | No       | max 500            | —       |
| `page`          | integer | No       | min 1              | 1       |
| `limit`         | integer | No       | min 1, max 500     | 20      |

**Response `200`**

```json
{
  "success": true,
  "message": "Visa list retrieved successfully",
  "data": {
    "data": [
      {
        "id": "55",
        "category": "Parent",
        "subclass": "103",
        "visa_stream": "Parent",
        "main_applicant_fee": "$7,600",
        "additional_applicant_18_plus_fee": "$3,300",
        "applicant_under_18_fee": "$1,655",
        "second_vac_charges": "$2,065 per person",
        "notes": "Second instalment applies",
        "source": "https://immi.homeaffairs.gov.au/visas/getting-a-visa/fees-and-charges/current-visa-pricing"
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 20,
      "total": 78,
      "last_page": 4,
      "from": 1,
      "to": 20
    }
  }
}
```

**Response `422` (validation)**

```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "page": ["The page field must be at least 1."]
  }
}
```

---

## 2. Get Estimate

**Request**

```
POST /api/visa-estimate/estimate
Content-Type: application/json
```

| Body field                      | Type    | Required | Rules              | Default |
|---------------------------------|---------|----------|--------------------|---------|
| `visa_id`                       | integer | Yes      | min 1              | —       |
| `lodging_date`                  | string  | No       | valid date         | —       |
| `lodging_online`                | boolean | No       | —                  | —       |
| `primary_in_australia`          | boolean | No       | —                  | —       |
| `primary_holds_table2_visa`     | boolean | No       | —                  | —       |
| `additional_applicants_18_plus` | integer | No       | min 0, max 20      | 0       |
| `additional_applicants_u18`   | integer | No       | min 0, max 20      | 0       |

**Example body**

```json
{
  "visa_id": 55,
  "additional_applicants_18_plus": 1,
  "additional_applicants_u18": 3
}
```

**Response `200`**

```json
{
  "success": true,
  "message": "Estimate calculated successfully",
  "data": {
    "line_items": [
      {
        "product": "Parent (subclass 103)",
        "quantity": 1,
        "price": 7600
      },
      {
        "product": "Additional Applicant Charge 18+",
        "quantity": 1,
        "price": 3300
      },
      {
        "product": "Additional Applicant Charge U18",
        "quantity": 3,
        "price": 4965
      },
      {
        "product": "GST",
        "quantity": 1,
        "price": 0
      }
    ],
    "total": 15865,
    "currency": "AUD",
    "price_starts_from": "AUD 15,865.00",
    "disclaimer": "This is an estimate only. The estimator might not include the second instalment payable for some visas. Verify current charges at https://immi.homeaffairs.gov.au/visas/getting-a-visa/fees-and-charges"
  }
}
```

**Response `404` (visa not found)**

```json
{
  "success": false,
  "message": "Visa not found. Use /api/visa-estimate/visa-list for valid visa IDs."
}
```

**Response `422` (validation)**

```json
{
  "message": "The visa id field is required.",
  "errors": {
    "visa_id": ["The visa id field is required."]
  }
}
```
