✅ Single endpoint /api/webhook to receive all incoming webhooks

🧠 Source detection via headers or payload fields (GitHub, Stripe, Custom)

🗃 Database storage for each source:

GitHub → Store commit details (e.g., commit ID, message, author)

Stripe → Store payment transaction details (e.g., amount, status, currency)

Custom → Store the full JSON payload

✅ Error handling: Logs issues during processing for easier debugging

🧪 Automated tests for webhook functionality

🧪 How to Test
Option 1: Run Laravel's Automated Tests

php artisan test

Option 2: Manual Testing with curl/Postman

A. GitHub Webhook Test
curl -X POST http://127.0.0.1:8000/api/webhook \
  -H "Content-Type: application/json" \
  -H "X-GitHub-Event: push" \
  -d '{
    "commits": [
      {
        "id": "abc123",
        "message": "Initial commit",
        "author": { "name": "John Doe" }
      }
    ]
  }'

B. Stripe Webhook Test
curl -X POST http://127.0.0.1:8000/api/webhook \
  -H "Content-Type: application/json" \
  -d '{
    "data": {
      "object": {
        "amount": 100,
        "currency": "usd",
        "status": "succeeded"
      }
    }
  }'

  C. Custom Webhook Test
  
  curl -X POST http://127.0.0.1:8000/api/webhook \
  -H "Content-Type: application/json" \
  -d '{
    "event": "custom_event",
    "details": {
      "info": "Sample custom payload"
    }
  }'
