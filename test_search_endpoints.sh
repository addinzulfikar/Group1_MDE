#!/bin/bash

echo "✓ Testing Search API Endpoints"
echo "=============================="
echo ""

# Test endpoint search
echo "1. Testing GET /api/v1/tracking/search?q=TRK00024491"
curl -s http://localhost/api/v1/tracking/search?q=TRK00024491 | jq '.status, .total' 2>/dev/null || echo "API not responding"

echo ""
echo "2. Testing GET /api/v1/tracking (list all)"
curl -s http://localhost/api/v1/tracking?page=1 | jq '.status, .total' 2>/dev/null || echo "API not responding"

echo ""
echo "3. Testing GET /api/v1/tracking/TRK00024491E9F6 (specific)"
curl -s http://localhost/api/v1/tracking/TRK00024491E9F6 | jq '.status, .data.tracking_number, .data.package.sender_name' 2>/dev/null || echo "API not responding"

echo ""
echo "✅ All endpoint tests completed!"
